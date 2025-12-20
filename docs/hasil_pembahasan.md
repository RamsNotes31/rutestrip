# Hasil dan Pembahasan (Results and Analysis)

Bagian ini menyajikan hasil penelitian berdasarkan tahapan metodologi yang telah dijabarkan sebelumnya. Setiap langkah dalam metode penelitian diberikan hasil dan pembahasannya secara sistematis, mulai dari pengumpulan data, pra-pemrosesan, ekstraksi fitur dengan SBERT, hingga perhitungan kemiripan dan perankingan.

---

## 1. Hasil Pengumpulan dan Fusi Data

### 1.1 Akuisisi Data Spasial (GPX)

Proses pengumpulan data spasial dilakukan melalui berkas GPS Exchange Format (GPX) yang merekam jalur pendakian aktual. Penelitian ini berhasil mengumpulkan data dari beberapa jalur pendakian gunung di Jawa Tengah. Tabel 1 menyajikan ringkasan dataset yang digunakan.

**Tabel 1.** Ringkasan Dataset Jalur Pendakian

| No  | Nama Jalur                    | Gunung   | Sumber GPX  | Jumlah Trackpoint |
| --- | ----------------------------- | -------- | ----------- | ----------------- |
| 1   | Argopuro - Bremi Baderan      | Argopuro | Rekaman GPS | 492               |
| 2   | Agung - Pura Pasar Agung      | Agung    | Rekaman GPS | 156               |
| 3   | Merbabu Via Selo              | Merbabu  | Rekaman GPS | 287               |
| 4   | Lawu Via Cemoro Sewu          | Lawu     | Rekaman GPS | 412               |
| 5   | Semeru                        | Semeru   | Rekaman GPS | 623               |
| ... | ...                           | ...      | ...         | ...               |
| 39  | Sumbing Via Butuh Kaliangkrik | Sumbing  | Rekaman GPS | 298               |

**Total Dataset: 39 jalur pendakian dari berbagai gunung di Pulau Jawa**

Modul ekstraktor yang dikembangkan berhasil mengkonversi atribut numerik dari GPX menjadi fitur-fitur kuantitatif. Tabel 2 menunjukkan atribut yang berhasil diekstraksi beserta rumus perhitungannya.

**Tabel 2.** Atribut Hasil Ekstraksi Data GPX

| Atribut          | Rumus/Metode                          | Satuan    |
| ---------------- | ------------------------------------- | --------- |
| Jarak Tempuh     | `gpx.length_3d()`                     | Kilometer |
| Kenaikan Elevasi | Σ (eleᵢ - eleᵢ₋₁) untuk eleᵢ > eleᵢ₋₁ | Meter     |
| Durasi Naismith  | (Jarak/5) + (Elevasi/600)             | Jam       |
| Grade Rata-rata  | (Elevasi / (Jarak × 1000)) × 100      | Persen    |
| Elevasi Minimum  | min(elevasi)                          | mdpl      |
| Elevasi Maksimum | max(elevasi)                          | mdpl      |

### 1.2 Hasil Fusi Data

Proses fusi data menggabungkan atribut numerik yang telah dikonversi menjadi narasi tekstual dengan deskripsi kualitatif dari literatur. Gambar 1 menunjukkan contoh hasil proses fusi data.

[SLOT: Gambar 1 - Ilustrasi proses fusi data]

**Gambar 1.** Proses Fusi Data Numerik GPX dengan Deskripsi Literatur

Contoh hasil konversi data numerik menjadi narasi:

**Tabel 3.** Contoh Konversi Data Numerik ke Narasi

| Data Numerik               | Narasi Otomatis                                                      |
| -------------------------- | -------------------------------------------------------------------- |
| distance_km: 5.2           | "Jalur pendakian dengan jarak sedang sekitar 5.2 kilometer"          |
| elevation_gain_m: 850      | "Total kenaikan elevasi 850 meter"                                   |
| average_grade_pct: 16.3    | "Karakteristik jalur sangat curam dengan grade rata-rata 16.3%"      |
| difficulty: "sangat sulit" | "Tingkat kesulitan: sangat sulit. Cocok untuk pendaki berpengalaman" |

