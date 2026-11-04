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

    protected static string|UnitEnum|null $navigationGroup = 'Pertanyaan Masyarakat';

    protected static ?string $modelLabel = 'Pertanyaan';

    protected static ?string $pluralModelLabel = 'Pertanyaan Masyarakat';

    protected static ?int $navigationSort = 1;

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
                Section::make('Identitas Penanya')
                    ->columns(3)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label('No. Telepon / WhatsApp')
                            ->tel()
                            ->maxLength(30),
                    ]),
                Section::make('Isi Pertanyaan')
                    ->columns(2)
                    ->schema([
                        Select::make('topic_id')
                            ->label('Topik Pertanyaan')
                            ->relationship('topic', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('status')
                            ->label('Status')
                            ->options(QuestionStatus::class)
                            ->default(QuestionStatus::Pending)
                            ->required(),
                        TextInput::make('subject')
                            ->label('Subjek / Judul Pertanyaan')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Textarea::make('body')
                            ->label('Isi Pertanyaan')
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
                    ->label('Subjek')
                    ->searchable()
                    ->sortable()
                    ->limit(45),
                TextColumn::make('topic.name')
                    ->label('Topik')
                    ->badge()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Penanya')
                    ->description(fn (Question $record): string => $record->email)
                    ->searchable(['name', 'email']),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('answers_count')
                    ->label('Jawaban')
                    ->counts('answers')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Masuk Pada')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(QuestionStatus::class),
                SelectFilter::make('topic')
                    ->label('Topik')
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
