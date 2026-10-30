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
        Schema::create('surat_masuk', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_masuk_surat');
            $table->string('nomor_urut')->nullable();
            $table->text('alamat_pengirim')->nullable();
            $table->date('tanggal_surat');
            $table->string('nomor_surat')->nullable();
            $table->text('perihal');
            $table->string('tujuan_disposisi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_masuk');
    }
};
