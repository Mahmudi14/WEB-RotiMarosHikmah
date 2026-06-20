@extends('layouts.master')

@section('page_title', 'Manajemen Pengguna')

@section('content')
    <div class="space-y-6" x-data="{
        deleteModalOpen: false,
        deleteAction: '',
        deleteUserName: '',
    
        openDeleteModal(action, name) {
            this.deleteAction = action;
            this.deleteUserName = name;
            this.deleteModalOpen = true;
        },
    
        closeDeleteModal() {
            this.deleteModalOpen = false;
            this.deleteAction = '';
            this.deleteUserName = '';
        }
    }" @keydown.escape.window="closeDeleteModal()">
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
                            Manajemen Pengguna
                        </h2>
                        <p class="mt-2 max-w-2xl text-sm leading-relaxed text-[#F4D3B0]">
                            Kelola akun admin, kasir, dan keuangan untuk sistem kasir Roti Maros Hikmah.
                        </p>
                    </div>

                    <a href="{{ route('super-admin.users.create') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-[#F4B044] px-5 py-3 text-sm font-black text-[#2B1A10] shadow-lg shadow-[#F4B044]/20 transition hover:-translate-y-0.5 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-[#F4B044]/25">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Tambah Pengguna
                    </a>
                </div>
            </div>
        </div>

        {{-- Alert --}}
        @if (session('success'))
            <div class="rounded-2xl border border-[#1F444C]/20 bg-[#1F444C]/10 px-5 py-4 text-sm font-bold text-[#1F444C]">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->has('user'))
            <div class="rounded-2xl border border-[#A92A35]/20 bg-[#A92A35]/10 px-5 py-4 text-sm font-bold text-[#A92A35]">
                {{ $errors->first('user') }}
            </div>
        @endif

        {{-- Filter --}}
        <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]"
            x-data="{
                roleOpen: false,
                selectedRole: @js(request('role', '')),
                roles: @js($roles),
                get selectedRoleLabel() {
                    return this.selectedRole ? this.roles[this.selectedRole] : 'Semua Role'
                }
            }">
            <form method="GET" action="{{ route('super-admin.users.index') }}"
                class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_230px_auto] lg:items-center xl:grid-cols-[minmax(0,1fr)_310px_auto]">

                {{-- Search --}}
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[#6B3E12]/60">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-4.35-4.35m1.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>

                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama atau email pengguna..."
                        class="block h-12 w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] py-0 pl-12 pr-4 text-sm font-medium text-[#2B1A10] shadow-sm transition placeholder:text-[#6B3E12]/45 focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">
                </div>

                {{-- Custom Role Dropdown --}}
                <div>
                    <input type="hidden" name="role" x-model="selectedRole">

                    <div class="relative">
                        <button type="button" @click="roleOpen = !roleOpen"
                            class="flex h-12 w-full items-center justify-between rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-0 text-left text-sm font-bold text-[#2B1A10] shadow-sm transition hover:bg-white focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">

                            <div class="flex items-center gap-3">
                                <span
                                    class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-[#F4B044]/20 text-[#6B3E12]">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4.5 20.25a8.25 8.25 0 0115 0" />
                                    </svg>
                                </span>

                                <span x-text="selectedRoleLabel" class="truncate"
                                    :class="selectedRole ? 'text-[#2B1A10]' : 'text-[#6B3E12]/60'">
                                </span>
                            </div>

                            <svg class="h-5 w-5 text-[#6B3E12] transition duration-200"
                                :class="roleOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                stroke-width="2.4" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        {{-- Dropdown --}}
                        <div x-show="roleOpen" x-cloak @click.outside="roleOpen = false"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                            class="absolute z-40 mt-3 w-full overflow-hidden rounded-2xl border border-[#F4D3B0]/80 bg-white shadow-[0_25px_70px_-35px_rgba(31,68,76,0.55)]">

                            <div class="p-2">
                                {{-- Semua Role --}}
                                <button type="button" @click="selectedRole = ''; roleOpen = false"
                                    class="group flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-sm font-bold transition"
                                    :class="selectedRole === ''
                                        ?
                                        'bg-[#F4B044] text-[#2B1A10]' :
                                        'text-[#2B1A10] hover:bg-[#F4B044]/15'">

                                    <div class="flex items-center gap-3">
                                        <span class="flex h-9 w-9 items-center justify-center rounded-xl transition"
                                            :class="selectedRole === ''
                                                ?
                                                'bg-[#2B1A10]/10' :
                                                'bg-[#F4B044]/15 text-[#6B3E12] group-hover:bg-[#F4B044]/25'">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M4 6h16M4 12h16M4 18h16" />
                                            </svg>
                                        </span>

                                        <div>
                                            <p>Semua Role</p>
                                            <p class="mt-0.5 text-xs font-medium opacity-70">
                                                Tampilkan semua pengguna
                                            </p>
                                        </div>
                                    </div>

                                    <svg x-show="selectedRole === ''" x-cloak class="h-5 w-5" fill="none"
                                        stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>

                                @foreach ($roles as $value => $label)
                                    <button type="button" @click="selectedRole = '{{ $value }}'; roleOpen = false"
                                        class="group flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-sm font-bold transition"
                                        :class="selectedRole === '{{ $value }}'
                                            ?
                                            'bg-[#F4B044] text-[#2B1A10]' :
                                            'text-[#2B1A10] hover:bg-[#F4B044]/15'">

                                        <div class="flex items-center gap-3">
                                            <span class="flex h-9 w-9 items-center justify-center rounded-xl transition"
                                                :class="selectedRole === '{{ $value }}'
                                                    ?
                                                    'bg-[#2B1A10]/10' :
                                                    'bg-[#F4B044]/15 text-[#6B3E12] group-hover:bg-[#F4B044]/25'">

                                                @if ($value === 'admin')
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                        stroke-width="2.2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M4.5 12a7.5 7.5 0 1115 0v5.25A2.25 2.25 0 0117.25 19.5H6.75a2.25 2.25 0 01-2.25-2.25V12z" />
                                                    </svg>
                                                @elseif ($value === 'kasir')
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                        stroke-width="2.2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M2.25 8.25h19.5M3.75 8.25v9A2.25 2.25 0 006 19.5h12a2.25 2.25 0 002.25-2.25v-9M6.75 12h.008v.008H6.75V12zm3 0h.008v.008H9.75V12zm3 0h.008v.008h-.008V12z" />
                                                    </svg>
                                                @else
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                        stroke-width="2.2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M3.75 6.75A2.25 2.25 0 016 4.5h12a2.25 2.25 0 012.25 2.25v10.5A2.25 2.25 0 0118 19.5H6a2.25 2.25 0 01-2.25-2.25V6.75z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M8.25 12h7.5M12 8.25v7.5" />
                                                    </svg>
                                                @endif
                                            </span>

                                            <div>
                                                <p>{{ $label }}</p>
                                                <p class="mt-0.5 text-xs font-medium opacity-70">
                                                    @if ($value === 'admin')
                                                        Filter pengguna admin
                                                    @elseif ($value === 'kasir')
                                                        Filter pengguna kasir
                                                    @else
                                                        Filter pengguna keuangan
                                                    @endif
                                                </p>
                                            </div>
                                        </div>

                                        <svg x-show="selectedRole === '{{ $value }}'" x-cloak class="h-5 w-5"
                                            fill="none" stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action --}}
                <div class="flex shrink-0 gap-3">
                    <button type="submit"
                        class="inline-flex h-12 shrink-0 items-center justify-center gap-2 rounded-2xl bg-[#1F444C] px-5 py-0 text-sm font-black text-white shadow-lg shadow-[#1F444C]/20 transition hover:-translate-y-0.5 hover:shadow-xl">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.3"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-4.35-4.35m1.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Cari
                    </button>

                    <a href="{{ route('super-admin.users.index') }}"
                        class="inline-flex h-12 shrink-0 items-center justify-center gap-2 rounded-2xl border border-[#F4D3B0] bg-white px-5 py-0 text-sm font-black text-[#6B3E12] transition hover:bg-[#F7F6F4]">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.3"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0013.803-3.7M7.977 14.652H2.985m18.03-5.304-3.181-3.183a8.25 8.25 0 00-13.803 3.7" />
                        </svg>
                        Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div
            class="overflow-hidden rounded-3xl border border-[#F4D3B0]/70 bg-white shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[#F4D3B0]/70">
                    <thead class="bg-[#F7F6F4]">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                                Pengguna
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                                Role
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                                Dibuat
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-[#F4D3B0]/60 bg-white">
                        @forelse ($users as $user)
                            @php
                                $roleLabel = match ($user->role) {
                                    'admin' => 'Admin',
                                    'kasir' => 'Kasir',
                                    'keuangan' => 'Keuangan',
                                    default => ucfirst(str_replace('_', ' ', $user->role)),
                                };

                                $roleClass = match ($user->role) {
                                    'admin' => 'bg-[#1F444C]/10 text-[#1F444C]',
                                    'kasir' => 'bg-[#F4B044]/20 text-[#6B3E12]',
                                    'keuangan' => 'bg-[#A92A35]/10 text-[#A92A35]',
                                    default => 'bg-gray-100 text-gray-700',
                                };
                            @endphp

                            <tr class="transition hover:bg-[#F7F6F4]/70">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-[#F4B044] text-sm font-black text-[#2B1A10] shadow-sm">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>

                                        <div>
                                            <p class="font-black text-[#2B1A10]">
                                                {{ $user->name }}
                                            </p>
                                            <p class="mt-0.5 text-sm font-medium text-[#6B3E12]">
                                                {{ $user->email }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex rounded-full px-3 py-1 text-xs font-black {{ $roleClass }}">
                                        {{ $roleLabel }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-sm font-semibold text-[#6B3E12]">
                                    {{ $user->created_at?->format('d M Y') }}
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('super-admin.users.edit', $user) }}"
                                            class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-[#1F444C]/10 text-[#1F444C] transition hover:bg-[#1F444C] hover:text-white">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M16.862 4.487l1.651-1.651a1.875 1.875 0 112.651 2.651L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                                            </svg>
                                        </a>

                                        <button type="button"
                                            @click="openDeleteModal(@js(route('super-admin.users.destroy', $user)), @js($user->name))"
                                            class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-[#A92A35]/10 text-[#A92A35] transition hover:bg-[#A92A35] hover:text-white">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166M19.228 5.79L18.16 19.673A2.25 2.25 0 0115.916 21.75H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .397c.34-.059.68-.114 1.022-.166m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916A2.25 2.25 0 0013.5 2.25h-3A2.25 2.25 0 008.25 4.5v.916" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-14 text-center">
                                    <div class="mx-auto flex max-w-sm flex-col items-center">
                                        <div
                                            class="flex h-16 w-16 items-center justify-center rounded-3xl bg-[#F4B044]/20 text-[#6B3E12]">
                                            <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m-7.5 3.198a4.5 4.5 0 01-7.5 0A3 3 0 016.34 15.52m7.5 3.198a4.5 4.5 0 007.5 0M9 11.25a3 3 0 100-6 3 3 0 000 6zm6 0a3 3 0 100-6 3 3 0 000 6z" />
                                            </svg>
                                        </div>
                                        <h3 class="mt-4 text-lg font-black text-[#2B1A10]">
                                            Belum ada pengguna
                                        </h3>
                                        <p class="mt-1 text-sm text-[#6B3E12]">
                                            Tambahkan admin, kasir, atau keuangan untuk mulai mengatur akses sistem.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="border-t border-[#F4D3B0]/70 bg-[#F7F6F4] px-6 py-4">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

        {{-- Delete Confirmation Modal --}}
        <template x-teleport="body">
            <div x-show="deleteModalOpen" x-cloak
                class="fixed inset-0 z-[9999] flex items-center justify-center p-4 sm:p-6" aria-modal="true"
                role="dialog">

                {{-- Overlay --}}
                <div x-show="deleteModalOpen" x-transition.opacity
                    class="absolute inset-0 bg-[#1F444C]/55 backdrop-blur-md" @click="closeDeleteModal()">
                </div>

                {{-- Modal Card --}}
                <div x-show="deleteModalOpen" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                    class="relative z-10 w-full max-w-md overflow-hidden rounded-[1.75rem] border border-[#F4D3B0]/70 bg-white shadow-[0_35px_90px_-35px_rgba(31,68,76,0.65)]">

                    {{-- Header --}}
                    <div class="relative overflow-hidden bg-[#A92A35] px-6 py-6 text-white">
                        <div class="absolute -right-10 -top-12 h-32 w-32 rounded-full bg-white/10"></div>
                        <div class="absolute -bottom-16 -left-12 h-36 w-36 rounded-full bg-black/10"></div>

                        <div class="relative flex items-start gap-4">
                            <div
                                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white/15 text-white ring-1 ring-white/20">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.4"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v3.75m0 3.75h.008v.008H12V16.5z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M10.29 3.86L1.82 18a1.5 1.5 0 001.29 2.25h17.78A1.5 1.5 0 0022.18 18L13.71 3.86a1.5 1.5 0 00-2.42 0z" />
                                </svg>
                            </div>

                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.24em] text-white/75">
                                    Konfirmasi Hapus
                                </p>
                                <h3 class="mt-1 text-xl font-black text-white">
                                    Hapus Pengguna?
                                </h3>
                            </div>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="px-6 py-5">
                        <p class="text-sm leading-relaxed text-[#6B3E12]">
                            Kamu akan menghapus pengguna
                            <span class="font-black text-[#2B1A10]" x-text="deleteUserName"></span>.
                            Tindakan ini tidak dapat dibatalkan.
                        </p>

                        <div class="mt-5 rounded-2xl border border-[#A92A35]/20 bg-[#A92A35]/5 px-4 py-3">
                            <p class="text-sm font-semibold text-[#A92A35]">
                                Pastikan data pengguna ini memang sudah tidak dibutuhkan.
                            </p>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div
                        class="flex flex-col-reverse gap-3 border-t border-[#F4D3B0]/70 bg-[#F7F6F4] px-6 py-5 sm:flex-row sm:justify-end">
                        <button type="button" @click="closeDeleteModal()"
                            class="inline-flex items-center justify-center rounded-2xl border border-[#F4D3B0] bg-white px-5 py-3 text-sm font-black text-[#6B3E12] transition hover:bg-[#F7F6F4] focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">
                            Batal
                        </button>

                        <form method="POST" :action="deleteAction">
                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-[#A92A35] px-5 py-3 text-sm font-black text-white shadow-lg shadow-[#A92A35]/20 transition hover:-translate-y-0.5 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-[#A92A35]/20">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.3"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M14.74 9l-.346 9m-4.788 0L9.26 9" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 6h-15m4.5 0V4.5A1.5 1.5 0 0110.5 3h3A1.5 1.5 0 0115 4.5V6m2.25 0l-.75 13.5A1.5 1.5 0 0115 21H9a1.5 1.5 0 01-1.5-1.5L6.75 6" />
                                </svg>
                                Ya, Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </div>
@endsection
