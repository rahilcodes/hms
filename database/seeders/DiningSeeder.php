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
                    ['name' => 'Classic Club Sandwich', 'price' => 450, 'description' => 'Triple-decker with chicken, bacon, lettuce, and tomato.'],
                    ['name' => 'Truffle Fries', 'price' => 250, 'description' => 'Crispy fries tossed in truffle oil and parmesan.'],
                ]
            ],
            [
                'name' => 'Main Course',
                'items' => [
                    ['name' => 'Pan-Seared Salmon', 'price' => 1200, 'description' => 'Served with roasted seasonal vegetables and lemon butter sauce.'],
                    ['name' => 'Wagyu Beef Burger', 'price' => 850, 'description' => 'Premium wagyu beef patty with caramelized onions and aged cheddar.'],
                    ['name' => 'Butter Chicken & Naan', 'price' => 650, 'description' => 'Classic creamy tomato curry with tender chicken pieces.'],
                ]
            ],
            [
                'name' => 'Drinks',
                'items' => [
                    ['name' => 'Fresh Orange Juice', 'price' => 200, 'description' => 'Freshly squeezed 100% natural orange juice.'],
                    ['name' => 'Artisan Coffee', 'price' => 180, 'description' => 'Premium roast arabica coffee.'],
                    ['name' => 'Masala Chai', 'price' => 120, 'description' => 'Traditional spiced tea.'],
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
