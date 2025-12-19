<?php
namespace Database\Seeders;

use App\Models\HikingRoute;
use Illuminate\Database\Seeder;

class RouteCoordinatesSeeder extends Seeder
{
    /**
     * Re-process existing GPX files to extract route coordinates
     */
    public function run(): void
    {
        $routes = HikingRoute::whereNull('route_coordinates')
            ->whereNotNull('gpx_file_path')
            ->get();

        $this->command->info("Found {$routes->count()} routes without coordinates");

        $success = 0;
        $failed  = 0;

        foreach ($routes as $route) {
            $fullPath = storage_path('app/public/' . $route->gpx_file_path);

            if (! file_exists($fullPath)) {
                $this->command->warn("❌ File not found: {$route->name}");
                $failed++;
                continue;
            }

            try {
                // Parse GPX file manually
                $gpxContent = file_get_contents($fullPath);
                $xml        = simplexml_load_string($gpxContent);

                if (! $xml) {
                    $this->command->warn("❌ Invalid GPX: {$route->name}");
                    $failed++;
                    continue;
                }

                $coordinates = [];

                // Extract track points
                foreach ($xml->trk as $track) {
                    foreach ($track->trkseg as $segment) {
                        foreach ($segment->trkpt as $point) {
                            $lat           = (float) $point['lat'];
                            $lon           = (float) $point['lon'];
                            $coordinates[] = [$lat, $lon];
                        }
                    }
                }

                if (empty($coordinates)) {
                    // Try route points
                    foreach ($xml->rte as $rte) {
                        foreach ($rte->rtept as $point) {
                            $lat           = (float) $point['lat'];
                            $lon           = (float) $point['lon'];
                            $coordinates[] = [$lat, $lon];
                        }
                    }
                }

                if (empty($coordinates)) {
                    // Try waypoints
                    foreach ($xml->wpt as $point) {
                        $lat           = (float) $point['lat'];
                        $lon           = (float) $point['lon'];
                        $coordinates[] = [$lat, $lon];
                    }
                }

                if (empty($coordinates)) {
                    $this->command->warn("❌ No coordinates found: {$route->name}");
                    $failed++;
                    continue;
                }

                // Simplify to max 100 points
                $step       = max(1, (int) (count($coordinates) / 100));
                $simplified = [];
                for ($i = 0; $i < count($coordinates); $i += $step) {
                    $simplified[] = $coordinates[$i];
                }

                // Update route
                $route->update(['route_coordinates' => $simplified]);

                $this->command->info("✅ {$route->name}: " . count($simplified) . " points");
                $success++;

            } catch (\Exception $e) {
                $this->command->error("❌ Error processing {$route->name}: " . $e->getMessage());
                $failed++;
            }
        }

        $this->command->info("------------------------------");
        $this->command->info("✅ Success: {$success}");
        $this->command->info("❌ Failed: {$failed}");
    }
}
