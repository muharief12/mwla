<?php

namespace App\Filament\Resources\Assessments\Pages;

use App\Filament\Resources\Assessments\AssessmentResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateAssessment extends CreateRecord
{
    protected static string $resource = AssessmentResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // 1. Simpan Data Assessment Utama
        $assessment = static::getModel()::create([
            'member_id' => $data['member_id'],
            'purpose'     => $data['purpose'],
        ]);

        // 2. Simpan 15 Pasang Weight Comparisons
        $weightsCount = ['MD' => 0, 'PD' => 0, 'TD' => 0, 'OP' => 0, 'EF' => 0, 'FR' => 0];
        foreach ($data['weight_comparisons'] as $pairIndex => $chosenDimension) {
            $assessment->weight_comparisons()->create([
                'dimension_choosen' => $chosenDimension,
                'pair_number'      => (int) str_replace('pair_', '', $pairIndex),
            ]);

            // Increment frekuensi kemunculan dimensi
            if (isset($weightsCount[$chosenDimension])) {
                $weightsCount[$chosenDimension]++;
            }
        }

        // 3. Simpan 6 Raw Rating Scores & Hitung Total Weighted Score
        $totalWeightedScore = 0;
        if (!empty($data['ratings'])) {
            foreach ($data['ratings'] as $dimension => $rawScore) {
                // Simpan tepat 6 baris dengan dimension & raw_score
                $assessment->rating_scores()->create([
                    'dimension' => $dimension,
                    'raw_score' => $rawScore,
                ]);

                // Kalkulasi Produk: (Bobot Dimensi x Rating Dimensi)
                $totalWeightedScore += ($weightsCount[$dimension] * $rawScore);
            }
        }

        // 4. Hitung Skor Akhir WWL (Dibagi 15)
        $wwlScore = $totalWeightedScore / 15;

        // Tentukan Kategori Beban Kerja
        $category = match (true) {
            $wwlScore < 50  => 'Rendah',
            $wwlScore <= 79 => 'Sedang',
            default         => 'Tinggi',
        };

        // 5. Simpan Hasil ke Tabel `results`
        $assessment->result()->create([
            'total_weight_score' => $totalWeightedScore,
            'wwl_score' => $wwlScore,
            'wl_category' => $category,
        ]);

        return $assessment;
    }
}
