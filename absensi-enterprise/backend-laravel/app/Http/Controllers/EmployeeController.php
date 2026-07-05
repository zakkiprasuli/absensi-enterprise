<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\Http;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::orderBy('created_at', 'desc')->get();
        return view('admin.karyawan', compact('employees'));
    }

    public function create()
    {
        return view('admin.karyawan-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required|unique:employees,nip',
            'nama' => 'required',
            'email' => 'required|email|unique:employees,email',
            'password' => 'required|min:6',
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'join_date' => 'nullable|date',
            'status' => 'required|in:aktif,nonaktif,resign',
            'foto_wajah' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            $photo = $request->file('foto_wajah');

            $response = Http::attach(
                'file',
                file_get_contents($photo->getPathname()),
                $photo->getClientOriginalName()
            )->post('http://127.0.0.1:8001/api/v1/faces/extract');

            if ($response->failed() || $response->json('status') !== 'success') {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Gagal memproses wajah. Pastikan foto jelas dan coba lagi.');
            }

            $embedding = $response->json('data.embedding');
            $vectorString = '[' . implode(',', $embedding) . ']';

            Employee::create([
                'nip' => $request->nip,
                'name' => $request->nama,
                'email' => $request->email,
                'password' => $request->password,
                'phone' => $request->phone,
                'position' => $request->position,
                'department' => $request->department,
                'join_date' => $request->join_date,
                'status' => $request->status,
                'face_embedding' => $vectorString,
            ]);

            return redirect()->route('karyawan.index')
                ->with('success', 'Karyawan berhasil didaftarkan dan wajah berhasil diekstrak!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }
    public function edit(Employee $employee)
    {
        return view('admin.karyawan-edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'nip' => 'required|unique:employees,nip,' . $employee->id,
            'nama' => 'required',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'password' => 'nullable|min:6',
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'join_date' => 'nullable|date',
            'status' => 'required|in:aktif,nonaktif,resign',
        ]);

        $data = [
            'nip' => $request->nip,
            'name' => $request->nama,
            'email' => $request->email,
            'phone' => $request->phone,
            'position' => $request->position,
            'department' => $request->department,
            'join_date' => $request->join_date,
            'status' => $request->status,
        ];

        // Password cuma diupdate kalau diisi (tidak wajib reset tiap edit)
        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $employee->update($data);

        return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function toggleStatus(Employee $employee)
    {
        $employee->status = $employee->status === 'aktif' ? 'nonaktif' : 'aktif';
        $employee->save();

        $pesan = $employee->status === 'aktif'
            ? 'Karyawan diaktifkan kembali.'
            : 'Karyawan dinonaktifkan.';

        return redirect()->back()->with('success', $pesan);
    }
}