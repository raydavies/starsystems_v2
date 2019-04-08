<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Testimonial extends Model
{
    use SoftDeletes;

	protected $table = 'testimonials';

	protected $dates = ['created_at', 'deleted_at', 'updated_at'];

	protected $fillable = [
		'name',
		'city',
		'state_province',
		'comment',
	];

	public function getCreatedAtAttribute()
    {
        $value = $this->attributes['created_at'];

        return $this->setLocalTimezone($value);
    }

    public function getDeletedAtAttribute()
    {
        $value = $this->attributes['deleted_at'];

        return $this->setLocalTimezone($value);
    }

    public function getUpdatedAtAttribute()
    {
        $value = $this->attributes['updated_at'];

        return $this->setLocalTimezone($value);
    }

    /**
     * @TODO: figure out how to prevent 0000-00-00 00:00:00 from breaking json
     * @param mixed $date
     * @return null|Carbon
     */
    protected function setLocalTimezone($date)
    {
        if (is_null($date)) {
            return $date;
        }
        return $this->asDateTime($date)->timezone('America/Chicago');
    }
}
