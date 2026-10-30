<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', fn (Blueprint $table) => $table->softDeletes());

        foreach (['surat_masuk', 'surat_keluar'] as $name) {
            Schema::table($name, function (Blueprint $table) {
                $table->foreignId('created_by')->nullable()->constrained('users')->restrictOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->restrictOnDelete();
                $table->index('nomor_surat');
            });
        }

        Schema::table('surat_masuk', fn (Blueprint $table) => $table->index('tanggal_masuk_surat'));
        Schema::table('surat_keluar', function (Blueprint $table) {
            $table->foreignId('surat_masuk_id')->nullable()->constrained('surat_masuk')->restrictOnDelete();
            $table->index('tanggal_keluar_surat');
        });

        // Guest sessions remain valid. Clean legacy orphan references before enforcing the FK.
        DB::table('sessions')->whereNotNull('user_id')
            ->whereNotIn('user_id', DB::table('users')->select('id'))->update(['user_id' => null]);
        Schema::table('sessions', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sessions', fn (Blueprint $table) => $table->dropForeign(['user_id']));
        Schema::table('surat_keluar', function (Blueprint $table) {
            $table->dropConstrainedForeignId('surat_masuk_id');
            $table->dropIndex(['tanggal_keluar_surat']);
        });
        Schema::table('surat_masuk', fn (Blueprint $table) => $table->dropIndex(['tanggal_masuk_surat']));
        foreach (['surat_masuk', 'surat_keluar'] as $name) {
            Schema::table($name, function (Blueprint $table) {
                $table->dropConstrainedForeignId('created_by');
                $table->dropConstrainedForeignId('updated_by');
                $table->dropIndex(['nomor_surat']);
            });
        }
        Schema::table('users', fn (Blueprint $table) => $table->dropSoftDeletes());
    }
};
