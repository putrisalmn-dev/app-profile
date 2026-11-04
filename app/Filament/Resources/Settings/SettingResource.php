<?php

namespace App\Filament\Resources\Settings;

use App\Enums\SettingType;
use App\Filament\Resources\Settings\Pages\ManageSettings;
use App\Models\Setting;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('System');
    }

    public static function getModelLabel(): string
    {
        return __('Setting');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Site Settings');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('group')
                    ->label(__('Group'))
                    ->options([
                        'general' => __('General'),
                        'contact' => __('Contact'),
                        'social' => __('Social Media'),
                    ])
                    ->default('general')
                    ->required(),
                TextInput::make('key')
                    ->label(__('Key'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->alphaDash()
                    ->maxLength(255),
                TextInput::make('label')
                    ->label(__('Label'))
                    ->maxLength(255),
                Select::make('type')
                    ->label(__('Value Type'))
                    ->options(SettingType::class)
                    ->default(SettingType::Text)
                    ->live()
                    ->required(),
                TextInput::make('value')
                    ->label(__('Value'))
                    ->visible(fn (Get $get): bool => self::typeIs($get('type'), SettingType::Text))
                    ->dehydrated(fn (Get $get): bool => self::typeIs($get('type'), SettingType::Text))
                    ->columnSpanFull(),
                Textarea::make('value')
                    ->label(__('Value'))
                    ->rows(4)
                    ->visible(fn (Get $get): bool => in_array(self::normalizeType($get('type')), [SettingType::Textarea, SettingType::Json], true))
                    ->dehydrated(fn (Get $get): bool => in_array(self::normalizeType($get('type')), [SettingType::Textarea, SettingType::Json], true))
                    ->rule(fn (Get $get) => self::typeIs($get('type'), SettingType::Json) ? 'json' : null)
                    ->columnSpanFull(),
                FileUpload::make('value')
                    ->label(__('Image'))
                    ->image()
                    ->disk('public')
                    ->directory('settings')
                    ->visible(fn (Get $get): bool => self::typeIs($get('type'), SettingType::Image))
                    ->dehydrated(fn (Get $get): bool => self::typeIs($get('type'), SettingType::Image))
                    ->columnSpanFull(),
                Toggle::make('value')
                    ->label(__('Active'))
                    ->visible(fn (Get $get): bool => self::typeIs($get('type'), SettingType::Boolean))
                    ->dehydrated(fn (Get $get): bool => self::typeIs($get('type'), SettingType::Boolean))
                    ->formatStateUsing(fn (mixed $state): bool => filter_var($state, FILTER_VALIDATE_BOOLEAN))
                    ->dehydrateStateUsing(fn (mixed $state): string => $state ? '1' : '0')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultGroup('group')
            ->columns([
                TextColumn::make('group')
                    ->label(__('Group'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('key')
                    ->label(__('Key'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('label')
                    ->label(__('Label'))
                    ->searchable(),
                TextColumn::make('type')
                    ->label(__('Type'))
                    ->badge(),
                TextColumn::make('value')
                    ->label(__('Value'))
                    ->limit(50)
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('group')
                    ->label(__('Group'))
                    ->options([
                        'general' => __('General'),
                        'contact' => __('Contact'),
                        'social' => __('Social Media'),
                    ]),
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
            'index' => ManageSettings::route('/'),
        ];
    }

    private static function normalizeType(SettingType|string|null $type): SettingType
    {
        if ($type instanceof SettingType) {
            return $type;
        }

        return SettingType::tryFrom((string) $type) ?? SettingType::Text;
    }

    private static function typeIs(SettingType|string|null $current, SettingType $expected): bool
    {
        return self::normalizeType($current) === $expected;
    }
}
