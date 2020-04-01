<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->foreign('project_id')
                ->references('id')->on('projects')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('page_id')
                ->references('id')->on('pages')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('player_id')
                ->references('user_id')->on('players')
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
        Schema::table('events', function (Blueprint $table) {
            //
        });
    }
}
