<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTermUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('term_user', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('term_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();

            $table->unique(['term_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('term_user');
    }
}
