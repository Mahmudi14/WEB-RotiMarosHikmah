<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserAccountSeeder extends Seeder
{
    /**
     * Jalankan seeder akun awal.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@rotimaros.test',
                'password' => 'password',
                'role' => 'super_admin',
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@rotimaros.test',
                'password' => 'password',
                'role' => 'admin',
            ],
            [
                'name' => 'Kasir',
                'email' => 'kasir@rotimaros.test',
                'password' => 'password',
                'role' => 'kasir',
            ],
            [
                'name' => 'Keuangan',
                'email' => 'keuangan@rotimaros.test',
                'password' => 'password',
                'role' => 'keuangan',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => Hash::make($user['password']),
                    'role' => $user['role'],
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}