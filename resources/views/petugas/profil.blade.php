@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="flex-1 overflow-y-auto p-6 bg-stone-50">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('petugas.dashboard') }}"
           class="flex items-center justify-center w-9 h-9 rounded-xl border border-stone-200 bg-white text-stone-500 no-underline hover:border-orange-300 hover:text-orange-500 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-stone-900 tracking-tight">Profil Saya</h1>
            <p class="text-sm font-light text-stone-500 mt-0.5">Kelola informasi dan foto profil Anda</p>
        </div>
    </div>

    <div class="max-w-[32rem]">
        <form method="POST" action="{{ route('petugas.profil.update') }}" enctype="multipart/form-data"
              x-data="{
                  photoPreview: null,
                  handleFile(e) {
                      const file = e.target.files[0];
                      if (!file) return;
                      const reader = new FileReader();
                      reader.onload = ev => this.photoPreview = ev.target.result;
                      reader.readAsDataURL(file);
                  }
              }">
            @csrf
            @method('PUT')

            @if($errors->any())
            <div class="mb-5 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm">
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

            {{-- Foto profil --}}
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-stone-100 mb-4">
                <p class="text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wider mb-4">Foto Profil</p>

                <div class="flex items-center gap-5">
                    {{-- Avatar preview --}}
                    <div class="relative shrink-0 cursor-pointer" @click="$refs.fotoInput.click()">
                        <template x-if="photoPreview">
                            <img :src="photoPreview"
                                 class="w-20 h-20 rounded-full object-cover ring-2 ring-orange-200 ring-offset-2">
                        </template>
                        <template x-if="!photoPreview">
                            @if($petugas->foto_profil)
                            <img src="{{ Storage::url($petugas->foto_profil) }}" alt="{{ $petugas->nama_petugas }}"
                                 class="w-20 h-20 rounded-full object-cover ring-2 ring-orange-200 ring-offset-2">
                            @else
                            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white text-2xl font-black ring-2 ring-orange-200 ring-offset-2">
                                {{ strtoupper(substr($petugas->nama_petugas, 0, 1)) }}
                            </div>
                            @endif
                        </template>
                        <div class="absolute bottom-0 right-0 w-6 h-6 rounded-full bg-orange-500 border-2 border-white flex items-center justify-center shadow-sm">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-stone-900">{{ $petugas->nama_petugas }}</p>
                        <p class="text-xs font-light text-stone-400 mt-0.5 capitalize">{{ $petugas->level }}</p>
                        <button type="button" @click="$refs.fotoInput.click()"
                                class="mt-2 text-xs font-medium text-orange-500 hover:text-orange-600 bg-transparent border-0 p-0 cursor-pointer font-sans">
                            Ganti foto profil
                        </button>
                    </div>
                </div>

                <input type="file" name="foto_profil" accept="image/*" class="hidden"
                       x-ref="fotoInput" @change="handleFile($event)">
                @error('foto_profil')<p class="text-xs text-red-500 mt-2">{{ $message }}</p>@enderror
            </div>

            {{-- Informasi --}}
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-stone-100 mb-4">
                <p class="text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wider mb-4">Informasi Akun</p>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-stone-700 mb-1.5">Nama Lengkap</label>
                    <div class="w-full px-4 py-3 rounded-xl border border-stone-100 bg-stone-50 text-sm text-stone-500 select-none">
                        {{ $petugas->nama_petugas }}
                    </div>
                    <p class="text-[0.6875rem] font-light text-stone-400 mt-1">Nama hanya dapat diubah oleh admin.</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-stone-700 mb-1.5">Username</label>
                    <div class="w-full px-4 py-3 rounded-xl border border-stone-100 bg-stone-50 text-sm text-stone-500 select-none font-mono">
                        {{ $petugas->username }}
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-stone-700 mb-1.5">Nomor Telepon</label>
                    <input type="text" name="telp" value="{{ old('telp', $petugas->telp) }}" required
                           placeholder="08xx-xxxx-xxxx"
                           @class([
                               'w-full px-4 py-3 rounded-xl border text-sm text-stone-900 outline-none transition-[border-color,box-shadow] focus:ring-[3px]',
                               'border-red-300 bg-red-50 focus:border-red-400 focus:ring-red-400/15' => $errors->has('telp'),
                               'border-stone-200 bg-stone-50 focus:border-orange-400 focus:ring-orange-400/15' => !$errors->has('telp'),
                           ])>
                    @error('telp')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Ganti Password --}}
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-stone-100 mb-6">
                <p class="text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wider mb-1">Ganti Password</p>
                <p class="text-xs font-light text-stone-400 mb-4">Kosongkan jika tidak ingin mengubah password.</p>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-stone-700 mb-1.5">Password Baru</label>
                    <input type="password" name="password" placeholder="Minimal 6 karakter"
                           @class([
                               'w-full px-4 py-3 rounded-xl border text-sm text-stone-900 outline-none transition-[border-color,box-shadow] focus:ring-[3px]',
                               'border-red-300 bg-red-50 focus:border-red-400 focus:ring-red-400/15' => $errors->has('password'),
                               'border-stone-200 bg-stone-50 focus:border-orange-400 focus:ring-orange-400/15' => !$errors->has('password'),
                           ])>
                    @error('password')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-stone-700 mb-1.5">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" placeholder="Ulangi password baru"
                           class="w-full px-4 py-3 rounded-xl border border-stone-200 bg-stone-50 text-sm text-stone-900 outline-none transition-[border-color,box-shadow] focus:border-orange-400 focus:ring-[3px] focus:ring-orange-400/15">
                </div>
            </div>

            <button type="submit"
                    class="w-full py-3 px-4 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-[0.9375rem] font-semibold border-0 cursor-pointer font-sans transition-all shadow-sm shadow-orange-200 hover:-translate-y-0.5">
                Simpan Perubahan
            </button>
        </form>
    </div>
</div>
@endsection
