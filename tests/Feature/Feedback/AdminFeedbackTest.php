<?php

namespace Tests\Feature\Feedback;

use App\Models\Feedback;
use App\Models\FeedbackPenugasan;
use App\Models\Masyarakat;
use App\Models\Petugas;
use App\Notifications\Petugas\NewFeedbackAssigned;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdminFeedbackTest extends TestCase
{
    use RefreshDatabase;

    private Petugas $admin;
    private Petugas $petugas;
    private Masyarakat $masyarakat;

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

        $this->masyarakat = Masyarakat::create([
            'nik'      => '1234567890123456',
            'nama'     => 'Budi',
            'username' => 'budi',
            'password' => bcrypt('password'),
            'telp'     => '081234567890',
        ]);
    }

    private function makeFeedback(): Feedback
    {
        return Feedback::create([
            'masyarakat_id' => $this->masyarakat->id,
            'isi'           => 'Feedback pengujian dari masyarakat',
            'status'        => 'pending',
        ]);
    }

    public function test_admin_can_view_feedback_list(): void
    {
        $this->makeFeedback();

        $this->actingAs($this->admin, 'petugas')
            ->get(route('admin.feedback.index'))
            ->assertOk();
    }

    public function test_admin_can_view_feedback_detail(): void
    {
        $feedback = $this->makeFeedback();

        $this->actingAs($this->admin, 'petugas')
            ->get(route('admin.feedback.show', $feedback))
            ->assertOk();
    }

    public function test_admin_can_assign_individual_feedback(): void
    {
        Notification::fake();

        $feedback = $this->makeFeedback();

        $this->actingAs($this->admin, 'petugas')
            ->post(route('admin.feedback.assign', $feedback), [
                'tipe'       => 'individual',
                'petugas_id' => $this->petugas->id_petugas,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('feedback_penugasan', [
            'feedback_id' => $feedback->id,
            'tipe'        => 'individual',
            'petugas_id'  => $this->petugas->id_petugas,
        ]);
    }

    public function test_individual_assignment_sends_notification_to_petugas(): void
    {
        Notification::fake();

        $feedback = $this->makeFeedback();

        $this->actingAs($this->admin, 'petugas')
            ->post(route('admin.feedback.assign', $feedback), [
                'tipe'       => 'individual',
                'petugas_id' => $this->petugas->id_petugas,
            ]);

        Notification::assertSentTo($this->petugas, NewFeedbackAssigned::class);
    }

    public function test_admin_can_assign_global_feedback(): void
    {
        $feedback = $this->makeFeedback();

        $this->actingAs($this->admin, 'petugas')
            ->post(route('admin.feedback.assign', $feedback), [
                'tipe' => 'global',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('feedback_penugasan', [
            'feedback_id' => $feedback->id,
            'tipe'        => 'global',
            'petugas_id'  => null,
        ]);
    }

    public function test_duplicate_global_assignment_is_rejected(): void
    {
        $feedback = $this->makeFeedback();

        FeedbackPenugasan::create([
            'feedback_id' => $feedback->id,
            'tipe'        => 'global',
        ]);

        $this->actingAs($this->admin, 'petugas')
            ->post(route('admin.feedback.assign', $feedback), ['tipe' => 'global'])
            ->assertSessionHas('error');
    }

    public function test_admin_can_update_global_penugasan_status(): void
    {
        $feedback   = $this->makeFeedback();
        $penugasan  = FeedbackPenugasan::create([
            'feedback_id' => $feedback->id,
            'tipe'        => 'global',
            'status'      => 'pending',
        ]);

        $this->actingAs($this->admin, 'petugas')
            ->patch(route('admin.feedback.penugasan.status', [$feedback, $penugasan]), [
                'status' => 'proses',
                'pesan'  => null,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('feedback_penugasan', [
            'id'     => $penugasan->id,
            'status' => 'proses',
        ]);
    }

    public function test_admin_can_update_global_penugasan_to_selesai_with_pesan(): void
    {
        $feedback  = $this->makeFeedback();
        $penugasan = FeedbackPenugasan::create([
            'feedback_id' => $feedback->id,
            'tipe'        => 'global',
        ]);

        $this->actingAs($this->admin, 'petugas')
            ->patch(route('admin.feedback.penugasan.status', [$feedback, $penugasan]), [
                'status' => 'selesai',
                'pesan'  => 'Sudah ditindaklanjuti oleh tim.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('feedback_penugasan', [
            'id'     => $penugasan->id,
            'status' => 'selesai',
            'pesan'  => 'Sudah ditindaklanjuti oleh tim.',
        ]);
    }

    public function test_update_global_penugasan_to_selesai_requires_pesan(): void
    {
        $feedback  = $this->makeFeedback();
        $penugasan = FeedbackPenugasan::create([
            'feedback_id' => $feedback->id,
            'tipe'        => 'global',
        ]);

        $this->actingAs($this->admin, 'petugas')
            ->patch(route('admin.feedback.penugasan.status', [$feedback, $penugasan]), [
                'status' => 'selesai',
            ])
            ->assertSessionHasErrors('pesan');
    }

    public function test_admin_cannot_update_individual_penugasan_status(): void
    {
        $feedback  = $this->makeFeedback();
        $penugasan = FeedbackPenugasan::create([
            'feedback_id' => $feedback->id,
            'tipe'        => 'individual',
            'petugas_id'  => $this->petugas->id_petugas,
        ]);

        $this->actingAs($this->admin, 'petugas')
            ->patch(route('admin.feedback.penugasan.status', [$feedback, $penugasan]), [
                'status' => 'proses',
            ])
            ->assertForbidden();
    }

    public function test_admin_can_delete_penugasan(): void
    {
        $feedback  = $this->makeFeedback();
        $penugasan = FeedbackPenugasan::create([
            'feedback_id' => $feedback->id,
            'tipe'        => 'global',
        ]);

        $this->actingAs($this->admin, 'petugas')
            ->delete(route('admin.feedback.penugasan.destroy', [$feedback, $penugasan]))
            ->assertRedirect();

        $this->assertDatabaseMissing('feedback_penugasan', ['id' => $penugasan->id]);
    }

    public function test_penugasan_from_different_feedback_returns_404(): void
    {
        $feedback1 = $this->makeFeedback();
        $feedback2 = $this->makeFeedback();
        $penugasan = FeedbackPenugasan::create([
            'feedback_id' => $feedback1->id,
            'tipe'        => 'global',
        ]);

        $this->actingAs($this->admin, 'petugas')
            ->delete(route('admin.feedback.penugasan.destroy', [$feedback2, $penugasan]))
            ->assertNotFound();
    }
}
