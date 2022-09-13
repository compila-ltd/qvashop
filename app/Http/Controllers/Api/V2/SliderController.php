<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\SliderCollection;
use Cache;

class SliderController extends Controller
{
    public function sliders()
    {
        return Cache::remember('app.home_slider_images', 86400, function(){
            return new SliderCollection(json_decode(get_setting('home_slider_images'), true));
        });
    }

    public function bannerOne()
    {
        return Cache::remember('app.home_banner1_images', 86400, function(){
            return new SliderCollection(json_decode(get_setting('home_banner1_images'), true));
        });
    }

    public function bannerTwo()
    {
        return Cache::remember('app.home_banner2_images', 86400, function(){
            return new SliderCollection(json_decode(get_setting('home_banner2_images'), true));
        });
    }

    public function bannerThree()
    {
        return Cache::remember('app.home_banner3_images', 86400, function(){
            return new SliderCollection(json_decode(get_setting('home_banner3_images'), true));
        });
    }
}
