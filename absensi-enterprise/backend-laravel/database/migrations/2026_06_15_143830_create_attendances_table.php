<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel pegawai (karyawan siapa yang absen?)
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            
            // Waktu absen masuk dan pulang
            $table->timestamp('clock_in')->nullable();
            $table->timestamp('clock_out')->nullable();
            
            // Status kehadiran (bisa dikembangkan nanti: Tepat Waktu, Terlambat, dll)
            $table->string('status')->default('hadir');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};