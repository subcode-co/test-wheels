<?php

namespace App\Filament\Resources\Spins;

use App\Filament\Resources\Spins\Pages\ListSpins;
use App\Models\Spin;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SpinResource extends Resource
{
    protected static ?string $model = Spin::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationLabel = 'نتائج الدورات';

    protected static ?string $modelLabel = 'دورة';

    protected static ?string $pluralModelLabel = 'نتائج الدورات';

    protected static string|\UnitEnum|null $navigationGroup = 'دولاب الحظ';

    protected static ?string $slug = 'spins';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('participant.phone')->label('رقم الهاتف')->searchable()->sortable(),
                TextColumn::make('prize.name')->label('الجائزة')->searchable()->sortable(),
                TextColumn::make('created_at')->label('التاريخ')->dateTime('Y-m-d H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSpins::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
