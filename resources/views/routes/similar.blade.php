@extends('layouts.app')

@section('title', 'Rute Serupa - ' . $route->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <a href="{{ route('routes.show', $route) }}" class="inline-flex items-center text-emerald-600 hover:text-emerald-700 mb-4 group">
            <svg class="w-5 h-5 mr-2 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke {{ $route->name }}
        </a>

        <h1 class="text-3xl font-bold text-slate-800 mb-2">Rute Serupa dengan {{ $route->name }}</h1>
        <p class="text-slate-600">
            Ditemukan <span class="font-semibold text-emerald-600">{{ $similarRoutes->count() }}</span> rute dengan kemiripan > 30%
        </p>

        <!-- Formula -->
        <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-lg inline-block">
            <p class="text-sm text-amber-800">
                <strong>Formula:</strong> Cosine Similarity = (A · B) / (||A|| × ||B||)
            </p>
        </div>
    </div>

    @if($similarRoutes->count() > 0)
    <!-- Similar Routes Grid -->
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($similarRoutes as $similar)
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-slate-100 hover:shadow-xl transition-all">
            <!-- Map Preview -->
            @if($similar->route_coordinates && count($similar->route_coordinates) > 0)
            <div id="map-{{ $similar->id }}" class="w-full h-40 bg-slate-200"></div>
            @else
            <div class="w-full h-40 bg-gradient-to-br from-emerald-100 to-teal-100 flex items-center justify-center">
                <span class="text-4xl">🏔️</span>
            </div>
            @endif

            <div class="p-5">
                <!-- Similarity Badge -->
                <div class="flex items-center justify-between mb-3">
                    <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-sm font-bold rounded-full">
                        {{ $similar->similarity_score }}% match
                    </span>
                    <span class="text-xs font-mono text-slate-500 bg-slate-100 px-2 py-1 rounded">
                        cos={{ $similar->cosine_value }}
                    </span>
                </div>

                <!-- Route Name -->
                <h3 class="font-bold text-lg text-slate-800 mb-2">{{ $similar->name }}</h3>

                <!-- Description -->
                @if($similar->narrative_text || $similar->manual_description)
                <p class="text-sm text-slate-600 mb-3 line-clamp-2">
                    {{ Str::limit($similar->manual_description ?: $similar->narrative_text, 100) }}
                </p>
                @endif

                <!-- Stats -->
                <div class="grid grid-cols-3 gap-2 mb-4 text-center">
                    <div class="bg-slate-50 p-2 rounded-lg">
                        <p class="text-xs text-slate-500">Jarak</p>
                        <p class="font-semibold text-slate-700">{{ $similar->formatted_distance }}</p>
                    </div>
                    <div class="bg-slate-50 p-2 rounded-lg">
                        <p class="text-xs text-slate-500">Elevasi</p>
                        <p class="font-semibold text-slate-700">{{ number_format($similar->elevation_gain_m) }}m</p>
                    </div>
                    <div class="bg-slate-50 p-2 rounded-lg">
                        <p class="text-xs text-slate-500">Grade</p>
                        <p class="font-semibold text-slate-700">{{ $similar->formatted_grade }}</p>
                    </div>
                </div>

                <!-- Difficulty & Button -->
                <div class="flex items-center justify-between">
                    <span class="text-sm px-2 py-1 rounded
                        @if($similar->difficulty_level === 'Mudah') bg-green-100 text-green-700
                        @elseif($similar->difficulty_level === 'Sedang') bg-yellow-100 text-yellow-700
                        @elseif($similar->difficulty_level === 'Sulit') bg-orange-100 text-orange-700
                        @else bg-red-100 text-red-700
                        @endif">
                        {{ $similar->difficulty_level }}
                    </span>

                    <a href="{{ route('routes.show', $similar) }}"
                       class="px-4 py-2 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700 transition-colors">
                        Lihat Detail
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-16 bg-white rounded-2xl shadow-lg">
        <span class="text-6xl mb-4 block">🔍</span>
        <h3 class="text-xl font-semibold text-slate-700 mb-2">Tidak Ada Rute Serupa</h3>
        <p class="text-slate-500">Tidak ditemukan rute dengan kemiripan > 30%</p>
    </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    @foreach($similarRoutes as $similar)
    @if($similar->route_coordinates && count($similar->route_coordinates) > 0)
    (function() {
        var coords = @json($similar->route_coordinates);
        if (coords && coords.length > 0) {
            var map = L.map('map-{{ $similar->id }}', {
                zoomControl: false, dragging: false, scrollWheelZoom: false,
                doubleClickZoom: false, touchZoom: false
            });
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 18 }).addTo(map);
            var poly = L.polyline(coords, { color: '#10b981', weight: 3, opacity: 0.9 }).addTo(map);
            map.fitBounds(poly.getBounds(), { padding: [10, 10] });
        }
    })();
    @endif
    @endforeach
});
</script>
@endpush
@endsection
