<?php

namespace App\Enums;

enum PageCategory: string
{
    case SaaS = 'saas';
    case Service = 'service';
    case ECommerce = 'ecommerce';
    case Law = 'law';
    case Nonprofit = 'nonprofit';
    case Healthcare = 'healthcare';
    case Custom = 'custom';

    public function label(): string
    {
        return match ($this) {
            self::SaaS => 'SaaS',
            self::ECommerce => 'eCommerce',
            default => ucfirst($this->value),
        };
    }
}
