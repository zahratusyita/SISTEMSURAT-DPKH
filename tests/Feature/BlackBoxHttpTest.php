<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BlackBoxHttpTest extends TestCase
{
    use RefreshDatabase;

    private function login(): User
    {
        $user = User::factory()->create(['name' => 'Admin Black Box', 'password' => Hash::make('password123')]);
        $this->actingAs($user);

        return $user;
    }

    private function payload(string $kind, string $marker = 'BB Surat', string $date = '2026-09-17'): array
    {
        return [
            'tanggal_'.$kind.'_surat' => $date,
            'tanggal_surat' => $date,
            'nomor_urut' => 'BB-001',
            'nomor_surat' => 'BB/001',
            'perihal' => $marker,
            $kind === 'masuk' ? 'alamat_pengirim' : 'alamat_penerima' => 'Instansi Pengujian',
            $kind === 'masuk' ? 'tujuan_disposisi' : 'asal' => 'Bagian Umum',
        ];
    }

    private function index(string $kind, array $filters = []): string
    {
        return route('surat-'.$kind.'.index', $filters + ['bulan' => 'all', 'tahun' => 'all']);
    }

    public function test_wrong_login_and_empty_credentials_are_rejected(): void
    {
        $user = User::factory()->create(['password' => Hash::make('password123')]);
        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'wrong-password'])->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->post(route('login.store'), [])->assertSessionHasErrors(['email', 'password']);
        $this->assertGuest();
        $this->post(route('login.store'), ['email' => 'email-invalid', 'password' => 'password123'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_both_letter_types_complete_create_read_update_delete_through_http(): void
    {
        $this->login();
        foreach (['masuk', 'keluar'] as $kind) {
            $marker = 'BB CRUD '.$kind;
            $this->post(route('surat-'.$kind.'.store'), $this->payload($kind, $marker))->assertRedirect(route('surat-'.$kind.'.index'));
            $list = $this->get($this->index($kind))->assertOk()->assertSee($marker);
            $matched = preg_match('~href="[^"]*/surat-'.$kind.'/(\d+)(?:/edit)?"~', $list->getContent(), $matches);
            $this->assertSame(1, $matched);
            $id = $matches[1];
            $this->get(route('surat-'.$kind.'.show', $id))->assertOk()->assertSee($marker)->assertSee('Admin Black Box');
            $this->get(route('surat-'.$kind.'.edit', $id))->assertOk()->assertSee($marker);
            $updated = 'BB Diubah '.$kind;
            $this->put(route('surat-'.$kind.'.update', $id), $this->payload($kind, $updated))->assertRedirect(route('surat-'.$kind.'.index'));
            $this->get(route('surat-'.$kind.'.show', $id))->assertOk()->assertSee($updated)->assertDontSee($marker);
            $this->delete(route('surat-'.$kind.'.destroy', $id))->assertRedirect(route('surat-'.$kind.'.index'));
            $this->get(route('surat-'.$kind.'.show', $id))->assertNotFound();
            $this->get($this->index($kind))->assertDontSee($updated);
        }
    }

    public function test_required_letter_fields_are_rejected_for_both_types(): void
    {
        $this->login();
        foreach (['masuk', 'keluar'] as $kind) {
            $payload = $this->payload($kind);
            $payload['perihal'] = '';
            $payload['tanggal_'.$kind.'_surat'] = '';
            $payload['tanggal_surat'] = '';
            $this->post(route('surat-'.$kind.'.store'), $payload)
                ->assertSessionHasErrors(['perihal', 'tanggal_'.$kind.'_surat', 'tanggal_surat']);
        }
    }

    public function test_attachment_type_and_ten_megabyte_limit_are_validated(): void
    {
        Storage::fake('public');
        $this->login();
        foreach (['masuk', 'keluar'] as $kind) {
            foreach ([UploadedFile::fake()->create('program.exe', 10, 'application/x-msdownload'), UploadedFile::fake()->create('besar.pdf', 10241, 'application/pdf')] as $file) {
                $this->post(route('surat-'.$kind.'.store'), $this->payload($kind) + ['lampiran' => $file])->assertSessionHasErrors('lampiran');
            }
        }
    }

    public function test_wrong_old_password_short_password_and_mismatched_confirmation_are_rejected(): void
    {
        $this->login();
        $this->patch(route('profile.password'), ['current_password' => 'wrong-password', 'password' => 'newpassword123', 'password_confirmation' => 'newpassword123'])
            ->assertSessionHasErrors('current_password');
        $this->patch(route('profile.password'), ['current_password' => 'password123', 'password' => '123', 'password_confirmation' => '123'])
            ->assertSessionHasErrors('password');
        $this->patch(route('profile.password'), ['current_password' => 'password123', 'password' => 'newpassword123', 'password_confirmation' => 'different123'])
            ->assertSessionHasErrors('password');
    }

    public function test_duplicate_and_invalid_profile_admin_emails_are_rejected(): void
    {
        $this->login();
        $other = User::factory()->create();
        foreach ([$other->email, 'email-invalid'] as $email) {
            $this->patch(route('profile.update'), ['name' => 'Nama Diubah', 'email' => $email])->assertSessionHasErrors('email');
            $this->post(route('profile.admins.store'), ['name' => 'Admin Baru', 'email' => $email, 'password' => 'password123', 'password_confirmation' => 'password123'])
                ->assertSessionHasErrors('email');
        }
        $this->get(route('profile'))->assertOk()->assertSee('Admin Black Box');
    }

    public function test_dashboard_displays_correct_month_year_and_combined_counts(): void
    {
        $this->login();
        $this->travelTo(Carbon::create(2026, 9, 17, 12));
        foreach (['2026-09-01', '2026-09-02', '2026-08-01', '2025-09-01'] as $index => $date) {
            $this->post(route('surat-masuk.store'), $this->payload('masuk', 'BB Statistik '.$index, $date))->assertSessionHasNoErrors();
        }
        $this->post(route('surat-keluar.store'), $this->payload('keluar', 'BB Statistik Keluar', '2026-09-03'))->assertSessionHasNoErrors();
        $page = $this->get(route('dashboard'))->assertOk();
        $document = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML($page->getContent());
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        $xpath = new DOMXPath($document);
        $counts = [];
        foreach ($xpath->query('//p[contains(@class,"text-3xl") and contains(@class,"font-extrabold")]') as $node) {
            $counts[] = trim($node->textContent);
        }
        $this->assertSame(['2', '1', '3', '5'], $counts);
        $page->assertSee('BB Statistik Keluar')->assertSee('BB Statistik 1');
    }

    public function test_search_month_year_all_months_and_date_order_for_both_types(): void
    {
        $this->login();
        foreach (['masuk', 'keluar'] as $kind) {
            foreach (['JAN' => '2026-01-02', 'FEB' => '2026-02-02', 'MAR' => '2026-03-02'] as $label => $date) {
                $this->post(route('surat-'.$kind.'.store'), $this->payload($kind, 'BB-'.$label.'-'.$kind, $date))->assertSessionHasNoErrors();
            }
            $this->get($this->index($kind))->assertOk()->assertSeeInOrder(['BB-MAR-'.$kind, 'BB-FEB-'.$kind, 'BB-JAN-'.$kind]);
            $this->get($this->index($kind, ['bulan' => '02', 'tahun' => '2026']))->assertOk()
                ->assertSee('BB-FEB-'.$kind)->assertDontSee('BB-MAR-'.$kind)->assertDontSee('BB-JAN-'.$kind);
            $this->get($this->index($kind, ['search' => 'BB-FEB']))->assertOk()
                ->assertSee('BB-FEB-'.$kind)->assertDontSee('BB-MAR-'.$kind)->assertDontSee('BB-JAN-'.$kind);
            $this->get($this->index($kind, ['search' => 'BB-TIDAK-ADA']))->assertOk()->assertDontSee('BB-FEB-'.$kind);
            $this->get($this->index($kind, ['tahun' => '20']))->assertSessionHasErrors('tahun');
        }
    }

    public function test_pagination_shows_ten_records_and_remaining_records_on_page_two(): void
    {
        $this->login();
        foreach (['masuk', 'keluar'] as $kind) {
            for ($number = 1; $number <= 12; $number++) {
                $marker = 'BB-PAGE-'.str_pad((string) $number, 2, '0', STR_PAD_LEFT).'-'.$kind;
                $this->post(route('surat-'.$kind.'.store'), $this->payload($kind, $marker, '2026-09-'.str_pad((string) $number, 2, '0', STR_PAD_LEFT)))
                    ->assertSessionHasNoErrors();
            }
            $this->get($this->index($kind))->assertOk()->assertSee('BB-PAGE-12-'.$kind)->assertSee('BB-PAGE-03-'.$kind)->assertDontSee('BB-PAGE-02-'.$kind);
            $this->get($this->index($kind, ['page' => 2]))->assertOk()->assertSee('BB-PAGE-02-'.$kind)->assertSee('BB-PAGE-01-'.$kind)->assertDontSee('BB-PAGE-12-'.$kind);
        }
    }

    public function test_both_letter_exports_are_downloadable_excel_files(): void
    {
        $this->login();
        foreach (['masuk', 'keluar'] as $kind) {
            $this->post(route('surat-'.$kind.'.store'), $this->payload($kind, 'BB Ekspor '.$kind))->assertSessionHasNoErrors();
            $download = $this->get(route('surat-'.$kind.'.export', ['bulan' => 'all', 'tahun' => 'all']));
            $download->assertOk()->assertDownload();
            $this->assertGreaterThan(1000, $download->baseResponse->getFile()->getSize());
        }
    }
}
