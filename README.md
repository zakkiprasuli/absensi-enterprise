# Absensi Enterprise

Sistem absensi biometrik berbasis AI dengan face recognition.

## Teknologi
- **Laravel 12** — Backend Web Admin & REST API
- **FastAPI** — AI Microservice (Face Recognition dengan InsightFace)
- **PostgreSQL + pgvector** — Database dengan dukungan vector embedding
- **Flutter** — Mobile App karyawan
- **Docker & Docker Compose** — Containerization & Orkestrasi

## Cara Menjalankan dengan Docker

### Prasyarat
- Docker Desktop terinstall
- Git

### Langkah

1. Clone repository:
```bash
   git clone https://github.com/USERNAME/absensi-enterprise.git
   cd absensi-enterprise
```

2. Jalankan semua service:
```bash
   docker compose up -d
```

3. Jalankan migrasi database:
```bash
   docker exec absensi_laravel php artisan migrate
```

4. Akses aplikasi:
   - Web Admin: http://localhost:8000
   - FastAPI Docs: http://localhost:8001/docs
   - Database: localhost:5432

### Menghentikan service
```bash
docker compose down
```

### Menghapus semua data (termasuk database)
```bash
docker compose down -v
```

## Struktur Project