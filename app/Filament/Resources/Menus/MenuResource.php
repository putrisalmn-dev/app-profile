<?php

namespace App\Filament\Resources\Menus;

use App\Enums\MenuItemType;
use App\Enums\MenuLocation;
use App\Filament\Resources\Menus\Pages\CreateMenu;
use App\Filament\Resources\Menus\Pages\EditMenu;
use App\Filament\Resources\Menus\Pages\ListMenus;
use App\Models\Menu;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBars3;

    protected static string|UnitEnum|null $navigationGroup = 'Konten CMS';

    protected static ?string $modelLabel = 'Menu Navigasi';

    protected static ?string $pluralModelLabel = 'Menu Navigasi';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Menu')
                    ->columns(3)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Menu')
                            ->required()
                            ->maxLength(255),
                        Select::make('location')
                            ->label('Lokasi')
                            ->options(MenuLocation::class)
                            ->required(),
                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->inline(false),
                    ]),
                Section::make('Item Menu')
                    ->schema([
                        Repeater::make('rootItems')
                            ->relationship('rootItems')
                            ->label('Daftar Item Menu')
                            ->orderColumn('sort_order')
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                            ->schema([
                                ...self::menuItemFields(),
                                Repeater::make('children')
                                    ->relationship('children')
                                    ->label('Sub-menu')
                                    ->orderColumn('sort_order')
                                    ->collapsible()
                                    ->collapsed()
                                    ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                                    ->schema(self::menuItemFields())
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Menu')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('location')
                    ->label('Lokasi')
                    ->badge()
                    ->sortable(),
                TextColumn::make('items_count')
                    ->label('Jumlah Item')
                    ->counts('items'),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMenus::route('/'),
            'create' => CreateMenu::route('/create'),
            'edit' => EditMenu::route('/{record}/edit'),
        ];
    }

    /**
     * @return array<int, Component>
     */
    private static function menuItemFields(): array
    {
        return [
            TextInput::make('label')
                ->label('Label')
                ->required()
                ->maxLength(255),
            Select::make('type')
                ->label('Tipe Tautan')
                ->options(MenuItemType::class)
                ->default(MenuItemType::Custom)
                ->live()
                ->afterStateUpdated(function (Set $set): void {
                    $set('linkable_id', null);
                    $set('url', null);
                })
                ->required(),
            TextInput::make('url')
                ->label('URL')
                ->placeholder('/tentang-kami atau https://...')
                ->visible(fn (Get $get): bool => self::normalizeType($get('type')) === MenuItemType::Custom)
                ->required(fn (Get $get): bool => self::normalizeType($get('type')) === MenuItemType::Custom)
                ->maxLength(255),
            Select::make('linkable_id')
                ->label('Target Konten')
                ->options(function (Get $get): array {
                    $type = self::normalizeType($get('type'));
                    $modelClass = $type->modelClass();
                    $titleColumn = $type->titleColumn();

                    if ($modelClass === null || $titleColumn === null) {
                        return [];
                    }

                    return $modelClass::query()->pluck($titleColumn, 'id')->all();
                })
                ->searchable()
                ->visible(fn (Get $get): bool => self::normalizeType($get('type')) !== MenuItemType::Custom)
                ->required(fn (Get $get): bool => self::normalizeType($get('type')) !== MenuItemType::Custom),
            Toggle::make('is_active')
                ->label('Aktif')
                ->default(true),
        ];
    }

    private static function normalizeType(MenuItemType|string|null $type): MenuItemType
    {
        if ($type instanceof MenuItemType) {
            return $type;
        }

        return MenuItemType::tryFrom((string) $type) ?? MenuItemType::Custom;
    }
}
