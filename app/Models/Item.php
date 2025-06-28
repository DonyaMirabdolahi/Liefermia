<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'active',
        'image_url'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    public function rules(): BelongsToMany
    {
        return $this->belongsToMany(Rule::class, 'item_rules');
    }

    public function extras(): BelongsToMany
    {
        return $this->belongsToMany(Extra::class, 'item_extras');
    }

    public function sizes(): BelongsToMany
    {
        return $this->belongsToMany(Size::class, 'item_sizes')
            ->withPivot('price');
    }
} 