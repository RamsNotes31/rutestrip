#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script untuk menampilkan Tabel dan Data Hasil Pembahasan
Sesuai dengan format paper akademik
"""

import sys
import json
import os

if sys.platform == 'win32':
    sys.stdout.reconfigure(encoding='utf-8')

# =============================================================================
# FUNGSI HELPER
# =============================================================================

def print_header(title):
    """Print header dengan border"""
    width = 80
    print("\n" + "=" * width)
    print(f"  {title}")
    print("=" * width)

def print_table(headers, rows, title=None):
    """Print tabel dengan format yang rapi"""
    if title:
        print(f"\n{title}\n")
    
    # Hitung lebar kolom
    col_widths = []
    for i, h in enumerate(headers):
        max_width = len(str(h))
        for row in rows:
            if i < len(row):
                max_width = max(max_width, len(str(row[i])))
        col_widths.append(max_width + 2)
    
    # Print header
    header_line = "| " + " | ".join(str(h).ljust(col_widths[i]) for i, h in enumerate(headers)) + " |"
    separator = "|" + "|".join("-" * (w + 2) for w in col_widths) + "|"
    
    print(separator)
    print(header_line)
    print(separator)
    
    # Print rows
    for row in rows:
        row_line = "| " + " | ".join(str(row[i] if i < len(row) else "").ljust(col_widths[i]) for i in range(len(headers))) + " |"
        print(row_line)
    print(separator)

# =============================================================================
# TABEL 1: RINGKASAN DATASET JALUR PENDAKIAN
# =============================================================================

def tabel_1_dataset():
    print_header("TABEL 1. Ringkasan Dataset Jalur Pendakian")
    
    headers = ["No", "Nama Jalur", "Gunung", "Provinsi", "Trackpoint"]
    rows = [
        [1, "Argopuro - Bremi Baderan", "Argopuro", "Jawa Timur", 492],
        [2, "Agung - Pura Pasar Agung", "Agung", "Bali", 156],
        [3, "Agung - Besakih", "Agung", "Bali", 1247],
        [4, "Anak Krakatau", "Krakatau", "Lampung", 87],
        [5, "Arjuno - Lawang", "Arjuno", "Jawa Timur", 423],
        ["...", "...", "...", "...", "..."],
        [39, "Sumbing Via Butuh", "Sumbing", "Jawa Tengah", 298],
    ]
    print_table(headers, rows)
    print("\n📊 Total Dataset: 39 jalur pendakian dari 18 gunung berbeda")

# =============================================================================
# TABEL 2: ATRIBUT HASIL EKSTRAKSI GPX
# =============================================================================

def tabel_2_atribut_gpx():
    print_header("TABEL 2. Atribut Hasil Ekstraksi Data GPX")
    
    headers = ["Atribut", "Rumus/Metode", "Satuan", "Rentang"]
    rows = [
        ["Jarak Tempuh", "gpx.length_3d()", "Kilometer", "3.85 - 114.51"],
        ["Kenaikan Elevasi", "Σ (ele_i - ele_{i-1})", "Meter", "0 - 8,120"],
        ["Durasi Naismith", "(Jarak/5) + (Elevasi/600)", "Jam", "0.55 - 29.26"],
        ["Grade Rata-rata", "(Elevasi/(Jarak×1000))×100", "Persen", "0.00 - 21.89"],
        ["Elevasi Minimum", "min(elevasi)", "mdpl", "773 - 2,200"],
        ["Elevasi Maksimum", "max(elevasi)", "mdpl", "2,350 - 3,371"],
    ]
    print_table(headers, rows)

# =============================================================================
# TABEL 3: CONTOH KONVERSI DATA NUMERIK KE NARASI
# =============================================================================

def tabel_3_konversi():
    print_header("TABEL 3. Contoh Konversi Data Numerik ke Narasi")
    
    headers = ["Atribut Numerik", "Nilai", "Narasi Otomatis"]
    rows = [
        ["distance_km", "11.10", "Jalur pendakian dengan jarak panjang sekitar 11.10 km"],
        ["elevation_gain_m", "1,910", "Total kenaikan elevasi 1910 meter"],
        ["naismith_duration", "5.40", "Estimasi waktu tempuh lama selama 5.40 jam"],
        ["average_grade_pct", "17.21", "Karakteristik jalur sangat curam dengan grade 17.21%"],
        ["difficulty", "sulit", "Tingkat kesulitan: sulit. Cocok untuk berpengalaman"],
    ]
    print_table(headers, rows)
    
    print("\n📝 Contoh Narasi Gabungan (Fusi Data):")
    print("─" * 75)
    print("""
  Deskripsi Manual:
  "Jalur dengan sabana luas dan pemandangan sunrise spektakuler. 
   Padang rumput hijau sepanjang perjalanan dengan view Gunung Merapi."
  
  + Narasi Otomatis dari GPX:
  "Jalur pendakian dengan jarak panjang sekitar 11.10 kilometer. 
   Estimasi waktu tempuh lama selama 5.40 jam. Karakteristik jalur 
   sangat curam dengan grade rata-rata 17.21%. Total kenaikan elevasi 
   1910 meter. Tingkat kesulitan: sulit."
  
  = NARASI GABUNGAN (untuk input SBERT)
