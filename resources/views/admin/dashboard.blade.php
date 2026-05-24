@extends('layouts.app')

@section('title', 'Dashboard Admin')

@push('head-scripts')
<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
@endpush

@section('content')
@php use Illuminate\Support\Str; @endphp
<div class="flex-1 overflow-y-auto p-6 bg-stone-50">

    {{-- Page header --}}
    <div class="mb-6">
        <h1 class="text-xl font-bold text-stone-900 tracking-tight">Dashboard</h1>
        <p class="text-sm font-light text-stone-500 mt-0.5">Ringkasan data pengaduan masyarakat</p>
    </div>

    {{-- KPI stat cards --}}
    @php
    $cards = [
        ['label' => 'Total Pengaduan', 'val' => $stats['total'],    'sub' => 'Semua pengaduan masuk',
         'accent' => 'bg-orange-500',  'icon_bg' => 'bg-gradient-to-br from-orange-100 to-orange-50',
         'icon_stroke' => 'text-orange-600',
         'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        ['label' => 'Menunggu',        'val' => $stats['menunggu'], 'sub' => 'Belum ditangani',
         'accent' => 'bg-amber-500',   'icon_bg' => 'bg-gradient-to-br from-amber-100 to-amber-50',
         'icon_stroke' => 'text-amber-600',
         'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['label' => 'Diproses',        'val' => $stats['proses'],   'sub' => 'Sedang ditindaklanjuti',
         'accent' => 'bg-blue-500',    'icon_bg' => 'bg-gradient-to-br from-blue-100 to-blue-50',
         'icon_stroke' => 'text-blue-600',
         'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'],
        ['label' => 'Selesai',         'val' => $stats['selesai'],  'sub' => 'Telah diselesaikan',
         'accent' => 'bg-emerald-500', 'icon_bg' => 'bg-gradient-to-br from-emerald-100 to-emerald-50',
         'icon_stroke' => 'text-emerald-600',
         'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
    ];
    @endphp

    <div class="grid grid-cols-4 gap-4 mb-6">
        @foreach($cards as $c)
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-stone-100 overflow-hidden relative">
            <div class="absolute left-0 top-0 bottom-0 w-1 {{ $c['accent'] }} rounded-l-2xl"></div>
            <div class="flex items-start justify-between mb-3.5 pl-2">
                <p class="text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-widest leading-snug">{{ $c['label'] }}</p>
                <div class="w-10 h-10 rounded-xl {{ $c['icon_bg'] }} flex items-center justify-center shrink-0 ml-2">
                    <svg class="w-5 h-5 {{ $c['icon_stroke'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $c['icon'] }}"/>
                    </svg>
                </div>
            </div>
            <p class="stat-number font-black text-[2.5rem] text-stone-900 leading-none tracking-tight pl-2" data-count="{{ $c['val'] }}">0</p>
            <p class="text-xs font-light text-stone-400 mt-1.5 pl-2">{{ $c['sub'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Chart + Recent --}}
    <div class="grid grid-cols-[5fr_7fr] gap-4">

        {{-- Doughnut chart --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-stone-100">
            <h3 class="font-semibold text-[0.9375rem] text-stone-900 mb-4">Distribusi Status</h3>
            <div class="h-[220px] relative">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        {{-- Recent list --}}
        <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-stone-100">
            <div class="px-5 py-4 border-b border-stone-100 flex items-center justify-between">
                <h3 class="font-semibold text-[0.9375rem] text-stone-900">Pengaduan Terbaru</h3>
                <span class="text-xs font-light text-stone-400">10 terakhir</span>
            </div>

            @if($recentPengaduan->isEmpty())
                <div class="p-12 text-center">
                    <svg class="w-10 h-10 text-stone-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="text-stone-400 text-sm font-light">Belum ada pengaduan masuk.</p>
                </div>
            @else
                <div class="overflow-y-auto max-h-[270px] divide-y divide-stone-100">
                    @foreach($recentPengaduan as $p)
                    @php
                    $statusCfg = [
                        'menunggu'    => ['pill'=>'bg-amber-100 text-amber-700',   'dot'=>'bg-amber-400'],
                        'proses'      => ['pill'=>'bg-blue-100 text-blue-700',     'dot'=>'bg-blue-400'],
                        'selesai'     => ['pill'=>'bg-emerald-100 text-emerald-700','dot'=>'bg-emerald-400'],
                        'tidak_valid' => ['pill'=>'bg-red-100 text-red-700',       'dot'=>'bg-red-400'],
                    ][$p->status] ?? ['pill'=>'bg-stone-100 text-stone-600','dot'=>'bg-stone-400'];
                    @endphp
                    <div class="flex items-center gap-3.5 px-5 py-3.5 hover:bg-stone-50 transition-colors">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white font-bold text-[0.8125rem] shrink-0">
                            {{ strtoupper(substr($p->masyarakat->nama ?? 'A', 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-stone-900 truncate">{{ $p->masyarakat->nama ?? 'Anonim' }}</p>
                            <p class="text-xs font-light text-stone-400 truncate">{{ Str::limit($p->isi_laporan, 55) }}</p>
                        </div>
                        <div class="shrink-0 text-right">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.6875rem] font-medium {{ $statusCfg['pill'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $statusCfg['dot'] }}"></span>
                                {{ ucfirst($p->status === 'tidak_valid' ? 'Tidak Valid' : $p->status) }}
                            </span>
                            <p class="text-[0.6875rem] font-light text-stone-400 mt-1">{{ $p->tgl_pengaduan }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.stat-number').forEach(el => {
    const target = parseInt(el.dataset.count);
    const obj = { val: 0 };
    gsap.to(obj, {
        val: target,
        duration: 1,
        ease: 'power2.out',
        onUpdate() { el.textContent = Math.round(obj.val); }
    });
});

new Chart(document.getElementById('statusChart').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: ['Menunggu', 'Diproses', 'Selesai'],
        datasets: [{
            data: [{{ $stats['menunggu'] }}, {{ $stats['proses'] }}, {{ $stats['selesai'] }}],
            backgroundColor: ['#F59E0B', '#3B82F6', '#10B981'],
            borderWidth: 0,
            hoverOffset: 6,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '68%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: { font: { family: 'Inter', size: 12, weight: '400' }, padding: 16, usePointStyle: true, pointStyleWidth: 8 }
            }
        }
    }
});
</script>
@endpush
