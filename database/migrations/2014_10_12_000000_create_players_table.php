<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlayersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('role')->default('0')->unsigned();
            $table->string('name', 30)->unique();
            $table->integer('hp')->unsigned();
            $table->integer('hp_max')->unsigned();
            $table->integer('x');
            $table->integer('y');
            $table->integer('z');
            $table->string('token', 64)->nullable();
            $table->string('ip', 64)->nullable();
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
        Schema::dropIfExists('players');
    }
}
