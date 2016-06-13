<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vocabulary extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'vocabularies';

	/**
     * Get the comments for the blog post.
     */
    public function terms()
    {
        return $this->hasMany('App\Term', 'vocabulary_id');
    }
}
