@extends('layouts.app')

@section('title', 'Radius Geofencing - Enterprise Attendance')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')
    <div class="mb-4">
        <h1 class="fw-bold h3 mb-1">Pengaturan Radar Geofencing</h1>
        <p class="text-muted small mb-0">Tentukan titik pusat kantor dan batas toleransi jarak absen karyawan.</p>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card setting-card p-4 h-100">
                <h5 class="fw-bold mb-4">Titik Koordinat Pusat</h5>
                
                @if(session('success'))
                    <div class="alert alert-success small py-2">{{ session('success') }}</div>
                @endif

                <form action="{{ route('geofencing.update') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-medium text-secondary">Latitude (Garis Lintang)</label>
                        <input type="text" name="latitude" id="lat_input" class="form-control font-monospace bg-light" value="{{ $kantor->latitude ?? '-6.200000' }}" readonly required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium text-secondary">Longitude (Garis Bujur)</label>
                        <input type="text" name="longitude" id="lng_input" class="form-control font-monospace bg-light" value="{{ $kantor->longitude ?? '106.816666' }}" readonly required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-medium text-secondary">Radius Toleransi (Meter)</label>
                        <div class="input-group">
                            <input type="number" name="radius" id="radius_input" class="form-control" value="{{ $kantor->radius ?? '50' }}" required>
                            <span class="input-group-text bg-light">Meter</span>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success w-100 fw-semibold rounded-3 py-2">
                        <i class="bi bi-save me-2"></i> Simpan Pengaturan
                    </button>
                </form>
            </div>
        </div>

        <div class="col-md-8 mt-4 mt-md-0">
            <div class="card setting-card p-4 h-100">
                <div class="d-flex gap-2 mb-3">
                    <button type="button" class="btn btn-outline-success text-nowrap" onclick="getLocation()" title="Gunakan GPS Laptop Saat Ini">
                        <i class="bi bi-crosshair"></i> Deteksi Lokasi
                    </button>
                    <div class="input-group">
                        <input type="text" id="search_input" class="form-control" placeholder="Cari nama gedung / jalan kantor...">
                        <button type="button" class="btn btn-primary" onclick="searchLocation()">Cari</button>
                    </div>
                </div>

                <div id="map" class="rounded-3 border" style="width: 100%; height: 400px; cursor: crosshair; z-index: 1;"></div>
                
                <p class="text-muted small mt-3 text-center mb-0">
                    <i class="bi bi-info-circle me-1"></i> Geser pin atau klik pada peta untuk menentukan lokasi. Lingkaran merah menunjukkan area absen yang sah.
                </p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Ambil data dari database (dengan nilai default jika kosong)
            var initialLat = parseFloat("{{ $kantor->latitude ?? '-6.200000' }}");
            var initialLng = parseFloat("{{ $kantor->longitude ?? '106.816666' }}");
            var initialRadius = parseInt("{{ $kantor->radius ?? '50' }}");

            // Inisialisasi Peta
            var map = L.map('map').setView([initialLat, initialLng], 18);
            
            // Lapisan Peta Satelit (Esri World Imagery)
            L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                attribution: 'Tiles &copy; Esri'
            }).addTo(map);

            // Lapisan Nama Jalan/Tempat agar tidak bingung
            L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_only_labels/{z}/{x}/{y}.png', {
                attribution: '&copy; CartoDB'
            }).addTo(map);

            // Marker / Pin
            var marker = L.marker([initialLat, initialLng], {draggable: true}).addTo(map);

            // Lingkaran Radar (Warna Merah agar terlihat di atas satelit)
            var geofenceCircle = L.circle([initialLat, initialLng], {
                color: '#dc3545',
                fillColor: '#dc3545',
                fillOpacity: 0.3,
                radius: initialRadius
            }).addTo(map);

            // A. Fungsi Klik Peta
            map.on('click', function(e) {
                updateMapAndInputs(e.latlng.lat, e.latlng.lng);
            });

            // B. Fungsi Geser Marker
            marker.on('dragend', function (e) {
                var pos = marker.getLatLng();
                updateMapAndInputs(pos.lat, pos.lng);
            });

            // C. Fungsi Sinkronisasi Perubahan Angka Radius
            document.getElementById('radius_input').addEventListener('input', function(e) {
                var radVal = parseInt(e.target.value);
                if (!isNaN(radVal)) geofenceCircle.setRadius(radVal);
            });

            // Fungsi Utama Pembaruan Form & Titik (Dideklarasikan di sini agar terbaca)
            window.updateMapAndInputs = function(lat, lng) {
                marker.setLatLng([lat, lng]);
                geofenceCircle.setLatLng([lat, lng]);
                document.getElementById('lat_input').value = lat.toFixed(6);
                document.getElementById('lng_input').value = lng.toFixed(6);
            }

            // D. Fungsi Sharelock (GPS Laptop)
            window.getLocation = function() {
                if (navigator.geolocation) {
                    alert("Mencari titik GPS Anda...");
                    navigator.geolocation.getCurrentPosition(function(position) {
                        var lat = position.coords.latitude;
                        var lng = position.coords.longitude;
                        map.setView([lat, lng], 18);
                        updateMapAndInputs(lat, lng);
                    }, function(error) {
                        alert("Gagal: " + error.message);
                    }, { enableHighAccuracy: true });
                } else {
                    alert("Browser tidak mendukung GPS.");
                }
            }

            // E. Fungsi Cari Alamat
            window.searchLocation = function() {
                var query = document.getElementById('search_input').value;
                if (query.trim() === "") return;

                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.length > 0) {
                            var lat = parseFloat(data[0].lat);
                            var lng = parseFloat(data[0].lon);
                            map.setView([lat, lng], 18);
                            updateMapAndInputs(lat, lng);
                        } else {
                            alert("Alamat tidak ditemukan.");
                        }
                    });
            }
        });
    </script>
@endpush