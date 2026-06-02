@extends('layouts.masyarakat')

@section('title', 'Profil Saya')

@section('header')
<div class="px-4 py-3.5 bg-white border-b border-stone-200 shrink-0">
    <p class="font-bold text-[1.0625rem] text-stone-900 tracking-tight">Profil Saya</p>
    <p class="text-xs font-light text-stone-500 mt-0.5">Kelola informasi dan foto profil Anda</p>
</div>
@endsection

@section('content')
<div class="p-4 flex flex-col gap-3.5"
     x-data="{
         photoPreview: null,
         editMode: false,
         showPwModal: false,
         showFeedbackModal: false,
         feedbackPreview: null,
         handleFile(e) {
             const file = e.target.files[0];
             if (!file) return;
             const reader = new FileReader();
             reader.onload = ev => this.photoPreview = ev.target.result;
             reader.readAsDataURL(file);
         },
         handleFeedbackFile(e) {
             const file = e.target.files[0];
             if (!file) return;
             const reader = new FileReader();
             reader.onload = ev => this.feedbackPreview = ev.target.result;
             reader.readAsDataURL(file);
         }
     }"
     x-init="if ({{ $errors->has('password') ? 'true' : 'false' }}) showPwModal = true">

    {{-- Non-password errors banner --}}
    @if($errors->any() && !$errors->has('password'))
    <div class="px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm">
        @foreach($errors->all() as $e)
        <div class="flex items-start gap-1.5">
            <svg class="w-3.5 h-3.5 shrink-0 mt-0.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ $e }}
        </div>
        @endforeach
    </div>
    @endif

    {{-- Profile form: avatar file input + telp --}}
    <form method="POST" action="{{ route('masyarakat.profil.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Avatar card --}}
        <div class="bg-gradient-to-br from-orange-500 to-orange-700 rounded-2xl p-6 text-center relative overflow-hidden mb-3.5">
            <div class="absolute inset-0 pointer-events-none opacity-[0.08]"
                 style="background-image:radial-gradient(circle,#fff 1px,transparent 1px);background-size:20px 20px;"></div>
            <div class="relative">
                {{-- Photo: clickable only in edit mode --}}
                <div class="relative inline-block mb-3.5"
                     :class="editMode ? 'cursor-pointer' : 'cursor-default'"
                     @click="if (editMode) $refs.fotoInput.click()">
                    <template x-if="photoPreview">
                        <img :src="photoPreview"
                             class="w-24 h-24 rounded-full object-cover mx-auto ring-4 ring-white/40">
                    </template>
                    <template x-if="!photoPreview">
                        @if($user->foto_profil)
                        <img src="{{ Storage::url($user->foto_profil) }}" alt="{{ $user->nama }}"
                             class="w-24 h-24 rounded-full object-cover mx-auto ring-4 ring-white/40">
                        @else
                        <div class="w-24 h-24 rounded-full bg-white/20 border-2 border-white/40 flex items-center justify-center mx-auto text-white text-[2rem] font-black shadow-[0_4px_16px_rgba(0,0,0,0.2)]">
                            {{ strtoupper(substr($user->nama, 0, 1)) }}
                        </div>
                        @endif
                    </template>
                    {{-- Camera overlay: only visible in edit mode --}}
                    <div x-show="editMode"
                         class="absolute bottom-0 right-0 w-7 h-7 rounded-full bg-white flex items-center justify-center shadow-md border-2 border-orange-200">
                        <svg class="w-3.5 h-3.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                </div>

                <p class="font-bold text-lg text-white tracking-tight">{{ $user->nama }}</p>
                <span class="inline-flex items-center mt-1.5 px-3 py-0.5 rounded-full bg-white/20 border border-white/30 text-[0.6875rem] font-medium text-white/90">
                    Masyarakat
                </span>
                <p class="text-[0.6875rem] text-white/55 mt-1.5">Bergabung {{ $user->created_at->format('d M Y') }}</p>
                <p x-show="editMode" class="text-[0.6875rem] text-white/60 mt-2">Ketuk foto untuk menggantinya</p>
            </div>
        </div>

        <input type="file" name="foto_profil" accept="image/*" class="hidden"
               x-ref="fotoInput" @change="handleFile($event)">
        @error('foto_profil')<p class="text-xs text-red-500 -mt-2 mb-1">{{ $message }}</p>@enderror

        {{-- Statistics Section --}}
        <div class="mx-3.5 mt-4 grid grid-cols-3 gap-3">
            <div class="rounded-xl bg-white border border-stone-100 p-3.5 text-center shadow-sm">
                <p class="text-2xl font-bold text-stone-900">{{ $stats['total'] }}</p>
                <p class="mt-0.5 text-[0.6875rem] text-stone-500">Total Dibuat</p>
            </div>
            <div class="rounded-xl bg-emerald-50 border border-emerald-100 p-3.5 text-center shadow-sm">
                <p class="text-2xl font-bold text-emerald-700">{{ $stats['selesai'] }}</p>
                <p class="mt-0.5 text-[0.6875rem] text-stone-500">Selesai</p>
            </div>
            <div class="rounded-xl bg-blue-50 border border-blue-100 p-3.5 text-center shadow-sm">
                <p class="text-2xl font-bold text-blue-600">{{ $stats['diproses'] }}</p>
                <p class="mt-0.5 text-[0.6875rem] text-stone-500">Sedang Diproses</p>
            </div>
        </div>

        {{-- Info card --}}
        <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-stone-100 mt-3.5">
            {{-- Header with Edit button --}}
            <div class="px-4 py-3 border-b border-stone-100 flex items-center justify-between">
                <p class="text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wider">Informasi Akun</p>
                <button type="button"
                        x-show="!editMode"
                        @click="editMode = true"
                        class="flex items-center gap-1.5 px-3 py-1 rounded-lg bg-orange-50 border border-orange-200 text-orange-600 text-xs font-medium cursor-pointer transition-colors hover:bg-orange-100">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                    Edit Profil
                </button>
            </div>

            {{-- Nama Lengkap — always read-only --}}
            <div class="flex items-center gap-3.5 px-4 py-3.5 border-b border-stone-100">
                <div class="w-9 h-9 rounded-xl bg-stone-100 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[0.6875rem] font-light text-stone-400">Nama Lengkap</p>
                    <p class="text-sm font-semibold text-stone-900 truncate mt-0.5">{{ $user->nama }}</p>
                </div>
            </div>

            {{-- NIK — always read-only --}}
            <div class="flex items-center gap-3.5 px-4 py-3.5 border-b border-stone-100">
                <div class="w-9 h-9 rounded-xl bg-stone-100 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[0.6875rem] font-light text-stone-400">NIK</p>
                    <p class="text-sm font-semibold text-stone-900 truncate mt-0.5 font-mono tracking-wider">
                        {{ $user->nik ? '••••' . substr($user->nik, -4) : '—' }}
                    </p>
                </div>
            </div>

            {{-- Nomor Telepon — read-only view / editable in edit mode --}}
            <div class="px-4 py-3.5 border-b border-stone-100">
                <div class="flex items-center gap-3.5">
                    <div class="w-9 h-9 rounded-xl bg-orange-100 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[0.6875rem] font-light text-stone-400 mb-0.5">Nomor Telepon</p>
                        {{-- View mode --}}
                        <p x-show="!editMode"
                           class="text-sm font-semibold text-stone-900 truncate">{{ $user->telp ?? '—' }}</p>
                        {{-- Edit mode --}}
                        <input x-show="editMode"
                               type="text" name="telp" value="{{ old('telp', $user->telp) }}"
                               placeholder="08xx-xxxx-xxxx"
                               class="w-full text-sm font-semibold text-stone-900 bg-transparent border-0 border-b border-dashed border-stone-200 outline-none pb-0.5 focus:border-orange-400 transition-colors font-sans p-0">
                    </div>
                </div>
                @error('telp')<p class="text-xs text-red-500 mt-1 ml-[3.375rem]">{{ $message }}</p>@enderror
            </div>

            {{-- Email — always read-only --}}
            @if($user->email ?? false)
            <div class="flex items-center gap-3.5 px-4 py-3.5">
                <div class="w-9 h-9 rounded-xl bg-stone-100 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[0.6875rem] font-light text-stone-400">Email</p>
                    <p class="text-sm font-semibold text-stone-900 truncate mt-0.5">{{ $user->email }}</p>
                </div>
            </div>
            @endif
        </div>

        {{-- Edit action buttons — only visible in edit mode --}}
        <div x-show="editMode" class="flex gap-3 mt-0.5">
            <button type="button"
                    @click="editMode = false; photoPreview = null"
                    class="flex-1 py-3.5 rounded-xl bg-stone-100 hover:bg-stone-200 border border-stone-200 text-stone-600 text-[0.9375rem] font-semibold cursor-pointer font-sans flex items-center justify-center gap-2 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Batal
            </button>
            <button type="submit"
                    class="flex-1 py-3.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-[0.9375rem] font-semibold border-0 cursor-pointer font-sans flex items-center justify-center gap-2 transition-all shadow-[0_4px_12px_rgba(234,88,12,0.3)] hover:shadow-[0_6px_16px_rgba(234,88,12,0.35)] hover:-translate-y-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan
            </button>
        </div>

    </form>{{-- END profile form --}}

    {{-- Ganti Password card — button only, no inline fields --}}
    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-stone-100">
        <div class="px-4 py-4 flex items-center gap-3.5">
            <div class="w-9 h-9 rounded-xl bg-stone-100 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-stone-800">Password Akun</p>
                <p class="text-[0.6875rem] font-light text-stone-400 mt-0.5">Ubah password login Anda</p>
            </div>
            <button type="button"
                    @click="showPwModal = true"
                    class="shrink-0 px-4 py-2 rounded-lg bg-stone-100 hover:bg-stone-200 border border-stone-200 text-stone-700 text-xs font-semibold cursor-pointer transition-colors">
                Ganti Password
            </button>
        </div>
    </div>

    {{-- Kirim Feedback card --}}
    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-stone-100">
        <div class="px-4 py-4 flex items-center gap-3.5">
            <div class="w-9 h-9 rounded-xl bg-orange-100 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-stone-800">Kirim Feedback</p>
                <p class="text-[0.6875rem] font-light text-stone-400 mt-0.5">Sampaikan masukan atau saran Anda</p>
            </div>
            <button type="button"
                    @click="showFeedbackModal = true"
                    class="shrink-0 px-4 py-2 rounded-lg bg-orange-100 hover:bg-orange-200 border border-orange-200 text-orange-700 text-xs font-semibold cursor-pointer transition-colors">
                Kirim
            </button>
        </div>
    </div>

    {{-- Logout — must be OUTSIDE the profile form (nested forms are invalid HTML) --}}
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit"
                class="w-full py-3.5 rounded-xl bg-red-50 hover:bg-red-100 border border-red-200 text-red-600 text-[0.9375rem] font-semibold cursor-pointer font-sans flex items-center justify-center gap-2 transition-colors">
            <svg class="w-[1.125rem] h-[1.125rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            Keluar dari Akun
        </button>
    </form>

    <p class="text-center text-[0.6875rem] font-light text-stone-400 pb-2">M-Lapor &middot; Pengaduan Masyarakat</p>

    {{-- Password modal --}}
    <div x-show="showPwModal"
         x-cloak
         class="fixed inset-0 z-50 flex items-end justify-center sm:items-center"
         @keydown.escape.window="showPwModal = false">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
             @click="showPwModal = false"></div>

        {{-- Modal card --}}
        <div class="relative w-full max-w-sm mx-4 mb-4 sm:mb-0 bg-white rounded-2xl shadow-2xl overflow-hidden"
             @click.stop>

            {{-- Modal header --}}
            <div class="px-5 pt-5 pb-4 border-b border-stone-100 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-orange-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                    </div>
                    <p class="text-[0.9375rem] font-bold text-stone-900 tracking-tight">Ganti Password</p>
                </div>
                <button type="button"
                        @click="showPwModal = false"
                        class="w-7 h-7 rounded-lg bg-stone-100 hover:bg-stone-200 flex items-center justify-center cursor-pointer transition-colors">
                    <svg class="w-3.5 h-3.5 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Password form --}}
            <form method="POST" action="{{ route('masyarakat.profil.password') }}">
                @csrf
                @method('PUT')
                <div class="px-5 py-4 flex flex-col gap-4">

                    @if($errors->has('password'))
                    <div class="px-3.5 py-2.5 rounded-xl bg-red-50 border border-red-200 text-red-600 text-xs">
                        @foreach($errors->get('password') as $err)
                        <div class="flex items-start gap-1.5">
                            <svg class="w-3.5 h-3.5 shrink-0 mt-0.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $err }}
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-stone-700 mb-1.5">Password Baru</label>
                        <input type="password" name="password" placeholder="Minimal 6 karakter"
                               @class([
                                   'w-full px-4 py-3 rounded-xl border text-sm text-stone-900 outline-none transition-[border-color,box-shadow] focus:ring-2',
                                   'border-red-300 bg-red-50 focus:border-red-400 focus:ring-red-400/20' => $errors->has('password'),
                                   'border-stone-200 bg-stone-50 focus:border-orange-400 focus:ring-orange-400/20' => !$errors->has('password'),
                               ])>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-stone-700 mb-1.5">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" placeholder="Ulangi password baru"
                               class="w-full px-4 py-3 rounded-xl border border-stone-200 bg-stone-50 text-sm text-stone-900 outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-400/20 transition-[border-color,box-shadow]">
                    </div>
                </div>

                {{-- Modal actions --}}
                <div class="px-5 pb-5 flex gap-3">
                    <button type="button"
                            @click="showPwModal = false"
                            class="flex-1 py-3 rounded-xl bg-stone-100 hover:bg-stone-200 border border-stone-200 text-stone-600 text-sm font-semibold cursor-pointer transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex-1 py-3 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold border-0 cursor-pointer transition-all shadow-[0_4px_12px_rgba(234,88,12,0.3)] hover:shadow-[0_6px_16px_rgba(234,88,12,0.35)]">
                        Simpan Password
                    </button>
                </div>
            </form>

        </div>
    </div>

    {{-- Feedback modal --}}
    <div x-show="showFeedbackModal"
         x-cloak
         class="fixed inset-0 z-50 flex items-end justify-center sm:items-center"
         @keydown.escape.window="showFeedbackModal = false">

        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
             @click="showFeedbackModal = false"></div>

        <div class="relative w-full max-w-sm mx-4 mb-4 sm:mb-0 bg-white rounded-2xl shadow-2xl overflow-hidden"
             @click.stop>

            <div class="px-5 pt-5 pb-4 border-b border-stone-100 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-orange-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                    </div>
                    <p class="text-[0.9375rem] font-bold text-stone-900 tracking-tight">Kirim Feedback</p>
                </div>
                <button type="button"
                        @click="showFeedbackModal = false"
                        class="w-7 h-7 rounded-lg bg-stone-100 hover:bg-stone-200 flex items-center justify-center cursor-pointer transition-colors">
                    <svg class="w-3.5 h-3.5 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('masyarakat.feedback.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="px-5 py-4 flex flex-col gap-4">
                    <div>
                        <label class="block text-sm font-medium text-stone-700 mb-1.5">Isi Feedback</label>
                        <textarea name="isi" rows="4" required minlength="5"
                                  placeholder="Tuliskan masukan, saran, atau keluhan Anda..."
                                  class="w-full px-4 py-3 rounded-xl border border-stone-200 bg-stone-50 text-sm text-stone-900 outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-400/20 transition-[border-color,box-shadow] resize-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-stone-700 mb-1.5">Foto <span class="font-light text-stone-400">(opsional)</span></label>
                        <template x-if="feedbackPreview">
                            <div class="relative mb-2">
                                <img :src="feedbackPreview" class="w-full max-h-36 object-cover rounded-xl border border-stone-100">
                                <button type="button"
                                        @click="feedbackPreview = null; $refs.feedbackFoto.value = ''"
                                        class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-black/50 flex items-center justify-center border-0 cursor-pointer">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </template>
                        <label class="flex items-center gap-2.5 px-4 py-3 rounded-xl border border-dashed border-stone-300 bg-stone-50 cursor-pointer hover:bg-stone-100 transition-colors">
                            <svg class="w-4 h-4 text-stone-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-sm text-stone-500" x-text="feedbackPreview ? 'Ganti foto' : 'Pilih foto'">Pilih foto</span>
                            <input type="file" name="foto" accept="image/*" class="hidden"
                                   x-ref="feedbackFoto" @change="handleFeedbackFile($event)">
                        </label>
                    </div>
                </div>

                <div class="px-5 pb-5 flex gap-3">
                    <button type="button"
                            @click="showFeedbackModal = false"
                            class="flex-1 py-3 rounded-xl bg-stone-100 hover:bg-stone-200 border border-stone-200 text-stone-600 text-sm font-semibold cursor-pointer transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex-1 py-3 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold border-0 cursor-pointer transition-all shadow-[0_4px_12px_rgba(234,88,12,0.3)] hover:shadow-[0_6px_16px_rgba(234,88,12,0.35)]">
                        Kirim Feedback
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
