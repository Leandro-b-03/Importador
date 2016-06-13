<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ColumnNew extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'columns_new';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['data_id', 'folder_id', 'posicao', 'column', 'value'];
}
