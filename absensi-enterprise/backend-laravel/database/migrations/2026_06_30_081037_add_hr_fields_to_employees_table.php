<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Kolom sudah dimasukkan langsung ke create_employees_table
        // Migration ini dikosongkan untuk menghindari duplikasi di test environment
    }

    public function down()
    {
        // Tidak ada yang perlu di-rollback
    }
};