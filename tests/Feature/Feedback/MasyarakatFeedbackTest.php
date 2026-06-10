<?php

namespace Tests\Feature\Feedback;

use App\Models\Masyarakat;
use App\Models\Petugas;
use App\Notifications\Admin\NewFeedbackSubmitted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MasyarakatFeedbackTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_masyarakat_can_submit_feedback(): void
    {
        $user = $this->makeMasyarakat();

        $this->actingAs($user, 'masyarakat')
            ->post(route('masyarakat.feedback.store'), ['isi' => 'Layanan sangat membantu masyarakat setempat'])
            ->assertRedirect();

        $this->assertDatabaseHas('feedback', [
            'masyarakat_id' => $user->id,
            'isi'           => 'Layanan sangat membantu masyarakat setempat',
        ]);
    }

    public function test_feedback_notification_sent_to_admin(): void
    {
        Notification::fake();

        $user  = $this->makeMasyarakat();
        $admin = $this->makeAdmin();

        $this->actingAs($user, 'masyarakat')
            ->post(route('masyarakat.feedback.store'), ['isi' => 'Layanan sangat membantu masyarakat setempat']);

        Notification::assertSentTo($admin, NewFeedbackSubmitted::class);
    }

    public function test_feedback_requires_isi(): void
    {
        $user = $this->makeMasyarakat();

        $this->actingAs($user, 'masyarakat')
            ->post(route('masyarakat.feedback.store'), ['isi' => ''])
            ->assertSessionHasErrors('isi');
    }

    public function test_feedback_isi_must_be_at_least_5_chars(): void
    {
        $user = $this->makeMasyarakat();

        $this->actingAs($user, 'masyarakat')
            ->post(route('masyarakat.feedback.store'), ['isi' => 'Hi'])
            ->assertSessionHasErrors('isi');
    }

    public function test_feedback_isi_cannot_exceed_2000_chars(): void
    {
        $user = $this->makeMasyarakat();

        $this->actingAs($user, 'masyarakat')
            ->post(route('masyarakat.feedback.store'), ['isi' => str_repeat('a', 2001)])
            ->assertSessionHasErrors('isi');
    }

    public function test_guest_cannot_submit_feedback(): void
    {
        $this->post(route('masyarakat.feedback.store'), ['isi' => 'Layanan bagus sekali'])
            ->assertRedirect(route('login'));
    }

    public function test_feedback_success_message_shown(): void
    {
        $user = $this->makeMasyarakat();

        $this->actingAs($user, 'masyarakat')
            ->post(route('masyarakat.feedback.store'), ['isi' => 'Layanan sangat membantu masyarakat'])
            ->assertSessionHas('success');
    }
}
