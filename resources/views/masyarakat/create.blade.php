@extends('layouts.masyarakat')

@section('title', 'Buat Pengaduan')

@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush

@section('header')
<div class="px-4 py-3.5 bg-white border-b border-stone-200 shrink-0 flex items-center gap-3">
    <a href="{{ route('masyarakat.dashboard') }}"
       class="flex items-center justify-center w-8 h-8 rounded-xl border border-stone-200 text-stone-500 no-underline hover:border-orange-300 hover:text-orange-500 transition-colors shrink-0">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>
    <div>
        <p class="font-bold text-[1.0625rem] text-stone-900 tracking-tight">Buat Pengaduan</p>
        <p class="text-[0.6875rem] font-light text-stone-500">Sampaikan keluhan Anda kepada kami</p>
    </div>
</div>
@endsection

@section('content')

<script>
// Leaflet instances live outside Alpine to avoid Proxy interference
var _map = null, _marker = null;

// Reverse geocode then update Alpine reactive data directly via the passed proxy
async function _doGeocode(alpine, lat, lng) {
    alpine.geocoding = true;
    try {
        var r = await fetch(
            'https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat +
            '&lon=' + lng + '&accept-language=id'
        );
        var d = await r.json();
        alpine.lokasi = d.display_name || (lat.toFixed(5) + ', ' + lng.toFixed(5));
    } catch (e) {
        alpine.lokasi = lat.toFixed(5) + ', ' + lng.toFixed(5);
    } finally {
        alpine.geocoding = false;
    }
}

