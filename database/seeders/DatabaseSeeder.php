<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\AppSetting;
use App\Models\City;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        City::factory()->createMany([
            ['city_name' => 'Mumbai', 'status' => 'active'],
            ['city_name' => 'Delhi', 'status' => 'active'],
            ['city_name' => 'Bengaluru', 'status' => 'active'],
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Admin::query()->firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
            ]
        );

        AppSetting::current();
    }
}
