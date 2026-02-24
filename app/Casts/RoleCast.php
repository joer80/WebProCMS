<?php

namespace App\Casts;

use App\Enums\Role;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * @implements CastsAttributes<Role, string>
 */
class RoleCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): Role
    {
        return collect(Role::cases())->firstWhere('name', ucfirst((string) $value)) ?? Role::Standard;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        if ($value instanceof Role) {
            return strtolower($value->name);
        }

        return strtolower((string) $value);
    }
}
