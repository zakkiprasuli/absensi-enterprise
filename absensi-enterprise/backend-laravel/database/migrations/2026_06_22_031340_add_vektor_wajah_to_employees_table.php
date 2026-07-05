<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Menambahkan kolom teks panjang untuk menyimpan vektor AI
            $table->text('vektor_wajah')->nullable()->after('nama');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('vektor_wajah');
        });
    }
};
