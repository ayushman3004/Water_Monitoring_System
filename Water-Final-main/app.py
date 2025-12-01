import streamlit as st
import pickle
import numpy as np
import pandas as pd
import seaborn as sns
import matplotlib.pyplot as plt
from water_pollution_detector import predict_pollution_from_color, visualize_color_analysis
import tempfile
import os

# Set page configuration
st.set_page_config(
    page_title="Water Quality Analysis",
    page_icon="💧",
    layout="wide",
    initial_sidebar_state="expanded"
)

# Custom CSS
st.markdown("""
    <style>
    .main {
        padding: 0rem 0rem;
    }
    .stAlert {
        padding: 1rem;
        margin: 1rem 0;
        border-radius: 0.5rem;
    }
    .st-emotion-cache-16idsys p {
        font-size: 20px;
    }
    </style>
""", unsafe_allow_html=True)

# Sidebar
with st.sidebar:
    st.image("https://img.icons8.com/fluency/96/water.png", width=100)
    st.title("Navigation")
    page = st.radio("Choose a Page", 
                    ["Home", "Prediction", "Data Analysis", "About"])
    
    st.markdown("---")
    st.markdown("### About the Model")
    st.info("""
    This model predicts water potability based on:
    - pH Level
    - Hardness
    - Turbidity
    - Sulfate Content
    - Chlorine Level
    """)

# Load the model and scaler
@st.cache_resource
def load_model():
    try:
        with open('water_quality_model.pkl', 'rb') as file:
            model_data = pickle.load(file)
        return model_data
    except Exception as e:
        st.error(f"Error loading model: {str(e)}")
        return None

model_data = load_model()

if page == "Home":
    # Home page
    st.title("🌊 Water Quality Analysis System")
    st.markdown("### Welcome to the Water Quality Prediction Platform")
    
    col1, col2 = st.columns(2)
    
    with col1:
        st.markdown("""
        #### Key Features:
        - Real-time water quality prediction
        - Interactive data visualization
        - Detailed parameter analysis
        - Expert system recommendations
        """)
        
    with col2:
        st.image("https://img.icons8.com/fluency/240/water.png", width=200)
    
    st.markdown("---")
    
    # Quick Start Guide
    st.markdown("### 📚 Quick Start Guide")
    st.markdown("""
    1. Navigate to the **Prediction** page to analyze water samples
    2. View detailed analytics in the **Data Analysis** section
    3. Learn more about the system in the **About** section
    """)

