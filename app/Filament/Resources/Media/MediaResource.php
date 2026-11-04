<?php

namespace App\Filament\Resources\Media;

use App\Filament\Resources\Media\Pages\ManageMedia;
use App\Models\Media;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Number;
use UnitEnum;

class MediaResource extends Resource
{
    protected static ?string $model = Media::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static string|UnitEnum|null $navigationGroup = 'Konten CMS';

    protected static ?string $modelLabel = 'Media';

    protected static ?string $pluralModelLabel = 'Media Library';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('file_path')
                    ->label('Berkas')
                    ->required()
                    ->disk('public')
                    ->directory('media')
                    ->storeFileNamesIn('file_name')
                    ->maxSize(10240)
                    ->columnSpanFull(),
                TextInput::make('alt_text')
                    ->label('Teks Alternatif (Alt Text)')
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('file_path')
                    ->label('Pratinjau')
                    ->disk('public'),
                TextColumn::make('file_name')
                    ->label('Nama Berkas')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('mime_type')
                    ->label('Tipe MIME')
                    ->badge(),
                TextColumn::make('file_size')
                    ->label('Ukuran')
                    ->formatStateUsing(fn (?int $state): string => Number::fileSize((int) $state))
                    ->sortable(),
                TextColumn::make('alt_text')
                    ->label('Alt Text')
                    ->limit(30),
                TextColumn::make('uploader.name')
                    ->label('Diunggah Oleh')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Diunggah')
                    ->dateTime()
                    ->sortable(),
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
            'index' => ManageMedia::route('/'),
        ];
    }
}
