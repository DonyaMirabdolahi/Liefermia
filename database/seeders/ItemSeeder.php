<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Rule;
use App\Models\Extra;
use App\Models\Size;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $sizes = [
            'Small' => Size::create(['name' => 'Small']),
            'Medium' => Size::create(['name' => 'Medium']),
            'Large' => Size::create(['name' => 'Large']),
            'XLarge' => Size::create(['name' => 'XLarge']),
        ];

        $extraTypes = [
            'Cheese' => ['Mozzarella', 'Cheddar', 'Parmesan', 'Gouda', 'Blue Cheese'],
            'Meats' => ['Pepperoni', 'Italian Sausage', 'Ham', 'Bacon', 'Chicken', 'Ground Beef', 'Salami'],
            'Vegetables' => ['Mushrooms', 'Onions', 'Bell Peppers', 'Olives', 'Tomatoes', 'Jalapeños', 'Spinach', 'Garlic', 'Artichokes'],
            'Premium' => ['Shrimp', 'Anchovies', 'Sun-dried Tomatoes', 'Pineapple', 'Roasted Peppers']
        ];

        $extras = [];
        foreach ($extraTypes as $type => $items) {
            foreach ($items as $item) {
                $extras[$item] = Extra::create(['name' => $item]);
            }
        }

        $rules = [];
        
        $ruleConfigs = [
            [
                'name' => 'Choose Your Cheese',
                'guard_name' => 'free_options',
                'field_type' => 'dropdown',
                'extras_type' => 'Cheese',
                'max_option' => 1
            ],
            [
                'name' => 'Extra Cheese Options',
                'guard_name' => 'free_options',
                'field_type' => 'checkbox',
                'extras_type' => 'Cheese',
                'max_option' => 2
            ],
            [
                'name' => 'Select Main Meat',
                'guard_name' => 'free_options',
                'field_type' => 'dropdown',
                'extras_type' => 'Meats',
                'max_option' => 1
            ],
            [
                'name' => 'Additional Meats',
                'guard_name' => 'free_options',
                'field_type' => 'checkbox',
                'extras_type' => 'Meats',
                'max_option' => 3
            ],
            [
                'name' => 'Choose Vegetables',
                'guard_name' => 'free_options',
                'field_type' => 'checkbox',
                'extras_type' => 'Vegetables',
                'max_option' => 4
            ],
            [
                'name' => 'Main Vegetable',
                'guard_name' => 'free_options',
                'field_type' => 'dropdown',
                'extras_type' => 'Vegetables',
                'max_option' => 1
            ],
            [
                'name' => 'Premium Selection',
                'guard_name' => 'free_options',
                'field_type' => 'dropdown',
                'extras_type' => 'Premium',
                'max_option' => 1
            ],
            [
                'name' => 'Multiple Premium Toppings',
                'guard_name' => 'free_options',
                'field_type' => 'checkbox',
                'extras_type' => 'Premium',
                'max_option' => 2
            ],
        ];

        foreach ($ruleConfigs as $config) {
            $rule = Rule::create([
                'name' => $config['name'],
                'guard_name' => $config['guard_name'],
                'field_type' => $config['field_type'],
                'max_option' => $config['max_option']
            ]);

            $rule->extras()->attach(
                collect($extras)->filter(function($extra, $name) use ($extraTypes, $config) {
                    return in_array($name, $extraTypes[$config['extras_type']]);
                })->pluck('id')
            );

            $rules[$config['name']] = $rule;
        }

        for ($i = 2; $i <= 5; $i++) {
            $rule = Rule::create([
                'name' => "Maximum {$i} Toppings",
                'guard_name' => 'max_options',
                'max_option' => $i,
                'field_type' => 'checkbox'
            ]);
            
            $rule->extras()->attach(collect($extras)->pluck('id'));
            
            $rules["max_{$i}_toppings"] = $rule;
        }

        $pizzaStyles = [
            'Classic' => [
                'description' => 'Traditional pizza with classic toppings', 
                'max_extras' => 3,
                'possible_rules' => [
                    'Choose Your Cheese',
                    'Extra Cheese Options',
                    'Select Main Meat',
                    'Choose Vegetables'
                ]
            ],
            'Supreme' => [
                'description' => 'Loaded with premium toppings', 
                'max_extras' => 5,
                'possible_rules' => [
                    'Additional Meats',
                    'Choose Vegetables',
                    'Multiple Premium Toppings',
                    'Extra Cheese Options'
                ]
            ],
            'Specialty' => [
                'description' => 'Unique combination of flavors', 
                'max_extras' => 4,
                'possible_rules' => [
                    'Premium Selection',
                    'Main Vegetable',
                    'Choose Your Cheese',
                    'Select Main Meat'
                ]
            ],
            'Basic' => [
                'description' => 'Simple and delicious', 
                'max_extras' => 2,
                'possible_rules' => [
                    'Choose Your Cheese',
                    'Select Main Meat',
                    'Main Vegetable'
                ]
            ],
        ];

        foreach ($pizzaStyles as $style => $config) {
            for ($i = 1; $i <= $faker->numberBetween(2, 4); $i++) {
                $name = $faker->randomElement([
                    "{$style} {$faker->word}", 
                    "{$faker->word} {$style}", 
                    "{$faker->colorName} {$style}"
                ]);

                $basePrice = $faker->randomFloat(2, 8, 12);
                $pizzaData = [
                    'name' => ucwords($name),
                    'description' => $config['description'] . '. ' . $faker->sentence(),
                    'active' => $faker->boolean(80),
                    'sizes' => [
                        'Small' => $basePrice,
                        'Medium' => $basePrice + 3,
                        'Large' => $basePrice + 6,
                        'XLarge' => $basePrice + 9,
                    ],
                    'extras' => [],
                    'rules' => []
                ];

                $numExtras = $faker->numberBetween(2, $config['max_extras']);
                $pizzaData['extras'] = $faker->randomElements(array_keys($extras), $numExtras);

                $maxRule = "max_{$config['max_extras']}_toppings";
                $pizzaData['rules'][] = $maxRule;

                $numRules = $faker->numberBetween(1, count($config['possible_rules']));
                $selectedRules = $faker->randomElements($config['possible_rules'], $numRules);
                
                $pizza = Item::create([
                    'name' => $pizzaData['name'],
                    'description' => $pizzaData['description'],
                    'active' => $pizzaData['active'],
                    'image_url' => $faker->randomElement([
                        '/images/pizzas/pizza1.webp',
                        '/images/pizzas/pizza2.webp',
                        '/images/pizzas/pizza3.webp',
                        '/images/pizzas/pizza4.webp',
                        '/images/pizzas/pizza5.webp',
                        '/images/pizzas/pizza6.png',
                        '/images/pizzas/pizza7.png',
                        '/images/pizzas/pizza8.webp'
                    ])
                ]);

                foreach ($pizzaData['sizes'] as $sizeName => $price) {
                    $pizza->sizes()->attach($sizes[$sizeName], ['price' => $price]);
                }

                foreach ($pizzaData['extras'] as $extraName) {
                    $pizza->extras()->attach($extras[$extraName]);
                }

                $pizza->rules()->attach($rules[$maxRule]);
                foreach ($selectedRules as $ruleName) {
                    $pizza->rules()->attach($rules[$ruleName]);
                }
            }
        }

        foreach ($extras as $extraName => $extra) {
            $basePrice = 0;
            
            if (in_array($extraName, $extraTypes['Premium'])) {
                $basePrice = $faker->randomFloat(2, 2, 3);
            } elseif (in_array($extraName, $extraTypes['Meats'])) {
                $basePrice = $faker->randomFloat(2, 1.5, 2.5);
            } elseif (in_array($extraName, $extraTypes['Cheese'])) {
                $basePrice = $faker->randomFloat(2, 1, 2);
            } else {
                $basePrice = $faker->randomFloat(2, 0.75, 1.5);
            }

            foreach ($sizes as $sizeName => $size) {
                $priceMultiplier = [
                    'Small' => 1,
                    'Medium' => 1.5,
                    'Large' => 2,
                    'XLarge' => 2.5
                ][$sizeName];

                $extra->sizes()->attach($size, [
                    'price' => round($basePrice * $priceMultiplier, 2)
                ]);
            }
        }
    }
} 