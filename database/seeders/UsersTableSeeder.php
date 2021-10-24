<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        // Default User
        DB::table('users')->insert([
            'id'          => 1,
            'uuid'        => '6985b9e8-7db1-4e8f-b8b4-6eae56d22020',
            'email'       => 'lumen.api@maggsweb.co.uk',
            'password'    => Hash::make('password'),
            'status'      => 'Active',
            'name'        => 'Chris Maggs',
            'application' => 'Default Application',
            'created_at'  => Carbon::now(),
        ]);

        User::factory()->times(2)->create();
    }
}