""")
    print("─" * 75)

# =============================================================================
# TABEL 4: HASIL SETIAP TAHAP PREPROCESSING
# =============================================================================

def tabel_4_preprocessing_steps():
    print_header("TABEL 4. Hasil Setiap Tahap Preprocessing")
    
    headers = ["Tahap", "Input", "Output"]
    rows = [
        ["Original", "Jalur ini SANGAT curam!!! https://link.com", "-"],
        ["Data Cleaning", "↓", "Jalur ini SANGAT curam Cocok untuk pendaki"],
        ["Case Folding", "↓", "jalur ini sangat curam cocok untuk pendaki"],
        ["Stopword Removal", "↓", "jalur sangat curam cocok pendaki"],
    ]
    print_table(headers, rows)
    
    print("\n📌 Catatan Preprocessing:")
    print("   • URL dihapus dengan regex: r'https?://\\S+|www\\.\\S+'")
    print("   • Karakter spesial dihapus: r'[^\\w\\s\\-]'")
    print("   • Case folding: text.lower()")
    print("   • Stopword removal: selektif (mempertahankan kata penting)")

# =============================================================================
# TABEL 5: DISTRIBUSI KATA PADA PREPROCESSING
# =============================================================================

def tabel_5_distribusi_kata():
    print_header("TABEL 5. Distribusi Kata pada Preprocessing")
    
    headers = ["Kategori", "Jumlah", "Contoh Kata"]
    rows = [
        ["Stopwords (dihapus)", "47", "yang, dan, di, ke, dari, ini, itu"],
        ["Kata Negasi (dipertahankan)", "5", "tidak, bukan, jangan, belum, tanpa"],
        ["Kata Sifat Krusial (dipertahankan)", "15", "mudah, sulit, curam, landai, pemula"],
    ]
    print_table(headers, rows)
    
    print("\n📋 Daftar Lengkap Kata yang Dipertahankan:")
    print("─" * 60)
    print("   PRESERVE_WORDS = {")
    print("       'tidak', 'bukan', 'jangan', 'belum', 'tanpa',  # negasi")
    print("       'mudah', 'sulit', 'curam', 'landai',           # sifat jalur")
    print("       'panjang', 'pendek', 'tinggi', 'rendah',       # ukuran")
    print("       'sejuk', 'panas', 'dingin', 'indah', 'bagus',  # sifat lain")
    print("       'pemula', 'berpengalaman', 'santai',           # level")
    print("       'menantang', 'ekstrem'                         # difficulty")
    print("   }")
    print("─" * 60)

# =============================================================================
# TABEL 6: STATISTIK PREPROCESSING
# =============================================================================

def tabel_6_preprocessing():
    print_header("TABEL 6. Statistik Preprocessing pada Dataset")
    
    headers = ["Metrik", "Sebelum", "Sesudah", "Perubahan"]
    rows = [
        ["Total Kata", "2,847", "1,923", "-32.5%"],
        ["Kata Unik", "312", "198", "-36.5%"],
        ["Rata-rata Panjang Dokumen", "73 kata", "49 kata", "-32.9%"],
    ]
    print_table(headers, rows)

# =============================================================================
# TABEL 7: SPESIFIKASI MODEL SBERT
# =============================================================================

def tabel_7_sbert():
    print_header("TABEL 7. Spesifikasi Model SBERT")
    
    headers = ["Parameter", "Nilai"]
    rows = [
        ["Nama Model", "paraphrase-multilingual-MiniLM-L12-v2"],
        ["Arsitektur", "Transformer (12 layers)"],
        ["Dimensi Embedding", "384"],
        ["Ukuran Model", "~420 MB"],
        ["Bahasa Didukung", "50+ (termasuk Indonesia)"],
        ["Max Sequence Length", "128 tokens"],
        ["Training Data", "50M+ sentence pairs"],
    ]
    print_table(headers, rows)

# =============================================================================
# TABEL 8: STATISTIK EMBEDDING
# =============================================================================

def tabel_8_embedding():
    print_header("TABEL 8. Statistik Embedding yang Dihasilkan")
    
    headers = ["Metrik", "Nilai"]
    rows = [
        ["Jumlah Dokumen", "39"],
        ["Dimensi Vektor", "384"],
        ["Waktu Encoding Total", "6.66 detik"],
        ["Rata-rata per Dokumen", "170.77 ms"],
        ["Rentang Nilai Vektor", "-1.0 hingga 1.0"],
        ["Model Loading Time", "9.24 detik"],
    ]
    print_table(headers, rows)

# =============================================================================
# TABEL 9-12: HASIL REKOMENDASI PER QUERY
# =============================================================================

def tabel_9_12_rekomendasi():
    print_header("TABEL 9-12. Hasil Rekomendasi untuk 4 Skenario Query")
    
    # Query 1
    print("\n📌 QUERY 1: \"jalur mudah untuk pemula\"")
    print("   Expected: Jalur dengan kesulitan mudah\n")
    headers = ["Rank", "Nama Jalur", "Kesulitan", "Grade%", "Jarak(km)", "Skor", "Relevan"]
    rows = [
        [1, "Agung - Pura Pasar Agung", "mudah", "0.00", "8.91", "0.4150", "✓"],
        [2, "Ijen - Sempol", "mudah", "3.33", "114.51", "0.4043", "✓"],
        [3, "Merbabu Via Selo", "sulit", "17.21", "11.10", "0.3904", "✗"],
        [4, "Lawu Via Cemoro Sewu", "sulit", "12.97", "26.88", "0.3894", "✗"],
        [5, "Merbabu Via Suwanting", "sulit", "16.50", "12.06", "0.3859", "✗"],
    ]
    print_table(headers, rows)
    
    # Query 2
    print("\n📌 QUERY 2: \"trek menantang elevasi tinggi\"")
    print("   Expected: Jalur sulit dengan elevasi tinggi\n")
    headers = ["Rank", "Nama Jalur", "Kesulitan", "Elevasi(m)", "Durasi", "Skor", "Relevan"]
    rows = [
        [1, "Merbabu Via Thekelan", "sulit", "1,911", "5.94 jam", "0.7778", "✓"],
        [2, "Semeru", "sedang", "3,142", "13.20 jam", "0.7773", "✓"],
        [3, "Merbabu Via Selo", "sulit", "1,910", "5.40 jam", "0.7750", "✓"],
        [4, "Merbabu Via Suwanting", "sulit", "1,989", "5.73 jam", "0.7744", "✓"],
        [5, "Ciremai - Linggarjati", "sulit", "2,148", "7.52 jam", "0.7702", "✓"],
    ]
    print_table(headers, rows)
    
    # Query 3
    print("\n📌 QUERY 3: \"pendakian singkat 2-3 jam\"")
    print("   Expected: Jalur dengan durasi pendek\n")
    headers = ["Rank", "Nama Jalur", "Durasi", "Jarak(km)", "Grade%", "Skor", "Relevan"]
    rows = [
        [1, "Argopuro Baderan", "14.96 jam", "45.10", "7.91", "0.6891", "✗"],
        [2, "Argopuro - Bremi Baderan", "16.17 jam", "53.83", "6.02", "0.6850", "✗"],
        [3, "Argopuro - Bremi", "16.17 jam", "53.83", "6.02", "0.6850", "✗"],
        [4, "Sumbing Via Batursari", "6.59 jam", "17.95", "21.89", "0.6785", "✗"],
        [5, "Sumbing Via Bowongso", "6.43 jam", "17.55", "21.71", "0.6778", "✗"],
    ]
    print_table(headers, rows)
    
    # Query 4
    print("\n📌 QUERY 4: \"gunung dengan sabana dan sunrise\"")
    print("   Expected: Jalur dengan pemandangan sabana/sunrise\n")
    headers = ["Rank", "Nama Jalur", "Grade%", "Fitur Jalur", "Kesulitan", "Skor", "Relevan"]
    rows = [
        [1, "Lawu Via Tambak", "12.15", "Sunrise spektakuler", "sulit", "0.5430", "✓"],
        [2, "Lawu Via Cetho", "10.10", "Sunrise sunset", "sulit", "0.5345", "✓"],
        [3, "Lawu Via Cemoro Sewu", "4.09", "Petilasan, sunrise", "sulit", "0.5309", "✓"],
        [4, "Ijen - Sempol", "3.33", "Kawah, api biru", "mudah", "0.4847", "✓"],
        [5, "Merbabu Via Selo", "17.21", "Sabana luas, Merapi", "sulit", "0.4774", "✓"],
    ]
    print_table(headers, rows)

# =============================================================================
# TABEL 13: PRECISION@K
# =============================================================================

def tabel_13_precision():
    print_header("TABEL 13. Hasil Evaluasi Precision@K")
    
    headers = ["Skenario", "Query", "P@3", "P@5"]
    rows = [
        [1, "jalur mudah untuk pemula", "0.67", "0.40"],
        [2, "trek menantang elevasi tinggi", "1.00", "1.00"],
        [3, "pendakian singkat 2-3 jam", "0.00", "0.00"],
        [4, "gunung dengan sabana dan sunrise", "1.00", "1.00"],
        ["", "RATA-RATA", "0.67", "0.60"],
    ]
    print_table(headers, rows)

# =============================================================================
# TABEL 14: SBERT VS TF-IDF
# =============================================================================

def tabel_14_perbandingan():
    print_header("TABEL 14. Perbandingan Precision@5: SBERT vs TF-IDF")
    
    headers = ["Skenario", "SBERT", "TF-IDF", "Selisih"]
    rows = [
        [1, "0.40", "0.20", "+0.20"],
        [2, "1.00", "0.60", "+0.40"],
        [3, "0.00", "0.00", "0.00"],
        [4, "1.00", "0.20", "+0.80"],
        ["", "RATA-RATA: 0.60", "0.25", "+0.35 (+140%)"],
    ]
    print_table(headers, rows)

# =============================================================================
# TABEL 17: PERFORMA SISTEM
# =============================================================================

def tabel_17_performa():
    print_header("TABEL 17. Hasil Pengukuran Performa Sistem")
    
    headers = ["Metrik", "Nilai", "Benchmark"]
    rows = [
        ["Response Time (Pencarian)", "127-150 ms", "< 500 ms ✓"],
        ["Response Time (Detail)", "85-120 ms", "< 300 ms ✓"],
        ["Memory Usage (Peak)", "256 MB", "< 512 MB ✓"],
        ["SBERT Model Loading", "9.24 detik", "One-time"],
        ["Throughput", "50 req/menit", "Acceptable"],
    ]
    print_table(headers, rows)

# =============================================================================
# RINGKASAN AKHIR
# =============================================================================

def ringkasan():
    print_header("RINGKASAN HASIL PENELITIAN")
    
    print("""
