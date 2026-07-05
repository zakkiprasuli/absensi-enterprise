<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class Employee extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = [
        'nip',
        'name',
        'email',
        'password',
        'phone',
        'position',
        'department',
        'join_date',
        'status',
        'face_embedding',
    ];

    protected $hidden = ['password', 'face_embedding'];

    protected $casts = [
        'join_date' => 'date',
    ];

    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = Hash::make($value);
        }
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}