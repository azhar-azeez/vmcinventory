<?php

namespace App\Enums;

enum CustomerType: string
{
    case RENT = 'rent';
    case WHOLESALER = 'wholesaler';

    public function label(): string
    {
        return match ($this) {
            self::RENT => __('Rent'),
            self::WHOLESALER => __('Wholesaler'),
        };
    }
}