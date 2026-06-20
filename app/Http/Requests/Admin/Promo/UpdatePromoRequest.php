<?php

namespace App\Http\Requests\Admin\Promo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdatePromoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'kode_promo' => $this->kode_promo ? strtoupper(trim($this->kode_promo)) : null,
            'nilai_diskon' => $this->nilai_diskon ? str_replace(['.', ','], ['', '.'], $this->nilai_diskon) : null,
            'product_ids' => $this->product_ids ?? [],
        ]);
    }

    public function rules(): array
    {
        $promo = $this->route('promo');

        return [
            'nama_promo' => ['required', 'string', 'max:255'],

            'kode_promo' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('promos', 'kode_promo')->ignore($promo?->id),
            ],

            'tipe_diskon' => ['required', 'in:persentase,nominal'],
            'nilai_diskon' => ['required', 'numeric', 'min:1'],

            'cakupan_promo' => ['required', 'in:semua_menu,menu_tertentu'],

            'product_ids' => ['nullable', 'array'],
            'product_ids.*' => ['integer', 'exists:products,id'],

            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],

            'status' => ['required', 'in:aktif,nonaktif'],

            'deskripsi' => ['nullable', 'string'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($this->tipe_diskon === 'persentase' && (float) $this->nilai_diskon > 100) {
                    $validator->errors()->add('nilai_diskon', 'Diskon persentase tidak boleh lebih dari 100%.');
                }

                if ($this->cakupan_promo === 'menu_tertentu' && empty($this->product_ids)) {
                    $validator->errors()->add('product_ids', 'Pilih minimal satu produk untuk promo menu tertentu.');
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'nama_promo.required' => 'Nama promo wajib diisi.',
            'kode_promo.unique' => 'Kode promo sudah digunakan.',
            'tipe_diskon.required' => 'Tipe diskon wajib dipilih.',
            'tipe_diskon.in' => 'Tipe diskon tidak valid.',
            'nilai_diskon.required' => 'Nilai diskon wajib diisi.',
            'nilai_diskon.numeric' => 'Nilai diskon harus berupa angka.',
            'nilai_diskon.min' => 'Nilai diskon minimal 1.',
            'cakupan_promo.required' => 'Cakupan promo wajib dipilih.',
            'cakupan_promo.in' => 'Cakupan promo tidak valid.',
            'product_ids.*.exists' => 'Produk yang dipilih tidak valid.',
            'tanggal_mulai.date' => 'Tanggal mulai tidak valid.',
            'tanggal_selesai.date' => 'Tanggal selesai tidak valid.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
            'status.required' => 'Status promo wajib dipilih.',
            'status.in' => 'Status promo tidak valid.',
        ];
    }
}