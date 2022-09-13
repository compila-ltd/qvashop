<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuctionProductBid extends Model
{
    public function product(){
    	return $this->belongsTo(Product::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
