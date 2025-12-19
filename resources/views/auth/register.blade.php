@extends('layouts.app')

@section('title', 'Daftar Akun')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <!-- Register Card -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
            <!-- Header -->
            <div class="bg-mountain-gradient p-8 text-center">
                <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-white mb-2">Daftar Akun</h1>
                <p class="text-white/80 text-sm">Buat akun untuk menyimpan rute favorit</p>
            </div>

            <!-- Form -->
            <div class="p-8">
                @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">
                    {{ session('success') }}
                </div>
                @endif

                <form method="POST" action="{{ route('register.submit') }}">
                    @csrf

                    <!-- Name -->
                    <div class="mb-5">
                        <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">Nama Lengkap</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all @error('name') border-red-500 @enderror"
                               placeholder="John Doe" required>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-5">
                        <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all @error('email') border-red-500 @enderror"
                               placeholder="email@example.com" required>
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-5">
                        <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">Password</label>
                        <input type="password" name="password" id="password"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all @error('password') border-red-500 @enderror"
                               placeholder="Minimal 6 karakter" required>
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-6">
                        <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-2">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all"
                               placeholder="Ulangi password" required>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                            class="w-full py-4 bg-mountain-gradient text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all">
                        Daftar Sekarang
                    </button>
                </form>

                <!-- Login Link -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-slate-500">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" class="text-emerald-600 hover:text-emerald-700 font-medium">
                            Masuk
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
