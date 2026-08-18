<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParishResource\Pages;
use App\Models\Parish;
use App\Models\Category;
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
                        // On EDIT — parish name is fixed, just show it as plain text
                        Forms\Components\TextInput::make('name')
                            ->label('Parish Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g. St. Joseph Parish')
                            ->hiddenOn('create'),

                        // On CREATE — Select with auto-fill of code & deanery
                        Forms\Components\Select::make('name')
                            ->label('Parish Name')
                            ->options(function () {
                                // Official 14 CAM Festival parishes
                                return [
                                    // Livingstone Deanery
                                    'St. Peter the Apostle Parish'  => '⛪ St. Peter the Apostle Parish (Airport)',
                                    'Our Lady of Angels Parish'      => '⛪ Our Lady of Angels Parish',
                                    'St. Francis of Assisi Parish'   => '⛪ St. Francis of Assisi Parish',
                                    'St. Theresa Cathedral Parish'   => '⛪ St. Theresa Cathedral Parish',
                                    "St. Paul's Parish"              => "⛪ St. Paul's Parish (Ngwenya)",
                                    'St. Joseph the Worker Parish'   => '⛪ St. Joseph the Worker Parish (Mukuni)',
                                    'Christ the King Parish'         => '⛪ Christ the King Parish',
                                    'Maria Regina Parish'            => '⛪ Maria Regina Parish',
                                    'St. Stephen Parish'             => '⛪ St. Stephen Parish',
                                    // Sesheke Deanery
                                    'St. Fidelis Parish'             => '⛪ St. Fidelis Parish (Sichili)',
                                    'St. Kizito Parish'              => '⛪ St. Kizito Parish',
                                    'St. Paul Parish'                => '⛪ St. Paul Parish (Nawinda)',
                                    // Sioma Deanery
                                    'St. Joseph Parish'              => '⛪ St. Joseph Parish (Lusu)',
                                    'St. Anthony Parish'             => '⛪ St. Anthony Parish',
                                ];
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
                                    // Livingstone Deanery
                                    'St. Peter the Apostle Parish'  => ['code' => 'SPA', 'deanery' => 'Livingstone Deanery'],
                                    'Our Lady of Angels Parish'      => ['code' => 'OLA', 'deanery' => 'Livingstone Deanery'],
                                    'St. Francis of Assisi Parish'   => ['code' => 'SFA', 'deanery' => 'Livingstone Deanery'],
                                    'St. Theresa Cathedral Parish'   => ['code' => 'STC', 'deanery' => 'Livingstone Deanery'],
                                    "St. Paul's Parish"              => ['code' => 'SPP', 'deanery' => 'Livingstone Deanery'],
                                    'St. Joseph the Worker Parish'   => ['code' => 'SJW', 'deanery' => 'Livingstone Deanery'],
                                    'Christ the King Parish'         => ['code' => 'CTK', 'deanery' => 'Livingstone Deanery'],
                                    'Maria Regina Parish'            => ['code' => 'MRP', 'deanery' => 'Livingstone Deanery'],
                                    'St. Stephen Parish'             => ['code' => 'SSP', 'deanery' => 'Livingstone Deanery'],
                                    // Sesheke Deanery
                                    'St. Fidelis Parish'             => ['code' => 'SFD', 'deanery' => 'Sesheke Deanery'],
                                    'St. Kizito Parish'              => ['code' => 'SKP', 'deanery' => 'Sesheke Deanery'],
                                    'St. Paul Parish'                => ['code' => 'SPN', 'deanery' => 'Sesheke Deanery'],
                                    // Sioma Deanery
                                    'St. Joseph Parish'              => ['code' => 'SJP', 'deanery' => 'Sioma Deanery'],
                                    'St. Anthony Parish'             => ['code' => 'SAP', 'deanery' => 'Sioma Deanery'],
                                ];

                                if ($state && isset($parishMap[$state])) {
                                    $set('code', $parishMap[$state]['code']);
                                    $set('deanery', $parishMap[$state]['deanery']);
                                } elseif ($state && empty($get('code'))) {
                                    // Generate a short code for custom parish names
                                    $words = preg_split('/\s+/', trim($state));
                                    $code  = '';
                                    foreach ($words as $w) {
                                        $code .= strtoupper(substr($w, 0, 1));
                                    }
                                    $set('code', substr($code, 0, 4) ?: 'PAR');
                                }
                            })
                            ->hiddenOn('edit'),


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

                Forms\Components\Section::make('Competition Participation')
                    ->description('Tick the festival categories this parish contingent is participating in.')
                    ->schema([
                        Forms\Components\CheckboxList::make('participating_categories')
                            ->label('Participating Categories')
                            ->options(fn () => Category::orderBy('id')->pluck('name', 'id')->toArray())
                            ->descriptions(fn () => Category::orderBy('id')->get()->mapWithKeys(function ($cat) {
                                return [$cat->id => "Max {$cat->max_raw_score} pts" . ($cat->allocated_minutes > 0 ? " • {$cat->allocated_minutes} mins" : '')];
                            })->toArray())
                            ->columns(2)
                            ->gridDirection('row')
                            ->bulkToggleable()
                            ->searchable()
                            ->helperText('Tick all the events this parish is entered to compete in at the CAM Festival.'),
                    ]),

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
                Tables\Columns\TextColumn::make('participating_categories_count')
                    ->label('Events')
                    ->state(fn (Parish $record): int => is_array($record->participating_categories) ? count($record->participating_categories) : 0)
                    ->badge()
                    ->color('warning')
                    ->alignCenter()
                    ->tooltip(function (Parish $record) {
                        if (empty($record->participating_categories)) {
                            return 'No specific categories ticked';
                        }
                        return implode(', ', Category::whereIn('id', $record->participating_categories)->pluck('name')->toArray());
                    }),
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
