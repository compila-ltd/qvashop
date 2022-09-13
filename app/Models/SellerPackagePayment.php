<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerPackagePayment extends Model
{
    public function user(){
    	return $this->belongsTo(User::class);
    }

    public function seller_package(){
    	return $this->belongsTo(SellerPackage::class);
    }
}
