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

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('CMS Content');
    }

    public static function getModelLabel(): string
    {
        return __('Navigation Menu');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Navigation Menus');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Menu Information'))
                    ->columns(3)
                    ->schema([
                        TextInput::make('name')
                            ->label(__('Menu Name'))
                            ->required()
                            ->maxLength(255),
                        Select::make('location')
                            ->label(__('Location'))
                            ->options(MenuLocation::class)
                            ->required(),
                        Toggle::make('is_active')
                            ->label(__('Active'))
                            ->default(true)
                            ->inline(false),
                    ]),
                Section::make(__('Menu Items'))
                    ->schema([
                        Repeater::make('rootItems')
                            ->relationship('rootItems')
                            ->label(__('Menu Item List'))
                            ->orderColumn('sort_order')
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                            ->schema([
                                ...self::menuItemFields(),
                                Repeater::make('children')
                                    ->relationship('children')
                                    ->label(__('Submenu'))
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
                    ->label(__('Menu Name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('location')
                    ->label(__('Location'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('items_count')
                    ->label(__('Items Count'))
                    ->counts('items'),
                IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label(__('Updated At'))
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
                ->label(__('Label'))
                ->required()
                ->maxLength(255),
            Select::make('type')
                ->label(__('Link Type'))
                ->options(MenuItemType::class)
                ->default(MenuItemType::Custom)
                ->live()
                ->afterStateUpdated(function (Set $set): void {
                    $set('linkable_id', null);
                    $set('url', null);
                })
                ->required(),
            TextInput::make('url')
                ->label(__('URL'))
                ->placeholder(__('/about-us or https://...'))
                ->visible(fn (Get $get): bool => self::normalizeType($get('type')) === MenuItemType::Custom)
                ->required(fn (Get $get): bool => self::normalizeType($get('type')) === MenuItemType::Custom)
                ->maxLength(255),
            Select::make('linkable_id')
                ->label(__('Target Content'))
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
                ->label(__('Active'))
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
