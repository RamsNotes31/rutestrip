<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hiking_routes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama gunung/jalur
            $table->string('gpx_file_path'); // Lokasi file GPX
            $table->float('distance_km')->nullable(); // Jarak dalam KM
            $table->integer('elevation_gain_m')->nullable(); // Elevasi dalam meter
            $table->float('naismith_duration_hour')->nullable(); // Estimasi waktu Naismith
            $table->float('average_grade_pct')->nullable(); // Grade kemiringan rata-rata (%)
            $table->text('narrative_text')->nullable(); // Narasi hasil generate Python
            $table->json('sbert_embedding')->nullable(); // Vektor embedding SBERT
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hiking_routes');
    }
};
