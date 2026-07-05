@extends('layouts.app')

@section('title', 'Edit Karyawan - Enterprise Attendance')

@section('content')
    <div class="mb-4">
        <a href="{{ route('karyawan.index') }}" class="text-muted small text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Data Karyawan
        </a>
        <h1 class="fw-bold h3 mb-1 mt-2">Edit Data Karyawan</h1>
        <p class="text-muted small mb-0">{{ $employee->name }} — NIP {{ $employee->nip }}</p>
    </div>

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
        <form action="{{ route('karyawan.update', $employee->id) }}" method="POST">
            @csrf
            @method('PUT')

            <h6 class="fw-bold text-success mb-3">Data Identitas</h6>
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-medium text-secondary">NIP</label>
                    <input type="text" name="nip" class="form-control" value="{{ old('nip', $employee->nip) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-medium text-secondary">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama', $employee->name) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-medium text-secondary">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $employee->email) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-medium text-secondary">Password Baru (opsional)</label>
                    <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin diganti">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-medium text-secondary">Nomor HP</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $employee->phone) }}">
                </div>
            </div>

            <h6 class="fw-bold text-success mb-3">Data Kepegawaian</h6>
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-medium text-secondary">Jabatan</label>
                    <input type="text" name="position" class="form-control" value="{{ old('position', $employee->position) }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-medium text-secondary">Divisi / Departemen</label>
                    <input type="text" name="department" class="form-control" value="{{ old('department', $employee->department) }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-medium text-secondary">Tanggal Bergabung</label>
                    <input type="date" name="join_date" class="form-control" value="{{ old('join_date', $employee->join_date?->format('Y-m-d')) }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-medium text-secondary">Status Karyawan</label>
                    <select name="status" class="form-select" required>
                        @foreach(['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif', 'resign' => 'Resign'] as $val => $label)
                            <option value="{{ $val }}" {{ old('status', $employee->status) == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <p class="text-muted small">
                <i class="bi bi-info-circle me-1"></i> Untuk mengganti foto wajah, gunakan fitur "Registrasi Ulang Wajah" (belum tersedia, bisa ditambahkan terpisah).
            </p>

            <div class="d-flex gap-2 justify-content-end mt-4">
                <a href="{{ route('karyawan.index') }}" class="btn btn-light rounded-3 px-4">Batal</a>
                <button type="submit" class="btn btn-success rounded-3 px-4 fw-semibold">
                    <i class="bi bi-save me-2"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
@endsection