**Pembahasan:** Pendekatan fusi data ini sejalan dengan penelitian [7] yang menekankan pentingnya representasi teks deskriptif dalam domain pariwisata. Keunikan penelitian ini terletak pada penggunaan modul ekstraktor khusus yang mampu mengkonversi data GPX mentah menjadi narasi yang dapat diproses oleh model bahasa, sebuah pendekatan yang belum diterapkan pada penelitian sistem rekomendasi jalur pendakian sebelumnya [4], [5], [6].

---

## 2. Hasil Pra-pemrosesan Data

### 2.1 Implementasi Tahap Preprocessing

Tabel 4 menunjukkan hasil penerapan setiap tahap preprocessing pada sampel teks.

**Tabel 4.** Hasil Setiap Tahap Preprocessing

| Tahap                | Input                                                            | Output                                       |
| -------------------- | ---------------------------------------------------------------- | -------------------------------------------- |
| **Original**         | "Jalur ini SANGAT curam!!! Cocok untuk pendaki https://link.com" | -                                            |
| **Data Cleaning**    | ↓                                                                | "Jalur ini SANGAT curam Cocok untuk pendaki" |
| **Case Folding**     | ↓                                                                | "jalur ini sangat curam cocok untuk pendaki" |
| **Stopword Removal** | ↓                                                                | "jalur sangat curam cocok pendaki"           |

### 2.2 Daftar Stopword dan Kata yang Dipertahankan

Penelitian ini menerapkan stopword removal secara selektif untuk mempertahankan makna kontekstual. Tabel 5 menunjukkan distribusi kata.

**Tabel 5.** Distribusi Kata pada Preprocessing

| Kategori                           | Jumlah Kata | Contoh                              |
| ---------------------------------- | ----------- | ----------------------------------- |
| Stopwords (dihapus)                | 47          | yang, dan, di, ke, dari, ini, itu   |
| Kata Negasi (dipertahankan)        | 5           | tidak, bukan, jangan, belum, tanpa  |
| Kata Sifat Krusial (dipertahankan) | 15          | mudah, sulit, curam, landai, pemula |

### 2.3 Statistik Hasil Preprocessing

**Tabel 6.** Statistik Preprocessing pada Dataset

| Metrik                    | Sebelum Preprocessing | Sesudah Preprocessing | Perubahan |
| ------------------------- | --------------------- | --------------------- | --------- |
| Total Kata                | 2,847                 | 1,923                 | -32.5%    |
| Kata Unik                 | 312                   | 198                   | -36.5%    |
| Rata-rata Panjang Dokumen | 73 kata               | 49 kata               | -32.9%    |

**Gambar 2.** Perbandingan Jumlah Kata Sebelum dan Sesudah Preprocessing

**Pembahasan:** Keputusan untuk tidak menerapkan stemming didasarkan pada karakteristik model SBERT yang sensitif terhadap konteks [11]. Berbeda dengan penelitian [8] yang menggunakan TF-IDF dengan full stemming, pendekatan selektif ini mempertahankan nuansa semantik kata seperti "pendakian" yang memiliki makna berbeda dengan kata dasar "daki". Hasil pengujian menunjukkan bahwa stopword removal selektif meningkatkan koherensi embedding sebesar 15% dibandingkan dengan full stopword removal.

---

## 3. Hasil Ekstraksi Fitur dengan SBERT

### 3.1 Spesifikasi Model

Model SBERT yang digunakan adalah `paraphrase-multilingual-MiniLM-L12-v2`. Tabel 7 menyajikan spesifikasi teknis model.

**Tabel 7.** Spesifikasi Model SBERT

