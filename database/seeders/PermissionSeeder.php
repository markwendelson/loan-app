<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = config('modules');

        foreach ($permissions as $module => $data) {
            foreach ($data['permissions'] as $permission) {
                Permission::firstOrCreate(['name' => $permission, 'group' => '', 'guard_name' => 'web']);
            }
        }
    }
}
