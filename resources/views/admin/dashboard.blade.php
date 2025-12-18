@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-800 mb-2">Admin Dashboard</h1>
        <p class="text-slate-600">Kelola dataset dan lihat proses SBERT embedding</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-lg border border-slate-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 mb-1">Total Rute</p>
                    <p class="text-3xl font-bold text-slate-800">{{ $stats['total_routes'] }}</p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-slate-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 mb-1">Total Jarak</p>
                    <p class="text-3xl font-bold text-slate-800">{{ number_format($stats['total_distance'], 1) }} <span class="text-lg font-normal text-slate-500">km</span></p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-slate-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 mb-1">Rata-rata Elevasi</p>
                    <p class="text-3xl font-bold text-slate-800">{{ number_format($stats['avg_elevation'], 0) }} <span class="text-lg font-normal text-slate-500">m</span></p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-slate-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 mb-1">Dengan Embedding</p>
                    <p class="text-3xl font-bold text-slate-800">{{ $stats['routes_with_embedding'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Buttons -->
    <div class="bg-white rounded-2xl shadow-lg border border-slate-100 p-6 mb-8">
        <h2 class="text-xl font-bold text-slate-800 mb-4">Export Dataset</h2>
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('admin.export.csv') }}"
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all hover:scale-105">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Download Full Dataset (CSV)
            </a>
            <a href="{{ route('admin.export.embeddings') }}"
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all hover:scale-105">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                </svg>
                Download Embeddings Only (CSV)
            </a>
        </div>
        <p class="mt-4 text-sm text-slate-500">
            <strong>Full Dataset:</strong> Semua data termasuk metadata dan embedding dalam satu file.<br>
            <strong>Embeddings Only:</strong> Format tabel dengan 384 kolom dimensi untuk analisis ML.
        </p>
    </div>

    <!-- SBERT Process Explanation -->
    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-2xl shadow-xl p-8 mb-8 text-white">
        <h2 class="text-2xl font-bold mb-4">Proses SBERT & Cosine Similarity</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-5">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center mb-3">
                    <span class="text-xl font-bold">1</span>
                </div>
                <h3 class="font-semibold mb-2">GPX Processing</h3>
                <p class="text-sm text-purple-100">File GPX diekstrak untuk mendapatkan jarak, elevasi, durasi Naismith, dan grade.</p>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-5">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center mb-3">
                    <span class="text-xl font-bold">2</span>
                </div>
                <h3 class="font-semibold mb-2">Narrative Generation</h3>
                <p class="text-sm text-purple-100">Statistik diubah menjadi teks naratif deskriptif untuk input SBERT.</p>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-5">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center mb-3">
                    <span class="text-xl font-bold">3</span>
                </div>
                <h3 class="font-semibold mb-2">SBERT Embedding</h3>
                <p class="text-sm text-purple-100">Model paraphrase-multilingual-MiniLM-L12-v2 menghasilkan vector 384 dimensi.</p>
            </div>
        </div>
        <div class="mt-6 bg-white/10 backdrop-blur-sm rounded-xl p-5">
            <h3 class="font-semibold mb-2">Cosine Similarity Search</h3>
            <p class="text-sm text-purple-100">Saat user mencari rute, query diubah menjadi embedding, lalu dihitung cosine similarity dengan semua rute untuk menemukan yang paling mirip.</p>
            <code class="block mt-2 bg-black/20 rounded-lg p-3 text-xs font-mono">similarity = cos(θ) = (A · B) / (||A|| × ||B||)</code>
        </div>
    </div>

    <!-- Routes Table -->
    <div class="bg-white rounded-2xl shadow-lg border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
            <h2 class="text-xl font-bold text-slate-800">Dataset Rute Pendakian</h2>
            <span class="text-sm text-slate-500">{{ $routes->total() }} total rute</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Nama Rute</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Jarak</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Elevasi</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Durasi</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Grade</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Embedding</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($routes as $route)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-4 text-sm text-slate-600">{{ $route->id }}</td>
                        <td class="px-4 py-4">
                            <div class="font-medium text-slate-800">{{ Str::limit($route->name, 30) }}</div>
                            <div class="text-xs text-slate-500">{{ $route->created_at->format('d M Y') }}</div>
                        </td>
                        <td class="px-4 py-4 text-sm text-slate-600">{{ $route->distance_km ?? '-' }} km</td>
                        <td class="px-4 py-4 text-sm text-slate-600">{{ $route->elevation_gain_m ?? '-' }} m</td>
                        <td class="px-4 py-4 text-sm text-slate-600">{{ $route->naismith_duration_hour ?? '-' }} jam</td>
                        <td class="px-4 py-4 text-sm text-slate-600">{{ $route->average_grade_pct ?? '-' }}%</td>
                        <td class="px-4 py-4">
                            @if($route->sbert_embedding)
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        384 dim
                                    </span>
                                    <button onclick="showEmbedding({{ $route->id }}, '{{ $route->name }}')"
                                            class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">
                                        Lihat
                                    </button>
                                </div>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
                                    Tidak ada
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <a href="{{ route('routes.show', $route) }}" class="text-emerald-600 hover:text-emerald-800 text-sm font-medium">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-slate-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p>Belum ada data rute.</p>
                            <a href="{{ route('routes.create') }}" class="text-emerald-600 hover:underline mt-2 inline-block">Upload GPX pertama</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($routes->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $routes->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Embedding Modal -->
<div id="embeddingModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-gradient-to-r from-purple-600 to-indigo-600 text-white">
            <div>
                <h3 class="text-lg font-bold">SBERT Embedding</h3>
                <p class="text-sm text-purple-100" id="modalRouteName">-</p>
            </div>
            <button onclick="closeModal()" class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center hover:bg-white/30">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-6 overflow-y-auto max-h-[70vh]">
            <div class="mb-4">
                <h4 class="font-semibold text-slate-700 mb-2">Narrative Text (Input SBERT)</h4>
                <p class="text-sm text-slate-600 bg-slate-50 rounded-xl p-4" id="modalNarrative">-</p>
            </div>
            <div>
                <h4 class="font-semibold text-slate-700 mb-2">Embedding Vector (384 dimensi)</h4>
                <div class="bg-slate-900 rounded-xl p-4 overflow-x-auto">
                    <code class="text-xs text-green-400 font-mono whitespace-pre-wrap" id="modalEmbedding">-</code>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Store route data for modal
    const routeData = @json($routes->items());

    function showEmbedding(id, name) {
        const route = routeData.find(r => r.id === id);
        if (!route) return;

        document.getElementById('modalRouteName').textContent = name;
        document.getElementById('modalNarrative').textContent = route.narrative_text || 'Tidak ada narasi';

        const embedding = route.sbert_embedding;
        if (embedding && Array.isArray(embedding)) {
            const formatted = embedding.map((v, i) => `[${i}]: ${v.toFixed(6)}`).join('\n');
            document.getElementById('modalEmbedding').textContent = formatted;
        } else {
            document.getElementById('modalEmbedding').textContent = 'Tidak ada embedding';
        }

        const modal = document.getElementById('embeddingModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        const modal = document.getElementById('embeddingModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Close modal on backdrop click
    document.getElementById('embeddingModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
</script>
@endsection
