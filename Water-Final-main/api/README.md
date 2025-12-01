# Water Quality Prediction API

FastAPI wrapper for both parameter-based and image-based water quality prediction models.

## Setup

```bash
cd Water-Final-main/api
pip install -r requirements.txt
```

## Run the Server

```bash
uvicorn main:app --reload --host 127.0.0.1 --port 8000
```

Then open: http://localhost:8000/docs for interactive API documentation.

---

## Endpoints

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/health` | GET | Health check - shows model status |
| `/predict` | POST | Parameter-based water quality prediction |
| `/predict/image` | POST | Image-based pollution detection |
| `/predict/legacy` | POST | Legacy endpoint using features array |

---

## API Examples

### 1. Health Check

```bash
curl http://localhost:8000/health
```

**Response:**
```json
{
  "status": "ok",
  "model_loaded": true,
  "image_detector_loaded": true
}
```

---

### 2. Parameter-based Prediction (`/predict`)

**cURL:**
```bash
curl -X POST "http://localhost:8000/predict" \
  -H "Content-Type: application/json" \
  -d '{
    "pH": 7.0,
    "Hardness": 150.0,
    "Turbidity": 5.0,
    "Sulfate": 250.0,
    "Chlorine": 1.0
  }'
```

**Response:**
```json
{
  "is_potable": true,
  "confidence": 0.85,
  "quality_score": 85.0,
  "prediction_text": "Potable"
}
```

**PHP Example:**
```php
<?php
$url = "http://localhost:8000/predict";

$data = [
    "pH" => 7.0,
    "Hardness" => 150.0,
    "Turbidity" => 5.0,
    "Sulfate" => 250.0,
    "Chlorine" => 1.0
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $result = json_decode($response, true);
    echo "Potable: " . ($result["is_potable"] ? "Yes" : "No") . "\n";
    echo "Confidence: " . round($result["confidence"] * 100, 1) . "%\n";
    echo "Quality Score: " . $result["quality_score"] . "/100\n";
} else {
    echo "Error: " . $response . "\n";
}
?>
```

---

### 3. Image-based Prediction (`/predict/image`)

**cURL:**
```bash
curl -X POST "http://localhost:8000/predict/image" \
  -F "file=@water_sample.jpg"
```

**Response:**
```json
{
  "label": "Clean Water",
  "pollution_percentage": 10.0,
  "is_polluted": false
}
```

**PHP Example:**
```php
<?php
$url = "http://localhost:8000/predict/image";
$imagePath = "/path/to/water_image.jpg";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    "file" => new CURLFile($imagePath, "image/jpeg", "water.jpg")
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $result = json_decode($response, true);
    echo "Detection: " . $result["label"] . "\n";
    echo "Pollution Level: " . $result["pollution_percentage"] . "%\n";
    echo "Is Polluted: " . ($result["is_polluted"] ? "Yes" : "No") . "\n";
} else {
    echo "Error: " . $response . "\n";
}
?>
```

---

### 4. Legacy Endpoint (`/predict/legacy`)

For backward compatibility with the old features array format:

```bash
curl -X POST "http://localhost:8000/predict/legacy" \
  -H "Content-Type: application/json" \
  -d '{"features": [7.0, 150.0, 5.0, 250.0, 1.0]}'
```

---

## Parameter Reference

### Parameter-based Prediction

| Parameter | Range | Description | Ideal Value |
|-----------|-------|-------------|-------------|
| pH | 0-14 | Acidity/basicity | 6.5-8.5 |
| Hardness | 0-1000 mg/L | Mineral content | ≤ 200 |
| Turbidity | 0-100 NTU | Water clarity | ≤ 5 |
| Sulfate | 0-1000 mg/L | Sulfate content | ≤ 250 |
| Chlorine | 0-10 mg/L | Chlorine level | 0.2-2.0 |

### Image-based Detection

Supported formats: `.jpg`, `.jpeg`, `.png`

Detects:
- **Clean Water** (pollution < 50%)
- **Algae-rich** (green tint detected)
- **Polluted Water** (red/brown tint detected)
- **Uncertain** (mixed colors)