elif page == "Prediction":
    st.title("🔍 Water Quality Prediction")
    
    # Create tabs for different prediction methods
    param_tab, image_tab = st.tabs(["Parameter-based Prediction", "Image-based Detection"])
    
    with param_tab:
        st.markdown("Enter water quality parameters to predict potability")
        
        # Create two columns for input
        col1, col2 = st.columns(2)
        
        # Dictionary to store input values
        input_values = {}
        
        # Parameter ranges and descriptions
        parameter_info = {
            'pH': {
                'min': 0.0, 'max': 14.0, 'default': 7.0,
                'description': "Measure of acidity/basicity (ideal: 6.5-8.5)"
            },
            'Hardness': {
                'min': 0.0, 'max': 1000.0, 'default': 150.0,
                'description': "Mineral content in mg/L (ideal: ≤ 200)"
            },
            'Turbidity': {
                'min': 0.0, 'max': 100.0, 'default': 5.0,
                'description': "Water clarity measure in NTU (ideal: ≤ 5)"
            },
            'Sulfate': {
                'min': 0.0, 'max': 1000.0, 'default': 250.0,
                'description': "Sulfate content in mg/L (ideal: ≤ 250)"
            },
            'Chlorine': {
                'min': 0.0, 'max': 10.0, 'default': 1.0,
                'description': "Chlorine level in mg/L (ideal: 0.2-2.0)"
            }
        }
        
        # Create input fields
        with col1:
            for param in ['pH', 'Hardness', 'Turbidity']:
                st.markdown(f"#### {param}")
                st.markdown(f"_{parameter_info[param]['description']}_")
                input_values[param] = st.slider(
                    f"Select {param}",
                    min_value=float(parameter_info[param]['min']),
                    max_value=float(parameter_info[param]['max']),
                    value=float(parameter_info[param]['default']),
                    key=param
                )
                st.markdown("---")
        
        with col2:
            for param in ['Sulfate', 'Chlorine']:
                st.markdown(f"#### {param}")
                st.markdown(f"_{parameter_info[param]['description']}_")
                input_values[param] = st.slider(
                    f"Select {param}",
                    min_value=float(parameter_info[param]['min']),
                    max_value=float(parameter_info[param]['max']),
                    value=float(parameter_info[param]['default']),
                    key=param
                )
                st.markdown("---")
        
        # Prediction button
        if st.button("🔍 Analyze Water Quality", use_container_width=True):
            try:
                # Prepare input data
                input_data = np.array([input_values[feature] for feature in model_data['feature_names']]).reshape(1, -1)
                
                # Scale the input data
                input_data_scaled = model_data['scaler'].transform(input_data)
                
                # Make prediction
                prediction = model_data['model'].predict(input_data_scaled)
                probability = model_data['model'].predict_proba(input_data_scaled)
                
                # Display prediction
                st.markdown("### 📊 Analysis Results")
                
                # Create three columns for results
                result_col1, result_col2, result_col3 = st.columns(3)
                
                with result_col1:
                    if prediction[0] == 1:
                        st.success("✅ Water is Potable")
                    else:
                        st.error("❌ Water is Not Potable")
                        
                with result_col2:
                    confidence = probability[0][1] if prediction[0] == 1 else probability[0][0]
                    st.metric("Confidence Level", f"{confidence:.2%}")
                    
                with result_col3:
                    st.markdown(f"### Quality Score")
                    quality_score = probability[0][1] * 100
                    st.progress(quality_score/100)
                    st.text(f"{quality_score:.1f}/100")
                
                # Parameter Analysis
                st.markdown("### 📈 Parameter Analysis")
                analysis_cols = st.columns(5)
                
                for idx, (param, value) in enumerate(input_values.items()):
                    with analysis_cols[idx]:
                        st.metric(
                            param,
                            f"{value:.2f}",
                            delta=f"{value - parameter_info[param]['default']:.2f}",
                            delta_color="inverse"
                        )
                
                # Recommendations
                st.markdown("### 💡 Recommendations")
                recommendations = []
                
                if input_values['pH'] < 6.5 or input_values['pH'] > 8.5:
                    recommendations.append("- Adjust pH level to be between 6.5 and 8.5")
                if input_values['Hardness'] > 200:
                    recommendations.append("- Consider water softening treatment")
                if input_values['Turbidity'] > 5:
                    recommendations.append("- Implement filtration to reduce turbidity")
                if input_values['Sulfate'] > 250:
                    recommendations.append("- Reduce sulfate levels through treatment")
                if input_values['Chlorine'] < 0.2 or input_values['Chlorine'] > 2.0:
                    recommendations.append("- Adjust chlorine levels to be between 0.2 and 2.0 mg/L")
                    
                if recommendations:
                    st.warning("\n".join(recommendations))
                else:
                    st.success("All parameters are within acceptable ranges!")
                
            except Exception as e:
                st.error(f"Error making prediction: {str(e)}")

    with image_tab:
        st.markdown("### 📸 Image-based Water Pollution Detection")
        st.markdown("""
        Upload an image of water to analyze pollution levels based on color characteristics.
        This method uses computer vision to detect:
        - Clear water
        - Algae presence
        - Visible pollution
        - Other contamination indicators
        """)
        
        uploaded_file = st.file_uploader("Choose a water image...", type=['jpg', 'jpeg', 'png'])
        
        if uploaded_file is not None:
            # Create a temporary file to save the uploaded image
            with tempfile.NamedTemporaryFile(delete=False, suffix='.jpg') as tmp_file:
                tmp_file.write(uploaded_file.getvalue())
                tmp_path = tmp_file.name
            
            try:
                # Display the uploaded image
                st.image(uploaded_file, caption="Uploaded Water Image", use_column_width=True)
                
                # Analyze the image
                label, pollution_pct = predict_pollution_from_color(tmp_path)
                
                # Display results
                col1, col2 = st.columns(2)
                
                with col1:
                    st.markdown(f"### Results")
                    st.markdown(f"**Detection:** {label}")
                    st.progress(pollution_pct/100)
                    st.markdown(f"Pollution Level: {pollution_pct:.1f}%")
                
                with col2:
                    if pollution_pct >= 75:
                        st.error("⚠️ High pollution detected! Water treatment recommended.")
                    elif pollution_pct >= 50:
                        st.warning("⚠️ Moderate pollution detected. Further testing recommended.")
                    else:
                        st.success("✅ Water appears relatively clean.")
                
                # Show color analysis
                st.markdown("### 📊 Color Analysis")
                fig = visualize_color_analysis(tmp_path)
                
            except Exception as e:
                st.error(f"Error analyzing image: {str(e)}")
            finally:
                # Clean up the temporary file
                if os.path.exists(tmp_path):
                    os.remove(tmp_path)

