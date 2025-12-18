#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
RuteStrip - GPX Processor & SBERT Search Engine
================================================
Script untuk mengekstrak fitur dari file GPX dan melakukan pencarian berbasis kemiripan semantik.

Mode:
    - ingest: Process GPX file -> Extract features -> Generate embedding
    - search: Query text -> Calculate cosine similarity -> Return ranked results
"""

import argparse
import json
import sys
import os
import warnings

# Fix Windows network errors with HuggingFace Hub
os.environ['HF_HUB_DISABLE_SYMLINKS_WARNING'] = '1'
os.environ['HF_HUB_OFFLINE'] = '0'  # Try online first
os.environ['TRANSFORMERS_OFFLINE'] = '0'

# Disable SSL verification issues on some Windows systems
os.environ['CURL_CA_BUNDLE'] = ''
os.environ['REQUESTS_CA_BUNDLE'] = ''

warnings.filterwarnings('ignore')

# Lazy imports untuk performa
def get_gpxpy():
    import gpxpy
    return gpxpy

def get_sentence_transformer():
    try:
        from sentence_transformers import SentenceTransformer
        return SentenceTransformer
    except Exception as e:
        raise ImportError(f"Failed to import SentenceTransformer: {str(e)}")

def get_cosine_similarity():
    from sklearn.metrics.pairwise import cosine_similarity
    return cosine_similarity

import numpy as np

# Global model cache
_model = None

def get_model():
    """Load SBERT model (cached) with fallback options"""
    global _model
    if _model is None:
        try:
            SentenceTransformer = get_sentence_transformer()
            # Try loading the model
            _model = SentenceTransformer('sentence-transformers/paraphrase-multilingual-MiniLM-L12-v2')
        except Exception as e:
            error_msg = str(e)
            if 'WinError' in error_msg or '10106' in error_msg:
                # Windows network error - try offline mode
                os.environ['HF_HUB_OFFLINE'] = '1'
                os.environ['TRANSFORMERS_OFFLINE'] = '1'
                try:
                    SentenceTransformer = get_sentence_transformer()
                    _model = SentenceTransformer('sentence-transformers/paraphrase-multilingual-MiniLM-L12-v2')
                except Exception as e2:
                    raise Exception(f"Model loading failed. Please download model first by running: python -c \"from sentence_transformers import SentenceTransformer; SentenceTransformer('sentence-transformers/paraphrase-multilingual-MiniLM-L12-v2')\" - Error: {str(e2)}")
            else:
                raise Exception(f"Failed to load SBERT model: {error_msg}")
    return _model


def parse_gpx(file_path):
    """Parse GPX file and extract track points"""
    gpxpy = get_gpxpy()
    
    with open(file_path, 'r', encoding='utf-8') as gpx_file:
        gpx = gpxpy.parse(gpx_file)
    
    # Apply vertical smoothing untuk memperbaiki data elevasi
    gpx.smooth(vertical=True, horizontal=False)
    
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
    
    # Jika data waktu kosong, gunakan length_3d()
    has_time = any(p['time'] is not None for p in points)
    
    if has_time:
        distance_m = gpx.length_3d()
    else:
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


def generate_narrative(stats):
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
    
    return narrative


def generate_embedding(text):
    """Generate SBERT embedding for text"""
    model = get_model()
    embedding = model.encode(text)
    return embedding.tolist()


def mode_ingest(gpx_path):
    """Process GPX file and return statistics + embedding"""
    try:
        gpx, points = parse_gpx(gpx_path)
        
        if not points:
            raise ValueError("No track points found in GPX file")
        
        stats = calculate_statistics(gpx, points)
        narrative = generate_narrative(stats)
        embedding = generate_embedding(narrative)
        
        result = {
            'success': True,
            'distance_km': stats['distance_km'],
            'elevation_gain_m': stats['elevation_gain_m'],
            'naismith_duration_hour': stats['naismith_duration_hour'],
            'average_grade_pct': stats['average_grade_pct'],
            'narrative_text': narrative,
            'embedding': embedding
        }
        
        return result
        
    except Exception as e:
        return {
            'success': False,
            'error': str(e)
        }


def mode_search(query, routes_data):
    """Search similar routes based on query text"""
    try:
        cosine_similarity = get_cosine_similarity()
        
        # Generate query embedding
        query_embedding = generate_embedding(query)
        query_vector = np.array(query_embedding).reshape(1, -1)
        
        # Parse routes data
        if isinstance(routes_data, str):
            routes_data = json.loads(routes_data)
        
        results = []
        
        for route in routes_data:
            route_embedding = np.array(route['embedding']).reshape(1, -1)
            similarity = cosine_similarity(query_vector, route_embedding)[0][0]
            
            results.append({
                'id': route['id'],
                'score': float(similarity)
            })
        
        # Sort by similarity score (descending)
        results.sort(key=lambda x: x['score'], reverse=True)
        
        return {
            'success': True,
            'query': query,
            'results': results
        }
        
    except Exception as e:
        return {
            'success': False,
            'error': str(e)
        }


def main():
    parser = argparse.ArgumentParser(description='RuteStrip GPX Processor')
    parser.add_argument('--mode', required=True, choices=['ingest', 'search'],
                        help='Processing mode: ingest or search')
    parser.add_argument('--gpx', help='Path to GPX file (for ingest mode)')
    parser.add_argument('--query', help='Search query text (for search mode)')
    parser.add_argument('--data', help='JSON data of routes with embeddings (for search mode)')
    
    args = parser.parse_args()
    
    if args.mode == 'ingest':
        if not args.gpx:
            print(json.dumps({'success': False, 'error': 'GPX file path required for ingest mode'}))
            sys.exit(1)
        result = mode_ingest(args.gpx)
        
    elif args.mode == 'search':
        if not args.query or not args.data:
            print(json.dumps({'success': False, 'error': 'Query and data required for search mode'}))
            sys.exit(1)
        result = mode_search(args.query, args.data)
    
    print(json.dumps(result, ensure_ascii=False))


if __name__ == '__main__':
    main()
