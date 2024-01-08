<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopCity extends Model
{
    use HasFactory;

    protected $table = 'shops_cities';

    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
