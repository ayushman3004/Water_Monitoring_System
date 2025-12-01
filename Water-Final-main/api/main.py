"""
FastAPI wrapper for Water Quality Prediction Models.
Includes both parameter-based and image-based prediction.
Does NOT modify any existing ML code - just wraps them.
"""

import sys
import os
import tempfile
import shutil
from contextlib import asynccontextmanager

# Add parent directory to path so we can import existing modules
sys.path.insert(0, os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

from fastapi import FastAPI, HTTPException, UploadFile, File, Request
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from typing import List, Optional
import time

# Import existing functions - no modifications to original code
from predict_cli import predict as predict_params, load_model
from water_pollution_detector import predict_pollution_from_color

# ============== Lifespan for model loading ==============
@asynccontextmanager
async def lifespan(app: FastAPI):
    """Load models on startup, cleanup on shutdown."""
    global _model_data, _image_detector_ready
    
    # Startup
    _model_data = load_model()
    if _model_data:
        print("✅ Parameter-based model loaded successfully")
    else:
        print("❌ Failed to load parameter-based model")
    
    _image_detector_ready = True
    print("✅ Image-based detector ready")
    
    yield  # Server runs here
    
    # Shutdown cleanup
    print("🛑 Shutting down API...")

app = FastAPI(
    title="Water Quality Prediction API",
    description="API with parameter-based and image-based water quality prediction",
    version="2.0.0",
    lifespan=lifespan
)

# ============== CORS Middleware ==============
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # In production, specify your domains
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# ============== Simple Rate Limiting ==============
request_counts = {}
RATE_LIMIT = 30  # requests per minute per IP
RATE_WINDOW = 60  # seconds

@app.middleware("http")
async def rate_limit_middleware(request: Request, call_next):
    client_ip = request.client.host
    current_time = time.time()
    
    # Clean old entries
    request_counts[client_ip] = [
        t for t in request_counts.get(client_ip, [])
        if current_time - t < RATE_WINDOW
    ]
    
    # Check rate limit
    if len(request_counts.get(client_ip, [])) >= RATE_LIMIT:
        from fastapi.responses import JSONResponse
        return JSONResponse(
            status_code=429,
            content={"detail": "Too many requests. Please wait a minute."}
        )
    
    # Record request
    request_counts.setdefault(client_ip, []).append(current_time)
    
    response = await call_next(request)
    return response

# ============== Request/Response Models ==============

class PredictRequest(BaseModel):
    """Request model for parameter-based prediction."""
    pH: float
    Hardness: float
    Turbidity: float
    Sulfate: float
    Chlorine: float

    class Config:
        json_schema_extra = {
            "example": {
                "pH": 7.0,
                "Hardness": 150.0,
                "Turbidity": 5.0,
                "Sulfate": 250.0,
                "Chlorine": 1.0
            }
        }

class PredictResponse(BaseModel):
    is_potable: bool
    confidence: float
    quality_score: float
    prediction_text: str

class ImagePredictResponse(BaseModel):
    label: str
    pollution_percentage: float
    is_polluted: bool

class HealthResponse(BaseModel):
    status: str
    model_loaded: bool
    image_detector_loaded: bool

# ============== Global Model State ==============
_model_data = None
_image_detector_ready = False

# ============== Endpoints ==============

@app.get("/health", response_model=HealthResponse)
async def health():
    """Health check endpoint."""
    return HealthResponse(
        status="ok",
        model_loaded=_model_data is not None,
        image_detector_loaded=_image_detector_ready
    )

@app.post("/predict", response_model=PredictResponse)
async def predict_endpoint(request: PredictRequest):
    """
    Predict water potability based on numeric parameters.
    
    Parameters:
    - pH: Water pH level (0-14)
    - Hardness: Mineral content in mg/L
    - Turbidity: Water clarity in NTU
    - Sulfate: Sulfate content in mg/L
    - Chlorine: Chlorine level in mg/L
    
    Returns prediction with confidence and quality score.
    """
    if _model_data is None:
        raise HTTPException(status_code=503, detail="Model not loaded")
    
    # Call existing predict function from predict_cli.py
    result = predict_params(
        request.pH,
        request.Hardness,
        request.Turbidity,
        request.Sulfate,
        request.Chlorine
    )
    
    if "error" in result:
        raise HTTPException(status_code=500, detail=result["error"])
    
    return PredictResponse(
        is_potable=result["is_potable"],
        confidence=result["confidence"],
        quality_score=result["quality_score"],
        prediction_text=result["prediction_text"]
    )

@app.post("/predict/image", response_model=ImagePredictResponse)
async def predict_image_endpoint(file: UploadFile = File(...)):
    """
    Predict water pollution from an uploaded image.
    
    Accepts: image file (jpg, jpeg, png)
    
    Analyzes water color to detect:
    - Clean water
    - Algae presence
    - Pollution indicators
    
    Returns pollution label and percentage.
    """
    if not _image_detector_ready:
        raise HTTPException(status_code=503, detail="Image detector not ready")
    
    # Validate file type
    allowed_types = ["image/jpeg", "image/jpg", "image/png"]
    if file.content_type not in allowed_types:
        raise HTTPException(
            status_code=400,
            detail=f"Invalid file type. Allowed: jpg, jpeg, png. Got: {file.content_type}"
        )
    
    # Save uploaded file temporarily
    temp_path = None
    try:
        # Create temp file with proper extension
        suffix = ".jpg" if "jpeg" in file.content_type or "jpg" in file.content_type else ".png"
        with tempfile.NamedTemporaryFile(delete=False, suffix=suffix) as tmp:
            shutil.copyfileobj(file.file, tmp)
            temp_path = tmp.name
        
        # Call existing function from water_pollution_detector.py
        label, pollution_pct = predict_pollution_from_color(temp_path)
        
        return ImagePredictResponse(
            label=label,
            pollution_percentage=pollution_pct,
            is_polluted=pollution_pct >= 50
        )
        
    except ValueError as e:
        raise HTTPException(status_code=400, detail=str(e))
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Image analysis failed: {str(e)}")
    finally:
        # Clean up temp file
        if temp_path and os.path.exists(temp_path):
            os.remove(temp_path)


# ============== Legacy endpoint for backward compatibility ==============

class LegacyPredictRequest(BaseModel):
    """Legacy request model using features array."""
    features: List[float]

@app.post("/predict/legacy")
async def predict_legacy_endpoint(request: LegacyPredictRequest):
    """
    Legacy endpoint: Predict using features array.
    features: [pH, Hardness, Turbidity, Sulfate, Chlorine]
    """
    if len(request.features) != 5:
        raise HTTPException(
            status_code=400,
            detail="Expected 5 features: [pH, Hardness, Turbidity, Sulfate, Chlorine]"
        )
    
    pH, Hardness, Turbidity, Sulfate, Chlorine = request.features
    result = predict_params(pH, Hardness, Turbidity, Sulfate, Chlorine)
    
    if "error" in result:
        raise HTTPException(status_code=500, detail=result["error"])
    
    return result
