#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Test Query - Output ke File"""

import sys
if sys.platform == 'win32':
    sys.stdout.reconfigure(encoding='utf-8')

from research_demo import import_routes_from_json, search_routes

# Import routes
routes = import_routes_from_json("output/routes_processed.json")

# Output file
with open("output/test_results.txt", "w", encoding="utf-8") as f:
    queries = [
        ("jalur mudah untuk pemula", "Jalur dengan kesulitan mudah"),
        ("trek menantang elevasi tinggi", "Jalur sulit dengan elevasi tinggi"),
        ("pendakian singkat 2-3 jam", "Jalur dengan durasi pendek"),
        ("gunung dengan sabana dan sunrise", "Jalur dengan pemandangan")
    ]
    
    for i, (query, expected) in enumerate(queries, 1):
        f.write(f"\n{'='*80}\n")
        f.write(f"QUERY {i}: \"{query}\"\n")
        f.write(f"Expected: {expected}\n")
        f.write(f"{'='*80}\n\n")
        
        results = search_routes(query, routes, top_n=5)
        
        f.write(f"{'Rank':<5} {'Nama':<45} {'Jarak':<8} {'Elevasi':<10} {'Durasi':<8} {'Kesulitan':<12} {'Skor':<8}\n")
        f.write("-" * 100 + "\n")
        
        for j, r in enumerate(results, 1):
            rt = r['route']
            name = rt.name.replace("1766", "").replace("_", " ")[:43]
            f.write(f"{j:<5} {name:<45} {rt.stats.distance_km:<8} "
                    f"{rt.stats.elevation_gain_m:<10} {rt.stats.naismith_duration_hour:<8} "
                    f"{rt.stats.difficulty:<12} {r['similarity']:.4f}\n")
        
        f.write("\n")

print("Results saved to output/test_results.txt")
