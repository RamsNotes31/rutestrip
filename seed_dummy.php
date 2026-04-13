<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\HikingRoute;
use App\Models\Comment;
use App\Models\Rating;
use App\Models\User;

// Ambil Mt Arjuno via Tretes (ambil yg ID paling baru spy aman)
$arjuno = HikingRoute::where('name', 'like', '%Arjuno via Tretes%')->orderBy('id', 'desc')->first();

if ($arjuno) {
    $arjuno->update([
        'basecamp_name' => 'Basecamp Tretes',
        'basecamp_lat' => -7.6974,
        'basecamp_lng' => 112.6312,
        'basecamp_address' => 'Jl. Panggungsari No.41, Tretes, Prigen, Pasuruan, Jawa Timur',
        'contact_phone' => '0821-3444-5555',
        'entry_fee' => 15000,
        'facilities' => 'Toilet, Warung Makan, Area Parkir Luas, Mushola, Shelter Penitipan Barang',
        'best_season' => 'Mei - September (Musim Kemarau)',
        'tips' => 'Jalur Tretes berstatus makadam bebatuan tiada ampun. Persiapkan pijakan dan sendi lutut. Sumber air melimpah di Pos 2 (Kokopan) dan Pos 3 (Pondokan). Bawa jaket tebal karena angin di Puncak Ogal-Agil sangat kencang.',
        'description' => 'Pendakian via Tretes merupakan jalur terpopuler dan tertua menuju puncak Arjuno maupun Welirang. Sepanjang perjalanan, pendaki akan disuguhkan jalan makadam peninggalan zaman dulu yang lebar namun berbatu tajam dengan kemiringan konstan yang cukup ekstrem. Terdapat pos persinggahan ikonik yaitu Pondokan yang menjadi tempat berkumpulnya para penambang belerang. Ini adalah rute favorit dengan suguhan pemandangan gunung kembar dan hutan cemara yang sangat asri.'
    ]);

    // Bikin dummy user
    $user1 = User::first() ?? User::factory()->create(['name' => 'Admin Rutestrip', 'email' => 'admin@rutestrip.test', 'password' => bcrypt('password')]);
    $user2 = User::where('email', 'pendaki@test.com')->first() ?? User::factory()->create(['name' => 'Pramuka Alam', 'email' => 'pendaki@test.com', 'password' => bcrypt('password')]);

    // Bikin rating & review
    Rating::updateOrCreate(
        ['user_id' => $user1->id, 'hiking_route_id' => $arjuno->id],
        ['rating' => 5]
    );

    Comment::updateOrCreate(
        ['user_id' => $user1->id, 'hiking_route_id' => $arjuno->id, 'content' => 'Jalurnya nanjak terus! Tapi pemandangan saat sunrise dari Puncak sangat indah.']
    );

    Rating::updateOrCreate(
        ['user_id' => $user2->id, 'hiking_route_id' => $arjuno->id],
        ['rating' => 4]
    );

    Comment::updateOrCreate(
        ['user_id' => $user2->id, 'hiking_route_id' => $arjuno->id, 'content' => 'Makadam tiada akhir, tapak kaki lumayan kepanasan kalau siang hari. Tapi ini rute paling aman dan sumber airnya jelas nyata. Recommended!']
    );

    // Bikin dummy likes
    \App\Models\Favorite::firstOrCreate(['user_id' => $user1->id, 'hiking_route_id' => $arjuno->id]);
    \App\Models\Favorite::firstOrCreate(['user_id' => $user2->id, 'hiking_route_id' => $arjuno->id]);
}

// Bikin dummy untuk sisanya (semua yang belum punya basecamp_name)
HikingRoute::whereNull('basecamp_name')->update([
    'basecamp_name' => 'Pos Pendakian Utama',
    'basecamp_address' => 'Pos Resmi Balai Taman Nasional Setempat',
    'contact_phone' => '0812-777-999',
    'entry_fee' => 20000,
    'facilities' => 'Area Parkir, Balai Pertemuan, Tempat Ibadah, Toilet, Toko Souvenir',
    'best_season' => 'April - Oktober (Kemarau)',
    'tips' => 'Hindari pendakian di malam hari untuk area hutan rapat. Selalu registrasi ulang saat naik dan turun gunung kepada petugas.',
    'description' => 'Ini adalah salah satu jalur pendakian yang menawarkan keindahan panorama alam di awal pendakian, dilanjutkan dengan jalur hutan hujan tropis yang lebat, dan diakhiri dengan sabana atau jalur bebatuan vulkanik menjelang kawasan puncak. Sangat direkomendasikan bagi pendaki yang mencari tantangan.'
]);

echo "Data berhasil disuntikkan!\n";
