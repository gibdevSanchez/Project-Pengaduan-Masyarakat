@extends('layouts.app')

@section('title', 'Expired Data')

@section('content')
<div class="flex-1 overflow-y-auto p-6 bg-stone-50">

    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-4 mb-5">
        <div>
            <h1 class="text-xl font-bold text-stone-900 tracking-tight">Expired Data</h1>
            <p class="text-sm font-light text-stone-500 mt-0.5">
                Data yang telah dihapus (soft-delete). Hapus permanen untuk membebaskan ruang database.
            </p>
        </div>

        @if($data->total() > 0)
        <form method="POST" action="{{ route('admin.expired.destroy-all') }}"
              onsubmit="return confirm('Hapus SEMUA {{ $data->total() }} data secara permanen? Tindakan ini tidak bisa dibatalkan.')">
            @csrf @method('DELETE')
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white
                           hover:bg-red-700 transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                Hapus Semua ({{ $data->total() }})
            </button>
        </form>
        @endif
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="mb-4 rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700 ring-1 ring-emerald-200">
        {{ session('success') }}
    </div>
    @endif

    {{-- Empty state --}}
    @if($data->isEmpty())
    <div class="rounded-2xl bg-white p-16 text-center shadow-sm border border-stone-100">
        <div class="w-16 h-16 rounded-2xl bg-stone-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </div>
        <p class="font-semibold text-stone-700 mb-1">Tidak ada expired data</p>
        <p class="text-sm text-stone-400 font-light">Database bersih. Tidak ada data yang menunggu penghapusan permanen.</p>
    </div>

    @else
    {{-- Table --}}
    <div class="rounded-2xl bg-white shadow-sm border border-stone-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-stone-50 border-b border-stone-200">
                    <th class="px-4 py-3 text-left text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wider">ID</th>
                    <th class="px-4 py-3 text-left text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wider">Isi Laporan</th>
                    <th class="px-4 py-3 text-left text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wider">Status Terakhir</th>
                    <th class="px-4 py-3 text-left text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wider">Pengirim</th>
                    <th class="px-4 py-3 text-left text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wider">Dihapus Pada</th>
                    <th class="px-4 py-3 text-left text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @foreach($data as $item)
                @php
                $statusCfg = [
                    'menunggu'    => 'bg-amber-100 text-amber-700',
                    'proses'      => 'bg-blue-100 text-blue-700',
                    'selesai'     => 'bg-emerald-100 text-emerald-700',
                    'tidak_valid' => 'bg-red-100 text-red-700',
                ][$item->status] ?? 'bg-stone-100 text-stone-600';
                @endphp
                <tr class="hover:bg-stone-50/60 transition-colors">
                    <td class="px-4 py-3 font-mono text-xs text-stone-400">#{{ $item->id_pengaduan }}</td>
                    <td class="px-4 py-3 text-xs text-stone-600 max-w-[220px] truncate">
                        {{ $item->isi_laporan }}
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-[0.6875rem] font-medium {{ $statusCfg }}">
                            {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-stone-600">
                        {{ $item->masyarakat?->nama ?? 'Anonim' }}
                    </td>
                    <td class="px-4 py-3 text-xs text-stone-500 whitespace-nowrap">
                        {{ $item->deleted_at?->format('d M Y, H:i') ?? '–' }}
                    </td>
                    <td class="px-4 py-3">
                        <form method="POST"
                              action="{{ route('admin.expired.destroy', $item->id_pengaduan) }}"
                              onsubmit="return confirm('Hapus permanen pengaduan #{{ $item->id_pengaduan }}? Tindakan ini tidak bisa dibatalkan.')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="text-xs font-medium text-red-600 hover:text-red-800 transition-colors">
                                Hapus Permanen
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($data->hasPages())
        <div class="border-t border-stone-100 px-4 py-3 bg-stone-50">
            {{ $data->links() }}
        </div>
        @endif
    </div>
    @endif

</div>
@endsection
