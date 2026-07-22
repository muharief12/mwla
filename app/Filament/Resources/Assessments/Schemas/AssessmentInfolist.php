<?php

namespace App\Filament\Resources\Assessments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AssessmentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // TextEntry::make('member_id')
                //     ->numeric(),
                // TextEntry::make('purpose')
                //     ->placeholder('-'),
                // TextEntry::make('created_at')
                //     ->dateTime()
                //     ->placeholder('-'),
                // SECTION 1: METADATA UTAMA
                Section::make('Informasi Sesi Pengukuran')
                    ->schema([
                        TextEntry::make('member.student.name')
                            ->label('Nama Mahasiswa'),
                        TextEntry::make('purpose')
                            ->label('Tujuan Penilaian'),
                        TextEntry::make('created_at')
                            ->label('Tanggal Penilaian')
                            ->dateTime('d M Y, H:i'),
                        TextEntry::make('result.wl_category')
                            ->label('Kategori Beban Kerja')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'Rendah' => 'success',
                                'Sedang' => 'warning',
                                'Tinggi' => 'danger',
                                default  => 'gray',
                            }),
                    ])->columns(2),

                // SECTION 2: MATRIKS DETAIL KALKULASI NASA-TLX
                Section::make('Rincian Matriks Perhitungan NASA-TLX')
                    ->description('Berikut adalah rincian bobot (pilihan berpasangan) dan rating skala 0-100 untuk tiap dimensi.')
                    ->schema([
                        ViewEntry::make('calculated_matrix')
                            ->hiddenLabel()
                            ->view('filament.components.nasa-tlx-matrix'),
                    ]),

            ]);
    }
}
