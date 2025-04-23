<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Buat atau ambil user admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],            // ubah sesuai email yang diinginkan
            [
                'name' => 'Admin',             // ubah sesuai nama admin
                'password' => bcrypt('admin12345'),       // ubah sesuai password default

            ]
        );

        // Assign role admin
        $admin->assignRole('admin');
    }
}