from fastapi.testclient import TestClient
from main import app

client = TestClient(app)

def test_root_endpoint():
    response = client.get("/")
    assert response.status_code == 200
    assert response.json()["status"] == "success"

def test_extract_without_file():
    response = client.post("/api/v1/faces/extract")
    # Harus 422 (validation error) bukan 500
    assert response.status_code == 422