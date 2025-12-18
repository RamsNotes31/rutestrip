# RuteStrip - Sistem Rekomendasi Rute Pendakian Gunung

Sistem rekomendasi rute pendakian berbasis Content-Based Filtering menggunakan SBERT dan Cosine Similarity.

## Persyaratan

### PHP/Laravel

-   PHP 8.1+
-   Composer
-   Laravel 11

### Python

-   Python 3.8+
-   pip

## Instalasi

### 1. Install Dependensi PHP

```bash
composer install
```

### 2. Setup Database

```bash
# Salin file environment
cp .env.example .env

# Generate app key
php artisan key:generate

# Edit .env untuk konfigurasi database MySQL
# DB_CONNECTION=mysql
# DB_DATABASE=rutestrip
# DB_USERNAME=root
# DB_PASSWORD=

# Jalankan migrasi
php artisan migrate

# Buat symbolic link untuk storage
php artisan storage:link
```

### 3. Install Python Dependencies

```bash
cd python
pip install -r requirements.txt
```

> **Catatan**: Instalasi pertama akan mendownload model SBERT (~420MB).

### 4. Test Python Script

```bash
# Test mode ingest
python processor.py --mode=ingest --gpx=test_trail.gpx

# Output berupa JSON dengan statistik dan embedding
```

## Konfigurasi Python Path

Jika Python tidak ada di PATH sistem, tambahkan ke `.env`:

```
PYTHON_PATH=C:/Python311/python.exe
```

## Menjalankan Aplikasi

```bash
php artisan serve
```

Akses di browser: http://127.0.0.1:8000

## Fitur

-   **Upload GPX**: Admin dapat mengupload file GPX dari perangkat GPS
-   **Analisis Otomatis**: Ekstraksi jarak, elevasi, grade, waktu Naismith
-   **Pencarian AI**: User mencari dengan bahasa natural
-   **Rekomendasi Cerdas**: Hasil berdasarkan Cosine Similarity embedding SBERT

## Struktur Folder

```
rutestrip/
├── app/
│   ├── Http/Controllers/
│   │   ├── RouteController.php    # Upload & CRUD rute
│   │   └── SearchController.php   # Pencarian AI
│   ├── Models/
│   │   └── HikingRoute.php        # Model rute pendakian
│   └── Services/
│       └── PythonProcessorService.php  # Bridge ke Python
├── python/
│   ├── processor.py               # Script Python utama
│   ├── requirements.txt           # Dependensi Python
│   └── test_trail.gpx             # File GPX contoh
└── resources/views/
    ├── layouts/app.blade.php      # Layout utama
    ├── routes/                    # Views admin
    └── search/                    # Views pencarian
```

## Teknologi

-   **Backend**: Laravel 11
-   **Frontend**: Tailwind CSS
-   **AI/ML**: SBERT (paraphrase-multilingual-MiniLM-L12-v2)
-   **GPX Parser**: gpxpy
-   **Similarity**: scikit-learn (Cosine Similarity)
