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

class AnswersRelationManager extends RelationManager
{
    protected static string $relationship = 'answers';

    protected static ?string $title = 'Jawaban Petugas';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('status')
                    ->label('Status Jawaban')
                    ->options(AnswerStatus::class)
                    ->default(AnswerStatus::Published)
                    ->helperText('Jika status "Terbit", notifikasi email akan dikirim ke penanya dan status pertanyaan otomatis menjadi Dijawab.')
                    ->required(),
                RichEditor::make('body')
                    ->label('Isi Jawaban')
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
                    ->label('Dijawab Oleh')
                    ->placeholder('Sistem'),
                TextColumn::make('body')
                    ->label('Isi Jawaban')
                    ->html()
                    ->limit(80),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                TextColumn::make('answered_at')
                    ->label('Waktu Jawab')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tulis Jawaban'),
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