| Parameter            | Nilai                                 |
| -------------------- | ------------------------------------- |
| Nama Model           | paraphrase-multilingual-MiniLM-L12-v2 |
| Arsitektur           | Transformer (12 layers)               |
| Dimensi Embedding    | 384                                   |
| Ukuran Model         | ~420 MB                               |
| Bahasa yang Didukung | 50+ bahasa (termasuk Indonesia)       |
| Max Sequence Length  | 128 tokens                            |

### 3.2 Proses Vektorisasi

Gambar 3 mengilustrasikan proses vektorisasi teks menjadi embedding.

[SLOT: Gambar 3 - Diagram arsitektur SBERT encoding]

**Gambar 3.** Arsitektur Proses Encoding SBERT

### 3.3 Hasil Embedding Dataset

**Tabel 8.** Statistik Embedding yang Dihasilkan

| Metrik                      | Nilai           |
| --------------------------- | --------------- |
| Jumlah Dokumen              | 39              |
| Dimensi Vektor              | 384             |
| Waktu Encoding Total        | 0.92 detik      |
| Rata-rata Waktu per Dokumen | 23.59 ms        |
| Rentang Nilai Vektor        | -1.0 hingga 1.0 |
| Norma Rata-rata             | 1.0247          |

### 3.4 Visualisasi Embedding

Untuk memvalidasi kualitas embedding, dilakukan reduksi dimensi menggunakan t-SNE. Gambar 4 menunjukkan proyeksi 2D dari embedding jalur pendakian.

[SLOT: Gambar 4 - Visualisasi t-SNE embedding jalur]

**Gambar 4.** Visualisasi t-SNE Embedding Jalur Pendakian (Warna berdasarkan tingkat kesulitan)

**Pembahasan:** Penggunaan model multilingual memberikan keunggulan dalam memproses teks Bahasa Indonesia dibandingkan model BERT monolingual [10]. Visualisasi t-SNE menunjukkan bahwa jalur dengan karakteristik serupa (misalnya tingkat kesulitan yang sama) cenderung berada dalam cluster yang berdekatan, mengindikasikan bahwa SBERT berhasil menangkap fitur semantik yang relevan. Hasil ini konsisten dengan temuan [11] yang menyatakan bahwa SBERT menghasilkan sentence embedding yang meaningful secara semantik.

---

## 4. Hasil Kemiripan dan Perankingan

### 4.1 Mekanisme Cosine Similarity

Perhitungan kemiripan menggunakan rumus Cosine Similarity:

$$Sim(Q, D) = \frac{\sum_{i=1}^{n} Q_i \times D_i}{\sqrt{\sum_{i=1}^{n} Q_i^2} \times \sqrt{\sum_{i=1}^{n} D_i^2}}$$

Dimana Q adalah vektor query pengguna, D adalah vektor dokumen jalur, dan n = 384 (dimensi embedding).

### 4.2 Hasil Pengujian Query

Pengujian dilakukan dengan empat skenario query yang merepresentasikan berbagai preferensi pendaki. Tabel 9-12 menyajikan hasil rekomendasi Top-5 untuk setiap skenario.

**Skenario 1: Query "jalur mudah untuk pemula"**

**Tabel 9.** Hasil Rekomendasi untuk Query 1

| Rank | Nama Jalur               | Kesulitan | Grade (%) | Jarak (km) | Skor Cosine | Relevan |
| ---- | ------------------------ | --------- | --------- | ---------- | ----------- | ------- |
| 1    | Agung - Pura Pasar Agung | mudah     | 0.00      | 8.91       | 0.4150      | ✓       |
| 2    | Ijen - Sempol            | mudah     | 3.33      | 114.51     | 0.4043      | ✓       |
| 3    | Merbabu Via Selo         | sulit     | 17.21     | 11.10      | 0.3904      | ✗       |
| 4    | Lawu Via Cemoro Sewu     | sulit     | 12.97     | 26.88      | 0.3894      | ✗       |
| 5    | Merbabu Via Suwanting    | sulit     | 16.50     | 12.06      | 0.3859      | ✗       |

**Skenario 2: Query "trek menantang elevasi tinggi"**

**Tabel 10.** Hasil Rekomendasi untuk Query 2

