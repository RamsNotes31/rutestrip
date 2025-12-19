<?php
namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Favorite;
use App\Models\HikingRoute;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Database\Seeder;

class DummyLikesSeeder extends Seeder
{
    /**
     * Create dummy likes, ratings, and comments for routes
     */
    public function run(): void
    {
        $routes = HikingRoute::all();
        $users  = User::where('role', 'user')->get();

        if ($users->isEmpty()) {
            $this->command->warn('⚠️ No users found. Creating dummy users...');

            // Create dummy users
            for ($i = 1; $i <= 10; $i++) {
                User::create([
                    'name' => "Pendaki {$i}",
                    'email' => "pendaki{$i}@example.com",
                    'password' => bcrypt('password'),
                    'role'     => 'user',
                ]);
            }

            $users = User::where('role', 'user')->get();
        }

        $this->command->info("🎲 Creating dummy data for {$routes->count()} routes...");

        $totalLikes    = 0;
        $totalRatings  = 0;
        $totalComments = 0;

        $comments = [
            'Jalur yang sangat indah! Pemandangan sunrise-nya luar biasa.',
            'Lumayan menantang tapi sepadan dengan viewnya.',
            'Cocok untuk pemula, tidak terlalu curam.',
            'Sumber air tersedia di beberapa pos.',
            'Hati-hati dengan cuaca, sering berkabut.',
            'Trek bagus, tapi bawa air yang cukup.',
            'Basecamp-nya nyaman, ada toilet dan warung.',
            'Pemandangan hutan pinusnya cantik sekali.',
            'Jalur terurus dengan baik.',
            'Perjalanan melelahkan tapi puas!',
            'Wajib coba untuk pecinta alam.',
            'Sunrise-nya tidak mengecewakan.',
            'Guide lokal sangat membantu.',
            'Fasilitas basecamp cukup lengkap.',
            'Trek yang menyenangkan untuk weekend.',
        ];

        foreach ($routes as $route) {
            // Random number of likes (3-15 per route)
            $likesCount    = rand(3, 15);
            $selectedUsers = $users->random(min($likesCount, $users->count()));

            foreach ($selectedUsers as $user) {
                Favorite::firstOrCreate([
                    'user_id'         => $user->id,
                    'hiking_route_id' => $route->id,
                ]);
                $totalLikes++;
            }

            // Random ratings (2-8 per route)
            $ratingsCount = rand(2, 8);
            $ratingUsers  = $users->random(min($ratingsCount, $users->count()));

            foreach ($ratingUsers as $user) {
                Rating::updateOrCreate(
                    ['user_id' => $user->id, 'hiking_route_id' => $route->id],
                    ['rating' => rand(3, 5)]// Rating 3-5 (mostly positive)
                );
                $totalRatings++;
            }

            // Random comments (1-3 per route)
            $commentsCount = rand(1, 3);
            $commentUsers  = $users->random(min($commentsCount, $users->count()));

            foreach ($commentUsers as $user) {
                Comment::create([
                    'user_id'         => $user->id,
                    'hiking_route_id' => $route->id,
                    'content'         => $comments[array_rand($comments)],
                ]);
                $totalComments++;
            }
        }

        $this->command->info("------------------------------");
        $this->command->info("✅ Created {$totalLikes} likes");
        $this->command->info("✅ Created {$totalRatings} ratings");
        $this->command->info("✅ Created {$totalComments} comments");
    }
}
