<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopState extends Model
{
    use HasFactory;

    protected $table = 'shops_states';

    public function cities(){
        return $this->hasMany(City::class);
    }
}
