@extends('layouts.masyarakat')

@section('title', 'Berita Terkini')

@section('header')
<div class="px-4 py-3.5 bg-white border-b border-stone-200 shrink-0">
    <p class="font-bold text-[1.0625rem] text-stone-900 tracking-tight">Berita Terkini</p>
    <p class="text-xs font-light text-stone-500 mt-0.5">Informasi seputar layanan kota</p>
</div>
@endsection

@section('content')
<div class="p-3.5 flex flex-col gap-3 pb-6">

    {{-- Berita Besar Banner --}}
    @if($beritaBesar)
    @if($beritaBesar->foto)
    {{-- With photo: image with overlay --}}
    <div class="rounded-2xl overflow-hidden shadow-md relative">
        <img src="{{ Storage::url($beritaBesar->foto) }}" alt="{{ $beritaBesar->judul }}"
             class="w-full h-48 object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/30 to-transparent"></div>
        <div class="absolute inset-0 p-5 flex flex-col justify-end">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[0.6875rem] font-semibold bg-white/25 border border-white/20 text-white mb-2 capitalize w-fit">
                {{ $beritaBesar->kategori }}
            </span>
            <p class="font-bold text-[1.0625rem] text-white leading-snug mb-1">{{ $beritaBesar->judul }}</p>
            <p class="text-[0.8125rem] text-white/80 leading-relaxed line-clamp-2">{{ $beritaBesar->isi }}</p>
            <div class="flex items-center gap-2 mt-3">
                <span class="text-[0.6875rem] text-white/70 font-medium">{{ $beritaBesar->penulis?->nama_petugas }}</span>
                <span class="text-[0.6875rem] text-white/40">·</span>
                <span class="text-[0.6875rem] text-white/70">{{ $beritaBesar->created_at->format('d M Y') }}</span>
            </div>
        </div>
    </div>
    @else
    {{-- No photo: orange gradient --}}
    <div class="bg-gradient-to-br from-orange-500 to-orange-700 rounded-2xl overflow-hidden shadow-md relative">
        <div class="absolute inset-0 pointer-events-none opacity-[0.08]"
             style="background-image:radial-gradient(circle,#fff 1px,transparent 1px);background-size:20px 20px;"></div>
        <div class="relative p-5">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[0.6875rem] font-semibold bg-white/25 border border-white/20 text-white mb-3 capitalize">
                {{ $beritaBesar->kategori }}
            </span>
            <p class="font-bold text-[1.0625rem] text-white leading-snug mb-2">{{ $beritaBesar->judul }}</p>
            <p class="text-[0.8125rem] text-white/85 leading-relaxed line-clamp-3">{{ $beritaBesar->isi }}</p>
            <div class="flex items-center gap-2 mt-4 pt-3.5 border-t border-white/20">
                <svg class="w-3.5 h-3.5 text-white/60 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
                <span class="text-[0.6875rem] text-white/70 font-medium">{{ $beritaBesar->penulis?->nama_petugas }}</span>
                <span class="text-[0.6875rem] text-white/40">·</span>
                <span class="text-[0.6875rem] text-white/70">{{ $beritaBesar->created_at->format('d M Y') }}</span>
            </div>
        </div>
    </div>
    @endif
    @endif

    {{-- Berita Biasa List --}}
    @forelse($berita as $b)
    <div class="bg-white rounded-xl overflow-hidden shadow-sm border border-stone-100">
        @if($b->foto)
        <img src="{{ Storage::url($b->foto) }}" alt="{{ $b->judul }}"
             class="w-full h-36 object-cover">
        @endif
        <div class="p-4">
            <div class="flex items-start gap-3.5">
                <div class="flex-1 min-w-0">
                    <span class="inline-flex px-2 py-0.5 rounded-full text-[0.625rem] font-semibold bg-stone-100 text-stone-600 mb-2 capitalize">
                        {{ $b->kategori }}
                    </span>
                    <p class="font-semibold text-[0.9375rem] text-stone-900 leading-snug mb-1.5">{{ $b->judul }}</p>
                    <p class="text-[0.8125rem] font-light text-stone-500 leading-relaxed line-clamp-2">{{ $b->isi }}</p>
                    <div class="flex items-center gap-2 mt-2.5">
                        <span class="text-[0.6875rem] font-medium text-stone-400">{{ $b->penulis?->nama_petugas }}</span>
                        <span class="text-[0.6875rem] text-stone-300">·</span>
                        <span class="text-[0.6875rem] text-stone-400">{{ $b->created_at->format('d M Y') }}</span>
                        @if($b->id_pengaduan)
                        <span class="text-[0.6875rem] text-stone-300">·</span>
                        <span class="text-[0.6875rem] text-orange-500">Terkait #{{ $b->id_pengaduan }}</span>
                        @endif
                    </div>
                </div>
                @if(!$b->foto)
                <div class="w-14 h-14 rounded-xl bg-orange-50 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                </div>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl p-10 text-center border border-stone-100">
        <p class="text-stone-400 text-sm">Belum ada berita terbaru.</p>
    </div>
    @endforelse

    @if($berita->hasPages())
    <div class="px-1">{{ $berita->links() }}</div>
    @endif

</div>
@endsection
