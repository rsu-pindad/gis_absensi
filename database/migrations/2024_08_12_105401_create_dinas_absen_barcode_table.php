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
        Schema::create('dinas_absen_barcode', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('petugas_id');
            $table->unsignedBigInteger('absensi_id');
            $table->string('user_barcode_url');
            $table->string('user_barcode_img');
            $table->string('otp_qr')->nullable();
            $table->string('otp_input')->nullable();
            $table->text('fingerprint')->nullable();
            $table->ipAddress('devices_ip')->nullable();
            $table->json('informasi_device')->nullable();
            $table->json('informasi_os')->nullable();
            $table->double('lotd_user_barcode_masuk')->nullable();
            $table->double('latd_user_barcode_masuk')->nullable();
            $table->double('lotd_user_barcode_keluar')->nullable();
            $table->double('latd_user_barcode_keluar')->nullable();
            $table->time('user_masuk', $precision = 0)->nullable();
            $table->time('user_keluar', $precision = 0)->nullable();
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
        Schema::dropIfExists('dinas_absen_barcode');
    }
};
