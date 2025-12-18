@extends('layouts.app')

@section('title', 'Batch Upload GPX')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <a href="{{ route('routes.index') }}" class="inline-flex items-center text-emerald-600 hover:text-emerald-700 mb-4 group">
            <svg class="w-5 h-5 mr-2 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Daftar Rute
        </a>

        <h1 class="text-3xl font-bold text-slate-800 mb-2">Batch Upload GPX</h1>
        <p class="text-slate-600">Upload beberapa file GPX sekaligus (maksimal 50 file).</p>
    </div>

    <!-- Upload Form -->
    <div class="bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">Batch Upload</h2>
                    <p class="text-indigo-100 text-sm">Pilih beberapa file GPX sekaligus</p>
                </div>
            </div>
        </div>

        <form action="{{ route('routes.batch.store') }}" method="POST" enctype="multipart/form-data" class="p-8" id="batchForm">
            @csrf

            <!-- GPX Files Upload -->
            <div class="mb-8">
                <label for="gpx_files" class="block text-sm font-semibold text-slate-700 mb-2">
                    File GPX <span class="text-red-500">*</span>
                </label>

                <div class="relative">
                    <input type="file"
                           name="gpx_files[]"
                           id="gpx_files"
                           accept=".gpx,.xml"
                           class="hidden"
                           multiple
                           required>

                    <label for="gpx_files"
                           class="flex flex-col items-center justify-center w-full h-56 border-2 border-dashed border-slate-300 rounded-2xl cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/50 transition-all @error('gpx_files') border-red-500 @enderror @error('gpx_files.*') border-red-500 @enderror"
                           id="dropzone">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6" id="dropzone-content">
                            <div class="w-20 h-20 bg-indigo-100 rounded-2xl flex items-center justify-center mb-4">
                                <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                            </div>
                            <p class="mb-2 text-sm text-slate-600">
                                <span class="font-semibold text-indigo-600">Klik untuk pilih file</span> atau drag & drop
                            </p>
                            <p class="text-xs text-slate-500">Pilih beberapa file GPX atau XML (Maks. 50 file, 10MB per file)</p>
                        </div>
                    </label>
                </div>

                @error('gpx_files')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @error('gpx_files.*')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Selected Files List -->
            <div class="mb-8 hidden" id="files-list-container">
                <h3 class="text-sm font-semibold text-slate-700 mb-3">File yang dipilih:</h3>
                <div class="bg-slate-50 rounded-xl p-4 max-h-64 overflow-y-auto" id="files-list">
                    <!-- Files will be listed here -->
                </div>
                <p class="mt-2 text-sm text-slate-600">
                    <span id="file-count" class="font-semibold text-indigo-600">0</span> file dipilih
                </p>
            </div>

            <!-- Info Box -->
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-8">
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-amber-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div class="text-sm text-amber-700">
                        <p class="font-semibold mb-1">Catatan Penting</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Nama rute akan diambil dari nama file (tanpa ekstensi)</li>
                            <li>Proses bisa memakan waktu beberapa menit tergantung jumlah file</li>
                            <li>File yang gagal diproses akan dilewati, yang berhasil tetap tersimpan</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Progress Section (hidden initially) -->
            <div class="hidden mb-8" id="progress-section">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-semibold text-slate-700">Mengupload...</span>
                    <span class="text-sm text-slate-600" id="progress-text">0%</span>
                </div>
                <div class="w-full bg-slate-200 rounded-full h-3 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-3 rounded-full transition-all duration-300" id="progress-bar" style="width: 0%"></div>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit"
                    id="submit-btn"
                    class="w-full py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all flex items-center justify-center space-x-2 hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                <span id="btn-text">Upload Semua File</span>
            </button>
        </form>
    </div>

    <!-- Failed Files Results (if any) -->
    @if(session('batch_results') && count(session('batch_results')['failed']) > 0)
    <div class="mt-8 bg-red-50 border border-red-200 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-red-700 mb-4">File yang Gagal Diproses:</h3>
        <div class="space-y-2">
            @foreach(session('batch_results')['failed'] as $failed)
            <div class="flex items-start space-x-3 bg-white p-3 rounded-lg">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="font-medium text-red-700">{{ $failed['file'] }}</p>
                    <p class="text-sm text-red-600">{{ $failed['error'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<script>
    const fileInput = document.getElementById('gpx_files');
    const dropzone = document.getElementById('dropzone');
    const dropzoneContent = document.getElementById('dropzone-content');
    const filesListContainer = document.getElementById('files-list-container');
    const filesList = document.getElementById('files-list');
    const fileCount = document.getElementById('file-count');
    const batchForm = document.getElementById('batchForm');
    const submitBtn = document.getElementById('submit-btn');
    const btnText = document.getElementById('btn-text');
    const progressSection = document.getElementById('progress-section');
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');

    // Handle file selection
    fileInput.addEventListener('change', function() {
        updateFileList(this.files);
    });

    function updateFileList(files) {
        if (files.length > 0) {
            filesListContainer.classList.remove('hidden');
            filesList.innerHTML = '';

            Array.from(files).forEach((file, index) => {
                const size = (file.size / 1024 / 1024).toFixed(2);
                const item = document.createElement('div');
                item.className = 'flex items-center justify-between py-2 px-3 bg-white rounded-lg mb-2 last:mb-0';
                item.innerHTML = `
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="text-sm text-slate-700 truncate max-w-xs">${file.name}</span>
                    </div>
                    <span class="text-xs text-slate-500">${size} MB</span>
                `;
                filesList.appendChild(item);
            });

            fileCount.textContent = files.length;

            // Update dropzone appearance
            dropzoneContent.innerHTML = `
                <div class="w-20 h-20 bg-green-100 rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <p class="mb-2 text-sm font-semibold text-green-600">${files.length} file terpilih</p>
                <p class="text-xs text-slate-500">Klik untuk mengubah pilihan</p>
            `;
        }
    }

    // Drag and drop
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, () => {
            dropzone.classList.add('border-indigo-400', 'bg-indigo-50');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, () => {
            dropzone.classList.remove('border-indigo-400', 'bg-indigo-50');
        }, false);
    });

    dropzone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        fileInput.files = files;
        updateFileList(files);
    }, false);

    // Form submission with loading state
    batchForm.addEventListener('submit', function() {
        submitBtn.disabled = true;
        btnText.textContent = 'Mengupload & Memproses...';
        progressSection.classList.remove('hidden');

        // Simulate progress (actual progress would require AJAX)
        let progress = 0;
        const interval = setInterval(() => {
            progress += Math.random() * 10;
            if (progress > 90) {
                progress = 90;
                clearInterval(interval);
            }
            progressBar.style.width = progress + '%';
            progressText.textContent = Math.round(progress) + '%';
        }, 500);
    });
</script>
@endsection
