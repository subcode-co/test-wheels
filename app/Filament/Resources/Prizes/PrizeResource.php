<?php

namespace App\Filament\Resources\Prizes;

use App\Filament\Resources\Prizes\Pages\CreatePrize;
use App\Filament\Resources\Prizes\Pages\EditPrize;
use App\Filament\Resources\Prizes\Pages\ListPrizes;
use App\Models\Prize;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PrizeResource extends Resource
{
    protected static ?string $model = Prize::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationLabel = 'جوائز العجلة';

    protected static ?string $modelLabel = 'جائزة';

    protected static ?string $pluralModelLabel = 'جوائز العجلة';

    protected static string|\UnitEnum|null $navigationGroup = 'دولاب الحظ';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('الاسم')
                ->required()
                ->maxLength(255),
            TextInput::make('code')
                ->label('الكود')
                ->required()
                ->maxLength(50)
                ->unique(ignoreRecord: true),
            TextInput::make('probability_weight')
                ->label('وزن الاحتمال')
                ->numeric()
                ->default(1)
                ->minValue(1),
            TextInput::make('display_order')
                ->label('ترتيب العرض')
                ->numeric()
                ->default(0),
            ColorPicker::make('color')
                ->label('اللون'),
            Toggle::make('is_winner')
                ->label('يعتبر فوز (نعم/لا)')
                ->helperText('نعم = جائزة فوز، لا = مثل حظ أوفر')
                ->default(true),
            Toggle::make('is_active')
                ->label('نشط')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('السؤال / الجائزة')->searchable(),
                TextColumn::make('code')->label('الكود'),
                TextColumn::make('probability_weight')->label('وزن الاحتمال'),
                TextColumn::make('display_order')->label('الترتيب'),
                ColorColumn::make('color')->label('اللون'),
                IconColumn::make('is_winner')->label('فوز')->boolean(),
                IconColumn::make('is_active')->label('نشط')->boolean(),
            ])
            ->defaultSort('display_order')
            ->reorderable('display_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPrizes::route('/'),
            'create' => CreatePrize::route('/create'),
            'edit' => EditPrize::route('/{record}/edit'),
        ];
    }
}
