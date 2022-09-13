<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
    public function attribute() {
        return $this->belongsTo(Attribute::class);
    }
}
