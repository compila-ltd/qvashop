<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{

  protected $with = ['user'];

  public function user()
  {
    return $this->belongsTo(User::class);
  }
  
  public function seller_package(){
      return $this->belongsTo(SellerPackage::class);
  }
}
