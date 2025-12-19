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

        <!-- Basecamp & Practical Info Section -->
        @if($route->basecamp_name || $route->entry_fee || $route->facilities)
        <div class="p-8 border-t border-slate-100">
            <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center">
                <svg class="w-5 h-5 text-purple-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Info Basecamp & Praktis
            </h3>

            <div class="grid md:grid-cols-2 gap-6">
                <!-- Left Column : Address & Contact -->
                <div class="space-y-4">
                    @if($route->basecamp_name)
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="text-lg">⛺</span>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Nama Basecamp</p>
                            <p class="font-semibold text-slate-800">{{ $route->basecamp_name }}</p>
                        </div>
                    </div>
                    @endif

                    @if($route->basecamp_address)
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="text-lg">📍</span>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Alamat</p>
                            <p class="text-slate-700">{{ $route->basecamp_address }}</p>
                            @if($route->basecamp_lat && $route->basecamp_lng)
                            <a href="https://www.google.com/maps?q={{ $route->basecamp_lat }},{{ $route->basecamp_lng }}"
                               target="_blank"
                               class="text-sm text-emerald-600 hover:underline inline-flex items-center mt-1">
                                Buka di Google Maps →
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if($route->contact_phone)
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="text-lg">📞</span>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Kontak</p>
                            <p class="font-semibold text-slate-800">{{ $route->contact_phone }}</p>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Right Column: Price, Facilities, Season -->
                <div class="space-y-4">
                    @if($route->entry_fee)
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="text-lg">💰</span>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Harga Tiket Masuk</p>
                            <p class="font-bold text-2xl text-emerald-600">Rp {{ number_format($route->entry_fee, 0, ',', '.') }}</p>
                            <p class="text-xs text-slate-400">Per orang, weekday</p>
                        </div>
                    </div>
                    @endif

                    @if($route->facilities)
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="text-lg">🏕️</span>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Fasilitas</p>
                            <p class="text-slate-700">{{ $route->facilities }}</p>
                        </div>
                    </div>
                    @endif

                    @if($route->best_season)
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="text-lg">🗓️</span>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Musim Terbaik</p>
                            <p class="font-semibold text-slate-800">{{ $route->best_season }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Tips Section -->
            @if($route->tips)
            <div class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                <div class="flex items-start gap-3">
                    <span class="text-2xl">💡</span>
                    <div>
                        <p class="font-semibold text-amber-800 mb-1">Tips Pendakian</p>
                        <p class="text-amber-700 text-sm">{{ $route->tips }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif

        <!-- Narrative Section -->
        <div class="p-8 border-t border-slate-100">
            @if($route->description)
            <!-- Manual Description (Review-based) -->
            <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center">
                <svg class="w-5 h-5 text-emerald-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Info Jalur (Vegetasi, Sumber Air, Panorama)
            </h3>
            <div class="bg-emerald-50 rounded-xl p-6 mb-6 border border-emerald-100">
                <p class="text-slate-700 leading-relaxed">
                    {{ $route->description }}
                </p>
            </div>
            @endif

            <!-- AI Description -->
            <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center">
                <svg class="w-5 h-5 text-emerald-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Deskripsi AI (SBERT)
            </h3>
            <div class="bg-slate-50 rounded-xl p-6 mb-6">
                <p class="text-slate-700 leading-relaxed">
                    {{ $route->narrative_text ?? 'Belum ada deskripsi untuk jalur ini.' }}
                </p>
            </div>

            <!-- Route Map -->
            @if($route->route_coordinates && count($route->route_coordinates) > 0)
            <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center">
                <svg class="w-5 h-5 text-emerald-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
                Peta Rute Pendakian
            </h3>
            <div class="bg-slate-50 rounded-xl overflow-hidden border border-slate-200">
                <div id="route-map" class="w-full h-80 md:h-96"></div>
                <div class="p-4 bg-white border-t border-slate-100">
                    <div class="flex items-center justify-between text-sm text-slate-600">
                        <span class="flex items-center">
                            <span class="w-4 h-1 bg-emerald-500 rounded mr-2"></span>
                            Jalur Pendakian
                        </span>
                        <span>{{ count($route->route_coordinates) }} titik koordinat</span>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Rating & Like Stats -->
        <div class="p-6 bg-gradient-to-r from-emerald-50 to-teal-50 border-t border-slate-100">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-6">
                    <!-- Rating Display -->
                    <div class="flex items-center gap-2">
                        <div class="flex">
                            @for($i = 1; $i <= 5; $i++)
                            <svg class="w-5 h-5 {{ $i <= $route->average_rating ? 'text-yellow-400' : 'text-slate-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            @endfor
                        </div>
                        <span class="font-bold text-slate-700">{{ $route->average_rating }}/5</span>
                        <span class="text-sm text-slate-500">({{ $route->ratings->count() }} rating)</span>
                    </div>

                    <!-- Like Count -->
                    <div class="flex items-center gap-2">
                        <span class="text-xl">❤️</span>
                        <span class="font-bold text-slate-700">{{ $route->likes_count }}</span>
                        <span class="text-sm text-slate-500">likes</span>
                    </div>
                </div>

                <!-- Like Button -->
                @auth
                <form action="{{ route('routes.like.toggle', $route) }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="px-4 py-2 rounded-xl font-medium transition-colors {{ $route->isLikedBy(Auth::id()) ? 'bg-red-500 text-white hover:bg-red-600' : 'bg-white text-slate-700 hover:bg-red-100 border border-slate-200' }}">
                        {{ $route->isLikedBy(Auth::id()) ? '❤️ Disukai' : '🤍 Suka' }}
                    </button>
                </form>
                @else
                <a href="{{ route('login') }}" class="px-4 py-2 bg-white text-slate-700 rounded-xl font-medium border border-slate-200 hover:bg-slate-50">
                    🤍 Login untuk Suka
                </a>
                @endauth
            </div>
        </div>

        <!-- Actions -->
        <div class="p-8 bg-slate-50 border-t border-slate-100">
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="{{ route('routes.similar', $route) }}"
                   class="flex-1 py-3 text-center bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl transition-colors flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Lihat Semua Rute Serupa
                </a>
                @auth
                @if(Auth::user()->isAdmin())
                <form action="{{ route('routes.destroy', $route) }}" method="POST" class="flex-1"
                      onsubmit="return confirm('Yakin ingin menghapus rute ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full py-3 bg-red-100 hover:bg-red-200 text-red-700 font-medium rounded-xl transition-colors">
                        🗑️ Hapus Rute (Admin)
                    </button>
                </form>
                @endif
                @endauth
            </div>
        </div>

        <!-- Review Form -->
        @auth
        <div class="p-8 border-t border-slate-100">
            <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center">
                <svg class="w-5 h-5 text-amber-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
                Beri Review
            </h3>
            <form action="{{ route('routes.review.store', $route) }}" method="POST" class="space-y-4">
                @csrf
                <!-- Star Rating -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Rating</label>
                    <div class="flex gap-2" x-data="{ rating: 0 }">
                        @for($i = 1; $i <= 5; $i++)
                        <label class="cursor-pointer">
                            <input type="radio" name="rating" value="{{ $i }}" class="sr-only" required>
                            <svg class="w-8 h-8 text-slate-300 hover:text-yellow-400 transition-colors star-rating" data-value="{{ $i }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </label>
                        @endfor
                    </div>
                </div>

                <!-- Comment -->
                <div>
                    <label for="comment" class="block text-sm font-medium text-slate-700 mb-2">Komentar (opsional)</label>
                    <textarea name="comment" id="comment" rows="3"
                        class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        placeholder="Bagikan pengalaman pendakian Anda..."></textarea>
                </div>

                <button type="submit" class="px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white font-medium rounded-xl transition-colors">
                    Kirim Review
                </button>
            </form>
        </div>
        @else
        <div class="p-8 border-t border-slate-100 text-center">
            <p class="text-slate-600 mb-3">Login untuk memberikan review</p>
            <a href="{{ route('login') }}" class="inline-block px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl transition-colors">
                Login
            </a>
        </div>
        @endauth

        <!-- User Reviews -->
        @if($route->comments->count() > 0)
        <div class="p-8 border-t border-slate-100">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Review Pengguna ({{ $route->comments->count() }})</h3>
            <div class="space-y-4">
                @foreach($route->comments->take(5) as $comment)
                <div class="p-4 bg-slate-50 rounded-xl">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-semibold text-slate-700">{{ $comment->user->name ?? 'Anonim' }}</span>
                        <span class="text-xs text-slate-500">{{ $comment->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-slate-600">{{ $comment->content }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Similar Routes Section -->
@if($similarRoutes->count() > 0)
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
    <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-100">
        <div class="p-6 border-b border-slate-100">
            <h3 class="text-xl font-bold text-slate-800 flex items-center">
                <svg class="w-6 h-6 text-emerald-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Rekomendasi Rute Serupa (Cosine Similarity)
            </h3>
            <p class="text-sm text-slate-500 mt-1">Rute dengan karakteristik paling mirip berdasarkan SBERT embedding (384 dimensi)</p>
        </div>

        <div class="p-6">
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($similarRoutes as $similar)
                <a href="{{ route('routes.show', $similar) }}"
                   class="block bg-slate-50 hover:bg-emerald-50 rounded-xl transition-colors border border-slate-100 hover:border-emerald-200 overflow-hidden">
                    @if($similar->route_coordinates && count($similar->route_coordinates) > 0)
                    <div id="similar-map-{{ $similar->id }}" class="w-full h-20 bg-slate-200"></div>
                    @else
                    <div class="w-full h-20 bg-gradient-to-br from-emerald-100 to-teal-100 flex items-center justify-center">
                        <span class="text-2xl">🏔️</span>
                    </div>
                    @endif
                    <div class="p-3">
                        <div class="flex items-center justify-between mb-2">
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">{{ $similar->similarity_score }}%</span>
                            <span class="text-[10px] text-slate-500 font-mono bg-slate-100 px-1 rounded">cos={{ number_format($similar->similarity_score / 100, 3) }}</span>
                        </div>
                        <h4 class="font-semibold text-slate-800 text-sm truncate mb-1">{{ $similar->name }}</h4>
                        <div class="flex items-center justify-between text-xs text-slate-500">
                            <span>{{ $similar->formatted_distance }}</span>
                            <span class="px-1.5 py-0.5 rounded text-[10px] @if($similar->difficulty_level === 'Mudah') bg-green-100 text-green-700 @elseif($similar->difficulty_level === 'Sedang') bg-yellow-100 text-yellow-700 @else bg-orange-100 text-orange-700 @endif">{{ $similar->difficulty_level }}</span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>

            <!-- Formula Display -->
            <div class="mt-6 p-4 bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl border border-amber-200">
                <p class="font-mono text-center text-amber-800 mb-1">Sim(A, B) = (A · B) / (||A|| × ||B||)</p>
                <p class="text-xs text-amber-600 text-center">A = Embedding rute ini | B = Embedding rute lain | Range: -1 s/d 1 (1 = identik)</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    @foreach($similarRoutes as $similar)
    @if($similar->route_coordinates && count($similar->route_coordinates) > 0)
    (function() {
        var coords = @json($similar->route_coordinates);
        if (coords && coords.length > 0 && document.getElementById('similar-map-{{ $similar->id }}')) {
            var map = L.map('similar-map-{{ $similar->id }}', {
                zoomControl: false, dragging: false, scrollWheelZoom: false, doubleClickZoom: false, touchZoom: false
            });
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 18 }).addTo(map);
            var poly = L.polyline(coords, { color: '#10b981', weight: 3, opacity: 0.9 }).addTo(map);
            map.fitBounds(poly.getBounds(), { padding: [5, 5] });
        }
    })();
    @endif
    @endforeach
});
</script>
@endpush
@endif

@if($route->route_coordinates && count($route->route_coordinates) > 0)
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var coords = @json($route->route_coordinates);

    if (coords && coords.length > 0) {
        var map = L.map('route-map', {
            zoomControl: true,
            scrollWheelZoom: true
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // Draw route polyline
        var polyline = L.polyline(coords, {
            color: '#10b981',
            weight: 4,
            opacity: 0.9
        }).addTo(map);

        // Add start marker (green)
        L.circleMarker(coords[0], {
            radius: 8,
            fillColor: '#22c55e',
            color: '#fff',
            weight: 2,
            fillOpacity: 1
        }).addTo(map).bindPopup('🚩 Start');

        // Add end marker (red)
        L.circleMarker(coords[coords.length - 1], {
            radius: 8,
            fillColor: '#ef4444',
            color: '#fff',
            weight: 2,
            fillOpacity: 1
        }).addTo(map).bindPopup('🏁 Finish');

        map.fitBounds(polyline.getBounds(), { padding: [30, 30] });
    }
});
</script>
@endpush
@endif

@endsection
