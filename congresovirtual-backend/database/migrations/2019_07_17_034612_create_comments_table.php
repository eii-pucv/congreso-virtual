<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('body');
            $table->integer('votos_a_favor')->default(0);
            $table->integer('votos_en_contra')->default(0);
            $table->float('perception')->nullable();
            $table->integer('state')->default(0);                     // 0: comentario publico, 1: comentario (con archivo) en espera de moderacion, 2: comentario bloqueado por denuncia, 3: comentario rechazado por moderacion
            $table->bigInteger('parent_id')->unsigned()->nullable();        // Reference to parent comment
            $table->bigInteger('project_id')->unsigned()->nullable();
            $table->bigInteger('article_id')->unsigned()->nullable();
            $table->bigInteger('idea_id')->unsigned()->nullable();
            $table->bigInteger('public_consultation_id')->unsigned()->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
}
