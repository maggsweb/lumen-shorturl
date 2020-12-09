<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AccessLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('access_log', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->uuid('user_id')->nullable(false);
            $table->foreign('user_id')->references('id')->on('users');

            $table->uuid('url_id')->nullable(false);
            $table->foreign('url_id')->references('id')->on('urls');

            $table->dateTime('date');
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
        Schema::drop('access_log');
    }
}
