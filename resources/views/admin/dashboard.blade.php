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

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total Pengaduan</p>
            <p class="mt-1 text-3xl font-bold text-gray-900">{{ $stats['total'] }}</p>
        </div>

        <div class="rounded-xl bg-yellow-50 p-5 shadow-sm ring-1 ring-yellow-100">
            <p class="text-xs font-medium uppercase tracking-wide text-yellow-700">Menunggu</p>
            <p class="mt-1 text-3xl font-bold text-yellow-900">{{ $stats['menunggu'] }}</p>
            <p class="mt-1 text-xs text-yellow-600">+{{ $stats['menungguToday'] }} hari ini</p>
        </div>

        <div class="rounded-xl bg-blue-50 p-5 shadow-sm ring-1 ring-blue-100">
            <p class="text-xs font-medium uppercase tracking-wide text-blue-700">Diproses</p>
            <p class="mt-1 text-3xl font-bold text-blue-900">{{ $stats['proses'] }}</p>
            <p class="mt-1 text-xs text-blue-600">{{ $stats['petugasAktif'] }} petugas aktif</p>
        </div>

        <div class="rounded-xl bg-green-50 p-5 shadow-sm ring-1 ring-green-100">
            <p class="text-xs font-medium uppercase tracking-wide text-green-700">Selesai</p>
            <p class="mt-1 text-3xl font-bold text-green-900">{{ $stats['selesai'] }}</p>
            <p class="mt-1 text-xs text-green-600">{{ $stats['completionRate'] }}% selesai (7 hari)</p>
        </div>
    </div>

    {{-- Weekly Bar Chart --}}
    <div class="mt-6 rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100"
         x-data="weeklyChart({!! $weeklyData->toJson() !!})">
        <h3 class="mb-4 text-sm font-semibold text-gray-700">Pengaduan Masuk per Minggu (8 minggu terakhir)</h3>
        <div class="flex items-end gap-2" style="height:8rem;">
            <template x-for="(bar, i) in bars" :key="i">
                <div class="flex flex-1 flex-col items-center gap-1">
                    <span x-text="bar.count" class="text-xs text-gray-500"></span>
                    <div class="w-full rounded-t bg-blue-400 transition-all"
                         :style="`height:${bar.height}%`"></div>
                    <span x-text="bar.label"
                          class="truncate text-center text-xs text-gray-400 w-full"></span>
                </div>
            </template>
        </div>
    </div>

    {{-- Online Petugas + Chart + Recent --}}
    <div class="mt-6 rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-gray-700">Petugas Aktif Sekarang</h3>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                {{ $onlinePetugas->count() }} online
            </span>
        </div>
        @if($onlinePetugas->isEmpty())
        <p class="text-sm text-stone-400 font-light italic">Tidak ada petugas yang aktif saat ini.</p>
        @else
        <div class="flex flex-wrap gap-3">
            @foreach($onlinePetugas as $op)
            <div class="flex items-center gap-2.5 px-3 py-2 rounded-xl bg-stone-50 border border-stone-100">
                @if($op->foto_profil)
                <img src="{{ Storage::url($op->foto_profil) }}" alt="{{ $op->nama_petugas }}"
                     class="w-8 h-8 rounded-full object-cover shrink-0 ring-2 ring-emerald-200">
                @else
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white text-xs font-bold shrink-0 ring-2 ring-emerald-200">
                    {{ strtoupper(substr($op->nama_petugas, 0, 1)) }}
                </div>
                @endif
                <div>
                    <p class="text-sm font-semibold text-stone-900 leading-snug">{{ $op->nama_petugas }}</p>
                    <p class="text-[0.6875rem] text-stone-400 font-light">{{ $op->last_seen_at->diffForHumans() }}</p>
                </div>
                <span class="w-2 h-2 rounded-full bg-emerald-400 shrink-0 ml-1"></span>
            </div>
            @endforeach
        </div>
        @endif
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
                            <p class="text-[0.6875rem] font-light text-stone-400 mt-1">{{ $p->created_at->format('d M Y') }}</p>
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
function weeklyChart(data) {
    const entries = Object.entries(data);
    const max = Math.max(...entries.map(([, v]) => v), 1);
    return {
        bars: entries.map(([week, count]) => ({
            label: 'W' + week.split('-')[1],
            count,
            height: Math.round((count / max) * 100),
        })),
    };
}

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
