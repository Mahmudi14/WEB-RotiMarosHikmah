@extends('layouts.master')

@section('page_title', 'Profil Saya')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="overflow-hidden rounded-3xl bg-[#1F444C] p-6 text-white shadow-lg shadow-[#1F444C]/10">
            <div class="relative">
                <div class="absolute -right-10 -top-12 h-32 w-32 rounded-full bg-[#F4B044]/20"></div>
                <div class="absolute -bottom-16 -left-12 h-36 w-36 rounded-full bg-white/10"></div>

                <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#F4D3B0]">
                            Pengaturan Akun
                        </p>
                        <h2 class="mt-2 text-2xl font-black tracking-tight text-white">
                            Profil Saya
                        </h2>
                        <p class="mt-2 max-w-2xl text-sm leading-relaxed text-[#F4D3B0]">
                            Kelola informasi akun, email, dan keamanan password untuk akses sistem kasir Roti Maros Hikmah.
                        </p>
                    </div>

                    <div class="flex items-center gap-4 rounded-2xl border border-white/10 bg-white/10 px-4 py-3">
                        <div
                            class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[#F4B044] text-lg font-black text-[#2B1A10] shadow-lg shadow-black/10">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>

                        <div>
                            <p class="font-black text-white">
                                {{ auth()->user()->name }}
                            </p>
                            <p class="mt-1 text-xs font-semibold capitalize text-[#F4D3B0]">
                                {{ str_replace('_', ' ', auth()->user()->role) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Content --}}
        <div class="grid gap-6 xl:grid-cols-2">
            {{-- Informasi Profil --}}
            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-6 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                @include('profile.partials.update-profile-information-form')
            </div>

            {{-- Ubah Password --}}
            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-6 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                @include('profile.partials.update-password-form')
            </div>
        </div>
    </div>
@endsection
