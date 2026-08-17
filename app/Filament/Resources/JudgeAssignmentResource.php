<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JudgeAssignmentResource\Pages;
use App\Models\User;
use App\Models\AdjudicationScore;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class JudgeAssignmentResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Adjudicators & Assignments';

    protected static ?string $navigationGroup = 'Festival Operations';

    protected static ?int $navigationSort = 0;

    protected static ?string $slug = 'judge-assignments';

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('role', 'judge')
            ->orWhereNotNull('judge_name');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Adjudicator Official Identity & Seat Assignment')
                    ->description('Assign or reassign this adjudicator to an official Festival Judging Seat.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Full Name / Title')
                            ->placeholder('e.g. Sr. Maria Mutale / Mr. Kenneth Banda')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label('Login Email Address')
                            ->email()
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('judge_name')
                            ->label('Assigned Official Judge Seat')
                            ->options([
                                'Judge 1' => '⚖️ Judge 1 (Technical & Core Adjudicator)',
                                'Judge 2' => '⚖️ Judge 2 (Artistic & Harmony Adjudicator)',
                                'Judge 3' => '⚖️ Judge 3 (Presentation & Diction Adjudicator)',
                            ])
                            ->required()
                            ->helperText('Determines which score sheet and official record this judge signs.'),

                        Forms\Components\Hidden::make('role')
                            ->default('judge'),

                        Forms\Components\TextInput::make('password')
                            ->label('Account Password')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->helperText('Leave empty on edit to keep the existing password.'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('judge_name')
                    ->label('Assigned Seat')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Judge 1' => 'warning',
                        'Judge 2' => 'info',
                        'Judge 3' => 'success',
                        default => 'gray',
                    })
                    ->weight('bold')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Adjudicator Name')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email Account')
                    ->copyable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('scorecards_count')
                    ->label('Evaluations Submitted')
                    ->state(function (User $record): int {
                        return AdjudicationScore::where('adjudicator_name', $record->getJudgeName())->count();
                    })
                    ->badge()
                    ->color('primary')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Assigned On')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('judge_name')
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Reassign / Edit'),
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
            'index' => Pages\ListJudgeAssignments::route('/'),
            'create' => Pages\CreateJudgeAssignment::route('/create'),
            'edit' => Pages\EditJudgeAssignment::route('/{record}/edit'),
        ];
    }
}
