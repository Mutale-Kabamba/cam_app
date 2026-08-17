<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParishResource\Pages;
use App\Models\Parish;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ParishResource extends Resource
{
    protected static ?string $model = Parish::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $navigationLabel = 'Parishes & Contingents';

    protected static ?string $navigationGroup = 'Festival Operations';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Parish Identity')
                    ->schema([
                        Forms\Components\Select::make('name')
                            ->label('Parish Name')
                            ->options([
                                'Livingstone Deanery' => [
                                    "St. Theresa's Cathedral" => "St. Theresa's Cathedral (Livingstone)",
                                    "Christ the King Parish" => "Christ the King Parish (Maramba)",
                                    "Kazungula Parish" => "Kazungula Parish (Kazungula)",
                                    "Maria Regina Parish" => "Maria Regina Parish",
                                    "Our Lady of Angels Parish" => "Our Lady of Angels Parish",
                                    "St. Francis' Parish" => "St. Francis' Parish",
                                    "St. Peter's Parish" => "St. Peter's Parish",
                                    "St. Thomas the Apostle Parish" => "St. Thomas the Apostle Parish",
                                ],
                                'Sesheke Deanery' => [
                                    "St. Kizito's Sesheke Parish" => "St. Kizito's Sesheke Parish",
                                    "St. Fidelis' Sichili Parish" => "St. Fidelis' Sichili Parish",
                                    "St. Mary's Njoko Parish" => "St. Mary's Njoko Parish",
                                    "St. Arnold Janssen's Mwandi Parish" => "St. Arnold Janssen's Mwandi Parish",
                                    "Nawinda Parish" => "Nawinda Parish",
                                ],
                                'Sioma Deanery' => [
                                    "Lusu Parish" => "Lusu Parish",
                                    "Sioma Parish" => "Sioma Parish",
                                    "Shangombo Parish" => "Shangombo Parish",
                                ],
                            ])
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                $parishMap = [
                                    "St. Theresa's Cathedral" => ['code' => 'STC', 'deanery' => 'Livingstone Deanery'],
                                    "Christ the King Parish" => ['code' => 'CTK', 'deanery' => 'Livingstone Deanery'],
                                    "Kazungula Parish" => ['code' => 'KZP', 'deanery' => 'Livingstone Deanery'],
                                    "Maria Regina Parish" => ['code' => 'MRP', 'deanery' => 'Livingstone Deanery'],
                                    "Our Lady of Angels Parish" => ['code' => 'OLA', 'deanery' => 'Livingstone Deanery'],
                                    "St. Francis' Parish" => ['code' => 'SFP', 'deanery' => 'Livingstone Deanery'],
                                    "St. Peter's Parish" => ['code' => 'SPP', 'deanery' => 'Livingstone Deanery'],
                                    "St. Thomas the Apostle Parish" => ['code' => 'STP', 'deanery' => 'Livingstone Deanery'],
                                    "St. Kizito's Sesheke Parish" => ['code' => 'SKS', 'deanery' => 'Sesheke Deanery'],
                                    "St. Fidelis' Sichili Parish" => ['code' => 'SFS', 'deanery' => 'Sesheke Deanery'],
                                    "St. Mary's Njoko Parish" => ['code' => 'SMN', 'deanery' => 'Sesheke Deanery'],
                                    "St. Arnold Janssen's Mwandi Parish" => ['code' => 'SAJ', 'deanery' => 'Sesheke Deanery'],
                                    "Nawinda Parish" => ['code' => 'NWP', 'deanery' => 'Sesheke Deanery'],
                                    "Lusu Parish" => ['code' => 'LSP', 'deanery' => 'Sioma Deanery'],
                                    "Sioma Parish" => ['code' => 'SMP', 'deanery' => 'Sioma Deanery'],
                                    "Shangombo Parish" => ['code' => 'SGP', 'deanery' => 'Sioma Deanery'],
                                ];
                                if ($state && isset($parishMap[$state])) {
                                    $set('code', $parishMap[$state]['code']);
                                    $set('deanery', $parishMap[$state]['deanery']);
                                }
                            }),
                        Forms\Components\TextInput::make('code')
                            ->label('Parish Code')
                            ->required()
                            ->maxLength(10),
                        Forms\Components\Select::make('deanery')
                            ->options([
                                'Livingstone Deanery' => 'Livingstone Deanery',
                                'Sesheke Deanery' => 'Sesheke Deanery',
                                'Sioma Deanery' => 'Sioma Deanery',
                            ])
                            ->required(),
                    ])->columns(3),

                Forms\Components\Section::make('Contingent & Camp Check-In')
                    ->schema([
                        Forms\Components\TextInput::make('patron_matron_name')
                            ->label('Patron / Matron Name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('patron_contact')
                            ->label('Contact Phone Number')
                            ->tel()
                            ->maxLength(50),
                        Forms\Components\TextInput::make('camp_contingent_count')
                            ->label('Contingent Headcount (Youths)')
                            ->required()
                            ->numeric()
                            ->default(25),
                        Forms\Components\Toggle::make('camp_checked_in')
                            ->label('Campsite Checked-In')
                            ->default(false),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Code')
                    ->badge()
                    ->color('warning')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Parish Name')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('deanery')
                    ->label('Deanery')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                Tables\Columns\TextColumn::make('patron_matron_name')
                    ->label('Patron / Matron')
                    ->searchable(),
                Tables\Columns\TextColumn::make('patron_contact')
                    ->label('Contact')
                    ->searchable(),
                Tables\Columns\TextColumn::make('camp_contingent_count')
                    ->label('Campers')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\ToggleColumn::make('camp_checked_in')
                    ->label('Checked In')
                    ->sortable()
                    ->alignCenter(),
            ])
            ->defaultSort('name')
            ->filters([
                Tables\Filters\SelectFilter::make('deanery')
                    ->options([
                        'Livingstone Deanery' => 'Livingstone Deanery',
                        'Sesheke Deanery' => 'Sesheke Deanery',
                        'Sioma Deanery' => 'Sioma Deanery',
                    ]),
                Tables\Filters\TernaryFilter::make('camp_checked_in')
                    ->label('Check-In Status')
                    ->trueLabel('Checked In')
                    ->falseLabel('Pending Arrival'),
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListParishes::route('/'),
            'create' => Pages\CreateParish::route('/create'),
            'edit' => Pages\EditParish::route('/{record}/edit'),
        ];
    }
}
