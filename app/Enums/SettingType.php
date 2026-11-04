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
            self::Text => __('Text'),
            self::Textarea => __('Long Text'),
            self::Image => __('Image'),
            self::Boolean => __('Yes/No'),
            self::Json => __('JSON'),
        };
    }
}
