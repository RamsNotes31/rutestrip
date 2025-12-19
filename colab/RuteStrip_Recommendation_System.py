# -*- coding: utf-8 -*-
"""
# 🏔️ Sistem Rekomendasi Rute Pendakian Gunung
## Content-Based Filtering dengan SBERT dan Cosine Similarity

Paper: "Sistem Rekomendasi Rute Pendakian Gunung Menggunakan Content-Based Filtering 
Berbasis SBERT dan Cosine Similarity"

---

### Komponen Utama:
1. **Data Collection & Fusion**: Ekstraksi fitur dari GPX + deskripsi manual
2. **Preprocessing**: Regex cleaning, case folding, stopword removal (selektif)
3. **Feature Extraction**: SBERT (paraphrase-multilingual-MiniLM-L12-v2) → 384 dimensi
4. **Similarity & Ranking**: Cosine Similarity dengan Top-N ranking
"""

#@title 1️⃣ Install Dependencies
!pip install gpxpy sentence-transformers scikit-learn pandas numpy -q
print("✅ Dependencies installed!")

#@title 2️⃣ Import Libraries
import os
import re
import json
import math
import numpy as np
import pandas as pd
import gpxpy
from sentence_transformers import SentenceTransformer
from sklearn.metrics.pairwise import cosine_similarity
from google.colab import files
import warnings
warnings.filterwarnings('ignore')

print("✅ Libraries imported!")

#@title 3️⃣ Text Preprocessing (Sesuai Paper)

# Stopwords umum Bahasa Indonesia (selektif - pertahankan negasi dan kata sifat penting)
STOPWORDS_ID = {
    'yang', 'dan', 'di', 'ke', 'dari', 'ini', 'itu', 'dengan', 'untuk', 'pada',
    'adalah', 'sebagai', 'dalam', 'juga', 'atau', 'ada', 'oleh', 'akan', 'sudah',
    'saya', 'kami', 'kita', 'mereka', 'dia', 'ia', 'anda', 'tersebut', 'dapat',
    'bisa', 'harus', 'telah', 'lalu', 'kemudian', 'serta', 'maupun', 'saat',
    'ketika', 'bila', 'kalau', 'jika', 'karena', 'agar', 'supaya', 'hingga',
    'sampai', 'antara', 'seperti', 'yaitu', 'yakni', 'bahwa', 'namun', 'tetapi'
}

# Kata-kata yang harus dipertahankan (negasi, kata sifat krusial)
PRESERVE_WORDS = {
    'tidak', 'bukan', 'jangan', 'belum', 'tanpa',  # negasi
    'mudah', 'sulit', 'curam', 'landai', 'panjang', 'pendek',  # sifat jalur
    'tinggi', 'rendah', 'sejuk', 'panas', 'dingin', 'indah', 'bagus',  # sifat lain
    'pemula', 'berpengalaman', 'santai', 'menantang', 'ekstrem'  # level
}

def preprocess_text(text: str, remove_stopwords: bool = True) -> str:
    """
    Preprocessing teks sesuai paper:
    1. Data Cleaning - Regex untuk hapus karakter non-ASCII, URL, whitespace berlebih
    2. Case Folding - Lowercase
    3. Stopword Removal - Selektif (pertahankan negasi dan kata sifat krusial)
    4. NO STEMMING - Karena SBERT sensitif terhadap konteks
    """
    if not text:
        return ""
    
    # 1. Data Cleaning
    text = re.sub(r'https?://\S+|www\.\S+', '', text)  # Hapus URL
    text = re.sub(r'[^\w\s\-]', ' ', text)  # Hapus karakter non-ASCII
    text = re.sub(r'\b\d+\b', '', text)  # Hapus angka berdiri sendiri
    text = re.sub(r'\s+', ' ', text).strip()  # Hapus whitespace berlebih
    
    # 2. Case Folding
    text = text.lower()
    
    # 3. Stopword Removal (selektif)
    if remove_stopwords:
        words = text.split()
        filtered_words = []
        for word in words:
            if word in PRESERVE_WORDS:
                filtered_words.append(word)
            elif word not in STOPWORDS_ID:
                filtered_words.append(word)
        text = ' '.join(filtered_words)
    
    return text

