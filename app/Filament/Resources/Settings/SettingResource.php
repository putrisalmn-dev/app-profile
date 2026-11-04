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

    protected static string|UnitEnum|null $navigationGroup = 'Sistem';

    protected static ?string $modelLabel = 'Pengaturan';

    protected static ?string $pluralModelLabel = 'Pengaturan Situs';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('group')
                    ->label('Grup')
                    ->options([
                        'general' => 'Umum',
                        'contact' => 'Kontak',
                        'social' => 'Media Sosial',
                    ])
                    ->default('general')
                    ->required(),
                TextInput::make('key')
                    ->label('Kunci (Key)')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->alphaDash()
                    ->maxLength(255),
                TextInput::make('label')
                    ->label('Label')
                    ->maxLength(255),
                Select::make('type')
                    ->label('Tipe Nilai')
                    ->options(SettingType::class)
                    ->default(SettingType::Text)
                    ->live()
                    ->required(),
                TextInput::make('value')
                    ->label('Nilai')
                    ->visible(fn (Get $get): bool => self::typeIs($get('type'), SettingType::Text))
                    ->dehydrated(fn (Get $get): bool => self::typeIs($get('type'), SettingType::Text))
                    ->columnSpanFull(),
                Textarea::make('value')
                    ->label('Nilai')
                    ->rows(4)
                    ->visible(fn (Get $get): bool => in_array(self::normalizeType($get('type')), [SettingType::Textarea, SettingType::Json], true))
                    ->dehydrated(fn (Get $get): bool => in_array(self::normalizeType($get('type')), [SettingType::Textarea, SettingType::Json], true))
                    ->rule(fn (Get $get) => self::typeIs($get('type'), SettingType::Json) ? 'json' : null)
                    ->columnSpanFull(),
                FileUpload::make('value')
                    ->label('Gambar')
                    ->image()
                    ->disk('public')
                    ->directory('settings')
                    ->visible(fn (Get $get): bool => self::typeIs($get('type'), SettingType::Image))
                    ->dehydrated(fn (Get $get): bool => self::typeIs($get('type'), SettingType::Image))
                    ->columnSpanFull(),
                Toggle::make('value')
                    ->label('Aktif')
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
                    ->label('Grup')
                    ->badge()
                    ->sortable(),
                TextColumn::make('key')
                    ->label('Kunci')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('label')
                    ->label('Label')
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge(),
                TextColumn::make('value')
                    ->label('Nilai')
                    ->limit(50)
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('group')
                    ->label('Grup')
                    ->options([
                        'general' => 'Umum',
                        'contact' => 'Kontak',
                        'social' => 'Media Sosial',
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
