<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $email = env('SEED_USER_EMAIL');
        $password = env('SEED_USER_PASSWORD');

        if (empty($email) || empty($password)) {
            throw new RuntimeException(
                'UsersTableSeeder requires SEED_USER_EMAIL and SEED_USER_PASSWORD to be set in your .env file.'
            );
        }

        // Default User (id 1 is referenced by the Links/Activity seeders)
        DB::table('users')->insert([
            'id'          => 1,
            'uuid'        => (string) Str::uuid(),
            'email'       => $email,
            'password'    => Hash::make($password),
            'status'      => 'Active',
            'name'        => env('SEED_USER_NAME', 'API User'),
            'application' => env('SEED_USER_APPLICATION', 'Default Application'),
            'created_at'  => Carbon::now(),
        ]);
    }
}
