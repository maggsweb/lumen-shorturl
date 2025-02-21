<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityTableSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        // Users to hard-code
        DB::table('activity')->insert([
            'id'         => 1,
            'user_id'    => 1,
            'link_id'    => 1,
            'action'     => 'Create',
            'created_at' => Carbon::now(),
            'ip_address' => request()->ip(),
        ]);
    }
}
