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
<body class="bg-white min-h-screen m-0 overflow-hidden">

<div class="flex min-h-screen">

    {{-- Left branding panel --}}
    <div class="hidden lg:flex flex-col justify-between w-[58%] p-12 relative overflow-hidden bg-gradient-to-br from-orange-500 via-orange-600 to-orange-900">

        {{-- Dot grid pattern --}}
        <div class="absolute inset-0 pointer-events-none opacity-[0.13]"
             style="background-image:radial-gradient(circle,#fff 1px,transparent 1px);background-size:28px 28px;"></div>

        {{-- Depth blobs --}}
        <div class="absolute -bottom-28 -left-28 w-96 h-96 rounded-full bg-orange-400/20 blur-3xl pointer-events-none"></div>
        <div class="absolute -top-20 right-0 w-80 h-80 rounded-full bg-orange-950/30 blur-3xl pointer-events-none"></div>

        {{-- Top: Logo --}}
        <div class="relative">
            <div class="flex items-center gap-3">
                <img src="/assets/Logo M-LAPOR Small.svg" alt="M-Lapor" class="h-14 w-auto object-contain drop-shadow-sm">
            </div>
        </div>

        {{-- Middle: Hero --}}
        <div class="relative">
            <div class="inline-flex items-center gap-2 bg-white/15 border border-white/25 text-white text-xs font-medium px-3 py-1.5 rounded-full mb-6">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse shrink-0"></span>
                Sistem aktif 24 jam
            </div>

            <h1 class="text-[3.75rem] font-black leading-[1.06] text-white mb-5 tracking-tight">
                Laporan<br>Warga.<br><span class="text-white/55">Ditindak.</span>
            </h1>
            <p class="font-light text-base text-white/70 mb-10 max-w-xs leading-relaxed">
                Platform pengaduan masyarakat yang transparan, cepat, dan dapat dipercaya.
            </p>

            <div class="flex flex-col gap-3.5">
                @foreach([
                    ['icon' => 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z',
                     'text' => 'Lapor kapan saja, dari mana saja'],
                    ['icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                     'text' => 'Pantau status pengaduan secara real-time'],
                    ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                     'text' => 'Respons resmi dari petugas berwenang'],
                ] as $f)
                <div class="flex items-center gap-3.5">
                    <div class="w-8 h-8 rounded-lg bg-white/15 border border-white/20 flex items-center justify-center shrink-0">
                        <svg class="w-[0.9375rem] h-[0.9375rem] text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $f['icon'] }}"/>
                        </svg>
                    </div>
                    <span class="text-sm text-white/85 leading-snug">{{ $f['text'] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Bottom: Stats --}}
        <div class="relative border-t border-white/20 pt-6 grid grid-cols-3 gap-4">
            @foreach([['1.2K+','Laporan masuk'],['94%','Diselesaikan'],['<24j','Respons rata-rata']] as [$num,$lbl])
            <div>
                <p class="text-2xl font-black text-white leading-none">{{ $num }}</p>
                <p class="text-[0.6875rem] text-white/55 mt-1">{{ $lbl }}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Right form panel --}}
    <div class="flex-1 flex flex-col items-center justify-center p-8 bg-white border-l border-stone-100">

        {{-- Mobile-only logo --}}
        <div class="flex items-center gap-2 mb-8 lg:hidden">
            <div class="w-8 h-8 rounded-xl bg-orange-500 flex items-center justify-center">
                <span class="text-white font-black text-sm">M</span>
            </div>
            <span class="font-bold text-stone-900 text-lg">M-Lapor</span>
        </div>

        <div class="w-full max-w-[22rem]">
            @yield('form')
        </div>
    </div>

</div>
</body>
</html>
