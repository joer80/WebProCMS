<?php

namespace App\Enums;

enum ContentType: string
{
    case Text = 'text';
    case Richtext = 'richtext';
    case Image = 'image';
    case Toggle = 'toggle';

    public function label(): string
    {
        return match ($this) {
            ContentType::Text => 'Text',
            ContentType::Richtext => 'Rich Text',
            ContentType::Image => 'Image',
            ContentType::Toggle => 'Toggle',
        };
    }
}
