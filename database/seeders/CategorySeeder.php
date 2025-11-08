<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Technology', 'description' => 'This is technology category'],
            ['name' => 'Travel', 'description' => 'This is Travel category'],
            ['name' => 'Food', 'description' => 'This is Food Category'],
            ['name' => 'Business', 'description' => 'This is Business category'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                [
                    'description' => $category['description'],
                    'slug' => Str::slug($category['name']), 
                ]
            );
        }
    }
}