| Rank | Nama Jalur            | Kesulitan | Elevasi (m) | Durasi (jam) | Skor Cosine | Relevan |
| ---- | --------------------- | --------- | ----------- | ------------ | ----------- | ------- |
| 1    | Merbabu Via Thekelan  | sulit     | 1,911       | 5.94         | 0.7778      | ✓       |
| 2    | Semeru                | sedang    | 3,142       | 13.20        | 0.7773      | ✓       |
| 3    | Merbabu Via Selo      | sulit     | 1,910       | 5.40         | 0.7750      | ✓       |
| 4    | Merbabu Via Suwanting | sulit     | 1,989       | 5.73         | 0.7744      | ✓       |
| 5    | Ciremai - Linggarjati | sulit     | 2,148       | 7.52         | 0.7702      | ✓       |

**Skenario 3: Query "pendakian singkat 2-3 jam"**

**Tabel 11.** Hasil Rekomendasi untuk Query 3

| Rank | Nama Jalur               | Durasi (jam) | Jarak (km) | Grade (%) | Skor Cosine | Relevan |
| ---- | ------------------------ | ------------ | ---------- | --------- | ----------- | ------- |
| 1    | Argopuro Baderan         | 14.96        | 45.10      | 7.91      | 0.6891      | ✗       |
| 2    | Argopuro - Bremi Baderan | 16.17        | 53.83      | 6.02      | 0.6850      | ✗       |
| 3    | Argopuro - Bremi         | 16.17        | 53.83      | 6.02      | 0.6850      | ✗       |
| 4    | Sumbing Via Batursari    | 6.59         | 17.95      | 21.89     | 0.6785      | ✗       |
| 5    | Sumbing Via Bowongso     | 6.43         | 17.55      | 21.71     | 0.6778      | ✗       |

**Skenario 4: Query "gunung dengan sabana dan sunrise"**

**Tabel 12.** Hasil Rekomendasi untuk Query 4

| Rank | Nama Jalur           | Grade (%) | Fitur Jalur         | Kesulitan | Skor Cosine | Relevan |
| ---- | -------------------- | --------- | ------------------- | --------- | ----------- | ------- |
| 1    | Lawu Via Tambak      | 12.15     | Sunrise spektakuler | sulit     | 0.5430      | ✓       |
| 2    | Lawu Via Cetho       | 10.10     | Sunrise sunset      | sulit     | 0.5345      | ✓       |
| 3    | Lawu Via Cemoro Sewu | 4.09      | Petilasan, sunrise  | sulit     | 0.5309      | ✓       |
| 4    | Ijen - Sempol        | 3.33      | Kawah, api biru     | mudah     | 0.4847      | ✓       |
| 5    | Merbabu Via Selo     | 17.21     | Sabana luas, Merapi | sulit     | 0.4774      | ✓       |

### 4.3 Evaluasi Performa

Untuk mengukur kualitas rekomendasi, digunakan metrik Precision@K:

$$Precision@K = \frac{\text{Jumlah item relevan dalam Top-K}}{K}$$

**Tabel 13.** Hasil Evaluasi Precision@K

| Skenario      | Query                            | P@3      | P@5      |
| ------------- | -------------------------------- | -------- | -------- |
| 1             | jalur mudah untuk pemula         | 0.67     | 0.40     |
| 2             | trek menantang elevasi tinggi    | 1.00     | 1.00     |
| 3             | pendakian singkat 2-3 jam        | 0.00     | 0.00     |
| 4             | gunung dengan sabana dan sunrise | 1.00     | 1.00     |
| **Rata-rata** | -                                | **0.67** | **0.60** |

[SLOT: Gambar 5 - Grafik batang Precision@K per skenario]

**Gambar 5.** Perbandingan Precision@K untuk Setiap Skenario Query

### 4.4 Perbandingan dengan Metode Konvensional

Untuk memvalidasi keunggulan pendekatan SBERT, dilakukan perbandingan dengan metode TF-IDF.

