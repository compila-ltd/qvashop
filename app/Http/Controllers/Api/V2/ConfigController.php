<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Addon;
use App\Models\BusinessSetting;
use App\Models\Language;
use Illuminate\Http\Request;
use stdClass;

class ConfigController extends Controller
{
    public function addon_list()
    {
        $addons = Addon::all();

        return response()->json($addons);
    }

    public function activated_social_login()
    {
        $activated_social_login_list = BusinessSetting::whereIn('type', ['facebook_login', 'google_login', 'twitter_login'])->get();
        return response()->json($activated_social_login_list);
    }

    public function business_settings(Request $request)
    {
        $business_settings = BusinessSetting::whereIn('type', explode(',', $request->keys))->get()->toArray();
        
        // $language_object = new stdClass();
        // $language_object->id = -123123;
        // $language_object->type = 'default_lanuage';
        // $language_object->value = env('DEFAULT_LANGUAGE');
        // $language_object->lang = null;
		
		// $language_info = Language::where('code', env('DEFAULT_LANGUAGE'))->first();
		// $mobile_app = new stdClass();
		// $mobile_app->id = -12312;
        // $mobile_app->type = 'mobile_app_code';
        // $mobile_app->value = $language_info->app_lang_code;
        // $mobile_app->lang = null;
		
        // $rtl_object = new stdClass();
		// $rtl_object->id = -1231;
        // $rtl_object->type = 'rtl';
        // $rtl_object->value = $language_info->rtl;
        // $rtl_object->lang = null;
		
        // $new_array = [$language_object, $rtl_object, $mobile_app];
		
		// $settings = array_merge($business_settings, $new_array);
		
        return response()->json($business_settings);
    }
}
