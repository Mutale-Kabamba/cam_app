<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConsolidatedResultResource\Pages;
use App\Models\ConsolidatedResult;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ConsolidatedResultResource extends Resource
{
    protected static ?string $model = ConsolidatedResult::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationLabel = 'Consolidated Standings';

    protected static ?string $navigationGroup = 'Judging & Results';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Category & Parish Result')
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('parish_id')
                            ->relationship('parish', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Score Aggregation & Championship Points')
                    ->schema([
                        Forms\Components\TextInput::make('adjudicators_average')
                            ->label('3-Judge Average Mark')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('time_penalty')
                            ->label('Timekeeper Penalty (Marks)')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('final_score')
                            ->label('Final Consolidated Score')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('rank')
                            ->label('Assigned Rank')
                            ->numeric(),
                        Forms\Components\TextInput::make('championship_points')
                            ->label('Championship Points Awarded')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Forms\Components\Toggle::make('is_finalized')
                            ->label('Published to Public Leaderboard & Big Screen')
                            ->default(false),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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
                Tables\Columns\TextColumn::make('adjudicators_average')
                    ->label('3-Judge Avg')
                    ->sortable()
                    ->alignRight(),
                Tables\Columns\TextColumn::make('time_penalty')
                    ->label('Time Penalty')
                    ->sortable()
                    ->color('danger')
                    ->alignRight(),
                Tables\Columns\TextColumn::make('final_score')
                    ->label('Final Score')
                    ->sortable()
                    ->weight('bold')
                    ->color('warning')
                    ->alignRight(),
                Tables\Columns\TextColumn::make('rank')
                    ->label('Rank')
                    ->sortable()
                    ->badge()
                    ->color(fn (?int $state): string => match ($state) {
                        1 => 'warning',
                        2 => 'gray',
                        3 => 'danger',
                        default => 'gray',
                    })
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('championship_points')
                    ->label('Points')
                    ->sortable()
                    ->color('success')
                    ->alignRight()
                    ->weight('bold'),
                Tables\Columns\IconColumn::make('is_finalized')
                    ->label('Published')
                    ->boolean()
                    ->alignCenter(),
            ])
            ->defaultSort('final_score', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name'),
                Tables\Filters\TernaryFilter::make('is_finalized')
                    ->label('Publish Status')
                    ->trueLabel('Published')
                    ->falseLabel('Draft'),
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
            'index' => Pages\ListConsolidatedResults::route('/'),
            'create' => Pages\CreateConsolidatedResult::route('/create'),
            'edit' => Pages\EditConsolidatedResult::route('/{record}/edit'),
        ];
    }
}
