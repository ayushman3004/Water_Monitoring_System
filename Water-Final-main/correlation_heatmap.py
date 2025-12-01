import pandas as pd
import numpy as np
import seaborn as sns
import matplotlib.pyplot as plt
import os

# Create results directory if it doesn't exist
if not os.path.exists('results'):
    os.makedirs('results')

# Load the dataset
df = pd.read_csv("water_quality_dataset_6200_extreme.csv")

# Create correlation heatmap
plt.figure(figsize=(12, 10))
correlation_matrix = df.corr()

# Create heatmap with annotations
sns.heatmap(correlation_matrix, 
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

print("\n✅ Correlation Analysis Results:")
print("📊 Heatmap saved as 'results/feature_correlation_heatmap.png'")
print("📊 Correlation values saved as 'results/feature_correlations.csv'")

# Print strongest correlations
print("\n🔍 Strongest Feature Correlations:")
# Get the upper triangle of correlations
upper = correlation_matrix.where(np.triu(np.ones(correlation_matrix.shape), k=1).astype(bool))
# Find strongest correlations
strongest_correlations = upper.unstack()
strongest_correlations = strongest_correlations.sort_values(key=abs, ascending=False)
# Print top 5 correlations
print(strongest_correlations[:5].to_string()) 