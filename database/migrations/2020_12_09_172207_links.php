<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Links extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('links', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->string('short')->unique();

            $table->uuid('user_id')->nullable(false);
            $table->foreign('user_id')->references('id')->on('users'); //->onDelete('cascade');

            $table->string("long")->nullable(false);

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
        Schema::drop('links');
    }
}