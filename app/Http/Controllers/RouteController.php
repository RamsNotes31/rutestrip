<?php
namespace App\Http\Controllers;

use App\Models\HikingRoute;
use App\Services\PythonProcessorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RouteController extends Controller
{
    private PythonProcessorService $pythonService;

    public function __construct(PythonProcessorService $pythonService)
    {
        $this->pythonService = $pythonService;
    }

    /**
     * Display a listing of hiking routes
     */
    public function index()
    {
        $routes = HikingRoute::latest()->paginate(12);
        return view('routes.index', compact('routes'));
    }

    /**
     * Show the form for uploading a new GPX file
     */
    public function create()
    {
        return view('routes.create');
    }

    /**
     * Store a newly created hiking route
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'gpx_file' => 'required|file|max:10240', // Max 10MB
        ]);

        // Validate file extension
        $gpxFile   = $request->file('gpx_file');
        $extension = strtolower($gpxFile->getClientOriginalExtension());
        if (! in_array($extension, ['gpx', 'xml'])) {
            return back()->withErrors(['gpx_file' => 'File harus berformat GPX atau XML.'])->withInput();
        }

        // Store GPX file
        $fileName = time() . '_' . $gpxFile->getClientOriginalName();
        $filePath = $gpxFile->storeAs('gpx_files', $fileName, 'public');
        $fullPath = storage_path('app/public/' . $filePath);

        try {
            // Process GPX with Python
            $result = $this->pythonService->ingest($fullPath);

            // Create hiking route record
            $route = HikingRoute::create([
                'name'                   => $request->name,
                'gpx_file_path'          => $filePath,
                'distance_km'            => $result['distance_km'] ?? null,
                'elevation_gain_m'       => $result['elevation_gain_m'] ?? null,
                'naismith_duration_hour' => $result['naismith_duration_hour'] ?? null,
                'average_grade_pct'      => $result['average_grade_pct'] ?? null,
                'narrative_text'         => $result['narrative_text'] ?? null,
                'sbert_embedding'        => $result['embedding'] ?? null,
            ]);

            return redirect()->route('routes.index')
                ->with('success', 'Jalur pendakian berhasil ditambahkan!');

        } catch (\Exception $e) {
            // Delete uploaded file if processing fails
            Storage::disk('public')->delete($filePath);

            return back()->withErrors(['gpx_file' => 'Gagal memproses file GPX: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified hiking route
     */
    public function show(HikingRoute $route)
    {
        return view('routes.show', compact('route'));
    }

    /**
     * Remove the specified hiking route
     */
    public function destroy(HikingRoute $route)
    {
        // Delete GPX file
        Storage::disk('public')->delete($route->gpx_file_path);

        $route->delete();

        return redirect()->route('routes.index')
            ->with('success', 'Jalur pendakian berhasil dihapus!');
    }

    /**
     * Show the form for batch uploading GPX files
     */
    public function createBatch()
    {
        return view('routes.batch');
    }

    /**
     * Store multiple hiking routes from batch upload
     */
    public function storeBatch(Request $request)
    {
        $request->validate([
            'gpx_files'   => 'required|array|min:1|max:50',
            'gpx_files.*' => 'required|file|max:10240',
        ]);

        // Additional validation for file extensions
        $allowedExtensions = ['gpx', 'xml'];
        foreach ($request->file('gpx_files') as $index => $file) {
            $extension = strtolower($file->getClientOriginalExtension());
            if (! in_array($extension, $allowedExtensions)) {
                return back()->withErrors([
                    'gpx_files' => "File #{$index}: {$file->getClientOriginalName()} bukan file GPX/XML yang valid.",
                ])->withInput();
            }
        }

        $results = [
            'success' => [],
            'failed'  => [],
        ];

        foreach ($request->file('gpx_files') as $gpxFile) {
            $originalName = $gpxFile->getClientOriginalName();
            $routeName    = pathinfo($originalName, PATHINFO_FILENAME);

            // Store GPX file
            $fileName = time() . '_' . uniqid() . '_' . $originalName;
            $filePath = $gpxFile->storeAs('gpx_files', $fileName, 'public');
            $fullPath = storage_path('app/public/' . $filePath);

            try {
                // Process GPX with Python
                $result = $this->pythonService->ingest($fullPath);

                if (! isset($result['success']) || ! $result['success']) {
                    throw new \Exception($result['error'] ?? 'Unknown error');
                }

                // Create hiking route record
                HikingRoute::create([
                    'name'                   => $routeName,
                    'gpx_file_path'          => $filePath,
                    'distance_km'            => $result['distance_km'] ?? null,
                    'elevation_gain_m'       => $result['elevation_gain_m'] ?? null,
                    'naismith_duration_hour' => $result['naismith_duration_hour'] ?? null,
                    'average_grade_pct'      => $result['average_grade_pct'] ?? null,
                    'narrative_text'         => $result['narrative_text'] ?? null,
                    'sbert_embedding'        => $result['embedding'] ?? null,
                ]);

                $results['success'][] = $originalName;

            } catch (\Exception $e) {
                // Delete uploaded file if processing fails
                Storage::disk('public')->delete($filePath);
                $results['failed'][] = [
                    'file'  => $originalName,
                    'error' => $e->getMessage(),
                ];
            }
        }

        $successCount = count($results['success']);
        $failedCount  = count($results['failed']);

        if ($failedCount === 0) {
            return redirect()->route('routes.index')
                ->with('success', "Berhasil mengupload {$successCount} file GPX!");
        } elseif ($successCount === 0) {
            return back()
                ->withErrors(['gpx_files' => 'Semua file gagal diproses.'])
                ->with('batch_results', $results);
        } else {
            return redirect()->route('routes.index')
                ->with('warning', "Berhasil: {$successCount} file, Gagal: {$failedCount} file.")
                ->with('batch_results', $results);
        }
    }
}
