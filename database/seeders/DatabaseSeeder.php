<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\categories;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $categories = Categories::factory(5)->create();

        Product::factory(5)->create()->each(function ($products) use ($categories) {
            $randcat = $categories->random();
            $products->categories()->attach($randcat);
        });
        $this->call([
            ProductSeeder::class,
            CategoriesSeeder::class,
          
        ]);
    }
}
