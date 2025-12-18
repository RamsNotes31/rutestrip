<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class PythonProcessorService
{
    private array $pythonCommand;
    private string $scriptPath;
    private array $env;

    public function __construct()
    {
        // Use py launcher with Python 3.11 for Windows compatibility
        // This avoids Python 3.14 compatibility issues with sentence-transformers
        $pythonPath = config('services.python.path', 'py');

        // Check if it's using py launcher (Windows)
        if ($pythonPath === 'py' || str_ends_with($pythonPath, 'py.exe')) {
            $this->pythonCommand = ['py', '-3.11'];
        } else {
            $this->pythonCommand = [$pythonPath];
        }

        $this->scriptPath = base_path('python/processor.py');

        // Setup environment
        $userHome = getenv('USERPROFILE') ?: getenv('HOME') ?: '';

        $this->env = array_merge($_SERVER, $_ENV, [
            'PATH'             => getenv('PATH'),
            'PYTHONIOENCODING' => 'utf-8',
        ]);
    }

    /**
     * Create a configured process
     */
    private function createProcess(array $additionalArgs): Process
    {
        $command = array_merge($this->pythonCommand, [$this->scriptPath], $additionalArgs);
        $process = new Process($command);
        $process->setEnv($this->env);
        $process->setWorkingDirectory(base_path('python'));
        return $process;
    }

    /**
     * Process GPX file and extract features + embeddings
     */
    public function ingest(string $gpxFilePath): array
    {
        $process = $this->createProcess([
            '--mode', 'ingest',
            '--gpx', $gpxFilePath,
        ]);

        $process->setTimeout(300); // 5 minutes timeout for SBERT model loading

        try {
            $process->mustRun();
            $output = $process->getOutput();
            $result = json_decode($output, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response from Python: ' . $output);
            }

            return $result;
        } catch (ProcessFailedException $exception) {
            Log::error('Python ingest failed: ' . $exception->getMessage());
            throw new \Exception('Failed to process GPX file: ' . $process->getErrorOutput());
        }
    }

    /**
     * Search similar routes based on query
     */
    public function search(string $query, array $routesData): array
    {
        $process = $this->createProcess([
            '--mode', 'search',
            '--query', $query,
            '--data', json_encode($routesData),
        ]);

        $process->setTimeout(120);

        try {
            $process->mustRun();
            $output = $process->getOutput();
            $result = json_decode($output, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response from Python: ' . $output);
            }

            return $result;
        } catch (ProcessFailedException $exception) {
            Log::error('Python search failed: ' . $exception->getMessage());
            throw new \Exception('Failed to search routes: ' . $process->getErrorOutput());
        }
    }
}
