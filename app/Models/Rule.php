<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Rule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'guard_name',
        'max_option',
        'field_type'
    ];

    protected $casts = [
        'max_option' => 'integer'
    ];

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'item_rules');
    }

    public function extras(): BelongsToMany
    {
        return $this->belongsToMany(Extra::class, 'rule_extras');
    }
} 