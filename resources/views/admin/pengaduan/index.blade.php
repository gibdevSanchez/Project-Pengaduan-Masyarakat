@extends('layouts.app')

@section('title', 'Manajemen Pengaduan')

@push('head-scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush

@section('content')
@php use Illuminate\Support\Str; @endphp
<div class="flex-1 overflow-y-auto p-6 bg-stone-50"
     x-data="adminDetailPage()"
     @open-detail.window="openDetail($event.detail)"
     @keydown.escape.window="closeDetail()">

    {{-- Detail modal --}}
    <div x-show="detail" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
         @click.self="closeDetail()">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden" @click.stop>
            <div class="flex items-center justify-between px-5 py-4 border-b border-stone-100 shrink-0">
                <p class="font-bold text-stone-900">Detail Pengaduan</p>
                <button @click="closeDetail()"
                        class="w-8 h-8 rounded-lg bg-stone-100 hover:bg-stone-200 flex items-center justify-center cursor-pointer border-0 transition-colors">
                    <svg class="w-4 h-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="px-5 py-4 max-h-[72vh] overflow-y-auto space-y-4">
                <div>
                    <p class="text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wider mb-2">Laporan</p>
                    <p class="text-sm text-stone-800 leading-relaxed" x-text="detail && detail.laporan"></p>
                </div>
                <div x-show="detail && detail.lokasi">
                    <p class="text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wider mb-1">Lokasi</p>
                    <p class="text-sm text-stone-700" x-text="detail && detail.lokasi"></p>
                </div>
                <div id="admin-detail-map"
                     x-show="detail && detail.lat && detail.lng"
                     class="w-full h-52 rounded-xl overflow-hidden border border-stone-200"></div>

                {{-- Anonymous response section --}}
                <div x-show="detail && detail.isAnonim">
                    <div class="border-t border-stone-100 pt-4">
                        <div class="flex items-center justify-between mb-1">
                            <p class="text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wider">Respons untuk Pelapor Anonim</p>
                            <span class="font-mono text-xs font-semibold text-stone-500 bg-stone-100 px-2 py-0.5 rounded-lg" x-text="detail && detail.trackingCode"></span>
                        </div>
                        <p class="text-xs text-stone-400 font-light mb-3 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Respons ini akan terlihat di halaman lacak publik.
                        </p>

                        <template x-if="adminAnonLoading">
                            <p class="text-center text-xs text-stone-400 py-2">Memuat respons...</p>
                        </template>

                        <div class="flex flex-col gap-2 mb-3">
                            <template x-for="(msg, i) in adminAnonMessages" :key="i">
                                <div class="rounded-xl border border-stone-200 bg-stone-50 px-3 py-2.5">
                                    <p class="text-[0.625rem] font-semibold uppercase tracking-wide text-stone-500 mb-1">
                                        Respons <span x-text="msg.dari === 'admin' ? 'Admin' : 'Petugas'"></span>
                                    </p>
                                    <p x-text="msg.pesan" class="text-sm text-stone-800 leading-relaxed"></p>
                                    <img x-show="msg.foto_url" :src="msg.foto_url"
                                         class="mt-2 w-full rounded-lg object-cover max-h-32 border border-stone-200">
                                    <p x-text="msg.created_at" class="mt-1.5 text-[0.6875rem] text-stone-400 font-light"></p>
                                </div>
                            </template>
                            <template x-if="!adminAnonLoading && adminAnonMessages.length === 0">
                                <p class="text-center text-xs text-stone-400 italic py-1">Belum ada respons yang dikirim.</p>
                            </template>
                        </div>

                        <template x-if="detail && !['selesai','tidak_valid'].includes(detail.status)">
                            <div class="space-y-2">
                                <textarea x-model="adminAnonPesan" rows="2" minlength="3"
                                          placeholder="Tulis respons untuk pelapor anonim..."
                                          class="w-full rounded-xl border border-stone-200 px-3 py-2 text-sm font-sans text-stone-900 outline-none focus:border-stone-400 focus:ring-2 focus:ring-stone-400/20 resize-none"></textarea>
                                <div class="flex items-center gap-2">
                                    <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-stone-200 bg-white text-xs text-stone-500 cursor-pointer hover:border-stone-300 hover:text-stone-700 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <span x-text="adminAnonFoto ? adminAnonFoto.name.substring(0,15)+'…' : 'Lampir foto'"></span>
                                        <input type="file" accept="image/*" class="hidden"
                                               @change="adminAnonFoto = $event.target.files[0] || null">
                                    </label>
                                    <button x-show="adminAnonFoto" @click="adminAnonFoto = null"
                                            class="text-xs text-red-500 hover:text-red-700 bg-transparent border-0 cursor-pointer font-sans p-0">✕</button>
                                </div>
                                <button @click="submitAdminAnonRespons()"
                                        :disabled="adminAnonPesan.trim().length < 3"
                                        class="w-full rounded-xl bg-stone-700 hover:bg-stone-800 py-2 text-sm font-semibold text-white border-0 cursor-pointer font-sans disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                    Kirim Respons
                                </button>
                            </div>
                        </template>
                        <template x-if="detail && ['selesai','tidak_valid'].includes(detail.status)">
                            <p class="text-center text-xs text-stone-400 italic">Pengaduan sudah ditutup.</p>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Header --}}
    <div class="flex items-center justify-between mb-5">
        <div>
            <h1 class="text-xl font-bold text-stone-900 tracking-tight">Manajemen Pengaduan</h1>
            <p class="text-sm font-light text-stone-500 mt-0.5">{{ $pengaduan->total() }} pengaduan total</p>
        </div>
    </div>

    {{-- Tab Navigation --}}
    <div class="mb-4 flex gap-0 border-b border-gray-200 overflow-x-auto">
        @foreach ([
            'all'      => 'Semua',
            'menunggu' => 'Menunggu',
            'proses'   => 'Diproses',
            'selesai'  => 'Selesai',
            'invalid'  => 'Tidak Valid',
        ] as $key => $label)
            <a href="{{ route('admin.pengaduan.index', array_merge(request()->except('tab', 'page'), ['tab' => $key])) }}"
               class="whitespace-nowrap px-4 py-2.5 text-sm font-medium border-b-2 transition-colors
                      {{ $tab === $key
                          ? 'border-blue-500 text-blue-600'
                          : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- Search + Filter bar --}}
    <form method="GET" action="{{ route('admin.pengaduan.index') }}" class="mb-3 flex flex-wrap items-center gap-2">
        <input type="hidden" name="tab" value="{{ $tab }}">
        @if(request('kategori'))
        <input type="hidden" name="kategori" value="{{ request('kategori') }}">
        @endif

        {{-- Search --}}
        <div class="relative flex-1 min-w-[180px]">
            <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none">
                <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari isi laporan..."
                   class="w-full pl-9 pr-4 py-2 rounded-xl border border-stone-200 bg-white text-sm text-stone-800 outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-400/20 font-sans">
        </div>

        {{-- Date from --}}
        <input type="date" name="date_from" value="{{ request('date_from') }}"
               class="px-3 py-2 rounded-xl border border-stone-200 bg-white text-sm text-stone-700 outline-none focus:border-orange-400 font-sans">

        <span class="text-stone-400 text-xs">s/d</span>

        {{-- Date to --}}
        <input type="date" name="date_to" value="{{ request('date_to') }}"
               class="px-3 py-2 rounded-xl border border-stone-200 bg-white text-sm text-stone-700 outline-none focus:border-orange-400 font-sans">

        <button type="submit"
                class="px-4 py-2 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold border-0 cursor-pointer transition-colors">
            Cari
        </button>

        @if(request()->hasAny(['search', 'date_from', 'date_to']))
        <a href="{{ route('admin.pengaduan.index', array_filter(['tab' => $tab, 'kategori' => request('kategori')])) }}"
           class="px-4 py-2 rounded-xl border border-stone-200 bg-white text-sm text-stone-500 hover:bg-stone-50 transition-colors no-underline">
            Reset
        </a>
        @endif
    </form>

    {{-- Kategori Filter --}}
    <div class="mb-4 flex justify-end">
        <select onchange="window.location=this.value"
                class="px-3 py-1.5 rounded-full text-xs border border-stone-200 bg-white text-stone-500 outline-none cursor-pointer hover:border-orange-300 transition-colors">
            <option value="{{ request()->fullUrlWithQuery(['kategori' => '', 'page' => 1]) }}" {{ !request('kategori') ? 'selected' : '' }}>Semua Kategori</option>
            @foreach(['infrastruktur', 'lingkungan', 'keamanan', 'sosial', 'lainnya'] as $k)
            <option value="{{ request()->fullUrlWithQuery(['kategori' => $k, 'page' => 1]) }}" {{ request('kategori') === $k ? 'selected' : '' }}>
                {{ ucfirst($k) }}
            </option>
            @endforeach
        </select>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-stone-100">
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
                $overdue = $p->isOverdue($slaHours);
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
                <tr class="{{ $overdue ? 'bg-red-50/30' : '' }} hover:bg-orange-50/40 transition-colors">
                    <td class="px-4 py-3 text-xs text-stone-400 font-mono">#{{ $p->id_pengaduan }}</td>
                    <td class="px-4 py-3 text-xs text-stone-500 whitespace-nowrap">
                        {{ $p->created_at->format('d M Y') }}
                        @if($overdue)
                        <span class="block text-[0.625rem] font-semibold text-red-500 mt-0.5">Melebihi SLA</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <span class="text-stone-800 font-medium text-sm">
                            {{ $p->masyarakat?->nama ?? 'Anonim' }}
                        </span>
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
                                            <option value="{{ $pt->id_petugas }}" {{ $p->id_petugas === $pt->id_petugas ? 'selected' : '' }}>
                                                {{ $pt->nama_petugas }}
                                            </option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="text-xs px-2.5 py-1.5 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition-colors border-0 cursor-pointer font-sans whitespace-nowrap">
                                            Set
                                        </button>
                                    </form>
                                </div>

                                {{-- Lihat detail --}}
                                <div class="border-t border-stone-100 pt-1 mt-0.5 mb-0.5">
                                    <button type="button"
                                            @click="open = false; $dispatch('open-detail', { id: {{ $p->id_pengaduan }}, isAnonim: {{ is_null($p->masyarakat_id) ? 'true' : 'false' }}, status: @js($p->status), trackingCode: @js($p->tracking_code), laporan: @js($p->isi_laporan), lokasi: @js($p->lokasi), lat: @js($p->lat ? (float) $p->lat : null), lng: @js($p->lng ? (float) $p->lng : null) })"
                                            class="w-full text-xs px-2.5 py-1.5 text-stone-600 hover:bg-stone-100 rounded-lg transition-colors border-0 cursor-pointer font-sans text-left">
                                        Lihat Detail
                                    </button>
                                </div>

                                {{-- Hapus --}}
                                <div class="border-t border-stone-100 pt-1 mt-0.5">
                                    <form method="POST" action="{{ route('admin.pengaduan.destroy', $p->id_pengaduan) }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" onclick="return confirm('Hapus pengaduan ini? Admin masih bisa melihatnya.')"
                                                class="w-full text-xs px-2.5 py-1.5 text-stone-500 hover:bg-stone-100 rounded-lg transition-colors border-0 cursor-pointer font-sans text-left">
                                            Hapus (soft)
                                        </button>
                                    </form>
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

