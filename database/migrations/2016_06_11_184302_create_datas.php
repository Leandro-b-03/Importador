<?php

use Illuminate\database\Schema\Blueprint;
use Illuminate\database\Migrations\Migration;

class CreateDatas extends Migration
{
    /**
     * run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('datas', function (Blueprint $table) {            
            $table->increments('id');
            $table->integer('folder_id')->unsigned();
            $table->integer('posicao');
            $table->string('cenario', 255)->nullable();
            $table->string('grupo', 255)->nullable();
            $table->string('pre_condicao', 255)->nullable();
            $table->string('procedimentos', 255)->nullable();
            $table->string('resultado_esperado', 255)->nullable();
            $table->string('evidencias_chave', 255)->nullable();
            $table->string('data_de_execucao', 255)->nullable();
            $table->string('responsavel', 255)->nullable();
            $table->string('resultado_do_teste', 255)->nullable();
            $table->string('evidencia', 255)->nullable();
            $table->string('observacoes', 255)->nullable();
            $table->string('vendedor', 255)->nullable();
            $table->string('bin', 255)->nullable();
            $table->string('plataforma', 255)->nullable();
            $table->timestamps();

            $table->foreign('folder_id')->references('id')->on('folders')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('datas');
    }
}