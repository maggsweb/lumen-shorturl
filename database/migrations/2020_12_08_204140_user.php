<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class User extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {

            // Auto incremented ID
            $table->increments('id');

            // UUID, to add to Header Requests, Unique, Indexed, Required..
            $table->uuid('token')->unique()->nullable(false);

            // User Account Status
            $table->enum('status', ['Active', 'InActive'])->default('Active');

            // Name and Application
            $table->string("name")->nullable(false);
            $table->string("application")->nullable(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
