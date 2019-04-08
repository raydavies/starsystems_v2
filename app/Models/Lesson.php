<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
	protected $table = 'lessons';

	public function subject()
	{
		return $this->belongsTo('App\Models\Subject');
	}

	public function level()
	{
		return $this->belongsTo('App\Models\Level');
	}
}
