<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum SettingType: string implements HasLabel
{
    case Text = 'text';
    case Textarea = 'textarea';
    case Image = 'image';
    case Boolean = 'boolean';
    case Json = 'json';

    public function getLabel(): string
    {
        return match ($this) {
            self::Text => 'Teks',
            self::Textarea => 'Teks Panjang',
            self::Image => 'Gambar',
            self::Boolean => 'Ya/Tidak',
            self::Json => 'JSON',
        };
    }
}
