<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'username' => 'utilisateur1',
            'country_code' => '+1',
            'phone' => '1234567890',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        // Créez d'autres utilisateurs
        User::create([
            'username' => 'utilisateur2',
            'country_code' => '+1',
            'phone' => '9876543210',
            'email' => 'another@example.com',
            'password' => Hash::make('password'),
        ]);

        // Ajoutez un autre utilisateur
        User::create([
            'username' => 'utilisateur3',
            'country_code' => '+1',
            'phone' => '5555555555',
            'email' => 'third@example.com',
            'password' => Hash::make('password'),
        ]);

        // Ajoutez un autre utilisateur
        User::create([
            'username' => 'utilisateur4',
            'country_code' => '+1',
            'phone' => '5555555556',
            'email' => 'four@example.com',
            'password' => Hash::make('password'),
        ]);
        // Ajoutez un autre utilisateur
        User::create([
            'username' => 'utilisateur5',
            'country_code' => '+1',
            'phone' => '5555555557',
            'email' => 'five@example.com',
            'password' => Hash::make('password'),
        ]);
        // Ajoutez un autre utilisateur
        User::create([
            'username' => 'utilisateur6',
            'country_code' => '+1',
            'phone' => '5555555559',
            'email' => 'six@example.com',
            'password' => Hash::make('password'),
        ]);
        // Ajoutez un autre utilisateur
        User::create([
            'username' => 'utilisateur7',
            'country_code' => '+1',
            'phone' => '555555558',
            'email' => 'seven@example.com',
            'password' => Hash::make('password'),
        ]);
        // Ajoutez un autre utilisateur
        User::create([
            'username' => 'utilisateur8',
            'country_code' => '+1',
            'phone' => '5555555534',
            'email' => 'eight@example.com',
            'password' => Hash::make('password'),
        ]);
        // Ajoutez un autre utilisateur
        User::create([
            'username' => 'utilisateur9',
            'country_code' => '+1',
            'phone' => '5555555512',
            'email' => 'nine@example.com',
            'password' => Hash::make('password'),
        ]);
        // Ajoutez un autre utilisateur
        User::create([
            'username' => 'utilisateur4',
            'country_code' => '+1',
            'phone' => '5555555502',
            'email' => 'ten@example.com',
            'password' => Hash::make('password'),
        ]);

    }
}
