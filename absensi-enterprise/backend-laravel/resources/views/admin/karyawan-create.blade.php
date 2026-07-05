@extends('layouts.app')

@section('title', 'Tambah Karyawan - Enterprise Attendance')

@section('content')
    <div class="mb-4">
        <a href="{{ route('karyawan.index') }}" class="text-muted small text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Data Karyawan
        </a>
        <h1 class="fw-bold h3 mb-1 mt-2">Daftarkan Karyawan Baru</h1>
        <p class="text-muted small mb-0">Lengkapi data dan unggah foto wajah untuk pendaftaran biometrik.</p>
    </div>

    @if(session('error'))
        <div class="alert alert-danger small py-2">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger small py-2">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card setting-card p-4 p-md-5">
        <form action="{{ route('karyawan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <h6 class="fw-bold text-success mb-3">Data Identitas</h6>
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-medium text-secondary">NIP (Nomor Induk Pegawai)</label>
                    <input type="text" name="nip" class="form-control" value="{{ old('nip') }}" placeholder="Contoh: 16800" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-medium text-secondary">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" placeholder="Nama Karyawan" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-medium text-secondary">Email (untuk login aplikasi)</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="nama@perusahaan.com" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-medium text-secondary">Password Awal</label>
                    <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-medium text-secondary">Nomor HP</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx">
                </div>
            </div>

            <h6 class="fw-bold text-success mb-3">Data Kepegawaian</h6>
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-medium text-secondary">Jabatan</label>
                    <input type="text" name="position" class="form-control" value="{{ old('position') }}" placeholder="Contoh: Staff IT">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-medium text-secondary">Divisi / Departemen</label>
                    <input type="text" name="department" class="form-control" value="{{ old('department') }}" placeholder="Contoh: IT, Finance">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-medium text-secondary">Tanggal Bergabung</label>
                    <input type="date" name="join_date" class="form-control" value="{{ old('join_date') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-medium text-secondary">Status Karyawan</label>
                    <select name="status" class="form-select" required>
                        <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        <option value="resign" {{ old('status') == 'resign' ? 'selected' : '' }}>Resign</option>
                    </select>
                </div>
            </div>

            <h6 class="fw-bold text-success mb-3">Biometrik Wajah</h6>
            <div class="mb-4">
                <label class="form-label small fw-medium text-secondary">Upload Foto Wajah (.jpg / .png)</label>
                <input type="file" name="foto_wajah" class="form-control" accept="image/*" required>
                <p class="text-muted small mt-2 mb-0">
                    <i class="bi bi-info-circle me-1"></i> Pastikan foto wajah terlihat jelas, menghadap depan, dan memiliki pencahayaan yang cukup agar AI dapat mengekstrak vektor wajah dengan akurat.
                </p>
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('karyawan.index') }}" class="btn btn-light rounded-3 px-4">Batal</a>
                <button type="submit" class="btn btn-success rounded-3 px-4 fw-semibold">
                    <i class="bi bi-cpu me-2"></i> Ekstrak & Daftarkan
                </button>
            </div>
        </form>
    </div>
@endsection