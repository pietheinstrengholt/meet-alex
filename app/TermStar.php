<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TermStar extends Model
{
	protected $fillable = ['term_id','rating','created_by'];
	protected $guarded = [];
	protected $table = 'term_stars';

	public function term()
	{
		return $this->belongsTo('App\Term');
	}
}

?>
