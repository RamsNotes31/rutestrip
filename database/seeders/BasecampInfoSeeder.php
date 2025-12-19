<?php
namespace Database\Seeders;

use App\Models\HikingRoute;
use Illuminate\Database\Seeder;

class BasecampInfoSeeder extends Seeder
{
    /**
     * Data basecamp dari berbagai sumber (Google Maps, situs pendakian)
     */
    protected $basecampData = [
        // Gunung Merbabu
        'Merbabu via Selo'      => [
            'basecamp_name'    => 'Basecamp Selo',
            'basecamp_address' => 'Desa Selo, Kec. Selo, Boyolali, Jawa Tengah',
            'basecamp_lat'     => -7.4847,
            'basecamp_lng'     => 110.4328,
            'entry_fee'        => 15000,
            'contact_phone'    => '0276-325108',
            'facilities'       => 'Toilet, Warung, Parkir, Shelter, Penyewaan Tenda',
            'best_season'      => 'April - Oktober (Musim Kemarau)',
            'tips'             => 'Bawa air minimal 3L karena tidak ada sumber air di jalur. Siapkan jaket tebal untuk suhu malam yang bisa mencapai 5°C.',
        ],
        'Merbabu via Suwanting' => [
            'basecamp_name'    => 'Basecamp Suwanting',
            'basecamp_address' => 'Desa Suwanting, Kec. Selo, Boyolali, Jawa Tengah',
            'basecamp_lat'     => -7.4567,
            'basecamp_lng'     => 110.4156,
            'entry_fee'        => 15000,
            'contact_phone'    => '0812-2596-0000',
            'facilities'       => 'Toilet, Warung, Parkir Motor',
            'best_season'      => 'April - Oktober',
            'tips'             => 'Jalur curam cocok untuk pendaki berpengalaman. Ada sumber air terbatas di tengah jalur.',
        ],
        'Merbabu via Thekelan'  => [
            'basecamp_name'    => 'Basecamp Thekelan',
            'basecamp_address' => 'Desa Thekelan, Kec. Getasan, Semarang, Jawa Tengah',
            'basecamp_lat'     => -7.4402,
            'basecamp_lng'     => 110.4089,
            'entry_fee'        => 15000,
            'contact_phone'    => '0812-2599-1234',
            'facilities'       => 'Toilet, Warung, Parkir, Sumber Air di Pos 1-3',
            'best_season'      => 'April - Oktober',
            'tips'             => 'Sumber air tersedia di beberapa pos. Jalur klasik dengan variasi trek lengkap.',
        ],

        // Gunung Semeru
        'Semeru'                => [
            'basecamp_name'    => 'Basecamp Ranu Pani',
            'basecamp_address' => 'Desa Ranu Pani, Kec. Senduro, Lumajang, Jawa Timur',
            'basecamp_lat'     => -8.0172,
            'basecamp_lng'     => 112.9506,
            'entry_fee'        => 29000,
            'contact_phone'    => '0341-491828',
            'facilities'       => 'Toilet, Warung, Homestay, Parkir, Pos Registrasi TNBTS',
            'best_season'      => 'April - November',
            'tips'             => 'WAJIB booking online via simaksi.menlhk.go.id. Kuota terbatas 500 orang/hari. Bawa masker gas untuk summit.',
        ],

        // Gunung Slamet
        'Slamet via Bambangan'  => [
            'basecamp_name'    => 'Basecamp Bambangan',
            'basecamp_address' => 'Desa Bambangan, Kec. Karangreja, Purbalingga, Jawa Tengah',
            'basecamp_lat'     => -7.2833,
            'basecamp_lng'     => 109.2167,
            'entry_fee'        => 20000,
            'contact_phone'    => '0281-891168',
            'facilities'       => 'Toilet, Warung, Parkir, Shelter',
            'best_season'      => 'Mei - September',
            'tips'             => 'Sumber air hanya di Pos 5. Tanjakan sangat curam - siapkan fisik prima.',
        ],

        // Gunung Sindoro
        'Sindoro'               => [
            'basecamp_name'    => 'Basecamp Kledung',
            'basecamp_address' => 'Kledung Pass, Kec. Kledung, Temanggung, Jawa Tengah',
            'basecamp_lat'     => -7.2589,
            'basecamp_lng'     => 109.9942,
            'entry_fee'        => 15000,
            'contact_phone'    => '0293-491000',
            'facilities'       => 'Toilet, Warung, Parkir Luas, Camping Ground',
            'best_season'      => 'April - Oktober',
            'tips'             => 'Jalur pendek tapi curam. Pemandangan kembar Sumbing sangat indah.',
        ],

        // Gunung Sumbing
        'Sumbing'               => [
            'basecamp_name'    => 'Basecamp Garung',
            'basecamp_address' => 'Desa Garung, Kec. Garung, Wonosobo, Jawa Tengah',
            'basecamp_lat'     => -7.3667,
            'basecamp_lng'     => 110.0667,
            'entry_fee'        => 15000,
            'contact_phone'    => '0286-321000',
            'facilities'       => 'Toilet, Warung, Parkir, Penginapan',
            'best_season'      => 'April - Oktober',
            'tips'             => 'Cuaca sering berkabut. Bawa jaket tebal dan jas hujan.',
        ],

        // Gunung Lawu
        'Lawu via Cemoro Sewu'  => [
            'basecamp_name'    => 'Basecamp Cemoro Sewu',
            'basecamp_address' => 'Cemoro Sewu, Kec. Plaosan, Magetan, Jawa Timur',
            'basecamp_lat'     => -7.6247,
            'basecamp_lng'     => 111.1922,
            'entry_fee'        => 20000,
            'contact_phone'    => '0351-895000',
            'facilities'       => 'Toilet, Warung, Parkir Luas, Warung Makan 24 Jam',
            'best_season'      => 'April - November',
            'tips'             => 'Banyak situs mistis. Hormati tradisi lokal. 4 shelter tersedia.',
        ],
        'Lawu via Cetho'        => [
            'basecamp_name'    => 'Basecamp Candi Cetho',
            'basecamp_address' => 'Candi Cetho, Gumeng, Karanganyar, Jawa Tengah',
            'basecamp_lat'     => -7.5953,
            'basecamp_lng'     => 111.1561,
            'entry_fee'        => 20000,
            'contact_phone'    => '0271-495123',
            'facilities'       => 'Toilet, Warung, Parkir, Candi Cetho',
            'best_season'      => 'April - November',
            'tips'             => 'Jalur lebih panjang tapi pemandangan hutan pinus indah.',
        ],

        // Gunung Ciremai
        'Ciremai'               => [
            'basecamp_name'    => 'Basecamp Apuy',
            'basecamp_address' => 'Desa Apuy, Kec. Cigugur, Kuningan, Jawa Barat',
            'basecamp_lat'     => -6.8500,
            'basecamp_lng'     => 108.3833,
            'entry_fee'        => 25000,
            'contact_phone'    => '0232-875000',
            'facilities'       => 'Toilet, Warung, Parkir, Pos TNGC',
            'best_season'      => 'Mei - September',
            'tips'             => 'Gunung tertinggi Jawa Barat. Bawa air cukup - sumber air terbatas.',
        ],

        // Gunung Gede
        'Gede'                  => [
            'basecamp_name'    => 'Pintu Rimba Cibodas',
            'basecamp_address' => 'Kebun Raya Cibodas, Cipanas, Cianjur, Jawa Barat',
            'basecamp_lat'     => -6.7478,
            'basecamp_lng'     => 107.0047,
            'entry_fee'        => 30000,
            'contact_phone'    => '0263-512233',
            'facilities'       => 'Toilet, Warung, Parkir, Kantor TNGP, Shelter',
            'best_season'      => 'April - November',
            'tips'             => 'Kuota terbatas - booking online di simaksi. Air terjun Cibeureum bisa dikunjungi.',
        ],

        // Kawah Ijen
        'Ijen'                  => [
            'basecamp_name'    => 'Paltuding',
            'basecamp_address' => 'Paltuding, Kec. Licin, Banyuwangi, Jawa Timur',
            'basecamp_lat'     => -8.0581,
            'basecamp_lng'     => 114.2350,
            'entry_fee'        => 15000,
            'contact_phone'    => '0333-424172',
            'facilities'       => 'Toilet, Warung, Parkir, Penyewaan Masker Gas',
            'best_season'      => 'Sepanjang Tahun',
            'tips'             => 'Untuk blue fire datang jam 1-2 pagi. WAJIB pakai masker gas karena belerang.',
        ],

        // Gunung Agung
        'Agung'                 => [
            'basecamp_name'    => 'Pura Pasar Agung',
            'basecamp_address' => 'Pura Pasar Agung, Sebudi, Karangasem, Bali',
            'basecamp_lat'     => -8.3258,
            'basecamp_lng'     => 115.4875,
            'entry_fee'        => 100000,
            'contact_phone'    => '0363-23573',
            'facilities'       => 'Toilet, Warung, Parkir, Guide Wajib',
            'best_season'      => 'April - Oktober',
            'tips'             => 'WAJIB pakai guide lokal (Rp 600.000-1.000.000). Cek status aktivitas vulkanik sebelum mendaki.',
        ],

        // Gunung Arjuno
        'Arjuno'                => [
            'basecamp_name'    => 'Basecamp Tretes',
            'basecamp_address' => 'Tretes, Kec. Prigen, Pasuruan, Jawa Timur',
            'basecamp_lat'     => -7.7181,
            'basecamp_lng'     => 112.6267,
            'entry_fee'        => 20000,
            'contact_phone'    => '0343-631000',
            'facilities'       => 'Toilet, Warung, Parkir, Penginapan',
            'best_season'      => 'April - November',
            'tips'             => 'Bisa kombinasi dengan Gunung Welirang. Hutan pinus indah di awal jalur.',
        ],

        // Gunung Argopuro
        'Argopuro'              => [
            'basecamp_name'    => 'Basecamp Bremi',
            'basecamp_address' => 'Desa Bremi, Kec. Krucil, Probolinggo, Jawa Timur',
            'basecamp_lat'     => -7.9167,
            'basecamp_lng'     => 113.5833,
            'entry_fee'        => 20000,
            'contact_phone'    => '0335-421000',
            'facilities'       => 'Toilet, Warung Sederhana, Parkir',
            'best_season'      => 'Mei - September',
            'tips'             => 'Trek terpanjang di Jawa (3-4 hari). Persiapkan fisik dan logistik matang.',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $updated = 0;

        foreach ($this->basecampData as $keyword => $data) {
            $count = HikingRoute::where('name', 'LIKE', "%{$keyword}%")
                ->update($data);

            if ($count > 0) {
                $this->command->info("✅ Updated {$count} route(s) matching '{$keyword}'");
                $updated += $count;
            }
        }

        $this->command->info("------------------------------");
        $this->command->info("Total updated: {$updated} routes");
    }
}
