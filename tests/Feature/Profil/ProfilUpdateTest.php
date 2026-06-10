<?php

namespace Tests\Feature\Profil;

use App\Models\Masyarakat;
use App\Models\Petugas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfilUpdateTest extends TestCase
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

    private function makePetugas(): Petugas
    {
        return Petugas::create([
            'nama_petugas' => 'Petugas Satu',
            'username'     => 'petugas1',
            'password'     => bcrypt('password'),
            'telp'         => '081234567890',
            'level'        => 'petugas',
        ]);
    }

    // ─── Masyarakat ────────────────────────────────────────────────────────────

    public function test_masyarakat_can_update_telp(): void
    {
        $user = $this->makeMasyarakat();

        $this->actingAs($user, 'masyarakat')
            ->put(route('masyarakat.profil.update'), ['telp' => '089999999999'])
            ->assertRedirect();

        $this->assertDatabaseHas('masyarakat', [
            'id'   => $user->id,
            'telp' => '089999999999',
        ]);
    }

    public function test_masyarakat_telp_required(): void
    {
        $user = $this->makeMasyarakat();

        $this->actingAs($user, 'masyarakat')
            ->put(route('masyarakat.profil.update'), ['telp' => ''])
            ->assertSessionHasErrors('telp');
    }

    public function test_masyarakat_can_change_password(): void
    {
        $user = $this->makeMasyarakat();

        $this->actingAs($user, 'masyarakat')
            ->put(route('masyarakat.profil.update'), [
                'telp'                  => '081234567890',
                'password'              => 'newpassword',
                'password_confirmation' => 'newpassword',
            ])
            ->assertRedirect();

        $this->assertTrue(Hash::check('newpassword', $user->fresh()->password));
    }

    public function test_masyarakat_password_min_6_chars(): void
    {
        $user = $this->makeMasyarakat();

        $this->actingAs($user, 'masyarakat')
            ->put(route('masyarakat.profil.update'), [
                'telp'                  => '081234567890',
                'password'              => '12345',
                'password_confirmation' => '12345',
            ])
            ->assertSessionHasErrors('password');
    }

    public function test_masyarakat_password_must_be_confirmed(): void
    {
        $user = $this->makeMasyarakat();

        $this->actingAs($user, 'masyarakat')
            ->put(route('masyarakat.profil.update'), [
                'telp'                  => '081234567890',
                'password'              => 'newpassword',
                'password_confirmation' => 'different',
            ])
            ->assertSessionHasErrors('password');
    }

    public function test_masyarakat_update_password_via_dedicated_route(): void
    {
        $user = $this->makeMasyarakat();

        $this->actingAs($user, 'masyarakat')
            ->put(route('masyarakat.profil.password'), [
                'password'              => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ])
            ->assertRedirect();

        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }

    public function test_masyarakat_password_route_requires_confirmation(): void
    {
        $user = $this->makeMasyarakat();

        $this->actingAs($user, 'masyarakat')
            ->put(route('masyarakat.profil.password'), [
                'password'              => 'newpassword123',
                'password_confirmation' => 'mismatch',
            ])
            ->assertSessionHasErrors('password');
    }

    public function test_masyarakat_can_view_profil_page(): void
    {
        $user = $this->makeMasyarakat();

        $this->actingAs($user, 'masyarakat')
            ->get(route('masyarakat.profil'))
            ->assertOk();
    }

    // ─── Petugas ───────────────────────────────────────────────────────────────

    public function test_petugas_can_update_telp(): void
    {
        $petugas = $this->makePetugas();

        $this->actingAs($petugas, 'petugas')
            ->put(route('petugas.profil.update'), ['telp' => '089999999999'])
            ->assertRedirect();

        $this->assertDatabaseHas('petugas', [
            'id_petugas' => $petugas->id_petugas,
            'telp'       => '089999999999',
        ]);
    }

    public function test_petugas_telp_required(): void
    {
        $petugas = $this->makePetugas();

        $this->actingAs($petugas, 'petugas')
            ->put(route('petugas.profil.update'), ['telp' => ''])
            ->assertSessionHasErrors('telp');
    }

    public function test_petugas_can_change_password(): void
    {
        $petugas = $this->makePetugas();

        $this->actingAs($petugas, 'petugas')
            ->put(route('petugas.profil.update'), [
                'telp'                  => '081234567890',
                'password'              => 'newpassword',
                'password_confirmation' => 'newpassword',
            ])
            ->assertRedirect();

        $this->assertTrue(Hash::check('newpassword', $petugas->fresh()->password));
    }

    public function test_petugas_password_min_6_chars(): void
    {
        $petugas = $this->makePetugas();

        $this->actingAs($petugas, 'petugas')
            ->put(route('petugas.profil.update'), [
                'telp'                  => '081234567890',
                'password'              => '12345',
                'password_confirmation' => '12345',
            ])
            ->assertSessionHasErrors('password');
    }

    public function test_petugas_can_view_profil_page(): void
    {
        $petugas = $this->makePetugas();

        $this->actingAs($petugas, 'petugas')
            ->get(route('petugas.profil'))
            ->assertOk();
    }

    public function test_guest_cannot_update_masyarakat_profil(): void
    {
        $this->put(route('masyarakat.profil.update'), ['telp' => '089999999999'])
            ->assertRedirect(route('login'));
    }
}
