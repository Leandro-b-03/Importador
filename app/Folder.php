<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'folders';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['file_id', 'name'];
}
