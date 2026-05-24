@extends('layouts.app')

@section('title', 'Manajemen Pengaduan')

@section('content')
@php use Illuminate\Support\Str; @endphp
<div class="flex-1 overflow-y-auto p-6 bg-stone-50">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-5">
        <div>
            <h1 class="text-xl font-bold text-stone-900 tracking-tight">Manajemen Pengaduan</h1>
            <p class="text-sm font-light text-stone-500 mt-0.5">{{ $pengaduan->total() }} pengaduan total</p>
        </div>
    </div>

    {{-- Filter bar --}}
    <div class="bg-white rounded-2xl shadow-sm border border-stone-100 p-4 mb-4 flex flex-wrap gap-2 items-center">
        @foreach(['' => 'Semua', 'menunggu' => 'Menunggu', 'proses' => 'Proses', 'selesai' => 'Selesai', 'tidak_valid' => 'Tidak Valid', 'deleted' => 'Dihapus'] as $val => $lbl)
        <a href="{{ request()->fullUrlWithQuery(['filter' => $val, 'status' => '', 'page' => 1]) }}"
           @class([
               'px-3.5 py-1.5 rounded-full text-xs font-medium border transition-all',
               'bg-orange-500 text-white border-orange-500 shadow-sm' => (request('filter', request('status', '')) === $val),
               'bg-white text-stone-500 border-stone-200 hover:border-orange-300 hover:text-stone-700' => (request('filter', request('status', '')) !== $val),
           ])>{{ $lbl }}</a>
        @endforeach

        <select onchange="window.location=this.value"
                class="ml-auto px-3 py-1.5 rounded-full text-xs border border-stone-200 bg-white text-stone-500 outline-none cursor-pointer hover:border-orange-300 transition-colors">
            <option value="{{ request()->fullUrlWithQuery(['kategori' => '', 'page' => 1]) }}" {{ !request('kategori') ? 'selected' : '' }}>Semua Kategori</option>
            @foreach(['infrastruktur', 'lingkungan', 'keamanan', 'sosial', 'lainnya'] as $k)
            <option value="{{ request()->fullUrlWithQuery(['kategori' => $k, 'page' => 1]) }}" {{ request('kategori') === $k ? 'selected' : '' }}>
                {{ ucfirst($k) }}
            </option>
            @endforeach
        </select>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-stone-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-stone-50 border-b border-stone-200">
                    <th class="px-4 py-3 text-left text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wider">ID</th>
                    <th class="px-4 py-3 text-left text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wider">Tanggal</th>
                    <th class="px-4 py-3 text-left text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wider">Warga</th>
                    <th class="px-4 py-3 text-left text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wider">Laporan</th>
                    <th class="px-4 py-3 text-left text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wider">Kategori</th>
                    <th class="px-4 py-3 text-left text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-left text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wider">Petugas</th>
                    <th class="px-4 py-3 text-left text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($pengaduan as $p)
                @php
                $statusCfg = [
                    'menunggu'    => ['label'=>'Menunggu',    'pill'=>'bg-amber-100 text-amber-700'],
                    'proses'      => ['label'=>'Diproses',    'pill'=>'bg-blue-100 text-blue-700'],
                    'selesai'     => ['label'=>'Selesai',     'pill'=>'bg-emerald-100 text-emerald-700'],
                    'tidak_valid' => ['label'=>'Tidak Valid', 'pill'=>'bg-red-100 text-red-700'],
                ][$p->status] ?? ['label'=>ucfirst($p->status),'pill'=>'bg-stone-100 text-stone-600'];
                $katCfg = [
                    'infrastruktur' => 'bg-blue-100 text-blue-700',
                    'lingkungan'    => 'bg-emerald-100 text-emerald-700',
                    'keamanan'      => 'bg-red-100 text-red-700',
                    'sosial'        => 'bg-purple-100 text-purple-700',
                    'lainnya'       => 'bg-stone-100 text-stone-600',
                ][$p->kategori] ?? 'bg-stone-100 text-stone-600';
                @endphp
                <tr class="{{ $p->trashed() ? 'opacity-60 bg-stone-50/60' : 'hover:bg-orange-50/40' }} transition-colors">
                    <td class="px-4 py-3 text-xs text-stone-400 font-mono">#{{ $p->id_pengaduan }}</td>
                    <td class="px-4 py-3 text-xs text-stone-500 whitespace-nowrap">{{ $p->created_at->format('d M Y') }}</td>
                    <td class="px-4 py-3">
                        <span class="{{ $p->trashed() ? 'line-through text-stone-400' : 'text-stone-800 font-medium' }} text-sm">
                            {{ $p->masyarakat?->nama ?? 'Anonim' }}
                        </span>
                        @if($p->trashed())
                        <span class="ml-1 text-[0.625rem] text-red-400 font-medium bg-red-50 px-1.5 py-0.5 rounded-full">Dihapus</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-stone-500 max-w-[180px] truncate">
                        {{ Str::limit($p->isi_laporan, 55) }}
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-[0.6875rem] font-medium {{ $katCfg }}">
                            {{ ucfirst($p->kategori) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-[0.6875rem] font-medium {{ $statusCfg['pill'] }}">
                            {{ $statusCfg['label'] }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs text-stone-500">
                        {{ $p->petugasAssigned?->nama_petugas ?? '–' }}
                    </td>
                    <td class="px-4 py-3" x-data="{ open: false }">
                        <div class="relative">
                            <button @click="open = !open"
                                    class="px-2.5 py-1.5 text-xs text-stone-500 hover:text-stone-800 border border-stone-200 hover:border-stone-300 rounded-lg transition-colors bg-white cursor-pointer font-sans font-medium">
                                ···
                            </button>
                            <div x-show="open" @click.outside="open = false" x-cloak
                                 class="absolute right-0 top-8 z-20 bg-white rounded-xl shadow-lg border border-stone-200 p-1.5 min-w-[190px]">

                                {{-- Ubah status --}}
                                <div class="px-2 pt-1 pb-2">
                                    <p class="text-[0.625rem] font-semibold text-stone-400 uppercase tracking-widest mb-1.5">Ubah Status</p>
                                    <form method="POST" action="{{ route('admin.pengaduan.status', $p->id_pengaduan) }}" class="flex gap-1.5">
                                        @csrf @method('PATCH')
                                        <select name="status" class="flex-1 text-xs px-2 py-1.5 border border-stone-200 rounded-lg outline-none bg-stone-50 font-sans cursor-pointer">
                                            @foreach(['menunggu', 'proses', 'selesai', 'tidak_valid'] as $s)
                                            <option value="{{ $s }}" {{ $p->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="text-xs px-2.5 py-1.5 bg-stone-800 hover:bg-stone-900 text-white rounded-lg transition-colors border-0 cursor-pointer font-sans whitespace-nowrap">
                                            Ubah
                                        </button>
                                    </form>
                                </div>

                                {{-- Assign petugas --}}
                                <div class="px-2 py-2 border-t border-stone-100">
                                    <p class="text-[0.625rem] font-semibold text-stone-400 uppercase tracking-widest mb-1.5">Assign Petugas</p>
                                    <form method="POST" action="{{ route('admin.pengaduan.assign', $p->id_pengaduan) }}" class="flex gap-1.5">
                                        @csrf
                                        <select name="id_petugas" class="flex-1 text-xs px-2 py-1.5 border border-stone-200 rounded-lg outline-none bg-stone-50 font-sans cursor-pointer">
                                            <option value="">– Pilih –</option>
                                            @foreach($petugasList as $pt)
                                            <option value="{{ $pt->id_petugas }}" {{ $p->id_petugas == $pt->id_petugas ? 'selected' : '' }}>
                                                {{ $pt->nama_petugas }}
                                            </option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="text-xs px-2.5 py-1.5 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition-colors border-0 cursor-pointer font-sans whitespace-nowrap">
                                            Set
                                        </button>
                                    </form>
                                </div>

                                {{-- Hapus --}}
                                <div class="border-t border-stone-100 pt-1 mt-0.5">
                                    @if(!$p->trashed())
                                    <form method="POST" action="{{ route('admin.pengaduan.destroy', $p->id_pengaduan) }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" onclick="return confirm('Hapus pengaduan ini? Admin masih bisa melihatnya.')"
                                                class="w-full text-xs px-2.5 py-1.5 text-stone-500 hover:bg-stone-100 rounded-lg transition-colors border-0 cursor-pointer font-sans text-left">
                                            Hapus (soft)
                                        </button>
                                    </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.pengaduan.force-destroy', $p->id_pengaduan) }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" onclick="return confirm('HAPUS PERMANEN dari database? Tidak dapat dibatalkan!')"
                                                class="w-full text-xs px-2.5 py-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors border-0 cursor-pointer font-sans text-left">
                                            Hapus Permanen
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-16 text-center">
                        <svg class="w-10 h-10 text-stone-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="text-stone-400 text-sm font-light">Tidak ada pengaduan.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($pengaduan->hasPages())
        <div class="px-4 py-3 border-t border-stone-100 bg-stone-50">
            {{ $pengaduan->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
