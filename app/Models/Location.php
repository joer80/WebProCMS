<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Location extends Model
{
    /** @use HasFactory<\Database\Factories\LocationFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'city',
        'state',
        'state_full',
        'zip',
        'phone',
        'photo',
    ];

    protected static function booted(): void
    {
        static::deleted(function (Location $location): void {
            if ($location->photo) {
                Storage::disk('public')->delete($location->photo);
            }
        });
    }

    public function photoUrl(): ?string
    {
        if (! $this->photo) {
            return null;
        }

        if (str_starts_with($this->photo, 'http://') || str_starts_with($this->photo, 'https://')) {
            return $this->photo;
        }

        return Storage::disk('public')->url($this->photo);
    }
}
