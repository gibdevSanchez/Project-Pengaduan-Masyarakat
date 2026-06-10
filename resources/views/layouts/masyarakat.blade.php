<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'M-Lapor')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    @stack('head')
</head>
<body class="m-0 min-h-screen bg-stone-200">

@php $user = auth('masyarakat')->user(); @endphp

{{-- Phone shell --}}
<div class="w-full max-w-[420px] min-h-screen mx-auto bg-stone-100 relative flex flex-col md:shadow-[0_0_60px_rgba(0,0,0,0.18)]">

    @yield('header')

    @if(session('success'))
    <div class="mx-3 mt-3 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-[0.8125rem] flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mx-3 mt-3 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-[0.8125rem] flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        {{ session('error') }}
    </div>
    @endif

    <div class="flex-1 overflow-y-auto pb-20">
        @yield('content')
    </div>

    {{-- Bottom navigation --}}
    <nav class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-[420px] z-50 bg-white border-t border-stone-200 flex items-center shadow-[0_-4px_20px_rgba(0,0,0,0.07)]">

        @php
        $tabs = [
            ['route' => 'masyarakat.dashboard', 'label' => 'Beranda', 'is' => 'masyarakat.dashboard',
             'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>'],
            ['route' => 'masyarakat.berita',    'label' => 'Berita',   'is' => 'masyarakat.berita',
             'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>'],
        ];
        $tabsRight = [
            ['route' => 'masyarakat.riwayat', 'label' => 'Riwayat', 'is' => 'masyarakat.riwayat',
             'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>'],
            ['route' => 'masyarakat.profil',  'label' => 'Profil',  'is' => 'masyarakat.profil',
             'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>'],
        ];
        @endphp

        @foreach($tabs as $tab)
            @php $active = request()->routeIs($tab['is']); @endphp
            <a href="{{ route($tab['route']) }}"
               class="flex flex-col items-center pt-2 pb-1.5 flex-1 no-underline relative {{ $active ? 'text-orange-500' : 'text-stone-400' }}">
                @if($active)
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-6 h-[3px] bg-orange-500 rounded-b-full"></div>
                @endif
                <svg class="w-[22px] h-[22px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $tab['icon'] !!}</svg>
                <span class="text-[10px] mt-0.5 {{ $active ? 'font-semibold' : 'font-medium' }}">{{ $tab['label'] }}</span>
            </a>
        @endforeach

        {{-- Center LAPOR FAB --}}
        <div class="flex-1 flex justify-center relative -top-3">
            <a href="{{ route('masyarakat.pengaduan.create') }}" class="flex flex-col items-center gap-0.5 no-underline">
                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-orange-500 to-orange-600 shadow-[0_4px_20px_rgba(234,88,12,0.5),0_0_0_4px_white] flex items-center justify-center transition-all duration-150 active:scale-95 hover:shadow-[0_6px_24px_rgba(234,88,12,0.6),0_0_0_4px_white]">
                    <svg class="w-[26px] h-[26px] text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <span class="text-[10px] font-bold text-orange-500 mt-0.5">LAPOR</span>
            </a>
        </div>

        @foreach($tabsRight as $tab)
            @php $active = request()->routeIs($tab['is']); @endphp
            <a href="{{ route($tab['route']) }}"
               class="flex flex-col items-center pt-2 pb-1.5 flex-1 no-underline relative {{ $active ? 'text-orange-500' : 'text-stone-400' }}">
                @if($active)
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-6 h-[3px] bg-orange-500 rounded-b-full"></div>
                @endif
                @if($tab['route'] === 'masyarakat.profil' && $user?->foto_profil)
                <img src="{{ Storage::url($user->foto_profil) }}"
                     class="w-[22px] h-[22px] rounded-full object-cover {{ $active ? 'ring-2 ring-orange-500' : 'ring-1 ring-stone-300' }}">
                @else
                <svg class="w-[22px] h-[22px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $tab['icon'] !!}</svg>
                @endif
                <span class="text-[10px] mt-0.5 {{ $active ? 'font-semibold' : 'font-medium' }}">{{ $tab['label'] }}</span>
            </a>
        @endforeach
    </nav>
</div>

@stack('scripts')

{{-- Notification toast --}}
<div x-data="notifToast()"
     class="fixed top-3 left-1/2 -translate-x-1/2 z-[70] w-[calc(min(100vw,420px)-24px)] pointer-events-none">
    <template x-for="t in toasts" :key="t.id">
        <div class="mb-2 pointer-events-auto"
             x-show="t.visible"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-3"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-3">
            <a :href="t.url ?? '#'"
               class="flex items-start gap-3 px-4 py-3 bg-white rounded-2xl shadow-lg border border-stone-200 no-underline">
                <div class="w-2 h-2 rounded-full bg-orange-500 shrink-0 mt-1.5"></div>
                <p class="flex-1 text-xs text-stone-800 leading-relaxed" x-text="t.message"></p>
                <button @click.prevent="dismiss(t.id)"
                        class="shrink-0 w-4 h-4 flex items-center justify-center text-stone-400 hover:text-stone-600 cursor-pointer border-0 bg-transparent mt-0.5">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </a>
        </div>
    </template>
</div>

<script>
function notifToast() {
    return {
        toasts: [],
        lastUnread: null,
        init() {
            this.poll();
            setInterval(() => this.poll(), 45000);
        },
        async poll() {
            try {
                const r = await fetch('{{ route('masyarakat.notifications') }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const d = await r.json();
                const current = d.unread_count;
                if (this.lastUnread !== null && current > this.lastUnread && d.notifications.length) {
                    const newest = d.notifications.find(n => !n.read);
                    if (newest) this.show(newest);
                }
                this.lastUnread = current;
            } catch {}
        },
        show(notif) {
            const id = Date.now();
            this.toasts.push({ id, message: notif.message, url: notif.url, visible: true });
            setTimeout(() => this.dismiss(id), 5000);
        },
        dismiss(id) {
            const t = this.toasts.find(t => t.id === id);
            if (t) t.visible = false;
            setTimeout(() => { this.toasts = this.toasts.filter(t => t.id !== id); }, 300);
        }
    }
}
</script>
</body>
</html>
