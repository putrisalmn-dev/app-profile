<?php

namespace App\Filament\Resources\QuestionTopics;

use App\Filament\Resources\QuestionTopics\Pages\ManageQuestionTopics;
use App\Models\QuestionTopic;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class QuestionTopicResource extends Resource
{
    protected static ?string $model = QuestionTopic::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolder;

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('Public Questions');
    }

    public static function getModelLabel(): string
    {
        return __('Question Topic');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Question Topics');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('Topic Name'))
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug((string) $state))),
                TextInput::make('slug')
                    ->label(__('Slug'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('icon')
                    ->label(__('Icon'))
                    ->placeholder('heroicon-o-chat-bubble-left-right')
                    ->maxLength(100),
                TextInput::make('sort_order')
                    ->label(__('Sort Order'))
                    ->numeric()
                    ->default(0)
                    ->required(),
                Textarea::make('description')
                    ->label(__('Description'))
                    ->rows(3)
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label(__('Active'))
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('name')
                    ->label(__('Topic Name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->label(__('Slug')),
                TextColumn::make('questions_count')
                    ->label(__('Questions'))
                    ->counts('questions')
                    ->sortable(),
                TextColumn::make('faqs_count')
                    ->label(__('FAQ'))
                    ->counts('faqs')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->boolean(),
            ])
            ->filters([
                //
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

    public static function getPages(): array
    {
        return [
            'index' => ManageQuestionTopics::route('/'),
        ];
    }
}
