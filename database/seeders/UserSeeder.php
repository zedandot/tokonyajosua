<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Seed 3 user default untuk setiap role SIMTOKO.
     */
    public function run(): void
    {
        $users = [
            [
                'name'      => 'Josua Owner',
                'email'     => 'owner@simtoko.com',
                'password'  => Hash::make('password'),
                'role'      => 'owner',
                'is_active' => true,
            ],
            [
                'name'      => 'Kasir Utama',
                'email'     => 'kasir@simtoko.com',
                'password'  => Hash::make('password'),
                'role'      => 'kasir',
                'is_active' => true,
            ],
            [
                'name'      => 'Petugas Gudang',
                'email'     => 'gudang@simtoko.com',
                'password'  => Hash::make('password'),
                'role'      => 'gudang',
                'is_active' => true,
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(['email' => $user['email']], $user);
        }
    }
}