print("✅ Preprocessing functions defined!")

#@title 4️⃣ GPX Processing Functions

def parse_gpx(gpx_path):
    """Parse GPX file and extract track points"""
    with open(gpx_path, 'r', encoding='utf-8') as gpx_file:
        gpx = gpxpy.parse(gpx_file)
    
    points = []
    for track in gpx.tracks:
        for segment in track.segments:
            for point in segment.points:
                points.append({
                    'lat': point.latitude,
                    'lon': point.longitude,
                    'ele': point.elevation if point.elevation else 0,
                    'time': point.time
                })
    return gpx, points


def calculate_statistics(gpx, points):
    """Calculate hiking statistics from GPX data"""
    distance_m = gpx.length_3d()
    distance_km = distance_m / 1000.0
    
    # Calculate elevation gain
    elevation_gain = 0
    for i in range(1, len(points)):
        ele_diff = points[i]['ele'] - points[i-1]['ele']
        if ele_diff > 0:
            elevation_gain += ele_diff
    
    # Rumus Naismith: (Jarak_KM / 5) + (Gain_Meter / 600)
    naismith_duration = (distance_km / 5) + (elevation_gain / 600)
    
    # Rumus Grade (%): (Gain / (Jarak_KM * 1000)) * 100
    if distance_km > 0:
        average_grade = (elevation_gain / (distance_km * 1000)) * 100
    else:
        average_grade = 0
    
    # Determine difficulty level
    if average_grade < 5:
        difficulty = "mudah"
    elif average_grade < 10:
        difficulty = "sedang"
    elif average_grade < 15:
        difficulty = "sulit"
    else:
        difficulty = "sangat sulit"
    
    # Elevation range
    elevations = [p['ele'] for p in points if p['ele'] > 0]
    min_elevation = min(elevations) if elevations else 0
    max_elevation = max(elevations) if elevations else 0
    
    return {
        'distance_km': round(distance_km, 2),
        'elevation_gain_m': int(elevation_gain),
        'naismith_duration_hour': round(naismith_duration, 2),
        'average_grade_pct': round(average_grade, 2),
        'min_elevation': int(min_elevation),
        'max_elevation': int(max_elevation),
        'difficulty': difficulty
    }


def generate_narrative(stats, manual_description=""):
    """Generate narrative text from statistics for SBERT embedding"""
    
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
        grade_desc = "curam dan membutuhkan stamina yang baik"
    else:
        grade_desc = "sangat curam dan berbahaya"
    
    narrative = (
        f"Jalur pendakian dengan jarak {jarak_desc} sekitar {stats['distance_km']} kilometer. "
        f"Estimasi waktu tempuh {waktu_desc} selama {stats['naismith_duration_hour']} jam menggunakan rumus Naismith. "
        f"Karakteristik jalur {grade_desc} dengan grade rata-rata {stats['average_grade_pct']}%. "
        f"Total kenaikan elevasi {stats['elevation_gain_m']} meter dengan ketinggian dari {stats['min_elevation']}m "
        f"hingga {stats['max_elevation']}m. "
        f"Tingkat kesulitan: {stats['difficulty']}. "
        f"Cocok untuk pendaki {'pemula' if stats['difficulty'] == 'mudah' else 'berpengalaman' if stats['difficulty'] in ['sulit', 'sangat sulit'] else 'dengan pengalaman menengah'}."
    )
    
    # Data Fusion: Gabungkan deskripsi manual dengan narrative otomatis
    if manual_description:
        narrative = manual_description + " " + narrative
    
    return narrative

print("✅ GPX processing functions defined!")

#@title 5️⃣ Load SBERT Model

