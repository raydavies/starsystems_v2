<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
	protected $table = 'subjects';

	public function lessons()
	{
		return $this->hasMany('App\Models\Lesson');
	}

	public function levels()
	{
		return $this->belongsToMany('App\Models\Level', 'levels_subjects', 'subject_id', 'level_id');
	}
}
