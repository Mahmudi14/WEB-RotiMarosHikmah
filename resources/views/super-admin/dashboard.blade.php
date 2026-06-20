@extends('layouts.master')

@section('page_title', 'Dashboard Super Admin')

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
                            Super Admin
                        </p>

                        <h2 class="mt-2 text-2xl font-black tracking-tight text-white">
                            Dashboard Super Admin
                        </h2>

                        <p class="mt-2 max-w-2xl text-sm leading-relaxed text-[#F4D3B0]">
                            Panel pengawasan pengguna, aktivitas sistem, dan keamanan login Roti Maros Hikmah.
                        </p>
                    </div>

                    <a href="{{ route('super-admin.users.index') }}"
                        class="inline-flex h-12 items-center justify-center rounded-2xl bg-[#F4B044] px-5 text-sm font-black text-[#2B1A10] shadow-lg shadow-[#F4B044]/20 transition hover:-translate-y-0.5 hover:shadow-xl active:scale-95">
                        Kelola Pengguna
                    </a>
                </div>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
            <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                            Total
                        </p>
                        <h3 class="mt-2 text-3xl font-black text-[#2B1A10]">
                            {{ $totalUsers ?? 0 }}
                        </h3>
                        <p class="mt-1 text-sm font-bold text-[#6B3E12]">
                            Pengguna
                        </p>
                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#1F444C]/10 text-[#1F444C]">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m8-4a4 4 0 100-8 4 4 0 000 8zM9 10a4 4 0 100-8 4 4 0 000 8z" />
                        </svg>
                    </div>
                </div>
            </div>

            @php
                $roleCards = [
                    [
                        'label' => 'Super Admin',
                        'value' => $totalSuperAdmins ?? 0,
                        'class' => 'bg-[#F4B044]/20 text-[#6B3E12]',
                    ],
                    [
                        'label' => 'Admin',
                        'value' => $totalAdmins ?? 0,
                        'class' => 'bg-[#1F444C]/10 text-[#1F444C]',
                    ],
                    [
                        'label' => 'Kasir',
                        'value' => $totalCashiers ?? 0,
                        'class' => 'bg-[#F4D3B0]/50 text-[#6B3E12]',
                    ],
                    [
                        'label' => 'Keuangan',
                        'value' => $totalFinance ?? 0,
                        'class' => 'bg-[#A92A35]/10 text-[#A92A35]',
                    ],
                ];
            @endphp

            @foreach ($roleCards as $card)
                <div
                    class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-[#6B3E12]">
                        Role
                    </p>

                    <div class="mt-4 flex items-center justify-between gap-4">
                        <div>
                            <h3 class="text-3xl font-black text-[#2B1A10]">
                                {{ $card['value'] }}
                            </h3>
                            <p class="mt-1 text-sm font-bold text-[#6B3E12]">
                                {{ $card['label'] }}
                            </p>
                        </div>

                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl {{ $card['class'] }}">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 12a4 4 0 100-8 4 4 0 000 8z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 20a8 8 0 0116 0" />
                            </svg>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
@endsection
