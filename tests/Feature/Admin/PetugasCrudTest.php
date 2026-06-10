<?php

namespace Tests\Feature\Admin;

use App\Models\Petugas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetugasCrudTest extends TestCase
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

    public function test_admin_can_view_petugas_list(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin, 'petugas')
            ->get(route('admin.petugas.index'))
            ->assertOk();
    }

    public function test_admin_can_view_create_form(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin, 'petugas')
            ->get(route('admin.petugas.create'))
            ->assertOk();
    }

    public function test_admin_can_create_petugas(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin, 'petugas')
            ->post(route('admin.petugas.store'), [
                'nama_petugas'          => 'Petugas Baru',
                'username'              => 'petugasbaru',
                'password'              => 'password123',
                'password_confirmation' => 'password123',
                'telp'                  => '081234567891',
            ])
            ->assertRedirect(route('admin.petugas.index'));

        $this->assertDatabaseHas('petugas', [
            'username' => 'petugasbaru',
            'level'    => 'petugas',
        ]);
    }

    public function test_create_petugas_requires_unique_username(): void
    {
        $admin = $this->makeAdmin();

        Petugas::create([
            'nama_petugas' => 'Existing',
            'username'     => 'existing',
            'password'     => bcrypt('password'),
            'telp'         => '081234567891',
            'level'        => 'petugas',
        ]);

        $this->actingAs($admin, 'petugas')
            ->post(route('admin.petugas.store'), [
                'nama_petugas'          => 'Duplicate',
                'username'              => 'existing',
                'password'              => 'password123',
                'password_confirmation' => 'password123',
                'telp'                  => '081234567892',
            ])
            ->assertSessionHasErrors('username');
    }

    public function test_create_petugas_requires_password_confirmation(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin, 'petugas')
            ->post(route('admin.petugas.store'), [
                'nama_petugas'          => 'Petugas Baru',
                'username'              => 'petugasbaru',
                'password'              => 'password123',
                'password_confirmation' => 'wrongpassword',
                'telp'                  => '081234567891',
            ])
            ->assertSessionHasErrors('password');
    }

    public function test_admin_can_view_edit_form(): void
    {
        $admin   = $this->makeAdmin();
        $petugas = Petugas::create([
            'nama_petugas' => 'Petugas Edit',
            'username'     => 'petugasedit',
            'password'     => bcrypt('password'),
            'telp'         => '081234567891',
            'level'        => 'petugas',
        ]);

        $this->actingAs($admin, 'petugas')
            ->get(route('admin.petugas.edit', $petugas->id_petugas))
            ->assertOk();
    }

    public function test_admin_can_update_petugas(): void
    {
        $admin   = $this->makeAdmin();
        $petugas = Petugas::create([
            'nama_petugas' => 'Petugas Lama',
            'username'     => 'petugaslama',
            'password'     => bcrypt('password'),
            'telp'         => '081234567891',
            'level'        => 'petugas',
        ]);

        $this->actingAs($admin, 'petugas')
            ->put(route('admin.petugas.update', $petugas->id_petugas), [
                'nama_petugas' => 'Petugas Baru',
                'username'     => 'petugaslama',
                'telp'         => '089999999999',
            ])
            ->assertRedirect(route('admin.petugas.index'));

        $this->assertDatabaseHas('petugas', [
            'id_petugas'   => $petugas->id_petugas,
            'nama_petugas' => 'Petugas Baru',
            'telp'         => '089999999999',
        ]);
    }

    public function test_admin_can_update_petugas_password(): void
    {
        $admin   = $this->makeAdmin();
        $petugas = Petugas::create([
            'nama_petugas' => 'Petugas Satu',
            'username'     => 'petugas1',
            'password'     => bcrypt('oldpassword'),
            'telp'         => '081234567891',
            'level'        => 'petugas',
        ]);

        $this->actingAs($admin, 'petugas')
            ->put(route('admin.petugas.update', $petugas->id_petugas), [
                'nama_petugas'          => 'Petugas Satu',
                'username'              => 'petugas1',
                'telp'                  => '081234567891',
                'password'              => 'newpassword',
                'password_confirmation' => 'newpassword',
            ])
            ->assertRedirect();

        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('newpassword', $petugas->fresh()->password));
    }

    public function test_admin_can_delete_petugas(): void
    {
        $admin   = $this->makeAdmin();
        $petugas = Petugas::create([
            'nama_petugas' => 'Petugas Hapus',
            'username'     => 'petugashapus',
            'password'     => bcrypt('password'),
            'telp'         => '081234567891',
            'level'        => 'petugas',
        ]);

        $this->actingAs($admin, 'petugas')
            ->delete(route('admin.petugas.destroy', $petugas->id_petugas))
            ->assertRedirect(route('admin.petugas.index'));

        $this->assertDatabaseMissing('petugas', ['id_petugas' => $petugas->id_petugas]);
    }

    public function test_admin_cannot_delete_admin_level_user(): void
    {
        $admin  = $this->makeAdmin();
        $admin2 = Petugas::create([
            'nama_petugas' => 'Admin Dua',
            'username'     => 'admin2',
            'password'     => bcrypt('password'),
            'telp'         => '081234567891',
            'level'        => 'admin',
        ]);

        // destroy checks level = 'petugas', so this should 404
        $this->actingAs($admin, 'petugas')
            ->delete(route('admin.petugas.destroy', $admin2->id_petugas))
            ->assertNotFound();
    }

    public function test_non_admin_cannot_create_petugas(): void
    {
        $petugas = Petugas::create([
            'nama_petugas' => 'Petugas Biasa',
            'username'     => 'petugasbiasa',
            'password'     => bcrypt('password'),
            'telp'         => '081234567891',
            'level'        => 'petugas',
        ]);

        $this->actingAs($petugas, 'petugas')
            ->post(route('admin.petugas.store'), [
                'nama_petugas'          => 'New',
                'username'              => 'newpetugas',
                'password'              => 'password123',
                'password_confirmation' => 'password123',
                'telp'                  => '081234567892',
            ])
            ->assertRedirect();
    }
}
