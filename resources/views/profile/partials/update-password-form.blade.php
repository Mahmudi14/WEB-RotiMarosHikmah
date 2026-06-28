<section>
    <header>
        <div class="flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#1F444C]/10 text-[#1F444C]">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 00-9 0v3.75" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6.75 10.5h10.5A1.5 1.5 0 0118.75 12v6.75a1.5 1.5 0 01-1.5 1.5H6.75a1.5 1.5 0 01-1.5-1.5V12a1.5 1.5 0 011.5-1.5z" />
                </svg>
            </div>

            <div>
                <h2 class="text-lg font-black text-[#2B1A10]">
                    Ubah Password
                </h2>
                <p class="mt-1 text-sm text-[#6B3E12]">
                    Gunakan password yang kuat untuk menjaga keamanan akun.
                </p>
            </div>
        </div>
    </header>

    <form method="post" action="{{ route('profile.password.update') }}" class="mt-6 space-y-5" x-data="{
        showCurrentPassword: false,
        showNewPassword: false,
        showConfirmPassword: false,
        isSubmitting: false
    }"
        @submit="
    if (isSubmitting) {
        $event.preventDefault();
        return;
    }

    isSubmitting = true;
">
        @csrf
        @method('put')

        {{-- Password Saat Ini --}}
        <div>
            <label for="update_password_current_password" class="text-sm font-bold text-[#2B1A10]">
                Password Saat Ini
            </label>

            <div class="relative mt-2">
                <input id="update_password_current_password" name="current_password"
                    :type="showCurrentPassword ? 'text' : 'password'" autocomplete="current-password"
                    class="block w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-3 pr-12 text-sm font-medium text-[#2B1A10] shadow-sm transition focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">

                <button type="button"
                    class="absolute inset-y-0 right-3 flex items-center text-[#6B3E12] transition hover:text-[#1F444C]"
                    @click="showCurrentPassword = !showCurrentPassword">

                    {{-- Icon mata tertutup --}}
                    <svg x-show="!showCurrentPassword" x-cloak class="h-5 w-5" fill="none" stroke="currentColor"
                        stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>

                    {{-- Icon mata terbuka/disembunyikan --}}
                    <svg x-show="showCurrentPassword" x-cloak class="h-5 w-5" fill="none" stroke="currentColor"
                        stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.58 10.58a2 2 0 002.84 2.84" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.88 4.24A10.43 10.43 0 0112 4c6 0 9.75 8 9.75 8a18.1 18.1 0 01-3.22 4.31" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6.61 6.61A18.3 18.3 0 002.25 12S6 20 12 20a10.4 10.4 0 004.39-.96" />
                    </svg>
                </button>
            </div>

            @foreach ($errors->updatePassword->get('current_password') as $message)
                <p class="mt-2 text-sm font-medium text-[#A92A35]">{{ $message }}</p>
            @endforeach
        </div>

        {{-- Password Baru --}}
        <div>
            <label for="update_password_password" class="text-sm font-bold text-[#2B1A10]">
                Password Baru
            </label>

            <div class="relative mt-2">
                <input id="update_password_password" name="password" :type="showNewPassword ? 'text' : 'password'"
                    autocomplete="new-password"
                    class="block w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-3 pr-12 text-sm font-medium text-[#2B1A10] shadow-sm transition focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">

                <button type="button"
                    class="absolute inset-y-0 right-3 flex items-center text-[#6B3E12] transition hover:text-[#1F444C]"
                    @click="showNewPassword = !showNewPassword">

                    <svg x-show="!showNewPassword" x-cloak class="h-5 w-5" fill="none" stroke="currentColor"
                        stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>

                    <svg x-show="showNewPassword" x-cloak class="h-5 w-5" fill="none" stroke="currentColor"
                        stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.58 10.58a2 2 0 002.84 2.84" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.88 4.24A10.43 10.43 0 0112 4c6 0 9.75 8 9.75 8a18.1 18.1 0 01-3.22 4.31" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6.61 6.61A18.3 18.3 0 002.25 12S6 20 12 20a10.4 10.4 0 004.39-.96" />
                    </svg>
                </button>
            </div>

            @foreach ($errors->updatePassword->get('password') as $message)
                <p class="mt-2 text-sm font-medium text-[#A92A35]">{{ $message }}</p>
            @endforeach
        </div>

        {{-- Konfirmasi Password Baru --}}
        <div>
            <label for="update_password_password_confirmation" class="text-sm font-bold text-[#2B1A10]">
                Konfirmasi Password Baru
            </label>

            <div class="relative mt-2">
                <input id="update_password_password_confirmation" name="password_confirmation"
                    :type="showConfirmPassword ? 'text' : 'password'" autocomplete="new-password"
                    class="block w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-3 pr-12 text-sm font-medium text-[#2B1A10] shadow-sm transition focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">

                <button type="button"
                    class="absolute inset-y-0 right-3 flex items-center text-[#6B3E12] transition hover:text-[#1F444C]"
                    @click="showConfirmPassword = !showConfirmPassword">

                    <svg x-show="!showConfirmPassword" x-cloak class="h-5 w-5" fill="none" stroke="currentColor"
                        stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>

                    <svg x-show="showConfirmPassword" x-cloak class="h-5 w-5" fill="none" stroke="currentColor"
                        stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.58 10.58a2 2 0 002.84 2.84" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.88 4.24A10.43 10.43 0 0112 4c6 0 9.75 8 9.75 8a18.1 18.1 0 01-3.22 4.31" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6.61 6.61A18.3 18.3 0 002.25 12S6 20 12 20a10.4 10.4 0 004.39-.96" />
                    </svg>
                </button>
            </div>

            @foreach ($errors->updatePassword->get('password_confirmation') as $message)
                <p class="mt-2 text-sm font-medium text-[#A92A35]">{{ $message }}</p>
            @endforeach
        </div>

        <div class="space-y-4 pt-2">
            <button type="submit" :disabled="isSubmitting"
                class="inline-flex h-12 w-full items-center justify-center gap-2 rounded-2xl bg-[#1F444C] px-5 text-sm font-black text-white shadow-lg shadow-[#1F444C]/20 transition hover:-translate-y-0.5 hover:shadow-xl active:scale-95 disabled:cursor-not-allowed disabled:opacity-70 disabled:hover:translate-y-0 disabled:active:scale-100 focus:outline-none focus:ring-4 focus:ring-[#1F444C]/20">

                <svg x-show="isSubmitting" x-cloak class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                    </path>
                </svg>

                <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Perubahan Password'"></span>
            </button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)"
                    class="inline-flex items-center gap-2 rounded-2xl border border-[#1F444C]/15 bg-[#1F444C]/10 px-4 py-3 text-sm font-black text-[#1F444C]">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>

                    Password berhasil diperbarui.
                </p>
            @endif
        </div>
    </form>
</section>
