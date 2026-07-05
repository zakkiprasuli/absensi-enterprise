<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Enterprise Attendance')</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    @stack('styles')

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #fcfdfe; color: #1e293b; }
        .sidebar { background-color: #ffffff; border-right: 1px solid #f1f5f9; min-height: 100vh; box-shadow: 4px 0 24px rgba(0, 0, 0, 0.01); }
        
        /* Desain Logo Premium */
        .logo-container { padding: 32px 0; display: flex; justify-content: center; align-items: center; }
        .premium-logo {
            width: 52px; height: 52px; background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 16px; display: flex; align-items: center; justify-content: center;
            color: #ffffff; font-size: 1.5rem; box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3); position: relative; transition: all 0.3s ease;
        }
        .premium-logo::after {
            content: ''; position: absolute; inset: -4px; border: 2px solid rgba(16, 185, 129, 0.2); border-radius: 20px; transition: all 0.3s ease;
        }
        
        /* Desain Navigasi */
        .nav-link-custom {
            color: #64748b; font-weight: 600; font-size: 0.9rem; padding: 14px 20px; border-radius: 12px;
            display: flex; align-items: center; gap: 14px; transition: all 0.2s; text-decoration: none; margin-bottom: 4px;
        }
        .nav-link-custom:hover { background-color: #f8fafc; color: #0f172a; }
        .nav-link-custom.active { background: linear-gradient(135deg, #e6fbf2 0%, #d1fae5 100%); color: #047857; }

        /* Card Global */
        .setting-card, .stat-card, .table-card {
            background: #ffffff; border: 1px solid #f1f5f9; border-radius: 20px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.015);
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 d-none d-md-block sidebar p-3 position-fixed">
            <div class="logo-container">
                <div class="premium-logo" title="Sistem Absensi AI"><i class="bi bi-shield-lock-fill"></i></div>
            </div>
            
            <div class="nav flex-column gap-1">
                <a href="{{ route('dashboard') }}" class="nav-link-custom {{ request()->is('dashboard*') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill"></i> Dashboard Real-Time
                </a>
                <a href="{{ route('karyawan.index') }}" class="nav-link-custom {{ request()->is('karyawan*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i> Data Karyawan
                </a>
                <a href="{{ route('geofencing.index') }}" class="nav-link-custom {{ request()->is('geofencing*') ? 'active' : '' }}">
                    <i class="bi bi-geo-alt-fill"></i> Radius Geofencing
                </a>
                <a href="{{ route('cuti.index') }}" class="nav-link-custom {{ request()->is('cuti*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-x-fill"></i> Izin & Cuti
                </a>
                <a href="{{ route('laporan.index') }}" class="nav-link-custom {{ request()->is('laporan*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-bar-graph-fill"></i> Laporan Kehadiran
                </a>
            </div>
            <a href="{{ route('pengaturan.index') }}" class="nav-link-custom {{ request()->is('pengaturan*') ? 'active' : '' }}">
                <i class="bi bi-gear-fill"></i> Pengaturan
            </a>

            <div class="mt-5 px-3">
            <div class="d-flex align-items-center gap-2 mb-3 px-2 py-2 rounded-3" style="background-color: #f8fafc;">
                <div class="rounded-circle bg-success d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                    style="width: 38px; height: 38px; font-size: 0.9rem;">
                    {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="overflow-hidden">
                    <p class="mb-0 small fw-bold text-truncate" style="line-height: 1.2;">
                        {{ Auth::user()->name ?? 'Admin' }}
                    </p>
                    <p class="mb-0 text-muted text-truncate" style="font-size: 0.72rem;">
                        {{ Auth::user()->email ?? '-' }}
                    </p>
                </div>
            </div>
            
            <div class="mt-5 px-3">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger w-100 rounded-3 small fw-semibold py-2">
                        <i class="bi bi-box-arrow-left me-2"></i> Keluar
                    </button>
                </form>
            </div>
        </div>
        </div>

        <div class="col-md-10 offset-md-2 p-4 p-md-5">
            @yield('content')
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>