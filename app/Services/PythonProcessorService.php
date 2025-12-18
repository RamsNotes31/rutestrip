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
        // Try to find Python 3.11 executable
        $pythonPath = $this->findPython311();

        if ($pythonPath) {
            $this->pythonCommand = [$pythonPath];
        } else {
            // Use full path to py launcher (more reliable from PHP)
            $this->pythonCommand = ['C:\\Windows\\py.exe', '-3.11'];
        }

        $this->scriptPath = base_path('python/processor.py');

        // Setup environment - copy all necessary Windows env vars
        $this->env = [
            'PATH'             => getenv('PATH'),
            'PYTHONIOENCODING' => 'utf-8',
            'USERPROFILE'      => getenv('USERPROFILE'),
            'LOCALAPPDATA'     => getenv('LOCALAPPDATA'),
            'APPDATA'          => getenv('APPDATA'),
            'HOME'             => getenv('USERPROFILE'),
            'SYSTEMROOT'       => getenv('SYSTEMROOT') ?: 'C:\\Windows',
            'PROGRAMFILES'     => getenv('PROGRAMFILES') ?: 'C:\\Program Files',
            'TEMP'             => getenv('TEMP') ?: sys_get_temp_dir(),
            'TMP'              => getenv('TMP') ?: sys_get_temp_dir(),
        ];
    }

    /**
     * Find Python 3.11 executable path
     */
    private function findPython311(): ?string
    {
        // Check common Python 3.11 locations
        $possiblePaths = [
            'C:\\Python311\\python.exe',
            'C:\\Python\\Python311\\python.exe',
            getenv('LOCALAPPDATA') . '\\Programs\\Python\\Python311\\python.exe',
            getenv('USERPROFILE') . '\\AppData\\Local\\Programs\\Python\\Python311\\python.exe',
        ];

        foreach ($possiblePaths as $path) {
            if ($path && file_exists($path)) {
                return $path;
            }
        }

        // Try using where command to find python3.11
        $process = new Process(['where', 'python']);
        $process->run();

        if ($process->isSuccessful()) {
            $paths = explode("\n", trim($process->getOutput()));
            foreach ($paths as $path) {
                $path = trim($path);
                if (strpos($path, '311') !== false || strpos($path, '3.11') !== false) {
                    return $path;
                }
            }
        }

        return null;
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
        // Write data to temp file to avoid command line length limits
        $tempFile = tempnam(sys_get_temp_dir(), 'rutestrip_search_');
        file_put_contents($tempFile, json_encode($routesData));

        try {
            $process = $this->createProcess([
                '--mode', 'search',
                '--query', $query,
                '--data-file', $tempFile,
            ]);

            $process->setTimeout(120);
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
        } finally {
            // Clean up temp file
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }
}
