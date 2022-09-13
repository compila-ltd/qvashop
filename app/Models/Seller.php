<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{

  protected $with = ['user', 'user.shop'];

  public function user(){
  	return $this->belongsTo(User::class);
  }

  public function payments(){
  	return $this->hasMany(Payment::class);
  }

  public function seller_package(){
    return $this->belongsTo(SellerPackage::class);
}
}
