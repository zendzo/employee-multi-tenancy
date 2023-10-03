<?php

namespace App\Filament\Resources\CountryResource\RelationManagers;

use App\Models\City;
use App\Models\State;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class EmployeesRelationManager extends RelationManager
{
  protected static string $relationship = 'employees';

  public function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Section::make("Country")
          ->schema([
            Forms\Components\Select::make('country_id')
              ->relationship(name: "country", titleAttribute: 'name')
              ->searchable()
              ->preload()
              ->afterStateUpdated(function (Set $set) {
                $set('state_id', null);
                $set('city_id', null);
              })
              ->live()
              ->required(),
            Forms\Components\Select::make('state_id')
              ->label('State')
              ->options(
                fn (Get $get): Collection => State::query()
                  ->where('country_id', $get('country_id'))
                  ->pluck('name', 'id')
              )
              ->afterStateUpdated(fn (Set $set) => $set('city_id', null))
              ->searchable()
              ->preload()
              ->live()
              ->required(),
            Forms\Components\Select::make('city_id')
              ->label('City')
              ->options(
                fn (Get $get): Collection => City::query()
                  ->where('state_id', $get('state_id'))
                  ->pluck('name', 'id')
              )
              ->searchable()
              ->preload()
              ->live()
              ->required(),
            Forms\Components\Select::make('department_id')
              ->label('Department')
              ->relationship('department', 'name')
              ->createOptionForm([
                Forms\Components\TextInput::make('name')
                  ->minLength(2)
                  ->maxLength(100)
                  ->required()
              ])
              ->searchable()
              ->preload()
              ->required()
              ->columnSpanFull(),
          ])->columns(3),
        Forms\Components\Section::make("User Name")
          ->description("Employee's Detail Name")
          ->schema([
            Forms\Components\TextInput::make('first_name')
              ->required()
              ->maxLength(255),
            Forms\Components\TextInput::make('last_name')
              ->required()
              ->maxLength(255),
          ])->columns(2),
        Forms\Components\Section::make("User Address")
          ->description("Employee Address")
          ->schema([
            Forms\Components\TextInput::make('address')
              ->required()
              ->maxLength(255),
            Forms\Components\TextInput::make('zip_code')
              ->required()
              ->maxLength(255),
          ])->columns(2),
        Forms\Components\Section::make("Dates Information")
          ->schema([
            Forms\Components\DatePicker::make('date_of_birth')
              ->required(),
            Forms\Components\DatePicker::make('date_hired')
              ->required()
          ])->columns(2),
      ]);
  }

  public function table(Table $table): Table
  {
    return $table
      ->recordTitleAttribute('first_name')
      ->columns([
        Tables\Columns\TextColumn::make('first_name'),
        Tables\Columns\TextColumn::make('last_name'),
        Tables\Columns\TextColumn::make('zip_code'),
        Tables\Columns\TextColumn::make('date_hired'),
      ])
      ->filters([
        //
      ])
      ->headerActions([
        Tables\Actions\CreateAction::make(),
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
        Tables\Actions\DeleteAction::make(),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
        ]),
      ]);
  }

  public function isReadOnly(): bool
  {
    return true;
  }
}
