<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('vote');
            $table->bigInteger('project_id')->unsigned()->nullable();
            $table->bigInteger('article_id')->unsigned()->nullable();
            $table->bigInteger('idea_id')->unsigned()->nullable();
            $table->bigInteger('comment_id')->unsigned()->nullable();
            $table->bigInteger('public_consultation_id')->unsigned()->nullable();
            $table->bigInteger('user_id')->unsigned();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['project_id', 'user_id']);
            $table->unique(['article_id', 'user_id']);
            $table->unique(['idea_id', 'user_id']);
            $table->unique(['comment_id', 'user_id']);
            $table->unique(['public_consultation_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('votes');
    }
}