// Called from x-init with the Alpine $data proxy passed explicitly
function _initMap(alpine) {
    _map = L.map('map-create').setView([-6.2, 106.8], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(_map);

    _map.on('click', async function (e) {
        var lat = e.latlng.lat, lng = e.latlng.lng;
        alpine.lat      = lat.toFixed(7);
        alpine.lng      = lng.toFixed(7);
        alpine.geoError = '';
        if (_marker) {
            _marker.setLatLng([lat, lng]);
        } else {
            _marker = L.marker([lat, lng]).addTo(_map);
        }
        await _doGeocode(alpine, lat, lng);
    });

    // Restore pin if validation failed and old values exist
    if (alpine.lat && alpine.lng) {
        var pos = [parseFloat(alpine.lat), parseFloat(alpine.lng)];
        _marker = L.marker(pos).addTo(_map);
        _map.setView(pos, 16);
    }
}
</script>

<div class="p-4"
     x-data="{
         charCount: 0,
         anonim: false,
         photoPreviews: [],
         photoError: '',
         lokasi: '{{ old('lokasi', '') }}',
         lat: '{{ old('lat', '') }}',
         lng: '{{ old('lng', '') }}',
         geocoding: false,
         geolocating: false,
         geoError: '',
         removePhoto(idx) {
             this.photoPreviews.splice(idx, 1);
             if (this.photoPreviews.length === 0) document.getElementById('foto-input').value = '';
         },
         handleFiles(e) {
             this.photoError = '';
             var maxBytes = 8 * 1024 * 1024;
             var tooBig = Array.from(e.target.files).find(function(f){ return f.size > maxBytes; });
             if (tooBig) {
                 this.photoError = 'File terlalu besar. Maksimal ukuran 8 MB per foto.';
                 e.target.value = '';
                 return;
             }
             var self = this;
             Array.from(e.target.files).forEach(function(file) {
                 var reader = new FileReader();
                 reader.onload = function(ev) { self.photoPreviews.push(ev.target.result); };
                 reader.readAsDataURL(file);
             });
         },
         locateMe() {
             if (!navigator.geolocation) {
                 this.geoError = 'Browser tidak mendukung geolokasi.';
                 return;
             }
             this.geolocating = true;
             this.geoError = '';
             var self = this;
             navigator.geolocation.getCurrentPosition(
                 async function(pos) {
                     var lat = pos.coords.latitude, lng = pos.coords.longitude;
                     self.lat = lat.toFixed(7);
                     self.lng = lng.toFixed(7);
                     if (_marker) {
                         _marker.setLatLng([lat, lng]);
                     } else {
                         _marker = L.marker([lat, lng]).addTo(_map);
                     }
                     _map.setView([lat, lng], 16);
                     self.geolocating = false;
                     await _doGeocode(self, lat, lng);
                 },
                 function() {
                     self.geoError = 'Akses lokasi ditolak. Ketuk peta secara manual.';
                     self.geolocating = false;
                 },
                 { enableHighAccuracy: true, timeout: 10000 }
             );
         }
     }"
     x-init="$nextTick(() => _initMap($data))">

    @if($errors->any())
    <div class="mb-4 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm">
        @foreach($errors->all() as $e)
        <div class="flex items-start gap-1.5">
            <svg class="w-3.5 h-3.5 shrink-0 mt-0.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ $e }}
        </div>
        @endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('masyarakat.pengaduan.store') }}" enctype="multipart/form-data">
        @csrf

        {{-- Kategori --}}
        <div class="mb-4">
            <label class="block text-sm font-semibold text-stone-700 mb-1.5">
                Kategori <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <select name="kategori" required
                        class="w-full appearance-none px-4 py-3 pr-10 rounded-xl border border-stone-200 bg-stone-50 text-stone-900 text-sm outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-400/20 font-sans cursor-pointer">
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
                <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-stone-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </div>
            @error('kategori')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Lokasi (Map Pin) --}}
        <div class="mb-4">
            <label class="block text-sm font-semibold text-stone-700 mb-1.5">
                Lokasi <span class="text-xs font-normal text-stone-400">(opsional)</span>
            </label>

            {{-- Map container with GPS button overlay --}}
            <div class="relative mb-2">
                <div id="map-create" class="w-full rounded-xl border border-stone-200" style="height:196px;"></div>

                {{-- GPS button overlaid on map --}}
                <button type="button" @click="locateMe()"
                        :disabled="geolocating"
                        class="absolute top-2 right-2 z-[1000] flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-white border border-stone-200 shadow-md text-xs font-semibold text-stone-700 cursor-pointer hover:bg-stone-50 transition-colors disabled:opacity-60 disabled:cursor-wait">
                    <svg x-show="!geolocating" class="w-3.5 h-3.5 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm8.94 3A8.994 8.994 0 0013 3.06V1h-2v2.06A8.994 8.994 0 003.06 11H1v2h2.06A8.994 8.994 0 0011 20.94V23h2v-2.06A8.994 8.994 0 0020.94 13H23v-2h-2.06z"/>
                    </svg>
                    <svg x-show="geolocating" x-cloak class="w-3.5 h-3.5 text-orange-500 shrink-0 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span x-text="geolocating ? 'Mencari...' : 'Lokasi Saya'"></span>
                </button>
            </div>

            <p class="text-[0.6875rem] font-light text-stone-400 mb-2">
                Ketuk peta untuk pin lokasi, atau gunakan tombol <strong>Lokasi Saya</strong> untuk mendeteksi otomatis.
            </p>

            {{-- GPS / geocode error --}}
            <p class="text-[0.6875rem] font-light text-red-500 mb-2"
               x-show="geoError" x-cloak x-text="geoError"></p>

            {{-- Address input (auto-filled from Nominatim, manually editable) --}}
            <div class="relative">
                <div class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none">
                    <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <input type="text" name="lokasi" x-model="lokasi"
                       :placeholder="geocoding ? 'Mendapatkan alamat...' : 'Ketuk peta atau isi alamat manual...'"
                       class="w-full pl-10 pr-4 py-3 rounded-xl border border-stone-200 bg-stone-50 text-stone-900 text-sm outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-400/20 font-sans">
            </div>
            <input type="hidden" name="lat" x-model="lat">
            <input type="hidden" name="lng" x-model="lng">

            <p class="text-[0.6875rem] font-light text-emerald-600 mt-1 flex items-center gap-1" x-show="lat && lng" x-cloak>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Pin lokasi ditentukan.
            </p>
        </div>

        {{-- Isi laporan --}}
        <div class="mb-4">
            <div class="flex justify-between items-baseline mb-1.5">
                <label class="text-sm font-semibold text-stone-700">
                    Isi Pengaduan <span class="text-red-500">*</span>
                </label>
                <span class="text-[0.6875rem] font-light"
                      :class="charCount < 10 ? 'text-red-400' : 'text-stone-400'"
                      x-text="charCount + ' karakter'"></span>
            </div>
            <textarea name="isi_laporan" rows="6" required minlength="10"
                      placeholder="Jelaskan pengaduan Anda secara detail: lokasi kejadian, waktu, dan permasalahan yang terjadi..."
                      class="w-full px-4 py-3.5 rounded-xl border border-stone-200 bg-white text-sm text-stone-900 font-sans resize-none outline-none leading-relaxed transition-[border-color,box-shadow] focus:border-orange-400 focus:ring-2 focus:ring-orange-400/20"
                      @input="charCount = $el.value.length">{{ old('isi_laporan') }}</textarea>
            <p class="text-[0.6875rem] font-light text-stone-400 mt-1">Minimal 10 karakter.</p>
            @error('isi_laporan')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Foto --}}
        <div class="mb-4">
            <label class="block text-sm font-semibold text-stone-700 mb-1.5">
                Foto Bukti <span class="text-xs font-normal text-stone-400">(opsional)</span>
            </label>

            <div x-show="photoPreviews.length === 0"
                 @click="document.getElementById('foto-input').click()"
                 class="group border-2 border-dashed border-stone-200 rounded-xl p-6 text-center cursor-pointer hover:border-orange-400 hover:bg-orange-50/50 transition-all">
                <div class="w-12 h-12 rounded-2xl bg-stone-100 group-hover:bg-orange-100 flex items-center justify-center mx-auto mb-3 transition-colors">
                    <svg class="w-6 h-6 text-stone-400 group-hover:text-orange-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-stone-700 group-hover:text-stone-900 transition-colors">Ketuk untuk pilih foto</p>
                <p class="text-xs font-light text-stone-400 mt-0.5">PNG, JPG · Maks. 5 foto · Maks. 8 MB per foto</p>
            </div>

            <div x-show="photoPreviews.length > 0" x-cloak class="flex flex-col gap-2">
                <template x-for="(src, idx) in photoPreviews" :key="idx">
                    <div class="relative">
                        <img :src="src" class="w-full max-h-48 object-cover rounded-xl border border-stone-100">
                        <button type="button" @click="removePhoto(idx)"
                                class="absolute top-2 right-2 w-8 h-8 rounded-full bg-black/60 hover:bg-black/80 text-white border-0 cursor-pointer flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </template>
                <button type="button" @click="document.getElementById('foto-input').click()"
                        x-show="photoPreviews.length < 5"
                        class="w-full py-2.5 rounded-xl border border-dashed border-stone-200 text-sm text-stone-500 hover:border-orange-400 hover:text-orange-500 cursor-pointer font-sans transition-colors">
                    + Tambah foto lagi
                </button>
            </div>

            <p class="text-xs text-red-500 mt-1" x-show="photoError" x-cloak x-text="photoError"></p>

            <input type="file" id="foto-input" name="foto[]" accept="image/*" multiple class="hidden" @change="handleFiles($event)">
            @error('foto')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            @error('foto.*')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Anonim toggle --}}
        <div class="mb-6 bg-white rounded-xl p-4 border border-stone-100 shadow-sm flex items-center gap-3.5 cursor-pointer select-none"
             @click="anonim = !anonim">
            <div :class="anonim ? 'bg-orange-500' : 'bg-stone-200'"
                 class="w-11 h-6 rounded-full relative transition-colors duration-200 shrink-0">
                <div :class="anonim ? 'translate-x-5' : 'translate-x-0.5'"
                     class="absolute top-0.5 w-5 h-5 rounded-full bg-white transition-transform duration-200 shadow-sm"></div>
            </div>
            <input type="checkbox" name="anonim" value="1" :checked="anonim" class="hidden">
            <div class="flex-1">
                <p class="text-sm font-semibold text-stone-900">Kirim sebagai anonim</p>
                <p class="text-xs font-light text-stone-500 mt-0.5" x-show="anonim" x-cloak>Identitas NIK Anda tidak akan ditampilkan kepada petugas</p>
                <p class="text-xs font-light text-stone-500 mt-0.5" x-show="!anonim">Identitas Anda akan dicantumkan dalam laporan</p>
            </div>
        </div>

        <button type="submit"
                class="w-full flex items-center justify-center gap-2 py-4 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-[0.9375rem] font-bold border-0 cursor-pointer font-sans transition-all shadow-[0_4px_14px_rgba(234,88,12,0.35)] hover:shadow-[0_6px_20px_rgba(234,88,12,0.4)] hover:-translate-y-0.5 active:translate-y-0">
            <svg class="w-[1.125rem] h-[1.125rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
            </svg>
            Kirim Pengaduan
        </button>
    </form>
</div>
@endsection
