<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Setting::current();
        return view('admin.pengaturan', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'jam_masuk' => 'required|date_format:H:i',
            'jam_pulang' => 'required|date_format:H:i',
            'toleransi_terlambat' => 'required|integer|min:0|max:120',
        ]);

        $setting = Setting::current();
        $setting->update([
            'jam_masuk' => $request->jam_masuk,
            'jam_pulang' => $request->jam_pulang,
            'toleransi_terlambat' => $request->toleransi_terlambat,
        ]);

        return redirect()->back()->with('success', 'Pengaturan jam kerja berhasil disimpan.');
    }
}