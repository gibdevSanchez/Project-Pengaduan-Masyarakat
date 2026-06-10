@extends('layouts.app')

@section('title', 'Utilitas')

@section('content')
<div class="flex-1 overflow-y-auto p-6 bg-stone-50">

    <div>
        <h1 class="text-xl font-bold text-stone-900 tracking-tight">Utilitas</h1>
        <p class="text-sm font-light text-stone-500 mt-0.5">Pengaturan sistem, monitoring, dan data operasional.</p>
    </div>

    @if(session('success'))
    <div class="mt-4 rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700 ring-1 ring-emerald-200">
        {{ session('success') }}
    </div>
    @endif

    <form method="POST" action="{{ route('admin.utilitas.save') }}" class="mt-6 space-y-6">
        @csrf

        {{-- Closing Template Card --}}
        <div class="rounded-2xl bg-white p-6 shadow-sm border border-stone-100">
            <h2 class="text-sm font-semibold text-stone-800">Template Penutup Pengaduan</h2>
            <p class="mt-1 text-xs text-stone-400 font-light">
                Teks ini secara otomatis dikirim ke thread klarifikasi saat petugas menyelesaikan pengaduan.
                Petugas dapat menambahkan pesan personal di bawahnya.
            </p>

            <div class="mt-5">
                <textarea name="closing_template" rows="6"
                          class="w-full rounded-xl border border-stone-200 px-3 py-2.5 text-sm text-stone-700
                                 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-100
                                 @error('closing_template') border-red-400 @enderror"
                          placeholder="Masukkan template pesan penutup...">{{ old('closing_template', $closingTemplate) }}</textarea>

                @error('closing_template')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- SLA Settings Card --}}
        <div class="rounded-2xl bg-white p-6 shadow-sm border border-stone-100">
            <h2 class="text-sm font-semibold text-stone-800">Batas Waktu SLA per Kategori</h2>
            <p class="mt-1 text-xs text-stone-400 font-light">
                Pengaduan yang belum selesai melebihi batas jam ini akan ditandai sebagai "Lewat SLA" di dashboard dan daftar pengaduan.
            </p>

            <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach([
                    'keamanan'      => ['label' => 'Keamanan',      'hex' => '#DC2626', 'bg' => '#FEF2F2', 'iborder' => '#FECACA', 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
                    'infrastruktur' => ['label' => 'Infrastruktur', 'hex' => '#2563EB', 'bg' => '#EFF6FF', 'iborder' => '#BFDBFE', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                    'lingkungan'    => ['label' => 'Lingkungan',    'hex' => '#059669', 'bg' => '#ECFDF5', 'iborder' => '#6EE7B7', 'icon' => 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                    'sosial'        => ['label' => 'Sosial',        'hex' => '#7C3AED', 'bg' => '#F5F3FF', 'iborder' => '#C4B5FD', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                    'lainnya'       => ['label' => 'Lainnya',       'hex' => '#78716C', 'bg' => '#FAFAF9', 'iborder' => '#D6D3D1', 'icon' => 'M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z'],
                ] as $key => $cfg)
                <div class="rounded-xl border border-stone-100 bg-white p-4 flex items-center gap-4"
                     style="border-left: 4px solid {{ $cfg['hex'] }}">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0"
                         style="background: {{ $cfg['bg'] }}">
                        <svg class="w-4 h-4" style="color: {{ $cfg['hex'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $cfg['icon'] }}"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <label class="block text-[0.6875rem] font-semibold mb-1.5" style="color: {{ $cfg['hex'] }}">{{ $cfg['label'] }}</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="sla_{{ $key }}" value="{{ old('sla_' . $key, $slaHours[$key]) }}"
                                   min="1" max="720" required
                                   style="border-color: {{ $cfg['iborder'] }}; color: {{ $cfg['hex'] }}"
                                   class="w-16 px-2 py-1.5 rounded-lg border bg-white text-sm outline-none font-sans text-center font-bold focus:ring-2 focus:ring-orange-100 focus:border-orange-400">
                            <span class="text-xs font-light" style="color: {{ $cfg['hex'] }}">jam</span>
                        </div>
                        @error('sla_' . $key)
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <button type="submit"
                class="inline-flex items-center gap-2 rounded-xl bg-orange-500 px-5 py-2.5 text-sm font-semibold text-white
                       hover:bg-orange-600 transition-colors focus:outline-none focus:ring-2 focus:ring-orange-400">
            Simpan Pengaturan
        </button>
    </form>

    {{-- ═══════════════════════════════════════════════════════════════
         HEALTH CHECK
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="mt-8" x-data="healthWidget()" x-init="load()">
        <div class="flex items-center justify-between mb-3">
            <div>
                <h2 class="text-sm font-semibold text-stone-800">Kesehatan Sistem</h2>
                <p class="text-xs text-stone-400 font-light mt-0.5">Status koneksi, storage, dan antrian secara real-time.</p>
            </div>
            <button @click="load()" :disabled="loading"
                    class="text-xs text-orange-500 hover:text-orange-600 font-medium disabled:opacity-50 bg-transparent border-0 cursor-pointer font-sans flex items-center gap-1">
                <svg :class="loading ? 'animate-spin' : ''" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Refresh
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

            {{-- DB Card --}}
            <div class="rounded-2xl bg-white p-5 border shadow-sm"
                 :class="data.db?.ok ? 'border-emerald-100' : 'border-red-200'">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center"
                         :style="data.db?.ok ? 'background:#ECFDF5' : 'background:#FEE2E2'">
                        <svg class="w-4 h-4" :style="data.db?.ok ? 'color:#059669' : 'color:#DC2626'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wide">Database</p>
                        <p class="text-sm font-bold" :style="data.db?.ok ? 'color:#059669' : 'color:#DC2626'" x-text="data.db?.label ?? '—'"></p>
                    </div>
                </div>
                <p class="text-xs text-stone-400 font-light" x-text="data.db ? (data.db.driver + ' · ' + data.db.name) : 'Memuat...'"></p>
            </div>

            {{-- Storage Card --}}
            <div class="rounded-2xl bg-white p-5 border shadow-sm"
                 :class="data.storage?.ok ? 'border-stone-100' : 'border-amber-200'">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center"
                         :style="data.storage?.ok ? 'background:#F5F5F4' : 'background:#FFFBEB'">
                        <svg class="w-4 h-4" :style="data.storage?.ok ? 'color:#78716C' : 'color:#D97706'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wide">Storage</p>
                        <p class="text-sm font-bold text-stone-800" x-text="data.storage ? (data.storage.used_mb + ' MB terpakai') : '—'"></p>
                    </div>
                </div>
                <template x-if="data.storage">
                    <div>
                        <div class="w-full h-1.5 bg-stone-100 rounded-full overflow-hidden mb-1.5">
                            <div class="h-full rounded-full transition-all"
                                 :style="`width:${data.storage.used_pct}%;background:${data.storage.used_pct > 85 ? '#EF4444' : data.storage.used_pct > 60 ? '#F59E0B' : '#10B981'}`"></div>
                        </div>
                        <p class="text-xs text-stone-400 font-light" x-text="`${data.storage.used_pct}% dari ${data.storage.total_gb} GB · sisa ${data.storage.free_gb} GB`"></p>
                    </div>
                </template>
            </div>

            {{-- Queue Card --}}
            <div class="rounded-2xl bg-white p-5 border shadow-sm"
                 :class="data.queue?.ok ? 'border-stone-100' : 'border-red-200'">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center"
                         :style="data.queue?.ok ? 'background:#F5F5F4' : 'background:#FEE2E2'">
                        <svg class="w-4 h-4" :style="data.queue?.ok ? 'color:#78716C' : 'color:#DC2626'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 10h16M4 14h8"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wide">Queue</p>
                        <p class="text-sm font-bold" :style="data.queue?.ok ? 'color:#059669' : 'color:#DC2626'"
                           x-text="data.queue ? (data.queue.ok ? 'Normal' : 'Ada Masalah') : '—'"></p>
                    </div>
                </div>
                <p class="text-xs text-stone-400 font-light"
                   x-text="data.queue ? `Pending: ${data.queue.pending} · Gagal: ${data.queue.failed}` : 'Memuat...'"></p>
            </div>

            {{-- App Card --}}
            <div class="rounded-2xl bg-white p-5 border shadow-sm"
                 :class="(data.app?.recent_errors ?? 0) > 0 ? 'border-amber-200' : 'border-stone-100'">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center"
                         :style="(data.app?.recent_errors ?? 0) > 0 ? 'background:#FFFBEB' : 'background:#F5F5F4'">
                        <svg class="w-4 h-4" :style="(data.app?.recent_errors ?? 0) > 0 ? 'color:#D97706' : 'color:#78716C'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wide">Aplikasi</p>
                        <p class="text-sm font-bold text-stone-800"
                           x-text="data.app ? `PHP ${data.app.php_version}` : '—'"></p>
                    </div>
                </div>
                <p class="text-xs text-stone-400 font-light"
                   x-text="data.app ? `Laravel ${data.app.laravel_version} · ${data.app.environment}` : 'Memuat...'"></p>
                <p x-show="(data.app?.recent_errors ?? 0) > 0"
                   class="mt-1 text-xs text-amber-600 font-medium"
                   x-text="`⚠ ${data.app?.recent_errors} error dalam 24 jam terakhir`"></p>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         MAP — SEBARAN LOKASI LAPORAN
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="mt-8" x-data="mapWidget()" x-init="load()">
        <div class="flex items-center justify-between mb-3">
            <div>
                <h2 class="text-sm font-semibold text-stone-800">Sebaran Lokasi Laporan</h2>
                <p class="text-xs text-stone-400 font-light mt-0.5">
                    Pengaduan yang memiliki data koordinat GPS — <span x-text="points.length"></span> titik terpetakan.
                </p>
            </div>
            <div class="flex items-center gap-2 text-[0.625rem] font-medium text-stone-500">
                <span class="flex items-center gap-1"><span class="inline-block w-2 h-2 rounded-full bg-blue-500"></span>Infrastruktur</span>
                <span class="flex items-center gap-1"><span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>Lingkungan</span>
                <span class="flex items-center gap-1"><span class="inline-block w-2 h-2 rounded-full bg-red-500"></span>Keamanan</span>
                <span class="flex items-center gap-1"><span class="inline-block w-2 h-2 rounded-full bg-purple-500"></span>Sosial</span>
                <span class="flex items-center gap-1"><span class="inline-block w-2 h-2 rounded-full bg-stone-400"></span>Lainnya</span>
            </div>
        </div>

        <div class="rounded-2xl bg-white border border-stone-100 shadow-sm overflow-hidden">
            <div id="utilitas-map" class="w-full h-72"></div>
            <div x-show="loading" class="absolute inset-0 flex items-center justify-center bg-white/80 rounded-2xl">
                <p class="text-sm text-stone-400">Memuat data peta...</p>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         SYSTEM LOG
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="mt-8" x-data="logWidget()" x-init="load()">
        <div class="flex items-center justify-between mb-3">
            <div>
                <h2 class="text-sm font-semibold text-stone-800">Log Sistem</h2>
                <p class="text-xs text-stone-400 font-light mt-0.5">Rekam jejak seluruh aktivitas penting dalam sistem.</p>
            </div>
        </div>

        {{-- Filter chips --}}
        <div class="flex items-center gap-1.5 flex-wrap mb-3">
            @foreach([
                'all'       => 'Semua',
                'auth'      => 'Auth',
                'petugas'   => 'Petugas',
                'pengaduan' => 'Pengaduan',
                'berita'    => 'Berita',
                'profil'    => 'Profil',
                'sistem'    => 'Sistem',
                'exception' => 'Error/Crash',
            ] as $key => $label)
            <button type="button"
                    @click="filterType = '{{ $key }}'; load(true)"
                    :class="filterType === '{{ $key }}'
                        ? 'bg-orange-500 text-white shadow-sm shadow-orange-200'
                        : 'bg-stone-100 text-stone-600 hover:bg-orange-50 hover:text-orange-600'"
                    class="px-3 py-1.5 rounded-full text-[0.6875rem] font-semibold border-0 cursor-pointer font-sans transition-all">
                {{ $label }}
            </button>
            @endforeach
        </div>

        <div class="rounded-2xl bg-white border border-stone-100 shadow-sm overflow-hidden">

            <div x-show="loading && logs.length === 0" class="flex items-center justify-center py-12">
                <p class="text-sm text-stone-400">Memuat log...</p>
            </div>

            <div x-show="!loading && logs.length === 0" class="flex items-center justify-center py-12">
                <p class="text-sm text-stone-400">Tidak ada log untuk tipe ini.</p>
            </div>

            <div x-show="logs.length > 0" class="divide-y divide-stone-100">
                <template x-for="log in logs" :key="log.id">
                    <div class="px-4 py-3 flex items-start gap-3 hover:bg-stone-50 transition-colors">

                        {{-- Type dot --}}
                        <div class="mt-0.5 shrink-0 w-2 h-2 rounded-full"
                             :style="`background-color:${logTypeColor(log.type)}`"></div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2 mb-0.5">
                                <p class="text-sm text-stone-800 font-medium leading-snug" x-text="log.description"></p>
                                <span class="shrink-0 text-[0.625rem] font-semibold px-1.5 py-0.5 rounded-full"
                                      :style="`background:${logTypeBg(log.type)};color:${logTypeColor(log.type)}`"
                                      x-text="logTypeLabel(log.type)"></span>
                            </div>
                            <div class="flex items-center gap-3 text-[0.6875rem] text-stone-400 font-light flex-wrap">
                                <span x-text="log.created_at" :title="log.created_at"></span>
                                <template x-if="log.causer">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        <span x-text="log.causer"></span>
                                    </span>
                                </template>
                                <template x-if="log.ip">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/></svg>
                                        <span x-text="log.ip"></span>
                                    </span>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div x-show="hasMore" class="px-4 py-3 border-t border-stone-100 bg-stone-50/50">
                <button @click="loadMore()" :disabled="loading"
                        class="w-full py-1.5 text-xs text-orange-500 hover:text-orange-600 font-medium bg-transparent border-0 cursor-pointer font-sans disabled:opacity-50">
                    <span x-show="!loading">Muat log lebih lama...</span>
                    <span x-show="loading">Memuat...</span>
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('head-scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush

@push('scripts')
<script>
// ── Health Widget ──────────────────────────────────────────────────────────
function healthWidget() {
    return {
        data: {},
        loading: false,
        async load() {
            this.loading = true;
            const res = await fetch('{{ route('admin.utilitas.health') }}', {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            this.data = await res.json();
            this.loading = false;
        },
    };
}

// ── Map Widget ─────────────────────────────────────────────────────────────
var _utilMap = null;

function mapWidget() {
    return {
        points: [],
        loading: false,
        async load() {
            this.loading = true;
            const res = await fetch('{{ route('admin.utilitas.peta-data') }}', {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            this.points = await res.json();
            this.loading = false;
            this.$nextTick(() => this.renderMap());
        },
        renderMap() {
            if (_utilMap) { _utilMap.remove(); _utilMap = null; }
            const el = document.getElementById('utilitas-map');
            if (!el) return;

            const colors = {
                infrastruktur: '#3B82F6',
                lingkungan:    '#10B981',
                keamanan:      '#EF4444',
                sosial:        '#8B5CF6',
                lainnya:       '#A8A29E',
            };

            const center = this.points.length
                ? [this.points[0].lat, this.points[0].lng]
                : [-2.5, 118.0];

            _utilMap = L.map(el, { zoomControl: true, scrollWheelZoom: false }).setView(center, this.points.length ? 10 : 5);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(_utilMap);

            this.points.forEach(p => {
                const color = colors[p.kategori] ?? colors.lainnya;
                L.circleMarker([p.lat, p.lng], {
                    radius: 7,
                    fillColor: color,
                    color: '#fff',
                    weight: 1.5,
                    opacity: 1,
                    fillOpacity: 0.85,
                }).bindPopup(`
                    <div style="min-width:160px;font-family:sans-serif;">
                        <p style="font-weight:600;font-size:0.8125rem;margin:0 0 4px">${p.lokasi ?? 'Lokasi tidak diisi'}</p>
                        <p style="font-size:0.75rem;color:#78716C;margin:0">${p.kategori} · ${p.status}</p>
                        <p style="font-size:0.6875rem;color:#A8A29E;margin:2px 0 0">${p.tgl}</p>
                    </div>
                `).addTo(_utilMap);
            });

            if (this.points.length > 1) {
                const group = new L.featureGroup(
                    this.points.map(p => L.marker([p.lat, p.lng]))
                );
                _utilMap.fitBounds(group.getBounds().pad(0.15));
            }
        },
    };
}

// ── Log Widget ─────────────────────────────────────────────────────────────
function logWidget() {
    return {
        logs: [],
        filterType: 'all',
        page: 1,
        hasMore: false,
        loading: false,

        logTypeCfg: {
            auth:      { label: 'Auth',      bg: '#EFF6FF', color: '#1D4ED8' },
            petugas:   { label: 'Petugas',   bg: '#FFF7ED', color: '#C2410C' },
            pengaduan: { label: 'Pengaduan', bg: '#ECFDF5', color: '#065F46' },
            berita:    { label: 'Berita',    bg: '#FAF5FF', color: '#6B21A8' },
            profil:    { label: 'Profil',    bg: '#FDF2F8', color: '#9D174D' },
            sistem:    { label: 'Sistem',    bg: '#FFFBEB', color: '#92400E' },
            exception: { label: 'Error',     bg: '#FEE2E2', color: '#991B1B' },
        },

        logTypeColor(type) { return this.logTypeCfg[type]?.color ?? '#78716C'; },
        logTypeBg(type)    { return this.logTypeCfg[type]?.bg ?? '#F5F5F4'; },
        logTypeLabel(type) { return this.logTypeCfg[type]?.label ?? type; },

        async load(reset = false) {
            if (reset) { this.logs = []; this.page = 1; this.hasMore = false; }
            this.loading = true;
            const params = new URLSearchParams({ type: this.filterType, page: this.page });
            const res = await fetch(`{{ route('admin.utilitas.logs') }}?${params}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            const json = await res.json();
            this.logs.push(...json.data);
            this.hasMore = json.next_page_url !== null;
            this.page++;
            this.loading = false;
        },

        loadMore() { this.load(); },
    };
}
</script>
@endpush
