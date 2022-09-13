<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App;

class CustomerProduct extends Model
{
    protected $with = ['customer_product_translations'];

    public function getTranslation($field = '', $lang = false){
      $lang = $lang == false ? App::getLocale() : $lang;
      $customer_product_translations = $this->customer_product_translations->where('lang', $lang)->first();
      return $customer_product_translations != null ? $customer_product_translations->$field : $this->$field;
    }

    public function category(){
    	return $this->belongsTo(Category::class);
    }

    public function subcategory(){
    	return $this->belongsTo(SubCategory::class);
    }

    public function subsubcategory(){
    	return $this->belongsTo(SubSubCategory::class);
    }

    public function brand(){
    	return $this->belongsTo(Brand::class);
    }

    public function user(){
    	return $this->belongsTo(User::class);
    }

    public function state(){
    	return $this->belongsTo(State::class);
    }

    public function city(){
    	return $this->belongsTo(City::class);
    }

    public function customer_product_translations(){
      return $this->hasMany(CustomerProductTranslation::class);
    }
}