print("⏳ Loading SBERT model (paraphrase-multilingual-MiniLM-L12-v2)...")
model = SentenceTransformer('sentence-transformers/paraphrase-multilingual-MiniLM-L12-v2')
print(f"✅ Model loaded! Embedding dimension: {model.get_sentence_embedding_dimension()}")

#@title 6️⃣ Embedding & Similarity Functions

def generate_embedding(text):
    """Generate SBERT embedding for text"""
    embedding = model.encode(text)
    return embedding.tolist()


def calculate_cosine_similarity(query_embedding, route_embedding):
    """
    Menghitung Cosine Similarity antara vektor query dan vektor rute
    
    Formula: Sim(Q, D) = (Q · D) / (||Q|| × ||D||)
    
    Di mana:
    - Q · D = Dot product antara vektor query dan vektor dokumen
    - ||Q|| = Magnitude (panjang) vektor query
    - ||D|| = Magnitude (panjang) vektor dokumen
    """
    query_vec = np.array(query_embedding).reshape(1, -1)
    route_vec = np.array(route_embedding).reshape(1, -1)
    similarity = cosine_similarity(query_vec, route_vec)[0][0]
    return float(similarity)

print("✅ Embedding & similarity functions defined!")

#@title 7️⃣ Database Simulation (In-Memory)

# Simulasi database menggunakan list
routes_database = []

def add_route(name, gpx_path, manual_description=""):
    """Proses GPX dan tambahkan ke database"""
    try:
        gpx, points = parse_gpx(gpx_path)
        if not points:
            print(f"❌ Error: No track points found in {gpx_path}")
            return None
        
        stats = calculate_statistics(gpx, points)
        narrative = generate_narrative(stats, manual_description)
        
        # Preprocess narrative sebelum embedding
        processed_narrative = preprocess_text(narrative, remove_stopwords=False)
        embedding = generate_embedding(processed_narrative)
        
        route = {
            'id': len(routes_database) + 1,
            'name': name,
            'description': manual_description,
            'distance_km': stats['distance_km'],
            'elevation_gain_m': stats['elevation_gain_m'],
            'naismith_duration_hour': stats['naismith_duration_hour'],
            'average_grade_pct': stats['average_grade_pct'],
            'difficulty': stats['difficulty'],
            'narrative_text': narrative,
            'embedding': embedding
        }
        
        routes_database.append(route)
        print(f"✅ Route added: {name}")
        print(f"   📏 Distance: {stats['distance_km']} km")
        print(f"   ⛰️ Elevation gain: {stats['elevation_gain_m']} m")
        print(f"   ⏱️ Duration: {stats['naismith_duration_hour']} hours")
        print(f"   📐 Grade: {stats['average_grade_pct']}%")
        print(f"   🏷️ Difficulty: {stats['difficulty']}")
        return route
    
    except Exception as e:
        print(f"❌ Error processing {gpx_path}: {str(e)}")
        return None


def search_routes(query, top_n=5):
    """
    Cari rute berdasarkan query menggunakan SBERT + Cosine Similarity
    
    Metode: Content-Based Filtering
    1. Query dipreproses (cleaning, lowercase, stopword removal)
    2. Query dikonversi ke embedding 384 dimensi menggunakan SBERT
    3. Cosine Similarity dihitung antara query embedding dan setiap rute
    4. Hasil diurutkan descending berdasarkan similarity score
    """
    if not routes_database:
        print("❌ Database kosong! Silakan tambahkan rute terlebih dahulu.")
        return []
    
    # Preprocess query
    processed_query = preprocess_text(query, remove_stopwords=True)
    print(f"\n🔍 Query asli: \"{query}\"")
    print(f"📝 Query setelah preprocessing: \"{processed_query}\"")
    
    # Generate query embedding
    query_embedding = generate_embedding(processed_query)
    
    # Calculate similarity for each route
    results = []
    for route in routes_database:
        similarity = calculate_cosine_similarity(query_embedding, route['embedding'])
        results.append({
            'route': route,
            'similarity': similarity
        })
    
    # Sort by similarity (descending)
    results.sort(key=lambda x: x['similarity'], reverse=True)
    
    # Return top N
    return results[:top_n]


