<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view users', 'create users', 'edit users', 'delete users',

            'view posts', 'create posts', 'edit own posts', 'edit any posts',
            'delete own posts', 'delete any posts',

            'view categories', 'create categories', 'edit categories', 'delete categories',

            'view tags', 'create tags', 'edit tags', 'delete tags',

            'view comments', 'create comments', 'edit own comments',
            'edit any comments', 'delete own comments', 'delete any comments',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'api']);
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);
        $authorRole = Role::firstOrCreate(['name' => 'author', 'guard_name' => 'api']);

        $adminRole->givePermissionTo(Permission::all());

        $authorRole->givePermissionTo([
            'view users',
            'view posts',
            'create posts',
            'edit own posts',
            'delete own posts',
            'view categories',
            'view tags',
            'view comments',
            'create comments',
            'edit own comments',
            'delete own comments',
        ]);
    }
}
