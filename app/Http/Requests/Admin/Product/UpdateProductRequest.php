<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'category_id' => [
                'required',
                'exists:categories,id',
            ],
            'kode_produk' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('products', 'kode_produk')->ignore($productId),
            ],
            'nama_produk' => [
                'required',
                'string',
                'max:100',
                Rule::unique('products', 'nama_produk')->ignore($productId),
            ],
            'deskripsi' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'harga_jual' => [
                'required',
                'numeric',
                'min:0',
                'max:999999999',
            ],
            'gambar' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
            'status_ketersediaan' => [
                'required',
                Rule::in(['tersedia', 'habis']),
            ],
            'status' => [
                'required',
                Rule::in(['aktif', 'nonaktif']),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori produk wajib dipilih.',
            'category_id.exists' => 'Kategori produk tidak valid.',

            'kode_produk.string' => 'Kode produk harus berupa teks.',
            'kode_produk.max' => 'Kode produk maksimal 50 karakter.',
            'kode_produk.unique' => 'Kode produk sudah digunakan.',

            'nama_produk.required' => 'Nama produk wajib diisi.',
            'nama_produk.string' => 'Nama produk harus berupa teks.',
            'nama_produk.max' => 'Nama produk maksimal 100 karakter.',
            'nama_produk.unique' => 'Nama produk sudah digunakan.',

            'deskripsi.string' => 'Deskripsi harus berupa teks.',
            'deskripsi.max' => 'Deskripsi maksimal 1000 karakter.',

            'harga_jual.required' => 'Harga jual wajib diisi.',
            'harga_jual.numeric' => 'Harga jual harus berupa angka.',
            'harga_jual.min' => 'Harga jual tidak boleh kurang dari 0.',
            'harga_jual.max' => 'Harga jual terlalu besar.',

            'gambar.image' => 'File harus berupa gambar.',
            'gambar.mimes' => 'Gambar harus berformat jpg, jpeg, png, atau webp.',
            'gambar.max' => 'Ukuran gambar maksimal 2MB.',

            'status_ketersediaan.required' => 'Status ketersediaan wajib dipilih.',
            'status_ketersediaan.in' => 'Status ketersediaan tidak valid.',

            'status.required' => 'Status produk wajib dipilih.',
            'status.in' => 'Status produk tidak valid.',
        ];
    }
}