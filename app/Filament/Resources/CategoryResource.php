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
                Forms\Components\Section::make('Category Specifications')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('type')
                            ->options([
                                'stage_performance' => 'Stage Performance',
                                'quiz_written' => 'Quiz & Written Exam',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('allocated_minutes')
                            ->label('Allocated Stage Time (Minutes)')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('prep_minutes')
                            ->label('Preparation Time (Minutes)')
                            ->numeric()
                            ->default(5),
                        Forms\Components\TextInput::make('max_raw_score')
                            ->label('Max Score Benchmark')
                            ->numeric()
                            ->default(100),
                    ])->columns(3),

                Forms\Components\Section::make('Theme & Descriptions')
                    ->schema([
                        Forms\Components\TextInput::make('theme')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->rows(3)
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
                    ->color('info')
                    ->sortable(),
                Tables\Columns\TextColumn::make('allocated_minutes')
                    ->label('Stage Time')
                    ->formatStateUsing(fn ($state) => $state > 0 ? "{$state} mins" : 'Quiz')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('prep_minutes')
                    ->label('Prep Time')
                    ->formatStateUsing(fn ($state) => "{$state} mins")
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('max_raw_score')
                    ->label('Max Marks')
                    ->alignRight()
                    ->badge()
                    ->color('warning'),
            ])
            ->defaultSort('name')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                //
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
