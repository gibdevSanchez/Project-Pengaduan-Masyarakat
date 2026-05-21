@extends('layouts.app')

@section('title', 'Manajemen Pengaduan')

@section('content')
<div class="flex-1 overflow-y-auto p-6 bg-stone-50">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-stone-900">Manajemen Pengaduan</h1>
            <p class="text-sm font-light text-stone-500 mt-0.5">{{ $pengaduan->total() }} pengaduan total</p>
        </div>
    </div>

    {{-- Filter bar --}}
    <div class="bg-white rounded-xl shadow-sm p-4 mb-4 flex flex-wrap gap-3 items-center">
        {{-- Status chips --}}
        <div class="flex gap-2 flex-wrap">
            @foreach(['' => 'Semua', 'menunggu' => 'Menunggu', 'proses' => 'Proses', 'selesai' => 'Selesai', 'deleted' => 'Dihapus'] as $val => $lbl)
            <a href="{{ request()->fullUrlWithQuery(['filter' => $val, 'status' => '', 'page' => 1]) }}"
               class="px-3 py-1.5 rounded-full text-xs font-medium border transition-colors
                      {{ (request('filter', request('status', '')) === $val) ? 'bg-orange-500 text-white border-orange-500' : 'bg-white text-stone-500 border-stone-200 hover:border-orange-400' }}">
                {{ $lbl }}
            </a>
            @endforeach
        </div>

        {{-- Kategori select --}}
        <select onchange="window.location=this.value"
                class="px-3 py-1.5 rounded-full text-xs border border-stone-200 bg-white text-stone-500 outline-none cursor-pointer">
            <option value="{{ request()->fullUrlWithQuery(['kategori' => '', 'page' => 1]) }}" {{ !request('kategori') ? 'selected' : '' }}>Semua Kategori</option>
            @foreach(['infrastruktur', 'lingkungan', 'keamanan', 'sosial', 'lainnya'] as $k)
            <option value="{{ request()->fullUrlWithQuery(['kategori' => $k, 'page' => 1]) }}" {{ request('kategori') === $k ? 'selected' : '' }}>
                {{ ucfirst($k) }}
            </option>
            @endforeach
        </select>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-stone-200 bg-stone-50">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-stone-500 uppercase tracking-wide">ID</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-stone-500 uppercase tracking-wide">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-stone-500 uppercase tracking-wide">Warga</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-stone-500 uppercase tracking-wide">Laporan</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-stone-500 uppercase tracking-wide">Kategori</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-stone-500 uppercase tracking-wide">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-stone-500 uppercase tracking-wide">Petugas</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-stone-500 uppercase tracking-wide">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($pengaduan as $p)
                @php
                $statusCfg = [
                    'menunggu' => ['label'=>'Menunggu','pill'=>'bg-amber-100 text-amber-800'],
                    'proses'   => ['label'=>'Diproses','pill'=>'bg-blue-100 text-blue-800'],
                    'selesai'  => ['label'=>'Selesai', 'pill'=>'bg-emerald-100 text-emerald-800'],
                ][$p->status] ?? ['label'=>$p->status,'pill'=>'bg-stone-100 text-stone-700'];
                $katCfg = [
                    'infrastruktur' => 'bg-blue-100 text-blue-700',
                    'lingkungan'    => 'bg-emerald-100 text-emerald-700',
                    'keamanan'      => 'bg-red-100 text-red-700',
                    'sosial'        => 'bg-purple-100 text-purple-700',
                    'lainnya'       => 'bg-stone-100 text-stone-600',
                ][$p->kategori] ?? 'bg-stone-100 text-stone-600';
                @endphp
                <tr class="{{ $p->trashed() ? 'opacity-60 bg-stone-50' : 'hover:bg-stone-50' }} transition-colors">
                    <td class="px-4 py-3 text-xs text-stone-400 font-mono">#{{ $p->id_pengaduan }}</td>
                    <td class="px-4 py-3 text-xs text-stone-500">{{ $p->tgl_pengaduan }}</td>
                    <td class="px-4 py-3">
                        <span class="{{ $p->trashed() ? 'line-through text-stone-400' : 'text-stone-900' }} text-sm">
                            {{ $p->masyarakat?->nama ?? 'Anonim' }}
                        </span>
                        @if($p->trashed())
                        <span class="ml-1 text-[0.625rem] text-red-400 font-medium">Dihapus</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-stone-600 max-w-[200px] truncate">
                        {{ Str::limit($p->isi_laporan, 60) }}
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
                    <td class="px-4 py-3 text-sm text-stone-500">
                        {{ $p->petugasAssigned?->nama_petugas ?? '–' }}
                    </td>
                    <td class="px-4 py-3" x-data="{ open: false }">
                        <div class="relative">
                            <button @click="open = !open"
                                    class="px-2 py-1 text-xs text-stone-500 hover:text-stone-900 border border-stone-200 rounded-lg transition-colors bg-white cursor-pointer font-sans">
                                ···
                            </button>
                            <div x-show="open" @click.outside="open = false" x-cloak
                                 class="absolute right-0 top-8 z-10 bg-white rounded-xl shadow-lg border border-stone-200 p-1 min-w-[180px]">

                                {{-- Ubah status --}}
                                <form method="POST" action="{{ route('admin.pengaduan.status', $p->id_pengaduan) }}" class="p-1">
                                    @csrf @method('PATCH')
                                    <select name="status" class="w-full text-xs px-2 py-1.5 border border-stone-200 rounded-lg mb-1 outline-none bg-stone-50 font-sans">
                                        @foreach(['menunggu', 'proses', 'selesai'] as $s)
                                        <option value="{{ $s }}" {{ $p->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="w-full text-xs px-2 py-1.5 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-lg transition-colors border-0 cursor-pointer font-sans">
                                        Ubah Status
                                    </button>
                                </form>

                                {{-- Assign petugas --}}
                                <form method="POST" action="{{ route('admin.pengaduan.assign', $p->id_pengaduan) }}" class="p-1 border-t border-stone-100">
                                    @csrf
                                    <select name="id_petugas" class="w-full text-xs px-2 py-1.5 border border-stone-200 rounded-lg mb-1 outline-none bg-stone-50 font-sans">
                                        <option value="">– Pilih petugas –</option>
                                        @foreach($petugasList as $pt)
                                        <option value="{{ $pt->id_petugas }}" {{ $p->id_petugas == $pt->id_petugas ? 'selected' : '' }}>
                                            {{ $pt->nama_petugas }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="w-full text-xs px-2 py-1.5 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition-colors border-0 cursor-pointer font-sans">
                                        Assign
                                    </button>
                                </form>

                                {{-- Hapus soft (hanya jika belum dihapus) --}}
                                @if(!$p->trashed())
                                <form method="POST" action="{{ route('admin.pengaduan.destroy', $p->id_pengaduan) }}" class="p-1 border-t border-stone-100">
                                    @csrf @method('DELETE')
                                    <button type="submit" onclick="return confirm('Hapus pengaduan ini? Admin masih bisa melihatnya.')"
                                            class="w-full text-xs px-2 py-1.5 text-stone-600 hover:bg-stone-100 rounded-lg transition-colors border-0 cursor-pointer font-sans text-left">
                                        Hapus (soft)
                                    </button>
                                </form>
                                @endif

                                {{-- Hapus permanen --}}
                                <form method="POST" action="{{ route('admin.pengaduan.force-destroy', $p->id_pengaduan) }}" class="p-1 border-t border-stone-100">
                                    @csrf @method('DELETE')
                                    <button type="submit" onclick="return confirm('HAPUS PERMANEN dari database? Tindakan ini tidak bisa dibatalkan!')"
                                            class="w-full text-xs px-2 py-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors border-0 cursor-pointer font-sans text-left">
                                        Hapus Permanen
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-12 text-center text-sm text-stone-400 font-light">Tidak ada pengaduan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if($pengaduan->hasPages())
        <div class="px-4 py-3 border-t border-stone-200">
            {{ $pengaduan->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
