<?php

namespace App\Filament\Resources\SpinParticipants;

use App\Filament\Resources\SpinParticipants\Pages\ListSpinParticipants;
use App\Models\SpinParticipant;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SpinParticipantResource extends Resource
{
    protected static ?string $model = SpinParticipant::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-phone';

    protected static ?string $navigationLabel = 'الهواتف المسجلة';

    protected static ?string $modelLabel = 'مشارك';

    protected static ?string $pluralModelLabel = 'الهواتف المسجلة';

    protected static string|\UnitEnum|null $navigationGroup = 'دولاب الحظ';

    protected static ?string $recordTitleAttribute = 'phone';

    protected static ?string $slug = 'spin-participants';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('phone')->label('رقم الهاتف')->searchable(),
                TextColumn::make('country_code')->label('رمز الدولة')->placeholder('—'),
                IconColumn::make('has_spun')
                    ->label('دور؟')
                    ->getStateUsing(fn (SpinParticipant $record): bool => $record->hasSpun())
                    ->boolean(),
                TextColumn::make('created_at')->label('تاريخ التسجيل')->dateTime('Y-m-d H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSpinParticipants::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
