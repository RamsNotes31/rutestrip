@extends('layouts.app')

@section('title', $route->name)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Back Link -->
    <a href="{{ route('routes.index') }}" class="inline-flex items-center text-emerald-600 hover:text-emerald-700 mb-6 group">
        <svg class="w-5 h-5 mr-2 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke Daftar Rute
    </a>

    <!-- Route Detail Card -->
    <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-100">
        <!-- Header -->
        <div class="bg-mountain-gradient p-8 md:p-12">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-medium mb-4
                        @if($route->difficulty_level === 'Mudah') bg-green-100 text-green-800
                        @elseif($route->difficulty_level === 'Sedang') bg-yellow-100 text-yellow-800
                        @elseif($route->difficulty_level === 'Sulit') bg-orange-100 text-orange-800
                        @else bg-red-100 text-red-800
                        @endif">
                        {{ $route->difficulty_level }}
                    </span>
                    <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-2">{{ $route->name }}</h1>
                    <p class="text-emerald-100">Diupload pada {{ $route->created_at->format('d M Y, H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-1 bg-slate-100">
            <div class="bg-white p-6 text-center">
                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <p class="text-3xl font-bold text-slate-800">{{ $route->formatted_distance }}</p>
                <p class="text-sm text-slate-500 mt-1">Total Jarak</p>
            </div>

            <div class="bg-white p-6 text-center">
                <div class="w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-3xl font-bold text-slate-800">{{ $route->formatted_duration }}</p>
                <p class="text-sm text-slate-500 mt-1">Estimasi Waktu</p>
            </div>

            <div class="bg-white p-6 text-center">
                <div class="w-12 h-12 bg-cyan-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                    </svg>
                </div>
                <p class="text-3xl font-bold text-slate-800">{{ number_format($route->elevation_gain_m) }}m</p>
                <p class="text-sm text-slate-500 mt-1">Elevasi Gain</p>
            </div>

            <div class="bg-white p-6 text-center">
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <p class="text-3xl font-bold text-slate-800">{{ $route->formatted_grade }}</p>
                <p class="text-sm text-slate-500 mt-1">Grade Rata-rata</p>
            </div>
        </div>

        <!-- Narrative Section -->
        <div class="p-8">
            <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center">
                <svg class="w-5 h-5 text-emerald-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Deskripsi AI
            </h3>
            <div class="bg-slate-50 rounded-xl p-6">
                <p class="text-slate-700 leading-relaxed">
                    {{ $route->narrative_text ?? 'Belum ada deskripsi untuk jalur ini.' }}
                </p>
            </div>
        </div>

        <!-- Actions -->
        <div class="p-8 bg-slate-50 border-t border-slate-100">
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="{{ route('search.index') }}"
                   class="flex-1 py-3 text-center bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl transition-colors">
                    Cari Rute Serupa
                </a>
                <form action="{{ route('routes.destroy', $route) }}" method="POST" class="flex-1"
                      onsubmit="return confirm('Yakin ingin menghapus rute ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full py-3 bg-red-100 hover:bg-red-200 text-red-700 font-medium rounded-xl transition-colors">
                        Hapus Rute
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
