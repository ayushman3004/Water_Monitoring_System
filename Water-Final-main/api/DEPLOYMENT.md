# Deployment Guide - Water Quality API

## Quick Comparison

| Deployment | Cost | Difficulty | Best For |
|------------|------|------------|----------|
| **Railway/Render** | Free tier | Easy | Demo/Testing |
| **DigitalOcean** | $5-12/mo | Medium | Small production |
| **AWS/GCP** | $20+/mo | Hard | Enterprise |
| **Local Server** | Hardware | Medium | Private use |

---

## Option 1: Railway (Easiest - Free Tier)

### Steps:
1. Create account at [railway.app](https://railway.app)
2. Connect your GitHub repo
3. Add a `Procfile`:

```
web: uvicorn main:app --host 0.0.0.0 --port $PORT
```

4. Set environment variables (if any)
5. Deploy!

**Limits:** 500 hours/month free, 512MB RAM

---

## Option 2: Render (Free Tier)

### Steps:
1. Create account at [render.com](https://render.com)
2. New Web Service → Connect repo
3. Build command: `pip install -r requirements.txt`
4. Start command: `uvicorn main:app --host 0.0.0.0 --port $PORT`

**Limits:** Sleeps after 15 min inactivity, 512MB RAM

---

## Option 3: DigitalOcean App Platform ($5/mo)

### Steps:
1. Create Droplet or use App Platform
2. Upload your code
3. Run:

```bash
# Install dependencies
pip install -r requirements.txt

# Run with Gunicorn (production)
gunicorn main:app -w 2 -k uvicorn.workers.UvicornWorker --bind 0.0.0.0:8000
```

---

## Option 4: Docker (Any Platform)

### Create `Dockerfile`:

```dockerfile
FROM python:3.11-slim

WORKDIR /app

# Copy ML model files first (for caching)
COPY water_quality_model.pkl .
COPY predict_cli.py .
COPY water_pollution_detector.py .

# Copy API files
COPY api/requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

COPY api/ ./api/

WORKDIR /app/api

EXPOSE 8000

CMD ["uvicorn", "main:app", "--host", "0.0.0.0", "--port", "8000", "--workers", "2"]
```

### Build & Run:
```bash
docker build -t water-api .
docker run -p 8000:8000 water-api
```

---

## Production Checklist

### ✅ Before Deploying:

- [ ] Remove `--reload` flag
- [ ] Set `workers` based on CPU (2-4 for ML)
- [ ] Enable HTTPS (use reverse proxy like Nginx)
- [ ] Set CORS origins to your domain only
- [ ] Add API key authentication (optional)
- [ ] Set up logging
- [ ] Monitor memory usage (~100-200MB per worker)

### ✅ Environment Variables:

```bash
# Example .env file
API_HOST=0.0.0.0
API_PORT=8000
API_WORKERS=2
CORS_ORIGINS=https://yourdomain.com
```

---

## Memory Requirements

| Component | Memory |
|-----------|--------|
| Base Python | ~50MB |
| FastAPI + Uvicorn | ~30MB |
| ML Model (sklearn) | ~50-100MB |
| OpenCV | ~50MB |
| **Total per worker** | ~200MB |

**Recommendation:** Minimum 512MB RAM, 1GB preferred

---

## Expected Performance

| Workers | Requests/sec | Concurrent Users |
|---------|--------------|------------------|
| 1 | ~10-20 | 5-10 |
| 2 | ~20-40 | 10-20 |
| 4 | ~40-80 | 20-50 |

*ML predictions are CPU-bound, so more workers = better throughput*

---

## Monitoring

### Health Check Endpoint:
```bash
curl https://your-api.com/health
```

### Simple Uptime Monitor:
Use [UptimeRobot](https://uptimerobot.com) (free) to ping `/health` every 5 minutes.

---

## Cost Estimates

| Traffic | Platform | Cost/Month |
|---------|----------|------------|
| <1000 req/day | Railway/Render | Free |
| 1000-10000 req/day | DigitalOcean | $5-12 |
| 10000+ req/day | AWS/GCP | $20-50+ |

---

## Quick Start Commands

### Development:
```bash
uvicorn main:app --reload --port 8000
```

### Production (Local):
```bash
python run_production.py
# or
gunicorn main:app -w 2 -k uvicorn.workers.UvicornWorker --bind 0.0.0.0:8000
```

### Production (Docker):
```bash
docker-compose up -d
```

