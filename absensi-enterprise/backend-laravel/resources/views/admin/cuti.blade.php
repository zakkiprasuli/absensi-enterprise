@extends('layouts.app')

@section('title', 'Izin & Cuti - Enterprise Attendance')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold h3 mb-1">Manajemen Izin & Cuti</h1>
            <p class="text-muted small mb-0">Kelola pengajuan izin, cuti, dan sakit karyawan.</p>
        </div>
        <button class="btn btn-success rounded-3 px-4 py-2 fw-semibold shadow-sm small" data-bs-toggle="modal" data-bs-target="#modalAjukanCuti">
            <i class="bi bi-calendar-plus me-2"></i> Ajukan Izin/Cuti
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success small py-2">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger small py-2">{{ session('error') }}</div>
    @endif

    <div class="card table-card">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Nama Karyawan</th>
                        <th>Jenis</th>
                        <th>Periode</th>
                        <th>Alasan</th>
                        <th>Lampiran</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaves as $leave)
                    <tr>
                        <td class="fw-semibold">{{ $leave->employee->name ?? 'Unknown' }}</td>
                        <td><span class="text-capitalize">{{ $leave->type }}</span></td>
                        <td>
                            {{ $leave->start_date->format('d M Y') }} - {{ $leave->end_date->format('d M Y') }}
                        </td>
                        <td class="small text-muted">{{ \Illuminate\Support\Str::limit($leave->reason, 40) }}</td>
                        <td>
                            @if($leave->attachment)
                                <a href="{{ asset('storage/' . $leave->attachment) }}" target="_blank" class="small">Lihat</a>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td>
                            @if($leave->status === 'approved')
                                <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">Disetujui</span>
                            @elseif($leave->status === 'rejected')
                                <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill">Ditolak</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning px-3 py-2 rounded-pill">Menunggu</span>
                            @endif
                        </td>
                        <td class="text-end">
                            @if($leave->status === 'pending')
                                <form action="{{ route('cuti.approve', $leave->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn btn-sm btn-light text-success me-1" title="Setujui">
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                </form>
                                <button class="btn btn-sm btn-light text-danger me-1" title="Tolak"
                                    data-bs-toggle="modal" data-bs-target="#modalTolak{{ $leave->id }}">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                            @endif
                            <form action="{{ route('cuti.destroy', $leave->id) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Hapus data pengajuan ini?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-light text-secondary" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Modal Tolak per baris -->
                    <div class="modal fade" id="modalTolak{{ $leave->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg rounded-4">
                                <form action="{{ route('cuti.reject', $leave->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <div class="modal-header border-bottom-0">
                                        <h6 class="modal-title fw-bold text-danger">Tolak Pengajuan</h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <label class="form-label small fw-medium">Alasan Penolakan</label>
                                        <textarea name="rejection_reason" class="form-control" rows="3" required></textarea>
                                    </div>
                                    <div class="modal-footer border-top-0">
                                        <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-danger rounded-3 px-4">Tolak</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            Belum ada pengajuan izin/cuti.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Ajukan -->
    <div class="modal fade" id="modalAjukanCuti" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold text-success">Ajukan Izin / Cuti</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('cuti.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label small fw-medium text-secondary">Karyawan</label>
                            <select name="employee_id" class="form-select" required>
                                <option value="">-- Pilih Karyawan --</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->nip }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium text-secondary">Jenis</label>
                            <select name="type" class="form-select" required>
                                <option value="izin">Izin</option>
                                <option value="cuti">Cuti</option>
                                <option value="sakit">Sakit</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label small fw-medium text-secondary">Tanggal Mulai</label>
                                <input type="date" name="start_date" class="form-control" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label small fw-medium text-secondary">Tanggal Selesai</label>
                                <input type="date" name="end_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium text-secondary">Alasan</label>
                            <textarea name="reason" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium text-secondary">Lampiran (opsional)</label>
                            <input type="file" name="attachment" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success rounded-3 px-4">Ajukan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection