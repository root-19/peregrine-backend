<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ManagerCOOAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if Manager account already exists
        $existingManager = DB::table('manager_coo_accounts')->where('email', 'manager@peregrine.com')->first();
        
        if (!$existingManager) {
            DB::table('manager_coo_accounts')->insert([
                'name' => 'Manager',
                'last_name' => 'Account',
                'email' => 'manager@peregrine.com',
                'password' => Hash::make('manager123'),
                'company_name' => 'Peregrine',
                'position' => 'Manager',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Check if COO account already exists
        $existingCOO = DB::table('manager_coo_accounts')->where('email', 'coo@peregrine.com')->first();
        
        if (!$existingCOO) {
            DB::table('manager_coo_accounts')->insert([
                'name' => 'COO',
                'last_name' => 'Account',
                'email' => 'coo@peregrine.com',
                'password' => Hash::make('coo123'),
                'company_name' => 'Peregrine',
                'position' => 'COO',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
