<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'terms';

	/**
     * The vocabulary that belong to the role.
     */
    public function vocabulary()
    {
        return $this->belongsTo('App\Vocabulary');
    }
}