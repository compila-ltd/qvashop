<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryHistory extends Model
{
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
