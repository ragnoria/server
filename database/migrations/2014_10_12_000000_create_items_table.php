<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('type');
            $table->string('name');
            $table->tinyInteger('size')->default('1')->comment('1 - 2');
            $table->tinyInteger('altitude')->default('0');
            $table->tinyInteger('is_animating')->default('0');
            $table->tinyInteger('is_blocking_creatures')->default('0');
            $table->tinyInteger('is_blocking_projectiles')->default('0');
            $table->tinyInteger('is_blocking_items')->default('0');
            $table->tinyInteger('is_moveable')->default('0');
            $table->tinyInteger('is_pickupable')->default('0');
            $table->tinyInteger('is_stackable')->default('0');
            $table->tinyInteger('is_always_top')->default('0');
            $table->tinyInteger('light_radius')->default('0');
            $table->tinyInteger('light_level')->default('0')->comment('0 - 5');
            $table->string('light_color', 7);
            $table->tinyInteger('padding_x')->default('0');
            $table->tinyInteger('padding_y')->default('0');
            $table->text('sprites');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}
