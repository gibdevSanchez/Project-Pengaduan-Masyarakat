@extends('layouts.masyarakat')

@section('title', 'Pengaduan Terkirim')

@section('header')
<div class="px-4 py-3.5 bg-white border-b border-stone-200 shrink-0 flex items-center gap-3">
    <a href="{{ route('masyarakat.dashboard') }}"
       class="flex items-center justify-center w-8 h-8 rounded-xl border border-stone-200 text-stone-500 no-underline hover:border-orange-300 hover:text-orange-500 transition-colors shrink-0">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
    </a>
    <div>
        <p class="font-bold text-[1.0625rem] text-stone-900 tracking-tight">Pengaduan Terkirim</p>
        <p class="text-[0.6875rem] font-light text-stone-500">Simpan kode lacak Anda</p>
    </div>
</div>
@endsection

@section('content')
<div class="p-4 flex flex-col items-center text-center pt-8" x-data="{ copied: false }">

    {{-- Success icon --}}
    <div class="w-20 h-20 rounded-full bg-emerald-100 flex items-center justify-center mb-5">
        <svg class="w-10 h-10 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
    </div>

    <h1 class="text-xl font-bold text-stone-900 mb-2">Pengaduan Berhasil Dikirim!</h1>
    <p class="text-sm font-light text-stone-500 mb-8 max-w-[280px]">
        Kami akan segera menindaklanjuti laporan Anda. Gunakan kode di bawah untuk memantau perkembangan.
    </p>

    {{-- Tracking code card --}}
    <div class="w-full bg-white rounded-2xl border border-stone-200 shadow-sm p-6 mb-4">
        <p class="text-xs font-semibold text-stone-400 uppercase tracking-wider mb-3">Kode Lacak Pengaduan</p>

        <div class="flex items-center justify-between gap-3 bg-stone-50 rounded-xl px-4 py-3 border border-stone-200 mb-4">
            <span class="font-mono font-bold text-2xl text-stone-900 tracking-widest">{{ $code }}</span>
            <button type="button"
                    @click="navigator.clipboard.writeText('{{ $code }}'); copied = true; setTimeout(() => copied = false, 2000)"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border-0 cursor-pointer transition-all"
                    :class="copied ? 'bg-emerald-100 text-emerald-700' : 'bg-stone-200 text-stone-600 hover:bg-stone-300'">
                <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                <svg x-show="copied" x-cloak class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span x-text="copied ? 'Disalin!' : 'Salin'"></span>
            </button>
        </div>

        <p class="text-xs font-light text-stone-400">Simpan kode ini. Anda bisa melacak status pengaduan kapan saja melalui halaman publik.</p>
    </div>

    {{-- Action buttons --}}
    <a href="{{ route('track', ['code' => $code]) }}"
       class="w-full flex items-center justify-center gap-2 py-3.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-sm font-bold no-underline transition-colors shadow-sm shadow-orange-200 mb-3">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        Lacak Pengaduan Sekarang
    </a>

    <a href="{{ route('masyarakat.dashboard') }}"
       class="w-full flex items-center justify-center py-3.5 rounded-xl border border-stone-200 text-stone-600 text-sm font-semibold no-underline hover:bg-stone-50 transition-colors">
        Kembali ke Beranda
    </a>
</div>
@endsection
