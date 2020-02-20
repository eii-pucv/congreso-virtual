<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('titulo');
            $table->string('postulante')->nullable();
            $table->string('estado')->nullable();
            $table->integer('etapa')->nullable();
            $table->longText('detalle')->nullable();
            $table->mediumText('resumen')->nullable();
            $table->dateTime('fecha_inicio')->nullable();
            $table->dateTime('fecha_termino')->nullable();
            $table->string('boletin');
            $table->boolean('is_principal')->default(false);
            $table->boolean('is_public')->default(false);
            $table->boolean('is_enabled')->default(true);
            $table->integer('votos_a_favor')->default(0);
            $table->integer('votos_en_contra')->default(0);
            $table->integer('abstencion')->default(0);
            $table->string('video')->nullable();
            $table->bigInteger('imagen_id')->unsigned()->nullable();
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
        Schema::dropIfExists('projects');
    }
}
