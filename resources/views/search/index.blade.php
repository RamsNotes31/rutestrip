@extends('layouts.app')

@section('title', 'Cari Rute Pendakian')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-mountain-gradient rounded-3xl p-8 md:p-12 mb-12 shadow-2xl">
        <div class="absolute inset-0 bg-hero-pattern opacity-30"></div>
        <div class="relative z-10">
            <div class="max-w-3xl">
                <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-4 leading-tight">
                    Temukan Jalur Pendakian
                    <span class="block text-emerald-200">Sesuai Kemampuanmu</span>
                </h1>
                <p class="text-lg text-emerald-100 mb-8 max-w-2xl">
                    Sistem rekomendasi cerdas menggunakan AI untuk menemukan rute pendakian gunung yang paling cocok dengan preferensi dan kemampuan Anda.
                </p>

                <!-- Search Form -->
                <form action="{{ route('search.submit') }}" method="POST" class="relative">
                    @csrf
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text"
                                   name="query"
                                   placeholder="Contoh: Jalur landai untuk pemula dengan pemandangan indah..."
                                   value="{{ old('query') }}"
                                   class="w-full pl-12 pr-4 py-4 bg-white/95 backdrop-blur-lg border-0 rounded-2xl text-slate-800 placeholder-slate-400 shadow-xl focus:ring-4 focus:ring-emerald-300 focus:outline-none text-lg"
                                   required
                                   minlength="3">
                        </div>
                        <button type="submit"
                                class="px-8 py-4 bg-emerald-500 hover:bg-emerald-400 text-white font-bold rounded-2xl shadow-xl hover:shadow-2xl transition-all flex items-center justify-center space-x-2 hover:scale-105">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <span>Cari Rute</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Decorative Mountain SVG -->
        <div class="absolute bottom-0 right-0 w-64 h-64 opacity-20">
            <svg viewBox="0 0 200 200" fill="currentColor" class="text-white">
                <path d="M100 20L180 180H20L100 20Z"/>
                <path d="M150 60L200 180H100L150 60Z" opacity="0.5"/>
            </svg>
        </div>
    </div>

    <!-- Quick Search Suggestions -->
    <div class="mb-12">
        <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-4">Coba pencarian populer:</h3>
        <div class="flex flex-wrap gap-3">
            @php
                $suggestions = [
                    'Jalur mudah untuk pemula',
                    'Rute menantang dengan elevasi tinggi',
                    'Pendakian singkat 2-3 jam',
                    'Jalur landai dengan jarak pendek',
                    'Trek panjang untuk pendaki berpengalaman',
                ];
            @endphp
            @foreach($suggestions as $suggestion)
            <form action="{{ route('search.submit') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="query" value="{{ $suggestion }}">
                <button type="submit"
                        class="px-4 py-2 bg-white border border-slate-200 rounded-full text-sm text-slate-600 hover:bg-emerald-50 hover:border-emerald-300 hover:text-emerald-700 transition-all shadow-sm hover:shadow">
                    {{ $suggestion }}
                </button>
            </form>
            @endforeach
        </div>
    </div>

    <!-- Features Section -->
    <div class="grid md:grid-cols-3 gap-8">
        <div class="glass-card rounded-2xl p-6 shadow-lg hover:shadow-xl transition-shadow">
            <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">AI-Powered</h3>
            <p class="text-slate-600">Menggunakan SBERT dan Cosine Similarity untuk rekomendasi cerdas berdasarkan deskripsi natural.</p>
        </div>

        <div class="glass-card rounded-2xl p-6 shadow-lg hover:shadow-xl transition-shadow">
            <div class="w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">Analisis GPX</h3>
            <p class="text-slate-600">Ekstraksi otomatis jarak, elevasi, grade kemiringan, dan estimasi waktu dari file GPX.</p>
        </div>

        <div class="glass-card rounded-2xl p-6 shadow-lg hover:shadow-xl transition-shadow">
            <div class="w-12 h-12 bg-cyan-100 rounded-xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">Naismith's Rule</h3>
            <p class="text-slate-600">Estimasi waktu pendakian akurat menggunakan rumus Naismith yang terbukti dan dipercaya.</p>
        </div>
    </div>
</div>
@endsection
