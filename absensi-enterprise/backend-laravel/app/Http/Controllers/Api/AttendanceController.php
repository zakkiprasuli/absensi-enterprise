<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Geofence;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // Hitung jarak dua koordinat (meter) — formula Haversine
    private function distanceInMeters($lat1, $lng1, $lat2, $lng2): float
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    private function checkGeofence(float $latitude, float $longitude): array
    {
        $geofence = Geofence::first();

        if (!$geofence) {
            return ['valid' => false, 'message' => 'Titik kantor belum diatur oleh HRD.'];
        }

        $distance = $this->distanceInMeters(
            $geofence->latitude, $geofence->longitude,
            $latitude, $longitude
        );

        if ($distance > $geofence->radius) {
            return [
                'valid'   => false,
                'message' => 'Anda berada di luar radius absen (' . round($distance) . 'm dari kantor).',
            ];
        }

        return ['valid' => true];
    }

    private function verifyFace($employee, $photo): array
    {
        try {
            $response = Http::attach(
                'file',
                file_get_contents($photo->getPathname()),
                $photo->getClientOriginalName()
            )->attach(
                'stored_embedding',
                $employee->face_embedding,
                'embedding.txt'
            )->post('http://127.0.0.1:8001/api/v1/faces/verify');

            if ($response->failed()) {
                return ['match' => false, 'message' => 'Layanan AI tidak merespons.'];
            }

            $data = $response->json();

            if ($data['status'] !== 'success') {
                return ['match' => false, 'message' => $data['message'] ?? 'Gagal verifikasi wajah.'];
            }

            return [
                'match'   => $data['data']['is_match'],
                'message' => $data['data']['is_match'] ? 'Wajah cocok.' : 'Wajah tidak cocok.',
            ];
        } catch (\Exception $e) {
            return ['match' => false, 'message' => 'Error koneksi ke AI: ' . $e->getMessage()];
        }
    }

    public function clockIn(Request $request)
    {
        $request->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo'     => 'required|image|max:4096',
        ]);

        $employee = $request->user();
        $today    = Carbon::today();

        // Cegah double clock-in
        $existing = Attendance::where('employee_id', $employee->id)
            ->whereDate('created_at', $today)
            ->whereNotNull('clock_in')
            ->first();

        if ($existing) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda sudah absen masuk hari ini.',
            ], 422);
        }

        // Cek geofence
        $geo = $this->checkGeofence($request->latitude, $request->longitude);
        if (!$geo['valid']) {
            return response()->json(['status' => 'error', 'message' => $geo['message']], 422);
        }

        // Verifikasi wajah
        $face = $this->verifyFace($employee, $request->file('photo'));
        if (!$face['match']) {
            return response()->json(['status' => 'error', 'message' => $face['message']], 422);
        }

        // Tentukan status: tepat waktu atau terlambat
        $setting          = Setting::current();
        $jamMasukToleransi = Carbon::parse($setting->jam_masuk)
            ->addMinutes($setting->toleransi_terlambat);
        $status = now()->lte($jamMasukToleransi) ? 'tepat_waktu' : 'terlambat';

        $attendance = Attendance::create([
            'employee_id' => $employee->id,
            'clock_in'    => now(),
            'status'      => $status,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Absen masuk berhasil',
            'data'    => [
                'clock_in' => $attendance->clock_in,
                'status'   => $attendance->status,
            ],
        ]);
    }

    public function clockOut(Request $request)
    {
        $request->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo'     => 'required|image|max:4096',
        ]);

        $employee = $request->user();
        $today    = Carbon::today();

        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('created_at', $today)
            ->whereNotNull('clock_in')
            ->whereNull('clock_out')
            ->first();

        if (!$attendance) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda belum absen masuk atau sudah absen pulang hari ini.',
            ], 422);
        }

        // Cek geofence
        $geo = $this->checkGeofence($request->latitude, $request->longitude);
        if (!$geo['valid']) {
            return response()->json(['status' => 'error', 'message' => $geo['message']], 422);
        }

        // Verifikasi wajah
        $face = $this->verifyFace($employee, $request->file('photo'));
        if (!$face['match']) {
            return response()->json(['status' => 'error', 'message' => $face['message']], 422);
        }

        // Tentukan apakah pulang awal
        $setting   = Setting::current();
        $jamPulang = Carbon::parse($setting->jam_pulang);
        if (now()->lt($jamPulang)) {
            $attendance->update(['clock_out' => now(), 'status' => 'pulang_awal']);
        } else {
            $attendance->update(['clock_out' => now()]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Absen pulang berhasil',
            'data'    => [
                'clock_in'  => $attendance->clock_in,
                'clock_out' => $attendance->clock_out,
                'status'    => $attendance->status,
            ],
        ]);
    }

    public function history(Request $request)
    {
        $attendances = Attendance::where('employee_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->take(30)
            ->get(['id', 'clock_in', 'clock_out', 'status', 'created_at']);

        return response()->json([
            'status' => 'success',
            'data'   => $attendances,
        ]);
    }
}