<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\EmployeeResource\Pages;
use App\Filament\App\Resources\EmployeeResource\RelationManagers;
use App\Models\City;
use App\Models\Employee;
use App\Models\State;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
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
                ->relationship(
                  name: 'department', 
                  titleAttribute: 'name',
                  modifyQueryUsing: fn (Builder $query) => $query->whereBelongsTo(Filament::getTenant())
                  )
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

    public static function table(Table $table): Table
    {
    return $table
            ->columns([
              Tables\Columns\TextColumn::make('country.name')
              ->sortable(),
              Tables\Columns\TextColumn::make('state.name')
              ->sortable(),
              Tables\Columns\TextColumn::make('city.name')
              ->sortable(),
              Tables\Columns\TextColumn::make('department.name')
              ->sortable(),
              Tables\Columns\TextColumn::make('first_name')
              ->searchable(),
              Tables\Columns\TextColumn::make('last_name')
              ->searchable(),
              Tables\Columns\TextColumn::make('address')
              ->searchable(),
              Tables\Columns\TextColumn::make('zip_code')
              ->searchable(),
              Tables\Columns\TextColumn::make('date_of_birth')
              ->date()
                ->sortable(),
              Tables\Columns\TextColumn::make('date_hired')
              ->date()
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
                Tables\Filters\SelectFilter::make('department')
                ->relationship('department', 'name')
                  ->searchable()
                  ->preload(),
                Filter::make('created_at')
                ->form([
                  DatePicker::make('from'),
                  DatePicker::make('until'),
                ])
                  // ...
                  ->indicateUsing(function (array $data): array {
                    $indicators = [];

                    if ($data['from'] ?? null) {
                      $indicators['from'] = 'Created from ' . Carbon::parse($data['from'])->toFormattedDateString();
                    }

                    if ($data['until'] ?? null) {
                      $indicators['until'] = 'Created until ' . Carbon::parse($data['until'])->toFormattedDateString();
                    }

                    return $indicators;
                  })->columnSpan(2)->columns(2)
              ],
                layout: FiltersLayout::AboveContentCollapsible
              )->filtersFormColumns(3)
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
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'view' => Pages\ViewEmployee::route('/{record}'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }    
}
