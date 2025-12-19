<?php
namespace Database\Seeders;

use App\Models\HikingRoute;
use App\Services\PythonProcessorService;
use Illuminate\Database\Seeder;

class RegenerateSBERTSeeder extends Seeder
{
    /**
     * Regenerate SBERT embeddings using embedding_text
     * This calls Python processor to generate new embeddings
     */
    public function run(): void
    {
        $routes = HikingRoute::whereNotNull('embedding_text')->get();
        $total  = $routes->count();

        if ($total === 0) {
            $this->command->error("❌ No routes with embedding_text found!");
            $this->command->info("   Run first: php artisan db:seed --class=RegenerateEmbeddingsSeeder");
            return;
        }

        $this->command->info("🤖 Regenerating SBERT embeddings for {$total} routes...");
        $this->command->info("   Model: paraphrase-multilingual-MiniLM-L12-v2 (384 dim)");
        $this->command->newLine();

        $pythonService = app(PythonProcessorService::class);
        $updated       = 0;
        $errors        = 0;

        foreach ($routes as $index => $route) {
            try {
                // Get embedding for combined text
                $embedding = $pythonService->getQueryEmbedding($route->embedding_text);

                if (! empty($embedding)) {
                    $route->sbert_embedding = $embedding;
                    $route->save();

                    $progress = round((($index + 1) / $total) * 100);
                    $this->command->info("[{$progress}%] ✅ {$route->name}");
                    $updated++;
                } else {
                    $this->command->warn("[!] ⚠️ Empty embedding for: {$route->name}");
                    $errors++;
                }
            } catch (\Exception $e) {
                $this->command->error("[!] ❌ Error for {$route->name}: " . $e->getMessage());
                $errors++;
            }
        }

        $this->command->newLine();
        $this->command->info("------------------------------");
        $this->command->info("✅ Updated: {$updated} routes");
        if ($errors > 0) {
            $this->command->warn("⚠️  Errors: {$errors} routes");
        }
        $this->command->info("🎉 SBERT embeddings regenerated!");
    }
}
