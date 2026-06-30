<?php

namespace App\Services\SuperAdmin;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        return User::query()
            ->whereIn('role', ['admin', 'kasir', 'keuangan'])
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('role', 'like', "%{$search}%");
                });
            })
            ->when($filters['role'] ?? null, function ($query, $role) {
                $query->where('role', $role);
            })
            ->when($filters['status'] ?? null, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();
    }

    public function create(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'status' => 'aktif',
            'password' => Hash::make($data['password']),
        ]);
    }

    public function update(User $user, array $data): User
    {
        $this->ensureNotSuperAdmin($user);

        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
        ];

        if (! empty($data['reset_password'])) {
            $payload['password'] = Hash::make('rotimaroshikmah111');
        } elseif (! empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }

        $user->update($payload);

        return $user->refresh();
    }

    public function toggleStatus(User $user): User
    {
        $this->ensureNotSuperAdmin($user);

        $user->update([
            'status' => $user->status === 'aktif' ? 'nonaktif' : 'aktif',
        ]);

        return $user->refresh();
    }

    public function roles(): array
    {
        return [
            'admin' => 'Admin',
            'kasir' => 'Kasir',
            'keuangan' => 'Keuangan',
        ];
    }

    public function statuses(): array
    {
        return [
            'aktif' => 'Aktif',
            'nonaktif' => 'Nonaktif',
        ];
    }

    private function ensureNotSuperAdmin(User $user): void
    {
        if ($user->role === 'super_admin') {
            throw ValidationException::withMessages([
                'user' => 'Akun Super Admin tidak boleh diubah melalui halaman ini.',
            ]);
        }
    }
}