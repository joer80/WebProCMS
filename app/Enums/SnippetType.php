<?php

namespace App\Enums;

enum SnippetType: string
{
    case Html = 'html';
    case Js = 'js';
    case Php = 'php';

    public function label(): string
    {
        return match ($this) {
            self::Html => 'HTML',
            self::Js => 'JavaScript',
            self::Php => 'PHP',
        };
    }

    public function defaultPlacement(): SnippetPlacement
    {
        return match ($this) {
            self::Html => SnippetPlacement::Head,
            self::Js => SnippetPlacement::BodyEnd,
            self::Php => SnippetPlacement::PhpTop,
        };
    }
}
