@extends('layouts.app')
@section('title', 'Feedback Masuk')
@section('content')
<div class="flex-1 overflow-y-auto p-6 space-y-5">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-stone-900">Feedback Masuk</h1>
            <p class="text-sm text-stone-500 mt-0.5">Semua feedback yang dikirim oleh masyarakat</p>
        </div>
        <span class="inline-flex items-center gap-1.5 rounded-lg bg-stone-100 px-3 py-1.5 text-sm font-medium text-stone-600">
            {{ $feedback->total() }} total
        </span>
    </div>

    <div class="bg-white rounded-xl border border-stone-200 overflow-hidden divide-y divide-stone-100">
        @forelse($feedback as $fb)
        @php
        $statusCfg = [
            'pending' => ['label' => 'Pending',  'class' => 'bg-amber-100 text-amber-700'],
            'sukses'  => ['label' => 'Sukses',   'class' => 'bg-emerald-100 text-emerald-700'],
            'invalid' => ['label' => 'Invalid',  'class' => 'bg-red-100 text-red-600'],
        ][$fb->status] ?? ['label' => $fb->status, 'class' => 'bg-stone-100 text-stone-500'];
        $penugasanCount = $fb->penugasan->count();
        @endphp
        <a href="{{ route('admin.feedback.show', $fb->id) }}"
           class="flex items-start gap-4 px-5 py-4 hover:bg-stone-50 transition-colors no-underline group">

            {{-- Avatar --}}
            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white text-xs font-bold shrink-0 mt-0.5">
                {{ strtoupper(substr($fb->masyarakat->nama ?? '?', 0, 1)) }}
            </div>

            {{-- Content --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-3 mb-1">
                    <p class="text-sm font-semibold text-stone-900 truncate">{{ $fb->masyarakat->nama ?? '—' }}</p>
                    <span class="text-xs text-stone-400 shrink-0">{{ $fb->created_at->format('d M Y, H:i') }}</span>
                </div>
                <p class="text-sm text-stone-600 line-clamp-2 leading-relaxed">{{ $fb->isi }}</p>
                <div class="flex items-center gap-2 mt-1.5">
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[0.6875rem] font-medium {{ $statusCfg['class'] }}">
                        {{ $statusCfg['label'] }}
                    </span>
                    @if($fb->foto)
                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[0.6875rem] font-medium bg-stone-100 text-stone-500">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Foto
                    </span>
                    @endif
                    @if($penugasanCount > 0)
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[0.6875rem] font-medium bg-blue-100 text-blue-700">
                        {{ $penugasanCount }} penugasan
                    </span>
                    @endif
                </div>
            </div>

            {{-- Chevron --}}
            <svg class="w-4 h-4 text-stone-300 group-hover:text-stone-400 shrink-0 mt-1 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
        @empty
        <div class="px-6 py-16 text-center">
            <div class="w-14 h-14 rounded-2xl bg-stone-100 flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-stone-700">Belum ada feedback masuk</p>
            <p class="text-xs text-stone-400 mt-1">Feedback dari masyarakat akan muncul di sini</p>
        </div>
        @endforelse
    </div>

    {{ $feedback->links() }}
</div>
@endsection
