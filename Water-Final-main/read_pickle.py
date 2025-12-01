import pickle
import numpy as np

# Load the pickle file
with open('water_quality_model.pkl', 'rb') as file:
    array_data = pickle.load(file)

print("Type of loaded object:", type(array_data))
print("\nArray shape:", array_data.shape)
print("Array data type:", array_data.dtype)
print("\nFirst few elements of the array:")
print(array_data[:5])  # Show first 5 elements or rows
print("\nArray statistics:")
print("Mean:", np.mean(array_data))
print("Standard deviation:", np.std(array_data))
print("Min:", np.min(array_data))
print("Max:", np.max(array_data))

# If it's a scikit-learn model, print additional information
if hasattr(array_data, 'get_params'):
    print("\nModel parameters:")
    print(array_data.get_params()) 