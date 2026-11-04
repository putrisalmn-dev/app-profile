<?php

namespace App\Filament\Resources\Slides;

use App\Filament\Resources\Slides\Pages\ManageSlides;
use App\Models\Slide;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class SlideResource extends Resource
{
    protected static ?string $model = Slide::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPresentationChartBar;

    protected static ?int $navigationSort = 6;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('CMS Content');
    }

    public static function getModelLabel(): string
    {
        return __('Banner Slide');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Banner Slides');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label(__('Slide Title'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('link_url')
                    ->label(__('Link URL'))
                    ->maxLength(255),
                Textarea::make('description')
                    ->label(__('Description'))
                    ->rows(2)
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->label(__('Slide Image'))
                    ->required()
                    ->image()
                    ->disk('public')
                    ->directory('slides')
                    ->columnSpanFull(),
                DatePicker::make('start_date')
                    ->label(__('Start Date')),
                DatePicker::make('end_date')
                    ->label(__('End Date'))
                    ->afterOrEqual('start_date'),
                TextInput::make('sort_order')
                    ->label(__('Sort Order'))
                    ->numeric()
                    ->default(0)
                    ->required(),
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
                ImageColumn::make('image')
                    ->label(__('Image'))
                    ->disk('public'),
                TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('start_date')
                    ->label(__('Start'))
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label(__('End'))
                    ->date()
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label(__('Sort Order'))
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
            'index' => ManageSlides::route('/'),
        ];
    }
}
