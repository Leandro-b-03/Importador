<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DataCustomField extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'data_custom_fields';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['data_id', 'custom_field_id', 'value'];
}
