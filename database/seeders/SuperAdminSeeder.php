<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $name = config('super_admin.name');
        $email = config('super_admin.email');
        $password = config('super_admin.password');

        if (! $email || ! $password) {
            throw new RuntimeException('SUPER_ADMIN_EMAIL dan SUPER_ADMIN_PASSWORD wajib diisi');
        }

        User::unguarded(function () use ($name, $email, $password) {
            User::updateOrCreate(
                [
                    'email' => $email,
                ],
                [
                    'name' => $name,
                    'role' => 'super_admin',
                    'status' => 'aktif',
                    'email_verified_at' => now(),
                    'password' => Hash::make($password),
                ]
            );
        });
    }
}