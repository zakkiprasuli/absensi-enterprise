@extends('layouts.app')

@section('title', 'Manajemen Karyawan - Enterprise Attendance')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold h3 mb-1">Manajemen Karyawan</h1>
            <p class="text-muted small mb-0">Kelola data personel dan pendaftaran biometrik wajah.</p>
        </div>
        <a href="{{ route('karyawan.create') }}" class="btn btn-success rounded-3 px-4 py-2 fw-semibold shadow-sm small">
            <i class="bi bi-person-plus-fill me-2"></i> Tambah Karyawan Baru
        </a>
    </div>

    <div class="card table-card">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Data Wajah</th>
                        <th>NIP</th>
                        <th>Nama Karyawan</th>
                        <th>Jabatan</th>
                        <th>Status</th>
                        <th>Tanggal Terdaftar</th>
                        <th>Status Vektor AI</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $emp)
                    <tr>
                        <td>
                            <div class="bg-light d-flex align-items-center justify-content-center rounded-3" style="width: 45px; height: 45px;">
                                <i class="bi bi-person-bounding-box text-success"></i>
                            </div>
                        </td>
                        <td><span class="text-secondary font-monospace fw-medium">{{ $emp->nip }}</span></td>
                        <td><div class="fw-semibold text-dark">{{ $emp->name }}</div></td>
                        <td class="text-muted small">{{ $emp->position ?? '-' }}</td>
                        <td>
                            @if($emp->status === 'aktif')
                                <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">Aktif</span>
                            @elseif($emp->status === 'resign')
                                <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill">Resign</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning px-3 py-2 rounded-pill">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('karyawan.edit', $emp->id) }}" class="btn btn-sm btn-light text-primary me-1" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <form action="{{ route('karyawan.toggleStatus', $emp->id) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('{{ $emp->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan kembali' }} karyawan ini?');">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-sm btn-light {{ $emp->status === 'aktif' ? 'text-danger' : 'text-success' }}"
                                    title="{{ $emp->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <i class="bi bi-{{ $emp->status === 'aktif' ? 'pause-circle' : 'play-circle' }}"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            Belum ada data karyawan yang terdaftar di database.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection