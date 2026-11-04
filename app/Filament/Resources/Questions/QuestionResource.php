<?php

namespace App\Filament\Resources\Questions;

use App\Enums\QuestionStatus;
use App\Filament\Resources\Questions\Pages\CreateQuestion;
use App\Filament\Resources\Questions\Pages\EditQuestion;
use App\Filament\Resources\Questions\Pages\ListQuestions;
use App\Filament\Resources\Questions\RelationManagers\AnswersRelationManager;
use App\Models\Question;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('Public Questions');
    }

    public static function getModelLabel(): string
    {
        return __('Question');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Public Questions');
    }

    public static function getNavigationBadge(): ?string
    {
        $pending = Question::query()->where('status', QuestionStatus::Pending)->count();

        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Asker Identity'))
                    ->columns(3)
                    ->schema([
                        TextInput::make('name')
                            ->label(__('Full Name'))
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label(__('Email'))
                            ->email()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label(__('Phone / WhatsApp Number'))
                            ->tel()
                            ->maxLength(30),
                    ]),
                Section::make(__('Question Details'))
                    ->columns(2)
                    ->schema([
                        Select::make('topic_id')
                            ->label(__('Question Topic'))
                            ->relationship('topic', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('status')
                            ->label(__('Status'))
                            ->options(QuestionStatus::class)
                            ->default(QuestionStatus::Pending)
                            ->required(),
                        TextInput::make('subject')
                            ->label(__('Question Subject'))
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Textarea::make('body')
                            ->label(__('Question Body'))
                            ->required()
                            ->rows(5)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('subject')
                    ->label(__('Subject'))
                    ->searchable()
                    ->sortable()
                    ->limit(45),
                TextColumn::make('topic.name')
                    ->label(__('Topic'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('Asker'))
                    ->description(fn (Question $record): string => $record->email)
                    ->searchable(['name', 'email']),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('answers_count')
                    ->label(__('Answers'))
                    ->counts('answers')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('Submitted At'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options(QuestionStatus::class),
                SelectFilter::make('topic')
                    ->label(__('Topic'))
                    ->relationship('topic', 'name'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AnswersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuestions::route('/'),
            'create' => CreateQuestion::route('/create'),
            'edit' => EditQuestion::route('/{record}/edit'),
        ];
    }
}
