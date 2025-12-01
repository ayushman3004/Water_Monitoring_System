# Water Quality Analysis System

A comprehensive water quality analysis system that combines traditional parameter-based assessment with image-based pollution detection.

## Features

- Parameter-based water quality prediction
- Image-based pollution detection using computer vision
- Interactive data visualization
- Real-time analysis and recommendations
- Support for multiple water quality parameters

## Live Demo

Access the live application at: [Your Streamlit Cloud URL after deployment]

## Local Development Setup

1. Clone the repository:
```bash
git clone [your-repo-url]
cd [your-repo-name]
```

2. Create a virtual environment:
```bash
python -m venv venv
source venv/bin/activate  # On Windows: venv\Scripts\activate
```

3. Install dependencies:
```bash
pip install -r requirements.txt
```

4. Run the application:
```bash
streamlit run app.py
```

## Deployment Instructions

### Deploy on Streamlit Cloud (Recommended)

1. Push your code to GitHub
2. Visit [share.streamlit.io](https://share.streamlit.io)
3. Sign in with GitHub
4. Click "New app"
5. Select your repository, branch, and main file (app.py)
6. Click "Deploy"

### Alternative Deployment Options

#### Deploy using Docker

1. Build the Docker image:
```bash
docker build -t water-quality-app .
```

2. Run the container:
```bash
docker run -p 8501:8501 water-quality-app
```

#### Deploy on Heroku

1. Install Heroku CLI
2. Login to Heroku:
```bash
heroku login
```

3. Create a new Heroku app:
```bash
heroku create your-app-name
```

4. Push to Heroku:
```bash
git push heroku main
```

## Usage

1. **Parameter-based Analysis:**
   - Enter water quality parameters
   - Click "Analyze Water Quality"
   - View results and recommendations

2. **Image-based Analysis:**
   - Upload a water image
   - Get instant pollution detection results
   - View color analysis and recommendations

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Files
- `app.py`: Main Streamlit application
- `train_model.py`: Model training script
- `correlation_heatmap.py`: Correlation analysis
- `boxplot_analysis.py`: Distribution analysis
- `water_quality_model.pkl`: Trained model file
- `requirements.txt`: Required Python packages

## Data Parameters
- pH: 0-14 scale
- Hardness: mg/L
- Turbidity: NTU
- Sulfate: mg/L
- Chlorine: mg/L 