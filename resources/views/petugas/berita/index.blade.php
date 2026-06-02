@extends('layouts.app')
@section('title', 'Berita Saya')
@section('content')
<div class="flex-1 overflow-y-auto p-6 space-y-5">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-stone-900">Berita Saya</h1>
            <p class="text-sm text-stone-500 mt-0.5">Kelola berita yang kamu tulis</p>
        </div>
        <a href="{{ route('petugas.berita.create') }}"
           class="inline-flex items-center gap-1.5 rounded-lg bg-orange-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-orange-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Buat Berita
        </a>
    </div>

    <div class="bg-white rounded-xl border border-stone-200 overflow-hidden">
        <table class="min-w-full divide-y divide-stone-100">
            <thead class="bg-stone-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Judul</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Kategori</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-50">
                @forelse($berita as $b)
                @php
                $now = now();
                if (!$b->is_published) {
                    $badge = ['label' => 'Draft', 'class' => 'bg-stone-100 text-stone-500'];
                } elseif ($b->mulai_tayang && $b->mulai_tayang->gt($now)) {
                    $badge = ['label' => 'Dijadwalkan', 'class' => 'bg-blue-100 text-blue-700', 'sub' => $b->mulai_tayang->format('d M Y, H:i')];
                } elseif ($b->selesai_tayang && $b->selesai_tayang->lt($now)) {
                    $badge = ['label' => 'Kadaluarsa', 'class' => 'bg-red-100 text-red-600'];
                } else {
                    $badge = ['label' => 'Aktif', 'class' => 'bg-emerald-100 text-emerald-700'];
                    if ($b->selesai_tayang) $badge['sub'] = 'Berakhir ' . $b->selesai_tayang->format('d M Y');
                }
                @endphp
                <tr class="hover:bg-stone-50 transition-colors">
                    <td class="px-5 py-3 text-sm font-medium text-stone-900 max-w-xs truncate">{{ $b->judul }}</td>
                    <td class="px-5 py-3 text-xs text-stone-500 capitalize">{{ $b->kategori }}</td>
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $badge['class'] }}">
                            {{ $badge['label'] }}
                        </span>
                        @isset($badge['sub'])
                        <p class="text-[0.6875rem] text-stone-400 mt-0.5">{{ $badge['sub'] }}</p>
                        @endisset
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('petugas.berita.edit', $b->id) }}"
                               class="text-xs font-medium text-orange-600 hover:text-orange-800">Edit</a>
                            <form method="POST" action="{{ route('petugas.berita.destroy', $b->id) }}"
                                  onsubmit="return confirm('Hapus berita ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-medium text-red-500 hover:text-red-700 cursor-pointer">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-5 py-10 text-center text-sm text-stone-400">
                        Belum ada berita. <a href="{{ route('petugas.berita.create') }}" class="text-orange-500 hover:underline">Buat yang pertama</a>.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($berita->hasPages())
        <div class="border-t border-stone-100 px-5 py-3">{{ $berita->links() }}</div>
        @endif
    </div>

</div>
@endsection
