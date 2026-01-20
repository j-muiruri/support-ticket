<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admins = [
            [
                'name' => 'Super Admin',
                'email' => 'admin@example.com',
                'mobile' => '254700000001',
                'password' => Hash::make('Admin@123'),
                'role' => 'ADMIN',
            ],
            [
                'name' => 'John Admin',
                'email' => 'john.admin@example.com',
                'mobile' => '254700000002',
                'password' => Hash::make('Admin@123'),
                'role' => 'ADMIN',
            ],
            [
                'name' => 'Jane Admin',
                'email' => 'jane.admin@example.com',
                'mobile' => '254700000003',
                'password' => Hash::make('Admin@123'),
                'role' => 'ADMIN',
            ],
        ];

        foreach ($admins as $admin) {
            User::updateOrCreate(
                ['email' => $admin['email']], // Check if email exists
                $admin // Create or update with these values
            );
        }

        $this->command->info('3 admin users created successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('Email: admin@example.com | Password: Admin@123');
        $this->command->info('Email: john.admin@example.com | Password: Admin@123');
        $this->command->info('Email: jane.admin@example.com | Password: Admin@123');
    }
}