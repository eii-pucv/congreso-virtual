<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysActionEventTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('action_event', function (Blueprint $table) {
            $table->foreign('action_id')
                ->references('id')->on('actions')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('event_id')
                ->references('id')->on('events')
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
        Schema::table('action_event', function (Blueprint $table) {
            //
        });
    }
}
