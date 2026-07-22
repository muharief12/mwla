<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $table = 'assessments';
    protected $guarded = ['id'];

    public function rating_scores()
    {
        return $this->hasMany(RatingScore::class, 'assessment_id');
    }

    public function weight_comparisons()
    {
        return $this->hasMany(WeightComparison::class, 'assessment_id');
    }

    public function result()
    {
        return $this->hasOne(Result::class, 'assessment_id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'id');
    }

    /**
     * Mengambil matriks rincian kalkulasi NASA-TLX secara instan
     */
    public function getCalculatedMatrixAttribute(): array
    {
        // 1. Hitung frekuensi Bobot (Weight) per dimensi
        $weights = $this->weight_comparisons
            ->groupBy('dimension_choosen')
            ->map(fn($items) => $items->count());

        // 2. Ambil Rating dan hitung Produk (Weight x Rating)
        $matrix = [];
        $dimensions = ['MD', 'PD', 'TD', 'OP', 'EF', 'FR'];

        foreach ($dimensions as $dim) {
            $weight = $weights->get($dim, 0);
            $rawScore = $this->rating_scores->where('dimension', $dim)->first()?->raw_score ?? 0;
            $product = $weight * $rawScore;

            $matrix[$dim] = [
                'weight'    => $weight,
                'raw_score' => $rawScore,
                'product'   => $product, // Perkalian dihitung dinamis saat laporan diakses
            ];
        }

        return $matrix;
    }
}
