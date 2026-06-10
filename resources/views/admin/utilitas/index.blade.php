@extends('layouts.app')

@section('title', 'Utilitas')

@section('content')
<div class="flex-1 overflow-y-auto p-6 bg-stone-50">

    <div>
        <h1 class="text-xl font-bold text-stone-900 tracking-tight">Utilitas</h1>
        <p class="text-sm font-light text-stone-500 mt-0.5">Pengaturan sistem dan konfigurasi operasional.</p>
    </div>

    @if(session('success'))
    <div class="mt-4 rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700 ring-1 ring-emerald-200">
        {{ session('success') }}
    </div>
    @endif

    <form method="POST" action="{{ route('admin.utilitas.save') }}" class="mt-6 space-y-6">
        @csrf

        {{-- Closing Template Card --}}
        <div class="rounded-2xl bg-white p-6 shadow-sm border border-stone-100">
            <h2 class="text-sm font-semibold text-stone-800">Template Penutup Pengaduan</h2>
            <p class="mt-1 text-xs text-stone-400 font-light">
                Teks ini secara otomatis dikirim ke thread klarifikasi saat petugas menyelesaikan pengaduan.
                Petugas dapat menambahkan pesan personal di bawahnya.
            </p>

            <div class="mt-5">
                <textarea name="closing_template" rows="6"
                          class="w-full rounded-xl border border-stone-200 px-3 py-2.5 text-sm text-stone-700
                                 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-100
                                 @error('closing_template') border-red-400 @enderror"
                          placeholder="Masukkan template pesan penutup...">{{ old('closing_template', $closingTemplate) }}</textarea>

                @error('closing_template')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- SLA Settings Card --}}
        <div class="rounded-2xl bg-white p-6 shadow-sm border border-stone-100">
            <h2 class="text-sm font-semibold text-stone-800">Batas Waktu SLA per Kategori</h2>
            <p class="mt-1 text-xs text-stone-400 font-light">
                Pengaduan yang belum selesai melebihi batas jam ini akan ditandai sebagai "Lewat SLA" pada dashboard dan daftar pengaduan.
            </p>

            <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach([
                    'keamanan'      => ['label' => 'Keamanan',      'color' => 'text-red-600',    'bg' => 'bg-red-50',    'border' => 'border-red-200'],
                    'infrastruktur' => ['label' => 'Infrastruktur', 'color' => 'text-blue-600',   'bg' => 'bg-blue-50',   'border' => 'border-blue-200'],
                    'lingkungan'    => ['label' => 'Lingkungan',    'color' => 'text-emerald-600','bg' => 'bg-emerald-50','border' => 'border-emerald-200'],
                    'sosial'        => ['label' => 'Sosial',        'color' => 'text-purple-600', 'bg' => 'bg-purple-50', 'border' => 'border-purple-200'],
                    'lainnya'       => ['label' => 'Lainnya',       'color' => 'text-stone-600',  'bg' => 'bg-stone-50',  'border' => 'border-stone-200'],
                ] as $key => $cfg)
                <div class="rounded-xl {{ $cfg['bg'] }} border {{ $cfg['border'] }} p-4">
                    <label class="block text-xs font-semibold {{ $cfg['color'] }} mb-2">{{ $cfg['label'] }}</label>
                    <div class="flex items-center gap-2">
                        <input type="number" name="sla_{{ $key }}" value="{{ old('sla_' . $key, $slaHours[$key]) }}"
                               min="1" max="720" required
                               class="w-20 px-3 py-2 rounded-lg border border-stone-200 bg-white text-sm text-stone-800 outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100 font-sans text-center font-semibold">
                        <span class="text-xs text-stone-500 font-light">jam</span>
                    </div>
                    @error('sla_' . $key)
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                @endforeach
            </div>
        </div>

        <button type="submit"
                class="inline-flex items-center gap-2 rounded-xl bg-orange-500 px-5 py-2.5 text-sm font-semibold text-white
                       hover:bg-orange-600 transition-colors focus:outline-none focus:ring-2 focus:ring-orange-400">
            Simpan Pengaturan
        </button>
    </form>

</div>
@endsection
