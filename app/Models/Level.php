<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
	protected $table = 'levels';

	public function lessons()
	{
		return $this->hasMany('App\Models\Lesson');
	}

	public function subjects()
	{
		return $this->belongsToMany('App\Models\Subject', 'levels_subjects', 'level_id', 'subject_id');
	}
}
