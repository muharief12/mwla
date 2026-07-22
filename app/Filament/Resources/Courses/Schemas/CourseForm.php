<?php

namespace App\Filament\Resources\Courses\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('lecture_id')
                    ->label('Dosen Pengampu')
                    ->relationship('lecture', 'name')
                    ->required(),
                TextInput::make('title')
                    ->label('Mata Kuliah')
                    ->required(),
                Textarea::make('desc')
                    ->label('Deskripsi')
                    ->required()
                    ->columnSpanFull(),
                Select::make('year_period')
                    ->label('Tahun Ajaran')
                    ->options(array_combine(
                        range(date('Y') + 1, date('Y') - 10),
                        range(date('Y') + 1, date('Y') - 10)
                    ))
                    ->default(date('Y'))
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('semester')
                    ->options(['ganjil' => 'Ganjil', 'genap' => 'Genap'])
                    ->required(),
            ]);
    }
}
