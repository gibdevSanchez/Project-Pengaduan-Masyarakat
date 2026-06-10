@extends('layouts.app')

@section('title', 'Daftar Pengaduan')

@section('content')
<div class="flex flex-1 overflow-hidden h-full" x-data="petugasApp()" @keydown.escape.window="lightboxSrc = null">

    {{-- Lightbox overlay --}}
    <div x-show="lightboxSrc" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 p-4 cursor-zoom-out"
         @click="lightboxSrc = null">
        <img :src="lightboxSrc" class="max-w-full max-h-full object-contain rounded-lg shadow-2xl" @click.stop>
        <button @click="lightboxSrc = null"
                class="absolute top-4 right-4 w-9 h-9 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center text-white border-0 cursor-pointer transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Inner toggleable sidebar --}}
    <div x-show="sidebarOpen"
         x-cloak
         class="w-60 shrink-0 border-r border-stone-200 bg-white flex flex-col overflow-hidden">

        <div class="p-4 border-b border-stone-100">
            <div class="flex items-center gap-3">
                @if(auth('petugas')->user()->foto_profil)
                <img src="{{ Storage::url(auth('petugas')->user()->foto_profil) }}"
                     alt="{{ auth('petugas')->user()->nama_petugas }}"
                     class="w-10 h-10 rounded-full object-cover shrink-0 ring-2 ring-orange-200">
                @else
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white font-black text-sm shrink-0 shadow-sm shadow-orange-200">
                    {{ strtoupper(substr(auth('petugas')->user()->nama_petugas, 0, 1)) }}
                </div>
                @endif
                <div class="min-w-0">
                    <p class="text-sm font-bold text-stone-900 truncate">{{ auth('petugas')->user()->nama_petugas }}</p>
                    <p class="text-xs font-light text-stone-500">Petugas</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 px-2 py-3 flex flex-col gap-0.5">
            <button type="button" @click="setMineFilter(false)"
                    :class="!mineOnly
                        ? 'bg-orange-500 text-white font-semibold shadow-sm shadow-orange-200'
                        : 'bg-transparent text-stone-500 hover:bg-stone-100 hover:text-stone-800'"
                    class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm cursor-pointer font-sans border-0 transition-all">
                <svg class="w-[1.125rem] h-[1.125rem] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                Semua Pengaduan
            </button>
            <button type="button" @click="setMineFilter(true)"
                    :class="mineOnly
                        ? 'bg-orange-500 text-white font-semibold shadow-sm shadow-orange-200'
                        : 'bg-transparent text-stone-500 hover:bg-stone-100 hover:text-stone-800'"
                    class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm cursor-pointer font-sans border-0 transition-all">
                <svg class="w-[1.125rem] h-[1.125rem] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Pengaduan Saya
            </button>
        </nav>
    </div>

    {{-- Complaint list column --}}
    <div class="w-[320px] shrink-0 border-r border-stone-200 bg-stone-50 flex flex-col overflow-hidden">

        {{-- Header: hamburger + search --}}
        <div class="p-3 border-b border-stone-200 bg-white shrink-0">
            <div class="flex items-center gap-2 mb-2.5">
                <button type="button" @click="sidebarOpen = !sidebarOpen"
                        class="flex items-center justify-center w-8 h-8 rounded-xl border border-stone-200 bg-white text-stone-500 hover:border-orange-300 hover:text-orange-500 transition-colors shrink-0 cursor-pointer font-sans">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div class="relative flex-1">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" x-model="search" @input="onSearchChange()" placeholder="Cari pengaduan..."
                           class="w-full pl-9 pr-3 py-2 rounded-xl border border-stone-200 bg-stone-50 text-[0.8125rem] text-stone-900 font-sans outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-400/20 focus:bg-white transition-colors">
                </div>
            </div>
        </div>

        {{-- Filter chips + kategori --}}
        <div class="flex items-center gap-1.5 px-3 py-2 border-b border-stone-200 bg-white shrink-0 overflow-x-auto scrollbar-none">
            @foreach(['' => 'Semua', 'menunggu' => 'Menunggu', 'proses' => 'Proses', 'selesai' => 'Selesai', 'tidak_valid' => 'Tidak Valid'] as $val => $lbl)
            <button type="button"
                    @click="filterStatus = '{{ $val }}'; loadComplaints(true)"
                    :class="filterStatus === '{{ $val }}'
                        ? 'bg-orange-500 text-white border-orange-500 shadow-sm'
                        : 'bg-white text-stone-500 border-stone-200 hover:border-orange-300'"
                    class="px-2.5 py-1 rounded-full text-[0.6875rem] font-medium whitespace-nowrap cursor-pointer font-sans border transition-all shrink-0">
                {{ $lbl }}
            </button>
            @endforeach
            <div class="relative ml-auto shrink-0">
                <select x-model="filterKategori" @change="loadComplaints(true)"
                        class="appearance-none pl-2.5 pr-6 py-1 rounded-xl border border-stone-200 bg-white text-[0.6875rem] font-medium text-stone-600 outline-none cursor-pointer focus:border-orange-400">
                    <option value="">Kategori</option>
                    <option value="infrastruktur">Infrastruktur</option>
                    <option value="lingkungan">Lingkungan</option>
                    <option value="keamanan">Keamanan</option>
                    <option value="sosial">Sosial</option>
                    <option value="lainnya">Lainnya</option>
                </select>
                <div class="absolute right-1.5 top-1/2 -translate-y-1/2 pointer-events-none text-stone-400">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- List --}}
        <div class="flex-1 overflow-y-auto">
            <template x-if="filteredComplaints.length === 0 && !loading">
                <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-stone-100 flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-stone-500">Tidak ada pengaduan</p>
                    <p class="text-xs font-light text-stone-400 mt-0.5">Coba ubah filter di atas</p>
                </div>
            </template>
            <template x-for="c in filteredComplaints" :key="c.id">
                <div @click="selectComplaint(c.id)"
                     :class="selectedId === c.id
                         ? 'bg-white border-l-[3px] border-l-orange-500 shadow-[2px_0_8px_rgba(0,0,0,0.06)]'
                         : 'border-l-[3px] border-l-transparent hover:bg-white/70'"
                     class="flex items-start gap-2.5 px-3 py-3.5 cursor-pointer transition-all border-b border-stone-100">

                    <div :style="`width:2.25rem;height:2.25rem;border-radius:9999px;background-color:${avatarColor(c.nama)};display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:0.8125rem;flex-shrink:0;`"
                         x-text="c.initials"></div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-0.5">
                            <span class="text-sm font-semibold text-stone-900 truncate" x-text="c.nama"></span>
                            <span class="text-[0.6875rem] font-light text-stone-400 shrink-0 ml-2" x-text="c.tgl"></span>
                        </div>
                        <p class="text-xs text-stone-500 truncate mb-1.5" x-text="c.snippet"></p>
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="text-[0.625rem] px-1.5 py-0.5 rounded-full font-medium"
                                  :class="{
                                    'bg-blue-100 text-blue-700': c.kategori === 'infrastruktur',
                                    'bg-emerald-100 text-emerald-700': c.kategori === 'lingkungan',
                                    'bg-red-100 text-red-700': c.kategori === 'keamanan',
                                    'bg-purple-100 text-purple-700': c.kategori === 'sosial',
                                    'bg-stone-100 text-stone-600': !c.kategori || c.kategori === 'lainnya',
                                  }"
                                  x-text="c.kategori ? c.kategori.charAt(0).toUpperCase() + c.kategori.slice(1) : ''">
                            </span>
                            <template x-if="c.lokasi">
                                <span class="inline-flex items-center gap-0.5 text-[0.625rem] text-stone-400">
                                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span class="truncate max-w-[6rem]" x-text="c.lokasi"></span>
                                </span>
                            </template>
                            {{-- SLA chip (compact) --}}
                            <template x-if="slaInfo(c)">
                                <span class="inline-flex items-center gap-0.5 text-[0.625rem] font-semibold px-1.5 py-0.5 rounded-full"
                                      :style="`background:${slaInfo(c).bg};color:${slaInfo(c).color}`">
                                    <svg class="w-2.5 h-2.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span x-text="slaInfo(c).label"></span>
                                </span>
                            </template>
                        </div>

                        <template x-if="!c.id_petugas">
                            <form :action="'/petugas/pengaduan/' + c.id + '/assign'" method="POST" class="mt-2" @click.stop>
                                @csrf
                                <button type="submit"
                                        class="w-full px-2 py-1.5 text-[0.6875rem] font-semibold bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition-colors border-0 cursor-pointer font-sans">
                                    Ambil Pengaduan
                                </button>
                            </form>
                        </template>
                        <template x-if="c.id_petugas">
                            <p class="inline-flex items-center gap-1 text-[0.625rem] text-orange-500 font-medium mt-1.5">
                                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Pengaduan Saya
                            </p>
                        </template>

                        <div class="flex justify-end mt-1.5">
                            <div x-html="statusBadgeHtml(c.status)"></div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <div class="p-3 border-t border-stone-200 bg-white shrink-0">
            <button x-show="hasMore && !loading" @click="loadComplaints()"
                    class="w-full py-2 text-xs text-orange-500 font-medium hover:text-orange-600 bg-transparent border-0 cursor-pointer font-sans">
                Muat lebih banyak...
            </button>
            <p x-show="loading" class="text-center text-xs text-stone-400 py-2">Memuat...</p>
        </div>
    </div>

    {{-- Detail panel --}}
    <div class="flex-1 flex flex-col overflow-hidden bg-white">

        {{-- Empty state --}}
        <div x-show="!selectedId" class="flex-1 flex flex-col items-center justify-center gap-3 text-center px-8">
            <div class="w-20 h-20 rounded-3xl bg-stone-100 flex items-center justify-center mb-1">
                <svg class="w-9 h-9 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <p class="font-semibold text-[0.9375rem] text-stone-800">Pilih pengaduan</p>
                <p class="font-light text-sm text-stone-400 mt-1">Klik salah satu pengaduan di sebelah kiri untuk melihat detail dan memberi tanggapan</p>
            </div>
        </div>

        {{-- Selected complaint detail --}}
        <template x-if="selectedId && selectedComplaint">
            <div class="flex flex-col h-full overflow-hidden">

                {{-- Zone 1: Sticky header --}}
                <div class="px-5 py-4 border-b border-stone-200 bg-white shrink-0">
                    <div class="flex items-center gap-3">
                        <div :style="`width:2.5rem;height:2.5rem;border-radius:9999px;background-color:${avatarColor(selectedComplaint.nama)};display:flex;align-items:center;justify-content:center;color:white;font-weight:700;flex-shrink:0;`"
                             x-text="selectedComplaint.initials"></div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-[0.9375rem] text-stone-900 truncate" x-text="selectedComplaint.nama"></p>
                            <p class="font-light text-xs text-stone-500" x-text="selectedComplaint.tgl"></p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="text-[0.625rem] px-2 py-0.5 rounded-full font-medium"
                                  :class="{
                                    'bg-blue-100 text-blue-700': selectedComplaint.kategori === 'infrastruktur',
                                    'bg-emerald-100 text-emerald-700': selectedComplaint.kategori === 'lingkungan',
                                    'bg-red-100 text-red-700': selectedComplaint.kategori === 'keamanan',
                                    'bg-purple-100 text-purple-700': selectedComplaint.kategori === 'sosial',
                                    'bg-stone-100 text-stone-600': !selectedComplaint.kategori || selectedComplaint.kategori === 'lainnya',
                                  }"
                                  x-text="selectedComplaint.kategori ? selectedComplaint.kategori.charAt(0).toUpperCase() + selectedComplaint.kategori.slice(1) : ''">
                            </span>
                            <div x-html="statusBadgeHtml(selectedComplaint.status)"></div>
                        </div>
                    </div>

                    {{-- SLA deadline bar --}}
                    <template x-if="slaInfo(selectedComplaint)">
                        <div class="mt-3 rounded-xl px-3 py-2.5 flex items-center gap-3"
                             :style="`background:${slaInfo(selectedComplaint).bg}`">
                            <svg class="w-4 h-4 shrink-0" :style="`color:${slaInfo(selectedComplaint).color}`" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="flex-1">
                                <p class="text-[0.6875rem] font-semibold" :style="`color:${slaInfo(selectedComplaint).color}`"
                                   x-text="slaInfo(selectedComplaint).overdue ? 'SLA Terlewat' : 'Batas Waktu SLA'"></p>
                                <p class="text-[0.75rem] font-bold" :style="`color:${slaInfo(selectedComplaint).color}`"
                                   x-text="slaInfo(selectedComplaint).label"></p>
                            </div>
                            <div class="text-right">
                                <p class="text-[0.6875rem] font-light" :style="`color:${slaInfo(selectedComplaint).color};opacity:0.75`"
                                   x-text="'SLA ' + selectedComplaint.sla_hours + 'j · sejak ' + selectedComplaint.tgl"></p>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Zone 2: Scrollable body --}}
                <div class="flex-1 overflow-y-auto bg-stone-50">

                    {{-- Block A: Isi Laporan + Foto --}}
                    <div class="p-5 border-b border-stone-200">
                        <p class="text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wider mb-2">Laporan</p>
                        <p class="text-sm text-stone-900 leading-relaxed" x-text="selectedComplaint.isiLaporan"></p>
                        <template x-if="selectedComplaint.fotos && selectedComplaint.fotos.length > 0">
                            <div class="mt-3 flex flex-col gap-2">
                                <template x-for="(src, idx) in selectedComplaint.fotos" :key="idx">
                                    <a :href="src" target="_blank" class="block">
                                        <img :src="src" class="w-full max-h-48 object-cover rounded-xl cursor-pointer border border-stone-100">
                                    </a>
                                </template>
                            </div>
                        </template>
                    </div>

                    {{-- Block B: Lokasi --}}
                    <template x-if="selectedComplaint.lokasi">
                        <div class="px-5 py-4 border-b border-stone-200">
                            <p class="text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wider mb-2">Lokasi</p>
                            <div class="flex items-start gap-2.5 mb-3">
                                <div class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-stone-900 mb-1" x-text="selectedComplaint.lokasi"></p>
                                    <a :href="'https://maps.google.com/?q=' + encodeURIComponent(selectedComplaint.lokasi)"
                                       target="_blank"
                                       class="text-xs text-orange-500 hover:text-orange-600 font-medium">
                                        Lihat di Google Maps →
                                    </a>
                                </div>
                            </div>
                            <div id="petugas-detail-map"
                                 x-show="selectedComplaint.lat && selectedComplaint.lng"
                                 class="w-full h-44 rounded-xl overflow-hidden border border-stone-200"></div>
                        </div>
                    </template>

                    {{-- Block C: Reporter info (non-anon only) --}}
                    <template x-if="!selectedComplaint.isAnonim">
                        <div class="px-5 py-4 border-b border-stone-200">
                            <p class="text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wider mb-3">Pelapor</p>
                            <div class="flex flex-col gap-2.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-[0.6875rem] font-light text-stone-400">Nama</p>
                                        <p class="text-sm font-medium text-stone-900" x-text="selectedComplaint.nama"></p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-[0.6875rem] font-light text-stone-400">NIK</p>
                                        <p class="text-sm font-medium text-stone-900" x-text="selectedComplaint.nikMasked ?? '—'"></p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-[0.6875rem] font-light text-stone-400">Telepon</p>
                                        <p class="text-sm font-medium text-stone-900" x-text="selectedComplaint.telp ?? '—'"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- Block C-Anon: Anonymous complaint identifier --}}
                    <template x-if="selectedComplaint.isAnonim">
                        <div class="px-5 py-4 border-b border-stone-200">
                            <p class="text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wider mb-3">Pelapor</p>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-stone-100 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[0.6875rem] font-light text-stone-400">Kode Lacak Anonim</p>
                                    <p class="text-sm font-mono font-bold text-stone-800 tracking-wider mt-0.5" x-text="selectedComplaint.tracking_code"></p>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- Block D-Anon: Anonymous response panel --}}
                    <template x-if="selectedComplaint.isAnonim && selectedComplaint.id_petugas == {{ auth('petugas')->user()->id_petugas }}">
                        <div class="px-5 py-4 border-b border-stone-200">
                            <p class="text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wider mb-3">Respons untuk Pelapor</p>
                            <p class="text-xs text-stone-400 font-light mb-3 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 shrink-0 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                                Respons ini akan terlihat di halaman lacak publik.
                            </p>

                            <template x-if="anonimResponsLoading">
                                <p class="text-center text-xs text-stone-400 py-3">Memuat respons...</p>
                            </template>

                            <div class="flex flex-col gap-2 mb-4">
                                <template x-for="(msg, i) in anonimResponsMessages" :key="i">
                                    <div class="rounded-xl border border-stone-200 bg-stone-50 px-3 py-2.5">
                                        <p class="text-[0.625rem] font-semibold uppercase tracking-wide text-stone-500 mb-1">Respons Petugas</p>
                                        <p x-text="msg.pesan" class="text-sm text-stone-800 leading-relaxed"></p>
                                        <img x-show="msg.foto_url" :src="msg.foto_url"
                                             class="mt-2 w-full rounded-lg object-cover max-h-32 border border-stone-200 cursor-zoom-in"
                                             @click="lightboxSrc = msg.foto_url">
                                        <p x-text="msg.created_at" class="mt-1.5 text-[0.6875rem] text-stone-400 font-light"></p>
                                    </div>
                                </template>
                                <template x-if="!anonimResponsLoading && anonimResponsMessages.length === 0">
                                    <p class="text-center text-xs text-stone-400 italic py-2">Belum ada respons yang dikirim.</p>
                                </template>
                            </div>

                            <template x-if="!['selesai','tidak_valid'].includes(selectedComplaint.status)">
                                <div class="space-y-2">
                                    <textarea x-model="anonimPesan" rows="2" minlength="3"
                                              placeholder="Tulis respons untuk pelapor anonim..."
                                              class="w-full rounded-xl border border-stone-200 px-3 py-2 text-sm font-sans text-stone-900 outline-none focus:border-stone-400 focus:ring-2 focus:ring-stone-400/20 resize-none"></textarea>
                                    <div class="flex items-center gap-2">
                                        <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-stone-200 bg-white text-xs text-stone-500 cursor-pointer hover:border-stone-300 hover:text-stone-700 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <span x-text="anonimFoto ? anonimFoto.name.substring(0,15)+'…' : 'Lampir foto'"></span>
                                            <input type="file" accept="image/*" class="hidden"
                                                   @change="anonimFoto = $event.target.files[0] || null">
                                        </label>
                                        <button x-show="anonimFoto" @click="anonimFoto = null"
                                                class="text-xs text-red-500 hover:text-red-700 bg-transparent border-0 cursor-pointer font-sans p-0">✕</button>
                                    </div>
                                    <button @click="submitAnonimRespons()"
                                            :disabled="anonimPesan.trim().length < 3"
                                            class="w-full rounded-xl bg-stone-700 hover:bg-stone-800 py-2 text-sm font-semibold text-white border-0 cursor-pointer font-sans disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                        Kirim Respons Anonim
                                    </button>
                                </div>
                            </template>
                            <template x-if="['selesai','tidak_valid'].includes(selectedComplaint.status)">
                                <p class="text-center text-xs text-stone-400 italic">Pengaduan sudah ditutup.</p>
                            </template>
                        </div>
                    </template>

                    {{-- Block D: Klarifikasi thread --}}
                    <template x-if="!selectedComplaint.isAnonim && klarifikasiOpen">
                        <div class="px-5 py-4">
                            <p class="text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wider mb-3">Klarifikasi</p>

                            <template x-if="klarifikasiLoading">
                                <p class="text-center text-xs text-stone-400 py-4">Memuat...</p>
                            </template>

                            <template x-if="!klarifikasiLoading && klarifikasiMessages.length === 0">
                                <p class="text-center text-xs text-stone-400 py-4 italic">Belum ada pesan. Kirim pertanyaan di bawah.</p>
                            </template>

                            <div class="flex flex-col gap-3 mb-4">
                                <template x-for="(msg, i) in klarifikasiMessages" :key="i">
                                    <div>
                                        {{-- Tahapan card --}}
                                        <template x-if="msg.jenis === 'tahapan'">
                                            <div class="my-2 flex items-start gap-2.5">
                                                <div class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-blue-100">
                                                    <svg class="h-3.5 w-3.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                </div>
                                                <div class="flex-1 rounded-xl border border-blue-200 bg-blue-50 px-3 py-2">
                                                    <p class="text-[0.625rem] font-semibold uppercase tracking-wide text-blue-600">Update Progres</p>
                                                    <p x-text="msg.pesan" class="mt-1 text-sm text-blue-900"></p>
                                                    <img x-show="msg.foto_url" :src="msg.foto_url"
                                                         class="mt-2 w-full rounded-lg object-cover max-h-32 border border-blue-200 cursor-zoom-in"
                                                         @click="lightboxSrc = msg.foto_url">
                                                    <p x-text="msg.created_at" class="mt-1 text-[0.6875rem] text-blue-400 font-light"></p>
                                                </div>
                                            </div>
                                        </template>

                                        {{-- Penutup card --}}
                                        <template x-if="msg.jenis === 'penutup'">
                                            <div class="my-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3">
                                                <p class="text-[0.625rem] font-semibold uppercase tracking-wide text-emerald-600">Pengaduan Diselesaikan</p>
                                                <p x-text="msg.pesan" class="mt-1 text-sm text-emerald-900 whitespace-pre-line"></p>
                                                <p x-text="msg.created_at" class="mt-2 text-[0.6875rem] text-emerald-400 font-light"></p>
                                            </div>
                                        </template>

                                        {{-- Regular chat bubble --}}
                                        <template x-if="msg.jenis !== 'tahapan' && msg.jenis !== 'penutup'">
                                            <div :class="msg.dari === 'petugas' ? 'flex justify-end' : 'flex justify-start'">
                                                <div class="max-w-[20rem]">
                                                    <template x-if="msg.dari === 'masyarakat'">
                                                        <p class="text-[0.6875rem] font-medium text-stone-400 mb-1" x-text="selectedComplaint.nama"></p>
                                                    </template>
                                                    <template x-if="msg.dari === 'admin'">
                                                        <p class="text-[0.6875rem] font-medium text-purple-400 mb-1">Admin</p>
                                                    </template>
                                                    <div :class="msg.dari === 'petugas'
                                                                 ? 'bg-orange-500 text-white rounded-2xl rounded-br-sm'
                                                                 : (msg.dari === 'admin'
                                                                    ? 'bg-purple-100 text-purple-900 rounded-2xl rounded-bl-sm border border-purple-200'
                                                                    : 'bg-white text-stone-900 rounded-2xl rounded-bl-sm border border-stone-100 shadow-sm')"
                                                         class="px-4 py-3 text-sm leading-relaxed"
                                                         x-text="msg.pesan"></div>
                                                    <img x-show="msg.foto_url" :src="msg.foto_url"
                                                         class="mt-1 w-full rounded-xl object-cover max-h-32 border border-stone-200 cursor-zoom-in"
                                                         @click="lightboxSrc = msg.foto_url">
                                                    <p class="text-[0.6875rem] font-light text-stone-400 mt-1"
                                                       :class="msg.dari === 'petugas' ? 'text-right' : 'text-left'"
                                                       x-text="msg.created_at"></p>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>

                            {{-- Tahapan & Selesai: only if this petugas is assigned --}}
                            <template x-if="selectedComplaint && selectedComplaint.id_petugas == {{ auth('petugas')->user()->id_petugas }} && !['selesai','tidak_valid'].includes(selectedComplaint.status)">
                                <div class="mb-3 space-y-2">
                                    {{-- Tambah Tahapan --}}
                                    <button @click="tahapanOpen = !tahapanOpen"
                                            class="w-full rounded-xl border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-medium text-blue-700 hover:bg-blue-100 transition-colors border-0 cursor-pointer font-sans">
                                        + Tambah Tahapan Proses
                                    </button>
                                    <div x-show="tahapanOpen" x-cloak class="space-y-2">
                                        <textarea x-model="tahapanPesan" rows="2" minlength="5" required
                                                  placeholder="Deskripsikan tahap yang sedang dikerjakan..."
                                                  class="w-full rounded-xl border border-stone-200 px-3 py-2 text-sm font-sans text-stone-900 outline-none focus:border-blue-400 resize-none"></textarea>
                                        <div class="flex items-center gap-2">
                                            <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-stone-200 bg-white text-xs text-stone-500 cursor-pointer hover:border-blue-300 hover:text-blue-600 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                <span x-text="tahapanFoto ? tahapanFoto.name.substring(0,15)+'…' : 'Lampir foto'"></span>
                                                <input type="file" accept="image/*" class="hidden"
                                                       @change="tahapanFoto = $event.target.files[0] || null">
                                            </label>
                                            <button x-show="tahapanFoto" @click="tahapanFoto=null"
                                                    class="text-xs text-red-500 hover:text-red-700 bg-transparent border-0 cursor-pointer font-sans p-0">✕</button>
                                        </div>
                                        <button @click="submitTahapan()"
                                                :disabled="tahapanPesan.trim().length < 5"
                                                class="w-full rounded-xl bg-blue-600 py-2 text-sm font-semibold text-white hover:bg-blue-700 border-0 cursor-pointer font-sans disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                            Kirim Tahapan
                                        </button>
                                    </div>

                                    {{-- Selesaikan --}}
                                    <button @click="selesaiOpen = !selesaiOpen"
                                            class="w-full rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-medium text-emerald-700 hover:bg-emerald-100 transition-colors border-0 cursor-pointer font-sans">
                                        Selesaikan Pengaduan
                                    </button>
                                    <div x-show="selesaiOpen" x-cloak class="space-y-2">
                                        <textarea x-model="selesaiPesan" rows="2"
                                                  placeholder="Pesan tambahan dari Anda (opsional)..."
                                                  class="w-full rounded-xl border border-stone-200 px-3 py-2 text-sm font-sans text-stone-900 outline-none focus:border-emerald-400 resize-none"></textarea>
                                        <button @click="submitSelesai()"
                                                class="w-full rounded-xl bg-emerald-600 py-2 text-sm font-semibold text-white hover:bg-emerald-700 border-0 cursor-pointer font-sans transition-colors">
                                            Konfirmasi Selesai
                                        </button>
                                    </div>
                                </div>
                            </template>

                            <template x-if="!['selesai','tidak_valid'].includes(selectedComplaint.status)">
                                <div class="space-y-2">
                                    <div class="flex gap-2">
                                        <input type="text" x-model="klarifikasiInput"
                                               @keydown.enter.prevent="sendKlarifikasi()"
                                               placeholder="Tulis pertanyaan untuk masyarakat..."
                                               class="flex-1 px-4 py-2.5 rounded-xl border border-stone-200 bg-white text-sm font-sans text-stone-900 outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-400/20">
                                        <label class="flex items-center justify-center w-10 h-10 rounded-xl border border-stone-200 bg-white text-stone-400 cursor-pointer hover:border-orange-300 hover:text-orange-500 transition-colors shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <input type="file" accept="image/*" class="hidden"
                                                   @change="klarifFoto = $event.target.files[0] || null">
                                        </label>
                                        <button @click="sendKlarifikasi()"
                                                :disabled="!klarifikasiInput.trim()"
                                                class="px-4 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold border-0 cursor-pointer font-sans transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                            Kirim
                                        </button>
                                    </div>
                                    <div x-show="klarifFoto" class="flex items-center gap-2 text-xs text-stone-500">
                                        <svg class="w-3.5 h-3.5 shrink-0 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14"/></svg>
                                        <span x-text="klarifFoto ? klarifFoto.name : ''"></span>
                                        <button @click="klarifFoto=null" class="text-red-500 hover:text-red-700 bg-transparent border-0 cursor-pointer font-sans p-0 ml-1">✕</button>
                                    </div>
                                </div>
                            </template>
                            <template x-if="['selesai','tidak_valid'].includes(selectedComplaint.status)">
                                <p class="text-center text-xs text-stone-400 italic">Thread klarifikasi telah ditutup.</p>
                            </template>
                        </div>
                    </template>

                </div>

                {{-- Zone 3: Sticky action bar --}}
                <div class="shrink-0 border-t border-stone-200 bg-white px-5 py-3.5">

                    {{-- Takedown inline form --}}
                    <template x-if="takedownOpen">
                        <div class="mb-3 p-4 bg-red-50 rounded-xl border border-red-200">
                            <p class="text-sm font-semibold text-red-800 mb-2">Alasan Takedown</p>
                            <textarea x-model="takedownReason" rows="2"
                                      placeholder="Jelaskan mengapa laporan ini tidak valid (min. 10 karakter)..."
                                      class="w-full px-3 py-2.5 rounded-lg border border-red-200 bg-white text-sm font-sans text-stone-900 resize-none outline-none focus:border-red-400 focus:ring-2 focus:ring-red-400/20"></textarea>
                            <div class="flex gap-2 mt-2">
                                <button @click="submitTakedown()"
                                        :disabled="takedownReason.trim().length < 10 || takedownLoading"
                                        class="flex-1 py-2 rounded-lg bg-red-500 hover:bg-red-600 text-white text-sm font-semibold border-0 cursor-pointer font-sans transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                    Konfirmasi Takedown
                                </button>
                                <button @click="takedownOpen = false; takedownReason = ''"
                                        class="px-4 py-2 rounded-lg border border-stone-200 bg-white text-stone-500 text-sm font-medium cursor-pointer font-sans hover:text-stone-900 transition-colors">
                                    Batal
                                </button>
                            </div>
                        </div>
                    </template>

                    {{-- Action buttons --}}
                    <div class="flex gap-2 flex-wrap">
                        <button x-show="selectedComplaint.status === 'menunggu'"
                                @click="submitStatus('proses')"
                                class="flex-1 py-2.5 rounded-xl bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold border-0 cursor-pointer font-sans transition-colors">
                            Tandai Proses
                        </button>
                        <button x-show="selectedComplaint.status === 'proses'"
                                @click="submitStatus('selesai')"
                                class="flex-1 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold border-0 cursor-pointer font-sans transition-colors">
                            Tandai Selesai
                        </button>
                        {{-- Klarifikasi hanya tersedia jika laporan sudah diambil (id_petugas tidak null) dan bukan anonim --}}
                        <button x-show="!selectedComplaint.isAnonim && selectedComplaint.id_petugas"
                                @click="toggleKlarifikasi()"
                                :class="klarifikasiOpen
                                    ? 'bg-orange-500 text-white'
                                    : 'border border-stone-200 bg-white text-stone-600 hover:bg-orange-50 hover:border-orange-300'"
                                class="flex-1 inline-flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-sm font-semibold border-0 cursor-pointer font-sans transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            Klarifikasi
                        </button>
                        {{-- Takedown hanya tersedia jika laporan sudah diambil (id_petugas tidak null) --}}
                        <button x-show="!['selesai','tidak_valid'].includes(selectedComplaint.status) && selectedComplaint.id_petugas"
                                @click="takedownOpen = !takedownOpen"
                                :class="takedownOpen
                                    ? 'bg-red-500 text-white'
                                    : 'border border-red-200 bg-red-50 text-red-600 hover:bg-red-100'"
                                class="flex-1 py-2.5 rounded-xl text-sm font-semibold border-0 cursor-pointer font-sans transition-colors">
                            Takedown
                        </button>
                        <p x-show="['selesai','tidak_valid'].includes(selectedComplaint.status)"
                           class="w-full text-center text-xs text-stone-400 italic py-1">
                            Tidak ada aksi yang tersedia.
                        </p>
                    </div>
                </div>

            </div>
        </template>
    </div>
</div>
@endsection

@push('head-scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush

@push('scripts')
<script>
var _pmap = null;

function initPmap(lat, lng) {
    if (_pmap) { _pmap.remove(); _pmap = null; }
    if (!lat || !lng) return;
    var el = document.getElementById('petugas-detail-map');
    if (!el) return;
    _pmap = L.map(el, { zoomControl: true, scrollWheelZoom: false }).setView([lat, lng], 16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(_pmap);
    L.marker([lat, lng]).addTo(_pmap);
}

function petugasApp() {
    return {
        complaints: [],
        selectedId: null,
        filterStatus: '',
        filterKategori: '',
        search: '',
        page: 1,
        hasMore: true,
        loading: false,
        searchTimer: null,
        sidebarOpen: false,
        mineOnly: false,
        lightboxSrc: null,
        klarifikasiOpen: false,
        klarifikasiMessages: [],
        klarifikasiLoading: false,
        klarifikasiInput: '',
        klarifFoto: null,
        takedownOpen: false,
        takedownReason: '',
        takedownLoading: false,
        tahapanOpen: false,
        tahapanPesan: '',
        tahapanFoto: null,
        selesaiOpen: false,
        selesaiPesan: '',
        anonimResponsMessages: [],
        anonimResponsLoading: false,
        anonimPesan: '',
        anonimFoto: null,
        slaTick: 0,

        async init() {
            this.$watch('selectedId', () => {
                this.klarifikasiOpen = false;
                this.klarifikasiMessages = [];
                this.klarifikasiInput = '';
                this.takedownOpen = false;
                this.takedownReason = '';
                this.anonimResponsMessages = [];
                this.anonimPesan = '';
                this.anonimFoto = null;
            });
            setInterval(() => { this.slaTick++; }, 60000);
            await this.loadComplaints();
        },

        async loadComplaints(reset = false) {
            if (reset) { this.complaints = []; this.page = 1; this.hasMore = true; }
            if (!this.hasMore || this.loading) return;
            this.loading = true;
            const params = new URLSearchParams({
                page: this.page,
                status: this.filterStatus,
                kategori: this.filterKategori,
                search: this.search,
                mine: this.mineOnly ? '1' : '',
            });
            const res = await fetch(`/petugas/complaints?${params}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const json = await res.json();
            this.complaints.push(...json.data);
            this.hasMore = json.next_page_url !== null;
            this.page++;
            this.loading = false;
        },

        onSearchChange() {
            clearTimeout(this.searchTimer);
            this.searchTimer = setTimeout(() => this.loadComplaints(true), 300);
        },

        setMineFilter(mine) {
            this.mineOnly = mine;
            this.loadComplaints(true);
        },

        selectComplaint(id) {
            this.selectedId = id;
            var self = this;
            this.$nextTick(function() {
                var c = self.selectedComplaint;
                initPmap(c && c.lat ? c.lat : null, c && c.lng ? c.lng : null);
                if (c && c.isAnonim && c.id_petugas == {{ auth('petugas')->user()->id_petugas }}) {
                    self.fetchAnonimRespons();
                }
            });
        },

        get selectedComplaint() {
            return this.complaints.find(c => c.id === this.selectedId) ?? null;
        },

        get filteredComplaints() {
            return this.complaints;
        },

        async toggleKlarifikasi() {
            if (!this.klarifikasiOpen && this.klarifikasiMessages.length === 0) {
                await this.fetchKlarifikasi();
            }
            this.klarifikasiOpen = !this.klarifikasiOpen;
        },

        async fetchKlarifikasi() {
            this.klarifikasiLoading = true;
            const res = await fetch(`/petugas/klarifikasi/${this.selectedId}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            this.klarifikasiMessages = await res.json();
            this.klarifikasiLoading = false;
        },

        async sendKlarifikasi() {
            if (!this.klarifikasiInput.trim()) return;
            const fd = new FormData();
            fd.append('pesan', this.klarifikasiInput);
            if (this.klarifFoto) fd.append('foto', this.klarifFoto);
            const res = await fetch(`/petugas/klarifikasi/${this.selectedId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: fd,
            });
            if (res.status === 201) {
                const msg = await res.json();
                this.klarifikasiMessages.push(msg);
                this.klarifikasiInput = '';
                this.klarifFoto = null;
            }
        },

        async submitStatus(status) {
            const res = await fetch(`/petugas/pengaduan/${this.selectedId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ status }),
            });
            if (res.ok) {
                const data = await res.json();
                const c = this.complaints.find(c => c.id === this.selectedId);
                if (c) c.status = data.status;
            }
        },

        async submitTakedown() {
            if (this.takedownReason.trim().length < 10) return;
            this.takedownLoading = true;
            const res = await fetch(`/petugas/pengaduan/${this.selectedId}/takedown`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ reason: this.takedownReason }),
            });
            if (res.ok) {
                const data = await res.json();
                const c = this.complaints.find(c => c.id === this.selectedId);
                if (c) c.status = data.status;
                this.takedownOpen = false;
                this.takedownReason = '';
            }
            this.takedownLoading = false;
        },

        async submitTahapan() {
            if (this.tahapanPesan.trim().length < 5) return;
            const fd = new FormData();
            fd.append('pesan', this.tahapanPesan);
            if (this.tahapanFoto) fd.append('foto', this.tahapanFoto);
            const res = await fetch(`/petugas/pengaduan/${this.selectedId}/tahapan`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: fd,
            });
            if (res.status === 201) {
                const msg = await res.json();
                this.klarifikasiMessages.push(msg);
                this.tahapanPesan = '';
                this.tahapanFoto = null;
                this.tahapanOpen = false;
            }
        },

        async submitSelesai() {
            const res = await fetch(`/petugas/pengaduan/${this.selectedId}/selesai`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ pesan_tambahan: this.selesaiPesan }),
            });
            if (res.ok) {
                const data = await res.json();
                const c = this.complaints.find(c => c.id === this.selectedId);
                if (c) c.status = data.status;
                this.selesaiPesan = '';
                this.selesaiOpen = false;
                await this.fetchKlarifikasi();
            }
        },

        async fetchAnonimRespons() {
            this.anonimResponsLoading = true;
            const res = await fetch(`/petugas/pengaduan/${this.selectedId}/anonim-respons`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            this.anonimResponsMessages = await res.json();
            this.anonimResponsLoading = false;
        },

        async submitAnonimRespons() {
            if (this.anonimPesan.trim().length < 3) return;
            const fd = new FormData();
            fd.append('pesan', this.anonimPesan);
            if (this.anonimFoto) fd.append('foto', this.anonimFoto);
            const res = await fetch(`/petugas/pengaduan/${this.selectedId}/anonim-respons`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: fd,
            });
            if (res.status === 201) {
                const msg = await res.json();
                this.anonimResponsMessages.push(msg);
                this.anonimPesan = '';
                this.anonimFoto = null;
            }
        },

        avatarColor(name) {
            const colors = ['#f97316','#f59e0b','#10b981','#3b82f6','#8b5cf6','#ec4899'];
            return colors[(name ?? 'A').charCodeAt(0) % colors.length];
        },

        slaInfo(c) {
            void this.slaTick; // reactive ticker
            if (!c || !c.sla_deadline || !c.created_at_iso) return null;
            if (['selesai','tidak_valid'].includes(c.status)) return null;
            const created  = new Date(c.created_at_iso);
            const deadline = new Date(c.sla_deadline);
            const now      = new Date();
            const totalMs  = deadline - created;
            const remainMs = deadline - now;
            const pct      = totalMs > 0 ? remainMs / totalMs : 0;
            const overdue  = remainMs < 0;
            const abs      = Math.abs(remainMs);
            const h        = Math.floor(abs / 3600000);
            const m        = Math.floor((abs % 3600000) / 60000);
            const time     = h > 0 ? `${h}j ${m}m` : `${m}m`;

            let color, bg;
            if (overdue || pct <= 0.1) {
                color = '#991B1B'; bg = '#FEE2E2';
            } else if (pct <= 0.5) {
                color = '#92400E'; bg = '#FEF3C7';
            } else {
                color = '#065F46'; bg = '#ECFDF5';
            }

            return {
                label:  overdue ? `Lewat ${time}` : `Sisa ${time}`,
                color, bg, overdue,
            };
        },

        statusColor(status) {
            return { menunggu:'#F59E0B', proses:'#3B82F6', selesai:'#10B981', tidak_valid:'#EF4444' }[status] ?? '#9CA3AF';
        },

        statusBadgeHtml(status) {
            const cfg = {
                menunggu:    { label:'Menunggu',    bg:'#FEF3C7', color:'#92400E', dot:'#F59E0B' },
                proses:      { label:'Diproses',    bg:'#EFF6FF', color:'#1E40AF', dot:'#3B82F6' },
                selesai:     { label:'Selesai',     bg:'#ECFDF5', color:'#065F46', dot:'#10B981' },
                tidak_valid: { label:'Tidak Valid', bg:'#FEE2E2', color:'#991B1B', dot:'#EF4444' },
            }[status] ?? { label: status, bg:'#F3F4F6', color:'#374151', dot:'#9CA3AF' };
            return `<span style="display:inline-flex;align-items:center;gap:0.375rem;padding:0.25rem 0.625rem;border-radius:9999px;font-size:0.75rem;font-weight:500;background-color:${cfg.bg};color:${cfg.color};"><span style="width:0.375rem;height:0.375rem;border-radius:9999px;background-color:${cfg.dot};display:inline-block;"></span>${cfg.label}</span>`;
        },
    }
}
</script>
@endpush
