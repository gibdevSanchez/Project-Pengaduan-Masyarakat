@extends('layouts.auth')

@section('title', 'Masuk — M-Lapor')

@section('form')
<div x-data="{ guard: '{{ old('guard', 'masyarakat') }}', showPwd: false }">

    <h2 class="text-2xl font-bold text-stone-900 mb-1 tracking-tight">Selamat datang</h2>
    <p class="text-sm font-light text-stone-500 mb-7">Masuk untuk melanjutkan ke M-Lapor</p>

    @if($errors->has('username'))
        <div class="flex items-center gap-2 mb-6 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm">
            <svg class="w-4 h-4 shrink-0 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            {{ $errors->first('username') }}
        </div>
    @endif

    {{-- Guard segmented control --}}
    <div class="mb-7">
        <p class="text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-widest mb-2.5">Masuk sebagai</p>

        <div class="relative flex p-1 bg-stone-100 rounded-2xl border border-stone-200">
            {{-- Sliding pill --}}
            <div :class="guard === 'masyarakat'
                    ? 'translate-x-0 shadow-[0_2px_8px_rgba(234,88,12,0.18),0_1px_3px_rgba(0,0,0,0.06)]'
                    : 'translate-x-full shadow-[0_2px_8px_rgba(59,130,246,0.18),0_1px_3px_rgba(0,0,0,0.06)]'"
                 class="absolute top-1 left-1 bottom-1 w-[calc(50%-4px)] bg-white rounded-xl pointer-events-none transition-all duration-300 ease-[cubic-bezier(0.4,0,0.2,1)]">
            </div>

            <button type="button" @click="guard = 'masyarakat'"
                    :class="guard === 'masyarakat' ? 'text-orange-500 font-semibold' : 'text-stone-400 font-medium'"
                    class="flex-1 flex items-center justify-center gap-1.5 py-2.5 px-3 text-[0.8125rem] border-0 bg-transparent rounded-xl cursor-pointer relative z-10 whitespace-nowrap transition-colors duration-200">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Masyarakat
            </button>

            <button type="button" @click="guard = 'petugas'"
                    :class="guard === 'petugas' ? 'text-blue-500 font-semibold' : 'text-stone-400 font-medium'"
                    class="flex-1 flex items-center justify-center gap-1.5 py-2.5 px-3 text-[0.8125rem] border-0 bg-transparent rounded-xl cursor-pointer relative z-10 whitespace-nowrap transition-colors duration-200">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                Petugas / Admin
            </button>
        </div>

        <p x-show="guard === 'masyarakat'" x-cloak class="text-xs font-light text-stone-400 mt-2 pl-1">
            Untuk warga yang ingin membuat atau memantau pengaduan.
        </p>
        <p x-show="guard === 'petugas'" x-cloak class="text-xs font-light text-stone-400 mt-2 pl-1">
            Untuk petugas lapangan dan administrator sistem.
        </p>
    </div>

    <form method="POST" action="/login">
        @csrf
        <input type="hidden" name="guard" :value="guard">

        <div class="flex flex-col gap-4 mb-6">

            {{-- Username --}}
            <div>
                <label class="block text-sm font-medium text-stone-700 mb-1.5">Username</label>
                <input type="text" name="username" value="{{ old('username') }}" required autocomplete="username"
                       placeholder="Masukkan username"
                       @class([
                           'w-full px-4 py-3 rounded-xl border text-sm text-stone-900 outline-none transition-[border-color,box-shadow] focus:ring-[3px]',
                           'border-red-300 bg-red-50 focus:border-red-400 focus:ring-red-400/15' => $errors->has('username'),
                           'border-stone-200 bg-stone-50 focus:border-orange-400 focus:ring-orange-400/15' => !$errors->has('username'),
                       ])>
            </div>

            {{-- Password --}}
            <div>
                <label class="block text-sm font-medium text-stone-700 mb-1.5">Password</label>
                <div class="relative">
                    <input :type="showPwd ? 'text' : 'password'" name="password" required autocomplete="current-password"
                           placeholder="Masukkan password"
                           class="w-full pl-4 pr-11 py-3 rounded-xl border border-stone-200 bg-stone-50 text-stone-900 text-sm outline-none transition-[border-color,box-shadow] focus:border-orange-400 focus:ring-[3px] focus:ring-orange-400/15">
                    <button type="button" @click="showPwd = !showPwd"
                            class="absolute right-3 top-1/2 -translate-y-1/2 p-1 rounded-lg text-stone-400 hover:text-stone-600 hover:bg-stone-100 transition-colors cursor-pointer border-0 bg-transparent">
                        <svg x-show="!showPwd" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="showPwd" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <button type="submit"
                :style="guard === 'masyarakat'
                    ? 'background-image:linear-gradient(135deg,#f97316,#ea580c)'
                    : 'background-image:linear-gradient(135deg,#3b82f6,#2563eb)'"
                class="w-full py-3.5 px-4 rounded-xl bg-orange-500 text-white text-[0.9375rem] font-semibold border-0 cursor-pointer shadow-[0_4px_14px_rgba(234,88,12,0.3)] transition-all duration-150 hover:-translate-y-0.5 hover:shadow-[0_6px_20px_rgba(234,88,12,0.38)] active:translate-y-px">
            Masuk
        </button>
    </form>

    <p x-show="guard === 'masyarakat'" x-cloak class="mt-6 text-center text-sm text-stone-400">
        Belum punya akun?
        <a href="{{ route('register') }}" class="font-semibold text-orange-500 hover:text-orange-600 hover:underline transition-colors">Daftar di sini</a>
    </p>
</div>
@endsection
