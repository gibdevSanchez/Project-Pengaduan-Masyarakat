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
<body class="bg-white min-h-screen m-0  overflow-hidden">

<div class="flex min-h-screen">

    {{-- Left branding panel (desktop only) --}}
    <div class="hidden lg:flex flex-col justify-between w-[60%] p-12 relative overflow-hidden bg-[#DF6501]">

        {{-- Decorative blobs --}}
        <div class="absolute -bottom-32 -left-32 w-[28rem] h-[28rem] rounded-full bg-white opacity-[0.15] pointer-events-none"></div>
        <div class="absolute -top-24 -right-24 w-72 h-72 rounded-full bg-white opacity-[0.16] pointer-events-none"></div>

        <div>
           <div class="flex items-center gap-3 mb-16">

            {{-- Logo --}}
            <img
                src="/assets/Logo M-LAPOR Small.svg"
                alt="Logo"
                class="h-20 w-auto object-contain">

        </div>

            {{-- Hero --}}
            <h1 class="text-7xl font-black leading-none text-white mb-4">
                Laporan<br>Warga.<br>
                <span class="text-white/70">Ditindak.</span>
            </h1>
            <p class="font-light text-lg text-white/70 mb-12">Sistem Pengaduan Masyarakat</p>

            {{-- Features --}}
            <div class="flex flex-col gap-4">
                @foreach(['Lapor kapan saja, dari mana saja', 'Pantau status pengaduan Anda secara real-time', 'Respons resmi dari petugas berwenang'] as $feat)
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-white shrink-0"></div>
                        <span class="text-sm text-white/80">{{ $feat }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <p class="text-xs font-light text-white/50">Sistem Informasi &middot; Universitas</p>
    </div>

    {{-- Right form panel --}}
    <div class="flex-1 flex items-center justify-center p-8 bg-white border-l border-stone-100">
        <div class="w-full max-w-[22rem]">
            @yield('form')
        </div>
    </div>

</div>
</body>
</html>
