<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['jam_masuk', 'jam_pulang', 'toleransi_terlambat'];

    // Helper: ambil satu-satunya baris setting (selalu pakai baris pertama)
    public static function current()
    {
        return self::first() ?? self::create();
    }
}