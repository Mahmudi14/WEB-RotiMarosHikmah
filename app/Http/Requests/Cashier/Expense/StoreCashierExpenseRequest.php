<?php

namespace App\Http\Requests\Cashier\Expense;

use Illuminate\Foundation\Http\FormRequest;

class StoreCashierExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'kasir';
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'harga' => $this->filled('harga')
                ? str_replace(['.', ','], ['', '.'], $this->harga)
                : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nama_pengeluaran' => ['required', 'string', 'max:255'],
            'harga' => ['required', 'numeric', 'min:1'],
            'deskripsi' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_pengeluaran.required' => 'Apa yang dibeli wajib diisi.',
            'nama_pengeluaran.max' => 'Apa yang dibeli maksimal 255 karakter.',

            'harga.required' => 'Harga wajib diisi.',
            'harga.numeric' => 'Harga harus berupa angka.',
            'harga.min' => 'Harga minimal Rp 1.',
        ];
    }
}