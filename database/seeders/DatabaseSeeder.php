<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->command->info('Seeding akun sistem M-Lapor...');
        $this->command->newLine();

        $this->call(PetugasSeeder::class);
        $this->command->newLine();

        $this->call(MasyarakatSeeder::class);
        $this->command->newLine();

        $this->command->info('Seeding selesai. Simpan kredensial di atas untuk login.');
    }
}
