<?php

namespace Tests\Feature;

use App\Exports\SuratKeluarExport;
use App\Exports\SuratMasukExport;
use App\Imports\SuratKeluarImport;
use App\Imports\SuratMasukImport;
use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use App\Models\User;
use App\Services\SuratService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class RelationalWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function incoming(array $extra = []): array
    {
        return array_merge(['tanggal_masuk_surat' => date('Y-m-d'), 'tanggal_surat' => date('Y-m-d'),
            'nomor_urut' => '001/IX/2026', 'alamat_pengirim' => 'Instansi A', 'nomor_surat' => 'A/001',
            'perihal' => 'Permohonan data', 'tujuan_disposisi' => 'Bagian Umum'], $extra);
    }

    private function outgoing(array $extra = []): array
    {
        return array_merge(['tanggal_keluar_surat' => date('Y-m-d'), 'tanggal_surat' => date('Y-m-d'),
            'nomor_urut' => '001/IX/2026', 'alamat_penerima' => 'Instansi A', 'nomor_surat' => 'B/001',
            'perihal' => 'Balasan permohonan data', 'asal' => 'Bagian Umum'], $extra);
    }

    private function loginUser(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        return $user;
    }

    public function test_guest_cannot_access_administration(): void
    {
        foreach (['dashboard', 'surat-masuk.index', 'surat-keluar.index', 'profile', 'surat-masuk.lookup'] as $route) {
            $this->get(route($route))->assertRedirect(route('login'));
        }
    }

    public function test_create_and_update_record_authorship_without_changing_creator(): void
    {
        $creator = $this->loginUser();
        $this->post(route('surat-masuk.store'), $this->incoming())->assertSessionHasNoErrors()->assertRedirect(route('surat-masuk.index'));
        $surat = SuratMasuk::firstOrFail();
        $this->assertSame($creator->id, $surat->created_by);
        $this->assertNull($surat->updated_by);
        $editor = User::factory()->create();
        $this->actingAs($editor)->put(route('surat-masuk.update', $surat), $this->incoming(['perihal' => 'Permohonan diperbarui']))->assertSessionHasNoErrors();
        $surat->refresh();
        $this->assertSame($creator->id, $surat->created_by);
        $this->assertSame($editor->id, $surat->updated_by);
        $this->get(route('surat-masuk.show', $surat))->assertOk()->assertSee($creator->name)->assertSee($editor->name);
    }

    public function test_client_cannot_forge_creator_or_editor(): void
    {
        $this->loginUser();
        $this->post(route('surat-masuk.store'), $this->incoming(['created_by' => 999]))->assertSessionHasErrors('created_by');
        $this->post(route('surat-keluar.store'), $this->outgoing(['updated_by' => 999]))->assertSessionHasErrors('updated_by');
        $this->assertSame(0, SuratMasuk::count());
        $this->assertSame(0, SuratKeluar::count());
    }

    public function test_reply_links_are_optional_validated_and_visible_both_ways(): void
    {
        $creator = $this->loginUser();
        $incoming = app(SuratService::class)->save(SuratMasuk::class, $this->incoming(), $creator->id);
        $this->post(route('surat-keluar.store'), $this->outgoing(['surat_masuk_id' => $incoming->id]))->assertSessionHasNoErrors();
        $outgoing = SuratKeluar::firstOrFail();
        $this->assertSame($creator->id, $outgoing->created_by);
        $this->get(route('surat-keluar.show', $outgoing))->assertOk()->assertSee(route('surat-masuk.show', $incoming), false);
        $this->get(route('surat-masuk.show', $incoming))->assertOk()->assertSee(route('surat-keluar.show', $outgoing), false);
        $this->put(route('surat-keluar.update', $outgoing), $this->outgoing(['surat_masuk_id' => null]))->assertSessionHasNoErrors();
        $this->assertNull($outgoing->fresh()->surat_masuk_id);
        $this->post(route('surat-keluar.store'), $this->outgoing(['surat_masuk_id' => 999999]))->assertSessionHasErrors('surat_masuk_id');
    }

    public function test_referenced_incoming_letter_and_reset_are_protected(): void
    {
        $creator = $this->loginUser();
        $incoming = app(SuratService::class)->save(SuratMasuk::class, $this->incoming(), $creator->id);
        app(SuratService::class)->save(SuratKeluar::class, $this->outgoing(['surat_masuk_id' => $incoming->id]), $creator->id);
        $this->delete(route('surat-masuk.destroy', $incoming))->assertSessionHasErrors('surat');
        $this->delete(route('surat-masuk.reset'), ['confirmation' => 'HAPUS'])->assertSessionHasErrors('surat');
        $this->assertSame(1, SuratMasuk::count());
        $this->assertSame(1, SuratKeluar::count());
    }

    public function test_reset_requires_confirmation_and_keeps_other_letter_type(): void
    {
        $user = $this->loginUser();
        app(SuratService::class)->save(SuratMasuk::class, $this->incoming(), $user->id);
        app(SuratService::class)->save(SuratKeluar::class, $this->outgoing(), $user->id);
        $this->delete(route('surat-keluar.reset'))->assertSessionHasErrors('confirmation');
        $this->delete(route('surat-keluar.reset'), ['confirmation' => 'HAPUS'])->assertSessionHasNoErrors();
        $this->assertSame(0, SuratKeluar::count());
        $this->assertSame(1, SuratMasuk::count());
    }

    public function test_account_removal_preserves_authorship_and_revokes_sessions(): void
    {
        $actor = $this->loginUser();
        $target = User::factory()->create(['remember_token' => 'old-remember-token']);
        $surat = app(SuratService::class)->save(SuratMasuk::class, $this->incoming(), $target->id);
        DB::table('sessions')->insert(['id' => 'test-account-session', 'user_id' => $target->id, 'payload' => '', 'last_activity' => time()]);
        DB::table('password_reset_tokens')->insert(['email' => $target->email, 'token' => 'reset-token']);
        $this->delete(route('profile.admins.destroy', $target))->assertSessionHasNoErrors();
        $this->assertSoftDeleted('users', ['id' => $target->id]);
        $this->assertNull(User::withTrashed()->find($target->id)->remember_token);
        $this->assertDatabaseMissing('sessions', ['user_id' => $target->id]);
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $target->email]);
        $this->get(route('surat-masuk.show', $surat))->assertOk()->assertSee($target->name)->assertSee('akun dihapus');
        $this->delete(route('profile.admins.destroy', $actor))->assertForbidden();
        $this->assertSame(1, User::count());
    }

    public function test_deleted_account_cannot_login(): void
    {
        $target = User::factory()->create(['password' => Hash::make('password123')]);
        $target->delete();
        $this->post(route('login.store'), ['email' => $target->email, 'password' => 'password123'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_valid_login_and_logout(): void
    {
        $user = User::factory()->create(['password' => Hash::make('password123')]);
        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password123', 'remember' => true])->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_mysql_rejects_invalid_letter_author(): void
    {
        $this->expectException(QueryException::class);
        DB::table('surat_masuk')->insert($this->incoming(['created_by' => 999999]));
    }

    public function test_mysql_rejects_invalid_session_user(): void
    {
        $this->expectException(QueryException::class);
        DB::table('sessions')->insert(['id' => 'orphan-session', 'user_id' => 999999, 'payload' => '', 'last_activity' => time()]);
    }

    public function test_mysql_rejects_invalid_reply_reference(): void
    {
        $this->expectException(QueryException::class);
        DB::table('surat_keluar')->insert($this->outgoing(['surat_masuk_id' => 999999]));
    }

    public function test_filters_lookup_and_all_pages_render(): void
    {
        $creator = $this->loginUser();
        $surat = app(SuratService::class)->save(SuratMasuk::class, $this->incoming(), $creator->id);
        foreach (['dashboard', 'profile', 'surat-masuk.index', 'surat-keluar.index', 'surat-masuk.create', 'surat-keluar.create', 'surat-masuk.import', 'surat-keluar.import'] as $route) {
            $this->get(route($route))->assertOk();
        }
        $this->get(route('surat-masuk.edit', $surat))->assertOk();
        $this->get(route('surat-masuk.index', ['search' => 'Permohonan', 'bulan' => 'all', 'tahun' => 'all']))->assertOk()->assertSee('Permohonan data');
        $this->getJson(route('surat-masuk.lookup', ['q' => 'A/001']))->assertOk()->assertJsonPath('0.id', $surat->id);
        $this->get(route('surat-masuk.index', ['bulan' => '99']))->assertSessionHasErrors('bulan');
        $this->get(route('surat-keluar.index', ['search' => ['bad']]))->assertSessionHasErrors('search');
    }

    public function test_profile_updates_and_admin_creation(): void
    {
        $user = $this->loginUser();
        $this->patch(route('profile.update'), ['name' => 'Nama baru', 'email' => 'updated@example.test'])->assertSessionHasNoErrors();
        $this->assertSame('Nama baru', $user->fresh()->name);
        $this->post(route('profile.admins.store'), ['name' => 'Admin baru', 'email' => 'new@example.test', 'password' => 'password123', 'password_confirmation' => 'password123'])->assertSessionHasNoErrors();
        $this->assertSame(2, User::count());
        $this->patch(route('profile.password'), ['current_password' => 'password', 'password' => 'updated123', 'password_confirmation' => 'updated123'])->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('updated123', $user->fresh()->password));
    }

    public function test_both_export_formats_can_be_imported_and_duplicates_skipped(): void
    {
        Storage::fake('local');
        $user = $this->loginUser();
        foreach ([[SuratMasuk::class, SuratMasukExport::class, SuratMasukImport::class, $this->incoming()], [SuratKeluar::class, SuratKeluarExport::class, SuratKeluarImport::class, $this->outgoing()]] as [$model, $exportClass, $importClass, $payload]) {
            app(SuratService::class)->save($model, $payload, $user->id);
            $bytes = Excel::raw(new $exportClass('all', 'all'), ExcelFormat::XLSX);
            $path = Storage::disk('local')->path(class_basename($model).'.xlsx');
            file_put_contents($path, $bytes);
            $model::query()->delete();
            $import = new $importClass(actorId: $user->id);
            Excel::import($import, $path);
            $this->assertSame(1, $import->created);
            $this->assertSame($payload['perihal'], $model::first()->perihal);
            $this->assertSame($user->id, $model::first()->created_by);
            $duplicate = new $importClass(previewOnly: true);
            Excel::import($duplicate, $path);
            $this->assertSame(1, $duplicate->duplicateRows);
        }
    }

    public function test_csv_preview_confirmation_and_replay_protection(): void
    {
        Storage::fake('local');
        $user = $this->loginUser();
        $csv = "tanggal_masuk_surat,nomor_urut,alamat_pengirim,tanggal_surat,nomor_surat,perihal,tujuan_disposisi,created_by\n2026-09-16,002,Instansi,2026-09-16,N/002,Data import,Umum,999999\n2026-09-16,002,Instansi,2026-09-16,N/002,Data import,Umum,999999\n2026-02-30,003,Instansi,2026-09-16,N/003,Invalid,Umum,999999\n";
        $file = UploadedFile::fake()->createWithContent('agenda.csv', $csv);
        $response = $this->post(route('surat-masuk.import.store'), ['file' => $file]);
        $response->assertOk()->assertViewHas('validRows', 1)->assertViewHas('duplicateRows', 1)->assertViewHas('invalidRows', 1);
        $this->assertSame(0, SuratMasuk::count());
        $token = session('surat_masuk_import.token');
        $this->post(route('surat-masuk.import.store'), ['import_token' => $token])->assertRedirect(route('surat-masuk.index'));
        $this->assertSame(1, SuratMasuk::count());
        $this->assertSame($user->id, SuratMasuk::first()->created_by);
        $this->post(route('surat-masuk.import.store'), ['import_token' => $token])->assertSessionHasErrors('file');
        $this->assertSame(1, SuratMasuk::count());
    }

    public function test_import_failure_rolls_back_all_rows_and_keeps_preview_retryable(): void
    {
        Storage::fake('local');
        $this->loginUser();
        $csv = "tanggal_masuk_surat,nomor_urut,alamat_pengirim,tanggal_surat,nomor_surat,perihal,tujuan_disposisi\n2026-09-16,001,Instansi,2026-09-16,N/001,Pertama,Umum\n2026-09-16,002,Instansi,2026-09-16,N/002,Kedua,Umum\n";
        $this->post(route('surat-masuk.import.store'), ['file' => UploadedFile::fake()->createWithContent('agenda.csv', $csv)])->assertOk();
        $token = session('surat_masuk_import.token');
        $rows = 0;
        Event::listen('eloquent.saving: '.SuratMasuk::class, function () use (&$rows) {
            if (++$rows === 2) {
                throw new \RuntimeException('Simulated second-row import failure');
            }
        });
        try {
            $this->post(route('surat-masuk.import.store'), ['import_token' => $token])->assertSessionHasErrors('file');
        } finally {
            Event::forget('eloquent.saving: '.SuratMasuk::class);
        }
        $this->assertDatabaseCount('surat_masuk', 0);
        $this->assertSame($token, session('surat_masuk_import.token'));
        $this->post(route('surat-masuk.import.store'), ['import_token' => $token])->assertRedirect(route('surat-masuk.index'));
        $this->assertDatabaseCount('surat_masuk', 2);
        $this->assertNull(session('surat_masuk_import'));
    }

    public function test_outgoing_flat_csv_supports_explicit_reply_id(): void
    {
        Storage::fake('local');
        $user = $this->loginUser();
        $incoming = app(SuratService::class)->save(SuratMasuk::class, $this->incoming(), $user->id);
        $csv = "tanggal_keluar_surat,nomor_urut,alamat_penerima,tanggal_surat,nomor_surat,perihal,asal,surat_masuk_id\n2026-09-16,004,Instansi,2026-09-16,N/004,Balasan CSV,Umum,".$incoming->id."\n";
        $file = UploadedFile::fake()->createWithContent('keluar.csv', $csv);
        $this->post(route('surat-keluar.import.store'), ['file' => $file])->assertOk()->assertViewHas('validRows', 1);
        $this->post(route('surat-keluar.import.store'), ['import_token' => session('surat_keluar_import.token')])->assertSessionHasNoErrors();
        $this->assertSame($incoming->id, SuratKeluar::first()->surat_masuk_id);
    }

    public function test_failed_attachment_write_does_not_create_a_letter(): void
    {
        $user = $this->loginUser();
        $file = \Mockery::mock(UploadedFile::class);
        $file->shouldReceive('store')->once()->with('surat-masuk', 'public')->andReturn(false);
        try {
            app(SuratService::class)->save(SuratMasuk::class, $this->incoming(), $user->id, file: $file);
            $this->fail('A failed upload must not create a broken attachment reference');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('lampiran', $exception->errors());
        }
        $this->assertDatabaseCount('surat_masuk', 0);
    }

    public function test_failed_update_keeps_old_attachment_and_removes_new_upload(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        $user = $this->loginUser();
        $service = app(SuratService::class);
        $surat = $service->save(SuratMasuk::class, $this->incoming(), $user->id, file: UploadedFile::fake()->image('old.png'));
        $oldPath = $surat->lampiran_path;
        Event::listen('eloquent.saving: '.SuratMasuk::class, function () {
            throw new \RuntimeException('Simulated save failure');
        });
        try {
            $service->save(SuratMasuk::class, $this->incoming(), $user->id, $surat, UploadedFile::fake()->image('new.png'));
            $this->fail('Save should fail');
        } catch (\RuntimeException $exception) {
            $this->assertSame('Simulated save failure', $exception->getMessage());
        } finally {
            Event::forget('eloquent.saving: '.SuratMasuk::class);
        }
        Storage::disk('public')->assertExists($oldPath);
        $this->assertCount(1, Storage::disk('public')->allFiles());
        $this->assertSame($oldPath, $surat->fresh()->lampiran_path);
    }

    public function test_transfer_dry_run_commit_and_nonempty_target_protection(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'sipas-transfer-');
        try {
            $source = new \PDO('sqlite:'.$path);
            foreach (['jobs', 'job_batches', 'failed_jobs'] as $table) {
                $source->exec('CREATE TABLE '.$table.' (id INTEGER PRIMARY KEY)');
            }
            $source->exec('CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT, email TEXT, password TEXT, created_at TEXT, updated_at TEXT)');
            $password = Hash::make('transfer123');
            $source->prepare('INSERT INTO users VALUES (?,?,?,?,?,?)')->execute([41, 'Legacy user', 'legacy@example.test', $password, '2026-01-01 10:00:00', '2026-01-01 10:00:00']);
            $source->exec('CREATE TABLE surat_masuk (id INTEGER PRIMARY KEY, tanggal_masuk_surat TEXT, tanggal_surat TEXT, perihal TEXT)');
            $source->exec("INSERT INTO surat_masuk VALUES (51,'2026-09-16','2026-09-16','Surat lama')");
            $source->exec('CREATE TABLE surat_keluar (id INTEGER PRIMARY KEY, tanggal_keluar_surat TEXT, tanggal_surat TEXT, perihal TEXT)');
            $source->exec("INSERT INTO surat_keluar VALUES (61,'2026-09-16','2026-09-16','Surat keluar lama')");
            $this->artisan('sipas:transfer-sqlite', ['--source' => $path, '--dry-run' => true])->assertSuccessful();
            $this->assertSame(0, User::count());
            $this->artisan('sipas:transfer-sqlite', ['--source' => $path])->assertSuccessful();
            $this->assertSame($password, User::find(41)->password);
            $this->assertNull(SuratMasuk::find(51)->created_by);
            $this->assertSame('Surat keluar lama', SuratKeluar::find(61)->perihal);
            $this->artisan('sipas:transfer-sqlite', ['--source' => $path])->assertFailed();
            $this->assertSame(1, User::count());
        } finally {
            $source = null;
            unlink($path);
        }
    }

    public function test_successful_attachment_replacement_and_removal_cleanup_files(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        $user = $this->loginUser();
        $service = app(SuratService::class);
        $surat = $service->save(SuratMasuk::class, $this->incoming(), $user->id, file: UploadedFile::fake()->image('first.png'));
        $old = $surat->lampiran_path;
        $surat = $service->save(SuratMasuk::class, $this->incoming(), $user->id, $surat, UploadedFile::fake()->image('second.png'));
        Storage::disk('public')->assertMissing($old);
        Storage::disk('public')->assertExists($surat->lampiran_path);
        $current = $surat->lampiran_path;
        $surat = $service->save(SuratMasuk::class, $this->incoming(), $user->id, $surat, removeAttachment: true);
        $this->assertNull($surat->lampiran_path);
        Storage::disk('public')->assertMissing($current);
    }

    public function test_invalid_import_file_and_other_user_confirmation_are_rejected(): void
    {
        Storage::fake('local');
        $this->loginUser();
        $this->post(route('surat-masuk.import.store'), ['file' => UploadedFile::fake()->createWithContent('broken.csv', 'not,a,valid,template')])->assertSessionHasErrors('file');
        $csv = "tanggal_masuk_surat,tanggal_surat,perihal\n2026-09-16,2026-09-16,Preview pengguna lain\n";
        $this->post(route('surat-masuk.import.store'), ['file' => UploadedFile::fake()->createWithContent('valid.csv', $csv)])->assertOk();
        $token = session('surat_masuk_import.token');
        $this->actingAs(User::factory()->create());
        $this->post(route('surat-masuk.import.store'), ['import_token' => $token])->assertSessionHasErrors('file');
        $this->assertSame(0, SuratMasuk::count());
    }

    public function test_physical_deletion_of_referenced_user_is_rejected_by_mysql(): void
    {
        $user = $this->loginUser();
        app(SuratService::class)->save(SuratMasuk::class, $this->incoming(), $user->id);
        $this->expectException(QueryException::class);
        $user->forceDelete();
    }

    public function test_dates_and_text_outside_mysql_limits_return_validation_errors(): void
    {
        $this->loginUser();
        $this->post(route('surat-masuk.store'), $this->incoming(['tanggal_surat' => '0999-01-01']))->assertSessionHasErrors('tanggal_surat');
        $this->post(route('surat-keluar.store'), $this->outgoing(['tanggal_keluar_surat' => '2026-09-16 12:30:00']))->assertSessionHasErrors('tanggal_keluar_surat');
        $this->post(route('surat-masuk.store'), $this->incoming(['perihal' => str_repeat('a', 16001)]))->assertSessionHasErrors('perihal');
        $this->assertSame(0, SuratMasuk::count());
    }
}
