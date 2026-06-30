<?php

namespace App\Http\Requests\Admin\Cashier;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCashierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        $cashier = $this->route('cashier');

        return [
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($cashier?->id),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama kasir wajib diisi.',
            'name.max' => 'Nama kasir maksimal 255 karakter.',

            'email.required' => 'Email kasir wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',
            'email.unique' => 'Email sudah digunakan.',
        ];
    }
}