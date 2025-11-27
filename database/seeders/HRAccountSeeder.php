<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class HRAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if HR account already exists
        $existingHR = DB::table('hr_accounts')->where('email', 'hr@peregrine.com')->first();
        
        if (!$existingHR) {
            DB::table('hr_accounts')->insert([
                'name' => 'HR',
                'last_name' => 'Admin',
                'email' => 'hr@peregrine.com',
                'password' => Hash::make('hr123'),
                'company_name' => 'Peregrine',
                'position' => 'HR Manager',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
