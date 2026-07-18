<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        // Hanya jalankan di PostgreSQL, skip di SQLite (test environment)
        if ($driver === 'pgsql') {
            DB::statement('CREATE EXTENSION IF NOT EXISTS vector;');
        }

        Schema::create('employees', function (Blueprint $table) use ($driver) {
            $table->id();
            $table->string('nip')->unique();
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('password')->nullable();
            $table->string('phone')->nullable();
            $table->string('position')->nullable();
            $table->string('department')->nullable();
            $table->date('join_date')->nullable();
            $table->enum('status', ['aktif', 'nonaktif', 'resign'])->default('aktif');

            // face_embedding: pakai vector di PostgreSQL, text di SQLite
            if ($driver === 'pgsql') {
                $table->vector('face_embedding', 512)->nullable();
            } else {
                $table->text('face_embedding')->nullable();
            }

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};