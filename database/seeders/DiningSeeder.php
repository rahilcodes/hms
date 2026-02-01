<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenuCategory;
use App\Models\MenuItem;

class DiningSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Popular Picks',
                'items' => [
                    ['name' => 'Classic Club Sandwich', 'price' => 15.00, 'description' => 'Triple-decker with chicken, bacon, lettuce, and tomato.'],
                    ['name' => 'Truffle Fries', 'price' => 9.00, 'description' => 'Crispy fries tossed in truffle oil and parmesan.'],
                ]
            ],
            [
                'name' => 'Main Course',
                'items' => [
                    ['name' => 'Pan-Seared Salmon', 'price' => 28.00, 'description' => 'Served with roasted seasonal vegetables and lemon butter sauce.'],
                    ['name' => 'Wagyu Beef Burger', 'price' => 24.00, 'description' => 'Premium wagyu beef patty with caramelized onions and aged cheddar.'],
                ]
            ],
            [
                'name' => 'Drinks',
                'items' => [
                    ['name' => 'Fresh Orange Juice', 'price' => 7.00, 'description' => 'Freshly squeezed 100% natural orange juice.'],
                    ['name' => 'Artisan Coffee', 'price' => 6.00, 'description' => 'Premium roast arabica coffee.'],
                ]
            ]
        ];

        foreach ($categories as $catData) {
            $category = MenuCategory::create(['name' => $catData['name']]);
            foreach ($catData['items'] as $itemData) {
                MenuItem::create(array_merge($itemData, ['menu_category_id' => $category->id]));
            }
        }
    }
}
