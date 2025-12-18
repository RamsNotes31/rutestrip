<?php
namespace App\Http\Controllers;

use App\Models\HikingRoute;
use App\Services\PythonProcessorService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    private PythonProcessorService $pythonService;

    public function __construct(PythonProcessorService $pythonService)
    {
        $this->pythonService = $pythonService;
    }

    /**
     * Show search form
     */
    public function index()
    {
        return view('search.index');
    }

    /**
     * Process search and show results
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:3|max:500',
        ]);

        $query = $request->input('query');

        // Get all routes with embeddings
        $routes = HikingRoute::whereNotNull('sbert_embedding')->get();

        if ($routes->isEmpty()) {
            return view('search.results', [
                'query'   => $query,
                'results' => collect([]),
                'message' => 'Belum ada data jalur pendakian. Silakan upload file GPX terlebih dahulu.'
            ]);
        }

        // Prepare data for Python
        $routesData = $routes->map(function ($route) {
            return [
                'id'        => $route->id,
                'embedding' => $route->sbert_embedding,
            ];
        })->toArray();

        try {
            // Get similarity scores from Python
            $searchResults = $this->pythonService->search($query, $routesData);

            // Map results with route data
            $results = collect($searchResults['results'] ?? [])->map(function ($item) use ($routes) {
                $route = $routes->firstWhere('id', $item['id']);
                if ($route) {
                    $route->similarity_score = round($item['score'] * 100, 1);
                    return $route;
                }
                return null;
            })->filter()->take(10);

            return view('search.results', [
                'query'   => $query,
                'results' => $results,
            ]);

        } catch (\Exception $e) {
            return back()->withErrors(['query' => 'Gagal melakukan pencarian: ' . $e->getMessage()])
                ->withInput();
        }
    }
}
