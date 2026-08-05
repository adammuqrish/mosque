<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Account for ADMIN
        User::create([
            'name' => 'Admin Masjid',
            'email' => 'admin@mosque.com',
            'password' => Hash::make('password'), // Password: password
            'role' => 'admin',
            'phone' => '0123456789',
        ]);

        // 2. Account for BENDAHARI (Treasurer)
        User::create([
            'name' => 'Bendahari Masjid',
            'email' => 'treasurer@mosque.com',
            'password' => Hash::make('password'), // Password: password
            'role' => 'treasurer',
            'phone' => '0198765432',
        ]);

        // 3. Account for JEMAAH (Member)
        User::create([
            'name' => 'Ali Bin Abu',
            'email' => 'ali@mosque.com',
            'password' => Hash::make('password'), // Password: password
            'role' => 'member',
            'phone' => '0101112222',
        ]);
    }
}
