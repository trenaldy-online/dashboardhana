<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Membuat 1 akun khusus admin
        User::create([
            'name' => 'Admin AHCC',
            'email' => 'digitalmarketing.ahcc@gmail.com',
            'password' => Hash::make('admin123456789'),
        ]);
    }
}