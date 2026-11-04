<?php

namespace App\Models;

use App\Enums\SettingType;
use Database\Factories\SettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

#[Fillable(['group', 'key', 'label', 'value', 'type'])]
class Setting extends Model
{
    /** @use HasFactory<SettingFactory> */
    use HasFactory;

    public const CACHE_KEY = 'settings.public';

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY));
    }

    protected function casts(): array
    {
        return [
            'type' => SettingType::class,
        ];
    }

    /**
     * The value converted according to the setting type.
     */
    public function typedValue(): mixed
    {
        if ($this->value === null || $this->value === '') {
            return $this->type === SettingType::Boolean ? false : null;
        }

        return match ($this->type) {
            SettingType::Boolean => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            SettingType::Json => json_decode($this->value, true),
            SettingType::Image => Storage::disk('public')->url($this->value),
            default => $this->value,
        };
    }

    /**
     * All settings grouped by their group, keyed by setting key, with typed values.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function grouped(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, fn (): array => static::query()
            ->orderBy('group')
            ->orderBy('key')
            ->get()
            ->groupBy('group')
            ->map(fn ($settings) => $settings->mapWithKeys(fn (Setting $setting) => [$setting->key => $setting->typedValue()]))
            ->toArray());
    }
}
