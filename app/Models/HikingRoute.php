<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HikingRoute extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'gpx_file_path',
        'distance_km',
        'elevation_gain_m',
        'naismith_duration_hour',
        'average_grade_pct',
        'narrative_text',
        'sbert_embedding',
    ];

    protected $casts = [
        'distance_km'            => 'float',
        'elevation_gain_m'       => 'integer',
        'naismith_duration_hour' => 'float',
        'average_grade_pct'      => 'float',
        'sbert_embedding'        => 'array',
    ];

    /**
     * Get formatted distance
     */
    public function getFormattedDistanceAttribute(): string
    {
        return number_format($this->distance_km, 2) . ' km';
    }

    /**
     * Get formatted duration
     */
    public function getFormattedDurationAttribute(): string
    {
        $hours   = floor($this->naismith_duration_hour);
        $minutes = round(($this->naismith_duration_hour - $hours) * 60);
        return "{$hours} jam {$minutes} menit";
    }

    /**
     * Get formatted grade
     */
    public function getFormattedGradeAttribute(): string
    {
        return number_format($this->average_grade_pct, 1) . '%';
    }

    /**
     * Get difficulty level based on grade
     */
    public function getDifficultyLevelAttribute(): string
    {
        if ($this->average_grade_pct < 5) {
            return 'Mudah';
        }

        if ($this->average_grade_pct < 10) {
            return 'Sedang';
        }

        if ($this->average_grade_pct < 15) {
            return 'Sulit';
        }

        return 'Sangat Sulit';
    }
}
