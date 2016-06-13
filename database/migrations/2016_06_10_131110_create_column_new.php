<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateColumnNew extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('columns_new', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('data_id');
            $table->integer('folder_id');
            $table->integer('posicao');
            $table->string('column');
            $table->string('value')->nullable();;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('columns_new');
    }
}
