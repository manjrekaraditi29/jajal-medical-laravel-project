<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@jajalmedical.com'],
            [
                'name' => 'Dr. Sarah Al-Mansoor (Admin)',
                'password' => Hash::make('Admin@12345'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'viewer@jajalmedical.com'],
            [
                'name' => 'James Sterling (Clinical Viewer)',
                'password' => Hash::make('Viewer@12345'),
                'role' => 'viewer',
            ]
        );
    }
}