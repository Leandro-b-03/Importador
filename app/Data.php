<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Data extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'datas';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['folder_id', 'posicao', 'cenario', 'grupo', 'pre_condicao', 'procedimentos', 'resultado_esperado', 'evidencias_chave', 'data_de_execucao', 'responsavel', 'resultado_do_teste', 'evidencia', 'observacoes', 'vendedor', 'bin', 'plataforma'];
}