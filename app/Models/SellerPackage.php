<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App;

class SellerPackage extends Model
{
    protected $guarded = [];

    public function getTranslation($field = '', $lang = false){
        $lang = $lang == false ? App::getLocale() : $lang;
        $seller_package_translation = $this->hasMany(SellerPackageTranslation::class)->where('lang', $lang)->first();
        return $seller_package_translation != null ? $seller_package_translation->$field : $this->$field;
    }

    public function seller_package_translations(){
      return $this->hasMany(SellerPackageTranslation::class);
    }

    public function seller_package_payments()
    {
        return $this->hasMany(SelllerPackagePayment::class);
    }

    public function shop()
    {
        return $this->hasOne(Shop::class);
    }

}
