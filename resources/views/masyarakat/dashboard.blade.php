@extends('layouts.masyarakat')

@section('title', 'Beranda')

@section('header')
<div class="shrink-0 bg-gradient-to-br from-orange-500 to-orange-700 relative overflow-hidden">
    {{-- Dot pattern --}}
    <div class="absolute inset-0 pointer-events-none opacity-[0.1]"
         style="background-image:radial-gradient(circle,#fff 1px,transparent 1px);background-size:20px 20px;"></div>

    <div class="relative px-4 pt-4 pb-0">
        <div class="flex items-start justify-between mb-3">
            {{-- Real-time clock --}}
           <div x-data="{
                time: '',
                date: '',
                init() {
                    this.update();
                    setInterval(() => this.update(), 60000);
                },
                update() {
                    const n = new Date();
                    const days  = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
                    const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                    this.time = String(n.getHours()).padStart(2,'0') + ':' +
                                String(n.getMinutes()).padStart(2,'0');
                    this.date = days[n.getDay()] + ', ' + n.getDate() + ' ' +
                                months[n.getMonth()] + ' ' + n.getFullYear();
                }
            }" x-init="init()">
                <p class="text-2xl font-bold text-white leading-none tracking-tight tabular-nums" x-text="time">00:00</p>
                <p class="text-[0.6875rem] text-white/65 font-light mt-1" x-text="date"></p>
            </div>
            {{-- Avatar --}}
            <div class="w-10 h-10 rounded-full bg-white/20 border border-white/30 flex items-center justify-center text-white font-bold text-base shrink-0">
                {{ strtoupper(substr($user->nama, 0, 1)) }}
            </div>
        </div>

       

        {{-- Stats strip --}}
        @php
        $total   = $pengaduan->count();
        $proses  = $pengaduan->whereIn('status', ['proses', 'menunggu'])->count();
        $selesai = $pengaduan->where('status', 'selesai')->count();
        @endphp
        <div class="grid grid-cols-3 gap-2 pb-5">
            @foreach([['Total', $total, 'Laporan'], ['Aktif', $proses, 'Diproses'], ['Selesai', $selesai, 'Tuntas']] as [$lbl, $count, $sub])
            <div class="bg-white/15 border border-white/20 rounded-2xl px-3 py-3 text-center">
                <p class="text-2xl font-black text-white leading-none">{{ $count }}</p>
                <p class="text-[0.6875rem] text-white/75 mt-1 font-medium">{{ $lbl }}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@section('content')
