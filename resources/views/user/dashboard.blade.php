@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Welcome Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Halo, {{ $user->name }}! 👋</h1>
            <p class="text-slate-600 mt-1">Kelola rute favorit dan riwayat pencarian Anda</p>
        </div>
        <!-- Realtime Indicator -->
        <div class="flex items-center gap-2 text-sm text-slate-500">
            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
            <span>Live</span>
            <span id="last-update" class="text-xs text-slate-400"></span>
        </div>
    </div>

    <!-- Stats Cards - Realtime Updates -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-slate-100 hover:shadow-xl transition-all">
            <div class="w-12 h-12 bg-rose-100 rounded-xl flex items-center justify-center mb-3">
                <svg class="w-6 h-6 text-rose-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                </svg>
            </div>
            <p class="text-3xl font-bold text-slate-800 stat-value" id="stat-favorites">{{ $stats['favorites'] }}</p>
            <p class="text-sm text-slate-500">Rute Favorit</p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6 border border-slate-100 hover:shadow-xl transition-all">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-3">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <p class="text-3xl font-bold text-slate-800 stat-value" id="stat-searches">{{ $stats['searches'] }}</p>
            <p class="text-sm text-slate-500">Pencarian</p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6 border border-slate-100 hover:shadow-xl transition-all">
            <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-3">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
            <p class="text-3xl font-bold text-slate-800 stat-value" id="stat-comments">{{ $stats['comments'] }}</p>
            <p class="text-sm text-slate-500">Komentar</p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6 border border-slate-100 hover:shadow-xl transition-all">
            <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mb-3">
                <svg class="w-6 h-6 text-amber-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                </svg>
            </div>
            <p class="text-3xl font-bold text-slate-800 stat-value" id="stat-ratings">{{ $stats['ratings'] }}</p>
            <p class="text-sm text-slate-500">Rating</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Recent Favorites -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg p-6 border border-slate-100">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-slate-800">⭐ Rute Favorit Terakhir</h2>
                <a href="{{ route('user.favorites') }}" class="text-emerald-600 hover:underline text-sm">Lihat Semua</a>
            </div>

            @if($recentFavorites->count() > 0)
            <div class="grid sm:grid-cols-2 gap-4" id="favorites-container">
                @foreach($recentFavorites as $route)
                <a href="{{ route('routes.show', $route) }}"
                   class="block p-4 bg-slate-50 rounded-xl hover:bg-emerald-50 transition-colors">
                    <h3 class="font-semibold text-slate-800 truncate">{{ $route->name }}</h3>
                    <div class="flex items-center gap-3 mt-2 text-sm text-slate-500">
                        <span>{{ $route->formatted_distance }}</span>
                        <span>•</span>
                        <span>{{ $route->difficulty_level }}</span>
                    </div>
                </a>
                @endforeach
            </div>
            @else
            <p class="text-slate-500 text-center py-8">Belum ada rute favorit. <a href="{{ route('routes.index') }}" class="text-emerald-600 hover:underline">Jelajahi rute</a></p>
            @endif
        </div>

        <!-- Recent Searches -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-slate-100">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-slate-800">🔍 Pencarian Terakhir</h2>
                <a href="{{ route('user.history') }}" class="text-emerald-600 hover:underline text-sm">Semua</a>
            </div>

            @if($recentSearches->count() > 0)
            <div class="space-y-3" id="searches-container">
                @foreach($recentSearches as $search)
                <div class="p-3 bg-slate-50 rounded-lg">
                    <p class="text-slate-800 font-medium truncate">{{ $search->query }}</p>
                    <div class="flex items-center justify-between mt-1 text-xs text-slate-400">
                        <span>{{ $search->results_count }} hasil</span>
                        <span>{{ $search->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-slate-500 text-center py-8">Belum ada riwayat pencarian</p>
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8 grid sm:grid-cols-3 gap-4">
        <a href="{{ route('search.index') }}"
           class="flex items-center p-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl transition-colors">
            <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <div>
                <p class="font-semibold">Cari Rute</p>
                <p class="text-sm opacity-80">Temukan rute pendakian</p>
            </div>
        </a>

        <a href="{{ route('routes.index') }}"
           class="flex items-center p-4 bg-white hover:bg-slate-50 text-slate-800 rounded-xl border border-slate-200 transition-colors">
            <svg class="w-8 h-8 mr-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
            </svg>
            <div>
                <p class="font-semibold">Semua Rute</p>
                <p class="text-sm text-slate-500" id="total-routes">{{ \App\Models\HikingRoute::count() }} rute tersedia</p>
            </div>
        </a>

        <a href="{{ route('user.profile') }}"
           class="flex items-center p-4 bg-white hover:bg-slate-50 text-slate-800 rounded-xl border border-slate-200 transition-colors">
            <svg class="w-8 h-8 mr-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <div>
                <p class="font-semibold">Edit Profil</p>
                <p class="text-sm text-slate-500">Ubah nama & password</p>
            </div>
        </a>
    </div>
</div>

@push('scripts')
<script>
// Realtime Dashboard Stats Polling
(function() {
    const POLL_INTERVAL = 5000; // 5 seconds

    function updateStats() {
        fetch('{{ route('user.dashboard.stats') }}')
            .then(response => response.json())
            .then(data => {
                // Animate stat updates
                animateValue('stat-favorites', data.favorites);
                animateValue('stat-searches', data.searches);
                animateValue('stat-comments', data.comments);
                animateValue('stat-ratings', data.ratings);

                // Update timestamp
                document.getElementById('last-update').textContent = 'Updated ' + data.timestamp;
            })
            .catch(err => console.log('Stats update failed:', err));
    }

    function animateValue(elementId, newValue) {
        const el = document.getElementById(elementId);
        const oldValue = parseInt(el.textContent);

        if (oldValue !== newValue) {
            el.textContent = newValue;
            el.classList.add('text-emerald-600', 'scale-110');
            setTimeout(() => {
                el.classList.remove('text-emerald-600', 'scale-110');
            }, 500);
        }
    }

    // Start polling
    setInterval(updateStats, POLL_INTERVAL);

    // Initial call after 2 seconds
    setTimeout(updateStats, 2000);
})();
</script>
@endpush

<style>
.stat-value {
    transition: all 0.3s ease;
}
</style>
@endsection
