<?php

namespace App\Services\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class CashierService
{
    public const DEFAULT_PASSWORD = 'rotimaroshikmah111';

    public function getPaginatedCashiers(Request $request, int $perPage = 10): LengthAwarePaginator
    {
        return User::query()
            ->where('role', 'kasir')
            ->where('status', 'aktif')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function createCashier(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => 'kasir',
            'status' => 'aktif',
            'password' => Hash::make(self::DEFAULT_PASSWORD),
        ]);
    }

    public function updateCashier(User $cashier, array $data): User
    {
        $this->ensureActiveCashier($cashier);

        $cashier->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        return $cashier->refresh();
    }

    public function resetPassword(User $cashier): User
    {
        $this->ensureActiveCashier($cashier);

        $cashier->update([
            'password' => Hash::make(self::DEFAULT_PASSWORD),
        ]);

        return $cashier->refresh();
    }

    private function ensureActiveCashier(User $user): void
    {
        abort_if(
            $user->role !== 'kasir' || $user->status !== 'aktif',
            404
        );
    }
}