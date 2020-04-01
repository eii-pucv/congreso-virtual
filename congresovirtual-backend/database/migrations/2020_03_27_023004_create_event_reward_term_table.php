<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventRewardTermTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_reward_term', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('event_id')->unsigned();
            $table->bigInteger('reward_id')->unsigned()->nullable();
            $table->integer('term_id')->unsigned()->nullable();

            $table->unique(['event_id', 'reward_id', 'term_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_reward_term');
    }
}
