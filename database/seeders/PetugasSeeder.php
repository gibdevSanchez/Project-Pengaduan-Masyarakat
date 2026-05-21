<?php

namespace Database\Seeders;

use App\Models\Petugas;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PetugasSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            [
                'nama_petugas' => 'Administrator',
                'username'     => 'admin',
                'password'     => 'admin123',
                'telp'         => '08000000000',
                'level'        => 'admin',
            ],
            [
                'nama_petugas' => 'Budi Santoso',
                'username'     => 'petugas01',
                'password'     => 'petugas123',
                'telp'         => '081100000001',
                'level'        => 'petugas',
            ],
            [
                'nama_petugas' => 'Siti Rahayu',
                'username'     => 'petugas02',
                'password'     => 'petugas456',
                'telp'         => '081100000002',
                'level'        => 'petugas',
            ],
        ];

        $rows = [];
        foreach ($accounts as $account) {
            $plainPassword = $account['password'];
            $account['password'] = Hash::make($plainPassword);
            Petugas::create($account);

            $rows[] = [
                ucfirst($account['level']),
                $account['nama_petugas'],
                $account['username'],
                $plainPassword,
            ];
        }

        $this->command->table(['Tipe', 'Nama', 'Username', 'Password'], $rows);
    }
}
