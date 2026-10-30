<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_keluar', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_keluar_surat');
            $table->string('nomor_urut')->nullable();
            $table->text('alamat_penerima')->nullable();
            $table->date('tanggal_surat');
            $table->string('nomor_surat')->nullable();
            $table->text('perihal');
            $table->string('asal')->nullable();
            $table->string('lampiran_path')->nullable();
            $table->string('lampiran_nama_asli')->nullable();
            $table->string('lampiran_mime')->nullable();
            $table->unsignedBigInteger('lampiran_ukuran')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_keluar');
    }
};
