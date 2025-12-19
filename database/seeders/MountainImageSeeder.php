<?php
namespace Database\Seeders;

use App\Models\HikingRoute;
use Illuminate\Database\Seeder;

class MountainImageSeeder extends Seeder
{
    /**
     * Mapping gambar gunung menggunakan Picsum (placeholder) + seed unik per gunung
     * Ini akan menghasilkan gambar berbeda untuk setiap gunung
     */
    protected $mountainSeeds = [
        'Agung'    => 1011, // Mountain landscape
        'Arjuno'   => 1018, // Forest mountain
        'Argopuro' => 1015, // River mountain
        'Krakatau' => 1043, // Volcanic
        'Butak'    => 1036, // Foggy mountain
        'Cikuray'  => 1039, // Green mountain
        'Ciremai'  => 1041, // Peak
        'Gede'     => 1047, // Tropical
        'Guntur'   => 1029, // Clouds
        'Ijen'     => 1044, // Lake
        'Lawu'     => 1035, // Sunrise
        'Merbabu'  => 1033, // Panoramic
        'Semeru'   => 1028, // Indonesia highest in Java
        'Sindoro'  => 1042, // Central Java twin
        'Slamet'   => 1040, // Central Java highest
        'Sumbing'  => 1059, // Central Java
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $updated = 0;

        foreach ($this->mountainSeeds as $mountainName => $seed) {
            // Gunakan Picsum dengan seed tertentu untuk konsistensi
            // Format: https://picsum.photos/seed/{seed}/800/400
            $imageUrl = "https://picsum.photos/seed/{$seed}/800/400";

            $count = HikingRoute::where('name', 'LIKE', "%{$mountainName}%")
                ->update(['image_path' => $imageUrl]);

            if ($count > 0) {
                $this->command->info("✅ Updated {$count} routes for Gunung {$mountainName}");
                $updated += $count;
            }
        }

        $this->command->info("------------------------------");
        $this->command->info("Total updated: {$updated} routes");
        $this->command->info("📸 Sumber: Picsum Photos (placeholder)");
    }
}
