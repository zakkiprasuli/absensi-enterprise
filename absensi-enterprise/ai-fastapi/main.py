from fastapi import FastAPI, File, UploadFile, HTTPException
import cv2
import numpy as np
from insightface.app import FaceAnalysis
from sklearn.metrics.pairwise import cosine_similarity
from fastapi import Form

# 1. Inisialisasi Aplikasi FastAPI
app = FastAPI(
    title="AI Absensi Enterprise",
    description="Microservice untuk Face Recognition",
    version="1.0.0"
)

# 2. Inisialisasi Model InsightFace
print("Sedang memuat model AI...")
face_app = FaceAnalysis(name='buffalo_l')
face_app.prepare(ctx_id=-1, det_size=(640, 640))
print("Model AI siap!")

@app.get("/")
def root_check():
    return {"status": "success", "message": "AI Face Recognition aktif!"}

# 3. Endpoint untuk Mengekstrak Wajah
@app.post("/api/v1/faces/extract")
async def extract_face(file: UploadFile = File(...)):
    try:
        contents = await file.read()
        nparr = np.frombuffer(contents, np.uint8)
        img = cv2.imdecode(nparr, cv2.IMREAD_COLOR)
        
        if img is None:
            return {
                "status": "error",
                "message": "File bukan gambar yang valid atau rusak saat di-decode"
            }

        # Deteksi wajah menggunakan InsightFace
        faces = face_app.get(img)

        # 🚨 ANTISIPASI CRASH: Jika faces bernilai None atau kosong
        if faces is None or len(faces) == 0:
            return {
                "status": "error",
                "message": "Tidak ada wajah yang terdeteksi di dalam foto"
            }
        
        if len(faces) > 1:
            return {
                "status": "error",
                "message": "Terdeteksi lebih dari satu wajah. Pastikan hanya ada satu orang di dalam frame"
            }

        # Ekstrak embedding vektor
        embedding = faces[0].embedding.tolist()

        return {
            "status": "success",
            "message": "Wajah berhasil diekstrak",
            "data": {
                "embedding": embedding,
                "embedding_preview": embedding[:5], 
                "total_dimensions": len(embedding)
            }
        }

    except Exception as e:
        # Mencetak error asli ke terminal Python agar mudah kamu pantau
        print(f"❌ ERROR INTERNAL FASTAPI: {str(e)}")
        raise HTTPException(status_code=500, detail=f"Internal AI Error: {str(e)}")

# 4. Endpoint untuk Membandingkan Dua Wajah (Verifikasi)
@app.post("/api/v1/faces/compare")
async def compare_faces(file1: UploadFile = File(...), file2: UploadFile = File(...)):
    try:
        # Membaca gambar pertama
        contents1 = await file1.read()
        nparr1 = np.frombuffer(contents1, np.uint8)
        img1 = cv2.imdecode(nparr1, cv2.IMREAD_COLOR)

        # Membaca gambar kedua
        contents2 = await file2.read()
        nparr2 = np.frombuffer(contents2, np.uint8)
        img2 = cv2.imdecode(nparr2, cv2.IMREAD_COLOR)

        if img1 is None or img2 is None:
            raise HTTPException(status_code=400, detail="File bukan gambar yang valid")

        # Deteksi wajah di kedua gambar
        faces1 = face_app.get(img1)
        faces2 = face_app.get(img2)

        if len(faces1) == 0 or len(faces2) == 0:
            raise HTTPException(status_code=400, detail="Wajah tidak terdeteksi di salah satu/kedua gambar")

        # Ambil embedding dari wajah pertama di masing-masing gambar
        emb1 = faces1[0].embedding
        emb2 = faces2[0].embedding

        # Hitung Cosine Similarity
        similarity = cosine_similarity([emb1], [emb2])[0][0]
        similarity_score = float(similarity)

        # Threshold kecocokan 0.55
        is_match = similarity_score > 0.55

        return {
            "status": "success",
            "message": "Perbandingan selesai",
            "data": {
                "similarity_score": similarity_score,
                "is_match": is_match,
                "threshold_used": 0.55
            }
        }

    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))
    

# 5. Endpoint Verifikasi: 1 Foto Baru vs 1 Embedding Tersimpan
@app.post("/api/v1/faces/verify")
async def verify_face(
    file: UploadFile = File(...),
    stored_embedding: str = Form(...)  # Embedding dari DB, dikirim sebagai string JSON
):
    try:
        import json

        # Baca & decode foto baru
        contents = await file.read()
        nparr = np.frombuffer(contents, np.uint8)
        img = cv2.imdecode(nparr, cv2.IMREAD_COLOR)

        if img is None:
            return {"status": "error", "message": "File bukan gambar yang valid"}

        # Deteksi wajah di foto baru
        faces = face_app.get(img)
        if not faces or len(faces) == 0:
            return {"status": "error", "message": "Wajah tidak terdeteksi di foto"}

        # Ambil embedding dari foto baru
        new_embedding = faces[0].embedding

        # Parse embedding tersimpan dari string JSON
        try:
            stored_list = json.loads(stored_embedding)
            stored_emb = np.array(stored_list, dtype=np.float32)
        except Exception:
            return {"status": "error", "message": "Format embedding tersimpan tidak valid"}

        # Hitung cosine similarity
        similarity = cosine_similarity([new_embedding], [stored_emb])[0][0]
        similarity_score = float(similarity)
        is_match = similarity_score > 0.55

        return {
            "status": "success",
            "message": "Verifikasi selesai",
            "data": {
                "similarity_score": similarity_score,
                "is_match": is_match,
                "threshold_used": 0.55
            }
        }

    except Exception as e:
        print(f"❌ ERROR VERIFY: {str(e)}")
        raise HTTPException(status_code=500, detail=f"Internal AI Error: {str(e)}")