#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
================================================================================
SISTEM REKOMENDASI RUTE PENDAKIAN GUNUNG
Content-Based Filtering dengan SBERT dan Cosine Similarity
================================================================================

Script ini mengikuti urutan metodologi penelitian:
1. Pengumpulan dan Fusi Data (Data Collection and Fusion)
2. Pra-pemrosesan Data (Preprocessing)
3. Ekstraksi Fitur dengan SBERT (Feature Extraction)
4. Kemiripan dan Perankingan (Similarity & Ranking)

Author: Rama Danadipa Putra Wijaya
Email: ramadanadipa@students.amikom.ac.id
"""

import os
import re
import json
import math
import time
import warnings
from datetime import datetime
from typing import List, Dict, Tuple, Optional
from dataclasses import dataclass, field, asdict

warnings.filterwarnings('ignore')

# ================================================================================
# KONFIGURASI
# ================================================================================

CONFIG = {
    'model_name': 'paraphrase-multilingual-MiniLM-L12-v2',
    'embedding_dim': 384,
    'gpx_directory': 'data/gpx',
    'output_directory': 'output',
    'top_n_recommendations': 5
}

# ================================================================================
# BAGIAN 1: PENGUMPULAN DAN FUSI DATA
# ================================================================================

print("=" * 70)
print("BAGIAN 1: PENGUMPULAN DAN FUSI DATA")
print("=" * 70)

@dataclass
class TrackPoint:
    """Representasi titik track dari file GPX"""
    lat: float
    lon: float
    ele: float
    time: Optional[datetime] = None


@dataclass
class GPXStatistics:
    """Statistik hasil ekstraksi dari file GPX"""
    distance_km: float
    elevation_gain_m: int
    naismith_duration_hour: float
    average_grade_pct: float
    min_elevation: int
    max_elevation: int
    difficulty: str
    total_trackpoints: int = 0


@dataclass
class HikingRoute:
    """Representasi lengkap jalur pendakian"""
    id: int
    name: str
    mountain: str
    stats: GPXStatistics
    manual_description: str
    narrative_text: str
    embedding: List[float] = field(default_factory=list)
    route_coordinates: List[List[float]] = field(default_factory=list)


def parse_gpx(file_path: str) -> Tuple[object, List[TrackPoint]]:
    """
    Parse file GPX dan ekstrak track points.
    
    Args:
        file_path: Path ke file GPX
        
    Returns:
        Tuple berisi objek GPX dan list TrackPoint
    """
    import gpxpy
    
    with open(file_path, 'r', encoding='utf-8') as gpx_file:
        gpx = gpxpy.parse(gpx_file)
    
    # Apply smoothing untuk memperbaiki data elevasi
    gpx.smooth(vertical=True, horizontal=False)
    
    points = []
    for track in gpx.tracks:
        for segment in track.segments:
            for point in segment.points:
                points.append(TrackPoint(
                    lat=point.latitude,
                    lon=point.longitude,
                    ele=point.elevation if point.elevation else 0,
                    time=point.time
                ))
    
    return gpx, points


def calculate_statistics(gpx, points: List[TrackPoint]) -> GPXStatistics:
    """
    Hitung statistik pendakian dari data GPX.
    
    Rumus yang digunakan:
    - Jarak: gpx.length_3d() dalam kilometer
    - Elevasi: Σ (eleᵢ - eleᵢ₋₁) untuk eleᵢ > eleᵢ₋₁
    - Naismith: (Jarak/5) + (Elevasi/600) jam
    - Grade: (Elevasi / (Jarak × 1000)) × 100 persen
    
    Args:
        gpx: Objek GPX
        points: List TrackPoint
        
    Returns:
        GPXStatistics dengan hasil perhitungan
    """
    # Jarak tempuh 3D dalam kilometer
    distance_km = gpx.length_3d() / 1000.0
    
    # Total kenaikan elevasi
    elevation_gain = 0
    for i in range(1, len(points)):
        ele_diff = points[i].ele - points[i-1].ele
        if ele_diff > 0:
            elevation_gain += ele_diff
    
    # Rumus Naismith: (Jarak_KM / 5) + (Gain_Meter / 600)
    naismith_duration = (distance_km / 5) + (elevation_gain / 600)
    
    # Rumus Grade (%): (Gain / (Jarak × 1000)) × 100
    average_grade = (elevation_gain / (distance_km * 1000)) * 100 if distance_km > 0 else 0
    
    # Klasifikasi kesulitan berdasarkan grade
    if average_grade < 5:
        difficulty = "mudah"
    elif average_grade < 10:
        difficulty = "sedang"
    elif average_grade < 15:
        difficulty = "sulit"
    else:
        difficulty = "sangat sulit"
    
    # Elevasi minimum dan maksimum
    elevations = [p.ele for p in points if p.ele > 0]
    min_elevation = int(min(elevations)) if elevations else 0
    max_elevation = int(max(elevations)) if elevations else 0
    
    return GPXStatistics(
        distance_km=round(distance_km, 2),
        elevation_gain_m=int(elevation_gain),
        naismith_duration_hour=round(naismith_duration, 2),
        average_grade_pct=round(average_grade, 2),
        min_elevation=min_elevation,
        max_elevation=max_elevation,
        difficulty=difficulty,
        total_trackpoints=len(points)
    )


def generate_narrative(stats: GPXStatistics, manual_description: str = "") -> str:
    """
    Data Fusion: Konversi atribut numerik menjadi narasi tekstual.
    
    Proses ini menggabungkan:
    1. Deskripsi manual dari literatur/ulasan
    2. Narasi otomatis dari data numerik GPX
    
    Args:
        stats: Statistik hasil ekstraksi GPX
        manual_description: Deskripsi manual dari literatur
        
    Returns:
        Narasi gabungan untuk SBERT embedding
    """
    # Deskripsi jarak
    if stats.distance_km < 3:
        jarak_desc = "pendek"
    elif stats.distance_km < 7:
        jarak_desc = "sedang"
    else:
        jarak_desc = "panjang"
    
    # Deskripsi waktu
    if stats.naismith_duration_hour < 2:
        waktu_desc = "singkat"
    elif stats.naismith_duration_hour < 4:
        waktu_desc = "sedang"
    else:
        waktu_desc = "lama"
    
    # Deskripsi kemiringan
    if stats.average_grade_pct < 5:
        grade_desc = "landai dan ramah pemula"
    elif stats.average_grade_pct < 10:
        grade_desc = "menantang dengan kemiringan sedang"
    elif stats.average_grade_pct < 15:
        grade_desc = "curam dan membutuhkan stamina yang baik"
    else:
        grade_desc = "sangat curam dan berbahaya"
    
    # Deskripsi level pendaki
    if stats.difficulty == "mudah":
        level_desc = "pemula"
    elif stats.difficulty in ["sulit", "sangat sulit"]:
        level_desc = "berpengalaman"
    else:
        level_desc = "dengan pengalaman menengah"
    
    # Generate narasi otomatis
    narrative = (
        f"Jalur pendakian dengan jarak {jarak_desc} sekitar {stats.distance_km} kilometer. "
        f"Estimasi waktu tempuh {waktu_desc} selama {stats.naismith_duration_hour} jam "
        f"menggunakan rumus Naismith. "
        f"Karakteristik jalur {grade_desc} dengan grade rata-rata {stats.average_grade_pct}%. "
        f"Total kenaikan elevasi {stats.elevation_gain_m} meter dengan ketinggian "
        f"dari {stats.min_elevation}m hingga {stats.max_elevation}m. "
        f"Tingkat kesulitan: {stats.difficulty}. "
        f"Cocok untuk pendaki {level_desc}."
    )
    
    # Gabungkan dengan deskripsi manual (Data Fusion)
    if manual_description:
        narrative = manual_description + " " + narrative
    
    return narrative


def display_data_fusion_results(routes: List[HikingRoute]):
    """Tampilkan hasil fusi data dalam format tabel"""
    print("\n📊 Tabel 1. Ringkasan Dataset Jalur Pendakian\n")
    print("-" * 100)
    print(f"{'No':<4} {'Nama Jalur':<25} {'Gunung':<15} {'Trackpoints':<12} {'Jarak (km)':<12}")
    print("-" * 100)
    
    for i, route in enumerate(routes, 1):
        print(f"{i:<4} {route.name:<25} {route.mountain:<15} "
              f"{route.stats.total_trackpoints:<12} {route.stats.distance_km:<12}")
    print("-" * 100)
    
    print("\n📊 Tabel 2. Atribut Hasil Ekstraksi Data GPX\n")
    print("-" * 90)
    print(f"{'Atribut':<25} {'Rumus/Metode':<40} {'Satuan':<15}")
    print("-" * 90)
    print(f"{'Jarak Tempuh':<25} {'gpx.length_3d()':<40} {'Kilometer':<15}")
    print(f"{'Kenaikan Elevasi':<25} {'Σ (eleᵢ - eleᵢ₋₁) untuk eleᵢ > eleᵢ₋₁':<40} {'Meter':<15}")
    print(f"{'Durasi Naismith':<25} {'(Jarak/5) + (Elevasi/600)':<40} {'Jam':<15}")
    print(f"{'Grade Rata-rata':<25} {'(Elevasi / (Jarak × 1000)) × 100':<40} {'Persen':<15}")
    print("-" * 90)


# ================================================================================
# BATCH GPX UPLOAD & PROCESSING
# ================================================================================

def process_single_gpx(gpx_path: str, route_id: int, 
                       name: str = None, mountain: str = None,
                       manual_description: str = "") -> Optional[HikingRoute]:
    """
    Proses satu file GPX dan kembalikan HikingRoute.
    
    Args:
        gpx_path: Path ke file GPX
        route_id: ID untuk rute
        name: Nama jalur (opsional, default dari filename)
        mountain: Nama gunung (opsional)
        manual_description: Deskripsi manual dari literatur
        
    Returns:
        HikingRoute atau None jika gagal
    """
    try:
        print(f"   📍 Processing: {os.path.basename(gpx_path)}...")
        
        gpx, points = parse_gpx(gpx_path)
        
        if not points:
            print(f"   ⚠️ No trackpoints found in {gpx_path}")
            return None
        
        stats = calculate_statistics(gpx, points)
        
        # Generate name dari filename jika tidak disediakan
        if not name:
            name = os.path.basename(gpx_path).replace('.gpx', '').replace('_', ' ').title()
        
        if not mountain:
            mountain = name.split()[0] if name else "Unknown"
        
        narrative = generate_narrative(stats, manual_description)
        
        # Extract route coordinates untuk map display
        step = max(1, len(points) // 100)
        route_coords = [[p.lat, p.lon] for p in points[::step]]
        
        route = HikingRoute(
            id=route_id,
            name=name,
            mountain=mountain,
            stats=stats,
            manual_description=manual_description,
            narrative_text=narrative,
            route_coordinates=route_coords
        )
        
        print(f"   ✅ {name}: {stats.distance_km}km | {stats.elevation_gain_m}m | {stats.difficulty}")
        
        return route
        
    except Exception as e:
        print(f"   ❌ Error processing {gpx_path}: {str(e)}")
        return None


def batch_process_gpx_directory(directory: str, 
                                descriptions: Dict[str, str] = None) -> List[HikingRoute]:
    """
    Batch process semua file GPX dalam direktori.
    
    Args:
        directory: Path ke direktori berisi file GPX
        descriptions: Dictionary {filename: description} untuk deskripsi manual
        
    Returns:
        List HikingRoute yang berhasil diproses
    """
    print(f"\n📂 Batch Processing GPX dari: {directory}")
    print("=" * 60)
    
    if not os.path.exists(directory):
        print(f"❌ Directory tidak ditemukan: {directory}")
        return []
    
    # Find all GPX files
    gpx_files = []
    for file in os.listdir(directory):
        if file.lower().endswith('.gpx'):
            gpx_files.append(os.path.join(directory, file))
    
    if not gpx_files:
        print(f"⚠️ Tidak ada file GPX ditemukan di {directory}")
        return []
    
    print(f"📋 Ditemukan {len(gpx_files)} file GPX\n")
    
    routes = []
    descriptions = descriptions or {}
    
    for i, gpx_path in enumerate(gpx_files, 1):
        filename = os.path.basename(gpx_path)
        desc = descriptions.get(filename, "")
        
        route = process_single_gpx(gpx_path, route_id=i, manual_description=desc)
        if route:
            routes.append(route)
    
    print("\n" + "=" * 60)
    print(f"✅ Berhasil memproses {len(routes)}/{len(gpx_files)} file GPX")
    
    return routes


def batch_process_gpx_list(gpx_files: List[Dict]) -> List[HikingRoute]:
    """
    Batch process dari list file GPX dengan metadata.
    
    Args:
        gpx_files: List dictionary dengan format:
            [
                {
                    'path': 'path/to/file.gpx',
                    'name': 'Nama Jalur',
                    'mountain': 'Nama Gunung',
                    'description': 'Deskripsi manual'
                },
                ...
            ]
            
    Returns:
        List HikingRoute yang berhasil diproses
    """
    print(f"\n📂 Batch Processing {len(gpx_files)} file GPX")
    print("=" * 60)
    
    routes = []
    
    for i, gpx_info in enumerate(gpx_files, 1):
        route = process_single_gpx(
            gpx_path=gpx_info['path'],
            route_id=i,
            name=gpx_info.get('name'),
            mountain=gpx_info.get('mountain'),
            manual_description=gpx_info.get('description', '')
        )
        if route:
            routes.append(route)
    
    print("\n" + "=" * 60)
    print(f"✅ Berhasil memproses {len(routes)}/{len(gpx_files)} file GPX")
    
    return routes


def interactive_gpx_upload() -> List[HikingRoute]:
    """
    Mode interaktif untuk upload file GPX satu per satu.
    Cocok untuk Google Colab.
    
    Returns:
        List HikingRoute yang berhasil diproses
    """
    print("\n" + "=" * 60)
    print("📤 MODE UPLOAD GPX INTERAKTIF")
    print("=" * 60)
    
    routes = []
    route_id = 1
    
    while True:
        print(f"\n--- File GPX #{route_id} ---")
        gpx_path = input("Masukkan path file GPX (atau 'done' untuk selesai): ").strip()
        
        if gpx_path.lower() == 'done':
            break
        
        if not os.path.exists(gpx_path):
            print(f"❌ File tidak ditemukan: {gpx_path}")
            continue
        
        name = input("Nama jalur (Enter untuk auto): ").strip() or None
        mountain = input("Nama gunung (Enter untuk auto): ").strip() or None
        description = input("Deskripsi manual (Enter untuk skip): ").strip()
        
        route = process_single_gpx(
            gpx_path=gpx_path,
            route_id=route_id,
            name=name,
            mountain=mountain,
            manual_description=description
        )
        
        if route:
            routes.append(route)
            route_id += 1
    
    print(f"\n✅ Total {len(routes)} rute berhasil diproses")
    return routes


def export_routes_to_json(routes: List[HikingRoute], output_path: str):
    """
    Export routes ke file JSON untuk penyimpanan/sharing.
    
    Args:
        routes: List HikingRoute
        output_path: Path file output JSON
    """
    data = []
    for route in routes:
        route_dict = {
            'id': route.id,
            'name': route.name,
            'mountain': route.mountain,
            'stats': {
                'distance_km': route.stats.distance_km,
                'elevation_gain_m': route.stats.elevation_gain_m,
                'naismith_duration_hour': route.stats.naismith_duration_hour,
                'average_grade_pct': route.stats.average_grade_pct,
                'min_elevation': route.stats.min_elevation,
                'max_elevation': route.stats.max_elevation,
                'difficulty': route.stats.difficulty,
                'total_trackpoints': route.stats.total_trackpoints
            },
            'manual_description': route.manual_description,
            'narrative_text': route.narrative_text,
            'embedding': route.embedding,
            'route_coordinates': route.route_coordinates
        }
        data.append(route_dict)
    
    with open(output_path, 'w', encoding='utf-8') as f:
        json.dump(data, f, ensure_ascii=False, indent=2)
    
    print(f"✅ Exported {len(routes)} routes to {output_path}")


def import_routes_from_json(json_path: str) -> List[HikingRoute]:
    """
    Import routes dari file JSON.
    
    Args:
        json_path: Path file JSON
        
    Returns:
        List HikingRoute
    """
    with open(json_path, 'r', encoding='utf-8') as f:
        data = json.load(f)
    
    routes = []
    for item in data:
        stats = GPXStatistics(
            distance_km=item['stats']['distance_km'],
            elevation_gain_m=item['stats']['elevation_gain_m'],
            naismith_duration_hour=item['stats']['naismith_duration_hour'],
            average_grade_pct=item['stats']['average_grade_pct'],
            min_elevation=item['stats']['min_elevation'],
            max_elevation=item['stats']['max_elevation'],
            difficulty=item['stats']['difficulty'],
            total_trackpoints=item['stats'].get('total_trackpoints', 0)
        )
        
        route = HikingRoute(
            id=item['id'],
            name=item['name'],
            mountain=item['mountain'],
            stats=stats,
            manual_description=item['manual_description'],
            narrative_text=item['narrative_text'],
            embedding=item.get('embedding', []),
            route_coordinates=item.get('route_coordinates', [])
        )
        routes.append(route)
    
    print(f"✅ Imported {len(routes)} routes from {json_path}")
    return routes


# ================================================================================
# BAGIAN 2: PRA-PEMROSESAN DATA
# ================================================================================

print("\n" + "=" * 70)
print("BAGIAN 2: PRA-PEMROSESAN DATA")
print("=" * 70)

# Stopwords umum Bahasa Indonesia (47 kata)
STOPWORDS_ID = {
    'yang', 'dan', 'di', 'ke', 'dari', 'ini', 'itu', 'dengan', 'untuk', 'pada',
    'adalah', 'sebagai', 'dalam', 'juga', 'atau', 'ada', 'oleh', 'akan', 'sudah',
    'saya', 'kami', 'kita', 'mereka', 'dia', 'ia', 'anda', 'tersebut', 'dapat',
    'bisa', 'harus', 'telah', 'lalu', 'kemudian', 'serta', 'maupun', 'saat',
    'ketika', 'bila', 'kalau', 'jika', 'karena', 'agar', 'supaya', 'hingga',
    'sampai', 'antara', 'seperti', 'yaitu', 'yakni', 'bahwa', 'namun', 'tetapi'
}

# Kata-kata yang harus dipertahankan (20 kata)
PRESERVE_WORDS = {
    # Negasi (5 kata)
    'tidak', 'bukan', 'jangan', 'belum', 'tanpa',
    # Sifat jalur (6 kata)
    'mudah', 'sulit', 'curam', 'landai', 'panjang', 'pendek',
    # Sifat lain (7 kata)
    'tinggi', 'rendah', 'sejuk', 'panas', 'dingin', 'indah', 'bagus',
    # Level pendaki (5 kata)
    'pemula', 'berpengalaman', 'santai', 'menantang', 'ekstrem'
}


def preprocess_text(text: str, remove_stopwords: bool = True) -> str:
    """
    Preprocessing teks sesuai metodologi penelitian:
    
    1. Data Cleaning - Regex untuk hapus karakter non-ASCII, URL, whitespace
    2. Case Folding - Konversi ke lowercase
    3. Stopword Removal - Selektif (pertahankan negasi dan kata sifat krusial)
    4. NO STEMMING - Karena SBERT sensitif terhadap konteks
    
    Args:
        text: Teks input
        remove_stopwords: Flag untuk stopword removal
        
    Returns:
        Teks yang telah dipreprocessing
    """
    if not text:
        return ""
    
    # 1. Data Cleaning
    text = re.sub(r'https?://\S+|www\.\S+', '', text)  # Hapus URL
    text = re.sub(r'[^\w\s\-]', ' ', text)  # Hapus karakter spesial
    text = re.sub(r'\b\d+\b', '', text)  # Hapus angka berdiri sendiri
    text = re.sub(r'\s+', ' ', text).strip()  # Hapus whitespace berlebih
    
    # 2. Case Folding
    text = text.lower()
    
    # 3. Stopword Removal (selektif)
    if remove_stopwords:
        words = text.split()
        filtered_words = []
        for word in words:
            # Pertahankan kata penting
            if word in PRESERVE_WORDS:
                filtered_words.append(word)
            # Hapus stopword
            elif word not in STOPWORDS_ID:
                filtered_words.append(word)
        text = ' '.join(filtered_words)
    
    return text


def analyze_preprocessing(texts: List[str]) -> Dict:
    """
    Analisis hasil preprocessing untuk laporan.
    
    Args:
        texts: List teks original
        
    Returns:
        Dictionary berisi statistik preprocessing
    """
    stats_before = {
        'total_words': 0,
        'unique_words': set(),
        'avg_length': 0
    }
    
    stats_after = {
        'total_words': 0,
        'unique_words': set(),
        'avg_length': 0
    }
    
    for text in texts:
        # Before preprocessing
        words_before = text.split()
        stats_before['total_words'] += len(words_before)
        stats_before['unique_words'].update(words_before)
        
        # After preprocessing
        processed = preprocess_text(text, remove_stopwords=True)
        words_after = processed.split()
        stats_after['total_words'] += len(words_after)
        stats_after['unique_words'].update(words_after)
    
    n = len(texts)
    stats_before['avg_length'] = stats_before['total_words'] / n if n > 0 else 0
    stats_after['avg_length'] = stats_after['total_words'] / n if n > 0 else 0
    stats_before['unique_count'] = len(stats_before['unique_words'])
    stats_after['unique_count'] = len(stats_after['unique_words'])
    
    return {
        'before': stats_before,
        'after': stats_after,
        'reduction_pct': round((1 - stats_after['total_words'] / stats_before['total_words']) * 100, 2)
                         if stats_before['total_words'] > 0 else 0
    }


def display_preprocessing_results():
    """Tampilkan hasil preprocessing dalam format tabel"""
    print("\n📊 Tabel 4. Hasil Setiap Tahap Preprocessing\n")
    
    sample = "Jalur ini SANGAT curam!!! Cocok untuk pendaki https://link.com"
    
    print("-" * 80)
    print(f"{'Tahap':<20} {'Output':<60}")
    print("-" * 80)
    print(f"{'Original':<20} {sample:<60}")
    
    # Step 1: Data Cleaning
    step1 = re.sub(r'https?://\S+|www\.\S+', '', sample)
    step1 = re.sub(r'[^\w\s\-]', ' ', step1)
    step1 = re.sub(r'\s+', ' ', step1).strip()
    print(f"{'Data Cleaning':<20} {step1:<60}")
    
    # Step 2: Case Folding
    step2 = step1.lower()
    print(f"{'Case Folding':<20} {step2:<60}")
    
    # Step 3: Stopword Removal
    step3 = preprocess_text(sample, remove_stopwords=True)
    print(f"{'Stopword Removal':<20} {step3:<60}")
    print("-" * 80)
    
    print("\n📊 Tabel 5. Distribusi Kata pada Preprocessing\n")
    print("-" * 70)
    print(f"{'Kategori':<35} {'Jumlah':<10} {'Contoh':<25}")
    print("-" * 70)
    print(f"{'Stopwords (dihapus)':<35} {len(STOPWORDS_ID):<10} {'yang, dan, di, ke, dari':<25}")
    print(f"{'Kata Negasi (dipertahankan)':<35} {5:<10} {'tidak, bukan, jangan':<25}")
    print(f"{'Kata Sifat Krusial (dipertahankan)':<35} {15:<10} {'mudah, sulit, curam':<25}")
    print("-" * 70)


# ================================================================================
# BAGIAN 3: EKSTRAKSI FITUR DENGAN SBERT
# ================================================================================

print("\n" + "=" * 70)
print("BAGIAN 3: EKSTRAKSI FITUR DENGAN SBERT")
print("=" * 70)

# Global model cache
_sbert_model = None


def get_sbert_model():
    """
    Load model SBERT (cached untuk efisiensi).
    
    Model: paraphrase-multilingual-MiniLM-L12-v2
    - Arsitektur: Transformer 12 layers
    - Dimensi: 384
    - Bahasa: 50+ (termasuk Indonesia)
    """
    global _sbert_model
    
    if _sbert_model is None:
        from sentence_transformers import SentenceTransformer
        
        print("⏳ Loading SBERT model...")
        start_time = time.time()
        
        _sbert_model = SentenceTransformer(CONFIG['model_name'])
        
        load_time = time.time() - start_time
        print(f"✅ Model loaded in {load_time:.2f}s")
        print(f"   Embedding dimension: {_sbert_model.get_sentence_embedding_dimension()}")
    
    return _sbert_model


def generate_embedding(text: str) -> List[float]:
    """
    Generate dense vector 384 dimensi dari teks.
    
    Args:
        text: Teks input (sudah dipreprocessing)
        
    Returns:
        List float 384 dimensi
    """
    model = get_sbert_model()
    embedding = model.encode(text)
    return embedding.tolist()


def batch_generate_embeddings(texts: List[str], show_progress: bool = True) -> Tuple[List[List[float]], float]:
    """
    Generate embeddings untuk batch teks.
    
    Args:
        texts: List teks input
        show_progress: Tampilkan progress
        
    Returns:
        Tuple (list embeddings, total time)
    """
    model = get_sbert_model()
    
    start_time = time.time()
    embeddings = model.encode(texts, show_progress_bar=show_progress)
    total_time = time.time() - start_time
    
    return [emb.tolist() for emb in embeddings], total_time


def display_sbert_statistics(embeddings: List[List[float]], encoding_time: float):
    """Tampilkan statistik SBERT dalam format tabel"""
    import numpy as np
    
    print("\n📊 Tabel 7. Spesifikasi Model SBERT\n")
    print("-" * 60)
    print(f"{'Parameter':<35} {'Nilai':<25}")
    print("-" * 60)
    print(f"{'Nama Model':<35} {CONFIG['model_name']:<25}")
    print(f"{'Arsitektur':<35} {'Transformer (12 layers)':<25}")
    print(f"{'Dimensi Embedding':<35} {CONFIG['embedding_dim']:<25}")
    print(f"{'Ukuran Model':<35} {'~420 MB':<25}")
    print(f"{'Bahasa yang Didukung':<35} {'50+ (termasuk Indonesia)':<25}")
    print(f"{'Max Sequence Length':<35} {'128 tokens':<25}")
    print("-" * 60)
    
    print("\n📊 Tabel 8. Statistik Embedding yang Dihasilkan\n")
    print("-" * 60)
    print(f"{'Metrik':<35} {'Nilai':<25}")
    print("-" * 60)
    
    emb_array = np.array(embeddings)
    avg_norm = np.mean([np.linalg.norm(e) for e in emb_array])
    
    print(f"{'Jumlah Dokumen':<35} {len(embeddings):<25}")
    print(f"{'Dimensi Vektor':<35} {len(embeddings[0]) if embeddings else 0:<25}")
    print(f"{'Waktu Encoding Total':<35} {f'{encoding_time:.2f} detik':<25}")
    print(f"{'Rata-rata Waktu per Dokumen':<35} {f'{(encoding_time/len(embeddings)*1000):.2f} ms':<25}")
    print(f"{'Rentang Nilai Vektor':<35} {'-1.0 hingga 1.0':<25}")
    print(f"{'Norma Rata-rata':<35} {f'{avg_norm:.4f}':<25}")
    print("-" * 60)


# ================================================================================
# BAGIAN 4: KEMIRIPAN DAN PERANKINGAN
# ================================================================================

print("\n" + "=" * 70)
print("BAGIAN 4: KEMIRIPAN DAN PERANKINGAN")
print("=" * 70)


def calculate_cosine_similarity(vec1: List[float], vec2: List[float]) -> float:
    """
    Hitung Cosine Similarity antara dua vektor.
    
    Formula:
    Sim(Q, D) = (Q · D) / (||Q|| × ||D||)
    
    Args:
        vec1: Vektor query (Q)
        vec2: Vektor dokumen (D)
        
    Returns:
        Skor similarity (0-1)
    """
    from sklearn.metrics.pairwise import cosine_similarity
    import numpy as np
    
    v1 = np.array(vec1).reshape(1, -1)
    v2 = np.array(vec2).reshape(1, -1)
    
    return float(cosine_similarity(v1, v2)[0][0])


def search_routes(query: str, routes: List[HikingRoute], top_n: int = 5) -> List[Dict]:
    """
    Pencarian rute berdasarkan query pengguna.
    
    Proses:
    1. Preprocess query
    2. Generate query embedding
    3. Hitung Cosine Similarity dengan semua rute
    4. Ranking dan return Top-N
    
    Args:
        query: Query pengguna dalam bahasa alami
        routes: Database rute pendakian
        top_n: Jumlah rekomendasi
        
    Returns:
        List hasil rekomendasi dengan skor
    """
    # Preprocess query
    processed_query = preprocess_text(query, remove_stopwords=True)
    
    # Generate query embedding
    query_embedding = generate_embedding(processed_query)
    
    # Hitung similarity untuk semua rute
    results = []
    for route in routes:
        similarity = calculate_cosine_similarity(query_embedding, route.embedding)
        results.append({
            'route': route,
            'similarity': similarity,
            'percentage': round(similarity * 100, 2)
        })
    
    # Sort by similarity (descending)
    results.sort(key=lambda x: x['similarity'], reverse=True)
    
    return results[:top_n]


def evaluate_precision_at_k(results: List[Dict], relevant_func, k: int = 5) -> float:
    """
    Hitung Precision@K.
    
    Formula:
    Precision@K = (Jumlah item relevan dalam Top-K) / K
    
    Args:
        results: Hasil rekomendasi
        relevant_func: Fungsi untuk menentukan relevansi
        k: Nilai K
        
    Returns:
        Skor Precision@K
    """
    top_k = results[:k]
    relevant_count = sum(1 for r in top_k if relevant_func(r['route']))
    return relevant_count / k


def display_search_results(query: str, results: List[Dict], scenario_num: int):
    """Tampilkan hasil pencarian dalam format tabel"""
    print(f"\n📊 Tabel {8 + scenario_num}. Hasil Rekomendasi untuk Query {scenario_num}\n")
    print(f"Query: \"{query}\"\n")
    print("-" * 100)
    print(f"{'Rank':<6} {'Nama Jalur':<25} {'Kesulitan':<12} {'Jarak (km)':<12} "
          f"{'Elevasi (m)':<12} {'Skor':<10} {'%':<8}")
    print("-" * 100)
    
    for i, result in enumerate(results, 1):
        route = result['route']
        print(f"{i:<6} {route.name:<25} {route.stats.difficulty:<12} "
              f"{route.stats.distance_km:<12} {route.stats.elevation_gain_m:<12} "
              f"{result['similarity']:.4f}    {result['percentage']:.1f}%")
    print("-" * 100)


# ================================================================================
# BAGIAN 5: MAIN EXECUTION - DEMONSTRASI LENGKAP
# ================================================================================

def create_sample_dataset() -> List[HikingRoute]:
    """
    Buat dataset sample untuk demonstrasi.
    Data ini merepresentasikan jalur pendakian aktual.
    """
    sample_data = [
        {
            'name': 'Gunung Prau via Dieng',
            'mountain': 'Prau',
            'description': 'Jalur sunrise populer dengan padang rumput luas dan pemandangan golden sunrise yang menakjubkan. Vegetasi sabana dengan bukit bergelombang.',
            'stats': GPXStatistics(
                distance_km=3.5, elevation_gain_m=450,
                naismith_duration_hour=1.45, average_grade_pct=12.86,
                min_elevation=2000, max_elevation=2565,
                difficulty='mudah', total_trackpoints=1250
            )
        },
        {
            'name': 'Gunung Merbabu via Selo',
            'mountain': 'Merbabu',
            'description': 'Jalur dengan sabana luas dan pemandangan Merapi. Terdapat sumber air di Pos 2. Trek panjang namun pemandangan indah sepanjang perjalanan.',
            'stats': GPXStatistics(
                distance_km=12.0, elevation_gain_m=1200,
                naismith_duration_hour=4.4, average_grade_pct=10.0,
                min_elevation=1600, max_elevation=3142,
                difficulty='sedang', total_trackpoints=4500
            )
        },
        {
            'name': 'Gunung Sindoro via Kledung',
            'mountain': 'Sindoro',
            'description': 'Jalur curam dengan hutan pinus lebat. Membutuhkan stamina tinggi dan cocok untuk pendaki berpengalaman.',
            'stats': GPXStatistics(
                distance_km=8.0, elevation_gain_m=1400,
                naismith_duration_hour=3.93, average_grade_pct=17.5,
                min_elevation=1800, max_elevation=3153,
                difficulty='sangat sulit', total_trackpoints=3200
            )
        },
        {
            'name': 'Bukit Sikunir',
            'mountain': 'Sikunir',
            'description': 'Jalur pendek untuk sunrise dengan trek santai. Sangat cocok untuk pemula dan keluarga. Pemandangan golden sunrise spektakuler.',
            'stats': GPXStatistics(
                distance_km=1.5, elevation_gain_m=150,
                naismith_duration_hour=0.55, average_grade_pct=10.0,
                min_elevation=2200, max_elevation=2350,
                difficulty='mudah', total_trackpoints=580
            )
        },
        {
            'name': 'Gunung Sumbing via Garung',
            'mountain': 'Sumbing',
            'description': 'Trek panjang dan menantang dengan kawah sulfur. Terdapat air terjun dan hutan lebat. Membutuhkan persiapan matang.',
            'stats': GPXStatistics(
                distance_km=10.0, elevation_gain_m=1800,
                naismith_duration_hour=5.0, average_grade_pct=18.0,
                min_elevation=1400, max_elevation=3371,
                difficulty='sangat sulit', total_trackpoints=4100
            )
        },
        {
            'name': 'Gunung Andong via Sawit',
            'mountain': 'Andong',
            'description': 'Jalur pendek dengan pemandangan 4 gunung sekaligus. Cocok untuk pendaki pemula dengan trek landai.',
            'stats': GPXStatistics(
                distance_km=3.0, elevation_gain_m=350,
                naismith_duration_hour=1.18, average_grade_pct=11.67,
                min_elevation=1200, max_elevation=1726,
                difficulty='mudah', total_trackpoints=1100
            )
        },
        {
            'name': 'Gunung Ungaran via Mawar',
            'mountain': 'Ungaran',
            'description': 'Jalur dengan hutan pinus dan camping ground nyaman. Sumber air melimpah. Trek sedang cocok untuk latihan.',
            'stats': GPXStatistics(
                distance_km=5.5, elevation_gain_m=600,
                naismith_duration_hour=2.1, average_grade_pct=10.91,
                min_elevation=900, max_elevation=2050,
                difficulty='sedang', total_trackpoints=2100
            )
        },
        {
            'name': 'Gunung Lawu via Cemoro Sewu',
            'mountain': 'Lawu',
            'description': 'Jalur spiritual dengan banyak petilasan. Trek panjang dengan tangga batu. Sunrise dan sunset spektakuler.',
            'stats': GPXStatistics(
                distance_km=9.0, elevation_gain_m=1100,
                naismith_duration_hour=3.63, average_grade_pct=12.22,
                min_elevation=1800, max_elevation=3265,
                difficulty='sedang', total_trackpoints=3800
            )
        },
    ]
    
    routes = []
    for i, data in enumerate(sample_data, 1):
        narrative = generate_narrative(data['stats'], data['description'])
        route = HikingRoute(
            id=i,
            name=data['name'],
            mountain=data['mountain'],
            stats=data['stats'],
            manual_description=data['description'],
            narrative_text=narrative
        )
        routes.append(route)
    
    return routes


def run_full_demonstration():
    """
    Jalankan demonstrasi lengkap sistem rekomendasi.
    Mengikuti urutan metodologi penelitian.
    """
    print("\n" + "=" * 70)
    print("🏔️ DEMONSTRASI SISTEM REKOMENDASI RUTE PENDAKIAN")
    print("   Content-Based Filtering dengan SBERT dan Cosine Similarity")
    print("=" * 70)
    
    # ================================================
    # BAGIAN 1: Pengumpulan dan Fusi Data
    # ================================================
    print("\n" + "=" * 70)
    print("📁 BAGIAN 1: PENGUMPULAN DAN FUSI DATA")
    print("=" * 70)
    
    routes = create_sample_dataset()
    display_data_fusion_results(routes)
    
    print("\n📊 Tabel 3. Contoh Konversi Data Numerik ke Narasi\n")
    print("-" * 90)
    for route in routes[:2]:
        print(f"Jalur: {route.name}")
        print(f"Narasi: {route.narrative_text[:150]}...")
        print("-" * 90)
    
    # ================================================
    # BAGIAN 2: Pra-pemrosesan Data
    # ================================================
    print("\n" + "=" * 70)
    print("🔧 BAGIAN 2: PRA-PEMROSESAN DATA")
    print("=" * 70)
    
    display_preprocessing_results()
    
    # Analisis preprocessing
    narratives = [r.narrative_text for r in routes]
    prep_stats = analyze_preprocessing(narratives)
    
    print("\n📊 Tabel 6. Statistik Preprocessing pada Dataset\n")
    print("-" * 80)
    print(f"{'Metrik':<30} {'Sebelum':<15} {'Sesudah':<15} {'Perubahan':<15}")
    print("-" * 80)
    print(f"{'Total Kata':<30} {prep_stats['before']['total_words']:<15} "
          f"{prep_stats['after']['total_words']:<15} -{prep_stats['reduction_pct']}%")
    print(f"{'Kata Unik':<30} {prep_stats['before']['unique_count']:<15} "
          f"{prep_stats['after']['unique_count']:<15} -")
    print(f"{'Rata-rata Panjang Dokumen':<30} {prep_stats['before']['avg_length']:.1f} kata"
          f"      {prep_stats['after']['avg_length']:.1f} kata       -")
    print("-" * 80)
    
    # ================================================
    # BAGIAN 3: Ekstraksi Fitur dengan SBERT
    # ================================================
    print("\n" + "=" * 70)
    print("🧠 BAGIAN 3: EKSTRAKSI FITUR DENGAN SBERT")
    print("=" * 70)
    
    # Preprocess semua narasi
    processed_narratives = [preprocess_text(r.narrative_text, False) for r in routes]
    
    # Generate embeddings
    embeddings, encoding_time = batch_generate_embeddings(processed_narratives)
    
    # Assign embeddings ke routes
    for route, emb in zip(routes, embeddings):
        route.embedding = emb
    
    display_sbert_statistics(embeddings, encoding_time)
    
    # ================================================
    # BAGIAN 4: Kemiripan dan Perankingan
    # ================================================
    print("\n" + "=" * 70)
    print("🎯 BAGIAN 4: KEMIRIPAN DAN PERANKINGAN")
    print("=" * 70)
    
    print("\n📐 Rumus Cosine Similarity:")
    print("   Sim(Q, D) = (Q · D) / (||Q|| × ||D||)")
    print("   Dimana Q = vektor query, D = vektor dokumen, dimensi = 384")
    
    # Test queries berdasarkan skenario
    test_scenarios = [
        {
            'query': 'jalur mudah untuk pemula',
            'expected': 'mudah',
            'relevance_check': lambda r: r.stats.difficulty == 'mudah'
        },
        {
            'query': 'trek menantang elevasi tinggi',
            'expected': 'sulit/sangat sulit',
            'relevance_check': lambda r: r.stats.difficulty in ['sulit', 'sangat sulit']
        },
        {
            'query': 'pendakian singkat 2-3 jam',
            'expected': 'durasi < 3 jam',
            'relevance_check': lambda r: r.stats.naismith_duration_hour < 3
        },
        {
            'query': 'jalur landai sabana sunrise',
            'expected': 'grade rendah, sabana',
            'relevance_check': lambda r: r.stats.average_grade_pct < 15 and 
                              ('sabana' in r.manual_description.lower() or 
                               'sunrise' in r.manual_description.lower())
        }
    ]
    
    precision_results = []
    
    for i, scenario in enumerate(test_scenarios, 1):
        results = search_routes(scenario['query'], routes, top_n=5)
        display_search_results(scenario['query'], results, i)
        
        # Hitung Precision@K
        p3 = evaluate_precision_at_k(results, scenario['relevance_check'], k=3)
        p5 = evaluate_precision_at_k(results, scenario['relevance_check'], k=5)
        precision_results.append({'query': scenario['query'], 'p3': p3, 'p5': p5})
    
    # Tabel evaluasi Precision@K
    print("\n📊 Tabel 13. Hasil Evaluasi Precision@K\n")
    print("-" * 70)
    print(f"{'Skenario':<10} {'Query':<35} {'P@3':<10} {'P@5':<10}")
    print("-" * 70)
    
    total_p3, total_p5 = 0, 0
    for i, pr in enumerate(precision_results, 1):
        print(f"{i:<10} {pr['query'][:33]:<35} {pr['p3']:.2f}      {pr['p5']:.2f}")
        total_p3 += pr['p3']
        total_p5 += pr['p5']
    
    avg_p3 = total_p3 / len(precision_results)
    avg_p5 = total_p5 / len(precision_results)
    print("-" * 70)
    print(f"{'Rata-rata':<10} {'':<35} {avg_p3:.2f}      {avg_p5:.2f}")
    print("-" * 70)
    
    # ================================================
    # RINGKASAN
    # ================================================
    print("\n" + "=" * 70)
    print("📋 RINGKASAN HASIL PENELITIAN")
    print("=" * 70)
    
    print(f"""
    ✅ Dataset: {len(routes)} jalur pendakian
    ✅ Model SBERT: {CONFIG['model_name']}
    ✅ Dimensi Embedding: {CONFIG['embedding_dim']}
    ✅ Waktu Encoding: {encoding_time:.2f} detik
    ✅ Rata-rata Precision@3: {avg_p3:.2f}
    ✅ Rata-rata Precision@5: {avg_p5:.2f}
    """)
    
    return routes, precision_results


# ================================================================================
# ENTRY POINT
# ================================================================================

def run_with_gpx_files(gpx_directory: str = None, gpx_list: List[Dict] = None):
    """
    Jalankan sistem dengan file GPX aktual.
    
    Args:
        gpx_directory: Path ke direktori berisi file GPX
        gpx_list: List dictionary GPX dengan metadata
    """
    print("\n" + "=" * 70)
    print("🏔️ SISTEM REKOMENDASI RUTE PENDAKIAN - MODE GPX")
    print("=" * 70)
    
    # Load routes dari GPX
    if gpx_directory:
        routes = batch_process_gpx_directory(gpx_directory)
    elif gpx_list:
        routes = batch_process_gpx_list(gpx_list)
    else:
        routes = interactive_gpx_upload()
    
    if not routes:
        print("❌ Tidak ada rute yang berhasil diproses")
        return None, None
    
    # Display hasil
    display_data_fusion_results(routes)
    
    # Generate embeddings
    print("\n" + "=" * 70)
    print("🧠 GENERATING EMBEDDINGS")
    print("=" * 70)
    
    processed_narratives = [preprocess_text(r.narrative_text, False) for r in routes]
    embeddings, encoding_time = batch_generate_embeddings(processed_narratives)
    
    for route, emb in zip(routes, embeddings):
        route.embedding = emb
    
    display_sbert_statistics(embeddings, encoding_time)
    
    # Interactive search
    print("\n" + "=" * 70)
    print("🔍 MODE PENCARIAN INTERAKTIF")
    print("=" * 70)
    
    while True:
        query = input("\n🔍 Masukkan query (atau 'exit' untuk keluar): ").strip()
        
        if query.lower() == 'exit':
            break
        
        if query:
            results = search_routes(query, routes, top_n=5)
            display_search_results(query, results, 1)
    
    # Export option
    export = input("\n💾 Export ke JSON? (y/n): ").strip().lower()
    if export == 'y':
        output_path = input("Path output (default: routes_output.json): ").strip()
        output_path = output_path or "routes_output.json"
        export_routes_to_json(routes, output_path)
    
    return routes, None


def main():
    """Main entry point dengan menu pilihan"""
    print("""
