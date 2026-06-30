<?php

namespace App\Services\Admin;

use App\Models\PosTerminal;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PosTerminalService
{
    public function statuses(): array
    {
        return [
            'aktif' => 'Aktif',
            'nonaktif' => 'Nonaktif',
        ];
    }

    public function getPaginatedTerminals(Request $request, int $perPage = 10): LengthAwarePaginator
    {
        return PosTerminal::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($query) use ($search) {
                    $query->where('kode_terminal', 'like', "%{$search}%")
                        ->orWhere('nama_terminal', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function createTerminal(array $data): PosTerminal
    {
        $data['kode_terminal'] = $data['kode_terminal'] ?: $this->generateTerminalCode();
        $data['bridge_token'] = $this->generateBridgeToken();
        $data['status'] = 'aktif';

        return PosTerminal::create($data);
    }

    public function updateTerminal(PosTerminal $terminal, array $data): PosTerminal
    {
        $terminal->update($data);

        return $terminal->refresh();
    }

    public function toggleStatus(PosTerminal $terminal): PosTerminal
    {
        $terminal->update([
            'status' => $terminal->status === 'aktif' ? 'nonaktif' : 'aktif',
        ]);

        return $terminal->refresh();
    }

    public function regenerateToken(PosTerminal $terminal): PosTerminal
    {
        $terminal->update([
            'bridge_token' => $this->generateBridgeToken(),
            'last_seen_at' => null,
        ]);

        return $terminal->refresh();
    }

    public function deleteTerminal(PosTerminal $terminal): void
    {
        DB::transaction(function () use ($terminal) {
            if ($this->isUsedByActiveShift($terminal)) {
                throw new Exception('Terminal tidak dapat dihapus karena sedang digunakan pada shift aktif.');
            }

            $terminal->update([
                'status' => 'nonaktif',
                'last_seen_at' => null,
            ]);

            $terminal->delete();
        });
    }

    private function generateTerminalCode(): string
    {
        $lastId = (int) PosTerminal::withTrashed()->max('id') + 1;

        do {
            $code = 'TRM-' . str_pad((string) $lastId, 3, '0', STR_PAD_LEFT);
            $lastId++;
        } while (PosTerminal::withTrashed()->where('kode_terminal', $code)->exists());

        return $code;
    }

    private function generateBridgeToken(): string
    {
        do {
            $token = Str::random(80);
        } while (PosTerminal::withTrashed()->where('bridge_token', $token)->exists());

        return $token;
    }

    private function isUsedByActiveShift(PosTerminal $terminal): bool
    {
        return DB::table('cashier_shifts')
            ->where('pos_terminal_id', $terminal->id)
            ->where('status', 'aktif')
            ->exists();
    }
}