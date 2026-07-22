<?php

namespace App\Filament\Resources\Assessments\Pages;

use App\Filament\Resources\Assessments\AssessmentResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAssessment extends EditRecord
{
    protected static string $resource = AssessmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // 1. Ambil record Assessment yang sedang diedit beserta relasinya
        $assessment = $this->getRecord()->load(['weight_comparisons', 'rating_scores']);

        // 2. Isi state 'weight_comparisons' (misal: 'pair_1' => 'MD')
        $data['weight_comparisons'] = [];
        foreach ($assessment->weight_comparisons as $weight) {
            $data['weight_comparisons']["pair_{$weight->pair_number}"] = $weight->dimension_choosen;
        }

        // 3. Isi state 'ratings' (misal: 'MD' => 75)
        $data['ratings'] = [];
        foreach ($assessment->rating_scores as $rating) {
            $data['ratings'][$rating->dimension] = $rating->raw_score;
        }

        return $data;
    }

    /**
     * Memperbarui data relasi dan menghitung ulang WWL saat tombol Save diklik
     */
    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        // 1. Update data utama Assessment
        $record->update([
            'member_id' => $data['member_id'],
            'purpose'   => $data['purpose'] ?? 'perkuliahan',
        ]);

        // 2. Update/Sync 15 Pasang Weight Comparisons
        $weightsCount = ['MD' => 0, 'PD' => 0, 'TD' => 0, 'OP' => 0, 'EF' => 0, 'FR' => 0];

        if (!empty($data['weight_comparisons'])) {
            foreach ($data['weight_comparisons'] as $pairKey => $chosenDimension) {
                $pairNumber = (int) str_replace('pair_', '', $pairKey);

                $record->weight_comparisons()->updateOrCreate(
                    ['pair_number' => $pairNumber],
                    ['dimension_choosen' => $chosenDimension]
                );

                if (isset($weightsCount[$chosenDimension])) {
                    $weightsCount[$chosenDimension]++;
                }
            }
        }

        // 3. Update/Sync 6 Rating Scores & Hitung Total Weighted Score
        $totalWeightedScore = 0;

        if (!empty($data['ratings'])) {
            foreach ($data['ratings'] as $dimension => $rawScore) {
                $record->rating_scores()->updateOrCreate(
                    ['dimension' => $dimension],
                    ['raw_score' => $rawScore]
                );

                $totalWeightedScore += ($weightsCount[$dimension] * $rawScore);
            }
        }

        // 4. Hitung Ulang WWL Score & Kategori
        $wwlScore = $totalWeightedScore / 15;
        $category = match (true) {
            $wwlScore < 50  => 'Rendah',
            $wwlScore <= 79 => 'Sedang',
            default         => 'Tinggi',
        };

        // 5. Update Tabel Result
        $record->result()->updateOrCreate(
            ['assessment_id' => $record->id],
            [
                'total_weight_score' => $totalWeightedScore,
                'wwl_score'            => $wwlScore,
                'wl_category'   => $category,
            ]
        );

        return $record;
    }
}
