@extends('layouts.app')
@section('title', 'Detail Feedback')
@section('content')
<div class="flex-1 overflow-y-auto p-6 space-y-5">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.feedback.index') }}"
           class="w-8 h-8 rounded-lg border border-stone-200 bg-white flex items-center justify-center text-stone-500 hover:bg-stone-100 transition-colors no-underline">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-stone-900">Detail Feedback</h1>
            <p class="text-sm text-stone-500 mt-0.5">dari {{ $feedback->masyarakat->nama ?? '—' }} &middot; {{ $feedback->created_at->format('d M Y, H:i') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">

        {{-- Left: feedback content --}}
        <div class="lg:col-span-3 space-y-4">
            <div class="bg-white rounded-xl border border-stone-200 p-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white text-sm font-bold shrink-0">
                        {{ strtoupper(substr($feedback->masyarakat->nama ?? '?', 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-stone-900">{{ $feedback->masyarakat->nama ?? '—' }}</p>
                        <p class="text-xs text-stone-400">{{ $feedback->created_at->diffForHumans() }}</p>
                    </div>
                    @php
                    $statusCfg = [
                        'pending' => ['label' => 'Pending', 'class' => 'bg-amber-100 text-amber-700'],
                        'sukses'  => ['label' => 'Sukses',  'class' => 'bg-emerald-100 text-emerald-700'],
                        'invalid' => ['label' => 'Invalid', 'class' => 'bg-red-100 text-red-600'],
                    ][$feedback->status] ?? ['label' => $feedback->status, 'class' => 'bg-stone-100 text-stone-500'];
                    @endphp
                    <span class="ml-auto inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusCfg['class'] }}">
                        {{ $statusCfg['label'] }}
                    </span>
                </div>

                <p class="text-sm text-stone-700 leading-relaxed whitespace-pre-wrap">{{ $feedback->isi }}</p>

                @if($feedback->foto)
                <div class="mt-4">
                    <img src="{{ Storage::url($feedback->foto) }}" alt="Foto feedback"
                         class="rounded-xl max-h-72 object-cover border border-stone-100 cursor-zoom-in"
                         onclick="this.classList.toggle('max-h-72')">
                </div>
                @endif
            </div>
        </div>

        {{-- Right: assignments panel --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Assign form --}}
            @php
            $hasActivePenugasan = $feedback->penugasan->contains(fn($p) => $p->status !== 'invalid');
            @endphp

            @if($hasActivePenugasan)
            {{-- Locked state --}}
            <div class="bg-stone-50 rounded-xl border border-stone-200 p-4">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-stone-200 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-stone-600">Penugasan Terkunci</p>
                        <p class="text-xs text-stone-400 mt-0.5 leading-relaxed">Sudah ada penugasan aktif. Penugasan baru hanya bisa dibuat setelah petugas menandai status <span class="font-semibold text-red-500">Invalid</span>.</p>
                    </div>
                </div>
            </div>
            @else
            {{-- Unlocked assign form --}}
            <div class="bg-white rounded-xl border border-stone-200 p-4" x-data="{ tipe: 'individual' }">
                <p class="text-sm font-semibold text-stone-800 mb-3">Tugaskan Feedback</p>

                <form method="POST" action="{{ route('admin.feedback.assign', $feedback->id) }}" class="space-y-3">
                    @csrf

                    {{-- Tipe --}}
                    <div class="flex gap-2">
                        <label class="flex-1 flex items-center gap-2 px-3 py-2.5 rounded-lg border cursor-pointer transition-colors"
                               :class="tipe === 'individual' ? 'border-orange-400 bg-orange-50' : 'border-stone-200 hover:bg-stone-50'">
                            <input type="radio" name="tipe" value="individual" x-model="tipe" class="accent-orange-500">
                            <span class="text-sm font-medium" :class="tipe === 'individual' ? 'text-orange-700' : 'text-stone-600'">Individual</span>
                        </label>
                        <label class="flex-1 flex items-center gap-2 px-3 py-2.5 rounded-lg border cursor-pointer transition-colors"
                               :class="tipe === 'global' ? 'border-blue-400 bg-blue-50' : 'border-stone-200 hover:bg-stone-50'">
                            <input type="radio" name="tipe" value="global" x-model="tipe" class="accent-blue-500">
                            <span class="text-sm font-medium" :class="tipe === 'global' ? 'text-blue-700' : 'text-stone-600'">Global (semua)</span>
                        </label>
                    </div>

                    {{-- Pilih petugas (only for individual) --}}
                    <div x-show="tipe === 'individual'" x-cloak>
                        <label class="block text-xs font-medium text-stone-500 mb-1.5">Pilih Petugas</label>
                        <select name="petugas_id"
                                class="w-full px-3 py-2.5 rounded-lg border border-stone-200 bg-stone-50 text-sm text-stone-900 outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-400/20 transition-all">
                            <option value="">— Pilih petugas —</option>
                            @foreach($petugasList as $p)
                            <option value="{{ $p->id_petugas }}">{{ $p->nama_petugas }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit"
                            class="w-full py-2.5 rounded-lg bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold border-0 cursor-pointer transition-colors">
                        Tugaskan
                    </button>
                </form>
            </div>
            @endif

            {{-- Existing penugasan --}}
            @if($feedback->penugasan->isNotEmpty())
            <div class="bg-white rounded-xl border border-stone-200 overflow-hidden">
                <div class="px-4 py-3 border-b border-stone-100">
                    <p class="text-sm font-semibold text-stone-800">Riwayat Penugasan</p>
                </div>
                <div class="divide-y divide-stone-100">
                    @foreach($feedback->penugasan as $pn)
                    @php
                    $pnStatusCfg = [
                        'pending' => ['label' => 'Pending', 'class' => 'bg-amber-100 text-amber-700'],
                        'proses'  => ['label' => 'Proses',  'class' => 'bg-blue-100 text-blue-700'],
                        'selesai' => ['label' => 'Selesai', 'class' => 'bg-emerald-100 text-emerald-700'],
                        'invalid' => ['label' => 'Invalid', 'class' => 'bg-red-100 text-red-600'],
                    ][$pn->status] ?? ['label' => $pn->status, 'class' => 'bg-stone-100 text-stone-500'];
                    @endphp
                    <div class="px-4 py-3 space-y-2" x-data="{ showStatus: false }">
                        <div class="flex items-center justify-between gap-2">
                            <div class="min-w-0">
                                @if($pn->tipe === 'global')
                                <p class="text-xs font-semibold text-blue-700 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                                    </svg>
                                    Global — semua petugas
                                </p>
                                @else
                                <p class="text-xs font-medium text-stone-700 truncate">{{ $pn->petugas->nama_petugas ?? '—' }}</p>
                                <p class="text-[0.6875rem] text-stone-400">Individual</p>
                                @endif
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[0.6875rem] font-medium {{ $pnStatusCfg['class'] }}">
                                    {{ $pnStatusCfg['label'] }}
                                </span>
                                @if($pn->tipe === 'global')
                                <button type="button" @click="showStatus = !showStatus"
                                        class="w-6 h-6 rounded bg-stone-100 hover:bg-stone-200 flex items-center justify-center border-0 cursor-pointer transition-colors"
                                        title="Ubah status">
                                    <svg class="w-3.5 h-3.5 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </button>
                                @endif
                                <form method="POST"
                                      action="{{ route('admin.feedback.penugasan.destroy', [$feedback->id, $pn->id]) }}"
                                      onsubmit="return confirm('Hapus penugasan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="w-6 h-6 rounded bg-red-50 hover:bg-red-100 flex items-center justify-center border-0 cursor-pointer transition-colors">
                                        <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                        @if($pn->pesan)
                        <p class="text-xs text-stone-500 bg-stone-50 rounded-lg px-3 py-2 leading-relaxed">{{ $pn->pesan }}</p>
                        @endif

                        {{-- Global status update form --}}
                        @if($pn->tipe === 'global')
                        <div x-show="showStatus" x-cloak class="border-t border-stone-100 pt-2 mt-2">
                            <form method="POST"
                                  action="{{ route('admin.feedback.penugasan.status', [$feedback->id, $pn->id]) }}"
                                  class="space-y-2">
                                @csrf
                                @method('PATCH')
                                <select name="status"
                                        class="w-full px-3 py-2 rounded-lg border border-stone-200 text-xs text-stone-700 outline-none focus:border-orange-400 transition-colors">
                                    @foreach(['pending','proses','selesai','invalid'] as $s)
                                    <option value="{{ $s }}" {{ $pn->status === $s ? 'selected' : '' }}>
                                        {{ ucfirst($s) }}
                                    </option>
                                    @endforeach
                                </select>
                                <textarea name="pesan" rows="2" placeholder="Pesan/alasan (wajib jika selesai/invalid)..."
                                          class="w-full px-3 py-2 rounded-lg border border-stone-200 text-xs text-stone-700 outline-none focus:border-orange-400 resize-none transition-colors">{{ $pn->pesan }}</textarea>
                                <button type="submit"
                                        class="w-full py-2 rounded-lg bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold border-0 cursor-pointer transition-colors">
                                    Simpan
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
