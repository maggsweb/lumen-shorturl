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
            'id' => 1,
            'uuid' => '6985b9e8-7db1-4e8f-b8b4-6eae56d22020',
            'status' => 'Active',
            'name' => 'Chris Maggs',
            'application' => 'Default Application',
            'created_at' => Carbon::now()
        ]);

        DB::table('users')->insert([
            'id' => 2,
            'uuid' => 'f1b41b1f-7d94-4424-a387-c96ec3a65521',
            'status' => 'Active',
            'name' => 'Temp User',
            'application' => 'Default Application',
            'created_at' => Carbon::now()
        ]);
    }
}
