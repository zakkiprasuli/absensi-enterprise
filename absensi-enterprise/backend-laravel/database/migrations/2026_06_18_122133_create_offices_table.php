<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // <-- Tambahkan ini

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('latitude', 10, 8); // Presisi tinggi untuk GPS
            $table->decimal('longitude', 11, 8);
            $table->integer('radius'); // Batas toleransi jarak dalam meter
            $table->timestamps();
        });

        // Langsung suntikkan 1 data kantor pusat untuk percobaan
        DB::table('offices')->insert([
            'name' => 'Kantor Pusat Semarang',
            'latitude' => -6.990399,   // Contoh titik tengah kota (Simpang Lima)
            'longitude' => 110.422956,
            'radius' => 50,            // Maksimal 50 meter dari titik ini
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('offices');
    }
};