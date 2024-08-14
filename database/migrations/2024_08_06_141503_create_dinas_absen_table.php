<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dinas_absen', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('petugas_id');
            $table->unsignedBigInteger('absensi_id');
            $table->string('otp');
            $table->text('fingerprint');
            $table->ipAddress('devices_ip')->nullable();
            $table->json('informasi_device')->nullable();
            $table->json('informasi_os')->nullable();
            $table->double('lotd_user');
            $table->double('latd_user');
            $table->dateTimeTz('presensi_masuk', $precision = 0)->nullable();
            $table->dateTimeTz('presensi_keluar', $precision = 0)->nullable();
            $table
                ->foreign('user_id')
                ->references('id')
                ->on('users')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table
                ->foreign('petugas_id')
                ->references('id')
                ->on('users')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table
                ->foreign('absensi_id')
                ->references('id')
                ->on('absensi')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dinas_absen');
    }
};
