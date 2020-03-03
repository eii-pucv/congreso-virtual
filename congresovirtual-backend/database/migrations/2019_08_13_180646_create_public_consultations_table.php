<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePublicConsultationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('public_consultations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('titulo');
            $table->string('autor')->nullable();
            $table->integer('estado')->default(0);       // 0: consulta publica inactiva, 1: consulta publica activa
            $table->longText('detalle');
            $table->mediumText('resumen');
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_termino');
            $table->integer('votos_a_favor')->default(0);
            $table->integer('votos_en_contra')->default(0);
            $table->string('icono')->nullable();
            $table->string('video_code')->nullable();
            $table->string('video_source')->nullable();
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
        Schema::dropIfExists('public_consultations');
    }
}
