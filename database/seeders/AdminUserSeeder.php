<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        $author1 = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Aalam',
                'password' => bcrypt('password123'),
                'email_verified_at' => now(),
            ]
        );
        $author1->assignRole('author');

        $author2 = User::firstOrCreate(
            ['email' => 'author@author.com'],
            [
                'name' => 'Salman',
                'password' => bcrypt('password123'),
                'email_verified_at' => now(),
            ]
        );
        $author2->assignRole('author');
    }
}
