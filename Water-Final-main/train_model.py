import pandas as pd
import numpy as np
from sklearn.model_selection import train_test_split
from sklearn.ensemble import RandomForestClassifier
from sklearn.preprocessing import StandardScaler
from sklearn.metrics import classification_report, confusion_matrix, accuracy_score
from imblearn.over_sampling import SMOTE
import seaborn as sns
import matplotlib.pyplot as plt
import pickle
import os

# Create directory for results if it doesn't exist
if not os.path.exists('results'):
    os.makedirs('results')

# Load the dataset
df = pd.read_csv("water_quality_dataset_6200_extreme.csv")

# Create correlation heatmap
plt.figure(figsize=(12, 10))
correlation_matrix = df.corr()
mask = np.triu(np.ones_like(correlation_matrix), k=1)
sns.heatmap(correlation_matrix, 
            mask=mask,
            annot=True, 
            cmap='coolwarm', 
            center=0,
            fmt='.2f',
            square=True,
            linewidths=0.5)
plt.title('Correlation Heatmap of Water Quality Features')
plt.tight_layout()
plt.savefig('results/feature_correlation_heatmap.png', dpi=300, bbox_inches='tight')
plt.close()

# Save correlation matrix to CSV
correlation_matrix.to_csv('results/feature_correlations.csv')

print("📊 Feature correlation heatmap has been saved as 'results/feature_correlation_heatmap.png'")
print("📊 Correlation values have been saved as 'results/feature_correlations.csv'")

# Separate features and target
X = df.drop("Potability", axis=1)
y = df["Potability"]

# Scale features
scaler = StandardScaler()
X_scaled = scaler.fit_transform(X)

# STEP 1: Train-test split BEFORE SMOTE
X_train, X_test, y_train, y_test = train_test_split(X_scaled, y, test_size=0.2, random_state=42)

# STEP 2: Apply SMOTE only to training set
smote = SMOTE(random_state=42)
X_train_smote, y_train_smote = smote.fit_resample(X_train, y_train)

print("✅ After SMOTE - Training Data Class Distribution:", np.bincount(y_train_smote))

# STEP 3: Train Random Forest with reduced complexity
rf_model = RandomForestClassifier(
    n_estimators=100,
    max_depth=6,                # limit depth to prevent overfitting
    min_samples_leaf=4,
    random_state=42
)
rf_model.fit(X_train_smote, y_train_smote)

# STEP 4: Predict on original test set (no SMOTE)
y_pred = rf_model.predict(X_test)

# STEP 5: Evaluate Test Set
print("\n📊 Classification Report (on test set):")
print(classification_report(y_test, y_pred))

# Create and save confusion matrix for test set
cm_test = confusion_matrix(y_test, y_pred)
plt.figure(figsize=(10, 8))
sns.heatmap(cm_test, annot=True, fmt='d', cmap='Blues',
            xticklabels=["Not Potable", "Potable"],
            yticklabels=["Not Potable", "Potable"])
plt.title("Confusion Matrix - Test Set")
plt.xlabel("Predicted")
plt.ylabel("Actual")
plt.tight_layout()
plt.savefig('results/confusion_matrix_test.png', dpi=300, bbox_inches='tight')
plt.close()

# Save test confusion matrix as CSV
cm_test_df = pd.DataFrame(cm_test, 
                         columns=["Predicted Not Potable", "Predicted Potable"],
                         index=["Actual Not Potable", "Actual Potable"])
cm_test_df.to_csv('results/confusion_matrix_test.csv')

# Save the model and scaler
model_data = {
    'model': rf_model,
    'scaler': scaler,
    'feature_names': list(X.columns)
}

with open('water_quality_model.pkl', 'wb') as file:
    pickle.dump(model_data, file)

print("\n✅ Model and scaler have been saved to 'water_quality_model.pkl'")

print("\n🔍 Evaluating on Unseen Data:")
# Load and evaluate unseen data
try:
    unseen_df = pd.read_csv("unseen_water_data.csv")
    
    # Rename columns if needed
    column_mapping = {
        'ph': 'pH',
        'Chloramines': 'Chlorine'
    }
    unseen_df.rename(columns=column_mapping, inplace=True)
    
    # Prepare features and labels
    X_unseen = unseen_df.drop("Potability", axis=1)
    y_unseen = unseen_df["Potability"]
    
    # Scale the unseen data
    X_unseen_scaled = scaler.transform(X_unseen)
    
    # Predict
    y_pred_unseen = rf_model.predict(X_unseen_scaled)
    
    # Evaluate
    print("\n📊 Classification Report (Unseen Data):")
    print(classification_report(y_unseen, y_pred_unseen))
    print("\n✅ Accuracy on Unseen Data:", accuracy_score(y_unseen, y_pred_unseen))
    
    # Create and save confusion matrix for unseen data
    cm_unseen = confusion_matrix(y_unseen, y_pred_unseen)
    plt.figure(figsize=(10, 8))
    sns.heatmap(cm_unseen, annot=True, fmt='d', cmap='Blues',
                xticklabels=["Not Potable", "Potable"],
                yticklabels=["Not Potable", "Potable"])
    plt.title("Confusion Matrix - Unseen Data")
    plt.xlabel("Predicted")
    plt.ylabel("Actual")
    plt.tight_layout()
    plt.savefig('results/confusion_matrix_unseen.png', dpi=300, bbox_inches='tight')
    plt.close()
    
    # Save unseen data confusion matrix as CSV
    cm_unseen_df = pd.DataFrame(cm_unseen,
                               columns=["Predicted Not Potable", "Predicted Potable"],
                               index=["Actual Not Potable", "Actual Potable"])
    cm_unseen_df.to_csv('results/confusion_matrix_unseen.csv')
    
    # Print confusion matrices
    print("\n📊 Confusion Matrix - Test Set:")
    print(cm_test_df)
    print("\n📊 Confusion Matrix - Unseen Data:")
    print(cm_unseen_df)
    
except Exception as e:
    print(f"\n⚠️ Error processing unseen data: {str(e)}")

print("\n✅ Results have been saved in the 'results' directory:"
      "\n   - confusion_matrix_test.png"
      "\n   - confusion_matrix_test.csv"
      "\n   - confusion_matrix_unseen.png"
      "\n   - confusion_matrix_unseen.csv")
