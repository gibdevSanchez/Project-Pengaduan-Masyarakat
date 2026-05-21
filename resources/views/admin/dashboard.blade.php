@extends('layouts.app')

@section('title', 'Dashboard Admin')

@push('head-scripts')
<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
@endpush

@section('content')
@php use Illuminate\Support\Str; @endphp
<div class="flex-1 overflow-y-auto p-6">

    {{-- Page header --}}
    <div class="mb-6">
        <h1 class="text-xl font-bold text-stone-900">Dashboard</h1>
        <p class="text-sm font-light text-stone-500 mt-0.5">Ringkasan data pengaduan masyarakat</p>
    </div>

    {{-- Stat cards --}}
    @php
    $cards = [
        ['label' => 'Total Pengaduan', 'val' => $stats['total'],    'sub' => 'Semua pengaduan masuk',  'top' => 'border-t-orange-500',  'icon_stroke' => 'text-orange-500', 'icon_bg' => 'bg-orange-50',  'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        ['label' => 'Menunggu',        'val' => $stats['menunggu'], 'sub' => 'Belum ditangani',        'top' => 'border-t-amber-500',   'icon_stroke' => 'text-amber-500',  'icon_bg' => 'bg-amber-50',   'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['label' => 'Diproses',        'val' => $stats['proses'],   'sub' => 'Sedang ditindaklanjuti', 'top' => 'border-t-blue-500',    'icon_stroke' => 'text-blue-500',   'icon_bg' => 'bg-blue-50',    'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'],
        ['label' => 'Selesai',         'val' => $stats['selesai'],  'sub' => 'Telah diselesaikan',     'top' => 'border-t-emerald-500', 'icon_stroke' => 'text-emerald-500','icon_bg' => 'bg-emerald-50', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
    ];
    @endphp

    <div class="grid grid-cols-4 gap-4 mb-6">
        @foreach($cards as $c)
        <div class="bg-white rounded-xl p-5 border-t-[3px] {{ $c['top'] }} shadow-sm">
            <div class="flex items-center justify-between mb-3.5">
                <p class="text-xs font-medium text-stone-500 uppercase tracking-wide">{{ $c['label'] }}</p>
                <div class="w-8 h-8 rounded-lg {{ $c['icon_bg'] }} flex items-center justify-center">
                    <svg class="w-4 h-4 {{ $c['icon_stroke'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $c['icon'] }}"/>
                    </svg>
                </div>
            </div>
            <p class="stat-number font-black text-4xl text-stone-900 leading-none" data-count="{{ $c['val'] }}">0</p>
            <p class="text-xs font-light text-stone-500 mt-1">{{ $c['sub'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Chart + Recent --}}
    <div class="grid grid-cols-[2fr_3fr] gap-4">

        {{-- Doughnut chart --}}
        <div class="bg-white rounded-xl p-5 shadow-sm">
            <h3 class="font-semibold text-[0.9375rem] text-stone-900 mb-4">Distribusi Status</h3>
            <div class="h-[220px] relative">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        {{-- Recent list --}}
        <div class="bg-white rounded-xl overflow-hidden shadow-sm">
            <div class="px-5 py-4 border-b border-stone-200 flex items-center justify-between">
                <h3 class="font-semibold text-[0.9375rem] text-stone-900">Pengaduan Terbaru</h3>
                <span class="text-xs font-light text-stone-400">10 terakhir</span>
            </div>

            @if($recentPengaduan->isEmpty())
                <div class="p-12 text-center text-stone-400 text-sm font-light">
                    Belum ada pengaduan masuk.
                </div>
            @else
                <div class="overflow-y-auto max-h-[260px]">
                    @foreach($recentPengaduan as $p)
                    <div class="flex items-center gap-3.5 px-5 py-3.5 border-b border-stone-100 hover:bg-orange-50 transition-colors">
                        <div class="w-9 h-9 rounded-full bg-orange-500 flex items-center justify-center text-white font-bold text-[0.8125rem] shrink-0">
                            {{ strtoupper(substr($p->masyarakat->nama ?? 'A', 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-stone-900 truncate">{{ $p->masyarakat->nama ?? 'Anonim' }}</p>
                            <p class="text-xs font-light text-stone-500 truncate">{{ Str::limit($p->isi_laporan, 55) }}</p>
                        </div>
                        <div class="shrink-0 text-right">
                            <x-status-badge :status="$p->status"/>
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
                labels: { font: { family: 'Inter', size: 12, weight: '300' }, padding: 16, usePointStyle: true }
            }
        }
    }
});
</script>
@endpush
