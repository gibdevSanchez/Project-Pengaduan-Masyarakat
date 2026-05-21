<?php

namespace Database\Seeders;

use App\Models\Masyarakat;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MasyarakatSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            [
                'nik'      => '3201010101010001',
                'nama'     => 'Andi Pratama',
                'username' => 'warga01',
                'password' => 'warga123',
                'telp'     => '082200000001',
            ],
            [
                'nik'      => '3201010101010002',
                'nama'     => 'Dewi Lestari',
                'username' => 'warga02',
                'password' => 'warga456',
                'telp'     => '082200000002',
            ],
        ];

        $rows = [];
        foreach ($accounts as $account) {
            $plainPassword = $account['password'];
            $account['password'] = Hash::make($plainPassword);
            Masyarakat::create($account);

            $rows[] = [
                'Masyarakat',
                $account['nama'],
                $account['username'],
                $plainPassword,
            ];
        }

        $this->command->table(['Tipe', 'Nama', 'Username', 'Password'], $rows);
    }
}
