<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClubPoint extends Model
{
    public function user(){
    	return $this->belongsTo(User::class);
    }

    public function order(){
    	return $this->belongsTo(Order::class);
    }
}
