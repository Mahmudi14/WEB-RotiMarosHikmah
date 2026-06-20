<?php

namespace App\Http\Requests\Admin\PosTerminal;

use Illuminate\Foundation\Http\FormRequest;

class StorePosTerminalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'kode_terminal' => $this->kode_terminal
                ? strtoupper(trim($this->kode_terminal))
                : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'kode_terminal' => ['nullable', 'string', 'max:50', 'unique:pos_terminals,kode_terminal'],
            'nama_terminal' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'kode_terminal.unique' => 'Kode terminal sudah digunakan.',
            'kode_terminal.max' => 'Kode terminal maksimal 50 karakter.',

            'nama_terminal.required' => 'Nama terminal wajib diisi.',
            'nama_terminal.max' => 'Nama terminal maksimal 255 karakter.',
        ];
    }
}