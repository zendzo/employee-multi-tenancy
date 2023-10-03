<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CityResource\Pages;
use App\Filament\Resources\CityResource\RelationManagers;
use App\Models\City;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CityResource extends Resource
{
  protected static ?string $model = City::class;

  protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

  protected static ?string $navigationLabel = 'City';

  protected static ?string $navigationGroup = 'System Management';

  protected static ?string $modelLabel = 'City';

  protected static ?int $navigationSort = 3;

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Select::make('state_id')
          ->relationship('state', 'name')
          ->searchable()
          ->preload()
          ->required(),
        Forms\Components\TextInput::make('name')
          ->required()
          ->maxLength(255),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('name')
          ->label('City')
          ->sortable()
          ->searchable(),
        Tables\Columns\TextColumn::make('state.name')
          ->searchable(isIndividual: true)
          ->sortable(),
        Tables\Columns\TextColumn::make('created_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make('updated_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->filters([
        //
      ])
      ->actions([
        Tables\Actions\ViewAction::make(),
        Tables\Actions\EditAction::make(),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
        ]),
      ]);
  }

  public static function infolist(Infolist $infolist): Infolist
  {
    return $infolist
      ->schema([
        Section::make("City Information")
        ->schema([
          TextEntry::make('state.name')
            ->label('State')
            ->icon('heroicon-o-flag')
            ->size(TextEntry\TextEntrySize::Large),
          TextEntry::make('name')->label('City')
        ])->columns(2)
      ]);
  }

  public static function getRelations(): array
  {
    return [
      CountryResource\RelationManagers\EmployeesRelationManager::class,
    ];
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListCities::route('/'),
      'create' => Pages\CreateCity::route('/create'),
      'view' => Pages\ViewCity::route('/{record}'),
      'edit' => Pages\EditCity::route('/{record}/edit'),
    ];
  }
}
