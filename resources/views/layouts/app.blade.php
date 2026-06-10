<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — M-Lapor</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    @stack('head-scripts')
</head>
<body class="bg-stone-50 h-screen overflow-hidden m-0">

@php
    $isAdmin      = auth('petugas')->check() && auth('petugas')->user()->level === 'admin';
    $isPetugas    = auth('petugas')->check() && !$isAdmin;
    $namaUser     = auth('petugas')->user()->nama_petugas ?? auth('masyarakat')->user()->nama ?? 'Pengguna';
    $roleUser     = auth('petugas')->user()->level ?? 'Masyarakat';
    $initials     = strtoupper(substr($namaUser, 0, 1));
    $fotoProfil   = auth('petugas')->user()->foto_profil ?? null;
@endphp

<div class="flex h-screen">

    {{-- Sidebar --}}
    <aside class="w-[220px] shrink-0 bg-white border-r border-stone-200 flex flex-col overflow-hidden">

        {{-- Brand strip --}}
        <div class="bg-gradient-to-br from-orange-500 to-orange-700 px-4 py-4 flex items-center gap-2.5 shrink-0">
            <div class="w-8 h-8 rounded-lg bg-white/20 border border-white/20 flex items-center justify-center text-white font-black text-sm shrink-0">M</div>
            <div>
                <p class="font-bold text-[0.9375rem] text-white leading-none tracking-tight">M-Lapor</p>
                <p class="text-[0.625rem] font-medium text-white/65 leading-none mt-0.5 uppercase tracking-[0.07em]">
                    @if($isAdmin) Administrator @elseif($isPetugas) Petugas @else Masyarakat @endif
                </p>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 px-2 py-3 flex flex-col gap-0.5 overflow-y-auto">

            @if($isAdmin)
                @php $links = [
                    ['route' => 'admin.dashboard',      'label' => 'Dashboard',         'is' => 'admin.dashboard',
                     'icon'  => 'M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10-3a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1v-7z'],
                    ['route' => 'admin.pengaduan.index', 'label' => 'Pengaduan',         'is' => 'admin.pengaduan.*',
                     'icon'  => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                    ['route' => 'admin.berita.index',   'label' => 'Berita',            'is' => 'admin.berita.*',
                     'icon'  => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z'],
                    ['route' => 'admin.petugas.index',  'label' => 'Manajemen Petugas', 'is' => 'admin.petugas.*',
                     'icon'  => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                    ['route' => 'admin.feedback.index', 'label' => 'Feedback',          'is' => 'admin.feedback.*',
                     'icon'  => 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z'],
                    ['route' => 'admin.expired.index',  'label' => 'Expired Data',      'is' => 'admin.expired.*',
                     'icon'  => 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'],
                    ['route' => 'admin.utilitas',      'label' => 'Utilitas',          'is' => 'admin.utilitas*',
                     'icon'  => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
                ]; @endphp
            @elseif($isPetugas)
                @php $links = [
                    ['route' => 'petugas.dashboard', 'label' => 'Daftar Pengaduan', 'is' => 'petugas.dashboard',
                     'icon'  => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                    ['route' => 'petugas.berita.index', 'label' => 'Berita Saya',     'is' => 'petugas.berita.*',
                     'icon'  => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z'],
                    ['route' => 'petugas.feedback.index', 'label' => 'Feedback',     'is' => 'petugas.feedback.*',
                     'icon'  => 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z'],
                    ['route' => 'petugas.profil',    'label' => 'Profil Saya',      'is' => 'petugas.profil*',
                     'icon'  => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                ]; @endphp
            @else
                @php $links = [
                    ['route' => 'masyarakat.dashboard',        'label' => 'Dashboard',      'is' => 'masyarakat.dashboard',
                     'icon'  => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                    ['route' => 'masyarakat.pengaduan.create', 'label' => 'Buat Pengaduan', 'is' => 'masyarakat.pengaduan.create',
                     'icon'  => 'M12 4v16m8-8H4'],
                ]; @endphp
            @endif

            @foreach($links as $link)
                @php $active = request()->routeIs($link['is']); @endphp
                <a href="{{ route($link['route']) }}"
                   @class([
                       'flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-medium no-underline transition-all duration-150',
                       'bg-orange-500 text-white font-semibold shadow-sm shadow-orange-200' => $active,
                       'text-stone-500 hover:bg-stone-100 hover:text-stone-800'             => !$active,
                   ])>
                    <svg class="w-[1.0625rem] h-[1.0625rem] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $link['icon'] }}"/>
                    </svg>
                    {{ $link['label'] }}
                </a>
            @endforeach
        </nav>

        {{-- Notification bell --}}
        <div x-data="notifPetugas()" class="px-3 pb-1 shrink-0 relative">
            <button @click="toggle()" class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-stone-500 hover:bg-stone-100 hover:text-stone-800 transition-all duration-150 relative">
                <svg class="w-[1.0625rem] h-[1.0625rem] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span class="flex-1 text-left">Notifikasi</span>
                <span x-show="unreadCount > 0" x-text="unreadCount > 9 ? '9+' : unreadCount"
                      class="min-w-[20px] h-5 px-1 rounded-full bg-orange-500 text-white text-[10px] font-bold flex items-center justify-center"></span>
            </button>

            <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 scale-95"
                 class="absolute bottom-full left-3 right-3 mb-1 bg-white rounded-xl shadow-xl border border-stone-200 overflow-hidden z-50">
                <div class="flex items-center justify-between px-3 py-2.5 border-b border-stone-100">
                    <span class="text-xs font-semibold text-stone-800">Notifikasi</span>
                    <button @click="markAllRead()" x-show="unreadCount > 0" class="text-[10px] text-orange-500 font-medium hover:text-orange-600">Semua dibaca</button>
                </div>
                <div class="max-h-72 overflow-y-auto">
                    <template x-if="loading"><div class="py-6 text-center text-stone-400 text-xs">Memuat...</div></template>
                    <template x-if="!loading && notifs.length === 0"><div class="py-6 text-center text-stone-400 text-xs">Tidak ada notifikasi</div></template>
                    <template x-for="n in notifs" :key="n.id">
                        <a :href="n.url ?? '#'" @click="!n.read && markRead(n.id)"
                           class="flex items-start gap-2.5 px-3 py-2.5 hover:bg-stone-50 border-b border-stone-50 no-underline transition-colors"
                           :class="n.read ? 'opacity-60' : ''">
                            <div class="w-1.5 h-1.5 rounded-full shrink-0 mt-1.5" :class="n.read ? 'bg-stone-300' : 'bg-orange-500'"></div>
                            <div class="min-w-0">
                                <p class="text-[0.6875rem] text-stone-800 leading-relaxed" x-text="n.message"></p>
                                <p class="text-[10px] text-stone-400 mt-0.5" x-text="n.created_at"></p>
                            </div>
                        </a>
                    </template>
                </div>
            </div>
        </div>

        {{-- User + logout --}}
        <div class="p-3 border-t border-stone-100 bg-stone-50/80 shrink-0">
            <div class="flex items-center gap-2.5 px-2 py-1.5 mb-2">
                @if($fotoProfil)
                <img src="{{ Storage::url($fotoProfil) }}" alt="{{ $namaUser }}"
                     class="w-8 h-8 rounded-full object-cover shrink-0 ring-2 ring-orange-200 ring-offset-1">
                @else
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white text-xs font-bold shrink-0 ring-2 ring-orange-200 ring-offset-1">{{ $initials }}</div>
                @endif
                <div class="min-w-0 flex-1">
                    <p class="text-[0.8125rem] font-semibold text-stone-900 truncate leading-snug">{{ $namaUser }}</p>
                    <p class="text-[0.6875rem] font-light text-stone-400 capitalize">{{ $roleUser }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-[0.8125rem] font-medium text-stone-500 bg-white border border-stone-200 cursor-pointer font-sans transition-all hover:bg-red-50 hover:text-red-600 hover:border-red-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- Main content --}}
    <div class="flex-1 flex flex-col overflow-hidden min-w-0">

        @if(session('success'))
            <div class="px-6 py-2.5 bg-emerald-50 border-b border-emerald-200 text-emerald-800 text-sm flex items-center gap-2 shrink-0">
                <svg class="w-4 h-4 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="px-6 py-2.5 bg-red-50 border-b border-red-200 text-red-600 text-sm flex items-center gap-2 shrink-0">
                <svg class="w-4 h-4 shrink-0 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </div>
</div>

@stack('scripts')
<script>
function notifPetugas() {
    @if($isAdmin)
    const FETCH_URL = '{{ route('admin.notifications') }}';
    const MARK_URL  = '{{ route('admin.notifications.read') }}';
    @else
    const FETCH_URL = '{{ route('petugas.notifications') }}';
    const MARK_URL  = '{{ route('petugas.notifications.read') }}';
    @endif
    return {
        open: false, notifs: [], unreadCount: 0, loading: false,
        init() { this.fetchNotifs(); setInterval(() => this.fetchCount(), 45000); },
        async fetchNotifs() {
            this.loading = true;
            const r = await fetch(FETCH_URL, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const d = await r.json(); this.notifs = d.notifications; this.unreadCount = d.unread_count; this.loading = false;
        },
        async fetchCount() {
            const r = await fetch(FETCH_URL, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const d = await r.json(); this.unreadCount = d.unread_count; if (this.open) this.notifs = d.notifications;
        },
        async markRead(id) {
            const csrf = document.querySelector('meta[name="csrf-token"]').content;
            await fetch(MARK_URL, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' }, body: JSON.stringify({ id }) });
            const n = this.notifs.find(n => n.id === id); if (n) n.read = true; this.unreadCount = Math.max(0, this.unreadCount - 1);
        },
        async markAllRead() {
            const csrf = document.querySelector('meta[name="csrf-token"]').content;
            await fetch(MARK_URL, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' }, body: JSON.stringify({}) });
            this.notifs.forEach(n => n.read = true); this.unreadCount = 0;
        },
        toggle() { this.open = !this.open; if (this.open) this.fetchNotifs(); }
    }
}
</script>
</body>
</html>
