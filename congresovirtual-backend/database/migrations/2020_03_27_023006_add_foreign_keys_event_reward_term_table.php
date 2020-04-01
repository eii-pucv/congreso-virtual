<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysEventRewardTermTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('event_reward_term', function (Blueprint $table) {
            $table->foreign('event_id')
                ->references('id')->on('events')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('reward_id')
                ->references('id')->on('rewards')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('term_id')
                ->references('id')->on('terms')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('event_reward_term', function (Blueprint $table) {
            //
        });
    }
}
