@extends('layouts.app')

@section('title', 'Tambah Petugas')

@section('content')
<div class="flex-1 overflow-y-auto p-6 bg-stone-50">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.petugas.index') }}"
           class="flex items-center justify-center w-9 h-9 rounded-xl border border-stone-200 bg-white text-stone-500 no-underline hover:border-orange-300 hover:text-orange-500 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-stone-900 tracking-tight">Tambah Petugas</h1>
            <p class="text-sm font-light text-stone-500 mt-0.5">Buat akun baru untuk petugas lapangan</p>
        </div>
    </div>

    <div class="max-w-[32rem]">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-stone-100">

            @if($errors->any())
                <div class="mb-5 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm">
                    @foreach($errors->all() as $e)
                        <div class="flex items-start gap-1.5"><span class="text-red-400 shrink-0 mt-0.5">•</span>{{ $e }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.petugas.store') }}">
                @csrf

                @php
                $fields = [
                    ['name' => 'nama_petugas',          'label' => 'Nama Lengkap',        'type' => 'text',     'placeholder' => 'Nama petugas'],
                    ['name' => 'username',              'label' => 'Username',            'type' => 'text',     'placeholder' => 'Username untuk login'],
                    ['name' => 'telp',                  'label' => 'Nomor Telepon',       'type' => 'text',     'placeholder' => '08xx-xxxx-xxxx'],
                    ['name' => 'password',              'label' => 'Password',            'type' => 'password', 'placeholder' => 'Minimal 6 karakter'],
                    ['name' => 'password_confirmation', 'label' => 'Konfirmasi Password', 'type' => 'password', 'placeholder' => 'Ulangi password'],
                ];
                @endphp

                @foreach($fields as $f)
                <div class="mb-4">
                    <label class="block text-sm font-medium text-stone-700 mb-1.5">{{ $f['label'] }}</label>
                    <input type="{{ $f['type'] }}" name="{{ $f['name'] }}"
                           value="{{ $f['type'] !== 'password' ? old($f['name']) : '' }}"
                           placeholder="{{ $f['placeholder'] }}" required
                           @class([
                               'w-full px-4 py-3 rounded-xl border text-sm text-stone-900 outline-none transition-[border-color,box-shadow] focus:ring-[3px]',
                               'border-red-300 bg-red-50 focus:border-red-400 focus:ring-red-400/15' => $errors->has($f['name']),
                               'border-stone-200 bg-stone-50 focus:border-orange-400 focus:ring-orange-400/15' => !$errors->has($f['name']),
                           ])>
                    @error($f['name'])
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                @endforeach

                <div class="flex gap-3 mt-6">
                    <button type="submit"
                            class="flex-1 py-3 px-4 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-[0.9375rem] font-semibold border-0 cursor-pointer font-sans transition-all shadow-sm shadow-orange-200 hover:-translate-y-0.5">
                        Simpan Akun
                    </button>
                    <a href="{{ route('admin.petugas.index') }}"
                       class="px-5 py-3 rounded-xl border border-stone-200 bg-white text-[0.9375rem] font-medium text-stone-500 hover:text-stone-800 hover:border-stone-300 no-underline inline-flex items-center transition-colors">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
