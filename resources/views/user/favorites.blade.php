@extends('layouts.app')

@section('title', 'Rute Favorit')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">⭐ Rute Favorit</h1>
            <p class="text-slate-600">Rute yang Anda simpan untuk dilihat nanti</p>
        </div>
        <a href="{{ route('user.dashboard') }}" class="text-emerald-600 hover:underline">← Kembali</a>
    </div>

    @if($favorites->count() > 0)
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($favorites as $route)
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-slate-100">
            <div class="p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-2">{{ $route->name }}</h3>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                    @if($route->difficulty_level === 'Mudah') bg-green-100 text-green-800
                    @elseif($route->difficulty_level === 'Sedang') bg-yellow-100 text-yellow-800
                    @elseif($route->difficulty_level === 'Sulit') bg-orange-100 text-orange-800
                    @else bg-red-100 text-red-800
                    @endif">
                    {{ $route->difficulty_level }}
                </span>

                <div class="grid grid-cols-2 gap-4 mt-4 text-sm">
                    <div>
                        <p class="text-slate-500">Jarak</p>
                        <p class="font-semibold text-slate-800">{{ $route->formatted_distance }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500">Waktu</p>
                        <p class="font-semibold text-slate-800">{{ $route->formatted_duration }}</p>
                    </div>
                </div>

                <div class="flex gap-2 mt-4">
                    <a href="{{ route('routes.show', $route) }}"
                       class="flex-1 py-2 text-center bg-emerald-100 text-emerald-700 rounded-lg hover:bg-emerald-200 transition-colors text-sm font-medium">
                        Detail
                    </a>
                    <form action="{{ route('user.favorite.toggle', $route) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit"
                                class="w-full py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors text-sm font-medium">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $favorites->links() }}
    </div>
    @else
    <div class="text-center py-16">
        <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
        </svg>
        <p class="text-slate-500 mb-4">Belum ada rute favorit</p>
        <a href="{{ route('routes.index') }}" class="inline-block px-6 py-3 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700">
            Jelajahi Rute
        </a>
    </div>
    @endif
</div>
@endsection
