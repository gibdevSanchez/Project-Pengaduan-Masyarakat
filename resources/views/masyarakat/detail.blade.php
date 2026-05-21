@extends('layouts.masyarakat')

@section('title', 'Detail Pengaduan')

@section('header')
<div class="px-4 py-3 bg-white border-b border-stone-200 shrink-0 flex items-center gap-3">
    <a href="{{ route('masyarakat.riwayat') }}"
       class="flex items-center justify-center w-8 h-8 rounded-lg border border-stone-200 text-stone-500 no-underline hover:border-orange-400 hover:text-orange-500 transition-colors shrink-0">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>
    <div>
        <p class="font-bold text-[1.0625rem] text-stone-900">Detail Pengaduan</p>
        <p class="text-[0.6875rem] font-light text-stone-500">{{ $pengaduan->tgl_pengaduan }}</p>
    </div>
</div>
@endsection

@section('content')
@php
$statusCfg = [
    'menunggu'    => ['label'=>'Menunggu',    'pill'=>'bg-amber-100 text-amber-800',    'dot'=>'bg-amber-500'],
    'proses'      => ['label'=>'Diproses',    'pill'=>'bg-blue-100 text-blue-800',      'dot'=>'bg-blue-500'],
    'selesai'     => ['label'=>'Selesai',     'pill'=>'bg-emerald-100 text-emerald-800','dot'=>'bg-emerald-500'],
    'tidak_valid' => ['label'=>'Tidak Valid', 'pill'=>'bg-red-100 text-red-800',        'dot'=>'bg-red-500'],
][$pengaduan->status] ?? ['label'=>$pengaduan->status,'pill'=>'bg-stone-100 text-stone-700','dot'=>'bg-stone-400'];

$kategoriCfg = [
    'infrastruktur' => 'bg-blue-100 text-blue-700',
    'lingkungan'    => 'bg-emerald-100 text-emerald-700',
    'keamanan'      => 'bg-red-100 text-red-700',
    'sosial'        => 'bg-purple-100 text-purple-700',
    'lainnya'       => 'bg-stone-100 text-stone-600',
][$pengaduan->kategori] ?? 'bg-stone-100 text-stone-600';
@endphp

<div class="p-4 flex flex-col gap-4">

    {{-- Meta badges --}}
    <div class="flex flex-wrap items-center gap-2">
        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[0.6875rem] font-semibold {{ $statusCfg['pill'] }}">
            <span class="w-1.5 h-1.5 rounded-full {{ $statusCfg['dot'] }}"></span>
            {{ $statusCfg['label'] }}
        </span>
        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[0.6875rem] font-semibold {{ $kategoriCfg }}">
            {{ ucfirst($pengaduan->kategori) }}
        </span>
        @if($pengaduan->lokasi)
        <span class="text-xs text-stone-400">📍 {{ $pengaduan->lokasi }}</span>
        @endif
    </div>

    {{-- Laporan --}}
    <div>
        <p class="text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wide mb-2">Laporan</p>
        <div class="bg-white rounded-xl p-4 shadow-sm">
            <p class="text-sm text-stone-900 leading-relaxed">{{ $pengaduan->isi_laporan }}</p>
            @if($pengaduan->foto)
            <img src="{{ Storage::url($pengaduan->foto) }}"
                 class="mt-3 w-full rounded-xl object-cover max-h-48" alt="Foto bukti">
            @endif
        </div>
    </div>

    {{-- Tanggapan --}}
    <div>
        <p class="text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wide mb-2">
            Tanggapan ({{ $pengaduan->tanggapan->count() }})
        </p>

        @if($pengaduan->tanggapan->isEmpty())
        <div class="bg-white rounded-xl p-4 shadow-sm text-center text-sm text-stone-400 font-light">
            Belum ada tanggapan dari petugas.
        </div>
        @else
        <div class="flex flex-col gap-3">
            @foreach($pengaduan->tanggapan as $t)
            <div class="bg-white rounded-xl p-4 shadow-sm">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-7 h-7 rounded-full bg-orange-500 flex items-center justify-center text-white text-[0.6875rem] font-bold shrink-0">
                        {{ strtoupper(substr($t->petugas?->nama_petugas ?? 'P', 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-stone-900">{{ $t->petugas?->nama_petugas ?? 'Petugas' }}</p>
                        <p class="text-[0.6875rem] text-stone-400 font-light">{{ $t->tgl_tanggapan }}</p>
                    </div>
                </div>
                <p class="text-sm text-stone-900 leading-relaxed">{{ $t->tanggapan }}</p>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Klarifikasi --}}
    @if(!is_null($pengaduan->nik) && $pengaduan->klarifikasi->count() > 0)
    <div>
        <p class="text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wide mb-2">
            Klarifikasi dengan Petugas
        </p>

        <div class="flex flex-col gap-3">
            @foreach($pengaduan->klarifikasi as $k)
            <div class="{{ $k->dari === 'masyarakat' ? 'flex justify-end' : 'flex justify-start' }}">
                <div class="max-w-[85%]">
                    @if($k->dari === 'petugas')
                    <p class="text-[0.6875rem] font-medium text-stone-400 mb-1">Petugas</p>
                    @endif
                    <div class="{{ $k->dari === 'masyarakat'
                        ? 'bg-orange-500 text-white rounded-2xl rounded-br-sm'
                        : 'bg-white text-stone-900 rounded-2xl rounded-bl-sm shadow-sm' }} px-4 py-3 text-sm leading-relaxed">
                        {{ $k->pesan }}
                    </div>
                    <p class="text-[0.6875rem] font-light text-stone-400 mt-1 {{ $k->dari === 'masyarakat' ? 'text-right' : 'text-left' }}">
                        {{ $k->created_at->format('d M Y, H:i') }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Reply input --}}
        @php $lastK = $pengaduan->klarifikasi->last(); @endphp
        @if($lastK && $lastK->dari === 'petugas' && !in_array($pengaduan->status, ['selesai', 'tidak_valid']))
        <div class="mt-3">
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
        </div>
        @else
        <p class="text-center text-xs text-stone-400 italic mt-3">
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
