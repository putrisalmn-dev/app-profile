<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum MenuLocation: string implements HasLabel
{
    case Header = 'header';
    case Footer = 'footer';
    case Sidebar = 'sidebar';

    public function getLabel(): string
    {
        return match ($this) {
            self::Header => 'Header',
            self::Footer => 'Footer',
            self::Sidebar => 'Sidebar',
        };
    }
}
