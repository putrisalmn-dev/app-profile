<?php

namespace App\Filament\Resources\Posts;

use App\Enums\PostStatus;
use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\Posts\Pages\EditPost;
use App\Filament\Resources\Posts\Pages\ListPosts;
use App\Models\Post;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('CMS Content');
    }

    public static function getModelLabel(): string
    {
        return __('Post');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Posts');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Post Content'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label(__('Title'))
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug((string) $state))),
                        TextInput::make('slug')
                            ->label(__('Slug'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Select::make('category_id')
                            ->label(__('Category'))
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload(),
                        Select::make('status')
                            ->label(__('Status'))
                            ->options(PostStatus::class)
                            ->default(PostStatus::Draft)
                            ->required(),
                        Textarea::make('summary')
                            ->label(__('Summary'))
                            ->rows(3)
                            ->columnSpanFull(),
                        RichEditor::make('content')
                            ->label(__('Content'))
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('posts/attachments')
                            ->columnSpanFull(),
                    ]),
                Section::make(__('Publishing & Media'))
                    ->columns(2)
                    ->schema([
                        FileUpload::make('image')
                            ->label(__('Featured Image'))
                            ->image()
                            ->disk('public')
                            ->directory('posts')
                            ->columnSpanFull(),
                        DateTimePicker::make('published_at')
                            ->label(__('Published Date'))
                            ->default(now()),
                        Toggle::make('is_featured')
                            ->label(__('Featured Post'))
                            ->default(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
            ->columns([
                ImageColumn::make('image')
                    ->label(__('Image'))
                    ->disk('public'),
                TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                TextColumn::make('category.name')
                    ->label(__('Category'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('author.name')
                    ->label(__('Author'))
                    ->toggleable(),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->sortable(),
                IconColumn::make('is_featured')
                    ->label(__('Featured'))
                    ->boolean(),
                TextColumn::make('view_count')
                    ->label(__('Views'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('published_at')
                    ->label(__('Published At'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options(PostStatus::class),
                SelectFilter::make('category')
                    ->label(__('Category'))
                    ->relationship('category', 'name'),
                TernaryFilter::make('is_featured')
                    ->label(__('Featured')),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'edit' => EditPost::route('/{record}/edit'),
        ];
    }
}
