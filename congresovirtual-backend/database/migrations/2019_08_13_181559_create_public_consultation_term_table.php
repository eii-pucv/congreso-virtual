<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePublicConsultationTermTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('public_consultation_term', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('public_consultation_id')->unsigned();
            $table->integer('term_id')->unsigned();

            $table->unique(['public_consultation_id', 'term_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('public_consultation_term');
    }
}
