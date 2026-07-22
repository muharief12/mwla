<?php

namespace App\Filament\Resources\Assessments\Schemas;

use App\Models\Member;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\Slider\Enums\PipsMode;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class AssessmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    // STEP 1: METADATA ASSESSMENT
                    Wizard\Step::make('Informasi Sesi')
                        ->description('Pilih anggota dan nama tugas/sesi yang dievaluasi')
                        ->icon('heroicon-m-clipboard-document-check')
                        ->schema([
                            // Select::make('member_id')
                            //     ->relationship('member', 'name')
                            //     ->searchable()
                            //     ->required(),
                            // TextInput::make('title')
                            //     ->label('Judul / Nama Tugas')
                            //     ->placeholder('Contoh: Evaluasi Modul Practicum 1')
                            //     ->required(),
                            Select::make('member_id')
                                ->label('Nama')
                                ->relationship('member', 'id', modifyQueryUsing: fn($query) => $query->with('student'))
                                ->getOptionLabelFromRecordUsing(fn($record) => $record->student->name ?? '-')
                                ->default(function () {
                                    // Mencari ID Member yang memiliki student_id = ID User yang sedang login
                                    return Member::where('student_id', Auth::id())->value('id');
                                })
                                ->required(),
                            Select::make('purpose')
                                ->label('Tujuan Penilaian')
                                ->options([
                                    'perkuliahan' => 'Perkuliahan',
                                    'UTS' => 'UTS',
                                    'UAS' => 'UAS',
                                ])
                                ->default('perkuliahan'),
                        ]),

                    // STEP 2: PAIRWISE COMPARISON (15 PASANG BOBOT)
                    Wizard\Step::make('Perbandingan Berpasangan')
                        ->description('Pilih dimensi yang paling dominan dirasakan pada setiap pasangan')
                        ->icon('heroicon-m-scale')
                        ->schema([
                            Section::make('15 Pasangan Dimensi NASA-TLX')
                                ->description('Pilih salah satu dari 2 faktor/dimensi yang lebih penting pada setiap perbandingan berikut:')
                                ->schema(
                                    self::getPairwiseFields() // Helper method untuk generate 15 pasang
                                )->columns(1),
                        ]),

                    // STEP 3: RATING DIMENSI (6 SKALA 0-100)
                    Wizard\Step::make('Penilaian Rating Dimensi')
                        ->description('Berikan nilai 0 - 100 untuk masing-masing dimensi beban kerja')
                        ->icon('heroicon-m-adjustments-vertical')
                        ->schema([
                            Section::make('Petunjuk pengerjaan')
                                ->description('Berikan nilai antara 0 (Sangat Lemah) - 100 (Sangat Kuat) untuk masing-masing dimensi beban kerja mental berdasarkan yang Anda rasakan.')
                                ->schema([])
                                // ->contained(false)
                                ->columns(1),
                            Grid::make(2)->schema([
                                Slider::make('ratings.MD')
                                    ->label('Mental Demand (Kebutuhan Mental)')
                                    ->helperText('Seberapa besar aktivitas mental dan perseptual yang Anda butuhkan dalam menjalani perkuliahan Analisis Kelayakan Industri ?')
                                    ->range(minValue: 0, maxValue: 100)
                                    ->pips(
                                        PipsMode::Steps,
                                        density: 5,
                                    )->tooltips()
                                    ->step(5)->default(50)->required(),

                                Slider::make('ratings.PD')
                                    ->label('Physical Demand (Kebutuhan Fisik)')
                                    ->helperText('Seberapa besar aktivitas fisik yang Anda butuhkan dalam menjalani perkuliahan Analisis Kelayakan Industri ?')
                                    ->range(minValue: 0, maxValue: 100)
                                    ->pips(
                                        PipsMode::Steps,
                                        density: 5,
                                    )->tooltips()
                                    ->step(5)->default(50)->required(),

                                Slider::make('ratings.TD')
                                    ->label('Temporal Demand (Kebutuhan Waktu)')
                                    ->helperText('Seberapa besar tekanan waktu yang Anda rasakan dalam menjalani perkuliahan Analisis Kelayakan Industri ?')
                                    ->range(minValue: 0, maxValue: 100)
                                    ->pips(
                                        PipsMode::Steps,
                                        density: 5,
                                    )->tooltips()
                                    ->step(5)->default(50)->required(),

                                Slider::make('ratings.OP')
                                    ->label('Performance (Performansi Kerja)')
                                    ->helperText('Seberapa berhasil Anda mencapai target belajar dalam menjalani perkuliahan Analisis Kelayakan Industri ?')
                                    ->range(minValue: 0, maxValue: 100)
                                    ->pips(
                                        PipsMode::Steps,
                                        density: 5,
                                    )->tooltips()
                                    ->step(5)->default(50)->required(),

                                Slider::make('ratings.EF')
                                    ->label('Effort (Usaha)')
                                    ->helperText('Seberapa keras Anda belajar dalam menjalani perkuliahan Analisis Kelayakan Industri secara mental dan fisik?')
                                    ->range(minValue: 0, maxValue: 100)
                                    ->pips(
                                        PipsMode::Steps,
                                        density: 5,
                                    )->tooltips()
                                    ->step(5)->default(50)->required(),

                                Slider::make('ratings.FR')
                                    ->label('Frustration (Tingkat Frustrasi)')
                                    ->helperText('Seberapa tidak aman, putus asa, atau terganggu yang Anda rasakan dalam menjalani perkuliahan Analisis Kelayakan Industri?')
                                    ->range(minValue: 0, maxValue: 100)
                                    ->pips(
                                        PipsMode::Steps,
                                        density: 5,
                                    )->tooltips()
                                    ->step(5)->default(50)->required(),
                            ]),
                        ]),
                ])
                    ->columnSpanFull()
                    ->submitAction(new \Illuminate\Support\HtmlString('
                    <button type="submit" class="fi-btn fi-btn-size-md fi-btn-color-primary">
                        Simpan & Hitung WWL
                    </button>
                '))
            ]);
    }

    /**
     * Helper untuk generate 15 pasang Radio pilihan dimensi
     */
    protected static function getPairwiseFields(): array
    {
        $pairs = [
            1 => ['MD', 'PD'],
            2 => ['MD', 'TD'],
            3 => ['MD', 'OP'],
            4 => ['MD', 'EF'],
            5 => ['MD', 'FR'],
            6 => ['PD', 'TD'],
            7 => ['PD', 'OP'],
            8 => ['PD', 'EF'],
            9 => ['PD', 'FR'],
            10 => ['TD', 'OP'],
            11 => ['TD', 'EF'],
            12 => ['TD', 'FR'],
            13 => ['OP', 'EF'],
            14 => ['OP', 'FR'],
            15 => ['EF', 'FR'],
        ];

        $labels = [
            'MD' => 'Mental Demand',
            'PD' => 'Physical Demand',
            'TD' => 'Temporal Demand',
            'OP' => 'Performance',
            'EF' => 'Effort',
            'FR' => 'Frustration'
        ];

        // Definisi singkat masing-masing dimensi berdasarkan standar NASA-TLX
        // 'MD' => 'Aktivitas mental/berpikir, mengingat, atau menganalisis.',
        // 'PD' => 'Aktivitas fisik yang dikeluarkan (mengangkat, menggeser, merakit, dll).',
        // 'TD' => 'Tekanan waktu atau kecepatan dalam menyelesaikan tugas.',
        // 'OP' => 'Tingkat keberhasilan dalam mencapai target pekerjaan.',
        // 'EF' => 'Sebesar apa usaha fisik dan mental yang dikeluarkan untuk bekerja.',
        // 'FR' => 'Tingkat stres, cemas, putus asa, atau terganggu saat bekerja.',
        $definitions = [
            'MD' => 'beban berpikir dan perhatian (mental)',
            'PD' => 'beban fisik',
            'TD' => 'Tekanan waktu',
            'OP' => 'tekanan untuk mencapai hasil',
            'EF' => 'beban fisik dan mental yang dikeluarkan',
            'FR' => 'rasa tidak nyaman (frustrasi, stres)',
        ];

        $fields = [];
        foreach ($pairs as $index => $pair) {
            $dim1 = $pair[0];
            $dim2 = $pair[1];

            $fields[] = Radio::make("weight_comparisons.pair_{$index}")
                ->label("Pasangan {$index}")
                ->options([
                    $pair[0] => $labels[$pair[0]],
                    $pair[1] => $labels[$pair[1]],
                ])
                ->descriptions([
                    $dim1 => $definitions[$dim1],
                    $dim2 => $definitions[$dim2],
                ]);
            // ->inline()
            // ->required();
        }

        return $fields;
    }
}