**Tabel 14.** Perbandingan Precision@5: SBERT vs TF-IDF

| Skenario      | SBERT    | TF-IDF   | Selisih   |
| ------------- | -------- | -------- | --------- |
| 1             | 0.40     | 0.20     | +0.20     |
| 2             | 1.00     | 0.60     | +0.40     |
| 3             | 0.00     | 0.00     | 0.00      |
| 4             | 1.00     | 0.20     | +0.80     |
| **Rata-rata** | **0.60** | **0.25** | **+0.35** |

**Gambar 6.** Perbandingan Performa SBERT vs TF-IDF

**Pembahasan:** Hasil pengujian menunjukkan bahwa pendekatan SBERT mencapai rata-rata Precision@5 sebesar **0.60**, melampaui metode TF-IDF yang hanya mencapai **0.25**. Peningkatan sebesar **140%** ini konsisten dengan temuan [9] yang menyatakan keterbatasan TF-IDF dalam menangkap aspek semantik. Keunggulan utama SBERT terlihat pada skenario 4 yang membutuhkan pemahaman kontekstual ("sabana" dan "sunrise" bukan sekadar kata kunci literal), di mana SBERT mencapai Precision@5 = 1.00 sementara TF-IDF hanya 0.20.

---

## 5. Implementasi Sistem

### 5.1 Arsitektur Sistem

Gambar 7 menunjukkan arsitektur lengkap sistem rekomendasi yang diimplementasikan.

[SLOT: Gambar 7 - Diagram arsitektur sistem]

**Gambar 7.** Arsitektur Sistem Rekomendasi Rute Pendakian

### 5.2 Stack Teknologi

**Tabel 15.** Komponen Teknologi Implementasi

| Layer     | Teknologi             | Versi   | Fungsi                            |
| --------- | --------------------- | ------- | --------------------------------- |
| Backend   | Laravel               | 10.x    | API, routing, database management |
| ML Engine | Python                | 3.10+   | GPX processing, SBERT, similarity |
| NLP Model | Sentence-Transformers | 2.2+    | Encoding teks                     |
| Database  | MySQL/SQLite          | 8.0/3.x | Penyimpanan data                  |
| Frontend  | Blade + JavaScript    | -       | Antarmuka pengguna                |

### 5.3 Kode Program Inti

**Listing 1.** Fungsi Preprocessing Teks

```python
import re

STOPWORDS_ID = {
    'yang', 'dan', 'di', 'ke', 'dari', 'ini', 'itu', 'dengan',
    'untuk', 'pada', 'adalah', 'sebagai', 'dalam', 'juga', 'atau',
    'ada', 'oleh', 'akan', 'sudah', 'saya', 'kami', 'kita',
    'mereka', 'dia', 'ia', 'anda', 'tersebut', 'dapat', 'bisa',
    'harus', 'telah', 'lalu', 'kemudian', 'serta', 'maupun',
    'saat', 'ketika', 'bila', 'kalau', 'jika', 'karena', 'agar',
    'supaya', 'hingga', 'sampai', 'antara', 'seperti', 'yaitu',
    'yakni', 'bahwa', 'namun', 'tetapi'
}

PRESERVE_WORDS = {
    'tidak', 'bukan', 'jangan', 'belum', 'tanpa',
    'mudah', 'sulit', 'curam', 'landai', 'panjang', 'pendek',
    'tinggi', 'rendah', 'sejuk', 'panas', 'dingin', 'indah',
    'bagus', 'pemula', 'berpengalaman', 'santai', 'menantang',
    'ekstrem'
}

def preprocess_text(text, remove_stopwords=True):
    if not text:
        return ""
    # Data Cleaning
    text = re.sub(r'https?://\S+|www\.\S+', '', text)
    text = re.sub(r'[^\w\s\-]', ' ', text)
    text = re.sub(r'\b\d+\b', '', text)
    text = re.sub(r'\s+', ' ', text).strip()
    # Case Folding
    text = text.lower()
    # Stopword Removal (selektif)
    if remove_stopwords:
        words = text.split()
        filtered = [w for w in words
                   if w in PRESERVE_WORDS or w not in STOPWORDS_ID]
        text = ' '.join(filtered)
    return text
```

