import pandas as pd
import seaborn as sns
import matplotlib.pyplot as plt
import os

# Create results directory if it doesn't exist
if not os.path.exists('results'):
    os.makedirs('results')

# Load the dataset
df = pd.read_csv("water_quality_dataset_6200_extreme.csv")

# Create a figure for all features boxplot
plt.figure(figsize=(15, 8))
sns.boxplot(data=df.drop('Potability', axis=1))
plt.title('Distribution of Water Quality Parameters')
plt.xticks(rotation=45)
plt.ylabel('Value')
plt.tight_layout()
plt.savefig('results/all_features_boxplot.png', dpi=300, bbox_inches='tight')
plt.close()

# Create individual boxplots split by potability
fig, axes = plt.subplots(3, 2, figsize=(15, 20))
features = df.drop('Potability', axis=1).columns
axes = axes.ravel()

for idx, feature in enumerate(features):
    sns.boxplot(data=df, x='Potability', y=feature, ax=axes[idx])
    axes[idx].set_title(f'{feature} Distribution by Potability')
    axes[idx].set_xlabel('Potability (0: Not Potable, 1: Potable)')

plt.tight_layout()
plt.savefig('results/features_by_potability_boxplots.png', dpi=300, bbox_inches='tight')
plt.close()

# Calculate and print summary statistics
print("\n📊 Summary Statistics for Each Feature:")
print(df.describe().round(2).to_string())

# Calculate and print outlier percentages
def calculate_outliers(data):
    Q1 = data.quantile(0.25)
    Q3 = data.quantile(0.75)
    IQR = Q3 - Q1
    lower_bound = Q1 - 1.5 * IQR
    upper_bound = Q3 + 1.5 * IQR
    outliers = ((data < lower_bound) | (data > upper_bound)).sum()
    return outliers / len(data) * 100

print("\n📊 Percentage of Outliers in Each Feature:")
for feature in features:
    outlier_pct = calculate_outliers(df[feature])
    print(f"{feature}: {outlier_pct:.2f}%") 