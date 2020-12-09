<?php

namespace Database\Seeders;

use Carbon\Carbon;
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
            'uuid' => '6985b9e8-7db1-4e8f-b8b4-6eae56d22020',
            'status' => 'Active',
            'name' => 'Chris Maggs',
            'application' => 'Test Application',
            'created_at' => Carbon::now()
        ]);
    }
}
