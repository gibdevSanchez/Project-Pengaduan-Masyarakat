<?php

namespace Tests\Feature\Feedback;

use App\Models\Feedback;
use App\Models\FeedbackPenugasan;
use App\Models\Masyarakat;
use App\Models\Petugas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetugasFeedbackTest extends TestCase
{
    use RefreshDatabase;

    private Petugas $petugas;
    private Petugas $petugas2;
    private Masyarakat $masyarakat;

    protected function setUp(): void
    {
        parent::setUp();

        $this->petugas = Petugas::create([
            'nama_petugas' => 'Petugas Satu',
            'username'     => 'petugas1',
            'password'     => bcrypt('password'),
            'telp'         => '081234567890',
            'level'        => 'petugas',
        ]);

        $this->petugas2 = Petugas::create([
            'nama_petugas' => 'Petugas Dua',
            'username'     => 'petugas2',
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
            'isi'           => 'Feedback pengujian',
        ]);
    }

    private function makePenugasan(Feedback $feedback, Petugas $petugas, string $status = 'pending'): FeedbackPenugasan
    {
        return FeedbackPenugasan::create([
            'feedback_id' => $feedback->id,
            'tipe'        => 'individual',
            'petugas_id'  => $petugas->id_petugas,
            'status'      => $status,
        ]);
    }

    public function test_petugas_can_view_feedback_list(): void
    {
        $this->actingAs($this->petugas, 'petugas')
            ->get(route('petugas.feedback.index'))
            ->assertOk();
    }

    public function test_petugas_can_update_individual_penugasan_status(): void
    {
        $feedback  = $this->makeFeedback();
        $penugasan = $this->makePenugasan($feedback, $this->petugas);

        $this->actingAs($this->petugas, 'petugas')
            ->patch(route('petugas.feedback.status', $penugasan), [
                'status' => 'proses',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('feedback_penugasan', [
            'id'     => $penugasan->id,
            'status' => 'proses',
        ]);
    }

    public function test_petugas_can_mark_penugasan_selesai_with_pesan(): void
    {
        $feedback  = $this->makeFeedback();
        $penugasan = $this->makePenugasan($feedback, $this->petugas);

        $this->actingAs($this->petugas, 'petugas')
            ->patch(route('petugas.feedback.status', $penugasan), [
                'status' => 'selesai',
                'pesan'  => 'Sudah diselesaikan.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('feedback_penugasan', [
            'id'     => $penugasan->id,
            'status' => 'selesai',
            'pesan'  => 'Sudah diselesaikan.',
        ]);
    }

    public function test_selesai_status_requires_pesan(): void
    {
        $feedback  = $this->makeFeedback();
        $penugasan = $this->makePenugasan($feedback, $this->petugas);

        $this->actingAs($this->petugas, 'petugas')
            ->patch(route('petugas.feedback.status', $penugasan), [
                'status' => 'selesai',
            ])
            ->assertSessionHasErrors('pesan');
    }

    public function test_petugas_cannot_update_finalized_penugasan(): void
    {
        $feedback  = $this->makeFeedback();
        $penugasan = $this->makePenugasan($feedback, $this->petugas, 'selesai');

        $this->actingAs($this->petugas, 'petugas')
            ->patch(route('petugas.feedback.status', $penugasan), [
                'status' => 'proses',
            ])
            ->assertForbidden();
    }

    public function test_petugas_cannot_update_other_petugas_penugasan(): void
    {
        $feedback  = $this->makeFeedback();
        $penugasan = $this->makePenugasan($feedback, $this->petugas2);

        $this->actingAs($this->petugas, 'petugas')
            ->patch(route('petugas.feedback.status', $penugasan), [
                'status' => 'proses',
            ])
            ->assertForbidden();
    }

    public function test_petugas_can_hide_finalized_penugasan(): void
    {
        $feedback  = $this->makeFeedback();
        $penugasan = $this->makePenugasan($feedback, $this->petugas, 'selesai');

        $this->actingAs($this->petugas, 'petugas')
            ->patch(route('petugas.feedback.hide', $penugasan))
            ->assertRedirect();

        $this->assertDatabaseHas('feedback_penugasan', [
            'id'                => $penugasan->id,
            'hidden_by_petugas' => true,
        ]);
    }

    public function test_petugas_cannot_hide_pending_penugasan(): void
    {
        $feedback  = $this->makeFeedback();
        $penugasan = $this->makePenugasan($feedback, $this->petugas, 'pending');

        $this->actingAs($this->petugas, 'petugas')
            ->patch(route('petugas.feedback.hide', $penugasan))
            ->assertForbidden();
    }

    public function test_petugas_cannot_hide_other_petugas_penugasan(): void
    {
        $feedback  = $this->makeFeedback();
        $penugasan = $this->makePenugasan($feedback, $this->petugas2, 'selesai');

        $this->actingAs($this->petugas, 'petugas')
            ->patch(route('petugas.feedback.hide', $penugasan))
            ->assertForbidden();
    }
}
