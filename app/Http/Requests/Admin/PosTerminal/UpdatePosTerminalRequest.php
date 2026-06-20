<?php

namespace App\Http\Requests\Admin\PosTerminal;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePosTerminalRequest extends FormRequest
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
        $terminal = $this->route('pos_terminal');

        return [
            'kode_terminal' => [
                'required',
                'string',
                'max:50',
                Rule::unique('pos_terminals', 'kode_terminal')->ignore($terminal?->id),
            ],
            'nama_terminal' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:aktif,nonaktif'],
            'deskripsi' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'kode_terminal.required' => 'Kode terminal wajib diisi.',
            'kode_terminal.unique' => 'Kode terminal sudah digunakan.',
            'kode_terminal.max' => 'Kode terminal maksimal 50 karakter.',

            'nama_terminal.required' => 'Nama terminal wajib diisi.',
            'nama_terminal.max' => 'Nama terminal maksimal 255 karakter.',

            'status.required' => 'Status terminal wajib dipilih.',
            'status.in' => 'Status terminal tidak valid.',
        ];
    }
}