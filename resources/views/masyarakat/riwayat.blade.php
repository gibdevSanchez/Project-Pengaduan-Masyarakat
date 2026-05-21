@extends('layouts.masyarakat')

@section('title', 'Riwayat Pengaduan')

@section('header')
<div class="px-4 py-3 bg-white border-b border-stone-200 shrink-0">
    <p class="font-bold text-[1.0625rem] text-stone-900">Riwayat Pengaduan</p>
    <p class="text-xs font-light text-stone-500 mt-0.5">{{ $pengaduan->count() }} laporan total</p>
</div>
@endsection

@section('content')
@php use Illuminate\Support\Str; @endphp
<div x-data="{ filter: 'semua' }">

    {{-- Filter chips --}}
    <div class="px-3.5 py-3 flex gap-2 overflow-x-auto scrollbar-none shrink-0 bg-white border-b border-stone-200">
        @foreach(['semua' => 'Semua', 'menunggu' => 'Menunggu', 'proses' => 'Diproses', 'selesai' => 'Selesai'] as $val => $lbl)
        <button @click="filter = '{{ $val }}'"
                :class="filter === '{{ $val }}'
                    ? 'bg-orange-500 text-white border-orange-500'
                    : 'bg-white text-stone-500 border-stone-200'"
                class="shrink-0 px-3.5 py-1.5 rounded-full text-[0.8125rem] font-medium border cursor-pointer font-sans transition-all">
            {{ $lbl }}
        </button>
        @endforeach
    </div>

    @if($pengaduan->isEmpty())
    <div class="text-center py-16 px-6">
        <div class="w-16 h-16 rounded-full bg-orange-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <p class="font-semibold text-stone-900 mb-1.5">Belum ada pengaduan</p>
        <p class="text-sm font-light text-stone-500">Pengaduan yang Anda buat akan muncul di sini.</p>
    </div>
    @else
    <div class="p-3.5 flex flex-col gap-2.5">
        @foreach($pengaduan as $p)
        @php
        $statusCfg = [
            'menunggu' => ['label'=>'Menunggu','pill'=>'bg-amber-100 text-amber-800','dot'=>'bg-amber-500'],
            'proses'   => ['label'=>'Diproses','pill'=>'bg-blue-100 text-blue-800',  'dot'=>'bg-blue-500'],
            'selesai'  => ['label'=>'Selesai', 'pill'=>'bg-emerald-100 text-emerald-800','dot'=>'bg-emerald-500'],
        ][$p->status] ?? ['label'=>$p->status,'pill'=>'bg-stone-100 text-stone-700','dot'=>'bg-stone-400'];
        @endphp
        <a href="{{ route('masyarakat.pengaduan.show', $p->id_pengaduan) }}"
           x-show="filter === 'semua' || filter === '{{ $p->status }}'"
           class="block bg-white rounded-xl p-4 shadow-sm no-underline">

            <div class="flex items-start justify-between gap-3 mb-2.5">
                <p class="text-sm text-stone-900 leading-relaxed flex-1">{{ Str::limit($p->isi_laporan, 100) }}</p>
                <span class="shrink-0 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.6875rem] font-semibold {{ $statusCfg['pill'] }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $statusCfg['dot'] }}"></span>
                    {{ $statusCfg['label'] }}
                </span>
                @php
                $katCfg = [
                    'infrastruktur' => 'bg-blue-100 text-blue-700',
                    'lingkungan'    => 'bg-emerald-100 text-emerald-700',
                    'keamanan'      => 'bg-red-100 text-red-700',
                    'sosial'        => 'bg-purple-100 text-purple-700',
                    'lainnya'       => 'bg-stone-100 text-stone-600',
                ][$p->kategori] ?? 'bg-stone-100 text-stone-600';
                @endphp
                <span class="shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-[0.6875rem] font-medium {{ $katCfg }}">
                    {{ ucfirst($p->kategori) }}
                </span>
            </div>

            <div class="flex items-center justify-between">
                <span class="text-xs text-stone-400 font-light">{{ $p->tgl_pengaduan }}</span>
                @if($p->tanggapan->count() > 0)
                <span class="text-xs text-orange-500 font-medium">{{ $p->tanggapan->count() }} tanggapan</span>
                @else
                <span class="text-xs text-stone-400">Belum ada tanggapan</span>
                @endif
            </div>

            @if($p->tanggapan->count() > 0)
            <div class="mt-3 pt-3 border-t border-stone-100">
                <p class="text-[0.6875rem] font-medium text-stone-400 mb-1.5">Tanggapan terakhir:</p>
                <p class="text-[0.8125rem] text-stone-900 leading-relaxed">{{ Str::limit($p->tanggapan->last()->tanggapan, 80) }}</p>
                <p class="text-[0.6875rem] text-stone-400 mt-1">— {{ $p->tanggapan->last()->petugas?->nama_petugas ?? 'Petugas' }}</p>
            </div>
            @endif
        </a>
        @endforeach
    </div>
    @endif
</div>
@endsection
