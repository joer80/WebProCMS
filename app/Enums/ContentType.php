<?php

namespace App\Enums;

enum ContentType: string
{
    case Text = 'text';
    case Richtext = 'richtext';
    case Image = 'image';
    case Toggle = 'toggle';
    case Classes = 'classes';
    case Grid = 'grid';
    case FormSelect = 'form_select';

    public function label(): string
    {
        return match ($this) {
            ContentType::Text => 'Text',
            ContentType::Richtext => 'Rich Text',
            ContentType::Image => 'Image',
            ContentType::Toggle => 'Toggle',
            ContentType::Classes => 'Classes',
            ContentType::Grid => 'Grid',
            ContentType::FormSelect => 'Form Select',
        };
    }
}