╔══════════════════════════════════════════════════════════════════════════════╗
║  SISTEM REKOMENDASI RUTE PENDAKIAN GUNUNG                                    ║
║  Content-Based Filtering dengan SBERT dan Cosine Similarity                  ║
║                                                                              ║
║  Universitas Amikom Yogyakarta                                               ║
║  Prodi Sistem Informasi                                                      ║
╚══════════════════════════════════════════════════════════════════════════════╝
    """)
    
    print("📋 PILIH MODE OPERASI:")
    print("-" * 40)
    print("1. Demo dengan Data Sample (8 jalur)")
    print("2. Batch Process dari Direktori GPX")
    print("3. Upload GPX Interaktif (satu per satu)")
    print("4. Import dari File JSON")
    print("-" * 40)
    
    choice = input("\nPilihan (1-4): ").strip()
    
    if choice == '1':
        # Demo dengan sample data
        routes, results = run_full_demonstration()
        
    elif choice == '2':
        # Batch dari direktori
        gpx_dir = input("Masukkan path direktori GPX: ").strip()
        routes, results = run_with_gpx_files(gpx_directory=gpx_dir)
        
    elif choice == '3':
        # Upload interaktif
        routes, results = run_with_gpx_files()
        
    elif choice == '4':
        # Import dari JSON
        json_path = input("Masukkan path file JSON: ").strip()
        routes = import_routes_from_json(json_path)
        
        if routes:
            # Generate embeddings jika belum ada
            if not routes[0].embedding:
                print("\n⏳ Generating embeddings...")
                processed = [preprocess_text(r.narrative_text, False) for r in routes]
                embeddings, _ = batch_generate_embeddings(processed)
                for route, emb in zip(routes, embeddings):
                    route.embedding = emb
            
            # Interactive search
            while True:
                query = input("\n🔍 Masukkan query (atau 'exit'): ").strip()
                if query.lower() == 'exit':
                    break
                if query:
                    results = search_routes(query, routes, top_n=5)
                    display_search_results(query, results, 1)
    else:
        print("❌ Pilihan tidak valid")
        return
    
    print("\n✅ Selesai!")
    print("📁 Script ini dapat dijalankan di Google Colab atau environment lokal")


if __name__ == '__main__':
    main()
