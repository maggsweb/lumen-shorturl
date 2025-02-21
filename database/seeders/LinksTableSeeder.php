<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LinksTableSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        // Users to hard-code
        DB::table('links')->insert([
            'id'         => 1,
            'user_id'    => 1,
            'short'      => 'google',
            'long'       => 'http://www.google.co.uk',
            'created_at' => Carbon::now(),
        ]);
    }
}
