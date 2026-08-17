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

    protected static ?string $navigationLabel = 'User Accounts (Admins & Judges)';

    protected static ?string $navigationGroup = 'Festival Operations';

    protected static ?int $navigationSort = 0;

    protected static ?string $slug = 'judge-assignments';

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('User Account Identity & Role Assignment')
                    ->description('Create or edit user credentials. Admins can provision accounts for official Judges or other Administrators.')
                    ->schema([
                        Forms\Components\Select::make('role')
                            ->label('Account Role')
                            ->options([
                                'admin' => '🛡️ Administrator (Full System & Setup Access)',
                                'judge' => '⚖️ Official Adjudicator (Judging Workstation)',
                            ])
                            ->default('judge')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state === 'admin') {
                                    $set('judge_name', null);
                                }
                            }),

                        Forms\Components\Select::make('judge_name')
                            ->label('Assigned Official Judge Seat')
                            ->options([
                                'Judge 1' => '⚖️ Judge 1 (Technical & Core Adjudicator)',
                                'Judge 2' => '⚖️ Judge 2 (Artistic & Harmony Adjudicator)',
                                'Judge 3' => '⚖️ Judge 3 (Presentation & Diction Adjudicator)',
                            ])
                            ->visible(fn (Forms\Get $get) => $get('role') === 'judge')
                            ->required(fn (Forms\Get $get) => $get('role') === 'judge')
                            ->helperText('Determines which score sheet and official evaluation record this judge signs.'),

                        Forms\Components\TextInput::make('name')
                            ->label('Full Name / Title')
                            ->placeholder('e.g. Sr. Maria Mutale / Mr. Kenneth Banda')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label('Login Email Address')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

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
                Tables\Columns\TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'warning',
                        'judge' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin' => '🛡️ Admin',
                        'judge' => '⚖️ Judge',
                        default => ucfirst($state),
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('judge_name')
                    ->label('Assigned Seat')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'Judge 1' => 'warning',
                        'Judge 2' => 'info',
                        'Judge 3' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state) => $state ?? '—')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('User Name')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email Account')
                    ->copyable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created On')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('role')
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'admin' => '🛡️ Administrators',
                        'judge' => '⚖️ Judges',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Edit / Reassign'),
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
