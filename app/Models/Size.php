<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Size extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'item_sizes')
            ->withPivot('price');
    }

    public function extras(): BelongsToMany
    {
        return $this->belongsToMany(Extra::class, 'extra_sizes')
            ->withPivot('price');
    }
} 