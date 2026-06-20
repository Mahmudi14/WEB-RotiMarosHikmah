<form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
    @csrf
    @method('PATCH')

    <div>
        <p class="text-xs font-black uppercase tracking-[0.22em] text-[#6B3E12]">
            Informasi Akun
        </p>
        <h3 class="mt-1 text-lg font-black text-[#2B1A10]">
            Data Profil
        </h3>
        <p class="mt-2 text-sm font-semibold leading-relaxed text-[#6B3E12]">
            Perbarui nama dan email akun yang digunakan untuk masuk ke sistem.
        </p>
    </div>

    <div>
        <label for="name" class="mb-2 block text-sm font-black text-[#2B1A10]">
            Nama Lengkap
        </label>

        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}"
            class="h-12 w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 text-sm font-bold text-[#2B1A10] outline-none transition focus:border-[#F4B044] focus:bg-white focus:ring-4 focus:ring-[#F4B044]/20">

        @error('name')
            <p class="mt-2 text-sm font-semibold text-[#A92A35]">
                {{ $message }}
            </p>
        @enderror
    </div>

    <div>
        <label for="email" class="mb-2 block text-sm font-black text-[#2B1A10]">
            Email
        </label>

        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}"
            class="h-12 w-full rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 text-sm font-bold text-[#2B1A10] outline-none transition focus:border-[#F4B044] focus:bg-white focus:ring-4 focus:ring-[#F4B044]/20">

        @error('email')
            <p class="mt-2 text-sm font-semibold text-[#A92A35]">
                {{ $message }}
            </p>
        @enderror
    </div>

    <div>
        <label class="mb-2 block text-sm font-black text-[#2B1A10]">
            Role
        </label>

        <div class="flex h-12 items-center rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4">
            <span class="text-sm font-black capitalize text-[#6B3E12]">
                {{ str_replace('_', ' ', $user->role) }}
            </span>
        </div>

        <p class="mt-2 text-xs font-semibold text-[#6B3E12]">
            Role hanya dapat diubah melalui Manajemen Pengguna.
        </p>
    </div>

    <button type="submit"
        class="inline-flex h-12 w-full items-center justify-center rounded-2xl bg-[#1F444C] px-5 text-sm font-black text-white shadow-lg shadow-[#1F444C]/20 transition hover:-translate-y-0.5 hover:shadow-xl active:scale-95">
        Simpan Perubahan Profil
    </button>
</form>
