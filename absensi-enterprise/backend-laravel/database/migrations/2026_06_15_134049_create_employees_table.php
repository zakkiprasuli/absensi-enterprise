<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Wajib: Mengaktifkan mesin pencari AI (pgvector) di PostgreSQL
        DB::statement('CREATE EXTENSION IF NOT EXISTS vector;');

        // 2. Membuat tabel employees
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('nip')->unique(); // Nomor Induk Pegawai
            $table->string('name');
            
            // Menyimpan "sidik jari wajah" 512 dimensi dari InsightFace
            $table->vector('face_embedding', 512)->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
