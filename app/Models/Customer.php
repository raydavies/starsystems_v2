<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

	protected $table = 'customers';

	protected $dates = ['created_at', 'deleted_at', 'updated_at'];

	protected $fillable = [
        'name',
        'street_address',
		'city',
        'state_province',
        'zip_code',
        'email',
        'phone_home',
        'phone_work',
        'child_name',
        'grade',
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

    /**
     * @param string $value
     * @return void
     */
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower($value);
    }

    /**
     * Strip non-numeric characters from the home phone.
     *
     * @param  string  $value
     * @return void
     */
    public function setPhoneHomeAttribute($value)
    {
        $this->attributes['phone_home'] = preg_replace('/[^\d]+/', '', $value);
    }

    /**
     * Strip non-numeric characters from the work phone.
     *
     * @param  string  $value
     * @return void
     */
    public function setPhoneWorkAttribute($value)
    {
        $this->attributes['phone_work'] = preg_replace('/[^\d]+/', '', $value);
    }

    /**
     * Ensures the grade level is an integer or null
     *
     * @param mixed $value
     */
    public function setGradeAttribute($value)
    {
        $this->attributes['grade'] = is_numeric($value) ? (int) $value : null;
    }

    /**
     * Returns the full Grade model so we can access the name
     */
    public function fullGrade()
    {
        return $this->hasOne('App\Models\Grade', 'level', 'grade');
    }

    public function contactHistory()
    {
        return $this->hasMany('App\Models\ContactHistory')->orderBy('occurred_at', 'asc');
    }
}
