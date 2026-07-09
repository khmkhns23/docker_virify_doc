<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin user
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'ระบบผู้ดูแลระบบ',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );

        // General user
        User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'ผู้ใช้งานทั่วไป',
                'password' => bcrypt('password'),
                'role' => 'user',
            ]
        );
    }
}
