<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'M-Lapor')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
</head>
<body class="m-0 min-h-screen bg-slate-200">

@php $user = auth('masyarakat')->user(); @endphp

{{-- Phone shell --}}
<div class="w-full max-w-[420px] min-h-screen mx-auto bg-stone-100 relative flex flex-col md:shadow-[0_0_60px_rgba(0,0,0,0.18)]">

    {{-- Header slot --}}
    @yield('header')

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="mx-3 mt-3 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-[0.8125rem] flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mx-3 mt-3 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-[0.8125rem]">
        {{ session('error') }}
    </div>
    @endif

    {{-- Page content --}}
    <div class="flex-1 overflow-y-auto pb-20">
        @yield('content')
    </div>

    {{-- Bottom navigation --}}
    <nav class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-[420px] z-50 bg-white border-t border-stone-200 flex items-center">

        @php
        $tabs = [
            ['route' => 'masyarakat.dashboard', 'label' => 'Beranda', 'is' => 'masyarakat.dashboard',
             'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>'],
            ['route' => 'masyarakat.berita',    'label' => 'Berita',   'is' => 'masyarakat.berita',
             'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>'],
        ];
        $tabsRight = [
            ['route' => 'masyarakat.riwayat', 'label' => 'Riwayat', 'is' => 'masyarakat.riwayat',
             'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>'],
            ['route' => 'masyarakat.profil',  'label' => 'Profil',  'is' => 'masyarakat.profil',
             'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>'],
        ];
        @endphp

        {{-- Left tabs --}}
        @foreach($tabs as $tab)
            @php $active = request()->routeIs($tab['is']); @endphp
            <a href="{{ route($tab['route']) }}"
               class="flex flex-col items-center gap-0.5 py-2 flex-1 no-underline {{ $active ? 'text-orange-500' : 'text-stone-400' }}">
                <svg class="w-[22px] h-[22px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $tab['icon'] !!}</svg>
                <span class="text-[10px] font-medium">{{ $tab['label'] }}</span>
            </a>
        @endforeach

        {{-- Center LAPOR button --}}
        <div class="flex-1 flex justify-center relative -top-3">
            <a href="{{ route('masyarakat.pengaduan.create') }}" class="flex flex-col items-center gap-0.5 no-underline">
                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-orange-500 to-orange-600 shadow-[0_4px_20px_rgba(234,88,12,0.55),0_0_0_4px_white] flex items-center justify-center transition-all duration-150 active:scale-95">
                    <svg class="w-[26px] h-[26px] text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <span class="text-[10px] font-bold text-orange-500 mt-1">LAPOR</span>
            </a>
        </div>

        {{-- Right tabs --}}
        @foreach($tabsRight as $tab)
            @php $active = request()->routeIs($tab['is']); @endphp
            <a href="{{ route($tab['route']) }}"
               class="flex flex-col items-center gap-0.5 py-2 flex-1 no-underline {{ $active ? 'text-orange-500' : 'text-stone-400' }}">
                <svg class="w-[22px] h-[22px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $tab['icon'] !!}</svg>
                <span class="text-[10px] font-medium">{{ $tab['label'] }}</span>
            </a>
        @endforeach
    </nav>
</div>

@stack('scripts')
</body>
</html>
