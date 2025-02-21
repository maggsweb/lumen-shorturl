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

            $table->mediumInteger('user_id')->unsigned()->nullable(true)->index();
            $table->foreign('user_id')->references('id')->on('users');

            $table->mediumInteger('link_id')->unsigned()->nullable(true)->index();
            $table->foreign('link_id')->references('id')->on('links');

            $table->enum('action', ['Create', 'Redirect', 'Error']);
            $table->text('details')->nullable(true);

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
