<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
	protected $fillable = ['status_name','status_description','created_by'];
	protected $guarded = [];
	protected $table = 'term_statuses';
}

?>
