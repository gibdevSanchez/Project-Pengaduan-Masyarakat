<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lacak Pengaduan — M-Lapor</title>
    @vite(['resources/css/app.css'])
    @php use Illuminate\Support\Facades\Storage; @endphp
</head>
<body class="min-h-screen bg-stone-100 flex flex-col items-center justify-start py-10 px-4">

    {{-- Logo --}}
    <div class="flex items-center gap-2.5 mb-8">
        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-orange-500 to-orange-700 flex items-center justify-center">
            <span class="text-white font-black text-base">M</span>
        </div>
        <span class="font-bold text-stone-900 text-xl tracking-tight">M-Lapor</span>
    </div>

    <div class="w-full max-w-md">

        {{-- Search card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-stone-200 p-6 mb-5">
            <h1 class="text-lg font-bold text-stone-900 mb-1">Lacak Pengaduan</h1>
            <p class="text-sm text-stone-500 font-light mb-5">Masukkan kode lacak yang Anda terima saat mengirim pengaduan.</p>

            <form method="GET" action="{{ route('track') }}" class="flex gap-2">
                <input type="text" name="code"
                       value="{{ $code ?? '' }}"
                       placeholder="Contoh: LPR-4X7K2M"
                       maxlength="12"
                       class="flex-1 px-4 py-3 rounded-xl border border-stone-200 bg-stone-50 text-sm text-stone-900 font-mono tracking-wider uppercase outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-400/20 transition-all">
                <button type="submit"
                        class="px-5 py-3 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold border-0 cursor-pointer transition-colors shadow-sm shadow-orange-200">
                    Cek
                </button>
            </form>

            @if($notFound)
            <div class="mt-4 flex items-center gap-2 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Kode <span class="font-mono font-semibold mx-1">{{ $code }}</span> tidak ditemukan.
            </div>
            @endif
        </div>

        @if($pengaduan)
        @php
        $statusCfg = [
            'menunggu'    => ['label' => 'Menunggu',    'class' => 'bg-amber-100 text-amber-700',    'dot' => 'bg-amber-400',    'border' => 'border-l-amber-400'],
            'proses'      => ['label' => 'Sedang Diproses', 'class' => 'bg-blue-100 text-blue-700',  'dot' => 'bg-blue-400',     'border' => 'border-l-blue-400'],
            'selesai'     => ['label' => 'Selesai',     'class' => 'bg-emerald-100 text-emerald-700','dot' => 'bg-emerald-400',  'border' => 'border-l-emerald-400'],
            'tidak_valid' => ['label' => 'Tidak Valid', 'class' => 'bg-red-100 text-red-700',        'dot' => 'bg-red-400',      'border' => 'border-l-red-400'],
        ][$pengaduan->status] ?? ['label' => $pengaduan->status, 'class' => 'bg-stone-100 text-stone-600', 'dot' => 'bg-stone-400', 'border' => 'border-l-stone-300'];
        @endphp

        {{-- Result card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-stone-200 overflow-hidden">
            {{-- Status bar --}}
            <div class="px-5 py-4 border-b border-stone-100 flex items-center justify-between">
                <div>
                    <p class="text-xs text-stone-400 font-light">Kode Lacak</p>
                    <p class="font-mono font-bold text-stone-800 tracking-wider text-sm mt-0.5">{{ $pengaduan->tracking_code }}</p>
                </div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold {{ $statusCfg['class'] }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $statusCfg['dot'] }}"></span>
                    {{ $statusCfg['label'] }}
                </span>
            </div>

            {{-- Detail --}}
            <div class="px-5 py-4 space-y-3">
                <div class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-stone-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    <div>
                        <p class="text-xs text-stone-400">Kategori</p>
                        <p class="text-sm font-medium text-stone-800 capitalize mt-0.5">{{ $pengaduan->kategori }}</p>
                    </div>
                </div>

                <div class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-stone-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <div>
                        <p class="text-xs text-stone-400">Tanggal Lapor</p>
                        <p class="text-sm font-medium text-stone-800 mt-0.5">{{ $pengaduan->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>

                @if($pengaduan->lokasi)
                <div class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-stone-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <div>
                        <p class="text-xs text-stone-400">Lokasi</p>
                        <p class="text-sm font-medium text-stone-800 mt-0.5">{{ $pengaduan->lokasi }}</p>
                    </div>
                </div>
                @endif

                <div class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-stone-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <div>
                        <p class="text-xs text-stone-400">Isi Laporan</p>
                        <p class="text-sm text-stone-700 mt-0.5 leading-relaxed">{{ \Illuminate\Support\Str::limit($pengaduan->isi_laporan, 200) }}</p>
                    </div>
                </div>
            </div>

            {{-- Progress timeline --}}
            @if($pengaduan->klarifikasi->isNotEmpty())
            <div class="border-t border-stone-100 px-5 py-4">
                <p class="text-xs font-semibold text-stone-400 uppercase tracking-wider mb-4">Riwayat Penanganan</p>
                <div class="relative pl-5 space-y-4">
                    <div class="absolute left-[7px] top-1 bottom-1 w-px bg-stone-200"></div>
                    @foreach($pengaduan->klarifikasi as $k)
                    @php
                    $isSelesai  = $k->jenis === 'penutup';
                    $isAnonResp = $k->jenis === 'respons_anonim';
                    @endphp
                    <div class="relative">
                        <div class="absolute -left-5 top-1 w-3.5 h-3.5 rounded-full border-2 {{ $isSelesai ? 'bg-emerald-500 border-emerald-500' : ($isAnonResp ? 'bg-stone-500 border-stone-500' : 'bg-orange-500 border-orange-500') }}"></div>
                        <p class="text-[0.6875rem] text-stone-400 font-light mb-0.5">{{ $k->created_at->format('d M Y, H:i') }}</p>
                        @if($isAnonResp)
                        <div class="rounded-xl border border-stone-200 bg-stone-50 px-3 py-2.5 mt-1">
                            <p class="text-[0.625rem] font-semibold uppercase tracking-wide text-stone-400 mb-1">Respons Petugas</p>
                            <p class="text-sm text-stone-700 leading-relaxed">{{ $k->pesan }}</p>
                            @if($k->foto)
                            <img src="{{ Storage::url($k->foto) }}"
                                 class="mt-2 w-full rounded-lg object-cover max-h-48 border border-stone-200">
                            @endif
                        </div>
                        @else
                        <p class="text-sm text-stone-700 leading-relaxed">{{ $k->pesan }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="px-5 py-3 bg-stone-50 border-t border-stone-100">
                <p class="text-xs text-stone-400 font-light">Terakhir diperbarui: {{ $pengaduan->updated_at->diffForHumans() }}</p>
            </div>
        </div>
        @endif

        <p class="text-center text-xs text-stone-400 mt-6 font-light">M-Lapor &middot; Pengaduan Masyarakat</p>
    </div>
</body>
</html>
