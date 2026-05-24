@extends('layouts.auth')

@section('title', 'Daftar — M-Lapor')

@section('form')
<div>
    <h2 class="text-2xl font-bold text-stone-900 mb-1 tracking-tight">Buat Akun Baru</h2>
    <p class="text-sm font-light text-stone-500 mb-7">Daftar untuk mulai melaporkan pengaduan Anda.</p>

    @if($errors->any())
        <div class="mb-6 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm">
            @foreach($errors->all() as $e)
                <div class="flex items-start gap-1.5"><span class="text-red-400 shrink-0 mt-0.5">•</span>{{ $e }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="flex flex-col gap-4">
        @csrf

        @php
        $fields = [
            ['name' => 'nik',      'label' => 'NIK',               'type' => 'text',     'placeholder' => '16 digit NIK',        'maxlength' => 16,  'inputmode' => 'numeric'],
            ['name' => 'nama',     'label' => 'Nama Lengkap',       'type' => 'text',     'placeholder' => 'Nama lengkap Anda',   'maxlength' => 35,  'inputmode' => ''],
            ['name' => 'username', 'label' => 'Username',           'type' => 'text',     'placeholder' => 'Buat username unik',  'maxlength' => 25,  'inputmode' => ''],
            ['name' => 'telp',     'label' => 'Nomor Telepon',      'type' => 'tel',      'placeholder' => '08123456789',         'maxlength' => 13,  'inputmode' => 'tel'],
            ['name' => 'password', 'label' => 'Password',           'type' => 'password', 'placeholder' => 'Minimal 6 karakter', 'maxlength' => 255, 'inputmode' => ''],
            ['name' => 'password_confirmation', 'label' => 'Konfirmasi Password', 'type' => 'password', 'placeholder' => 'Ulangi password', 'maxlength' => 255, 'inputmode' => ''],
        ];
        @endphp

        @foreach($fields as $f)
        <div>
            <label class="block text-sm font-medium text-stone-700 mb-1.5">
                {{ $f['label'] }} <span class="text-red-400">*</span>
            </label>
            <input type="{{ $f['type'] }}"
                   name="{{ $f['name'] }}"
                   value="{{ in_array($f['type'], ['password']) ? '' : old($f['name']) }}"
                   required
                   maxlength="{{ $f['maxlength'] }}"
                   {{ $f['inputmode'] ? 'inputmode='.$f['inputmode'] : '' }}
                   placeholder="{{ $f['placeholder'] }}"
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

        <button type="submit"
                class="w-full py-3.5 px-4 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold border-0 cursor-pointer transition-all shadow-[0_4px_14px_rgba(234,88,12,0.3)] hover:-translate-y-0.5 hover:shadow-[0_6px_20px_rgba(234,88,12,0.38)] active:translate-y-px mt-2">
            Buat Akun
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-stone-400">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="font-semibold text-orange-500 hover:text-orange-600 hover:underline transition-colors">Masuk di sini</a>
    </p>
</div>
@endsection
