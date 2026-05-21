@extends('layouts.masyarakat')

@section('title', 'Beranda')

@section('header')
<div class="px-4 pt-4 pb-4 bg-orange-500 shrink-0">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-[0.6875rem] text-white/75 font-light">Selamat datang,</p>
            <p class="text-base font-bold text-white mt-0.5">{{ $user->nama }}</p>
        </div>
        <div class="w-10 h-10 rounded-full bg-white/25 flex items-center justify-center text-white font-bold text-base">
            {{ strtoupper(substr($user->nama, 0, 1)) }}
        </div>
    </div>

    {{-- Stats strip --}}
    @php
    $total   = $pengaduan->count();
    $proses  = $pengaduan->whereIn('status', ['proses', 'menunggu'])->count();
    $selesai = $pengaduan->where('status', 'selesai')->count();
    @endphp
    <div class="grid grid-cols-3 gap-2 mt-4 pb-4">
        @foreach([['Total', $total], ['Diproses', $proses], ['Selesai', $selesai]] as [$label, $count])
        <div class="bg-white/18 rounded-xl px-2 py-2.5 text-center">
            <p class="text-xl font-black text-white leading-none">{{ $count }}</p>
            <p class="text-[0.6875rem] text-white/80 mt-0.5">{{ $label }}</p>
        </div>
        @endforeach
    </div>
</div>
@endsection

@section('content')
@php use Illuminate\Support\Str; @endphp
<div class="p-4">

    <div class="flex items-center justify-between mb-3.5">
        <p class="font-semibold text-[0.9375rem] text-stone-900">Pengaduan Terbaru</p>
        @if($pengaduan->isNotEmpty())
        <a href="{{ route('masyarakat.riwayat') }}" class="text-[0.8125rem] text-orange-500 font-medium no-underline">Lihat semua</a>
        @endif
    </div>

    @if($pengaduan->isEmpty())
    <div class="text-center py-12 px-6">
        <div class="w-16 h-16 rounded-full bg-orange-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <p class="font-semibold text-stone-900 mb-1.5">Belum ada pengaduan</p>
        <p class="text-sm font-light text-stone-500">Tekan tombol LAPOR untuk membuat pengaduan pertama Anda.</p>
    </div>
    @else
    <div class="flex flex-col gap-3">
        @foreach($pengaduan->take(5) as $p)
        @php
        $statusCfg = [
            'menunggu' => ['label'=>'Menunggu','pill'=>'bg-amber-100 text-amber-800','dot'=>'bg-amber-500'],
            'proses'   => ['label'=>'Diproses','pill'=>'bg-blue-100 text-blue-800',  'dot'=>'bg-blue-500'],
            'selesai'  => ['label'=>'Selesai', 'pill'=>'bg-emerald-100 text-emerald-800','dot'=>'bg-emerald-500'],
        ][$p->status] ?? ['label'=>$p->status,'pill'=>'bg-stone-100 text-stone-700','dot'=>'bg-stone-400'];
        @endphp
        <div class="bg-white rounded-xl p-4 shadow-sm">
            <div class="flex items-start justify-between gap-3 mb-2.5">
                <p class="text-sm text-stone-900 leading-relaxed flex-1 line-clamp-2">{{ Str::limit($p->isi_laporan, 80) }}</p>
                <span class="shrink-0 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.6875rem] font-semibold {{ $statusCfg['pill'] }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $statusCfg['dot'] }}"></span>
                    {{ $statusCfg['label'] }}
                </span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-xs text-stone-400 font-light">{{ $p->tgl_pengaduan }}</span>
                <span class="text-xs text-stone-400">{{ $p->tanggapan->count() }} tanggapan</span>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