┌─────────────────────────────────────────────────────────────────────────────┐
│                        HASIL PENELITIAN UTAMA                               │
├─────────────────────────────────────────────────────────────────────────────┤
│  📊 Dataset           : 39 jalur pendakian dari 18 gunung                   │
│  🧠 Model             : SBERT paraphrase-multilingual-MiniLM-L12-v2         │
│  📐 Dimensi Embedding : 384                                                 │
│  ⏱️  Waktu Encoding   : 6.66 detik (untuk 39 dokumen)                       │
│  🎯 Precision@5       : 0.60 (SBERT) vs 0.25 (TF-IDF)                       │
│  📈 Peningkatan       : +140% dibanding TF-IDF                              │
│  ⚡ Response Time     : 127-150 ms                                          │
├─────────────────────────────────────────────────────────────────────────────┤
│                           QUERY TERBAIK                                     │
├─────────────────────────────────────────────────────────────────────────────┤
│  1. "trek menantang elevasi tinggi"    → Precision@5 = 1.00, Skor = 0.7778  │
│  2. "gunung dengan sabana dan sunrise" → Precision@5 = 1.00, Skor = 0.5430  │
├─────────────────────────────────────────────────────────────────────────────┤
│                         LIMITASI DITEMUKAN                                  │
├─────────────────────────────────────────────────────────────────────────────┤
│  ⚠️ Query "pendakian singkat 2-3 jam"  → Precision@5 = 0.00                 │
│     (Sistem belum optimal untuk constraint numerik spesifik)                │
└─────────────────────────────────────────────────────────────────────────────┘
""")

# =============================================================================
# MAIN
# =============================================================================

if __name__ == '__main__':
    print("\n" + "█" * 80)
    print("█" + " " * 78 + "█")
    print("█" + "     OUTPUT TABEL DAN DATA - HASIL PEMBAHASAN PENELITIAN".center(78) + "█")
    print("█" + "     Sistem Rekomendasi Jalur Pendakian Berbasis SBERT".center(78) + "█")
    print("█" + " " * 78 + "█")
    print("█" * 80)
    
    # Output semua tabel
    tabel_1_dataset()
    tabel_2_atribut_gpx()
    tabel_3_konversi()
    tabel_4_preprocessing_steps()
    tabel_5_distribusi_kata()
    tabel_6_preprocessing()
    tabel_7_sbert()
    tabel_8_embedding()
    tabel_9_12_rekomendasi()
    tabel_13_precision()
    tabel_14_perbandingan()
    tabel_17_performa()
    ringkasan()
    
    print("\n✅ Semua tabel berhasil di-output!")
    print("📁 Data sesuai dengan: docs/hasil_pembahasan_lengkap.md\n")
