@extends('layouts.masyarakat')

@section('title', 'Detail Pengaduan')

@section('header')
<div class="px-4 py-3.5 bg-white border-b border-stone-200 shrink-0 flex items-center gap-3">
    <a href="{{ route('masyarakat.riwayat') }}"
       class="flex items-center justify-center w-8 h-8 rounded-xl border border-stone-200 text-stone-500 no-underline hover:border-orange-300 hover:text-orange-500 transition-colors shrink-0">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>
    <div>
        <p class="font-bold text-[1.0625rem] text-stone-900 tracking-tight">Detail Pengaduan</p>
        <p class="text-[0.6875rem] font-light text-stone-500">{{ $pengaduan->tgl_pengaduan }}</p>
    </div>
</div>
@endsection

@section('content')
@php
$statusCfg = [
    'menunggu'    => ['label'=>'Menunggu',    'pill'=>'bg-amber-100 text-amber-700',    'dot'=>'bg-amber-400'],
    'proses'      => ['label'=>'Diproses',    'pill'=>'bg-blue-100 text-blue-700',      'dot'=>'bg-blue-400'],
    'selesai'     => ['label'=>'Selesai',     'pill'=>'bg-emerald-100 text-emerald-700','dot'=>'bg-emerald-400'],
    'tidak_valid' => ['label'=>'Tidak Valid', 'pill'=>'bg-red-100 text-red-700',        'dot'=>'bg-red-400'],
][$pengaduan->status] ?? ['label'=>$pengaduan->status,'pill'=>'bg-stone-100 text-stone-600','dot'=>'bg-stone-400'];

$kategoriCfg = [
    'infrastruktur' => 'bg-blue-100 text-blue-700',
    'lingkungan'    => 'bg-emerald-100 text-emerald-700',
    'keamanan'      => 'bg-red-100 text-red-700',
    'sosial'        => 'bg-purple-100 text-purple-700',
    'lainnya'       => 'bg-stone-100 text-stone-600',
][$pengaduan->kategori] ?? 'bg-stone-100 text-stone-600';
@endphp

<div class="p-4 flex flex-col gap-3.5">

    {{-- Meta badges --}}
    <div class="flex flex-wrap items-center gap-2">
        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[0.6875rem] font-medium {{ $statusCfg['pill'] }}">
            <span class="w-1.5 h-1.5 rounded-full {{ $statusCfg['dot'] }}"></span>
            {{ $statusCfg['label'] }}
        </span>
        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[0.6875rem] font-medium {{ $kategoriCfg }}">
            {{ ucfirst($pengaduan->kategori) }}
        </span>
        @if($pengaduan->lokasi)
        <span class="inline-flex items-center gap-1 text-xs text-stone-500">
            <svg class="w-3 h-3 shrink-0 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            {{ $pengaduan->lokasi }}
        </span>
        @endif
    </div>

    {{-- Laporan --}}
    <div>
        <p class="text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wider mb-2">Laporan</p>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-stone-100">
            <p class="text-sm text-stone-800 leading-relaxed">{{ $pengaduan->isi_laporan }}</p>
            @if($pengaduan->foto)
            <img src="{{ Storage::url($pengaduan->foto) }}"
                 class="mt-3 w-full rounded-xl object-cover max-h-52 border border-stone-100" alt="Foto bukti">
            @endif
        </div>
    </div>

    {{-- Tanggapan --}}
    <div>
        <p class="text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wider mb-2">
            Tanggapan ({{ $pengaduan->tanggapan->count() }})
        </p>

        @if($pengaduan->tanggapan->isEmpty())
        <div class="bg-white rounded-xl p-5 shadow-sm border border-stone-100 text-center">
            <svg class="w-8 h-8 text-stone-200 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            <p class="text-sm text-stone-400 font-light">Belum ada tanggapan dari petugas.</p>
        </div>
        @else
        <div class="flex flex-col gap-2.5">
            @foreach($pengaduan->tanggapan as $t)
            <div class="bg-white rounded-xl p-4 shadow-sm border border-stone-100">
                <div class="flex items-center gap-2.5 mb-2.5">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white text-[0.6875rem] font-bold shrink-0">
                        {{ strtoupper(substr($t->petugas?->nama_petugas ?? 'P', 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-stone-800">{{ $t->petugas?->nama_petugas ?? 'Petugas' }}</p>
                        <p class="text-[0.6875rem] text-stone-400 font-light">{{ $t->tgl_tanggapan }}</p>
                    </div>
                </div>
                <p class="text-sm text-stone-700 leading-relaxed">{{ $t->tanggapan }}</p>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Klarifikasi --}}
    @if(!is_null($pengaduan->nik) && $pengaduan->klarifikasi->count() > 0)
    <div>
        <p class="text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wider mb-2">
            Klarifikasi dengan Petugas
        </p>

        <div class="flex flex-col gap-2.5 mb-3">
            @foreach($pengaduan->klarifikasi as $k)
            <div class="{{ $k->dari === 'masyarakat' ? 'flex justify-end' : 'flex justify-start' }}">
                <div class="max-w-[85%]">
                    @if($k->dari === 'petugas')
                    <p class="text-[0.6875rem] font-semibold text-stone-400 mb-1 ml-1">Petugas</p>
                    @endif
                    <div @class([
                        'px-4 py-3 text-sm leading-relaxed rounded-2xl',
                        'bg-orange-500 text-white rounded-br-sm' => $k->dari === 'masyarakat',
                        'bg-white text-stone-800 rounded-bl-sm shadow-sm border border-stone-100' => $k->dari !== 'masyarakat',
                    ])>
                        {{ $k->pesan }}
                    </div>
                    <p class="text-[0.6875rem] font-light text-stone-400 mt-1 {{ $k->dari === 'masyarakat' ? 'text-right' : 'text-left ml-1' }}">
                        {{ $k->created_at->format('d M Y, H:i') }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>

        @php $lastK = $pengaduan->klarifikasi->last(); @endphp
        @if($lastK && $lastK->dari === 'petugas' && !in_array($pengaduan->status, ['selesai', 'tidak_valid']))
        <form action="{{ route('masyarakat.klarifikasi.store', $pengaduan->id_pengaduan) }}" method="POST">
            @csrf
            <div class="flex gap-2">
                <input type="text" name="pesan" required
                       placeholder="Tulis jawaban Anda..."
                       class="flex-1 px-4 py-2.5 rounded-xl border border-stone-200 bg-white text-sm font-sans text-stone-900 outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-400/20">
                <button type="submit"
                        class="px-4 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold border-0 cursor-pointer font-sans transition-colors">
                    Kirim
                </button>
            </div>
            @error('pesan')
            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </form>
        @else
        <p class="text-center text-xs text-stone-400 italic">
            @if(in_array($pengaduan->status, ['selesai', 'tidak_valid']))
                Thread klarifikasi telah ditutup.
            @else
                Menunggu pertanyaan selanjutnya dari petugas.
            @endif
        </p>
        @endif
    </div>
    @endif

</div>
@endsection
