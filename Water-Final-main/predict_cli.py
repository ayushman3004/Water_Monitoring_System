import sys
import pickle
import numpy as np
import json
import os
import warnings

# Suppress warnings
warnings.filterwarnings("ignore")

def load_model():
    try:
        # Get the directory of the current script
        current_dir = os.path.dirname(os.path.abspath(__file__))
        model_path = os.path.join(current_dir, 'water_quality_model.pkl')
        
        with open(model_path, 'rb') as file:
            model_data = pickle.load(file)
        return model_data
    except Exception as e:
        return None

def predict(ph, hardness, turbidity, sulfate, chlorine):
    model_data = load_model()
    
    if model_data is None:
        return {"error": "Failed to load model"}

    try:
        # Prepare input data
        # Feature names expected by the model: ['pH', 'Hardness', 'Turbidity', 'Sulfate', 'Chlorine']
        input_values = {
            'pH': float(ph),
            'Hardness': float(hardness),
            'Turbidity': float(turbidity),
            'Sulfate': float(sulfate),
            'Chlorine': float(chlorine)
        }
        
        input_data = np.array([input_values[feature] for feature in model_data['feature_names']]).reshape(1, -1)
        
        # Scale the input data
        input_data_scaled = model_data['scaler'].transform(input_data)
        
        # Make prediction
        prediction = model_data['model'].predict(input_data_scaled)
        probability = model_data['model'].predict_proba(input_data_scaled)
        
        is_potable = bool(prediction[0] == 1)
        confidence = float(probability[0][1] if is_potable else probability[0][0])
        quality_score = float(probability[0][1] * 100)
        
        return {
            "is_potable": is_potable,
            "confidence": confidence,
            "quality_score": quality_score,
            "prediction_text": "Potable" if is_potable else "Not Potable"
        }
        
    except Exception as e:
        return {"error": str(e)}

if __name__ == "__main__":
    # Check if arguments are provided
    if len(sys.argv) != 6:
        print(json.dumps({"error": "Invalid number of arguments. Expected 5: pH, Hardness, Turbidity, Sulfate, Chlorine"}))
    else:
        try:
            ph = sys.argv[1]
            hardness = sys.argv[2]
            turbidity = sys.argv[3]
            sulfate = sys.argv[4]
            chlorine = sys.argv[5]
            
            result = predict(ph, hardness, turbidity, sulfate, chlorine)
            print(json.dumps(result))
        except Exception as e:
            print(json.dumps({"error": str(e)}))