@php use Illuminate\Support\Str; @endphp
<div class="p-4">

    {{-- Lacak Pengaduan Anonim --}}
    <div x-data="{
            open: false,
            code: '',
            loading: false,
            error: null,
            result: null,
            statusCfg: {
                menunggu:    { label: 'Menunggu',        bg: '#FEF3C7', color: '#92400E', dot: '#F59E0B' },
                proses:      { label: 'Sedang Diproses', bg: '#EFF6FF', color: '#1E40AF', dot: '#3B82F6' },
                selesai:     { label: 'Selesai',         bg: '#ECFDF5', color: '#065F46', dot: '#10B981' },
                tidak_valid: { label: 'Tidak Valid',     bg: '#FEE2E2', color: '#991B1B', dot: '#EF4444' },
            },
            get statusInfo() {
                if (!this.result) return null;
                return this.statusCfg[this.result.status] || { label: this.result.status, bg: '#F3F4F6', color: '#374151', dot: '#9CA3AF' };
            },
            async searchTrack() {
                if (!this.code.trim()) return;
                this.loading = true; this.error = null; this.result = null;
                try {
                    const res = await fetch('/track?code=' + encodeURIComponent(this.code.trim().toUpperCase()), {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await res.json();
                    if (data.found) { this.result = data; }
                    else { this.error = 'Kode tidak ditemukan.'; }
                } catch { this.error = 'Terjadi kesalahan, coba lagi.'; }
                this.loading = false;
            }
        }" class="mb-4">

        {{-- Trigger chip --}}
        <button @click="open = !open"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-500 text-xs font-medium border-0 cursor-pointer transition-colors">
            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
            </svg>
            Lacak Pengaduan Anonim
            <svg class="w-3 h-3 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        {{-- Collapsed input --}}
        <div x-show="open" x-cloak
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="mt-2 bg-stone-50 border border-stone-200 rounded-xl p-3">
            <form @submit.prevent="searchTrack()" class="flex gap-2">
                <input type="text" x-model="code" required maxlength="12"
                       placeholder="LPR-XXXXXX"
                       @input="code = code.toUpperCase()"
                       class="flex-1 px-3 py-2 rounded-lg border border-stone-200 bg-white text-sm font-mono text-stone-700 outline-none focus:border-stone-400 focus:ring-2 focus:ring-stone-400/15 transition-colors tracking-wider">
                <button type="submit" :disabled="loading"
                        class="px-3 py-2 rounded-lg bg-stone-600 hover:bg-stone-700 text-white text-xs font-semibold border-0 cursor-pointer transition-colors shrink-0 disabled:opacity-60">
                    <span x-show="!loading">Cek</span>
                    <span x-show="loading" x-cloak>···</span>
                </button>
            </form>
            <p x-show="error" x-cloak x-text="error"
               class="mt-2 text-xs text-red-500 font-medium"></p>
        </div>

        {{-- Result modal (bottom sheet) --}}
        <template x-teleport="body">
            <div x-show="result" x-cloak
                 class="fixed inset-0 z-[60] bg-black/50 backdrop-blur-sm flex items-center justify-center p-4"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click.self="result = null">
                <div class="w-full max-w-[390px] bg-white rounded-2xl max-h-[82vh] overflow-y-auto shadow-2xl"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     @click.stop>

                    {{-- Header --}}
                    <div class="flex items-center justify-between px-5 py-3 border-b border-stone-100 shrink-0">
                        <div>
                            <p class="text-[0.6875rem] text-stone-400 font-light">Kode Lacak</p>
                            <p class="font-mono font-bold text-stone-800 tracking-wider text-sm mt-0.5"
                               x-text="result && result.tracking_code"></p>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <span x-show="statusInfo"
                                  :style="statusInfo ? `background-color:${statusInfo.bg};color:${statusInfo.color}` : ''"
                                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold">
                                <span :style="statusInfo ? `background-color:${statusInfo.dot};width:6px;height:6px;border-radius:9999px;display:inline-block` : ''"></span>
                                <span x-text="statusInfo && statusInfo.label"></span>
                            </span>
                            <button @click="result = null"
                                    class="w-7 h-7 rounded-full bg-stone-100 hover:bg-stone-200 flex items-center justify-center border-0 cursor-pointer transition-colors shrink-0">
                                <svg class="w-3.5 h-3.5 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Detail info --}}
                    <div class="px-5 py-4 space-y-3">
                        <div class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-stone-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            <div>
                                <p class="text-xs text-stone-400">Kategori</p>
                                <p class="text-sm font-medium text-stone-800 capitalize mt-0.5" x-text="result && result.kategori"></p>
                            </div>
                        </div>
                        <div class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-stone-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <div>
                                <p class="text-xs text-stone-400">Tanggal Lapor</p>
                                <p class="text-sm font-medium text-stone-800 mt-0.5" x-text="result && result.created_at"></p>
                            </div>
                        </div>
                        <template x-if="result && result.lokasi">
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-stone-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <div>
                                    <p class="text-xs text-stone-400">Lokasi</p>
                                    <p class="text-sm font-medium text-stone-800 mt-0.5" x-text="result.lokasi"></p>
                                </div>
                            </div>
                        </template>
                        <div class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-stone-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <div>
                                <p class="text-xs text-stone-400">Isi Laporan</p>
                                <p class="text-sm text-stone-700 mt-0.5 leading-relaxed" x-text="result && result.isi_laporan"></p>
                            </div>
                        </div>
                    </div>

                    {{-- Riwayat timeline --}}
                    <template x-if="result && result.klarifikasi && result.klarifikasi.length > 0">
                        <div class="border-t border-stone-100 px-5 py-4">
                            <p class="text-xs font-semibold text-stone-400 uppercase tracking-wider mb-4">Riwayat Penanganan</p>
                            <div class="relative pl-5 space-y-4">
                                <div class="absolute left-[7px] top-1 bottom-1 w-px bg-stone-200"></div>
                                <template x-for="(k, i) in result.klarifikasi" :key="i">
                                    <div class="relative">
                                        <div class="absolute -left-5 top-1 w-3.5 h-3.5 rounded-full border-2"
                                             :class="k.jenis === 'penutup'
                                                 ? 'bg-emerald-500 border-emerald-500'
                                                 : (k.jenis === 'respons_anonim'
                                                     ? 'bg-stone-500 border-stone-500'
                                                     : 'bg-orange-500 border-orange-500')"></div>
                                        <p class="text-[0.6875rem] text-stone-400 font-light mb-0.5" x-text="k.created_at"></p>
                                        <template x-if="k.jenis === 'respons_anonim'">
                                            <div class="rounded-xl border border-stone-200 bg-stone-50 px-3 py-2.5 mt-1">
                                                <p class="text-[0.625rem] font-semibold uppercase tracking-wide text-stone-400 mb-1">Respons Petugas</p>
                                                <p class="text-sm text-stone-700 leading-relaxed" x-text="k.pesan"></p>
                                                <img x-show="k.foto_url" :src="k.foto_url"
                                                     class="mt-2 w-full rounded-lg object-cover max-h-40 border border-stone-200">
                                            </div>
                                        </template>
                                        <template x-if="k.jenis !== 'respons_anonim'">
                                            <p class="text-sm text-stone-700 leading-relaxed" x-text="k.pesan"></p>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- Footer --}}
                    <div class="px-5 py-3 bg-stone-50 border-t border-stone-100">
                        <p class="text-xs text-stone-400 font-light"
                           x-text="result ? 'Terakhir diperbarui: ' + result.updated_at_diff : ''"></p>
                    </div>

                </div>
            </div>
        </template>
    </div>

    <div class="flex items-center justify-between mb-3.5">
        <p class="font-semibold text-[0.9375rem] text-stone-900">Pengaduan Terbaru</p>
        @if($pengaduan->isNotEmpty())
        <a href="{{ route('masyarakat.riwayat') }}" class="text-[0.8125rem] text-orange-500 font-medium no-underline hover:text-orange-600">Lihat semua →</a>
        @endif
    </div>

    @if($pengaduan->isEmpty())
    <div class="text-center py-14 px-6">
        <div class="w-16 h-16 rounded-2xl bg-orange-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <p class="font-semibold text-stone-800 mb-1.5">Belum ada pengaduan</p>
        <p class="text-sm font-light text-stone-500 leading-relaxed">Tekan tombol <span class="font-semibold text-orange-500">LAPOR</span> di bawah untuk membuat pengaduan pertama Anda.</p>
    </div>
    @else
    <div class="flex flex-col gap-2.5">
        @foreach($pengaduan->take(5) as $p)
        @php
        $statusCfg = [
            'menunggu'    => ['label'=>'Menunggu',    'pill'=>'bg-amber-100 text-amber-700',   'dot'=>'bg-amber-400',   'border'=>'border-l-amber-400'],
            'proses'      => ['label'=>'Diproses',    'pill'=>'bg-blue-100 text-blue-700',     'dot'=>'bg-blue-400',    'border'=>'border-l-blue-400'],
            'selesai'     => ['label'=>'Selesai',     'pill'=>'bg-emerald-100 text-emerald-700','dot'=>'bg-emerald-400','border'=>'border-l-emerald-400'],
            'tidak_valid' => ['label'=>'Tidak Valid', 'pill'=>'bg-red-100 text-red-700',       'dot'=>'bg-red-400',     'border'=>'border-l-red-400'],
        ][$p->status] ?? ['label'=>$p->status,'pill'=>'bg-stone-100 text-stone-600','dot'=>'bg-stone-400','border'=>'border-l-stone-300'];
        @endphp
        <a href="{{ route('masyarakat.pengaduan.show', $p->id_pengaduan) }}"
           class="block bg-white rounded-xl p-4 shadow-sm border-l-[3px] {{ $statusCfg['border'] }} no-underline hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between gap-3 mb-2.5">
                <p class="text-sm text-stone-800 leading-relaxed flex-1 line-clamp-2">{{ Str::limit($p->isi_laporan, 80) }}</p>
                <span class="shrink-0 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.6875rem] font-medium {{ $statusCfg['pill'] }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $statusCfg['dot'] }}"></span>
                    {{ $statusCfg['label'] }}
                </span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-xs text-stone-400 font-light">{{ $p->created_at->format('d M Y') }}</span>
                <span class="text-xs {{ $p->tanggapan->count() > 0 ? 'text-orange-500 font-medium' : 'text-stone-400' }}">
                    {{ $p->tanggapan->count() > 0 ? $p->tanggapan->count().' tanggapan' : 'Belum ada tanggapan' }}
                </span>
            </div>
        </a>
        @endforeach
    </div>
    @endif
</div>
@endsection
