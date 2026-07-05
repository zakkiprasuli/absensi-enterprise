<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Enterprise Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; height: 100vh; display: flex; align-items: center; }
        .auth-card { border: none; border-radius: 16px; box-shadow: 0 4px 30px rgba(0, 0, 0, 0.03); background: #ffffff; width: 100%; max-width: 400px; padding: 32px; margin: auto; }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="text-center mb-4">
            <h3 class="fw-bold text-success">Create Admin Account</h3>
            <p class="text-muted small">Daftarkan akun manajemen HRD baru</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger small py-2">{{ $errors->first() }}</div>
        @endif

        <form action="{{ url('/register') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-medium text-secondary">Nama Lengkap</label>
                <input type="text" name="name" class="form-control rounded-3 py-2" placeholder="Nama Anda" required value="{{ old('name') }}">
            </div>
            <div class="mb-3">
                <label class="form-label small fw-medium text-secondary">Email Kantor</label>
                <input type="email" name="email" class="form-control rounded-3 py-2" placeholder="nama@perusahaan.com" required value="{{ old('email') }}">
            </div>
            <div class="mb-3">
                <label class="form-label small fw-medium text-secondary">Password</label>
                <input type="password" name="password" class="form-control rounded-3 py-2" placeholder="Minimal 6 karakter" required>
            </div>
            <div class="mb-4">
                <label class="form-label small fw-medium text-secondary">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="form-control rounded-3 py-2" placeholder="Ulangi password" required>
            </div>
            <button type="submit" class="btn btn-success w-100 rounded-3 py-2 fw-semibold mb-3">Daftar Akun</button>
            <div class="text-center">
                <span class="text-muted small">Sudah punya akun? <a href="{{ url('/login') }}" class="text-success text-decoration-none fw-medium">Login masuk</a></span>
            </div>
        </form>
    </div>
</body>
</html>