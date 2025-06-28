<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Extra extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public function rules(): BelongsToMany
    {
        return $this->belongsToMany(Rule::class, 'rule_extras');
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'item_extras');
    }

    public function sizes(): BelongsToMany
    {
        return $this->belongsToMany(Size::class, 'extra_sizes')
            ->withPivot('price');
    }
} 