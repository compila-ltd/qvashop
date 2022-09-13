<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'user_id', 'type', 'code','details','discount', 'discount_type', 'start_date', 'end_date'
    ];

    public function user(){
    	return $this->belongsTo(User::class);
    }
}
