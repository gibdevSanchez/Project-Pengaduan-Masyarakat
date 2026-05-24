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
                    ['route' => 'admin.petugas.index',  'label' => 'Manajemen Petugas', 'is' => 'admin.petugas.*',
                     'icon'  => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                ]; @endphp
            @elseif($isPetugas)
                @php $links = [
                    ['route' => 'petugas.dashboard', 'label' => 'Daftar Pengaduan', 'is' => 'petugas.dashboard',
                     'icon'  => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
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
</body>
</html>
