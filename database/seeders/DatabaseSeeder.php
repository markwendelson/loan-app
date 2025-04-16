<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->firstOrCreate([
        //     'name' => 'Test User',
        //     'username' => 'test@example.com',
        //     'email' => 'test@example.com',
        //     'password' => bcrypt('password')
        // ]);

        // Role seeder
        $super_adminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Permission seeder

        // User role seeder
        $super_admin = User::firstOrCreate([
            'name' => 'I am Super Admin',
            'username' => 'super@example.com',
            'email' => 'super@example.com',
            'password' => bcrypt('password')
        ]);

        $super_admin->assignRole($adminRole);


        $admin = User::firstOrCreate([
            'name' => 'I am Admin',
            'username' => 'admin@example.com',
            'email' => 'admin@example.com',
            'password' => bcrypt('password')
        ]);

        $admin->assignRole($adminRole);

        $user = User::firstOrCreate([
            'name' => 'I am Admin',
            'username' => 'user@example.com',
            'email' => 'user@example.com',
            'password' => bcrypt('password')
        ]);

        $user->assignRole($userRole);

        // User permission seeder

    }
}
