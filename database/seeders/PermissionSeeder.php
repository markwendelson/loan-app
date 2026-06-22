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

        $modules = config('modules');

        foreach ($modules as $moduleKey => $module) {
            foreach ($module['permissions'] as $permission) {
                if (isset($permission['name']) && isset($permission['label'])) {
                    Permission::firstOrCreate([
                        'name' => $permission['name'],
                        'group' => $moduleKey,
                        'guard_name' => 'web',
                    ]);
                } else {
                    \Log::error("Permission missing name or label in module: $moduleKey");
                }
            }
        }
    }
}
