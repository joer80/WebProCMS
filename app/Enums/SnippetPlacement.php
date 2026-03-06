<?php

namespace App\Enums;

enum SnippetPlacement: string
{
    case Head = 'head';
    case BodyEnd = 'body_end';
    case PhpTop = 'php_top';

    public function label(): string
    {
        return match ($this) {
            self::Head => 'Head (before </head>)',
            self::BodyEnd => 'Scripts (before </body>)',
            self::PhpTop => 'PHP (top of page)',
        };
    }
}
