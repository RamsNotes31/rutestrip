#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script untuk menambahkan deskripsi manual ke dataset
dan menjalankan ulang test query
"""

import sys
import json
if sys.platform == 'win32':
    sys.stdout.reconfigure(encoding='utf-8')

from research_demo import (
    import_routes_from_json,
    export_routes_to_json,
    generate_narrative,
    preprocess_text,
    batch_generate_embeddings,
    search_routes,
    GPXStatistics
)

# Deskripsi manual untuk gunung-gunung Indonesia
# Berdasarkan karakteristik aktual jalur pendakian
MANUAL_DESCRIPTIONS = {
    # Gunung dengan sabana dan sunrise
    "Merbabu": "Jalur dengan sabana luas dan pemandangan sunrise spektakuler. Padang rumput hijau sepanjang perjalanan dengan view Gunung Merapi.",
    "Prau": "Bukit dengan pemandangan golden sunrise dan padang sabana. Jalur pendek cocok untuk pemula dengan camping ground di puncak.",
    "Sindoro": "Gunung dengan hutan pinus lebat dan pemandangan sabana di puncak. Trek menantang dengan kawah sulfur.",
    "Sumbing": "Jalur pendakian menantang dengan sabana dan pemandangan matahari terbit. Vegetasi beragam dari hutan hingga padang rumput.",
    
    # Gunung dengan pemandangan indah
    "Lawu": "Jalur spiritual dengan petilasan dan pemandangan sunrise sunset spektakuler. Trek panjang dengan tangga batu.",
    "Semeru": "Gunung tertinggi di Jawa dengan pemandangan danau Ranu Kumbolo. Trek panjang dan menantang menuju puncak Mahameru.",
    "Arjuno": "Jalur pendakian dengan hutan pinus dan padang rumput. Pemandangan indah Gunung Welirang.",
    
    # Gunung dengan difficulty mudah
    "Agung": "Gunung suci Bali dengan pemandangan panorama pulau. Beberapa jalur dengan tingkat kesulitan berbeda.",
    "Ijen": "Kawah dengan api biru dan danau asam. Jalur relatif landai dengan pemandangan sunrise dari tepi kawah.",
    
    # Gunung dengan trek menantang
    "Argopuro": "Trek panjang multi-hari dengan savana luas dan hutan lebat. Jalur menantang untuk pendaki berpengalaman.",
    "Slamet": "Gunung tertinggi di Jawa Tengah dengan jalur curam. Terdapat sumber air di beberapa pos.",
    "Ciremai": "Gunung tertinggi di Jawa Barat dengan pemandangan dari dua sisi. Beberapa jalur dengan karakteristik berbeda.",
    
    # Gunung dengan sumber air
    "Ungaran": "Jalur dengan hutan pinus dan sumber air melimpah. Camping ground nyaman untuk latihan pendakian.",
    "Gede": "Gunung dengan air terjun dan pemandangan kawah. Trek bervariasi dari hutan hujan hingga padang edelweis.",
    "Pangrango": "Puncak kembar dengan Gede, jalur melewati hutan hujan lebat dengan sumber air.",
    
    # Gunung vulkanik aktif
    "Krakatau": "Gunung vulkanik dengan trekking singkat menuju puncak. Pemandangan laut dan pulau-pulau sekitar.",
    "Guntur": "Gunung aktif dengan kawah sulfur. Jalur pendakian dengan vegetasi beragam.",
    "Butak": "Gunung dengan pemandangan sabana dan sunrise. Trek sedang cocok untuk latihan pendakian."
}

def add_descriptions_to_routes():
    """Tambahkan deskripsi manual ke routes dan regenerate embeddings"""
    
    print("=" * 70)
    print("MENAMBAHKAN DESKRIPSI MANUAL KE DATASET")
    print("=" * 70)
    
    # Import routes
    routes = import_routes_from_json("output/routes_processed.json")
    print(f"\n📂 Loaded {len(routes)} routes")
    
    # Tambahkan deskripsi
    updated_count = 0
    for route in routes:
        # Cari nama gunung yang cocok
        for mountain_key, description in MANUAL_DESCRIPTIONS.items():
            if mountain_key.lower() in route.name.lower():
                # Update deskripsi dan regenerate narrative
                route.manual_description = description
                route.narrative_text = generate_narrative(route.stats, description)
                updated_count += 1
                print(f"   ✅ {route.name[:50]} <- {mountain_key}")
                break
    
    print(f"\n📝 Updated {updated_count}/{len(routes)} routes dengan deskripsi manual")
    
    # Regenerate embeddings
    print("\n⏳ Regenerating SBERT embeddings...")
    processed = [preprocess_text(r.narrative_text, False) for r in routes]
    embeddings, encoding_time = batch_generate_embeddings(processed, show_progress=True)
    
    for route, emb in zip(routes, embeddings):
        route.embedding = emb
    
    print(f"✅ Embeddings regenerated in {encoding_time:.2f}s")
    
    # Export
    export_routes_to_json(routes, "output/routes_with_descriptions.json")
    
    return routes


def test_improved_queries(routes):
    """Test query dengan dataset yang sudah ditingkatkan"""
    
    print("\n" + "=" * 70)
    print("TEST QUERY DENGAN DESKRIPSI MANUAL")
    print("=" * 70)
    
    queries = [
        ("jalur mudah untuk pemula", "mudah"),
        ("trek menantang elevasi tinggi", "elevasi tinggi"),
        ("pendakian singkat 2-3 jam", "durasi pendek"),
        ("gunung dengan sabana dan sunrise", "sabana/sunrise"),
        ("jalur dengan sumber air", "sumber air"),
        ("gunung dengan pemandangan kawah", "kawah")
    ]
    
    results_data = []
    
    for i, (query, expected) in enumerate(queries, 1):
        print(f"\n{'='*70}")
        print(f"QUERY {i}: \"{query}\"")
        print(f"Expected: {expected}")
        print("=" * 70)
        
        results = search_routes(query, routes, top_n=5)
        
        print(f"\n{'Rank':<5} {'Nama':<40} {'Jarak':<8} {'Kesulitan':<12} {'Skor':<8}")
        print("-" * 80)
        
        for j, r in enumerate(results, 1):
            rt = r['route']
            name = rt.name.split("_")[-1] if "_" in rt.name else rt.name
            name = name[:38]
            print(f"{j:<5} {name:<40} {rt.stats.distance_km:<8} "
                  f"{rt.stats.difficulty:<12} {r['similarity']:.4f}")
        
        # Show top result description
        top = results[0]['route']
        print(f"\n📝 Top 1 Description: {top.manual_description[:100]}..." if top.manual_description else "")
        
        results_data.append({
            'query': query,
            'top_score': results[0]['similarity'],
            'top_name': results[0]['route'].name
        })
    
    return results_data


if __name__ == '__main__':
    # Step 1: Tambah deskripsi
    routes = add_descriptions_to_routes()
    
    # Step 2: Test queries
    results = test_improved_queries(routes)
    
    # Summary
    print("\n" + "=" * 70)
    print("RINGKASAN PENINGKATAN")
    print("=" * 70)
    
    for r in results:
        print(f"  {r['query'][:35]:<35} -> Skor: {r['top_score']:.4f}")
    
    print("\n✅ Selesai!")
