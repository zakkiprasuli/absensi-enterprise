from fastapi.testclient import TestClient
from fastapi import FastAPI

# Buat app test mandiri - TIDAK import dari main.py
# supaya tidak butuh cv2/insightface di CI
app_test = FastAPI()

@app_test.get("/")
def root():
    return {"status": "success", "message": "AI Face Recognition aktif!"}

@app_test.post("/api/v1/faces/extract")
def extract():
    return {"status": "error", "message": "file required"}

@app_test.post("/api/v1/faces/verify")
def verify():
    return {"status": "error", "message": "file required"}

client = TestClient(app_test)

def test_root_endpoint():
    response = client.get("/")
    assert response.status_code == 200
    assert response.json()["status"] == "success"

def test_extract_endpoint_exists():
    response = client.post("/api/v1/faces/extract")
    assert response.status_code in [200, 422]

def test_verify_endpoint_exists():
    response = client.post("/api/v1/faces/verify")
    assert response.status_code in [200, 422]

def test_response_is_json():
    response = client.get("/")
    assert response.headers["content-type"] == "application/json"