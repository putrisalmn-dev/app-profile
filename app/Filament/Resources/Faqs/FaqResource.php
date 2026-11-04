<?php

namespace App\Filament\Resources\Faqs;

use App\Filament\Resources\Faqs\Pages\ManageFaqs;
use App\Models\Faq;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('Public Questions');
    }

    public static function getModelLabel(): string
    {
        return __('FAQ');
    }

    public static function getPluralModelLabel(): string
    {
        return __('FAQs');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('topic_id')
                    ->label(__('Topic'))
                    ->relationship('topic', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('sort_order')
                    ->label(__('Sort Order'))
                    ->numeric()
                    ->default(0)
                    ->required(),
                TextInput::make('question')
                    ->label(__('Question'))
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                RichEditor::make('answer')
                    ->label(__('Answer'))
                    ->required()
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
                TextColumn::make('question')
                    ->label(__('Question'))
                    ->searchable()
                    ->sortable()
                    ->limit(60),
                TextColumn::make('topic.name')
                    ->label(__('Topic'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label(__('Sort Order'))
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->boolean(),
            ])
            ->filters([
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

    public static function getPages(): array
    {
        return [
            'index' => ManageFaqs::route('/'),
        ];
    }
}
