@extends('layouts.masyarakat')

@section('title', 'Buat Pengaduan')

@section('header')
<div class="px-4 py-3 bg-white border-b border-stone-200 shrink-0 flex items-center gap-3">
    <a href="{{ route('masyarakat.dashboard') }}"
       class="flex items-center justify-center w-8 h-8 rounded-lg border border-stone-200 text-stone-500 no-underline hover:border-orange-400 hover:text-orange-500 transition-colors shrink-0">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>
    <div>
        <p class="font-bold text-[1.0625rem] text-stone-900">Buat Pengaduan</p>
        <p class="text-[0.6875rem] font-light text-stone-500">Sampaikan keluhan Anda</p>
    </div>
</div>
@endsection

@section('content')
<div class="p-4"
     x-data="{
         charCount: 0,
         anonim: false,
         photoPreview: null,
         removePhoto() { this.photoPreview = null; document.getElementById('foto-input').value = ''; },
         handleFile(e) {
             const file = e.target.files[0];
             if (!file) return;
             const reader = new FileReader();
             reader.onload = ev => this.photoPreview = ev.target.result;
             reader.readAsDataURL(file);
         }
     }">

    @if($errors->any())
        <div class="mb-4 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm">
            @foreach($errors->all() as $e)
                <div>• {{ $e }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('masyarakat.pengaduan.store') }}" enctype="multipart/form-data">
        @csrf

        {{-- Kategori --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-stone-900 mb-1.5">
                Kategori <span class="text-red-500">*</span>
            </label>
            <select name="kategori" required
                    class="w-full px-4 py-3 rounded-xl border border-stone-200 bg-stone-50 text-stone-900 text-sm outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-400/20 font-sans">
                <option value="" disabled {{ old('kategori') ? '' : 'selected' }}>Pilih kategori...</option>
                @foreach([
                    'infrastruktur' => 'Infrastruktur',
                    'lingkungan'    => 'Lingkungan',
                    'keamanan'      => 'Keamanan',
                    'sosial'        => 'Sosial',
                    'lainnya'       => 'Lainnya',
                ] as $val => $lbl)
                <option value="{{ $val }}" {{ old('kategori') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                @endforeach
            </select>
        </div>

        {{-- Lokasi --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-stone-900 mb-1.5">
                Lokasi <span class="font-light text-stone-500">(opsional)</span>
            </label>
            <input type="text" name="lokasi" value="{{ old('lokasi') }}"
                   placeholder="Contoh: Jl. Sudirman No. 5, RT 03"
                   class="w-full px-4 py-3 rounded-xl border border-stone-200 bg-stone-50 text-stone-900 text-sm outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-400/20 font-sans">
        </div>

        {{-- Isi laporan --}}
        <div class="mb-4">
            <div class="flex justify-between items-baseline mb-1.5">
                <label class="text-sm font-medium text-stone-900">
                    Isi Pengaduan <span class="text-red-500">*</span>
                </label>
                <span class="text-[0.6875rem] font-light text-stone-400" x-text="charCount + ' karakter'"></span>
            </div>
            <textarea name="isi_laporan" rows="6" required minlength="10"
                      placeholder="Jelaskan pengaduan Anda: lokasi, waktu, dan permasalahan yang terjadi..."
                      class="w-full px-4 py-3.5 rounded-xl border border-stone-200 bg-white text-sm text-stone-900 font-sans resize-none outline-none leading-relaxed transition-[border-color,box-shadow] focus:border-orange-400 focus:ring-2 focus:ring-orange-400/20"
                      @input="charCount = $el.value.length">{{ old('isi_laporan') }}</textarea>
            <p class="text-[0.6875rem] font-light text-stone-400 mt-1">Minimal 10 karakter.</p>
        </div>

        {{-- Foto --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-stone-900 mb-1.5">
                Foto Bukti <span class="font-light text-stone-500">(opsional)</span>
            </label>

            <div x-show="!photoPreview"
                 @click="document.getElementById('foto-input').click()"
                 class="border-2 border-dashed border-stone-200 rounded-xl p-6 text-center cursor-pointer hover:border-orange-400 hover:bg-orange-50 transition-colors">
                <svg class="w-7 h-7 mx-auto mb-2 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-sm font-medium text-stone-900">Ketuk untuk pilih foto</p>
                <p class="text-xs font-light text-stone-400 mt-0.5">PNG, JPG hingga 2 MB</p>
            </div>

            <div x-show="photoPreview" x-cloak class="relative">
                <img :src="photoPreview" class="w-full max-h-40 object-cover rounded-xl">
                <button type="button" @click="removePhoto()"
                        class="absolute top-2 right-2 w-7 h-7 rounded-full bg-black/60 text-white border-0 cursor-pointer flex items-center justify-center text-base leading-none">
                    &times;
                </button>
            </div>

            <input type="file" id="foto-input" name="foto" accept="image/*" class="hidden" @change="handleFile($event)">
        </div>

        {{-- Anonim toggle --}}
        <div class="mb-6 bg-white rounded-xl p-4 flex items-center gap-3.5 cursor-pointer" @click="anonim = !anonim">
            <div :class="anonim ? 'bg-orange-500' : 'bg-stone-300'"
                 class="w-11 h-6 rounded-full relative transition-colors duration-200 shrink-0">
                <div :class="anonim ? 'translate-x-5' : 'translate-x-0.5'"
                     class="absolute top-0.5 w-5 h-5 rounded-full bg-white transition-transform duration-200 shadow-sm"></div>
            </div>
            <input type="checkbox" name="anonim" value="1" :checked="anonim" class="hidden">
            <div>
                <p class="text-sm font-medium text-stone-900">Kirim sebagai anonim</p>
                <p x-show="anonim" x-cloak class="text-xs font-light text-stone-500 mt-0.5">Identitas NIK Anda tidak akan ditampilkan</p>
                <p x-show="!anonim" class="text-xs font-light text-stone-500 mt-0.5">Identitas Anda akan dicantumkan</p>
            </div>
        </div>

        <button type="submit"
                class="w-full flex items-center justify-center gap-2 py-4 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 text-white text-base font-bold border-0 cursor-pointer font-sans shadow-[0_4px_12px_rgba(234,88,12,0.4)]">
            <svg class="w-[1.125rem] h-[1.125rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
            </svg>
            Kirim Pengaduan
        </button>
    </form>
</div>
@endsection
