<?php

namespace Tests\Feature\Admin;

use App\Models\AppSetting;
use App\Models\Petugas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UtilitasTest extends TestCase
{
    use RefreshDatabase;

    private Petugas $admin;

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
    }

    public function test_admin_can_view_utilitas_page(): void
    {
        $this->actingAs($this->admin, 'petugas')
            ->get(route('admin.utilitas'))
            ->assertOk();
    }

    public function test_admin_can_save_closing_template(): void
    {
        $this->actingAs($this->admin, 'petugas')
            ->post(route('admin.utilitas.save'), [
                'closing_template'  => 'Terima kasih telah menggunakan M-Lapor. Salam hangat dari kami.',
                'sla_keamanan'      => 24,
                'sla_infrastruktur' => 72,
                'sla_lingkungan'    => 48,
                'sla_sosial'        => 72,
                'sla_lainnya'       => 72,
            ])
            ->assertRedirect(route('admin.utilitas'));

        $this->assertDatabaseHas('app_settings', [
            'key'   => 'closing_template',
            'value' => 'Terima kasih telah menggunakan M-Lapor. Salam hangat dari kami.',
        ]);
    }

    public function test_utilitas_page_shows_existing_template(): void
    {
        AppSetting::set('closing_template', 'Template yang sudah tersimpan sebelumnya.');

        $this->actingAs($this->admin, 'petugas')
            ->get(route('admin.utilitas'))
            ->assertOk()
            ->assertSee('Template yang sudah tersimpan sebelumnya.');
    }

    public function test_closing_template_cannot_be_empty(): void
    {
        $this->actingAs($this->admin, 'petugas')
            ->post(route('admin.utilitas.save'), [
                'closing_template'  => '',
                'sla_keamanan'      => 24,
                'sla_infrastruktur' => 72,
                'sla_lingkungan'    => 48,
                'sla_sosial'        => 72,
                'sla_lainnya'       => 72,
            ])
            ->assertSessionHasErrors('closing_template');
    }
}
