<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin User
        User::updateOrCreate(
            ['email' => 'admin@local.test'],
            [
                'name' => 'Admin', 
                'password' => Hash::make('123456'),
                'role' => 'admin',
                'is_active' => true
            ]
        );

        // Editor User
        User::updateOrCreate(
            ['email' => 'editor@local.test'],
            [
                'name' => 'Editor', 
                'password' => Hash::make('123456'),
                'role' => 'editor',
                'is_active' => true
            ]
        );
    }
}
