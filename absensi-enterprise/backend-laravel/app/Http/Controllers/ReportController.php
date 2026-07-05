<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;

class ReportController extends Controller
{
    public function index()
    {
        // Mengambil semua riwayat absensi beserta data karyawan yang berelasi
        // Diurutkan dari tanggal dan waktu terbaru
        $attendances = Attendance::with('employee')->orderBy('created_at', 'desc')->get();

        return view('admin.laporan', compact('attendances'));
    }
}