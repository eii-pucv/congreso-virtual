<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProposalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proposals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('titulo');
            $table->text('detalle');
            $table->text('autoria');                          // Autoría extrída del SIR
            $table->string('boletin')->unique();              // Número de boletín extrído del SIR
            $table->date('fecha_ingreso')->nullable();        // Fecha de ingreso extrída del SIR
            $table->integer('state')->default(0);       // 0: propuesta inactiva, 1: propuesta activa (se puede o no agregar video)
            $table->integer('type');                          // 1: petición para incluir a Congreso Virtual, 2: petición de urgencia
            $table->boolean('is_public')->default(false);
            $table->integer('petitions')->default(0);   // Cantidad de peticiones para incluir a Congreso Virtual
            $table->integer('urgencies')->default(0);   // Cantidad de peticiones de urgencia
            $table->text('argument')->nullable();
            $table->string('video_code')->nullable();
            $table->string('video_source')->nullable();
            $table->bigInteger('user_id')->unsigned();        // Usuario que propuso el proyecto de ley
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
        Schema::dropIfExists('proposals');
    }
}
