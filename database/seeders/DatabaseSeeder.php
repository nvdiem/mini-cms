<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run settings first (site configuration)
        $this->call(SettingsSeeder::class);

        // Run demo content (Users, Pages, Posts, etc.)
        $this->call(DemoContentSeeder::class);
    }
}
