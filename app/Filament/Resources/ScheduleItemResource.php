<?php

namespace App\Filament\Resources;

use App\Filament\Imports\ScheduleItemImporter;
use App\Filament\Resources\ScheduleItemResource\Pages;
use App\Models\ScheduleItem;
use App\Models\Category;
use App\Models\Parish;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\ImportAction;

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
            ->headerActions([
                Tables\Actions\ImportAction::make()
                    ->importer(ScheduleItemImporter::class)
                    ->label('Import CSV / Excel')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('gray'),

                Tables\Actions\Action::make('import_json')
                    ->label('Import JSON')
                    ->icon('heroicon-o-document-arrow-up')
                    ->color('warning')
                    ->form([
                        Forms\Components\Textarea::make('json_payload')
                            ->label('Paste JSON Schedule Items')
                            ->required()
                            ->rows(20)
                            ->placeholder(self::jsonPlaceholder())
                            ->helperText('Paste an array of schedule item objects. Use category name/slug and parish name/code.'),
                    ])
                    ->action(function (array $data): void {
                        $payload = json_decode($data['json_payload'], true);

                        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($payload)) {
                            Notification::make()
                                ->title('Invalid JSON')
                                ->body('The pasted content is not valid JSON. Please check the format and try again.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $created = 0;
                        $errors  = [];

                        foreach ($payload as $index => $row) {
                            try {
                                $item = new ScheduleItem();

                                // Required fields
                                $item->event_date            = $row['event_date']            ?? null;
                                $item->day_name              = $row['day_name']              ?? null;
                                $item->scheduled_start_time  = $row['scheduled_start_time']  ?? null;
                                $item->scheduled_end_time    = $row['scheduled_end_time']    ?? null;
                                $item->activity_title        = $row['activity_title']        ?? null;
                                $item->venue                 = $row['venue']                 ?? 'Main Stage';
                                $item->status                = $row['status']                ?? 'scheduled';
                                $item->performance_order     = isset($row['performance_order']) ? intval($row['performance_order']) : null;
                                $item->time_penalty_marks    = isset($row['time_penalty_marks']) ? intval($row['time_penalty_marks']) : 0;
                                $item->actual_duration_seconds = isset($row['actual_duration_seconds']) ? intval($row['actual_duration_seconds']) : 0;

                                // Resolve category
                                if (! empty($row['category'])) {
                                    $category = Category::where('name', $row['category'])
                                        ->orWhere('slug', $row['category'])
                                        ->first();
                                    $item->category_id = $category?->id;
                                } elseif (! empty($row['category_id'])) {
                                    $item->category_id = intval($row['category_id']);
                                }

                                // Resolve parish
                                if (! empty($row['parish'])) {
                                    $parish = Parish::where('name', $row['parish'])
                                        ->orWhere('code', $row['parish'])
                                        ->first();
                                    $item->parish_id = $parish?->id;
                                } elseif (! empty($row['parish_id'])) {
                                    $item->parish_id = intval($row['parish_id']);
                                }

                                $item->save();
                                $created++;
                            } catch (\Throwable $e) {
                                $errors[] = "Row " . ($index + 1) . ": " . $e->getMessage();
                            }
                        }

                        if ($created > 0) {
                            Notification::make()
                                ->title("Import Successful")
                                ->body("{$created} schedule item(s) imported successfully." . (count($errors) ? ' ' . count($errors) . ' row(s) had errors.' : ''))
                                ->success()
                                ->send();
                        }

                        if (count($errors) > 0 && $created === 0) {
                            Notification::make()
                                ->title('Import Failed')
                                ->body(implode("\n", array_slice($errors, 0, 5)))
                                ->danger()
                                ->send();
                        }
                    }),
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
            'index'  => Pages\ListScheduleItems::route('/'),
            'create' => Pages\CreateScheduleItem::route('/create'),
            'edit'   => Pages\EditScheduleItem::route('/{record}/edit'),
        ];
    }

    /**
     * Example JSON placeholder shown in the import textarea.
     */
    private static function jsonPlaceholder(): string
    {
        return <<<'JSON'
[
  {
    "event_date": "2026-08-19",
    "day_name": "Wednesday",
    "scheduled_start_time": "08:00",
    "scheduled_end_time": "08:30",
    "venue": "Main Stage",
    "activity_title": "Opening Prayer & Procession",
    "category": null,
    "parish": null,
    "performance_order": null,
    "status": "scheduled"
  },
  {
    "event_date": "2026-08-19",
    "day_name": "Wednesday",
    "scheduled_start_time": "09:00",
    "scheduled_end_time": "09:45",
    "venue": "Main Stage",
    "activity_title": "Choir Competition – St. Theresa's Cathedral",
    "category": "Choir",
    "parish": "St. Theresa's Cathedral",
    "performance_order": 1,
    "status": "scheduled"
  }
]
JSON;
    }
}
