<?php

namespace Tests\Feature\Notification;

use App\Models\AppSetting;
use App\Models\Pengaduan;
use App\Models\Petugas;
use App\Notifications\Admin\SlaBreached as AdminSlaBreached;
use App\Notifications\Petugas\SlaBreached as PetugasSlaBreached;
use App\Notifications\Petugas\SlaNearBreach;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CheckSlaNotificationsTest extends TestCase
{
    use RefreshDatabase;

    private Petugas $admin;
    private Petugas $petugas;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Petugas::create([
            'nama_petugas' => 'Admin',
            'username'     => 'admin',
            'password'     => bcrypt('password'),
            'telp'         => '081234567890',
            'level'        => 'admin',
        ]);

        $this->petugas = Petugas::create([
            'nama_petugas' => 'Petugas Satu',
            'username'     => 'petugas1',
            'password'     => bcrypt('password'),
            'telp'         => '081234567891',
            'level'        => 'petugas',
        ]);

        // Use default SLA: keamanan = 24h
        AppSetting::set('sla_keamanan', '24');
        AppSetting::set('sla_lainnya', '72');
    }

    private function makePengaduan(array $attrs = []): Pengaduan
    {
        return Pengaduan::create(array_merge([
            'isi_laporan' => 'Laporan test',
            'status'      => 'proses',
            'kategori'    => 'keamanan',
            'id_petugas'  => $this->petugas->id_petugas,
        ], $attrs));
    }

    public function test_command_runs_successfully(): void
    {
        Notification::fake();

        $exitCode = Artisan::call('sla:check-notifications');

        $this->assertEquals(0, $exitCode);
    }

    public function test_near_breach_notification_sent_when_15_percent_or_less_remaining(): void
    {
        Notification::fake();

        // SLA 24h; 15% = 3.6h. Set created_at 21h ago → 3h remaining = 12.5% ≤ 15%
        $pengaduan = $this->makePengaduan([
            'created_at' => now()->subHours(21),
        ]);

        Artisan::call('sla:check-notifications');

        Notification::assertSentTo($this->petugas, SlaNearBreach::class);
        $this->assertNotNull($pengaduan->fresh()->sla_notif_sent_at);
    }

    public function test_near_breach_not_sent_when_above_15_percent_remaining(): void
    {
        Notification::fake();

        // SLA 24h; set created_at 18h ago → 6h remaining = 25% > 15%
        $this->makePengaduan([
            'created_at' => now()->subHours(18),
        ]);

        Artisan::call('sla:check-notifications');

        Notification::assertNotSentTo($this->petugas, SlaNearBreach::class);
    }

    public function test_breach_notification_sent_when_overdue(): void
    {
        Notification::fake();

        // SLA 24h; set created_at 25h ago → overdue
        $pengaduan = $this->makePengaduan([
            'created_at' => now()->subHours(25),
        ]);

        Artisan::call('sla:check-notifications');

        Notification::assertSentTo($this->petugas, PetugasSlaBreached::class);
        Notification::assertSentTo($this->admin, AdminSlaBreached::class);
        $this->assertNotNull($pengaduan->fresh()->sla_breach_notif_sent_at);
    }

    public function test_breach_notification_not_sent_twice(): void
    {
        Notification::fake();

        $this->makePengaduan([
            'created_at'              => now()->subHours(25),
            'sla_breach_notif_sent_at'=> now()->subHour(),
        ]);

        Artisan::call('sla:check-notifications');

        Notification::assertNotSentTo($this->petugas, PetugasSlaBreached::class);
        Notification::assertNotSentTo($this->admin, AdminSlaBreached::class);
    }

    public function test_near_breach_not_sent_twice(): void
    {
        Notification::fake();

        $this->makePengaduan([
            'created_at'        => now()->subHours(21),
            'sla_notif_sent_at' => now()->subHour(),
        ]);

        Artisan::call('sla:check-notifications');

        Notification::assertNotSentTo($this->petugas, SlaNearBreach::class);
    }

    public function test_no_notification_for_completed_complaints(): void
    {
        Notification::fake();

        // selesai status — should not be picked up
        $this->makePengaduan([
            'status'     => 'selesai',
            'created_at' => now()->subHours(30),
        ]);

        Artisan::call('sla:check-notifications');

        Notification::assertNotSentTo($this->petugas, PetugasSlaBreached::class);
    }

    public function test_breach_notif_not_sent_when_no_petugas_assigned(): void
    {
        Notification::fake();

        $this->makePengaduan([
            'created_at' => now()->subHours(25),
            'id_petugas' => null,
        ]);

        Artisan::call('sla:check-notifications');

        // Admin still notified even without petugas
        Notification::assertSentTo($this->admin, AdminSlaBreached::class);
        Notification::assertNotSentTo($this->petugas, PetugasSlaBreached::class);
    }
}
