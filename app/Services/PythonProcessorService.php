<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class PythonProcessorService
{
    private string $pythonPath;
    private string $scriptPath;
    private array $env;

    public function __construct()
    {
        // Sesuaikan path Python sesuai sistem
        $this->pythonPath = config('services.python.path', 'python');
        $this->scriptPath = base_path('python/processor.py');
        
        // Setup environment untuk memastikan Python menemukan packages user
        $userHome = getenv('USERPROFILE') ?: getenv('HOME') ?: '';
        $pythonUserSite = $userHome . '\AppData\Roaming\Python\Python314\site-packages';
        
        $this->env = array_merge($_SERVER, $_ENV, [
            'PYTHONPATH' => $pythonUserSite,
            'PYTHONUSERBASE' => $userHome . '\AppData\Roaming\Python',
            'PATH' => getenv('PATH'),
        ]);
    }

    /**
     * Create a configured process
     */
    private function createProcess(array $command): Process
    {
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
            $this->pythonPath,
            $this->scriptPath,
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
            $this->pythonPath,
            $this->scriptPath,
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
