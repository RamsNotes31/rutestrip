<?php
namespace App\Http\Controllers;

use App\Models\HikingRoute;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    /**
     * Admin dashboard - Display all routes with SBERT details
     */
    public function dashboard()
    {
        $routes = HikingRoute::latest()->paginate(20);

        // Calculate statistics
        $stats = [
            'total_routes'          => HikingRoute::count(),
            'total_distance'        => HikingRoute::sum('distance_km'),
            'avg_elevation'         => HikingRoute::avg('elevation_gain_m'),
            'routes_with_embedding' => HikingRoute::whereNotNull('sbert_embedding')->count(),
        ];

        return view('admin.dashboard', compact('routes', 'stats'));
    }

    /**
     * Export all routes to CSV
     */
    public function exportCsv(): StreamedResponse
    {
        $routes = HikingRoute::all();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="rutestrip_dataset_' . date('Y-m-d_His') . '.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return response()->stream(function () use ($routes) {
            // Add UTF-8 BOM for Excel compatibility
            echo "\xEF\xBB\xBF";

            $handle = fopen('php://output', 'w');

            // Header row
            fputcsv($handle, [
                'id',
                'name',
                'gpx_file_path',
                'distance_km',
                'elevation_gain_m',
                'naismith_duration_hour',
                'average_grade_pct',
                'narrative_text',
                'sbert_embedding_dimensions',
                'sbert_embedding_preview',
                'sbert_embedding_full',
                'created_at',
                'updated_at',
            ]);

            foreach ($routes as $route) {
                $embedding           = $route->sbert_embedding;
                $embeddingDimensions = is_array($embedding) ? count($embedding) : 0;
                $embeddingPreview    = is_array($embedding) ? implode(',', array_slice($embedding, 0, 5)) . '...' : '';
                $embeddingFull       = is_array($embedding) ? json_encode($embedding) : '';

                fputcsv($handle, [
                    $route->id,
                    $route->name,
                    $route->gpx_file_path,
                    $route->distance_km,
                    $route->elevation_gain_m,
                    $route->naismith_duration_hour,
                    $route->average_grade_pct,
                    $route->narrative_text,
                    $embeddingDimensions,
                    $embeddingPreview,
                    $embeddingFull,
                    $route->created_at,
                    $route->updated_at,
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Export embeddings only to CSV (lighter file)
     */
    public function exportEmbeddings(): StreamedResponse
    {
        $routes = HikingRoute::whereNotNull('sbert_embedding')->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="rutestrip_embeddings_' . date('Y-m-d_His') . '.csv"',
        ];

        return response()->stream(function () use ($routes) {
            echo "\xEF\xBB\xBF";

            $handle = fopen('php://output', 'w');

            // Header: id, name, then 384 embedding columns
            $header = ['id', 'name', 'narrative_text'];
            for ($i = 0; $i < 384; $i++) {
                $header[] = "dim_{$i}";
            }
            fputcsv($handle, $header);

            foreach ($routes as $route) {
                $row = [
                    $route->id,
                    $route->name,
                    $route->narrative_text,
                ];

                $embedding = $route->sbert_embedding ?: [];
                for ($i = 0; $i < 384; $i++) {
                    $row[] = $embedding[$i] ?? 0;
                }

                fputcsv($handle, $row);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
