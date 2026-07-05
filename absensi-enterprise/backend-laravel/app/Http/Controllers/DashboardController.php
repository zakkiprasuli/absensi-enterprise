<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $currentYear = date('Y');

        // Ambil pengaturan jam kerja
        $setting = Setting::current();
        $jamMasukToleransi = Carbon::parse($setting->jam_masuk)
            ->addMinutes($setting->toleransi_terlambat)
            ->format('H:i:s');
        $jamPulang = $setting->jam_pulang;

        $totalEmployees = Employee::count();
        $sedangCuti = Leave::activeOn($today)->count();

        $tepatWaktu = Attendance::whereDate('clock_in', $today)
                        ->whereTime('clock_in', '<=', $jamMasukToleransi)->count();

        $terlambat = Attendance::whereDate('clock_in', $today)
                        ->whereTime('clock_in', '>', $jamMasukToleransi)->count();

        $pulangAwal = Attendance::whereDate('clock_out', $today)
                        ->whereNotNull('clock_out')
                        ->whereTime('clock_out', '<', $jamPulang)->count();

        $hadirHariIni = Attendance::whereDate('created_at', $today)->count();
        $tidakAbsen = $totalEmployees - $hadirHariIni - $sedangCuti;
        if ($tidakAbsen < 0) $tidakAbsen = 0;

        $totalCutiTahunan = Leave::where('status', 'approved')
            ->whereYear('start_date', $currentYear)
            ->count();

        // Top 5 karyawan tepat waktu bulan ini
        $topTepatWaktu = Attendance::select('employee_id', DB::raw('count(*) as total'))
            ->whereMonth('created_at', date('m'))
            ->whereYear('created_at', $currentYear)
            ->whereTime('clock_in', '<=', $jamMasukToleransi)
            ->groupBy('employee_id')
            ->orderBy('total', 'desc')
            ->take(5)
            ->with('employee')
            ->get();

        $logTerbaru = Attendance::with('employee')
            ->orderBy('updated_at', 'desc')
            ->take(6)
            ->get();

        // Data grafik perbulan
        $chartBulanTepatWaktu = [];
        $chartBulanTerlambat = [];
        $chartBulanPulangAwal = [];

        for ($i = 1; $i <= 12; $i++) {
            $chartBulanTepatWaktu[] = Attendance::whereMonth('created_at', $i)
                ->whereYear('created_at', $currentYear)
                ->whereTime('clock_in', '<=', $jamMasukToleransi)->count();

            $chartBulanTerlambat[] = Attendance::whereMonth('created_at', $i)
                ->whereYear('created_at', $currentYear)
                ->whereTime('clock_in', '>', $jamMasukToleransi)->count();

            $chartBulanPulangAwal[] = Attendance::whereMonth('created_at', $i)
                ->whereYear('created_at', $currentYear)
                ->whereNotNull('clock_out')
                ->whereTime('clock_out', '<', $jamPulang)->count();
        }

        // Pie chart pertahun
        $pieData = [
            array_sum($chartBulanTepatWaktu),
            array_sum($chartBulanTerlambat),
            array_sum($chartBulanPulangAwal),
            $totalCutiTahunan,
            ($totalEmployees * 260) - array_sum($chartBulanTepatWaktu) - array_sum($chartBulanTerlambat) - $totalCutiTahunan,
        ];
        if (end($pieData) < 0) {
            $pieData[count($pieData) - 1] = 0;
        }

        return view('admin.dashboard', compact(
            'totalEmployees', 'tepatWaktu', 'terlambat', 'pulangAwal', 'tidakAbsen', 'sedangCuti',
            'topTepatWaktu', 'logTerbaru', 'totalCutiTahunan',
            'chartBulanTepatWaktu', 'chartBulanTerlambat', 'chartBulanPulangAwal', 'pieData'
        ));
    }
}