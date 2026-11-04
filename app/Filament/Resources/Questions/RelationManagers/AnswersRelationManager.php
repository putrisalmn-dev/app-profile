<?php

namespace App\Filament\Resources\Questions\RelationManagers;

use App\Enums\AnswerStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class AnswersRelationManager extends RelationManager
{
    protected static string $relationship = 'answers';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Staff Answers');
    }

    public static function getModelLabel(): ?string
    {
        return __('Answer');
    }

    public static function getPluralModelLabel(): ?string
    {
        return __('Staff Answers');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('status')
                    ->label(__('Answer Status'))
                    ->options(AnswerStatus::class)
                    ->default(AnswerStatus::Published)
                    ->helperText(__('If set to "Published", an email notification will be sent to the asker and the question status will automatically become Answered.'))
                    ->required(),
                RichEditor::make('body')
                    ->label(__('Answer Body'))
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('author.name')
                    ->label(__('Answered By'))
                    ->placeholder(__('System')),
                TextColumn::make('body')
                    ->label(__('Answer Body'))
                    ->html()
                    ->limit(80),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge(),
                TextColumn::make('answered_at')
                    ->label(__('Answered At'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('Write Answer')),
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
}
