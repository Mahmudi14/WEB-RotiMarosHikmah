<?php

namespace App\Http\Requests\Cashier\Pos;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'kasir';
    }

    protected function prepareForValidation(): void
    {
        $cart = json_decode($this->cart_json ?: '[]', true);

        $items = collect(is_array($cart) ? $cart : [])
            ->map(function ($item) {
                return [
                    'product_id' => $item['product_id'] ?? $item['id'] ?? null,
                    'qty' => $item['qty'] ?? null,
                ];
            })
            ->filter(fn($item) => $item['product_id'] && $item['qty'])
            ->values()
            ->all();

        $this->merge([
            'items' => $items,
            'paid_amount' => $this->filled('paid_amount')
                ? str_replace(['.', ','], ['', '.'], $this->paid_amount)
                : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'cart_json' => ['required', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1', 'max:999'],

            'payment_method' => ['required', Rule::in(['tunai', 'qris', 'transfer'])],
            'paid_amount' => ['required_if:payment_method,tunai', 'nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Keranjang masih kosong.',
            'items.min' => 'Keranjang masih kosong.',

            'items.*.product_id.required' => 'Produk tidak valid.',
            'items.*.product_id.exists' => 'Produk tidak ditemukan.',

            'items.*.qty.required' => 'Jumlah produk tidak valid.',
            'items.*.qty.integer' => 'Jumlah produk harus berupa angka.',
            'items.*.qty.min' => 'Jumlah produk minimal 1.',

            'payment_method.required' => 'Metode pembayaran wajib dipilih.',
            'payment_method.in' => 'Metode pembayaran tidak valid.',

            'paid_amount.required_if' => 'Uang diterima wajib diisi untuk pembayaran tunai.',
            'paid_amount.numeric' => 'Uang diterima harus berupa angka.',
            'paid_amount.min' => 'Uang diterima tidak boleh kurang dari 0.',
        ];
    }
}