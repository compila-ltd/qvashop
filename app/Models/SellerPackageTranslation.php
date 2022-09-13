<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerPackageTranslation extends Model
{
    protected $fillable = ['name', 'lang', 'seller_package_id'];

    public function seller_package(){
      return $this->belongsTo(SellerPackage::class);
    }
}