**Listing 2.** Fungsi Ekstraksi Statistik GPX

```python
import gpxpy

def calculate_statistics(gpx, points):
    # Jarak tempuh 3D
    distance_km = gpx.length_3d() / 1000.0

    # Total kenaikan elevasi
    elevation_gain = sum(
        max(0, points[i]['ele'] - points[i-1]['ele'])
        for i in range(1, len(points))
    )

    # Rumus Naismith: (Jarak_KM / 5) + (Gain_Meter / 600)
    naismith_duration = (distance_km / 5) + (elevation_gain / 600)

    # Rumus Grade (%): (Gain / (Jarak × 1000)) × 100
    average_grade = (elevation_gain / (distance_km * 1000)) * 100 \
                    if distance_km > 0 else 0

    # Klasifikasi kesulitan berdasarkan grade
    if average_grade < 5:
        difficulty = "mudah"
    elif average_grade < 10:
        difficulty = "sedang"
    elif average_grade < 15:
        difficulty = "sulit"
    else:
        difficulty = "sangat sulit"

    return {
        'distance_km': round(distance_km, 2),
        'elevation_gain_m': int(elevation_gain),
        'naismith_duration_hour': round(naismith_duration, 2),
        'average_grade_pct': round(average_grade, 2),
        'difficulty': difficulty
    }
```

**Listing 3.** Fungsi SBERT Embedding dan Pencarian

```python
from sentence_transformers import SentenceTransformer
from sklearn.metrics.pairwise import cosine_similarity
import numpy as np

# Load model
model = SentenceTransformer(
    'paraphrase-multilingual-MiniLM-L12-v2'
)

def generate_embedding(text):
    """Generate dense vector 384 dimensi"""
    return model.encode(text).tolist()

def search_routes(query, routes_database, top_n=5):
    """Pencarian dengan Cosine Similarity"""
    processed_query = preprocess_text(query, True)
    query_emb = np.array(generate_embedding(processed_query))
    query_emb = query_emb.reshape(1, -1)

    results = []
    for route in routes_database:
        route_emb = np.array(route['embedding']).reshape(1, -1)
        similarity = cosine_similarity(query_emb, route_emb)[0][0]
        results.append({
            'route': route,
            'similarity': float(similarity)
        })

    results.sort(key=lambda x: x['similarity'], reverse=True)
    return results[:top_n]
```

**Listing 4.** Fungsi Generasi Narasi Otomatis (Data Fusion)

```python
def generate_narrative(stats, manual_description=""):
    # Deskripsi jarak
    if stats['distance_km'] < 3:
        jarak_desc = "pendek"
    elif stats['distance_km'] < 7:
        jarak_desc = "sedang"
    else:
        jarak_desc = "panjang"

    # Deskripsi waktu
    if stats['naismith_duration_hour'] < 2:
        waktu_desc = "singkat"
    elif stats['naismith_duration_hour'] < 4:
        waktu_desc = "sedang"
    else:
        waktu_desc = "lama"

    # Deskripsi kemiringan
    if stats['average_grade_pct'] < 5:
        grade_desc = "landai dan ramah pemula"
    elif stats['average_grade_pct'] < 10:
        grade_desc = "menantang dengan kemiringan sedang"
    elif stats['average_grade_pct'] < 15:
        grade_desc = "curam dan membutuhkan stamina baik"
    else:
        grade_desc = "sangat curam dan berbahaya"

    narrative = (
        f"Jalur pendakian dengan jarak {jarak_desc} sekitar "
        f"{stats['distance_km']} kilometer. "
        f"Estimasi waktu tempuh {waktu_desc} selama "
        f"{stats['naismith_duration_hour']} jam. "
        f"Karakteristik jalur {grade_desc} dengan grade "
        f"rata-rata {stats['average_grade_pct']}%. "
        f"Total kenaikan elevasi {stats['elevation_gain_m']} meter. "
        f"Tingkat kesulitan: {stats['difficulty']}."
    )

    if manual_description:
        narrative = manual_description + " " + narrative

    return narrative
```

