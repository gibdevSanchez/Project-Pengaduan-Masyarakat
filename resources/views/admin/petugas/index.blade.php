@extends('layouts.app')

@section('title', 'Manajemen Petugas')

@section('content')
<div class="flex-1 overflow-y-auto p-6 bg-stone-50">

    <div class="flex items-center justify-between mb-5">
        <div>
            <h1 class="text-xl font-bold text-stone-900 tracking-tight">Manajemen Petugas</h1>
            <p class="text-sm font-light text-stone-500 mt-0.5">{{ $petugas->count() }} akun petugas terdaftar</p>
        </div>
        <a href="{{ route('admin.petugas.create') }}"
           class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold no-underline transition-all shadow-sm shadow-orange-200 hover:-translate-y-0.5 active:translate-y-px">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Petugas
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-stone-100 overflow-hidden">
        @if($petugas->isEmpty())
            <div class="p-16 text-center">
                <div class="w-16 h-16 rounded-2xl bg-stone-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <p class="font-semibold text-stone-700 mb-1.5">Belum ada petugas</p>
                <p class="text-sm font-light text-stone-400">Tambah akun petugas untuk mulai mengelola pengaduan.</p>
            </div>
        @else
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-stone-50 border-b border-stone-200">
                        <th class="px-5 py-3.5 text-left text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wider">Nama</th>
                        <th class="px-5 py-3.5 text-left text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wider">Username</th>
                        <th class="px-5 py-3.5 text-left text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wider">Telepon</th>
                        <th class="px-5 py-3.5 text-right text-[0.6875rem] font-semibold text-stone-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @foreach($petugas as $p)
                    <tr class="hover:bg-stone-50/60 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                @if($p->foto_profil)
                                <img src="{{ Storage::url($p->foto_profil) }}" alt="{{ $p->nama_petugas }}"
                                     class="w-9 h-9 rounded-full object-cover shrink-0 ring-1 ring-stone-200">
                                @else
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white text-xs font-bold shrink-0">
                                    {{ strtoupper(substr($p->nama_petugas, 0, 1)) }}
                                </div>
                                @endif
                                <div>
                                    <p class="text-sm font-semibold text-stone-900">{{ $p->nama_petugas }}</p>
                                    @if(isset($p->level))
                                    <p class="text-[0.6875rem] text-stone-400 capitalize">{{ $p->level }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <code class="text-[0.8125rem] text-stone-500 bg-stone-100 px-2 py-0.5 rounded-md font-mono">{{ $p->username }}</code>
                        </td>
                        <td class="px-5 py-4 text-sm text-stone-500">{{ $p->telp }}</td>
                        <td class="px-5 py-4 text-right">
                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('admin.petugas.edit', $p->id_petugas) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-stone-200 bg-white text-[0.8125rem] font-medium text-stone-600 no-underline hover:border-orange-300 hover:text-orange-600 transition-colors">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Edit
                                </a>

                                <form method="POST" action="{{ route('admin.petugas.destroy', $p->id_petugas) }}"
                                      onsubmit="return confirm('Hapus akun petugas {{ addslashes($p->nama_petugas) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-red-200 bg-white text-[0.8125rem] font-medium text-red-500 cursor-pointer font-sans hover:bg-red-50 hover:border-red-300 transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