def display_search_results(results):
    """Tampilkan hasil pencarian dengan detail"""
    if not results:
        print("❌ Tidak ada hasil ditemukan.")
        return
    
    print("\n" + "="*60)
    print("📊 HASIL REKOMENDASI (Content-Based Filtering + SBERT)")
    print("="*60)
    print("\n📐 Formula Cosine Similarity yang digunakan:")
    print("   Sim(Q, D) = (Q · D) / (||Q|| × ||D||)")
    print("   - Q = Vektor query (384 dimensi)")
    print("   - D = Vektor rute (384 dimensi)")
    print("="*60)
    
    for i, result in enumerate(results, 1):
        route = result['route']
        sim_score = result['similarity']
        
        print(f"\n🥇 Ranking #{i}")
        print(f"   📛 Nama: {route['name']}")
        print(f"   📏 Jarak: {route['distance_km']} km")
        print(f"   ⛰️ Elevasi: {route['elevation_gain_m']} m")
        print(f"   ⏱️ Durasi: {route['naismith_duration_hour']} jam")
        print(f"   📐 Grade: {route['average_grade_pct']}%")
        print(f"   🏷️ Kesulitan: {route['difficulty']}")
        print(f"\n   🤖 Deskripsi SBERT:")
        print(f"   {route['narrative_text'][:200]}...")
        print(f"\n   📊 Cosine Similarity: {sim_score:.4f} ({sim_score*100:.2f}%)")
        print("-"*60)

print("✅ Database & search functions defined!")

#@title 8️⃣ Upload dan Proses File GPX

# Upload GPX files
print("📁 Upload file GPX Anda:")
uploaded = files.upload()

for filename in uploaded.keys():
    if filename.endswith('.gpx') or filename.endswith('.xml'):
        route_name = filename.replace('.gpx', '').replace('.xml', '').replace('_', ' ').title()
        
        # Optional: Tambahkan deskripsi manual
        manual_desc = input(f"Masukkan deskripsi untuk {route_name} (opsional, tekan Enter untuk skip): ")
        
        add_route(route_name, filename, manual_desc)
    else:
        print(f"⚠️ Skipping {filename} - bukan file GPX")

print(f"\n📚 Total rute dalam database: {len(routes_database)}")

#@title 9️⃣ Pencarian Rute dengan Natural Language Query

# Contoh pencarian
query = input("🔍 Masukkan preferensi Anda (contoh: 'jalur mudah untuk pemula'): ")

if query.strip():
    results = search_routes(query, top_n=5)
    display_search_results(results)
else:
    print("⚠️ Silakan masukkan query terlebih dahulu.")

#@title 🔟 Demo dengan Data Sample

