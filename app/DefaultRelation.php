<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DefaultRelation extends Model
{
	protected $fillable = ['relation_name','relation_description','created_by'];
	protected $guarded = [];
	protected $table = 'default_relation_types';
}

?>
