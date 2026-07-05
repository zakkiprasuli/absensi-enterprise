<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'employee_id',
        'clock_in',
        'clock_out',
        'status'
    ];

    // Menyambungkan data absensi ke data pegawai
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}