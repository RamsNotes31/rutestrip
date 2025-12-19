@extends('layouts.app')

@section('title', 'Arsitektur Sistem')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="text-center mb-12">
        <h1 class="text-3xl font-bold text-slate-800 mb-4">Arsitektur Sistem Rekomendasi</h1>
        <p class="text-slate-600 max-w-2xl mx-auto">
            Content-Based Filtering dengan SBERT dan Cosine Similarity
        </p>
    </div>

    <!-- Architecture Diagram -->
    <div class="bg-white rounded-3xl shadow-xl p-8 mb-8 border border-slate-100">
        <h2 class="text-xl font-bold text-slate-800 mb-6 text-center">📊 Diagram Alur Sistem</h2>

        <div class="flex flex-col lg:flex-row items-center justify-center gap-4 overflow-x-auto py-4">
            <!-- Step 1: Input -->
            <div class="flex flex-col items-center">
                <div class="w-32 h-24 bg-blue-100 rounded-xl flex items-center justify-center border-2 border-blue-300">
                    <div class="text-center">
                        <svg class="w-8 h-8 text-blue-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="text-xs font-medium text-blue-800">File GPX</span>
                    </div>
                </div>
                <span class="text-xs text-slate-500 mt-2">Input</span>
            </div>

            <svg class="w-6 h-6 text-slate-400 transform rotate-90 lg:rotate-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>

            <!-- Step 2: Preprocessing -->
            <div class="flex flex-col items-center">
                <div class="w-40 h-24 bg-purple-100 rounded-xl flex items-center justify-center border-2 border-purple-300">
                    <div class="text-center px-2">
                        <svg class="w-8 h-8 text-purple-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span class="text-xs font-medium text-purple-800">Preprocessing</span>
                        <span class="text-[10px] text-purple-600 block">Cleaning, Lowercase</span>
                    </div>
                </div>
                <span class="text-xs text-slate-500 mt-2">Data Processing</span>
            </div>

            <svg class="w-6 h-6 text-slate-400 transform rotate-90 lg:rotate-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>

            <!-- Step 3: Feature Extraction -->
            <div class="flex flex-col items-center">
                <div class="w-44 h-24 bg-emerald-100 rounded-xl flex items-center justify-center border-2 border-emerald-300">
                    <div class="text-center px-2">
                        <svg class="w-8 h-8 text-emerald-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-xs font-medium text-emerald-800">SBERT Embedding</span>
                        <span class="text-[10px] text-emerald-600 block">384 dimensi</span>
                    </div>
                </div>
                <span class="text-xs text-slate-500 mt-2">Feature Extraction</span>
            </div>

            <svg class="w-6 h-6 text-slate-400 transform rotate-90 lg:rotate-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>

            <!-- Step 4: Similarity -->
            <div class="flex flex-col items-center">
                <div class="w-40 h-24 bg-amber-100 rounded-xl flex items-center justify-center border-2 border-amber-300">
                    <div class="text-center px-2">
                        <svg class="w-8 h-8 text-amber-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-xs font-medium text-amber-800">Cosine Similarity</span>
                        <span class="text-[10px] text-amber-600 block">Q·D / (||Q||×||D||)</span>
                    </div>
                </div>
                <span class="text-xs text-slate-500 mt-2">Similarity</span>
            </div>

            <svg class="w-6 h-6 text-slate-400 transform rotate-90 lg:rotate-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>

            <!-- Step 5: Output -->
            <div class="flex flex-col items-center">
                <div class="w-36 h-24 bg-rose-100 rounded-xl flex items-center justify-center border-2 border-rose-300">
                    <div class="text-center px-2">
                        <svg class="w-8 h-8 text-rose-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <span class="text-xs font-medium text-rose-800">Top-N Ranking</span>
                        <span class="text-[10px] text-rose-600 block">Rekomendasi</span>
                    </div>
                </div>
                <span class="text-xs text-slate-500 mt-2">Output</span>
            </div>
        </div>
    </div>

    <!-- Component Details -->
    <div class="grid md:grid-cols-2 gap-6 mb-8">
        <!-- Data Collection -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-slate-100">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-800">1. Data Collection & Fusion</h3>
            </div>
            <ul class="text-sm text-slate-600 space-y-2">
                <li class="flex items-start">
                    <span class="text-emerald-500 mr-2">✓</span>
                    Ekstraksi fitur dari GPX (jarak, elevasi, grade, durasi Naismith)
                </li>
                <li class="flex items-start">
                    <span class="text-emerald-500 mr-2">✓</span>
                    Deskripsi manual (vegetasi, sumber air, panorama)
                </li>
                <li class="flex items-start">
                    <span class="text-emerald-500 mr-2">✓</span>
                    Koordinat rute untuk visualisasi peta
                </li>
            </ul>
        </div>

        <!-- Preprocessing -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-slate-100">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-800">2. Text Preprocessing</h3>
            </div>
            <ul class="text-sm text-slate-600 space-y-2">
                <li class="flex items-start">
                    <span class="text-emerald-500 mr-2">✓</span>
                    Data Cleaning (regex: URL, karakter non-ASCII)
                </li>
                <li class="flex items-start">
                    <span class="text-emerald-500 mr-2">✓</span>
                    Case Folding (lowercase)
                </li>
                <li class="flex items-start">
                    <span class="text-emerald-500 mr-2">✓</span>
                    Stopword Removal Selektif (pertahankan negasi & kata sifat)
                </li>
                <li class="flex items-start">
                    <span class="text-orange-500 mr-2">✗</span>
                    No Stemming (SBERT sensitif konteks)
                </li>
            </ul>
        </div>

        <!-- SBERT -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-slate-100">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-800">3. SBERT Embedding</h3>
            </div>
            <ul class="text-sm text-slate-600 space-y-2">
                <li class="flex items-start">
                    <span class="text-emerald-500 mr-2">✓</span>
                    Model: <code class="bg-slate-100 px-1 rounded">paraphrase-multilingual-MiniLM-L12-v2</code>
                </li>
                <li class="flex items-start">
                    <span class="text-emerald-500 mr-2">✓</span>
                    Dimensi embedding: 384
                </li>
                <li class="flex items-start">
                    <span class="text-emerald-500 mr-2">✓</span>
                    Support bahasa Indonesia
                </li>
            </ul>
        </div>

        <!-- Cosine Similarity -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-slate-100">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-800">4. Cosine Similarity</h3>
            </div>
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl p-4 border border-amber-100 mb-3">
                <p class="text-center font-mono text-lg text-amber-800">
                    Sim(Q, D) = (Q · D) / (||Q|| × ||D||)
                </p>
            </div>
            <ul class="text-sm text-slate-600 space-y-1">
                <li><strong>Q</strong> = Query embedding (user search)</li>
                <li><strong>D</strong> = Document embedding (route)</li>
                <li><strong>Range</strong> = -1 to 1 (1 = identical)</li>
            </ul>
        </div>
    </div>

    <!-- Tech Stack -->
    <div class="bg-white rounded-2xl shadow-lg p-6 border border-slate-100">
        <h3 class="text-lg font-bold text-slate-800 mb-4 text-center">🛠️ Technology Stack</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="text-center p-4 bg-slate-50 rounded-xl">
                <div class="text-2xl mb-2">🐘</div>
                <div class="font-medium text-slate-800">Laravel 11</div>
                <div class="text-xs text-slate-500">Backend Framework</div>
            </div>
            <div class="text-center p-4 bg-slate-50 rounded-xl">
                <div class="text-2xl mb-2">🐍</div>
                <div class="font-medium text-slate-800">Python 3.11</div>
                <div class="text-xs text-slate-500">ML Processing</div>
            </div>
            <div class="text-center p-4 bg-slate-50 rounded-xl">
                <div class="text-2xl mb-2">🤖</div>
                <div class="font-medium text-slate-800">SBERT</div>
                <div class="text-xs text-slate-500">Sentence Transformers</div>
            </div>
            <div class="text-center p-4 bg-slate-50 rounded-xl">
                <div class="text-2xl mb-2">🗺️</div>
                <div class="font-medium text-slate-800">Leaflet.js</div>
                <div class="text-xs text-slate-500">Map Visualization</div>
            </div>
        </div>
    </div>
</div>
@endsection
