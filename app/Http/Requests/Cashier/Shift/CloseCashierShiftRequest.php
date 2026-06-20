<?php

namespace App\Http\Requests\Cashier\Shift;

use Illuminate\Foundation\Http\FormRequest;

class CloseCashierShiftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'kasir';
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'closing_cash' => $this->filled('closing_cash')
                ? str_replace(['.', ','], ['', '.'], $this->closing_cash)
                : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'closing_cash' => ['required', 'numeric', 'min:0'],
            'closing_note' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'closing_cash.required' => 'Uang fisik di laci wajib diisi.',
            'closing_cash.numeric' => 'Uang fisik di laci harus berupa angka.',
            'closing_cash.min' => 'Uang fisik di laci tidak boleh kurang dari 0.',
        ];
    }
}