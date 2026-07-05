<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('email')->unique()->nullable()->after('name');
            $table->string('password')->nullable()->after('email');
            $table->string('phone')->nullable()->after('password');
            $table->string('position')->nullable()->after('phone');
            $table->string('department')->nullable()->after('position');
            $table->date('join_date')->nullable()->after('department');
            $table->enum('status', ['aktif', 'nonaktif', 'resign'])->default('aktif')->after('join_date');
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['email', 'password', 'phone', 'position', 'department', 'join_date', 'status']);
        });
    }
};  