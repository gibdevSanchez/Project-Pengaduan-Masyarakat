@extends('layouts.app')

@section('title', 'Daftar Pengaduan')

@section('content')
<div class="flex flex-1 overflow-hidden h-full" x-data="petugasApp()">

    {{-- Inner toggleable sidebar --}}
    <div x-show="sidebarOpen"
         x-cloak
         class="w-60 shrink-0 border-r border-stone-200 bg-white flex flex-col overflow-hidden">

        <div class="p-4 border-b border-stone-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-orange-500 flex items-center justify-center text-white font-black text-sm shrink-0">
                    {{ strtoupper(substr(auth('petugas')->user()->nama_petugas, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-bold text-stone-900 truncate">{{ auth('petugas')->user()->nama_petugas }}</p>
                    <p class="text-xs font-light text-stone-500">Petugas</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 px-2 py-3 flex flex-col gap-0.5">
            <button type="button" @click="setMineFilter(false)"
                    :class="!mineOnly
                        ? 'border-l-[3px] border-orange-500 bg-orange-50 text-orange-600 font-semibold'
                        : 'border-l-[3px] border-transparent text-stone-500 hover:bg-orange-50 hover:text-stone-900'"
                    class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm cursor-pointer font-sans border-0 bg-transparent transition-all">
                <svg class="w-[1.125rem] h-[1.125rem] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                Semua Pengaduan
            </button>
            <button type="button" @click="setMineFilter(true)"
                    :class="mineOnly
                        ? 'border-l-[3px] border-orange-500 bg-orange-50 text-orange-600 font-semibold'
                        : 'border-l-[3px] border-transparent text-stone-500 hover:bg-orange-50 hover:text-stone-900'"
                    class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm cursor-pointer font-sans border-0 bg-transparent transition-all">
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
        <div class="p-3 border-b border-stone-200 shrink-0">
            <div class="flex items-center gap-2 mb-2.5">
                <button type="button" @click="sidebarOpen = !sidebarOpen"
                        class="flex items-center justify-center w-8 h-8 rounded-lg border border-stone-200 bg-white text-stone-500 hover:border-orange-400 hover:text-orange-500 transition-colors shrink-0 cursor-pointer font-sans">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div class="relative flex-1">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" x-model="search" @input="onSearchChange()" placeholder="Cari pengaduan..."
                           class="w-full pl-9 pr-3 py-2 rounded-lg border border-stone-200 bg-white text-[0.8125rem] text-stone-900 font-sans outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-400/20">
                </div>
            </div>
        </div>

        {{-- Single filter row: status chips + kategori dropdown --}}
        <div class="flex items-center gap-1.5 px-3 py-2 border-b border-stone-200 shrink-0 overflow-x-auto">
            @foreach(['' => 'Semua', 'menunggu' => 'Menunggu', 'proses' => 'Proses', 'selesai' => 'Selesai', 'tidak_valid' => 'Tidak Valid'] as $val => $lbl)
            <button type="button"
                    @click="filterStatus = '{{ $val }}'; loadComplaints(true)"
                    :class="filterStatus === '{{ $val }}'
                        ? 'bg-orange-500 text-white border-orange-500'
                        : 'bg-white text-stone-500 border-stone-200'"
                    class="px-2.5 py-1 rounded-full text-[0.6875rem] font-medium whitespace-nowrap cursor-pointer font-sans border transition-all shrink-0">
                {{ $lbl }}
            </button>
            @endforeach
            <select x-model="filterKategori" @change="loadComplaints(true)"
                    class="ml-auto px-2 py-1 rounded-lg border border-stone-200 bg-white text-[0.6875rem] font-medium text-stone-600 outline-none cursor-pointer shrink-0">
                <option value="">Kategori</option>
                <option value="infrastruktur">Infrastruktur</option>
                <option value="lingkungan">Lingkungan</option>
                <option value="keamanan">Keamanan</option>
                <option value="sosial">Sosial</option>
                <option value="lainnya">Lainnya</option>
            </select>
        </div>

        {{-- List --}}
        <div class="flex-1 overflow-y-auto">
            <template x-if="filteredComplaints.length === 0 && !loading">
                <div class="p-12 text-center text-stone-400 text-sm font-light">Tidak ada pengaduan.</div>
            </template>
            <template x-for="c in filteredComplaints" :key="c.id">
                <div @click="selectComplaint(c.id)"
                     :class="selectedId === c.id
                         ? 'bg-white border-l-[3px] border-l-orange-500 shadow-[1px_0_8px_rgba(0,0,0,0.06)]'
                         : 'border-l-[3px] border-l-transparent hover:bg-orange-50'"
                     class="flex items-start gap-2.5 px-3 py-3.5 cursor-pointer transition-all border-b border-stone-200">

                    <div :style="`width:2.25rem;height:2.25rem;border-radius:9999px;background-color:${avatarColor(c.nama)};display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:0.8125rem;flex-shrink:0;`"
                         x-text="c.initials"></div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-0.5">
                            <span class="text-sm font-semibold text-stone-900 truncate" x-text="c.nama"></span>
                            <span class="text-[0.6875rem] font-light text-stone-400 shrink-0 ml-2" x-text="c.tgl"></span>
                        </div>
                        <p class="text-xs text-stone-500 truncate" x-text="c.snippet"></p>
                        <div class="flex items-center gap-1.5 mt-1.5 flex-wrap">
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
                            <span x-show="c.lokasi" class="text-[0.625rem] text-stone-400 truncate" x-text="'📍 ' + c.lokasi"></span>
                        </div>

                        <template x-if="!c.id_petugas">
                            <form :action="'/petugas/pengaduan/' + c.id + '/assign'" method="POST" class="mt-2" @click.stop>
                                @csrf
                                <button type="submit"
                                        class="w-full px-2 py-1 text-[0.6875rem] font-semibold bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition-colors border-0 cursor-pointer font-sans">
                                    Ambil Pengaduan
                                </button>
                            </form>
                        </template>
                        <template x-if="c.id_petugas">
                            <p class="text-[0.625rem] text-stone-400 mt-1.5 font-medium">👤 Pengaduan Saya</p>
                        </template>

                        <div class="flex justify-end mt-1.5">
                            <div :style="`width:0.5rem;height:0.5rem;border-radius:9999px;background-color:${statusColor(c.status)};`"></div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <div class="p-3 border-t border-stone-200 shrink-0">
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
        <div x-show="!selectedId" class="flex-1 flex flex-col items-center justify-center gap-3">
            <svg class="w-12 h-12 text-stone-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="font-medium text-[0.9375rem] text-stone-900">Pilih pengaduan</p>
            <p class="font-light text-sm text-stone-500">untuk melihat detail dan memberi tanggapan</p>
        </div>

        {{-- Selected complaint detail --}}
        <template x-if="selectedId && selectedComplaint">
            <div class="flex flex-col h-full overflow-hidden">

                {{-- Zone 1: Sticky header --}}
                <div class="flex items-center gap-3 px-5 py-4 border-b border-stone-200 bg-white shrink-0">
                    <div :style="`width:2.5rem;height:2.5rem;border-radius:9999px;background-color:${avatarColor(selectedComplaint.nama)};display:flex;align-items:center;justify-content:center;color:white;font-weight:700;`"
                         x-text="selectedComplaint.initials"></div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-[0.9375rem] text-stone-900 truncate" x-text="selectedComplaint.nama"></p>
                        <p class="font-light text-xs text-stone-500" x-text="selectedComplaint.tgl"></p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="text-[0.625rem] px-1.5 py-0.5 rounded-full font-medium"
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

                {{-- Zone 2: Scrollable body --}}
                <div class="flex-1 overflow-y-auto bg-stone-50">

                    {{-- Block A: Isi Laporan + Foto --}}
                    <div class="p-5 border-b border-stone-200">
                        <p class="text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wide mb-2">Laporan</p>
                        <p class="text-sm text-stone-900 leading-relaxed" x-text="selectedComplaint.isiLaporan"></p>
                        <template x-if="selectedComplaint.foto">
                            <a :href="selectedComplaint.foto" target="_blank" class="block mt-3">
                                <img :src="selectedComplaint.foto" class="w-full max-h-48 object-cover rounded-xl cursor-pointer">
                            </a>
                        </template>
                    </div>

                    {{-- Block B: Lokasi --}}
                    <template x-if="selectedComplaint.lokasi">
                        <div class="px-5 py-4 border-b border-stone-200">
                            <p class="text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wide mb-1">Lokasi</p>
                            <p class="text-sm text-stone-900 mb-1.5" x-text="selectedComplaint.lokasi"></p>
                            <a :href="'https://maps.google.com/?q=' + encodeURIComponent(selectedComplaint.lokasi)"
                               target="_blank"
                               class="text-sm text-orange-500 hover:text-orange-600 underline">
                                Lihat di Google Maps →
                            </a>
                        </div>
                    </template>

                    {{-- Block C: Reporter info (non-anon only) --}}
                    <template x-if="!selectedComplaint.isAnonim">
                        <div class="px-5 py-4 border-b border-stone-200">
                            <p class="text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wide mb-3">Pelapor</p>
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

                    {{-- Block D: Klarifikasi thread --}}
                    <template x-if="!selectedComplaint.isAnonim && klarifikasiOpen">
                        <div class="px-5 py-4">
                            <p class="text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wide mb-3">Klarifikasi</p>

                            <template x-if="klarifikasiLoading">
                                <p class="text-center text-xs text-stone-400 py-4">Memuat...</p>
                            </template>

                            <template x-if="!klarifikasiLoading && klarifikasiMessages.length === 0">
                                <p class="text-center text-xs text-stone-400 py-4 italic">Belum ada pesan. Kirim pertanyaan di bawah.</p>
                            </template>

                            <div class="flex flex-col gap-3 mb-4">
                                <template x-for="(msg, i) in klarifikasiMessages" :key="i">
                                    <div :class="msg.dari === 'petugas' ? 'flex justify-end' : 'flex justify-start'">
                                        <div>
                                            <template x-if="msg.dari === 'masyarakat'">
                                                <p class="text-[0.6875rem] font-medium text-stone-400 mb-1" x-text="selectedComplaint.nama"></p>
                                            </template>
                                            <div :class="msg.dari === 'petugas'
                                                    ? 'bg-orange-500 text-white rounded-2xl rounded-br-sm'
                                                    : 'bg-stone-100 text-stone-900 rounded-2xl rounded-bl-sm'"
                                                 class="max-w-[20rem] px-4 py-3 text-sm leading-relaxed"
                                                 x-text="msg.pesan"></div>
                                            <p class="text-[0.6875rem] font-light text-stone-400 mt-1"
                                               :class="msg.dari === 'petugas' ? 'text-right' : 'text-left'"
                                               x-text="msg.created_at"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <template x-if="!['selesai','tidak_valid'].includes(selectedComplaint.status)">
                                <div class="flex gap-2">
                                    <input type="text" x-model="klarifikasiInput"
                                           @keydown.enter.prevent="sendKlarifikasi()"
                                           placeholder="Tulis pertanyaan untuk masyarakat..."
                                           class="flex-1 px-4 py-2.5 rounded-xl border border-stone-200 bg-white text-sm font-sans text-stone-900 outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-400/20">
                                    <button @click="sendKlarifikasi()"
                                            :disabled="!klarifikasiInput.trim()"
                                            class="px-4 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold border-0 cursor-pointer font-sans transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                        Kirim
                                    </button>
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
                        <button x-show="!selectedComplaint.isAnonim"
                                @click="toggleKlarifikasi()"
                                :class="klarifikasiOpen
                                    ? 'bg-orange-500 text-white'
                                    : 'border border-stone-200 bg-white text-stone-600 hover:bg-orange-50'"
                                class="flex-1 py-2.5 rounded-xl text-sm font-semibold border-0 cursor-pointer font-sans transition-colors">
                            💬 Klarifikasi
                        </button>
                        <button x-show="!['selesai','tidak_valid'].includes(selectedComplaint.status)"
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

@push('scripts')
<script>
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
        klarifikasiOpen: false,
        klarifikasiMessages: [],
        klarifikasiLoading: false,
        klarifikasiInput: '',
        takedownOpen: false,
        takedownReason: '',
        takedownLoading: false,

        async init() {
            this.$watch('selectedId', () => {
                this.klarifikasiOpen = false;
                this.klarifikasiMessages = [];
                this.klarifikasiInput = '';
                this.takedownOpen = false;
                this.takedownReason = '';
            });
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
            const res = await fetch(`/petugas/klarifikasi/${this.selectedId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ pesan: this.klarifikasiInput }),
            });
            if (res.status === 201) {
                const msg = await res.json();
                this.klarifikasiMessages.push(msg);
                this.klarifikasiInput = '';
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

        avatarColor(name) {
            const colors = ['#f97316','#f59e0b','#10b981','#3b82f6','#8b5cf6','#ec4899'];
            return colors[(name ?? 'A').charCodeAt(0) % colors.length];
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
