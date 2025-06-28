<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Http\Resources\ItemResource;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::with(['sizes', 'rules.extras.sizes', 'extras.sizes'])
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'min_price' => $item->sizes->min('pivot.price'),
                    'image_url' => $item->image_url ?? '/images/default-pizza.jpg'
                ];
            });
        return view('items.index', ['items' => $items]);
    }

    public function getItemsJson()
    {
        $items = Item::with(['sizes', 'rules.extras.sizes', 'extras.sizes'])
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'min_price' => $item->sizes->min('pivot.price'),
                    'image_url' => $item->image_url ?? '/images/default-pizza.jpg'
                ];
            });
        return response()->json($items);
    }

    public function getItemDetails($id)
    {
        $item = Item::with(['sizes', 'rules.extras.sizes', 'extras.sizes'])->findOrFail($id);
        
        $sizes = $item->sizes->map(function ($size) {
            return [
                'id' => $size->id,
                'name' => $size->name,
                'price' => $size->pivot->price
            ];
        });

        $rules = $item->rules->map(function ($rule) {
            return [
                'id' => $rule->id,
                'name' => $rule->name,
                'field_type' => $rule->field_type,
                'max_option' => $rule->max_option,
                'guard_name' => $rule->guard_name,
                'extras' => $rule->extras->map(function ($extra) {
                    return [
                        'id' => $extra->id,
                        'name' => $extra->name,
                        'prices' => $extra->sizes->mapWithKeys(function ($size) {
                            return [$size->id => $size->pivot->price];
                        })
                    ];
                })
            ];
        });

        return response()->json([
            'id' => $item->id,
            'name' => $item->name,
            'image_url' => $item->image_url ?? '/images/default-pizza.jpg',
            'max_option' => $item->max_option,
            'sizes' => $sizes,
            'rules' => $rules
        ]);
    }

    public function show(Item $item)
    {
        $item->load(['sizes', 'rules.extras.sizes', 'extras.sizes']);
        return new ItemResource($item);
    }

    public function store(Request $request)
    {
        info(json_encode($request->all()));
        return response()->json(['message' => 'Item recived successfully'], 201);
    }
} 