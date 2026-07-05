<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Geofence;

class GeofenceController extends Controller
{
    // Fungsi untuk menampilkan halaman
    public function index()
    {
        // Ambil data geofence pertama dari database
        $kantor = Geofence::first();

        // Jika database masih kosong, berikan nilai default sementara
        if (!$kantor) {
            $kantor = (object) [
                'latitude' => '-6.200000',
                'longitude' => '106.816666',
                'radius' => 50
            ];
        }

        return view('admin.geofencing', compact('kantor'));
    }

    // Fungsi untuk menyimpan data yang diketik HRD
    public function update(Request $request)
    {
        $request->validate([
            'latitude' => 'required',
            'longitude' => 'required',
            'radius' => 'required|numeric'
        ]);

        // Cek apakah data sudah ada. Jika ada, update. Jika belum, buat baru.
        $geofence = Geofence::first();
        if ($geofence) {
            $geofence->update($request->all());
        } else {
            Geofence::create($request->all());
        }

        return redirect()->back()->with('success', 'Titik radar Geofencing berhasil diperbarui!');
    }
}