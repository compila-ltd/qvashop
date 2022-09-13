<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;

class LanguageCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function($data) {           

                return [
                    'id'   =>(int) $data->id,
                    'name' => translate($data->name),        
                    'code' => $data->code,   
                    'mobile_app_code' => $data->app_lang_code, 
                    'rtl' => $data->rtl == 1,  
                    'is_default' => env("DEFAULT_LANGUAGE",'en') == $data->code,  
                    'image' => static_asset('assets/img/flags/'.$data->code.'.png') ,  
                                     
                ];
            })
        ];
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200
        ];
    }
}
