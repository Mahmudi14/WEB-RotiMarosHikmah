@extends('layouts.master')

@section('page_title', 'Tambah Terminal Kasir')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="overflow-hidden rounded-3xl bg-[#1F444C] p-6 text-white shadow-lg shadow-[#1F444C]/10">
            <div class="relative">
                <div class="absolute -right-10 -top-12 h-32 w-32 rounded-full bg-[#F4B044]/20"></div>
                <div class="absolute -bottom-16 -left-12 h-36 w-36 rounded-full bg-white/10"></div>

                <div class="relative flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#F4D3B0]">
                            Admin / Terminal Kasir / Tambah
                        </p>

                        <h1 class="mt-2 text-2xl font-black tracking-tight">
                            Tambah Terminal Kasir
                        </h1>

                        <p class="mt-2 max-w-2xl text-sm font-medium leading-relaxed text-[#F7F6F4]/80">
                            Token bridge akan dibuat otomatis setelah terminal disimpan.
                        </p>
                    </div>

                    <a href="{{ route('admin.pos-terminals.index') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl border border-white/15 bg-white/10 px-5 py-3 text-sm font-black text-white transition hover:bg-white/15">
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        @include('admin.pos-terminals._form', [
            'action' => route('admin.pos-terminals.store'),
            'method' => 'POST',
            'submitLabel' => 'Simpan Terminal',
            'showStatus' => false,
        ])
    </div>
@endsection