elif page == "Data Analysis":
    st.title("📊 Data Analysis")
    
    # Add tabs for different analyses
    tab1, tab2, tab3 = st.tabs(["Feature Importance", "Correlations", "Distributions"])
    
    with tab1:
        st.markdown("### Feature Importance in Prediction")
        importance_df = pd.DataFrame({
            'Feature': model_data['feature_names'],
            'Importance': model_data['model'].feature_importances_
        }).sort_values('Importance', ascending=False)
        
        fig, ax = plt.subplots(figsize=(10, 6))
        sns.barplot(data=importance_df, x='Importance', y='Feature')
        plt.title('Feature Importance in Water Quality Prediction')
        st.pyplot(fig)
        
    with tab2:
        st.markdown("### Parameter Correlations")
        # Load your correlation matrix from results
        try:
            corr_matrix = pd.read_csv('results/feature_correlations.csv', index_col=0)
            fig, ax = plt.subplots(figsize=(10, 8))
            sns.heatmap(corr_matrix, annot=True, cmap='coolwarm', center=0)
            plt.title('Correlation Heatmap of Water Quality Parameters')
            st.pyplot(fig)
        except Exception as e:
            st.error("Correlation data not available. Please run the correlation analysis first.")
            
    with tab3:
        st.markdown("### Parameter Distributions")
        try:
            fig = plt.figure(figsize=(12, 6))
            plt.subplot(111)
            boxplot_data = pd.read_csv('water_quality_dataset_6200_extreme.csv')
            sns.boxplot(data=boxplot_data.drop('Potability', axis=1))
            plt.xticks(rotation=45)
            plt.title('Distribution of Water Quality Parameters')
            st.pyplot(fig)
        except Exception as e:
            st.error("Distribution data not available. Please run the distribution analysis first.")

else:  # About page
    st.title("ℹ️ About")
    st.markdown("""
    ### Water Quality Prediction System
    
    This application uses machine learning to predict water potability based on various chemical and physical parameters.
    
    #### Technology Stack:
    - **Frontend**: Streamlit
    - **Backend**: Python
    - **ML Framework**: Scikit-learn
    - **Data Analysis**: Pandas, NumPy
    - **Visualization**: Seaborn, Matplotlib
    
    #### Model Information:
    - Algorithm: Random Forest Classifier
    - Training Data: 6200 water quality samples
    - Features: pH, Hardness, Turbidity, Sulfate, Chlorine
    - Target: Water Potability (0: Not Potable, 1: Potable)
    
    #### References:
    - WHO Guidelines for Drinking Water Quality
    - EPA Water Quality Standards
    - Scientific Literature on Water Quality Assessment
    """)
    
    st.markdown("---")
    st.markdown("### 👥 Contact Information")
    st.markdown("""
    For questions or support:
    - 📧 Email: support@waterquality.ai
    - 🌐 Website: www.waterquality.ai
    - 📱 Phone: +1 (555) 123-4567
    """)

# Footer
st.markdown("---")
st.markdown(
    """
    <div style='text-align: center'>
        <p>Made with ❤️ by Water Quality Team | © 2024</p>
    </div>
    """,
    unsafe_allow_html=True
) 