@push('scripts')
<script>
var _adminMap = null;

function initAdminMap(lat, lng) {
    if (_adminMap) { _adminMap.remove(); _adminMap = null; }
    var el = document.getElementById('admin-detail-map');
    if (!el || !lat || !lng) return;
    _adminMap = L.map(el, { zoomControl: true, scrollWheelZoom: false }).setView([lat, lng], 16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(_adminMap);
    L.marker([lat, lng]).addTo(_adminMap);
}

function adminDetailPage() {
    return {
        detail: null,
        adminAnonMessages: [],
        adminAnonLoading: false,
        adminAnonPesan: '',
        adminAnonFoto: null,

        openDetail(data) {
            this.detail = data;
            this.adminAnonMessages = [];
            this.adminAnonPesan = '';
            this.adminAnonFoto = null;
            var self = this;
            this.$nextTick(function() {
                if (data.lat && data.lng) {
                    initAdminMap(data.lat, data.lng);
                }
            });
            if (data.isAnonim) {
                this.fetchAdminAnonRespons(data.id);
            }
        },

        async fetchAdminAnonRespons(id) {
            this.adminAnonLoading = true;
            const res = await fetch(`/admin/pengaduan/${id}/anonim-respons`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            this.adminAnonMessages = await res.json();
            this.adminAnonLoading = false;
        },

        async submitAdminAnonRespons() {
            if (this.adminAnonPesan.trim().length < 3) return;
            const fd = new FormData();
            fd.append('pesan', this.adminAnonPesan);
            if (this.adminAnonFoto) fd.append('foto', this.adminAnonFoto);
            const res = await fetch(`/admin/pengaduan/${this.detail.id}/anonim-respons`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: fd,
            });
            if (res.status === 201) {
                const msg = await res.json();
                this.adminAnonMessages.push(msg);
                this.adminAnonPesan = '';
                this.adminAnonFoto = null;
            }
        },

        closeDetail() {
            this.detail = null;
            if (_adminMap) { _adminMap.remove(); _adminMap = null; }
        },
    };
}
</script>
@endpush
