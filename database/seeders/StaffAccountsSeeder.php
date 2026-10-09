<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StaffAccountsSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['manager' => 'Manager@12345', 'staff' => 'Staff@12345'] as $role => $password) {
            User::updateOrCreate(
                ['email' => $role.'@workshop.com'],
                [
                    'name' => 'Demo '.ucfirst($role),
                    'role' => $role,
                    'password' => Hash::make($password),
                ]
            );
        }
    }
}
