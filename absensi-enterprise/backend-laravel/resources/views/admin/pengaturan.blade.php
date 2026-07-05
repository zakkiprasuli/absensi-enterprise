@extends('layouts.app')

@section('title', 'Pengaturan Jam Kerja - Enterprise Attendance')

@section('content')
    <div class="mb-4">
        <h1 class="fw-bold h3 mb-1">Pengaturan Jam Kerja</h1>
        <p class="text-muted small mb-0">Tentukan jam masuk, jam pulang, dan toleransi keterlambatan karyawan.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success small py-2">{{ session('success') }}</div>
    @endif

    <div class="col-md-6">
        <div class="card setting-card p-4">
            <form action="{{ route('pengaturan.update') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-medium text-secondary">Jam Masuk</label>
                    <input type="time" name="jam_masuk" class="form-control" value="{{ substr($setting->jam_masuk, 0, 5) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-medium text-secondary">Jam Pulang</label>
                    <input type="time" name="jam_pulang" class="form-control" value="{{ substr($setting->jam_pulang, 0, 5) }}" required>
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-medium text-secondary">Toleransi Keterlambatan (Menit)</label>
                    <div class="input-group">
                        <input type="number" name="toleransi_terlambat" class="form-control" value="{{ $setting->toleransi_terlambat }}" required>
                        <span class="input-group-text bg-light">Menit</span>
                    </div>
                    <p class="text-muted small mt-2 mb-0">
                        Karyawan dianggap "Tepat Waktu" jika absen sebelum jam masuk + toleransi ini.
                    </p>
                </div>
                <button type="submit" class="btn btn-success w-100 fw-semibold rounded-3 py-2">
                    <i class="bi bi-save me-2"></i> Simpan Pengaturan
                </button>
            </form>
        </div>
    </div>
@endsection