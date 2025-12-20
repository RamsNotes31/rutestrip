#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Batch GPX Processing Script - Non-Interactive
Langsung memproses semua file GPX dalam direktori
"""

import os
import sys

# Set encoding untuk Windows
if sys.platform == 'win32':
    sys.stdout.reconfigure(encoding='utf-8')

# Import fungsi dari research_demo
from research_demo import (
    batch_process_gpx_directory,
    batch_generate_embeddings,
    preprocess_text,
    display_data_fusion_results,
    display_sbert_statistics,
    search_routes,
    display_search_results,
    export_routes_to_json
)

def run_batch_processing(gpx_directory: str, output_json: str = None):
    """
    Proses batch file GPX dari direktori.
    
    Args:
        gpx_directory: Path ke folder berisi file GPX
        output_json: Path untuk export hasil (opsional)
    """
    print("=" * 70)
    print("BATCH GPX PROCESSING")
    print(f"Directory: {gpx_directory}")
    print("=" * 70)
    
    # 1. Batch process GPX files
    routes = batch_process_gpx_directory(gpx_directory)
    
    if not routes:
        print("Tidak ada rute yang berhasil diproses!")
        return None
    
    # 2. Display hasil fusi data
    display_data_fusion_results(routes)
    
    # 3. Generate embeddings
    print("\n" + "=" * 70)
    print("GENERATING SBERT EMBEDDINGS")
    print("=" * 70)
    
    processed_narratives = [preprocess_text(r.narrative_text, False) for r in routes]
    embeddings, encoding_time = batch_generate_embeddings(processed_narratives, show_progress=True)
    
    for route, emb in zip(routes, embeddings):
        route.embedding = emb
    
    display_sbert_statistics(embeddings, encoding_time)
    
    # 4. Test queries
    print("\n" + "=" * 70)
    print("TEST QUERIES")
    print("=" * 70)
    
    test_queries = [
        "jalur mudah untuk pemula",
        "trek menantang elevasi tinggi",
        "pendakian singkat 2-3 jam",
        "jalur landai sabana sunrise"
    ]
    
    for i, query in enumerate(test_queries, 1):
        results = search_routes(query, routes, top_n=5)
        display_search_results(query, results, i)
    
    # 5. Export jika diminta
    if output_json:
        export_routes_to_json(routes, output_json)
    
    print("\n" + "=" * 70)
    print(f"SELESAI! Total {len(routes)} rute diproses.")
    print("=" * 70)
    
    return routes


if __name__ == '__main__':
    # Default directory - ubah sesuai kebutuhan
    GPX_DIR = r"c:\laragon\www\rutestrip\storage\app\public\gpx_files"
    OUTPUT_JSON = r"c:\laragon\www\rutestrip\python\output\routes_processed.json"
    
    # Buat output directory jika belum ada
    os.makedirs(os.path.dirname(OUTPUT_JSON), exist_ok=True)
    
    # Jalankan batch processing
    routes = run_batch_processing(GPX_DIR, OUTPUT_JSON)
