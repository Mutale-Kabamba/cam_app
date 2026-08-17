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
                            ->options(function (?Parish $record) {
                                $defaults = [
                                    "St. Theresa's Cathedral" => "St. Theresa's Cathedral",
                                    "Christ the King Parish" => "Christ the King Parish",
                                    "Holy Childhood Parish" => "Holy Childhood Parish",
                                    "Kazungula Parish" => "Kazungula Parish",
                                    "Maria Regina Parish" => "Maria Regina Parish",
                                    "Our Lady of Angels Parish" => "Our Lady of Angels Parish",
                                    "St. Francis' Parish" => "St. Francis' Parish",
                                    "St. Peter's Parish" => "St. Peter's Parish",
                                    "St. Thomas the Apostle Parish" => "St. Thomas the Apostle Parish",
                                    "St. Kizito's Sesheke Parish" => "St. Kizito's Sesheke Parish",
                                    "St. Fidelis' Sichili Parish" => "St. Fidelis' Sichili Parish",
                                    "St. Mary's Njoko Parish" => "St. Mary's Njoko Parish",
                                    "St. Arnold Janssen's Mwandi Parish" => "St. Arnold Janssen's Mwandi Parish",
                                    "Nawinda Parish" => "Nawinda Parish",
                                    "Lusu Parish" => "Lusu Parish",
                                    "Sioma Parish" => "Sioma Parish",
                                    "Shangombo Parish" => "Shangombo Parish",
                                ];

                                $existing = Parish::pluck('name', 'name')->toArray();
                                if ($record && $record->name) {
                                    $existing[$record->name] = $record->name;
                                }

                                return array_merge($defaults, $existing);
                            })
                            ->searchable()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('New Parish Name')
                                    ->required()
                                    ->placeholder('e.g. St. Joseph Parish'),
                            ])
                            ->createOptionUsing(function (array $data): string {
                                return $data['name'];
                            })
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                $parishMap = [
                                    "St. Theresa's Cathedral" => ['code' => 'STC', 'deanery' => 'Livingstone Deanery'],
                                    "Christ the King Parish" => ['code' => 'CKP', 'deanery' => 'Livingstone Deanery'],
                                    "Holy Childhood Parish" => ['code' => 'HCP', 'deanery' => 'Livingstone Deanery'],
                                    "Kazungula Parish" => ['code' => 'KZP', 'deanery' => 'Livingstone Deanery'],
                                    "Maria Regina Parish" => ['code' => 'MRP', 'deanery' => 'Livingstone Deanery'],
                                    "Our Lady of Angels Parish" => ['code' => 'OLA', 'deanery' => 'Livingstone Deanery'],
                                    "St. Francis' Parish" => ['code' => 'SFP', 'deanery' => 'Livingstone Deanery'],
                                    "St. Peter's Parish" => ['code' => 'SPP', 'deanery' => 'Livingstone Deanery'],
                                    "St. Thomas the Apostle Parish" => ['code' => 'STP', 'deanery' => 'Livingstone Deanery'],
                                    "St. Kizito's Sesheke Parish" => ['code' => 'SKP', 'deanery' => 'Sesheke Deanery'],
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
                                } elseif ($state && empty($get('code'))) {
                                    // Generate a 3-letter uppercase code for custom parish
                                    $words = preg_split('/\s+/', trim($state));
                                    $code = '';
                                    foreach ($words as $w) {
                                        $code .= strtoupper(substr($w, 0, 1));
                                    }
                                    $set('code', substr($code, 0, 4) ?: 'PAR');
                                }
                            }),

                        Forms\Components\TextInput::make('code')
                            ->label('Parish Code')
                            ->required()
                            ->maxLength(10)
                            ->placeholder('e.g. STC'),

                        Forms\Components\Select::make('deanery')
                            ->label('Deanery')
                            ->options([
                                'Livingstone Deanery' => 'Livingstone Deanery',
                                'Sesheke Deanery' => 'Sesheke Deanery',
                                'Sioma Deanery' => 'Sioma Deanery',
                            ])
                            ->searchable()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('deanery')
                                    ->label('New Deanery Name')
                                    ->required()
                                    ->placeholder('e.g. Kazungula Deanery'),
                            ])
                            ->createOptionUsing(fn (array $data) => $data['deanery'])
                            ->required(),
                    ])->columns(3),

                Forms\Components\Section::make('Contingent & Headcount by Gender')
                    ->description('Record youth contingent numbers per gender. Total headcount is calculated automatically.')
                    ->schema([
                        Forms\Components\TextInput::make('male_count')
                            ->label('Male Youths')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                $male = intval($get('male_count') ?? 0);
                                $female = intval($get('female_count') ?? 0);
                                $set('camp_contingent_count', $male + $female);
                            }),

                        Forms\Components\TextInput::make('female_count')
                            ->label('Female Youths')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                $male = intval($get('male_count') ?? 0);
                                $female = intval($get('female_count') ?? 0);
                                $set('camp_contingent_count', $male + $female);
                            }),

                        Forms\Components\TextInput::make('camp_contingent_count')
                            ->label('Total Contingent Headcount (Youths)')
                            ->numeric()
                            ->default(0)
                            ->readOnly()
                            ->helperText('⚡ Auto-calculated (Male + Female)'),
                    ])->columns(3),

                Forms\Components\Section::make('Patron / Matron & Campsite Check-In')
                    ->schema([
                        Forms\Components\TextInput::make('patron_matron_name')
                            ->label('Patron / Matron Name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('patron_contact')
                            ->label('Contact Phone Number')
                            ->tel()
                            ->maxLength(50),
                        Forms\Components\Toggle::make('camp_checked_in')
                            ->label('Campsite Checked-In')
                            ->default(false),
                    ])->columns(3),
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
                    ->label('Youths')
                    ->description(fn (Parish $record): string => "♂ {$record->male_count} | ♀ {$record->female_count}")
                    ->sortable()
                    ->alignCenter()
                    ->weight('bold'),
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
