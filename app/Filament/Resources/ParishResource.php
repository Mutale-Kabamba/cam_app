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
                        Forms\Components\TextInput::make('name')
                            ->label('Parish Name')
                            ->required()
                            ->maxLength(255)
                            ->datalist([
                                "St. Theresa's Cathedral",
                                "Christ the King Parish",
                                "Holy Childhood Parish",
                                "Kazungula Parish",
                                "Maria Regina Parish",
                                "Our Lady of Angels Parish",
                                "St. Francis' Parish",
                                "St. Peter's Parish",
                                "St. Thomas the Apostle Parish",
                                "St. Kizito's Sesheke Parish",
                                "St. Fidelis' Sichili Parish",
                                "St. Mary's Njoko Parish",
                                "St. Arnold Janssen's Mwandi Parish",
                                "Nawinda Parish",
                                "Lusu Parish",
                                "Sioma Parish",
                                "Shangombo Parish",
                            ])
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                $parishMap = [
                                    "St. Theresa's Cathedral" => ['code' => 'STC', 'deanery' => 'Livingstone Deanery'],
                                    "Christ the King Parish" => ['code' => 'CTK', 'deanery' => 'Livingstone Deanery'],
                                    "Holy Childhood Parish" => ['code' => 'HCP', 'deanery' => 'Livingstone Deanery'],
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
                                    if (empty($get('code'))) {
                                        $set('code', $parishMap[$state]['code']);
                                    }
                                    if (empty($get('deanery'))) {
                                        $set('deanery', $parishMap[$state]['deanery']);
                                    }
                                }
                            }),
                        Forms\Components\TextInput::make('code')
                            ->label('Parish Code')
                            ->required()
                            ->maxLength(10)
                            ->placeholder('e.g. STC'),
                        Forms\Components\TextInput::make('deanery')
                            ->label('Deanery')
                            ->datalist([
                                'Livingstone Deanery',
                                'Sesheke Deanery',
                                'Sioma Deanery',
                            ])
                            ->placeholder('e.g. Livingstone Deanery')
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
