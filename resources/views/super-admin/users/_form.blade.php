@php
    $isEdit = isset($user);
@endphp

<form method="POST" action="{{ $action }}" class="space-y-5" x-data="{
    showPassword: false,
    showPasswordConfirmation: false,
    resetPassword: @js((bool) old('reset_password', false)),
    roleOpen: false,
    selectedRole: @js(old('role', $user->role ?? '')),
    roles: @js($roles),
    get selectedRoleLabel() {
        return this.selectedRole ? this.roles[this.selectedRole] : 'Pilih role pengguna'
    }
}">
    @csrf

    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-5 lg:grid-cols-2">
        {{-- Name --}}
        <div>
            <label for="name" class="text-sm font-bold text-[#2B1A10]">
                Nama
            </label>

            <input id="name" name="name" type="text" value="{{ old('name', $user->name ?? '') }}" required
                autofocus placeholder="Masukkan nama pengguna"
                class="mt-2 block w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-3 text-sm font-medium text-[#2B1A10] shadow-sm transition placeholder:text-[#6B3E12]/45 focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">

            @error('name')
                <p class="mt-2 text-sm font-semibold text-[#A92A35]">
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="text-sm font-bold text-[#2B1A10]">
                Email
            </label>

            <input id="email" name="email" type="email" value="{{ old('email', $user->email ?? '') }}" required
                placeholder="Masukkan email pengguna"
                class="mt-2 block w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-3 text-sm font-medium text-[#2B1A10] shadow-sm transition placeholder:text-[#6B3E12]/45 focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">

            @error('email')
                <p class="mt-2 text-sm font-semibold text-[#A92A35]">
                    {{ $message }}
                </p>
            @enderror
        </div>
    </div>

    {{-- Role --}}
    <div>
        <label class="text-sm font-bold text-[#2B1A10]">
            Role
        </label>

        <input type="hidden" name="role" x-model="selectedRole">

        <div class="relative mt-2">
            {{-- Trigger --}}
            <button type="button" @click="roleOpen = !roleOpen"
                class="flex h-12 w-full items-center justify-between rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-0 text-left text-sm font-bold text-[#2B1A10] shadow-sm transition hover:bg-white focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">

                <div class="flex min-w-0 items-center gap-3">
                    <span
                        class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-[#F4B044]/20 text-[#6B3E12]">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 20.25a8.25 8.25 0 0115 0" />
                        </svg>
                    </span>

                    <span x-text="selectedRoleLabel" class="truncate"
                        :class="selectedRole ? 'text-[#2B1A10]' : 'text-[#6B3E12]/60'">
                    </span>
                </div>

                <svg class="h-5 w-5 shrink-0 text-[#6B3E12] transition duration-200"
                    :class="roleOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.4"
                    viewBox="0 0 24 24">
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
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M4.5 12a7.5 7.5 0 1115 0v5.25A2.25 2.25 0 0117.25 19.5H6.75a2.25 2.25 0 01-2.25-2.25V12z" />
                                        </svg>
                                    @elseif ($value === 'kasir')
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.25 8.25h19.5M3.75 8.25v9A2.25 2.25 0 006 19.5h12a2.25 2.25 0 002.25-2.25v-9M6.75 12h.008v.008H6.75V12zm3 0h.008v.008H9.75V12zm3 0h.008v.008h-.008V12z" />
                                        </svg>
                                    @else
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3.75 6.75A2.25 2.25 0 016 4.5h12a2.25 2.25 0 012.25 2.25v10.5A2.25 2.25 0 0118 19.5H6a2.25 2.25 0 01-2.25-2.25V6.75z" />
                                        </svg>
                                    @endif
                                </span>

                                <div>
                                    <p>{{ $label }}</p>
                                    <p class="mt-0.5 text-xs font-medium opacity-70">
                                        @if ($value === 'admin')
                                            Mengelola data operasional
                                        @elseif ($value === 'kasir')
                                            Mengelola transaksi penjualan
                                        @else
                                            Mengelola laporan keuangan
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <svg x-show="selectedRole === '{{ $value }}'" x-cloak class="h-5 w-5" fill="none"
                                stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        @error('role')
            <p class="mt-2 text-sm font-semibold text-[#A92A35]">
                {{ $message }}
            </p>
        @enderror
    </div>

    @if ($isEdit)
        <div class="space-y-3">
            <div class="rounded-2xl border border-[#F4B044]/30 bg-[#F4B044]/10 px-4 py-3">
                <p class="text-sm font-semibold text-[#6B3E12]">
                    Kosongkan password jika tidak ingin mengubah password pengguna.
                </p>
            </div>

            <label
                class="flex cursor-pointer items-start gap-3 rounded-2xl border border-[#A92A35]/20 bg-[#A92A35]/5 px-4 py-4 transition hover:bg-[#A92A35]/10">
                <input type="hidden" name="reset_password" value="0">

                <input type="checkbox" name="reset_password" value="1" x-model="resetPassword"
                    class="mt-1 h-4 w-4 rounded border-[#F4D3B0] text-[#A92A35] focus:ring-[#A92A35]">

                <div>
                    <p class="text-sm font-black text-[#A92A35]">
                        Reset password ke default
                    </p>
                    <p class="mt-1 text-sm leading-relaxed text-[#6B3E12]">
                        Jika dicentang, password pengguna akan diubah menjadi
                        <span class="font-black text-[#2B1A10]">roti12345</span>.
                    </p>
                </div>
            </label>
        </div>
    @endif

    <div class="grid gap-5 lg:grid-cols-2">
        {{-- Password --}}
        <div>
            <label for="password" class="text-sm font-bold text-[#2B1A10]">
                {{ $isEdit ? 'Password Baru' : 'Password' }}
            </label>

            <div class="relative mt-2">
                <input id="password" name="password" :type="showPassword ? 'text' : 'password'"
                    :disabled="resetPassword" @if (!$isEdit) required @endif
                    placeholder="{{ $isEdit ? 'Opsional' : 'Minimal 8 karakter' }}"
                    class="block w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-3 pr-12 text-sm font-medium text-[#2B1A10] shadow-sm transition placeholder:text-[#6B3E12]/45 focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20 disabled:cursor-not-allowed disabled:opacity-60">

                <button type="button"
                    class="absolute inset-y-0 right-4 flex items-center text-[#6B3E12] transition hover:text-[#1F444C]"
                    @click="showPassword = !showPassword">
                    <svg x-show="!showPassword" x-cloak class="h-5 w-5" fill="none" stroke="currentColor"
                        stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>

                    <svg x-show="showPassword" x-cloak class="h-5 w-5" fill="none" stroke="currentColor"
                        stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.88 4.24A10.43 10.43 0 0112 4c6 0 9.75 8 9.75 8a18.1 18.1 0 01-3.22 4.31" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6.61 6.61A18.3 18.3 0 002.25 12S6 20 12 20a10.4 10.4 0 004.39-.96" />
                    </svg>
                </button>
            </div>

            @error('password')
                <p class="mt-2 text-sm font-semibold text-[#A92A35]">
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Password Confirmation --}}
        <div>
            <label for="password_confirmation" class="text-sm font-bold text-[#2B1A10]">
                {{ $isEdit ? 'Konfirmasi Password Baru' : 'Konfirmasi Password' }}
            </label>

            <div class="relative mt-2">
                <input id="password_confirmation" name="password_confirmation"
                    :type="showPasswordConfirmation ? 'text' : 'password'"
                    @if (!$isEdit) required @endif
                    placeholder="{{ $isEdit ? 'Opsional' : 'Ulangi password' }}"
                    class="block w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-3 pr-12 text-sm font-medium text-[#2B1A10] shadow-sm transition placeholder:text-[#6B3E12]/45 focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">

                <button type="button"
                    class="absolute inset-y-0 right-4 flex items-center text-[#6B3E12] transition hover:text-[#1F444C]"
                    @click="showPasswordConfirmation = !showPasswordConfirmation">
                    <svg x-show="!showPasswordConfirmation" x-cloak class="h-5 w-5" fill="none"
                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>

                    <svg x-show="showPasswordConfirmation" x-cloak class="h-5 w-5" fill="none"
                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.88 4.24A10.43 10.43 0 0112 4c6 0 9.75 8 9.75 8a18.1 18.1 0 01-3.22 4.31" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6.61 6.61A18.3 18.3 0 002.25 12S6 20 12 20a10.4 10.4 0 004.39-.96" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div class="flex flex-col gap-3 pt-3 sm:flex-row sm:items-center sm:justify-end">
        <a href="{{ route('super-admin.users.index') }}"
            class="inline-flex items-center justify-center rounded-2xl border border-[#F4D3B0] bg-white px-5 py-3 text-sm font-black text-[#6B3E12] transition hover:bg-[#F7F6F4]">
            Batal
        </a>

        <button type="submit"
            class="inline-flex items-center justify-center rounded-2xl bg-[#1F444C] px-5 py-3 text-sm font-black text-white shadow-lg shadow-[#1F444C]/20 transition hover:-translate-y-0.5 hover:shadow-xl">
            {{ $buttonText }}
        </button>
    </div>
</form>
