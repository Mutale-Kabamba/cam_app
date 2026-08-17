<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'Categories & Rubrics';

    protected static ?string $navigationGroup = 'Festival Configuration';

    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // ── Category Identity
                Forms\Components\Section::make('Category Identity')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Category Name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Forms\Set $set, string $context) {
                                if ($context === 'create') {
                                    $set('slug', \Illuminate\Support\Str::slug($state));
                                }
                            }),

                        Forms\Components\TextInput::make('slug')
                            ->label('URL Slug')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Auto-generated. Only edit if necessary.')
                            ->unique(ignoreRecord: true),

                        Forms\Components\Select::make('type')
                            ->label('Competition Type')
                            ->options([
                                'stage_performance' => '🎭 Stage Performance',
                                'quiz_written' => '📝 Quiz & Written Exam',
                            ])
                            ->required(),
                    ])->columns(3),

                // ── Timing & Scoring
                Forms\Components\Section::make('Timing & Scoring')
                    ->schema([
                        Forms\Components\TextInput::make('allocated_minutes')
                            ->label('Stage Time (Minutes)')
                            ->helperText('Set 0 for quiz categories.')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('prep_minutes')
                            ->label('Preparation Time (Minutes)')
                            ->numeric()
                            ->default(5),
                        Forms\Components\TextInput::make('max_raw_score')
                            ->label('Max Possible Score (pts)')
                            ->numeric()
                            ->required()
                            ->default(100)
                            ->helperText('Sum of all rubric criteria scores.'),
                    ])->columns(3),

                // ── Theme & Description
                Forms\Components\Section::make('Theme & Description')
                    ->schema([
                        Forms\Components\TextInput::make('theme')
                            ->label('Competition Theme / Scope')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->label('Category Description')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                // ── Competition Rules
                Forms\Components\Section::make('Competition Rules')
                    ->description('Define the official rules for this category. Judges and participants will see these.')
                    ->schema([
                        Forms\Components\Repeater::make('rules')
                            ->label('Rules')
                            ->schema([
                                Forms\Components\TextInput::make('rule')
                                    ->label('Rule')
                                    ->required()
                                    ->placeholder('e.g. Maximum 15 participants on stage.')
                                    ->columnSpanFull(),
                            ])
                            ->addActionLabel('+ Add Rule')
                            ->defaultItems(0)
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['rule'] ?? 'New Rule')
                            ->afterStateHydrated(function (Forms\Components\Repeater $component, $state) {
                                // DB stores rules as flat string array → convert for Repeater
                                if (is_array($state) && isset($state[0]) && is_string($state[0])) {
                                    $component->state(array_map(fn ($r) => ['rule' => $r], $state));
                                }
                            })
                            ->mutateDehydratedStateUsing(function (array $state): array {
                                // Convert [{rule:'...'}, ...] back to flat string array for DB
                                return array_values(array_map(fn ($item) => $item['rule'] ?? '', $state));
                            })
                            ->columnSpanFull(),
                    ]),

                // ── Judging Rubric
                Forms\Components\Section::make('Judging Rubric (Assessment Criteria)')
                    ->description('Define the marking criteria. The total of all "Max Possible Score" values should equal the "Max Possible Score" set above.')
                    ->schema([
                        Forms\Components\Repeater::make('judging_criteria')
                            ->label('Assessment Criteria')
                            ->schema([
                                Forms\Components\TextInput::make('no')
                                    ->label('No.')
                                    ->numeric()
                                    ->required()
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('criterion')
                                    ->label('Criterion Name')
                                    ->required()
                                    ->placeholder('e.g. Vocal Quality & Tone')
                                    ->columnSpan(3),
                                Forms\Components\TextInput::make('possible_score')
                                    ->label('Max Score (pts)')
                                    ->numeric()
                                    ->required()
                                    ->default(10)
                                    ->columnSpan(1),
                                Forms\Components\Textarea::make('description')
                                    ->label('Criterion Description')
                                    ->rows(2)
                                    ->placeholder('Describe what the judge evaluates under this criterion.')
                                    ->columnSpan(5),
                            ])
                            ->addActionLabel('+ Add Criterion')
                            ->defaultItems(0)
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => isset($state['criterion'])
                                ? "#{$state['no']} — {$state['criterion']} ({$state['possible_score']} pts)"
                                : 'New Criterion')
                            ->columns(5)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Category Name')
                    ->weight('bold')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'stage_performance' => 'info',
                        'quiz_written' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'stage_performance' => '🎭 Stage',
                        'quiz_written' => '📝 Quiz',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('judging_criteria')
                    ->label('Criteria')
                    ->formatStateUsing(fn ($state): string => count($state ?? []) . ' criteria')
                    ->badge()
                    ->color('gray')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('max_raw_score')
                    ->label('Max Marks')
                    ->alignCenter()
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('allocated_minutes')
                    ->label('Stage Time')
                    ->formatStateUsing(fn ($state) => $state > 0 ? "{$state} mins" : 'Quiz')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('prep_minutes')
                    ->label('Prep Time')
                    ->formatStateUsing(fn ($state) => "{$state} mins")
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
