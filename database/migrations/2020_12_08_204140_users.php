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
            $table->uuid('uuid')->nullable(false);

            // Account
            $table->string('email')->unique()->nullable(false);
            $table->string('password', 100)->nullable(false);
            $table->enum('status', ['Active', 'InActive'])->default('Active');

            // Name and Application
            $table->string('name')->nullable(false);
            $table->string('application')->nullable(false);

            $table->softDeletes();
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
