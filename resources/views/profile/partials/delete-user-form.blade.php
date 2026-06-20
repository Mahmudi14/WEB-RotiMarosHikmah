<section class="space-y-5">
    <header>
        <div class="flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#A92A35]/10 text-[#A92A35]">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19.5 6h-15m4.5 0V4.5A1.5 1.5 0 0110.5 3h3A1.5 1.5 0 0115 4.5V6m2.25 0l-.75 13.5A1.5 1.5 0 0115 21H9a1.5 1.5 0 01-1.5-1.5L6.75 6" />
                </svg>
            </div>

            <div>
                <h2 class="text-lg font-black text-[#A92A35]">
                    Hapus Akun
                </h2>
                <p class="mt-1 text-sm leading-relaxed text-[#6B3E12]">
                    Setelah akun dihapus, seluruh data akun akan terhapus secara permanen.
                </p>
            </div>
        </div>
    </header>

    <div class="rounded-2xl border border-[#A92A35]/20 bg-[#A92A35]/5 p-4">
        <p class="text-sm leading-relaxed text-[#6B3E12]">
            Tindakan ini tidak dapat dibatalkan. Pastikan data penting sudah diamankan sebelum menghapus akun.
        </p>
    </div>

    <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="inline-flex w-full items-center justify-center rounded-2xl bg-[#A92A35] px-5 py-3 text-sm font-black text-white shadow-lg shadow-[#A92A35]/20 transition hover:-translate-y-0.5 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-[#A92A35]/20">
        Hapus Akun
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <div class="flex items-start gap-4">
                <div
                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#A92A35]/10 text-[#A92A35]">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12V16.5z" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M10.29 3.86L1.82 18a1.5 1.5 0 001.29 2.25h17.78A1.5 1.5 0 0022.18 18L13.71 3.86a1.5 1.5 0 00-2.42 0z" />
                    </svg>
                </div>

                <div>
                    <h2 class="text-xl font-black text-[#2B1A10]">
                        Yakin ingin menghapus akun?
                    </h2>

                    <p class="mt-2 text-sm leading-relaxed text-[#6B3E12]">
                        Masukkan password untuk mengonfirmasi penghapusan akun secara permanen.
                    </p>
                </div>
            </div>

            <div class="mt-6">
                <label for="password" class="sr-only">Password</label>

                <input id="password" name="password" type="password" placeholder="Masukkan password"
                    class="block w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 py-3 text-sm font-medium text-[#2B1A10] shadow-sm transition placeholder:text-[#6B3E12]/50 focus:border-[#A92A35] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#A92A35]/20">

                @foreach ($errors->userDeletion->get('password') as $message)
                    <p class="mt-2 text-sm font-medium text-[#A92A35]">{{ $message }}</p>
                @endforeach
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')"
                    class="inline-flex items-center justify-center rounded-2xl border border-[#F4D3B0] bg-white px-5 py-3 text-sm font-black text-[#2B1A10] transition hover:bg-[#F7F6F4] focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">
                    Batal
                </button>

                <button type="submit"
                    class="inline-flex items-center justify-center rounded-2xl bg-[#A92A35] px-5 py-3 text-sm font-black text-white shadow-lg shadow-[#A92A35]/20 transition hover:-translate-y-0.5 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-[#A92A35]/20">
                    Hapus Akun
                </button>
            </div>
        </form>
    </x-modal>
</section>
