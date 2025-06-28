<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $maxOptionsRule = $this->rules->first(function ($rule) {
            return $rule->guard_name === 'max_options';
        });
        
        $freeExtras = $this->rules
            ->where('guard_name', 'free_options')
            ->pluck('extras')
            ->flatten()
            ->pluck('id')
            ->unique()
            ->values();

        $item = $this;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'active' => $this->active,
            'image_url' => $this->image_url ?? '/images/default-pizza.jpg',
            'max_option' => $maxOptionsRule ? $maxOptionsRule->max_option : null,
            'sizes' => $this->sizes->map(function ($size) use ($freeExtras, $item) {
                return [
                    'id' => $size->id,
                    'name' => $size->name,
                    'price' => $size->pivot->price,
                    'rules' => $item->rules
                        ->where('guard_name', '!=', 'max_options')
                        ->map(function ($rule) use ($size) {
                            return [
                                'id' => $rule->id,
                                'name' => $rule->name,
                                'guard_name' => $rule->guard_name,
                                'max_option' => $rule->max_option,
                                'field_type' => $rule->field_type,
                                'extras' => $rule->extras->map(function ($extra) use ($size, $rule) {
                                    $sizePrice = $extra->sizes->where('id', $size->id)->first();
                                    return [
                                        'id' => $extra->id,
                                        'name' => $extra->name,
                                        'is_free' => true,
                                        'price' => ($rule->guard_name === 'free_options') ? 0 : $sizePrice?->pivot->price ?? 0
                                    ];
                                })
                            ];
                        })->values(),
                    'extras' => $item->extras->map(function ($extra) use ($size, $freeExtras) {
                        $sizePrice = $extra->sizes->where('id', $size->id)->first();
                        return [
                            'id' => $extra->id,
                            'name' => $extra->name,
                            'is_free' => $freeExtras->contains($extra->id),
                            'price' => $sizePrice ? $sizePrice->pivot->price : 0
                        ];
                    })
                ];
            })
        ];
    }
} 