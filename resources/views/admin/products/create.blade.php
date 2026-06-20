@extends('layouts.master')

@section('page_title', 'Tambah Produk')

@section('content')
    <div class="space-y-6">
        <div class="overflow-hidden rounded-3xl bg-[#1F444C] p-6 text-white shadow-lg shadow-[#1F444C]/10">
            <div class="relative">
                <div class="absolute -right-10 -top-12 h-32 w-32 rounded-full bg-[#F4B044]/20"></div>
                <div class="absolute -bottom-16 -left-12 h-36 w-36 rounded-full bg-white/10"></div>

                <div class="relative flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#F4D3B0]">
                            Admin
                        </p>
                        <h2 class="mt-2 text-2xl font-black tracking-tight text-white">
                            Tambah Produk
                        </h2>
                        <p class="mt-2 max-w-2xl text-sm leading-relaxed text-[#F4D3B0]">
                            Tambahkan produk baru beserta kategori, harga jual, dan gambar produk.
                        </p>
                    </div>

                    <a href="{{ route('admin.products.index') }}"
                        class="inline-flex items-center justify-center rounded-2xl border border-white/20 bg-white/10 px-5 py-3 text-sm font-black text-white transition hover:bg-white/15">
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-6 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
            @include('admin.products._form', [
                'action' => route('admin.products.store'),
                'method' => 'POST',
                'buttonText' => 'Simpan Produk',
                'product' => null,
                'categories' => $categories,
                'showStatuses' => false,
            ])
        </div>
    </div>
@endsection
