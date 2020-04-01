<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRewardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rewards', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->integer('points')->unsigned()->default(0);
            $table->integer('actions_needed')->unsigned()->default(0);
            $table->string('icon')->nullable();
            $table->bigInteger('action_id')->unsigned();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['actions_needed', 'action_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rewards');
    }
}