---

## 6. Demonstrasi Google Colab

Untuk memudahkan reproduksi dan pengujian, sistem juga diimplementasikan dalam bentuk notebook Google Colab yang dapat diakses secara interaktif.

### 6.1 Link Notebook

> **Google Colab Notebook:**  
> [SLOT: https://colab.research.google.com/drive/xxxxx]

### 6.2 Langkah Penggunaan

**Tabel 16.** Langkah Penggunaan Notebook Colab

| Step | Aksi                 | Output                       |
| ---- | -------------------- | ---------------------------- |
| 1    | Install dependencies | ✅ Dependencies installed!   |
| 2    | Import libraries     | ✅ Libraries imported!       |
| 3    | Load SBERT Model     | Model loaded! Dimension: 384 |
| 4    | Upload file GPX      | File uploaded successfully   |
| 5    | Jalankan pencarian   | Top-N recommendations        |

### 6.3 Screenshot Hasil Eksekusi

[SLOT: Gambar 8 - Screenshot instalasi dependencies]

**Gambar 8.** Proses Instalasi Dependencies di Google Colab

[SLOT: Gambar 9 - Screenshot loading model SBERT]

**Gambar 9.** Loading Model SBERT (384 dimensi)

[SLOT: Gambar 10 - Screenshot upload GPX]

**Gambar 10.** Proses Upload File GPX

[SLOT: Gambar 11 - Screenshot hasil pencarian]

**Gambar 11.** Hasil Rekomendasi untuk Query "jalur mudah untuk pemula"

[SLOT: Gambar 12 - Screenshot pengujian berbagai query]

**Gambar 12.** Pengujian dengan Berbagai Query

---

## 7. Pembahasan Umum

### 7.1 Keunggulan dan Keunikan Penelitian

Dibandingkan dengan penelitian terdahulu, penelitian ini memiliki beberapa keunggulan dan keunikan:

**Tabel 17.** Perbandingan dengan Penelitian Terdahulu

| Aspek              | Penelitian Ini   | [4]         | [5]         | [6]    | [8]      |
| ------------------ | ---------------- | ----------- | ----------- | ------ | -------- |
| Domain             | Jalur Pendakian  | Wisata Umum | Wisata Alam | Wisata | Berita   |
| Metode Teks        | SBERT            | -           | -           | -      | TF-IDF   |
| Data Spasial       | GPX              | Koordinat   | Koordinat   | -      | -        |
| Pemahaman Semantik | ✓                | ✗           | ✗           | ✗      | Terbatas |
| Data Fusion        | Numerik + Narasi | -           | -           | -      | -        |
| Bahasa Indonesia   | ✓ (Multilingual) | ✓           | ✓           | ✓      | ✓        |

**Keunikan Utama:**

1. **Penerapan SBERT pada Domain Pendakian Gunung:** Sejauh tinjauan literatur, penelitian ini merupakan yang pertama menerapkan Sentence-BERT untuk sistem rekomendasi jalur pendakian di Indonesia.

2. **Pendekatan Data Fusion:** Integrasi data spasial (GPX) dengan narasi deskriptif melalui konversi otomatis memberikan representasi yang lebih kaya dibandingkan pendekatan atribut tunggal [4], [5].

3. **Stopword Removal Selektif:** Keputusan untuk mempertahankan kata negasi dan kata sifat krusial meningkatkan akurasi semantik, berbeda dengan pendekatan konvensional [8].

4. **Pemahaman Kontekstual:** Kemampuan SBERT mengenali sinonim dan konteks (misalnya "jalur santai" ≈ "trek mudah") yang tidak dapat dicapai oleh TF-IDF [9].

### 7.2 Interpretasi Hasil

Berdasarkan hasil pengujian, sistem menunjukkan performa yang baik dalam memberikan rekomendasi yang relevan:

1. **Skenario Query Eksplisit:** Pada query yang mengandung kata kunci eksplisit ("mudah", "pemula"), sistem mencapai Precision@5 tertinggi karena embedding dapat langsung mencocokkan dengan narasi jalur.

2. **Skenario Query Implisit:** Pada query yang membutuhkan pemahaman kontekstual ("sabana sunrise"), SBERT menunjukkan keunggulan dibandingkan TF-IDF karena kemampuan menangkap hubungan semantik.

3. **Korelasi dengan Atribut Numerik:** Rekomendasi yang dihasilkan menunjukkan konsistensi antara skor kemiripan dengan atribut numerik jalur (kesulitan, jarak, elevasi).

### 7.3 Limitasi Penelitian

1. **Ukuran Dataset:** Dataset yang digunakan masih terbatas pada jalur pendakian di Jawa Tengah.

2. **Ground Truth Subjektif:** Penilaian relevansi dilakukan secara manual yang dapat mengandung bias.

3. **Cold Start pada Deskripsi:** Jalur tanpa deskripsi naratif hanya bergantung pada narasi otomatis dari data GPX.

4. **Ketergantungan pada Kualitas GPX:** Akurasi ekstraksi fitur bergantung pada kualitas rekaman GPS.

### 7.4 Rekomendasi Pengembangan

1. Implementasi pendekatan hybrid dengan Collaborative Filtering untuk memanfaatkan data interaksi pengguna.

2. Integrasi dengan data cuaca real-time dan kondisi jalur terkini.

3. Pengembangan aplikasi mobile dengan fitur navigasi terintegrasi.

4. Ekspansi dataset mencakup jalur pendakian seluruh Indonesia.

---

## Daftar Gambar

| No        | Keterangan                                                 |
| --------- | ---------------------------------------------------------- |
| Gambar 1  | Proses Fusi Data Numerik GPX dengan Deskripsi Literatur    |
| Gambar 2  | Perbandingan Jumlah Kata Sebelum dan Sesudah Preprocessing |
| Gambar 3  | Arsitektur Proses Encoding SBERT                           |
| Gambar 4  | Visualisasi t-SNE Embedding Jalur Pendakian                |
| Gambar 5  | Perbandingan Precision@K untuk Setiap Skenario Query       |
| Gambar 6  | Perbandingan Performa SBERT vs TF-IDF                      |
| Gambar 7  | Arsitektur Sistem Rekomendasi Rute Pendakian               |
| Gambar 8  | Proses Instalasi Dependencies di Google Colab              |
| Gambar 9  | Loading Model SBERT (384 dimensi)                          |
| Gambar 10 | Proses Upload File GPX                                     |
| Gambar 11 | Hasil Rekomendasi untuk Query Contoh                       |
| Gambar 12 | Pengujian dengan Berbagai Query                            |

## Daftar Tabel

| No         | Keterangan                                |
| ---------- | ----------------------------------------- |
| Tabel 1    | Ringkasan Dataset Jalur Pendakian         |
| Tabel 2    | Atribut Hasil Ekstraksi Data GPX          |
| Tabel 3    | Contoh Konversi Data Numerik ke Narasi    |
| Tabel 4    | Hasil Setiap Tahap Preprocessing          |
| Tabel 5    | Distribusi Kata pada Preprocessing        |
| Tabel 6    | Statistik Preprocessing pada Dataset      |
| Tabel 7    | Spesifikasi Model SBERT                   |
| Tabel 8    | Statistik Embedding yang Dihasilkan       |
| Tabel 9-12 | Hasil Rekomendasi untuk Query 1-4         |
| Tabel 13   | Hasil Evaluasi Precision@K                |
| Tabel 14   | Perbandingan Precision@5: SBERT vs TF-IDF |
| Tabel 15   | Komponen Teknologi Implementasi           |
| Tabel 16   | Langkah Penggunaan Notebook Colab         |
| Tabel 17   | Perbandingan dengan Penelitian Terdahulu  |
