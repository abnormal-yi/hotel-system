<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeatureFlag extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'label',
        'enabled',
        'module',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];
}
