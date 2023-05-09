<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //Buat Admin Baru
        activity()->disableLogging();
        if(User::query()->count() === 0) User::create([
            'name' => 'Muhammad Isnu Nasrudin',
            'phone' => '6282228403855',
            'gender' => 'L',
            'jabatan' => 'Anggota Kesekretariatan',
            'permission' => ['*'],
            'email' => 'isnunas@gmail.com',
            'password' => bcrypt('lalisandine')
        ]);
        activity()->enableLogging();
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
