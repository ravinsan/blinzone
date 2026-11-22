<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name'     => 'Super Admin',
            'email'    => 'admin@blinzone.com',
            'password' => Hash::make('12345678'),
            'text_password' => 12345678,
            'mobile' => 8750986907,
            'status' => 1,
            'role_id' => 1
        ]);
    }
}