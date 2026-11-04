<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum QuestionStatus: string implements HasColor, HasLabel
{
    case Pending = 'pending';
    case Answered = 'answered';
    case Rejected = 'rejected';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu',
            self::Answered => 'Dijawab',
            self::Rejected => 'Ditolak',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Answered => 'success',
            self::Rejected => 'danger',
        };
    }
}
