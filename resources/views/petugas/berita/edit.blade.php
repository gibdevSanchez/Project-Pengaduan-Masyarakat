@extends('layouts.app')
@section('title', 'Edit Berita')
@section('content')
<div class="flex-1 overflow-y-auto p-6">
<div class="max-w-2xl space-y-5">

    <div class="flex items-center gap-3">
        <a href="{{ route('petugas.berita.index') }}" class="text-stone-400 hover:text-stone-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-xl font-bold text-stone-900">Edit Berita</h1>
    </div>

    @php
    $initMode = !$berita->is_published ? 'draft' : ($berita->mulai_tayang ? 'jadwalkan' : 'sekarang');
    @endphp

    <form method="POST" action="{{ route('petugas.berita.update', $berita->id) }}"
          class="space-y-5 rounded-xl bg-white p-6 border border-stone-200"
          enctype="multipart/form-data"
          x-data="{ mode: '{{ old('publikasi_mode', $initMode) }}', fotoPreview: null }">
        @csrf @method('PUT')

        <div>
            <label class="block text-sm font-medium text-stone-700 mb-1">Judul <span class="text-red-500">*</span></label>
            <input type="text" name="judul" value="{{ old('judul', $berita->judul) }}" required
                   class="w-full rounded-lg border border-stone-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-200">
        </div>

        <div>
            <label class="block text-sm font-medium text-stone-700 mb-1">Isi Berita <span class="text-red-500">*</span></label>
            <textarea name="isi" rows="6" required
                      class="w-full rounded-lg border border-stone-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-200">{{ old('isi', $berita->isi) }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-stone-700 mb-1">Foto <span class="text-stone-400 font-normal">(kosongkan untuk mempertahankan foto saat ini)</span></label>
            <div class="flex items-start gap-4">
                <div class="shrink-0">
                    <template x-if="fotoPreview">
                        <img :src="fotoPreview" class="w-24 h-24 rounded-lg object-cover border border-stone-200">
                    </template>
                    <template x-if="!fotoPreview">
                        @if($berita->foto)
                        <img src="{{ Storage::url($berita->foto) }}" alt="foto" class="w-24 h-24 rounded-lg object-cover border border-stone-200">
                        @else
                        <div class="w-24 h-24 rounded-lg bg-stone-100 flex items-center justify-center border border-stone-200">
                            <svg class="w-8 h-8 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        @endif
                    </template>
                </div>
                <div class="flex-1">
                    <input type="file" name="foto" accept="image/*"
                           class="w-full text-sm text-stone-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-orange-50 file:text-orange-600 hover:file:bg-orange-100"
                           @change="const f=$event.target.files[0]; if(f){const r=new FileReader();r.onload=e=>fotoPreview=e.target.result;r.readAsDataURL(f)}else{fotoPreview=null}">
                    @error('foto')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-stone-700 mb-1">Kategori</label>
                <select name="kategori" required class="w-full rounded-lg border border-stone-200 px-3 py-2 text-sm">
                    @foreach(['infrastruktur','lingkungan','keamanan','sosial','lainnya'] as $k)
                    <option value="{{ $k }}" {{ old('kategori', $berita->kategori) === $k ? 'selected' : '' }}>{{ ucfirst($k) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-stone-700 mb-1">Format</label>
                <select name="format" required class="w-full rounded-lg border border-stone-200 px-3 py-2 text-sm">
                    <option value="biasa" {{ old('format', $berita->format) === 'biasa' ? 'selected' : '' }}>Biasa</option>
                    <option value="besar" {{ old('format', $berita->format) === 'besar' ? 'selected' : '' }}>Besar (Headline)</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-stone-700 mb-1">Terkait Pengaduan Saya <span class="text-stone-400 font-normal">(opsional)</span></label>
            <select name="id_pengaduan" class="w-full rounded-lg border border-stone-200 px-3 py-2 text-sm">
                <option value="">— Tidak ada —</option>
                @foreach($pengaduanList as $p)
                <option value="{{ $p->id_pengaduan }}"
                        {{ old('id_pengaduan', $berita->id_pengaduan) == $p->id_pengaduan ? 'selected' : '' }}>
                    #{{ $p->id_pengaduan }} — {{ Str::limit($p->isi_laporan, 60) }}
                </option>
                @endforeach
            </select>
        </div>

        {{-- Publikasi --}}
        <div class="rounded-xl border border-stone-200 overflow-hidden">
            <div class="px-4 py-3 bg-stone-50 border-b border-stone-200">
                <p class="text-sm font-semibold text-stone-700">Status Publikasi</p>
            </div>
            <div class="p-4 space-y-2">
                <label class="flex items-start gap-3 cursor-pointer rounded-lg border p-3 transition-all"
                       :class="mode === 'draft' ? 'border-stone-300 bg-stone-50' : 'border-stone-100 hover:border-stone-200'">
                    <input type="radio" name="publikasi_mode" value="draft" x-model="mode" class="mt-0.5">
                    <div>
                        <p class="text-sm font-medium text-stone-800">Simpan sebagai Draft</p>
                        <p class="text-xs text-stone-400 mt-0.5">Tidak tampil ke publik</p>
                    </div>
                </label>
                <label class="flex items-start gap-3 cursor-pointer rounded-lg border p-3 transition-all"
                       :class="mode === 'sekarang' ? 'border-emerald-300 bg-emerald-50' : 'border-stone-100 hover:border-stone-200'">
                    <input type="radio" name="publikasi_mode" value="sekarang" x-model="mode" class="mt-0.5">
                    <div>
                        <p class="text-sm font-medium text-stone-800">Publikasikan Sekarang</p>
                        <p class="text-xs text-stone-400 mt-0.5">Langsung tampil ke publik</p>
                    </div>
                </label>
                <label class="flex items-start gap-3 cursor-pointer rounded-lg border p-3 transition-all"
                       :class="mode === 'jadwalkan' ? 'border-blue-300 bg-blue-50' : 'border-stone-100 hover:border-stone-200'">
                    <input type="radio" name="publikasi_mode" value="jadwalkan" x-model="mode" class="mt-0.5">
                    <div>
                        <p class="text-sm font-medium text-stone-800">Jadwalkan Publikasi</p>
                        <p class="text-xs text-stone-400 mt-0.5">Tampil otomatis pada tanggal yang ditentukan</p>
                    </div>
                </label>
            </div>

            <div class="px-4 pb-4 space-y-3" x-show="mode !== 'draft'" x-cloak>
                <div x-show="mode === 'jadwalkan'" x-cloak>
                    <label class="block text-sm font-medium text-stone-700 mb-1">Mulai Tayang <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="mulai_tayang"
                           value="{{ old('mulai_tayang', $berita->mulai_tayang?->format('Y-m-d\TH:i')) }}"
                           class="w-full rounded-lg border border-stone-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200"
                           :required="mode === 'jadwalkan'">
                    @error('mulai_tayang')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-stone-700 mb-1">Tanggal Berakhir <span class="text-stone-400 font-normal">(opsional)</span></label>
                    <input type="datetime-local" name="selesai_tayang"
                           value="{{ old('selesai_tayang', $berita->selesai_tayang?->format('Y-m-d\TH:i')) }}"
                           class="w-full rounded-lg border border-stone-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-200">
                    <p class="mt-1 text-xs text-stone-400">Berita otomatis disembunyikan setelah tanggal ini.</p>
                    @error('selesai_tayang')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="flex gap-3 pt-1">
            <button type="submit"
                    class="rounded-lg bg-orange-500 px-5 py-2 text-sm font-semibold text-white hover:bg-orange-600 transition-colors">
                Simpan Perubahan
            </button>
            <a href="{{ route('petugas.berita.index') }}"
               class="rounded-lg border border-stone-200 px-5 py-2 text-sm font-medium text-stone-600 hover:bg-stone-50 transition-colors">
                Batal
            </a>
        </div>
    </form>

</div>
</div>
@endsection
