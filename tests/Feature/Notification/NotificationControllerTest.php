<?php

namespace Tests\Feature\Notification;

use App\Models\Masyarakat;
use App\Models\Petugas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class NotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): Petugas
    {
        return Petugas::create([
            'nama_petugas' => 'Admin',
            'username'     => 'admin',
            'password'     => bcrypt('password'),
            'telp'         => '081234567890',
            'level'        => 'admin',
        ]);
    }

    private function makePetugas(): Petugas
    {
        return Petugas::create([
            'nama_petugas' => 'Petugas Satu',
            'username'     => 'petugas1',
            'password'     => bcrypt('password'),
            'telp'         => '081234567891',
            'level'        => 'petugas',
        ]);
    }

    private function makeMasyarakat(): Masyarakat
    {
        return Masyarakat::create([
            'nik'      => '1234567890123456',
            'nama'     => 'Budi',
            'username' => 'budi',
            'password' => bcrypt('password'),
            'telp'     => '081234567890',
        ]);
    }

    private function createNotification($notifiable, array $data = [], ?string $readAt = null): void
    {
        $notifiable->notifications()->create([
            'id'              => Str::uuid()->toString(),
            'type'            => 'App\\Notifications\\Test',
            'notifiable_type' => get_class($notifiable),
            'notifiable_id'   => $notifiable->getKey(),
            'data'            => array_merge([
                'type'    => 'test',
                'message' => 'Test notification message',
                'url'     => '/test',
            ], $data),
            'read_at'         => $readAt,
        ]);
    }

    // ─── Masyarakat ────────────────────────────────────────────────────────────

    public function test_masyarakat_notifications_index_returns_json(): void
    {
        $user = $this->makeMasyarakat();
        $this->createNotification($user, ['message' => 'Pengaduan Anda diproses']);
        $this->createNotification($user, ['message' => 'Petugas mengirim pesan'], now()->toDateTimeString());

        $response = $this->actingAs($user, 'masyarakat')
            ->getJson(route('masyarakat.notifications'));

        $response->assertOk()
            ->assertJsonStructure([
                'notifications' => [['id', 'type', 'message', 'url', 'read', 'created_at']],
                'unread_count',
            ])
            ->assertJsonPath('unread_count', 1);
    }

    public function test_masyarakat_mark_single_notification_read(): void
    {
        $user = $this->makeMasyarakat();
        $this->createNotification($user);

        $notifId = $user->notifications()->first()->id;

        $this->actingAs($user, 'masyarakat')
            ->postJson(route('masyarakat.notifications.read'), ['id' => $notifId])
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertNotNull($user->notifications()->where('id', $notifId)->value('read_at'));
    }

    public function test_masyarakat_mark_all_notifications_read(): void
    {
        $user = $this->makeMasyarakat();
        $this->createNotification($user);
        $this->createNotification($user);

        $this->actingAs($user, 'masyarakat')
            ->postJson(route('masyarakat.notifications.read'))
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertEquals(0, $user->unreadNotifications()->count());
    }

    public function test_masyarakat_notifications_empty_when_none(): void
    {
        $user = $this->makeMasyarakat();

        $this->actingAs($user, 'masyarakat')
            ->getJson(route('masyarakat.notifications'))
            ->assertOk()
            ->assertJsonPath('unread_count', 0)
            ->assertJsonPath('notifications', []);
    }

    // ─── Petugas ───────────────────────────────────────────────────────────────

    public function test_petugas_notifications_index_returns_json(): void
    {
        $petugas = $this->makePetugas();
        $this->createNotification($petugas, ['message' => 'Pengaduan baru di-assign']);

        $response = $this->actingAs($petugas, 'petugas')
            ->getJson(route('petugas.notifications'));

        $response->assertOk()
            ->assertJsonStructure([
                'notifications' => [['id', 'type', 'message', 'url', 'read', 'created_at']],
                'unread_count',
            ])
            ->assertJsonPath('unread_count', 1);
    }

    public function test_petugas_mark_all_notifications_read(): void
    {
        $petugas = $this->makePetugas();
        $this->createNotification($petugas);
        $this->createNotification($petugas);

        $this->actingAs($petugas, 'petugas')
            ->postJson(route('petugas.notifications.read'))
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertEquals(0, $petugas->unreadNotifications()->count());
    }

    public function test_petugas_mark_single_notification_read(): void
    {
        $petugas = $this->makePetugas();
        $this->createNotification($petugas);

        $notifId = $petugas->notifications()->first()->id;

        $this->actingAs($petugas, 'petugas')
            ->postJson(route('petugas.notifications.read'), ['id' => $notifId])
            ->assertOk();

        $this->assertNotNull($petugas->notifications()->where('id', $notifId)->value('read_at'));
    }

    // ─── Admin ─────────────────────────────────────────────────────────────────

    public function test_admin_notifications_index_returns_json(): void
    {
        $admin = $this->makeAdmin();
        $this->createNotification($admin, ['message' => 'SLA terlewat']);
        $this->createNotification($admin, ['message' => 'Feedback baru masuk'], now()->toDateTimeString());

        $response = $this->actingAs($admin, 'petugas')
            ->getJson(route('admin.notifications'));

        $response->assertOk()
            ->assertJsonStructure([
                'notifications' => [['id', 'type', 'message', 'url', 'read', 'created_at']],
                'unread_count',
            ])
            ->assertJsonPath('unread_count', 1);
    }

    public function test_admin_mark_all_notifications_read(): void
    {
        $admin = $this->makeAdmin();
        $this->createNotification($admin);
        $this->createNotification($admin);

        $this->actingAs($admin, 'petugas')
            ->postJson(route('admin.notifications.read'))
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertEquals(0, $admin->unreadNotifications()->count());
    }

    public function test_guest_cannot_access_masyarakat_notifications(): void
    {
        $this->getJson(route('masyarakat.notifications'))->assertRedirect();
    }

    public function test_guest_cannot_access_petugas_notifications(): void
    {
        $this->getJson(route('petugas.notifications'))->assertRedirect();
    }
}
