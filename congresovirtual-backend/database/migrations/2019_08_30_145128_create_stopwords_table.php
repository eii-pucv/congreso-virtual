<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStopwordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stopwords', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('value');
            $table->integer('stopword_type_id')->unsigned();
            $table->bigInteger('object_id')->unsigned()->nullable();
            $table->timestamps();

            $table->unique(['value', 'stopword_type_id', 'object_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stopwords');
    }
}
