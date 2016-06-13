<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDataCustomFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_custom_fields', function (Blueprint $table) {
            $table->integer('data_id')->unsigned();
            $table->integer('custom_field_id')->unsigned();
            $table->string('value', 255)->nullable();
            $table->timestamps();

            $table->foreign('data_id')->references('id')->on('datas')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('custom_field_id')->references('id')->on('custom_fields')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('data_custom_fields');
    }
}
