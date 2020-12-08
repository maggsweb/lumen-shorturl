<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        // Users to hard-code
        DB::table('users')->insert([
            'token' => '6985b9e8-7db1-4e8f-b8b4-6eae56d22020',
            'name' => 'Chris Maggs',
            'application' => 'Test Application'
        ]);
    }
}
