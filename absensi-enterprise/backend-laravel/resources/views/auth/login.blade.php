<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Enterprise Attendance</title>
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
            <h3 class="fw-bold text-success">Sign In HRD</h3>
            <p class="text-muted small">Masuk untuk mengelola sistem absensi biometrik</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success small py-2">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger small py-2">{{ $errors->first() }}</div>
        @endif

        <form action="{{ url('/login') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-medium text-secondary">Email Address</label>
                <input type="email" name="email" class="form-control rounded-3 py-2" placeholder="nama@perusahaan.com" required value="{{ old('email') }}">
            </div>
            <div class="mb-4">
                <label class="form-label small fw-medium text-secondary">Password</label>
                <input type="password" name="password" class="form-control rounded-3 py-2" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-success w-100 rounded-3 py-2 fw-semibold mb-3">Masuk ke Dashboard</button>
            <div class="text-center">
                <span class="text-muted small">Belum punya akun? <a href="{{ url('/register') }}" class="text-success text-decoration-none fw-medium">Daftar di sini</a></span>
            </div>
        </form>
    </div>
</body>
</html>