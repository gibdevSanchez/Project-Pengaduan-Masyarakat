@extends('layouts.app')
@section('title', 'Feedback')
@section('content')
<div class="flex-1 overflow-y-auto p-6 space-y-6">

    <div>
        <h1 class="text-xl font-bold text-stone-900">Feedback</h1>
        <p class="text-sm text-stone-500 mt-0.5">Tugas individual dan pengumuman global dari admin</p>
    </div>

    {{-- Individual assignments --}}
    <div class="space-y-3">
        <p class="text-xs font-semibold text-stone-400 uppercase tracking-wider">Tugas Saya</p>

        @forelse($individual as $pn)
        @php
        $statusCfg = [
            'pending' => ['label' => 'Pending', 'class' => 'bg-amber-100 text-amber-700', 'dot' => 'bg-amber-400'],
            'proses'  => ['label' => 'Proses',  'class' => 'bg-blue-100 text-blue-700',   'dot' => 'bg-blue-400'],
            'selesai' => ['label' => 'Selesai', 'class' => 'bg-emerald-100 text-emerald-700','dot' => 'bg-emerald-400'],
            'invalid' => ['label' => 'Invalid', 'class' => 'bg-red-100 text-red-600',     'dot' => 'bg-red-400'],
        ][$pn->status] ?? ['label' => $pn->status, 'class' => 'bg-stone-100 text-stone-500', 'dot' => 'bg-stone-400'];
        $isFinal = in_array($pn->status, ['selesai', 'invalid']);
        @endphp

        <div class="bg-white rounded-xl border border-stone-200 overflow-hidden" x-data="{ showAction: false }">
            <div class="p-4">
                <div class="flex items-start justify-between gap-3 mb-2.5">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white text-xs font-bold shrink-0">
                            {{ strtoupper(substr($pn->feedback->masyarakat->nama ?? '?', 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-stone-900 truncate">{{ $pn->feedback->masyarakat->nama ?? '—' }}</p>
                            <p class="text-xs text-stone-400">{{ $pn->feedback->created_at->format('d M Y') }}</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[0.6875rem] font-medium shrink-0 {{ $statusCfg['class'] }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $statusCfg['dot'] }}"></span>
                        {{ $statusCfg['label'] }}
                    </span>
                </div>

                <p class="text-sm text-stone-600 leading-relaxed line-clamp-3">{{ $pn->feedback->isi }}</p>

                @if($pn->pesan)
                <div class="mt-2.5 px-3 py-2 bg-stone-50 rounded-lg border border-stone-100">
                    <p class="text-xs text-stone-500 leading-relaxed">{{ $pn->pesan }}</p>
                </div>
                @endif

                <div class="flex items-center gap-2 mt-3">
                    @if(!$isFinal)
                    <button type="button" @click="showAction = !showAction"
                            class="flex-1 py-2 rounded-lg border border-stone-200 bg-stone-50 hover:bg-stone-100 text-stone-600 text-xs font-semibold cursor-pointer transition-colors">
                        Ubah Status
                    </button>
                    @else
                    <form method="POST" action="{{ route('petugas.feedback.hide', $pn->id) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="px-4 py-2 rounded-lg border border-stone-200 bg-stone-50 hover:bg-stone-100 text-stone-500 text-xs font-medium cursor-pointer transition-colors">
                            Sembunyikan
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            {{-- Chat-bar action panel --}}
            @if(!$isFinal)
            <div x-show="showAction" x-cloak
                 class="border-t border-stone-100 bg-stone-50 px-3 py-3"
                 x-data="{
                     status: '{{ $pn->status === 'pending' ? 'proses' : $pn->status }}',
                     needsMsg() { return this.status === 'selesai' || this.status === 'invalid'; }
                 }">
                <form method="POST" action="{{ route('petugas.feedback.status', $pn->id) }}">
                    @csrf
                    @method('PATCH')
                    <div class="flex items-end gap-2">
                        {{-- Left: message input --}}
                        <div class="flex-1 min-w-0">
                            <textarea name="pesan" rows="1"
                                      :placeholder="needsMsg() ? 'Pesan wajib diisi untuk status ini...' : 'Pesan/catatan (opsional)...'"
                                      :required="needsMsg()"
                                      class="w-full px-3 py-2.5 rounded-xl border border-stone-200 bg-white text-xs text-stone-700 outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-400/20 resize-none transition-all leading-relaxed"
                                      style="field-sizing: content; min-height: 2.375rem; max-height: 6rem;"></textarea>
                        </div>
                        {{-- Right: status dropdown + send --}}
                        <div class="flex items-center gap-1.5 shrink-0">
                            <select name="status" x-model="status"
                                    class="h-9 pl-2.5 pr-7 rounded-xl border border-stone-200 bg-white text-xs font-semibold text-stone-700 outline-none focus:border-orange-400 cursor-pointer appearance-none transition-colors"
                                    :style="status === 'proses' ? 'color: #1d4ed8; border-color: #93c5fd; background: #eff6ff;'
                                          : status === 'selesai' ? 'color: #065f46; border-color: #6ee7b7; background: #ecfdf5;'
                                          : 'color: #991b1b; border-color: #fca5a5; background: #fff1f2;'">
                                <option value="proses">Proses</option>
                                <option value="selesai">Selesai</option>
                                <option value="invalid">Invalid</option>
                            </select>
                            <button type="submit"
                                    class="h-9 w-9 rounded-xl flex items-center justify-center border-0 cursor-pointer transition-all shrink-0"
                                    :class="needsMsg() ? 'bg-orange-500 hover:bg-orange-600 shadow-sm shadow-orange-200' : 'bg-orange-500 hover:bg-orange-600 shadow-sm shadow-orange-200'">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <p x-show="needsMsg()" class="text-[0.6875rem] text-amber-600 mt-1.5 flex items-center gap-1">
                        <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Pesan wajib diisi untuk status Selesai / Invalid
                    </p>
                </form>
            </div>
            @endif
        </div>
        @empty
        <div class="bg-white rounded-xl border border-stone-200 px-5 py-12 text-center">
            <div class="w-12 h-12 rounded-2xl bg-stone-100 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <p class="text-sm text-stone-500">Tidak ada tugas individual untuk Anda</p>
        </div>
        @endforelse
    </div>

    {{-- Global announcements --}}
    <div class="space-y-3">
        <p class="text-xs font-semibold text-stone-400 uppercase tracking-wider">Pengumuman Global</p>

        @forelse($global as $pn)
        @php
        $gStatusCfg = [
            'pending' => ['label' => 'Pending', 'class' => 'bg-amber-100 text-amber-700'],
            'proses'  => ['label' => 'Proses',  'class' => 'bg-blue-100 text-blue-700'],
            'selesai' => ['label' => 'Selesai', 'class' => 'bg-emerald-100 text-emerald-700'],
            'invalid' => ['label' => 'Invalid', 'class' => 'bg-red-100 text-red-600'],
        ][$pn->status] ?? ['label' => $pn->status, 'class' => 'bg-stone-100 text-stone-500'];
        @endphp

        <div class="bg-blue-50 rounded-xl border border-blue-100 p-4">
            <div class="flex items-start justify-between gap-3 mb-2">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-xs font-bold shrink-0">
                        {{ strtoupper(substr($pn->feedback->masyarakat->nama ?? '?', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-stone-900 truncate">{{ $pn->feedback->masyarakat->nama ?? '—' }}</p>
                        <p class="text-xs text-stone-400">{{ $pn->feedback->created_at->format('d M Y') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 shrink-0">
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[0.6875rem] font-medium {{ $gStatusCfg['class'] }}">
                        {{ $gStatusCfg['label'] }}
                    </span>
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[0.6875rem] font-medium bg-blue-200 text-blue-800">
                        Global
                    </span>
                </div>
            </div>
            <p class="text-sm text-stone-600 leading-relaxed line-clamp-3">{{ $pn->feedback->isi }}</p>
            @if($pn->pesan)
            <div class="mt-2.5 px-3 py-2 bg-white/70 rounded-lg border border-blue-200">
                <p class="text-xs text-stone-500 leading-relaxed">{{ $pn->pesan }}</p>
            </div>
            @endif
            <p class="text-[0.6875rem] text-blue-500 mt-2 font-medium">Hanya admin yang dapat mengubah status ini</p>
        </div>
        @empty
        <div class="bg-white rounded-xl border border-stone-200 px-5 py-10 text-center">
            <p class="text-sm text-stone-500">Tidak ada pengumuman global saat ini</p>
        </div>
        @endforelse
    </div>

</div>
@endsection
