<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Users extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');

            // UUID, to add to Header Requests, Unique, Indexed, Required..
            $table->uuid('uuid')->unique()->nullable(false);

            // User Account Status
            $table->enum('status', ['Active', 'InActive'])->default('Active');

            // Name and Application
            $table->string('name')->nullable(false);
            $table->string('application')->nullable(false);

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
