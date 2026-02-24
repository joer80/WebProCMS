<?php

namespace App\Enums;

enum Role: int
{
    case Standard = 1;
    case Manager = 2;
    case Admin = 3;
    case Super = 4;

    public function isAtLeast(Role $role): bool
    {
        return $this->value >= $role->value;
    }
}
