<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'nip'      => 'required|string',
            'password' => 'required|string',
        ]);

        $employee = Employee::where('nip', $request->nip)->first();

        if (!$employee || !Hash::check($request->password, $employee->password)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'NIP atau password salah',
            ], 401);
        }

        if ($employee->status !== 'aktif') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Akun Anda tidak aktif. Hubungi HRD.',
            ], 403);
        }

        $token = $employee->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Login berhasil',
            'data'    => [
                'token'    => $token,
                'employee' => [
                    'id'         => $employee->id,
                    'nip'        => $employee->nip,
                    'name'       => $employee->name,
                    'email'      => $employee->email,
                    'position'   => $employee->position,
                    'department' => $employee->department,
                ],
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['status' => 'success', 'message' => 'Logout berhasil']);
    }

    public function me(Request $request)
    {
        return response()->json(['status' => 'success', 'data' => $request->user()]);
    }
}