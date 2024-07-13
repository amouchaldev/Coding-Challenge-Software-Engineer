<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = Category::all();

        Product::factory()->count(20)->create()->each(function ($product) use ($categories) {
            $randomCategories = $categories->random(rand(1, 4))->pluck('id');
            $product->categories()->attach($randomCategories);
        });     
    }
}
