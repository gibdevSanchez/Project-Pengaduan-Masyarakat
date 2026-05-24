@extends('layouts.masyarakat')

@section('title', 'Berita Terkini')

@section('header')
<div class="px-4 py-3.5 bg-white border-b border-stone-200 shrink-0">
    <p class="font-bold text-[1.0625rem] text-stone-900 tracking-tight">Berita Terkini</p>
    <p class="text-xs font-light text-stone-500 mt-0.5">Informasi seputar layanan kota</p>
</div>
@endsection

@section('content')
@php
$categoryColor = [
    'Infrastruktur'  => ['pill' => 'bg-blue-100 text-blue-700',     'icon' => 'text-blue-500',    'bg' => 'bg-blue-50'],
    'Layanan Publik' => ['pill' => 'bg-green-100 text-green-700',    'icon' => 'text-green-500',   'bg' => 'bg-green-50'],
    'Lingkungan'     => ['pill' => 'bg-emerald-100 text-emerald-700','icon' => 'text-emerald-500', 'bg' => 'bg-emerald-50'],
    'Tata Kota'      => ['pill' => 'bg-orange-100 text-orange-700',  'icon' => 'text-orange-500',  'bg' => 'bg-orange-50'],
    'Himbauan'       => ['pill' => 'bg-amber-100 text-amber-700',    'icon' => 'text-amber-500',   'bg' => 'bg-amber-50'],
];
@endphp

<div class="p-3.5 flex flex-col gap-3 pb-6">
    @foreach($berita as $i => $item)
    @php $cat = $categoryColor[$item['kategori']] ?? ['pill'=>'bg-stone-100 text-stone-600','icon'=>'text-stone-500','bg'=>'bg-stone-50']; @endphp

    @if($i === 0)
    {{-- Featured card --}}
    <div class="bg-gradient-to-br from-orange-500 to-orange-700 rounded-2xl overflow-hidden shadow-md relative">
        <div class="absolute inset-0 pointer-events-none opacity-[0.08]"
             style="background-image:radial-gradient(circle,#fff 1px,transparent 1px);background-size:20px 20px;"></div>
        <div class="relative p-5">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[0.6875rem] font-semibold bg-white/25 border border-white/20 text-white mb-3">
                {{ $item['kategori'] }}
            </span>
            <p class="font-bold text-[1.0625rem] text-white leading-snug mb-2">{{ $item['judul'] }}</p>
            <p class="text-[0.8125rem] text-white/85 leading-relaxed">{{ $item['ringkasan'] }}</p>
            <div class="flex items-center gap-2 mt-4 pt-3.5 border-t border-white/20">
                <svg class="w-3.5 h-3.5 text-white/60 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
                <span class="text-[0.6875rem] text-white/70 font-medium">{{ $item['sumber'] }}</span>
                <span class="text-[0.6875rem] text-white/40">·</span>
                <span class="text-[0.6875rem] text-white/70">{{ $item['waktu'] }}</span>
            </div>
        </div>
    </div>

    @else
    {{-- Regular card --}}
    <div class="bg-white rounded-xl overflow-hidden shadow-sm border border-stone-100">
        <div class="p-4">
            <div class="flex items-start gap-3.5">
                <div class="flex-1 min-w-0">
                    <span class="inline-flex px-2 py-0.5 rounded-full text-[0.625rem] font-semibold {{ $cat['pill'] }} mb-2">
                        {{ $item['kategori'] }}
                    </span>
                    <p class="font-semibold text-[0.9375rem] text-stone-900 leading-snug mb-1.5">{{ $item['judul'] }}</p>
                    <p class="text-[0.8125rem] font-light text-stone-500 leading-relaxed">{{ $item['ringkasan'] }}</p>
                    <div class="flex items-center gap-2 mt-2.5">
                        <span class="text-[0.6875rem] font-medium text-stone-400">{{ $item['sumber'] }}</span>
                        <span class="text-[0.6875rem] text-stone-300">·</span>
                        <span class="text-[0.6875rem] text-stone-400">{{ $item['waktu'] }}</span>
                    </div>
                </div>
                <div class="w-14 h-14 rounded-xl {{ $cat['bg'] }} flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 {{ $cat['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
    @endif

    @endforeach
</div>
@endsection
