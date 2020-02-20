<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationOrgsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('location_orgs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('es_principal')->default(false);
            $table->string('direccion');
            $table->string('pais');
            $table->string('region')->nullable();
            $table->string('comuna')->nullable();
            $table->integer('sector');                  // 0: ciudad | 1: area rural
            $table->bigInteger('user_id')->unsigned();
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
        Schema::dropIfExists('location_orgs');
    }
}
