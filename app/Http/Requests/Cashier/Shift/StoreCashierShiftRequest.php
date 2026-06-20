<?php

namespace App\Http\Requests\Cashier\Shift;

use Illuminate\Foundation\Http\FormRequest;

class StoreCashierShiftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'kasir';
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'opening_cash' => $this->opening_cash
                ? str_replace(['.', ','], ['', '.'], $this->opening_cash)
                : 0,
        ]);
    }

    public function rules(): array
    {
        return [
            'pos_terminal_id' => ['required', 'exists:pos_terminals,id'],
            'opening_cash' => ['required', 'numeric', 'min:0'],
            'opening_note' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'pos_terminal_id.required' => 'Terminal kasir wajib dipilih.',
            'pos_terminal_id.exists' => 'Terminal kasir tidak valid.',

            'opening_cash.required' => 'Kas awal wajib diisi.',
            'opening_cash.numeric' => 'Kas awal harus berupa angka.',
            'opening_cash.min' => 'Kas awal tidak boleh kurang dari 0.',
        ];
    }
}