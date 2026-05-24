@extends('layouts.masyarakat')

@section('title', 'Beranda')

@section('header')
<div class="shrink-0 bg-gradient-to-br from-orange-500 to-orange-700 relative overflow-hidden">
    {{-- Dot pattern --}}
    <div class="absolute inset-0 pointer-events-none opacity-[0.1]"
         style="background-image:radial-gradient(circle,#fff 1px,transparent 1px);background-size:20px 20px;"></div>

    <div class="relative px-4 pt-5 pb-0">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-[0.6875rem] text-white/70 font-light">Selamat datang,</p>
                <p class="text-base font-bold text-white mt-0.5 tracking-tight">{{ $user->nama }}</p>
            </div>
            <div class="w-10 h-10 rounded-full bg-white/20 border border-white/30 flex items-center justify-center text-white font-bold text-base">
                {{ strtoupper(substr($user->nama, 0, 1)) }}
            </div>
        </div>

        {{-- Stats strip --}}
        @php
        $total   = $pengaduan->count();
        $proses  = $pengaduan->whereIn('status', ['proses', 'menunggu'])->count();
        $selesai = $pengaduan->where('status', 'selesai')->count();
        @endphp
        <div class="grid grid-cols-3 gap-2 pb-5">
            @foreach([['Total', $total, 'Laporan'], ['Aktif', $proses, 'Diproses'], ['Selesai', $selesai, 'Tuntas']] as [$lbl, $count, $sub])
            <div class="bg-white/15 border border-white/20 rounded-2xl px-3 py-3 text-center">
                <p class="text-2xl font-black text-white leading-none">{{ $count }}</p>
                <p class="text-[0.6875rem] text-white/75 mt-1 font-medium">{{ $lbl }}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@section('content')
@php use Illuminate\Support\Str; @endphp
<div class="p-4">

    <div class="flex items-center justify-between mb-3.5">
        <p class="font-semibold text-[0.9375rem] text-stone-900">Pengaduan Terbaru</p>
        @if($pengaduan->isNotEmpty())
        <a href="{{ route('masyarakat.riwayat') }}" class="text-[0.8125rem] text-orange-500 font-medium no-underline hover:text-orange-600">Lihat semua →</a>
        @endif
    </div>

    @if($pengaduan->isEmpty())
    <div class="text-center py-14 px-6">
        <div class="w-16 h-16 rounded-2xl bg-orange-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <p class="font-semibold text-stone-800 mb-1.5">Belum ada pengaduan</p>
        <p class="text-sm font-light text-stone-500 leading-relaxed">Tekan tombol <span class="font-semibold text-orange-500">LAPOR</span> di bawah untuk membuat pengaduan pertama Anda.</p>
    </div>
    @else
    <div class="flex flex-col gap-2.5">
        @foreach($pengaduan->take(5) as $p)
        @php
        $statusCfg = [
            'menunggu'    => ['label'=>'Menunggu',    'pill'=>'bg-amber-100 text-amber-700',   'dot'=>'bg-amber-400',   'border'=>'border-l-amber-400'],
            'proses'      => ['label'=>'Diproses',    'pill'=>'bg-blue-100 text-blue-700',     'dot'=>'bg-blue-400',    'border'=>'border-l-blue-400'],
            'selesai'     => ['label'=>'Selesai',     'pill'=>'bg-emerald-100 text-emerald-700','dot'=>'bg-emerald-400','border'=>'border-l-emerald-400'],
            'tidak_valid' => ['label'=>'Tidak Valid', 'pill'=>'bg-red-100 text-red-700',       'dot'=>'bg-red-400',     'border'=>'border-l-red-400'],
        ][$p->status] ?? ['label'=>$p->status,'pill'=>'bg-stone-100 text-stone-600','dot'=>'bg-stone-400','border'=>'border-l-stone-300'];
        @endphp
        <a href="{{ route('masyarakat.pengaduan.show', $p->id_pengaduan) }}"
           class="block bg-white rounded-xl p-4 shadow-sm border-l-[3px] {{ $statusCfg['border'] }} no-underline hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between gap-3 mb-2.5">
                <p class="text-sm text-stone-800 leading-relaxed flex-1 line-clamp-2">{{ Str::limit($p->isi_laporan, 80) }}</p>
                <span class="shrink-0 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.6875rem] font-medium {{ $statusCfg['pill'] }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $statusCfg['dot'] }}"></span>
                    {{ $statusCfg['label'] }}
                </span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-xs text-stone-400 font-light">{{ $p->created_at->format('d M Y') }}</span>
                <span class="text-xs {{ $p->tanggapan->count() > 0 ? 'text-orange-500 font-medium' : 'text-stone-400' }}">
                    {{ $p->tanggapan->count() > 0 ? $p->tanggapan->count().' tanggapan' : 'Belum ada tanggapan' }}
                </span>
            </div>
        </a>
        @endforeach
    </div>
    @endif
</div>
@endsection
