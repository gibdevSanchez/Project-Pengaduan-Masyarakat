@props(['status'])
@php
$cfg = match($status) {
    'menunggu'    => ['label' => 'Menunggu',    'classes' => 'bg-amber-100 text-amber-800',    'dot' => 'bg-amber-500'],
    'proses'      => ['label' => 'Diproses',    'classes' => 'bg-blue-100 text-blue-800',      'dot' => 'bg-blue-500'],
    'selesai'     => ['label' => 'Selesai',     'classes' => 'bg-emerald-100 text-emerald-800','dot' => 'bg-emerald-500'],
    'tidak_valid' => ['label' => 'Tidak Valid', 'classes' => 'bg-red-100 text-red-800',        'dot' => 'bg-red-500'],
    default       => ['label' => ucfirst($status), 'classes' => 'bg-stone-100 text-stone-700', 'dot' => 'bg-stone-400'],
};
@endphp
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $cfg['classes'] }}">
    <span class="w-1.5 h-1.5 rounded-full {{ $cfg['dot'] }}"></span>
    {{ $cfg['label'] }}
</span>
