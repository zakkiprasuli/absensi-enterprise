@extends('layouts.app')

@section('title', 'Dashboard - Enterprise Attendance')

@push('styles')
<style>
    .card-stat { border: none; border-radius: 12px; color: white; overflow: hidden; position: relative; }
    .card-stat .icon-bg { position: absolute; right: -10px; bottom: -10px; font-size: 5rem; opacity: 0.15; }
    .bg-blue-custom { background: linear-gradient(135deg, #2b84f3, #1967d2); }
    .bg-green-custom { background: linear-gradient(135deg, #34d329, #22a519); }
    .bg-orange-custom { background: linear-gradient(135deg, #ff8c00, #e65c00); }
    .bg-red-custom { background: linear-gradient(135deg, #ff0f47, #d00030); }
    .content-card { background: #fff; border: 1px solid #f1f5f9; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); }
    .table-sm th, .table-sm td { font-size: 0.85rem; padding: 10px; vertical-align: middle; }
    .badge-custom { padding: 5px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; }
</style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold h4 mb-1 text-dark">Dashboard</h1>
            <p class="text-muted small mb-0">Home &raquo; Dashboard ({{ date('d M Y') }})</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card card-stat bg-blue-custom p-3 h-100">
                <i class="bi bi-check-circle icon-bg"></i>
                <h4 class="fw-bold mb-1">{{ $tepatWaktu }}/{{ $totalEmployees }}</h4>
                <p class="small mb-3">Presensi Tepat Waktu</p>
                <div class="d-flex justify-content-between align-items-center mt-auto">
                    <span class="small fw-bold">{{ date('Y') }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stat bg-green-custom p-3 h-100">
                <i class="bi bi-clock-history icon-bg"></i>
                <h4 class="fw-bold mb-1">{{ $terlambat }}/{{ $totalEmployees }}</h4>
                <p class="small mb-3">Terlambat Masuk</p>
                <div class="d-flex justify-content-between align-items-center mt-auto">
                    <span class="small fw-bold">{{ date('Y') }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stat bg-orange-custom p-3 h-100">
                <i class="bi bi-box-arrow-left icon-bg"></i>
                <h4 class="fw-bold mb-1">{{ $pulangAwal }}/{{ $totalEmployees }}</h4>
                <p class="small mb-3">Pulang Awal</p>
                <div class="d-flex justify-content-between align-items-center mt-auto">
                    <span class="small fw-bold">{{ date('Y') }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stat bg-red-custom p-3 h-100">
                <i class="bi bi-person-x icon-bg"></i>
                <h4 class="fw-bold mb-1">{{ $tidakAbsen }}/{{ $totalEmployees }}</h4>
                <p class="small mb-3">Tidak Absen</p>
                <div class="d-flex justify-content-between align-items-center mt-auto">
                    <span class="small fw-bold">{{ date('Y') }}</span>
                </div>
            </div>
        </div>
            <div class="col-md-4 col-12">
            <div class="card card-stat p-3 h-100" style="background: linear-gradient(135deg, #8e44ad, #6c3483);">
                <i class="bi bi-calendar-x icon-bg"></i>
                <h4 class="fw-bold mb-1">{{ $sedangCuti }}</h4>
                <p class="small mb-3">Sedang Cuti/Izin</p>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="content-card p-3 h-100">
                <h6 class="text-muted fw-bold mb-3 border-bottom pb-2">Presensi Perbulan ({{ date('Y') }})</h6>
                <div style="height: 300px; width: 100%;">
                    <canvas id="barChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="content-card p-3 h-100">
                <h6 class="text-muted fw-bold mb-3 border-bottom pb-2">Presensi Pertahun</h6>
                <div style="height: 300px; width: 100%; position: relative;">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-5">
            <div class="content-card p-3 h-100">
                <h6 class="text-muted fw-bold mb-3 border-bottom pb-2">Presensi Tepat Waktu (Top 5 Bulan Ini)</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-borderless table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th class="text-end">Total Tepat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topTepatWaktu as $index => $top)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="fw-semibold">{{ $top->employee->nama ?? $top->employee->name ?? 'Unknown' }}</td>
                                    <td class="text-end">{{ $top->total }} Hari</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted">Belum ada data bulan ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="content-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                    <h6 class="text-muted fw-bold mb-0">Log Absensi Terbaru</h6>
                    <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-file-earmark-excel"></i> XLSX</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Pegawai</th>
                                <th>Tanggal</th>
                                <th>Jam Masuk</th>
                                <th>Jam Pulang</th>
                                <th>Status Masuk</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logTerbaru as $log)
                                <tr>
                                    <td class="fw-semibold">{{ $log->employee->nama ?? $log->employee->name ?? 'Unknown' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($log->created_at)->format('Y-m-d') }}</td>
                                    <td>{{ $log->clock_in ? \Carbon\Carbon::parse($log->clock_in)->format('H:i:s') : '-' }}</td>
                                    <td>{{ $log->clock_out ? \Carbon\Carbon::parse($log->clock_out)->format('H:i:s') : '-' }}</td>
                                    <td>
                                        @if($log->clock_in && \Carbon\Carbon::parse($log->clock_in)->format('H:i:s') <= '08:00:00')
                                            <span class="badge bg-success badge-custom">tepat waktu</span>
                                        @else
                                            <span class="badge bg-warning text-dark badge-custom">terlambat</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted">Belum ada aktivitas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Mengambil data dari Laravel ke Javascript
        const dataTepat = {!! json_encode($chartBulanTepatWaktu) !!};
        const dataTelat = {!! json_encode($chartBulanTerlambat) !!};
        const dataPulangAwal = {!! json_encode($chartBulanPulangAwal) !!};
        
        // --- KONFIGURASI BAR CHART ---
        var ctxBar = document.getElementById('barChart').getContext('2d');
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [
                    { label: 'Tepat Waktu', data: dataTepat, backgroundColor: '#86e4b3' },
                    { label: 'Terlambat Masuk', data: dataTelat, backgroundColor: '#fcd385' },
                    { label: 'Pulang Awal', data: dataPulangAwal, backgroundColor: '#ffae73' }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true } },
                plugins: { legend: { position: 'top', labels: { boxWidth: 12, font: { size: 10 } } } }
            }
        });

        // --- KONFIGURASI PIE CHART ---
        const pieData = {!! json_encode($pieData) !!};
        var ctxPie = document.getElementById('pieChart').getContext('2d');
        new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: ['Tepat Waktu', 'Terlambat', 'Pulang Awal', 'Cuti/Izin', 'Tidak Absen'],
                datasets: [{
                    data: pieData,
                    backgroundColor: ['#c380f6', '#fcd385', '#ffae73', '#a78bfa', '#fc3a57'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } } }
            }
        });
    });
</script>
@endpush