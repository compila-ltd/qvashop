<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\BrandCollection;
use App\Http\Resources\V2\CategoryCollection;
use App\Models\Brand;
use App\Models\Category;
use Cache;

class FilterController extends Controller
{
    public function categories()
    {
        //if you want to show base categories
        return Cache::remember('app.filter_categories', 86400, function () {
            return new CategoryCollection(Category::where('parent_id', 0)->get());
        });

        //if you want to show featured categories
        //return new CategoryCollection(Category::where('featured', 1)->get());
    }

    public function brands()
    {
        //show only top 20 brands
        return Cache::remember('app.filter_brands', 86400, function () {
            return new BrandCollection(Brand::where('top', 1)->limit(20)->get());
        });
    }


}
