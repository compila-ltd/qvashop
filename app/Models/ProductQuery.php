<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductQuery extends Model
{
    use HasFactory;

    public function product(){
        return  $this->belongsTo(Product::class);
      }
  
      public function user(){
         return $this->belongsTo(User::class,'customer_id');
      }
}
