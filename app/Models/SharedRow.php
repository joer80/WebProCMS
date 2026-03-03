<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SharedRow extends Model
{
    protected $fillable = [
        'slug',
        'name',
    ];
}
