<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
	protected $fillable = ['term_id','comment'];
	protected $guarded = [];
	protected $table = 'term_comments';

	public function term()
	{
		return $this->belongsTo('App\Term','term_id','id');
	}

	public function reviewer()
	{
		return $this->belongsTo('App\User','created_by','id')->withTrashed();
	}
}

?>
