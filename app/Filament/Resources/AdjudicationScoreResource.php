<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdjudicationScoreResource\Pages;
use App\Models\AdjudicationScore;
use App\Models\Category;
use App\Models\Parish;
use App\Models\ScheduleItem;
use App\Models\ConsolidatedResult;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AdjudicationScoreResource extends Resource
{
    protected static ?string $model = AdjudicationScore::class;

    protected static ?string $navigationIcon = 'heroicon-o-scale';

    protected static ?string $navigationLabel = 'Adjudication Scores (3 Judges)';

    protected static ?string $navigationGroup = 'Judging & Results';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        // If logged in user is a Judge, show only their own scorecards
        if ($user && $user->isJudge()) {
            $query->where('adjudicator_name', $user->getJudgeName());
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // 1. Judge & Performance Identification Banner
                Forms\Components\Section::make('Evaluation Identity & Category')
                    ->schema([
                        Forms\Components\TextInput::make('adjudicator_name')
                            ->label('Judge Identity')
                            ->default(fn () => auth()->user()?->getJudgeName() ?? 'Judge 1')
                            ->readOnly(fn () => auth()->user()?->isJudge() ?? false)
                            ->required(),

                        Forms\Components\Select::make('category_id')
                            ->label('Competition Category')
                            ->relationship('category', 'name')
                            ->live()
                            ->searchable()
                            ->preload()
                            ->required()
                            ->afterStateUpdated(fn (Forms\Set $set) => $set('parish_id', null)),

                        Forms\Components\Select::make('parish_id')
                            ->label('Parish')
                            ->options(function (Forms\Get $get) {
                                $categoryId = $get('category_id');
                                if (!$categoryId) {
                                    return Parish::orderBy('name')->pluck('name', 'id');
                                }

                                // Get parishes specifically participating / scheduled for this category
                                $scheduledParishIds = ScheduleItem::where('category_id', $categoryId)
                                    ->whereNotNull('parish_id')
                                    ->pluck('parish_id')
                                    ->toArray();

                                if (!empty($scheduledParishIds)) {
                                    return Parish::whereIn('id', $scheduledParishIds)->orderBy('name')->pluck('name', 'id');
                                }

                                return Parish::orderBy('name')->pluck('name', 'id');
                            })
                            ->live()
                            ->searchable()
                            ->required()
                            ->helperText('Only parishes in the selected category appear here.'),
                    ])->columns(3),

                // 2A. CHOIR MUSIC FORM (Specific Header & 4 Songs Breakdown)
                Forms\Components\Section::make('Choir Music (Melody) Presentation Info')
                    ->visible(function (Forms\Get $get) {
                        $cat = Category::find($get('category_id'));
                        return $cat && ($cat->slug === 'choir' || str_contains(strtolower($cat->name), 'choir'));
                    })
                    ->schema([
                        Forms\Components\TextInput::make('conductor_name')
                            ->label('Conductor Name')
                            ->placeholder('e.g. John Banda'),
                        Forms\Components\TextInput::make('participant_count')
                            ->label('Number of Participants on Stage')
                            ->numeric()
                            ->placeholder('Unlimited allowed'),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('song_titles_breakdown.social_song')
                                    ->label('1. Social Song Title')
                                    ->placeholder('Title of Social Song'),
                                Forms\Components\TextInput::make('song_titles_breakdown.kyrie')
                                    ->label('2. Kyrie Title')
                                    ->placeholder('Title of Kyrie'),
                                Forms\Components\TextInput::make('song_titles_breakdown.gloria')
                                    ->label('3. Gloria Title')
                                    ->placeholder('Title of Gloria'),
                                Forms\Components\TextInput::make('song_titles_breakdown.thanksgiving')
                                    ->label('4. Thanksgiving Title')
                                    ->placeholder('Title of Thanksgiving'),
                            ]),
                    ]),

                // 2B. SELF-COMPOSED SONG FORM (Specific Header)
                Forms\Components\Section::make('Self-Composed Song Presentation Info')
                    ->visible(function (Forms\Get $get) {
                        $cat = Category::find($get('category_id'));
                        return $cat && ($cat->slug === 'self-composed' || str_contains(strtolower($cat->name), 'self-composed'));
                    })
                    ->schema([
                        Forms\Components\TextInput::make('item_title')
                            ->label('Title of Song')
                            ->placeholder('e.g. Tukopano mwa Pastoral Care'),
                        Forms\Components\TextInput::make('composer_author')
                            ->label('Composer(s)')
                            ->placeholder('Name of Composer'),
                        Forms\Components\TextInput::make('director_producer')
                            ->label('Director')
                            ->placeholder('Name of Director'),
                        Forms\Components\TextInput::make('language_used')
                            ->label('Language Used')
                            ->placeholder('e.g. Lozi, Tonga, English'),
                        Forms\Components\TextInput::make('participant_count')
                            ->label('Number of Participants')
                            ->numeric(),
                    ])->columns(2),

                // 2C. POETRY FORM (Specific Header)
                Forms\Components\Section::make('Poetry Presentation Info')
                    ->visible(function (Forms\Get $get) {
                        $cat = Category::find($get('category_id'));
                        return $cat && ($cat->slug === 'poetry' || str_contains(strtolower($cat->name), 'poetry'));
                    })
                    ->schema([
                        Forms\Components\TextInput::make('item_title')
                            ->label('Title of Poem')
                            ->placeholder('e.g. Walking in Pastoral Care'),
                        Forms\Components\TextInput::make('composer_author')
                            ->label('Composer(s) / Author')
                            ->placeholder('Author name'),
                        Forms\Components\TextInput::make('director_producer')
                            ->label('Producer / Director')
                            ->placeholder('Producer name'),
                        Forms\Components\TextInput::make('language_used')
                            ->label('Language Used')
                            ->placeholder('e.g. English / Lozi'),
                        Forms\Components\TextInput::make('participant_count')
                            ->label('Number of Participants (Max 6)')
                            ->numeric(),
                    ])->columns(2),

                // 2D. GENERAL CATEGORIES (Dance, Drama, Quiz)
                Forms\Components\Section::make('Presentation Info')
                    ->visible(function (Forms\Get $get) {
                        $cat = Category::find($get('category_id'));
                        return $cat && !in_array($cat->slug, ['choir', 'self-composed', 'poetry']) 
                               && !str_contains(strtolower($cat->name), 'choir') 
                               && !str_contains(strtolower($cat->name), 'self-composed') 
                               && !str_contains(strtolower($cat->name), 'poetry');
                    })
                    ->schema([
                        Forms\Components\TextInput::make('conductor_name')
                            ->label('Presenter / Leader / Director'),
                        Forms\Components\TextInput::make('participant_count')
                            ->label('Number of Participants')
                            ->numeric(),
                        Forms\Components\TextInput::make('item_title')
                            ->label('Presentation Title / Items / Subjects')
                            ->columnSpanFull(),
                    ])->columns(2),

                // 3. OFFICIAL CRITERIA SCORING RUBRIC (Dynamic specific items for each category)
                Forms\Components\Section::make('⚖️ Official Judging Criteria Rubric')
                    ->schema(function (Forms\Get $get) {
                        $categoryId = $get('category_id');
                        if (!$categoryId) {
                            return [
                                Forms\Components\Placeholder::make('no_cat_selected')
                                    ->label('Notice')
                                    ->content('Please select a Category above to load its official judging criteria rubric.'),
                            ];
                        }

                        $category = Category::find($categoryId);
                        if (!$category || empty($category->judging_criteria)) {
                            return [
                                Forms\Components\TextInput::make('raw_total_score')
                                    ->label('Total Score Awarded')
                                    ->numeric()
                                    ->required(),
                            ];
                        }

                        $schema = [];
                        foreach ($category->judging_criteria as $index => $crit) {
                            $critName = $crit['criterion'] ?? ($crit['name'] ?? ('Criterion ' . ($index + 1)));
                            $maxScore = $crit['possible_score'] ?? ($crit['max_score'] ?? 10);
                            $desc = $crit['description'] ?? ($crit['desc'] ?? '');

                            $schema[] = Forms\Components\TextInput::make("criteria_scores.{$critName}")
                                ->label("#" . ($index + 1) . " {$critName} (Max: {$maxScore} pts)")
                                ->helperText($desc)
                                ->numeric()
                                ->minValue(0)
                                ->maxValue($maxScore)
                                ->step(0.5)
                                ->default(0)
                                ->live(debounce: 500)
                                ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) use ($category) {
                                    $scores = $get('criteria_scores') ?? [];
                                    $total = 0;
                                    foreach ($scores as $s) {
                                        $total += floatval($s);
                                    }
                                    $set('raw_total_score', $total);
                                    $maxRaw = $category->max_raw_score > 0 ? $category->max_raw_score : 100;
                                    $set('normalized_score', round(($total / $maxRaw) * 100, 2));
                                })
                                ->required();
                        }

                        return $schema;
                    })->columns(2),

                // 4. TOTAL SCORE, COMMENTS & DISQUALIFICATION
                Forms\Components\Section::make('Final Score & Adjudicator Remarks')
                    ->schema([
                        Forms\Components\TextInput::make('raw_total_score')
                            ->label('Total Marks Awarded (/100)')
                            ->numeric()
                            ->required()
                            ->readOnly()
                            ->helperText('Automatically calculated from the criteria marks above.'),

                        Forms\Components\TextInput::make('normalized_score')
                            ->label('Normalized Score (%)')
                            ->numeric()
                            ->readOnly(),

                        Forms\Components\Textarea::make('comments')
                            ->label('Adjudicator Comments & Observations')
                            ->placeholder('Enter constructive feedback, strengths, blend, diction, and recommendations...')
                            ->rows(4)
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_disqualified')
                            ->label('Disqualified Performance')
                            ->helperText('Check this only for severe rule violations or exceeding stage regulations.')
                            ->default(false),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('adjudicator_name')
                    ->label('Judge')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Judge 1' => 'warning',
                        'Judge 2' => 'info',
                        'Judge 3' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('parish.name')
                    ->label('Parish')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('conductor_name')
                    ->label('Conductor / Director')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('raw_total_score')
                    ->label('Raw Score')
                    ->sortable()
                    ->alignRight()
                    ->weight('bold')
                    ->color('warning'),
                Tables\Columns\TextColumn::make('normalized_score')
                    ->label('Score (%)')
                    ->sortable()
                    ->alignRight(),
                Tables\Columns\IconColumn::make('is_disqualified')
                    ->label('Disqualified')
                    ->boolean()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Scored At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name'),
                Tables\Filters\SelectFilter::make('parish_id')
                    ->label('Parish')
                    ->relationship('parish', 'name'),
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
            'index' => Pages\ListAdjudicationScores::route('/'),
            'create' => Pages\CreateAdjudicationScore::route('/create'),
            'edit' => Pages\EditAdjudicationScore::route('/{record}/edit'),
        ];
    }
}
