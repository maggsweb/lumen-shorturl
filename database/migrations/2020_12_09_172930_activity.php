<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Activity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->uuid('user_id')->nullable(true);
            $table->foreign('user_id')->references('id')->on('users');

            $table->uuid('url_id')->nullable(false);
            $table->foreign('url_id')->references('id')->on('urls');

            $table->enum('action', ['Create', 'List_Url', 'List_User', 'Redirect']);

            $table->dateTime('created_at');
            $table->ipAddress('ip_address');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('activity');
    }
}
