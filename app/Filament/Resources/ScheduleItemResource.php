<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScheduleItemResource\Pages;
use App\Models\ScheduleItem;
use App\Models\Category;
use App\Models\Parish;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ScheduleItemResource extends Resource
{
    protected static ?string $model = ScheduleItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Timetable & Program';

    protected static ?string $navigationGroup = 'Festival Operations';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Schedule & Venue Details')
                    ->schema([
                        Forms\Components\DatePicker::make('event_date')
                            ->default('2026-08-18')
                            ->required(),
                        Forms\Components\Select::make('day_name')
                            ->options([
                                'Monday' => 'Monday',
                                'Tuesday' => 'Tuesday',
                                'Wednesday' => 'Wednesday',
                                'Thursday' => 'Thursday',
                                'Friday' => 'Friday',
                                'Saturday' => 'Saturday',
                                'Sunday' => 'Sunday',
                            ])
                            ->required(),
                        Forms\Components\TimePicker::make('scheduled_start_time')
                            ->seconds(false)
                            ->required(),
                        Forms\Components\TimePicker::make('scheduled_end_time')
                            ->seconds(false)
                            ->required(),
                        Forms\Components\TextInput::make('venue')
                            ->default('Main Stage')
                            ->required(),
                        Forms\Components\TextInput::make('activity_title')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Competition & Stage Management')
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->label('Competition Category')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('parish_id')
                            ->label('Performing Parish')
                            ->relationship('parish', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('performance_order')
                            ->numeric()
                            ->placeholder('e.g. 1'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'scheduled' => '⏳ Scheduled',
                                'in_progress' => '● LIVE (On Stage)',
                                'completed' => '✓ Completed',
                            ])
                            ->default('scheduled')
                            ->required(),
                        Forms\Components\TextInput::make('time_penalty_marks')
                            ->label('Timekeeper Penalty Marks')
                            ->numeric()
                            ->default(0)
                            ->helperText('Deduction: -2, -5, -10, -15 marks'),
                        Forms\Components\TextInput::make('actual_duration_seconds')
                            ->label('Actual Duration (Seconds)')
                            ->numeric()
                            ->default(0),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextInputColumn::make('performance_order')
                    ->label('Order #')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('day_name')
                    ->label('Day')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                Tables\Columns\TextColumn::make('scheduled_start_time')
                    ->label('Time Slot')
                    ->formatStateUsing(fn ($record) => substr($record->scheduled_start_time, 0, 5) . ' - ' . substr($record->scheduled_end_time, 0, 5))
                    ->sortable(),

                Tables\Columns\TextInputColumn::make('activity_title')
                    ->label('Activity Title')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextInputColumn::make('venue')
                    ->label('Venue')
                    ->searchable(),

                Tables\Columns\TextColumn::make('parish.name')
                    ->label('Parish')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\SelectColumn::make('status')
                    ->label('Stage Status')
                    ->options([
                        'scheduled' => '⏳ Scheduled',
                        'in_progress' => '● LIVE (On Stage)',
                        'completed' => '✓ Completed',
                    ])
                    ->sortable(),

                Tables\Columns\TextInputColumn::make('time_penalty_marks')
                    ->label('Time Penalty (Marks)')
                    ->sortable()
                    ->alignCenter(),
            ])
            ->defaultSort('scheduled_start_time')
            ->filters([
                Tables\Filters\SelectFilter::make('day_name')
                    ->options([
                        'Monday' => 'Monday',
                        'Tuesday' => 'Tuesday',
                        'Wednesday' => 'Wednesday',
                        'Thursday' => 'Thursday',
                        'Friday' => 'Friday',
                        'Saturday' => 'Saturday',
                        'Sunday' => 'Sunday',
                    ]),
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'scheduled' => 'Scheduled',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->slideOver(),
                Tables\Actions\ReplicateAction::make()
                    ->excludeAttributes(['id', 'created_at', 'updated_at']),
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
            'index' => Pages\ListScheduleItems::route('/'),
            'create' => Pages\CreateScheduleItem::route('/create'),
            'edit' => Pages\EditScheduleItem::route('/{record}/edit'),
        ];
    }
}
