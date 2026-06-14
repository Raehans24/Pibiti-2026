<?php

namespace Database\Seeders;

use App\Models\Note;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Generate 50 users
        $users = User::factory(50)->create();

        // Generate 100 notes assigned to random users
        Note::factory(100)->recycle($users)->create();
    }
}
