<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = (string) env('DEFAULT_ADMIN_EMAIL', 'admin@example.com');
        $name = (string) env('DEFAULT_ADMIN_NAME', 'Administrator');
        $password = (string) env('DEFAULT_ADMIN_PASSWORD', 'ChangeMe@123');

        Admin::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                // Admin model already has 'password' => 'hashed' cast,
                // but we hash explicitly to be safe across versions.
                'password' => Hash::make($password),
            ]
        );
    }
}

