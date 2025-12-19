<?php
namespace Database\Seeders;

use App\Models\HikingRoute;
use Illuminate\Database\Seeder;

class MountainDescriptionSeeder extends Seeder
{
    /**
     * Deskripsi manual berdasarkan review Google Maps, platform hiking, dan pengalaman pendaki
     * Format: keyword => deskripsi (vegetasi, sumber air, panorama, tips)
     */
    protected $descriptions = [
        // Gunung Merbabu - Jawa Tengah
        'Merbabu via Selo'      => 'Jalur paling populer dengan akses mudah. Pemandangan sabana luas dan bunga edelweiss. Cocok untuk pemula karena cenderung landai. Tidak ada sumber air di jalur, wajib bawa persediaan cukup. Sunrise spot terbaik di Jawa Tengah. Tips: bisa berdebu saat kemarau.',

        'Merbabu via Suwanting' => 'Jalur eksotis dengan hutan pinus lebat dan lembah indah. Tingkat kesulitan tinggi dengan tanjakan terjal, cocok untuk pendaki berpengalaman. Sumber air terbatas (satu gentong besar). Sabana ikonik menanti di puncak.',

        'Merbabu via Thekelan'  => 'Jalur klasik dengan variasi trek lengkap: hutan, sabana, punggungan berbatu. Terdapat sumber mata air di Pos 1, 2, dan sebelum Pos 3 dengan pipa dan keran. Cocok untuk camping di Pos 3 atau 4.',

        // Gunung Semeru - Jawa Timur (Tertinggi di Jawa)
        'Semeru'                => 'Gunung tertinggi di Pulau Jawa (3.676 mdpl). Jalur melewati Ranu Kumbolo - danau surga di ketinggian 2.400 mdpl dengan sunrise spektakuler. Padang savana Oro-Oro Ombo dengan bunga lavender. Suhu sangat dingin hingga -4°C. Wajib booking online karena kuota terbatas. Gunung berapi aktif - waspadai gas beracun saat summit.',

        // Gunung Slamet - Jawa Tengah (Tertinggi ke-2 di Jawa)
        'Slamet via Bambangan'  => 'Jalur paling populer dengan awal landai melalui hutan tropis lebat, teduh dan sejuk. Tanjakan sadis mulai Pos 3. Sumber air hanya di Pos 5 - bawa persediaan cukup. Dari Pos 5 bisa lihat Sindoro-Sumbing. Puncak didominasi bebatuan vulkanik.',

        'Slamet via Dipajaya'   => 'Pemandangan indah melewati kebun sayur dan hutan pinus menyejukkan. Banyak trek tanjakan membutuhkan tenaga ekstra. Vegetasi hutan yang asri.',

        'Slamet via Guci'       => 'Melewati hutan pinus menuju tebing dan pepohonan dataran tinggi. Bunga edelweiss dapat ditemukan. Pos 4 Ranu Amreta cocok untuk camping, terlindungi angin. Potensi sunset dan lautan awan dari puncak.',

        // Gunung Sumbing - Jawa Tengah
        'Sumbing'               => 'Gunung kembar Sindoro dengan panorama 360°. Jalur curam dan menantang. Vegetasi hutan cemara dan sabana. Sunrise view ke arah Merapi-Merbabu. Cuaca sering berkabut, siapkan jaket tebal.',

        // Gunung Sindoro - Jawa Tengah
        'Sindoro'               => 'Kembar Sumbing dengan puncak berkawah. Jalur Kledung paling populer dengan vegetasi hutan pinus dan sabana. Sumber air tersedia di beberapa pos. Panorama gunung-gunung Jawa Tengah.',

        // Gunung Lawu - Jawa Tengah/Timur
        'Lawu via Cemoro Sewu'  => 'Jalur paling populer dengan 4 shelter. Vegetasi hutan cemara rindang. Banyak situs bersejarah dan mistis. Sumber air tersedia. View sunrise ke arah Gunung Wilis.',

        'Lawu via Cetho'        => 'Jalur alternatif melewati Candi Cetho. Lebih panjang tapi pemandangan indah. Hutan pinus asri dengan udara sejuk.',

        // Gunung Ciremai - Jawa Barat (Tertinggi di Jabar)
        'Ciremai'               => 'Gunung tertinggi Jawa Barat (3.078 mdpl). Jalur Apuy dan Linggarjati populer. Vegetasi hutan hujan tropis dengan berbagai satwa. Kawah luas di puncak. Sumber air terbatas, bawa persediaan cukup.',

        // Gunung Arjuno - Jawa Timur
        'Arjuno'                => 'Gunung dengan air terjun dan hutan pinus. Jalur Tretes favorit pendaki. Bisa kombinasi dengan Welirang. Vegetasi subalpine dengan padang rumput luas. Cuaca sering berkabut.',

        // Gunung Argopuro - Jawa Timur
        'Argopuro'              => 'Trek panjang 3-4 hari dengan hutan primer lebat. Satwa liar beragam termasuk lutung dan elang. Savana Cikasur yang luas. Trekking terjauh di Jawa - butuh fisik prima.',

        // Kawah Ijen - Jawa Timur
        'Ijen'                  => 'Kawah dengan blue fire fenomenal (terlihat jam 2-5 pagi). Danau kawah biru toska. Trek relatif mudah 3km. Wajib pakai masker karena gas belerang. Penambang belerang tradisional.',

        // Gunung Agung - Bali
        'Agung'                 => 'Gunung tertinggi dan tersucui di Bali (3.142 mdpl). Jalur Besakih paling populer melewati Pura Pasar Agung. Pemandangan Bali dari puncak. Trek curam dan berbatu. Wajib didampingi guide lokal.',

        // Gunung Gede-Pangrango
        'Gede'                  => 'Taman Nasional dengan keanekaragaman hayati tinggi. Jalur Cibodas populer dengan air terjun Cibeureum. Bunga Edelweiss dan Cantigi. Sumber air melimpah. Cuaca sering hujan dan berkabut.',

        // Gunung Guntur - Garut
        'Guntur'                => 'Gunung berapi aktif dengan trek curam. Vegetasi hutan pinus dan semak dataran tinggi. Kawah masih mengeluarkan asap. View ke arah kota Garut.',

        // Gunung Butak
        'Butak'                 => 'Gunung alternatif sebelum Arjuno. Savana luas cocok camping. Trek relatif landai untuk pemula. Sunrise view ke Arjuno-Welirang.',

        // Gunung Cikuray
        'Cikuray'               => 'Gunung tertinggi ke-2 di Jawa Barat. Jalur pemancar populer. Hutan pinus dan sabana. Camping ground luas di puncak. View 360° ke gunung-gunung Priangan.',

        // Krakatau
        'Krakatau'              => 'Gunung berapi legendaris. Akses via boat dari Anyer. Trek pendek tapi medan berbatu vulkanik. Pemandangan laut dan pulau-pulau sekitar. Pantau aktivitas vulkanik sebelum mendaki.',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $updated = 0;

        foreach ($this->descriptions as $keyword => $description) {
            // Cari route yang cocok dengan keyword
            $count = HikingRoute::where('name', 'LIKE', "%{$keyword}%")
                ->whereNull('description')
                ->orWhere('description', '')
                ->where('name', 'LIKE', "%{$keyword}%")
                ->update(['description' => $description]);

            if ($count > 0) {
                $this->command->info("✅ Updated {$count} routes matching '{$keyword}'");
                $updated += $count;
            }
        }

        // Update yang belum dapat dengan deskripsi generik
        $remaining = HikingRoute::whereNull('description')->orWhere('description', '')->get();
        foreach ($remaining as $route) {
            $genericDesc = $this->generateGenericDescription($route->name);
            $route->update(['description' => $genericDesc]);
            $this->command->info("📝 Generic description for: {$route->name}");
            $updated++;
        }

        $this->command->info("------------------------------");
        $this->command->info("Total updated: {$updated} routes");
    }

    private function generateGenericDescription($name): string
    {
        return "Jalur pendakian {$name}. Nikmati pemandangan alam pegunungan Indonesia yang memukau. Persiapkan fisik dengan baik dan bawa perlengkapan lengkap. Cek kondisi cuaca sebelum mendaki.";
    }
}
