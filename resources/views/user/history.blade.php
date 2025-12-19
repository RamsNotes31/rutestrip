@extends('layouts.app')

@section('title', 'Riwayat Pencarian')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">🔍 Riwayat Pencarian</h1>
            <p class="text-slate-600">Semua pencarian yang pernah Anda lakukan</p>
        </div>
        <a href="{{ route('user.dashboard') }}" class="text-emerald-600 hover:underline">← Kembali</a>
    </div>

    @if($histories->count() > 0)
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-slate-100">
        <table class="w-full">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">Query</th>
                    <th class="px-6 py-4 text-center text-sm font-semibold text-slate-600">Hasil</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-slate-600">Waktu</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($histories as $history)
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-4">
                        <a href="{{ route('search.index') }}?q={{ urlencode($history->query) }}"
                           class="text-emerald-600 hover:underline font-medium">
                            {{ $history->query }}
                        </a>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
                            {{ $history->results_count }} rute
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right text-sm text-slate-500">
                        {{ $history->created_at->diffForHumans() }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $histories->links() }}
    </div>
    @else
    <div class="text-center py-16 bg-white rounded-2xl shadow-lg">
        <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <p class="text-slate-500 mb-4">Belum ada riwayat pencarian</p>
        <a href="{{ route('search.index') }}" class="inline-block px-6 py-3 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700">
            Mulai Cari Rute
        </a>
    </div>
    @endif
</div>
@endsection
