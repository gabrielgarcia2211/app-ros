<?php

namespace Database\Seeders\Users;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@rol.com',
            'password' => bcrypt('password'),
        ]);
        $adminRole = Role::where('name', 'admin')->first();
        $adminUser->assignRole($adminRole);
    }
}
