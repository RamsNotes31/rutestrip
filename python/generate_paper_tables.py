#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script untuk menghasilkan Tabel dalam format Paper Akademik
Output: LaTeX, CSV, dan HTML
"""

import sys
import csv
import os

if sys.platform == 'win32':
    sys.stdout.reconfigure(encoding='utf-8')

OUTPUT_DIR = "output/paper_tables"

# Buat folder output
os.makedirs(OUTPUT_DIR, exist_ok=True)

# =============================================================================
# DATA TABEL
# =============================================================================

TABLES = {
    "tabel1_dataset": {
        "caption": "Ringkasan Dataset Jalur Pendakian",
        "headers": ["No", "Nama Jalur", "Gunung", "Provinsi", "Trackpoint"],
        "rows": [
            [1, "Argopuro - Bremi Baderan", "Argopuro", "Jawa Timur", 492],
            [2, "Agung - Pura Pasar Agung", "Agung", "Bali", 156],
            [3, "Agung - Besakih", "Agung", "Bali", 1247],
            [4, "Anak Krakatau", "Krakatau", "Lampung", 87],
            [5, "Arjuno - Lawang", "Arjuno", "Jawa Timur", 423],
            ["...", "...", "...", "...", "..."],
            [39, "Sumbing Via Butuh Kaliangkrik", "Sumbing", "Jawa Tengah", 298],
        ]
    },
    "tabel2_atribut_gpx": {
        "caption": "Atribut Hasil Ekstraksi Data GPX",
        "headers": ["Atribut", "Rumus/Metode", "Satuan", "Rentang Nilai"],
        "rows": [
            ["Jarak Tempuh", "gpx.length_3d()", "Kilometer", "3.85 - 114.51"],
            ["Kenaikan Elevasi", "Σ (ele_i - ele_{i-1})", "Meter", "0 - 8.120"],
            ["Durasi Naismith", "(Jarak/5) + (Elevasi/600)", "Jam", "0.55 - 29.26"],
            ["Grade Rata-rata", "(Elevasi/(Jarak×1000))×100", "Persen", "0.00 - 21.89"],
            ["Elevasi Minimum", "min(elevasi)", "mdpl", "773 - 2.200"],
            ["Elevasi Maksimum", "max(elevasi)", "mdpl", "2.350 - 3.371"],
        ]
    },
    "tabel3_konversi_narasi": {
        "caption": "Contoh Konversi Data Numerik ke Narasi",
        "headers": ["Atribut Numerik", "Nilai", "Narasi Otomatis"],
        "rows": [
            ["distance_km", "11.10", "Jalur pendakian dengan jarak panjang sekitar 11.10 km"],
            ["elevation_gain_m", "1.910", "Total kenaikan elevasi 1910 meter"],
            ["naismith_duration", "5.40", "Estimasi waktu tempuh lama selama 5.40 jam"],
            ["average_grade_pct", "17.21", "Karakteristik jalur sangat curam dengan grade 17.21%"],
            ["difficulty", "sulit", "Tingkat kesulitan: sulit"],
        ]
    },
    "tabel4_preprocessing": {
        "caption": "Hasil Setiap Tahap Preprocessing",
        "headers": ["Tahap", "Input", "Output"],
        "rows": [
            ["Original", "Jalur ini SANGAT curam!!! https://link.com", "-"],
            ["Data Cleaning", "(dari atas)", "Jalur ini SANGAT curam Cocok untuk pendaki"],
            ["Case Folding", "(dari atas)", "jalur ini sangat curam cocok untuk pendaki"],
            ["Stopword Removal", "(dari atas)", "jalur sangat curam cocok pendaki"],
        ]
    },
    "tabel5_distribusi_kata": {
        "caption": "Distribusi Kata pada Preprocessing",
        "headers": ["Kategori", "Jumlah Kata", "Contoh"],
        "rows": [
            ["Stopwords (dihapus)", 47, "yang, dan, di, ke, dari, ini, itu"],
            ["Kata Negasi (dipertahankan)", 5, "tidak, bukan, jangan, belum, tanpa"],
            ["Kata Sifat Krusial (dipertahankan)", 15, "mudah, sulit, curam, landai, pemula"],
        ]
    },
    "tabel6_statistik_preprocessing": {
        "caption": "Statistik Preprocessing pada Dataset",
        "headers": ["Metrik", "Sebelum Preprocessing", "Sesudah Preprocessing", "Perubahan"],
        "rows": [
            ["Total Kata", "2.847", "1.923", "-32.5%"],
            ["Kata Unik", "312", "198", "-36.5%"],
            ["Rata-rata Panjang Dokumen", "73 kata", "49 kata", "-32.9%"],
        ]
    },
    "tabel7_spesifikasi_sbert": {
        "caption": "Spesifikasi Model SBERT",
        "headers": ["Parameter", "Nilai"],
        "rows": [
            ["Nama Model", "paraphrase-multilingual-MiniLM-L12-v2"],
            ["Arsitektur", "Transformer (12 layers)"],
            ["Dimensi Embedding", "384"],
            ["Ukuran Model", "~420 MB"],
            ["Bahasa yang Didukung", "50+ bahasa (termasuk Indonesia)"],
            ["Max Sequence Length", "128 tokens"],
        ]
    },
    "tabel8_statistik_embedding": {
        "caption": "Statistik Embedding yang Dihasilkan",
        "headers": ["Metrik", "Nilai"],
        "rows": [
            ["Jumlah Dokumen", "39"],
            ["Dimensi Vektor", "384"],
            ["Waktu Encoding Total", "6.66 detik"],
            ["Rata-rata Waktu per Dokumen", "170.77 ms"],
            ["Rentang Nilai Vektor", "-1.0 hingga 1.0"],
        ]
    },
    "tabel9_query1": {
        "caption": "Hasil Rekomendasi untuk Query \"jalur mudah untuk pemula\"",
        "headers": ["Rank", "Nama Jalur", "Kesulitan", "Grade (%)", "Jarak (km)", "Skor Cosine", "Relevan"],
        "rows": [
            [1, "Agung - Pura Pasar Agung", "mudah", "0.00", "8.91", "0.4150", "✓"],
            [2, "Ijen - Sempol", "mudah", "3.33", "114.51", "0.4043", "✓"],
            [3, "Merbabu Via Selo", "sulit", "17.21", "11.10", "0.3904", "✗"],
            [4, "Lawu Via Cemoro Sewu", "sulit", "12.97", "26.88", "0.3894", "✗"],
            [5, "Merbabu Via Suwanting", "sulit", "16.50", "12.06", "0.3859", "✗"],
        ]
    },
    "tabel10_query2": {
        "caption": "Hasil Rekomendasi untuk Query \"trek menantang elevasi tinggi\"",
        "headers": ["Rank", "Nama Jalur", "Kesulitan", "Elevasi (m)", "Durasi (jam)", "Skor Cosine", "Relevan"],
        "rows": [
            [1, "Merbabu Via Thekelan", "sulit", "1.911", "5.94", "0.7778", "✓"],
            [2, "Semeru", "sedang", "3.142", "13.20", "0.7773", "✓"],
            [3, "Merbabu Via Selo", "sulit", "1.910", "5.40", "0.7750", "✓"],
            [4, "Merbabu Via Suwanting", "sulit", "1.989", "5.73", "0.7744", "✓"],
            [5, "Ciremai - Linggarjati", "sulit", "2.148", "7.52", "0.7702", "✓"],
        ]
    },
    "tabel11_query3": {
        "caption": "Hasil Rekomendasi untuk Query \"pendakian singkat 2-3 jam\"",
        "headers": ["Rank", "Nama Jalur", "Durasi (jam)", "Jarak (km)", "Grade (%)", "Skor Cosine", "Relevan"],
        "rows": [
            [1, "Argopuro Baderan", "14.96", "45.10", "7.91", "0.6891", "✗"],
            [2, "Argopuro - Bremi Baderan", "16.17", "53.83", "6.02", "0.6850", "✗"],
            [3, "Argopuro - Bremi", "16.17", "53.83", "6.02", "0.6850", "✗"],
            [4, "Sumbing Via Batursari", "6.59", "17.95", "21.89", "0.6785", "✗"],
            [5, "Sumbing Via Bowongso", "6.43", "17.55", "21.71", "0.6778", "✗"],
        ]
    },
    "tabel12_query4": {
        "caption": "Hasil Rekomendasi untuk Query \"gunung dengan sabana dan sunrise\"",
        "headers": ["Rank", "Nama Jalur", "Grade (%)", "Fitur Jalur", "Kesulitan", "Skor Cosine", "Relevan"],
        "rows": [
            [1, "Lawu Via Tambak", "12.15", "Sunrise spektakuler", "sulit", "0.5430", "✓"],
            [2, "Lawu Via Cetho", "10.10", "Sunrise sunset", "sulit", "0.5345", "✓"],
            [3, "Lawu Via Cemoro Sewu", "4.09", "Petilasan, sunrise", "sulit", "0.5309", "✓"],
            [4, "Ijen - Sempol", "3.33", "Kawah, api biru", "mudah", "0.4847", "✓"],
            [5, "Merbabu Via Selo", "17.21", "Sabana luas, Merapi", "sulit", "0.4774", "✓"],
        ]
    },
    "tabel13_precision": {
        "caption": "Hasil Evaluasi Precision@K",
        "headers": ["Skenario", "Query", "P@3", "P@5"],
        "rows": [
            [1, "jalur mudah untuk pemula", "0.67", "0.40"],
            [2, "trek menantang elevasi tinggi", "1.00", "1.00"],
            [3, "pendakian singkat 2-3 jam", "0.00", "0.00"],
            [4, "gunung dengan sabana dan sunrise", "1.00", "1.00"],
            ["", "Rata-rata", "0.67", "0.60"],
        ]
    },
    "tabel14_perbandingan": {
        "caption": "Perbandingan Precision@5: SBERT vs TF-IDF",
        "headers": ["Skenario", "SBERT", "TF-IDF", "Selisih"],
        "rows": [
            [1, "0.40", "0.20", "+0.20"],
            [2, "1.00", "0.60", "+0.40"],
            [3, "0.00", "0.00", "0.00"],
            [4, "1.00", "0.20", "+0.80"],
            ["", "Rata-rata: 0.60", "0.25", "+0.35 (+140%)"],
        ]
    },
    "tabel17_performa": {
        "caption": "Hasil Pengukuran Performa Sistem",
        "headers": ["Metrik", "Nilai", "Benchmark"],
        "rows": [
            ["Response Time (Pencarian)", "127-150 ms", "< 500 ms ✓"],
            ["Response Time (Detail)", "85-120 ms", "< 300 ms ✓"],
            ["Memory Usage (Peak)", "256 MB", "< 512 MB ✓"],
            ["SBERT Model Loading", "9.24 detik", "One-time"],
            ["Throughput", "50 req/menit", "Acceptable"],
        ]
    },
}

# =============================================================================
# GENERATOR FUNCTIONS
# =============================================================================

def generate_latex(table_id, data):
    """Generate tabel dalam format LaTeX"""
    headers = data["headers"]
    rows = data["rows"]
    caption = data["caption"]
    
    # Column format
    col_format = "|" + "|".join(["l"] * len(headers)) + "|"
    
    latex = []
    latex.append(f"% {table_id}")
    latex.append("\\begin{table}[htbp]")
    latex.append("\\centering")
    latex.append(f"\\caption{{{caption}}}")
    latex.append(f"\\label{{tab:{table_id}}}")
    latex.append(f"\\begin{{tabular}}{{{col_format}}}")
    latex.append("\\hline")
    latex.append(" & ".join([f"\\textbf{{{h}}}" for h in headers]) + " \\\\")
    latex.append("\\hline")
    
    for row in rows:
        latex.append(" & ".join([str(cell) for cell in row]) + " \\\\")
    
    latex.append("\\hline")
    latex.append("\\end{tabular}")
    latex.append("\\end{table}")
    latex.append("")
    
    return "\n".join(latex)

def generate_csv(table_id, data):
    """Generate tabel dalam format CSV"""
    filepath = f"{OUTPUT_DIR}/{table_id}.csv"
    with open(filepath, 'w', newline='', encoding='utf-8') as f:
        writer = csv.writer(f)
        writer.writerow(data["headers"])
        writer.writerows(data["rows"])
    return filepath

def generate_html(table_id, data):
    """Generate tabel dalam format HTML (untuk Word copy-paste)"""
    headers = data["headers"]
    rows = data["rows"]
    caption = data["caption"]
    
    html = []
    html.append(f"<!-- {table_id} -->")
    html.append("<table border='1' cellpadding='5' cellspacing='0' style='border-collapse: collapse; font-family: Times New Roman; font-size: 11pt;'>")
    html.append(f"<caption><strong>Tabel. {caption}</strong></caption>")
    html.append("<thead>")
    html.append("<tr style='background-color: #f0f0f0;'>")
    for h in headers:
        html.append(f"  <th style='text-align: center; font-weight: bold;'>{h}</th>")
    html.append("</tr>")
    html.append("</thead>")
    html.append("<tbody>")
    
    for row in rows:
        html.append("<tr>")
        for cell in row:
            html.append(f"  <td style='text-align: left;'>{cell}</td>")
        html.append("</tr>")
    
    html.append("</tbody>")
    html.append("</table>")
    html.append("<br>")
    html.append("")
    
    return "\n".join(html)

def generate_word_table(table_id, data):
    """Generate tabel dalam format tab-separated (untuk paste ke Word)"""
    headers = data["headers"]
    rows = data["rows"]
    
    lines = []
    lines.append("\t".join([str(h) for h in headers]))
    for row in rows:
        lines.append("\t".join([str(cell) for cell in row]))
    
    return "\n".join(lines)

# =============================================================================
# MAIN
# =============================================================================

if __name__ == '__main__':
    print("\n" + "=" * 70)
    print("  GENERATOR TABEL PAPER AKADEMIK")
    print("  Format: LaTeX, CSV, HTML, Word Tab-Separated")
    print("=" * 70)
    
    # Generate all formats
    all_latex = []
    all_html = []
    all_word = []
    
    for table_id, data in TABLES.items():
        # LaTeX
        all_latex.append(generate_latex(table_id, data))
        
        # CSV
        csv_path = generate_csv(table_id, data)
        print(f"✅ CSV: {csv_path}")
        
        # HTML
        all_html.append(generate_html(table_id, data))
        
        # Word
        all_word.append(f"=== {data['caption']} ===")
        all_word.append(generate_word_table(table_id, data))
        all_word.append("")
    
    # Save LaTeX file
    latex_path = f"{OUTPUT_DIR}/all_tables.tex"
    with open(latex_path, 'w', encoding='utf-8') as f:
        f.write("\n".join(all_latex))
    print(f"✅ LaTeX: {latex_path}")
    
    # Save HTML file
    html_path = f"{OUTPUT_DIR}/all_tables.html"
    with open(html_path, 'w', encoding='utf-8') as f:
        f.write("<html><head><meta charset='utf-8'></head><body>\n")
        f.write("\n".join(all_html))
        f.write("</body></html>")
    print(f"✅ HTML: {html_path}")
    
    # Save Word-friendly file
    word_path = f"{OUTPUT_DIR}/all_tables_word.txt"
    with open(word_path, 'w', encoding='utf-8') as f:
        f.write("\n".join(all_word))
    print(f"✅ Word (tab-separated): {word_path}")
    
    print("\n" + "=" * 70)
    print("  INSTRUKSI PENGGUNAAN:")
    print("=" * 70)
    print("""
  📄 LaTeX:
     - Copy isi file all_tables.tex ke dokumen LaTeX Anda
     - Pastikan package \\usepackage{{tabular}} sudah di-include
  
  📊 CSV:
     - Buka file CSV dengan Excel
     - Copy-paste ke Word sebagai tabel
  
  🌐 HTML:
     - Buka file all_tables.html di browser
     - Select tabel → Copy → Paste ke Word (format terjaga)
  
  📝 Word Tab-Separated:
     - Buka file all_tables_word.txt
     - Copy semua → Paste ke Word
     - Select text → Insert → Table → Convert Text to Table
     - Pilih "Tabs" sebagai separator
""")
    print("=" * 70)
    print(f"\n📁 Semua file tersimpan di: {OUTPUT_DIR}/")
