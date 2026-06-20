<?php

namespace App\Http\Requests\Admin\Tax;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaxRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'persentase' => $this->persentase
                ? str_replace(['.', ','], ['', '.'], $this->persentase)
                : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nama_pajak' => ['required', 'string', 'max:255'],
            'persentase' => ['required', 'numeric', 'min:0.01', 'max:100'],
            'status' => ['required', 'in:aktif,nonaktif'],
            'deskripsi' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_pajak.required' => 'Nama pajak wajib diisi.',
            'nama_pajak.max' => 'Nama pajak maksimal 255 karakter.',

            'persentase.required' => 'Persentase pajak wajib diisi.',
            'persentase.numeric' => 'Persentase pajak harus berupa angka.',
            'persentase.min' => 'Persentase pajak minimal 0,01%.',
            'persentase.max' => 'Persentase pajak maksimal 100%.',

            'status.required' => 'Status pajak wajib dipilih.',
            'status.in' => 'Status pajak tidak valid.',
        ];
    }
}