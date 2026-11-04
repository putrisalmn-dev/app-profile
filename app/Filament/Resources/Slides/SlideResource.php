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

    protected static string|UnitEnum|null $navigationGroup = 'Konten CMS';

    protected static ?string $modelLabel = 'Slide Banner';

    protected static ?string $pluralModelLabel = 'Slide Banner';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Judul Slide')
                    ->required()
                    ->maxLength(255),
                TextInput::make('link_url')
                    ->label('URL Tautan')
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(2)
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->label('Gambar Slide')
                    ->required()
                    ->image()
                    ->disk('public')
                    ->directory('slides')
                    ->columnSpanFull(),
                DatePicker::make('start_date')
                    ->label('Tanggal Mulai Tayang'),
                DatePicker::make('end_date')
                    ->label('Tanggal Selesai Tayang')
                    ->afterOrEqual('start_date'),
                TextInput::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->default(0)
                    ->required(),
                Toggle::make('is_active')
                    ->label('Aktif')
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
                    ->label('Gambar')
                    ->disk('public'),
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('start_date')
                    ->label('Mulai')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('Selesai')
                    ->date()
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Aktif')
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
