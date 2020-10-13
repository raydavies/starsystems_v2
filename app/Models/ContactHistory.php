<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ContactHistory extends Model
{
    public $timestamps = false;
    
    protected $table = 'customer_history';

    protected $dates = ['occurred_at'];
    
    protected $fillable = [
        'action',
        'customer_id',
        'details',
        'user_id',
    ];

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }
    
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function getOccurredAtAttribute()
    {
        $date = $this->attributes['occurred_at'];

        return $this->asDateTime($date)->timezone('America/Chicago');
    }
}