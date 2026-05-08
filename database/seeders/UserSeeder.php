<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ===== ADMIN =====
        User::create([
            'name'     => 'Admin AgriLit',
            'email'    => 'admin@agrilit.id',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'phone'    => '081234567890',
            'region'   => 'Malang Kota',
        ]);

        // ===== PAKAR =====
        User::create([
            'name'     => 'L. Elfianus',
            'email'    => 'elfianus@agrilit.id',
            'password' => Hash::make('password'),
            'role'     => 'pakar',
            'phone'    => '081298765432',
            'region'   => 'Malang',
        ]);

        User::create([
            'name'     => 'Dr. Siti Rahayu',
            'email'    => 'siti.rahayu@agrilit.id',
            'password' => Hash::make('password'),
            'role'     => 'pakar',
            'phone'    => '085678901234',
            'region'   => 'Batu, Malang',
        ]);

        // ===== PETANI =====
        $petani = [
            [
                'name'   => 'Budi Santoso',
                'email'  => 'budi@agrilit.id',
                'region' => 'Kepanjen, Malang',
                'phone'  => '082111222333',
            ],
            [
                'name'   => 'Slamet Riyadi',
                'email'  => 'slamet@agrilit.id',
                'region' => 'Lawang, Malang',
                'phone'  => '082444555666',
            ],
            [
                'name'   => 'Wati Ningsih',
                'email'  => 'wati@agrilit.id',
                'region' => 'Tumpang, Malang',
                'phone'  => '082777888999',
            ],
            [
                'name'   => 'Hendra Kusuma',
                'email'  => 'hendra@agrilit.id',
                'region' => 'Pujon, Malang',
                'phone'  => '083111222333',
            ],
            [
                'name'   => 'Dewi Lestari',
                'email'  => 'dewi@agrilit.id',
                'region' => 'Ngantang, Malang',
                'phone'  => '083444555666',
            ],
        ];

        foreach ($petani as $p) {
            User::create([
                'name'     => $p['name'],
                'email'    => $p['email'],
                'password' => Hash::make('password'),
                'role'     => 'petani',
                'phone'    => $p['phone'],
                'region'   => $p['region'],
            ]);
        }
    }
}