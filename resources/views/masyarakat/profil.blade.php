@extends('layouts.masyarakat')

@section('title', 'Profil Saya')

@section('header')
<div class="px-4 py-3 bg-white border-b border-stone-200 shrink-0">
    <p class="font-bold text-[1.0625rem] text-stone-900">Profil Saya</p>
</div>
@endsection

@section('content')
<div class="p-5">

    {{-- Avatar card --}}
    <div class="bg-white rounded-2xl p-6 text-center shadow-sm mb-3.5">
        <div class="w-20 h-20 rounded-full bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center mx-auto mb-4 text-white text-[1.75rem] font-black shadow-[0_4px_12px_rgba(234,88,12,0.35)]">
            {{ strtoupper(substr($user->nama, 0, 1)) }}
        </div>
        <p class="font-bold text-lg text-stone-900">{{ $user->nama }}</p>
        <p class="text-[0.8125rem] font-light text-stone-500 mt-1">Masyarakat</p>
    </div>

    {{-- Info card --}}
    <div class="bg-white rounded-2xl overflow-hidden shadow-sm mb-3.5">
        <div class="px-4 py-3 border-b border-stone-100">
            <p class="text-xs font-semibold text-stone-400 uppercase tracking-wide">Informasi Akun</p>
        </div>

        @php
        $rows = [
            ['label' => 'Nama Lengkap', 'value' => $user->nama,
             'icon'  => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
            ['label' => 'NIK',          'value' => $user->nik ? '****' . substr($user->nik, -4) : '—',
             'icon'  => 'M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2'],
            ['label' => 'Telepon',      'value' => $user->telp ?? '—',
             'icon'  => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z'],
            ['label' => 'Email',        'value' => $user->email ?? '—',
             'icon'  => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
        ];
        @endphp

        @foreach($rows as $row)
        <div class="flex items-center gap-3.5 px-4 py-3.5 border-b border-stone-100">
            <div class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $row['icon'] }}"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-[0.6875rem] font-light text-stone-400">{{ $row['label'] }}</p>
                <p class="text-sm font-medium text-stone-900 truncate">{{ $row['value'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Logout --}}
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

    <p class="text-center text-[0.6875rem] font-light text-stone-400 mt-6">M-Lapor · Pengaduan Masyarakat</p>
</div>
@endsection
