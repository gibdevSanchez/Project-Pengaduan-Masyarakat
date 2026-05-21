@extends('layouts.app')

@section('title', 'Edit Petugas')

@section('content')
<div class="flex-1 overflow-y-auto p-6">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.petugas.index') }}"
           class="flex items-center justify-center w-8 h-8 rounded-lg border border-stone-200 bg-white text-stone-500 no-underline hover:border-orange-400 hover:text-orange-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-stone-900">Edit Petugas</h1>
            <p class="text-sm font-light text-stone-500 mt-0.5">{{ $petugas->nama_petugas }}</p>
        </div>
    </div>

    <div class="max-w-[32rem]">
        <div class="bg-white rounded-xl p-6 shadow-sm">

            @if($errors->any())
                <div class="mb-5 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm">
                    @foreach($errors->all() as $e)
                        <div>• {{ $e }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.petugas.update', $petugas->id_petugas) }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-medium text-stone-700 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="nama_petugas" value="{{ old('nama_petugas', $petugas->nama_petugas) }}" required
                           @class([
                               'w-full px-4 py-3 rounded-xl border text-sm text-stone-900 outline-none transition-[border-color,box-shadow] focus:ring-[3px]',
                               'border-red-300 bg-red-50 focus:border-red-400 focus:ring-red-400/15' => $errors->has('nama_petugas'),
                               'border-stone-200 bg-stone-50 focus:border-orange-400 focus:ring-orange-400/15' => !$errors->has('nama_petugas'),
                           ])>
                    @error('nama_petugas')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-stone-700 mb-1.5">Username</label>
                    <input type="text" name="username" value="{{ old('username', $petugas->username) }}" required
                           @class([
                               'w-full px-4 py-3 rounded-xl border text-sm text-stone-900 outline-none transition-[border-color,box-shadow] focus:ring-[3px]',
                               'border-red-300 bg-red-50 focus:border-red-400 focus:ring-red-400/15' => $errors->has('username'),
                               'border-stone-200 bg-stone-50 focus:border-orange-400 focus:ring-orange-400/15' => !$errors->has('username'),
                           ])>
                    @error('username')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-stone-700 mb-1.5">Nomor Telepon</label>
                    <input type="text" name="telp" value="{{ old('telp', $petugas->telp) }}" required
                           @class([
                               'w-full px-4 py-3 rounded-xl border text-sm text-stone-900 outline-none transition-[border-color,box-shadow] focus:ring-[3px]',
                               'border-red-300 bg-red-50 focus:border-red-400 focus:ring-red-400/15' => $errors->has('telp'),
                               'border-stone-200 bg-stone-50 focus:border-orange-400 focus:ring-orange-400/15' => !$errors->has('telp'),
                           ])>
                    @error('telp')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Password section --}}
                <div class="border-t border-stone-200 pt-4 mt-5 mb-4">
                    <p class="text-[0.8125rem] font-medium text-stone-500 mb-3.5">
                        Ubah Password <span class="font-light">(kosongkan jika tidak diubah)</span>
                    </p>

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
                        <label class="block text-sm font-medium text-stone-700 mb-1.5">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" placeholder="Ulangi password baru"
                               class="w-full px-4 py-3 rounded-xl border border-stone-200 bg-stone-50 text-sm text-stone-900 outline-none transition-[border-color,box-shadow] focus:border-orange-400 focus:ring-[3px] focus:ring-orange-400/15">
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="submit"
                            class="flex-1 py-3 px-4 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-[0.9375rem] font-semibold border-0 cursor-pointer font-sans transition-colors">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('admin.petugas.index') }}"
                       class="px-5 py-3 rounded-xl border border-stone-200 bg-white text-[0.9375rem] font-medium text-stone-500 hover:text-stone-900 no-underline inline-flex items-center transition-colors">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
