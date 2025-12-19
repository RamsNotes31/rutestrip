<?php
namespace Database\Seeders;

use App\Models\HikingRoute;
use Illuminate\Database\Seeder;

class RegenerateEmbeddingsSeeder extends Seeder
{
    /**
     * Regenerate all embeddings with combined text:
     * embedding_text = narrative_text + description + basecamp_address + facilities + tips
     */
    public function run(): void
    {
        $routes  = HikingRoute::all();
        $total   = $routes->count();
        $updated = 0;

        $this->command->info("🔄 Regenerating embeddings for {$total} routes...");
        $this->command->newLine();

        foreach ($routes as $index => $route) {
            // Combine all relevant text for embedding
            $textParts = [];

            // 1. AI-generated narrative (core for search)
            if ($route->narrative_text) {
                $textParts[] = $route->narrative_text;
            }

            // 2. Manual description (user reviews, vegetation, water sources)
            if ($route->description) {
                $textParts[] = $route->description;
            }

            // 3. Basecamp info (for location-based search)
            if ($route->basecamp_name) {
                $textParts[] = "Basecamp: " . $route->basecamp_name;
            }
            if ($route->basecamp_address) {
                $textParts[] = "Lokasi: " . $route->basecamp_address;
            }

            // 4. Facilities (for amenity-based search)
            if ($route->facilities) {
                $textParts[] = "Fasilitas: " . $route->facilities;
            }

            // 5. Tips (for practical search like "butuh masker gas")
            if ($route->tips) {
                $textParts[] = "Tips: " . $route->tips;
            }

            // 6. Best season (for time-based search)
            if ($route->best_season) {
                $textParts[] = "Musim terbaik: " . $route->best_season;
            }

            // Combine all text
            $combinedText = implode(". ", $textParts);

            // Update embedding_text field
            $route->embedding_text = $combinedText;
            $route->save();

            $progress = round((($index + 1) / $total) * 100);
            $this->command->info("[{$progress}%] ✅ {$route->name}");

            $updated++;
        }

        $this->command->newLine();
        $this->command->info("------------------------------");
        $this->command->info("✅ Updated embedding_text for {$updated} routes");
        $this->command->newLine();
        $this->command->warn("⚠️  To regenerate SBERT embeddings, run:");
        $this->command->info("   php artisan db:seed --class=RegenerateSBERTSeeder");
    }
}
