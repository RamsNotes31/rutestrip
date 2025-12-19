<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hiking_routes', function (Blueprint $table) {
            $table->string('basecamp_name')->nullable()->after('description');
            $table->text('basecamp_address')->nullable()->after('basecamp_name');
            $table->decimal('basecamp_lat', 10, 7)->nullable()->after('basecamp_address');
            $table->decimal('basecamp_lng', 10, 7)->nullable()->after('basecamp_lat');
            $table->integer('entry_fee')->nullable()->after('basecamp_lng'); // In IDR
            $table->string('contact_phone')->nullable()->after('entry_fee');
            $table->text('facilities')->nullable()->after('contact_phone'); // JSON: toilet, warung, shelter, etc
            $table->string('best_season')->nullable()->after('facilities'); // Musim terbaik
            $table->text('tips')->nullable()->after('best_season');         // Tips pendakian
        });
    }

    public function down(): void
    {
        Schema::table('hiking_routes', function (Blueprint $table) {
            $table->dropColumn([
                'basecamp_name',
                'basecamp_address',
                'basecamp_lat',
                'basecamp_lng',
                'entry_fee',
                'contact_phone',
                'facilities',
                'best_season',
                'tips',
            ]);
        });
    }
};
