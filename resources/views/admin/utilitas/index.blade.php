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

    {{-- Closing Template Card --}}
    <div class="mt-6 rounded-2xl bg-white p-6 shadow-sm border border-stone-100">
        <h2 class="text-sm font-semibold text-stone-800">Template Penutup Pengaduan</h2>
        <p class="mt-1 text-xs text-stone-400 font-light">
            Teks ini secara otomatis dikirim ke thread klarifikasi saat petugas menyelesaikan pengaduan.
            Petugas dapat menambahkan pesan personal di bawahnya.
        </p>

        <form method="POST" action="{{ route('admin.utilitas.save') }}" class="mt-5 space-y-4">
            @csrf

            <div>
                <textarea name="closing_template" rows="6"
                          class="w-full rounded-xl border border-stone-200 px-3 py-2.5 text-sm text-stone-700
                                 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-100
                                 @error('closing_template') border-red-400 @enderror"
                          placeholder="Masukkan template pesan penutup...">{{ old('closing_template', $closingTemplate) }}</textarea>

                @error('closing_template')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-orange-500 px-4 py-2.5 text-sm font-semibold text-white
                           hover:bg-orange-600 transition-colors focus:outline-none focus:ring-2 focus:ring-orange-400">
                Simpan Template
            </button>
        </form>
    </div>

</div>
@endsection
