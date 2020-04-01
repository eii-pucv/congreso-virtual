<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActionEventTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('action_event', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('quantity')->unsigned()->default(1);
            $table->bigInteger('action_id')->unsigned();
            $table->bigInteger('event_id')->unsigned();

            $table->unique(['action_id', 'event_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('action_event');
    }
}