# Buat data sample jika tidak ada file GPX yang diupload
if not routes_database:
    print("📝 Membuat data sample untuk demo...")
    
    # Simulasi data rute tanpa GPX
    sample_routes = [
        {
            'name': 'Gunung Prau via Dieng',
            'description': 'Jalur dengan pemandangan golden sunrise yang indah, vegetasi padang rumput',
            'distance_km': 3.5,
            'elevation_gain_m': 450,
            'difficulty': 'mudah'
        },
        {
            'name': 'Gunung Merbabu via Selo',
            'description': 'Trek menantang dengan sabana luas, sumber air tersedia di pos 2',
            'distance_km': 12.0,
            'elevation_gain_m': 1200,
            'difficulty': 'sulit'
        },
        {
            'name': 'Gunung Sindoro via Kledung',
            'description': 'Jalur curam berbatu, hutan pinus lebat, cocok pendaki berpengalaman',
            'distance_km': 8.0,
            'elevation_gain_m': 1400,
            'difficulty': 'sangat sulit'
        },
        {
            'name': 'Bukit Sikunir',
            'description': 'Jalur pendek untuk melihat sunrise, vegetasi rendah, landai',
            'distance_km': 1.5,
            'elevation_gain_m': 150,
            'difficulty': 'mudah'
        },
        {
            'name': 'Gunung Sumbing via Garung',
            'description': 'Trek panjang dengan medan beragam, pemandangan kawah, air terjun',
            'distance_km': 10.0,
            'elevation_gain_m': 1800,
            'difficulty': 'sangat sulit'
        }
    ]
    
    for route in sample_routes:
        # Generate narrative
        stats = {
            'distance_km': route['distance_km'],
            'elevation_gain_m': route['elevation_gain_m'],
            'naismith_duration_hour': round((route['distance_km']/5) + (route['elevation_gain_m']/600), 2),
            'average_grade_pct': round((route['elevation_gain_m']/(route['distance_km']*1000))*100, 2),
            'min_elevation': 1500,
            'max_elevation': 2500 + route['elevation_gain_m'],
            'difficulty': route['difficulty']
        }
        
        narrative = generate_narrative(stats, route['description'])
        processed = preprocess_text(narrative, remove_stopwords=False)
        embedding = generate_embedding(processed)
        
        routes_database.append({
            'id': len(routes_database) + 1,
            'name': route['name'],
            'description': route['description'],
            'distance_km': route['distance_km'],
            'elevation_gain_m': route['elevation_gain_m'],
            'naismith_duration_hour': stats['naismith_duration_hour'],
            'average_grade_pct': stats['average_grade_pct'],
            'difficulty': route['difficulty'],
            'narrative_text': narrative,
            'embedding': embedding
        })
        print(f"✅ Added: {route['name']}")
    
    print(f"\n📚 Database berisi {len(routes_database)} rute sample")

#@title 1️⃣1️⃣ Test Pencarian dengan Berbagai Query

test_queries = [
    "jalur mudah untuk pemula dengan pemandangan indah",
    "trek menantang dengan elevasi tinggi untuk pendaki berpengalaman",
    "pendakian singkat 2-3 jam dengan jarak pendek",
    "jalur landai dengan sabana dan sunrise"
]

print("🧪 Testing berbagai query:")
print("="*60)

for query in test_queries:
    print(f"\n🔍 Query: \"{query}\"")
    results = search_routes(query, top_n=3)
    
    print("\n📊 Top 3 Hasil:")
    for i, result in enumerate(results, 1):
        route = result['route']
        print(f"   {i}. {route['name']} - Similarity: {result['similarity']:.4f} ({result['similarity']*100:.1f}%)")
    print("-"*60)

#@title 1️⃣2️⃣ Export Hasil ke CSV

# Export database ke CSV
df = pd.DataFrame([{
    'ID': r['id'],
    'Nama': r['name'],
    'Deskripsi': r['description'],
    'Jarak (km)': r['distance_km'],
    'Elevasi (m)': r['elevation_gain_m'],
    'Durasi (jam)': r['naismith_duration_hour'],
    'Grade (%)': r['average_grade_pct'],
    'Kesulitan': r['difficulty'],
    'Narrative': r['narrative_text']
} for r in routes_database])

df.to_csv('hiking_routes_database.csv', index=False)
print("✅ Database exported to hiking_routes_database.csv")
files.download('hiking_routes_database.csv')

# Export embeddings
df_embeddings = pd.DataFrame([{
    'ID': r['id'],
    'Nama': r['name'],
    'Narrative': r['narrative_text'],
    **{f'dim_{i}': r['embedding'][i] for i in range(len(r['embedding']))}
} for r in routes_database])

df_embeddings.to_csv('hiking_routes_embeddings.csv', index=False)
print("✅ Embeddings exported to hiking_routes_embeddings.csv")
files.download('hiking_routes_embeddings.csv')

print("\n🎉 Selesai! File CSV sudah terdownload.")
