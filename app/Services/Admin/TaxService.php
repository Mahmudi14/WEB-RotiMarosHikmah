<?php

namespace App\Services\Admin;

use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TaxService
{
    public function statuses(): array
    {
        return [
            'aktif' => 'Aktif',
            'nonaktif' => 'Nonaktif',
        ];
    }

    public function getPaginatedTaxes(Request $request, int $perPage = 10): LengthAwarePaginator
    {
        return Tax::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('nama_pajak', 'like', "%{$request->search}%");
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function createTax(array $data): Tax
    {
        return DB::transaction(function () use ($data) {
            Tax::query()
                ->where('status', 'aktif')
                ->update(['status' => 'nonaktif']);

            $data['status'] = 'aktif';

            return Tax::create($data);
        });
    }

    public function updateTax(Tax $tax, array $data): Tax
    {
        return DB::transaction(function () use ($tax, $data) {
            if ($data['status'] === 'aktif') {
                Tax::query()
                    ->where('id', '!=', $tax->id)
                    ->where('status', 'aktif')
                    ->update(['status' => 'nonaktif']);
            }

            $tax->update($data);

            return $tax->refresh();
        });
    }

    public function toggleStatus(Tax $tax): Tax
    {
        return DB::transaction(function () use ($tax) {
            if ($tax->status === 'aktif') {
                $tax->update([
                    'status' => 'nonaktif',
                ]);

                return $tax->refresh();
            }

            Tax::query()
                ->where('id', '!=', $tax->id)
                ->where('status', 'aktif')
                ->update(['status' => 'nonaktif']);

            $tax->update([
                'status' => 'aktif',
            ]);

            return $tax->refresh();
        });
    }

    public function deleteTax(Tax $tax): void
    {
        DB::transaction(function () use ($tax) {
            $tax->update([
                'status' => 'nonaktif',
            ]);

            $tax->delete();
        });
    }
}