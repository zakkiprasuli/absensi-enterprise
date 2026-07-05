@extends('layouts.app')

@section('title', 'Laporan Kehadiran - Enterprise Attendance')

@section('content')
     <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
                    <h1 class="fw-bold h3 mb-1">Riwayat & Laporan Absensi</h1>
                    <p class="text-muted small mb-0">Rekapitulasi jejak kehadiran seluruh karyawan berbasis AI & GPS.</p>
                </div>
                <button class="btn btn-outline-success rounded-3 px-4 py-2 fw-semibold shadow-sm small">
                    <i class="bi bi-file-earmark-excel-fill me-2"></i> Unduh (.xlsx)
                </button>
            </div>

            <div class="card table-card">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Nama Karyawan</th>
                                <th>Jam Masuk</th>
                                <th>Jam Pulang</th>
                                <th>Jarak Koordinat</th>
                                <th>Status Bukti</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendances as $row)
                            <tr>
                                <td>
                                    <span class="fw-medium text-dark">{{ \Carbon\Carbon::parse($row->created_at)->format('d M Y') }}</span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $row->employee->nama ?? 'Tidak Diketahui' }}</div>
                                    <div class="text-secondary small font-monospace">{{ $row->nip }}</div>
                                </td>
                                <td>
                                    @if($row->waktu_masuk)
                                        <span class="badge bg-success-subtle text-success px-2 py-1 rounded-2"><i class="bi bi-box-arrow-in-right me-1"></i> {{ $row->waktu_masuk }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($row->waktu_pulang)
                                        <span class="badge bg-warning-subtle text-warning px-2 py-1 rounded-2"><i class="bi bi-box-arrow-left me-1"></i> {{ $row->waktu_pulang }}</span>
                                    @else
                                        <span class="badge bg-light text-secondary px-2 py-1 rounded-2">Belum Pulang</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted small"><i class="bi bi-geo-alt me-1"></i> {{ $row->jarak_meter ?? '0' }} m</span>
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">Wajah Cocok</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-folder-x fs-1 d-block mb-2"></i>
                                    Belum ada data riwayat absensi yang tercatat.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
@endsection
