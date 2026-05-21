<?php

namespace App\Http\Controllers\Masyarakat;

use App\Http\Controllers\Controller;

class BeritaController extends Controller
{
    public function index()
    {
        $berita = [
            [
                'judul'    => 'Pemkot Luncurkan Program Perbaikan Jalan di 5 Kecamatan',
                'sumber'   => 'Harian Kota',
                'waktu'    => '2 jam lalu',
                'kategori' => 'Infrastruktur',
                'gambar'   => null,
                'ringkasan'=> 'Pemerintah Kota mengalokasikan anggaran Rp 12 miliar untuk perbaikan jalan rusak di wilayah padat penduduk.',
            ],
            [
                'judul'    => 'Layanan Pengaduan Online Diminati 3.000 Warga Bulan Ini',
                'sumber'   => 'Portal Berita Daerah',
                'waktu'    => '5 jam lalu',
                'kategori' => 'Layanan Publik',
                'gambar'   => null,
                'ringkasan'=> 'Aplikasi M-Lapor terus mendapat respons positif dari masyarakat. Tingkat penyelesaian laporan mencapai 87%.',
            ],
            [
                'judul'    => 'Peningkatan Kapasitas Petugas Kebersihan Kota',
                'sumber'   => 'Radar Lokal',
                'waktu'    => '1 hari lalu',
                'kategori' => 'Lingkungan',
                'gambar'   => null,
                'ringkasan'=> 'Dinas Kebersihan menambah armada truk sampah dan melatih 200 petugas baru untuk meningkatkan layanan.',
            ],
            [
                'judul'    => 'Proyek Revitalisasi Taman Kota Dimulai Minggu Depan',
                'sumber'   => 'Metro Hari Ini',
                'waktu'    => '1 hari lalu',
                'kategori' => 'Tata Kota',
                'gambar'   => null,
                'ringkasan'=> 'Renovasi taman kota seluas 2 hektar akan dimulai dengan anggaran dari APBD 2026 sebesar Rp 4,5 miliar.',
            ],
            [
                'judul'    => 'Warga Diminta Aktif Laporkan Masalah Lingkungan',
                'sumber'   => 'Suara Warga',
                'waktu'    => '2 hari lalu',
                'kategori' => 'Himbauan',
                'gambar'   => null,
                'ringkasan'=> 'Pemerintah mengimbau masyarakat untuk tidak ragu melaporkan permasalahan lingkungan melalui kanal resmi.',
            ],
            [
                'judul'    => 'Perbaikan Drainase di Kawasan Rawan Banjir Selesai Lebih Cepat',
                'sumber'   => 'Kota Berita',
                'waktu'    => '3 hari lalu',
                'kategori' => 'Infrastruktur',
                'gambar'   => null,
                'ringkasan'=> 'Proyek normalisasi saluran drainase di 12 titik rawan banjir berhasil diselesaikan sebelum musim hujan tiba.',
            ],
        ];

        return view('masyarakat.berita', compact('berita'));
    }
}
