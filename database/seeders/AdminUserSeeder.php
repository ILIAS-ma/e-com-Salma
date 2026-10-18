<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@atlas.ma'],
            [
                'name' => 'Admin Atlas',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '+212600000000',
                'email_verified_at' => now(),
            ]
        );
    }
}
