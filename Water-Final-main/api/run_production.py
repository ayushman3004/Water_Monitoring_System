"""
Production server configuration for Water Quality API.
Run with: python run_production.py
"""

import multiprocessing
import uvicorn

def get_workers():
    """Calculate optimal worker count based on CPU cores."""
    cores = multiprocessing.cpu_count()
    # Formula: (2 x CPU cores) + 1, but cap at 4 for ML models (memory intensive)
    return min(cores * 2 + 1, 4)

if __name__ == "__main__":
    workers = get_workers()
    print(f"🚀 Starting production server with {workers} workers...")
    
    uvicorn.run(
        "main:app",
        host="0.0.0.0",
        port=8000,
        workers=workers,
        log_level="info",
        access_log=True,
        # Remove reload for production!
    )

