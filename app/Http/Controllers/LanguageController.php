<?php

namespace App\Http\Controllers;

use App\Models\AppTranslation;
use Illuminate\Http\Request;
use App\Models\Language;
use App\Models\Translation;
use Cache;
use Storage;
use Session;

class LanguageController extends Controller
{
    public function __construct() {
        // Staff Permission Check
        $this->middleware(['permission:language_setup'])->only('index','create','edit','destroy');
    }

    public function changeLanguage(Request $request)
    {
    	$request->session()->put('locale', $request->locale);
        $language = Language::where('code', $request->locale)->first();
    	flash(translate('Language changed to ').$language->name)->success();
    }

    public function index(Request $request)
    {
        $languages = Language::paginate(10);
        return view('backend.setup_configurations.languages.index', compact('languages'));
    }

    public function create(Request $request)
    {
        return view('backend.setup_configurations.languages.create');
    }

    public function store(Request $request)
    {
        if(Language::where('code',$request->code)->first()){
            flash(translate('This code is already used for another language'))->error();
            return back();
        }

        $language = new Language;
        $language->name = $request->name;
        $language->code = $request->code;
        $language->app_lang_code = $request->app_lang_code;
        $language->save();   

        Cache::forget('app.languages');

        flash(translate('Language has been inserted successfully'))->success();
        return redirect()->route('languages.index');
    }

    public function show(Request $request, $id)
    {
        $sort_search = null;
        $language = Language::findOrFail($id);
        $lang_keys = Translation::where('lang', 'en');
        
        if ($request->has('search')){
            $sort_search = $request->search;
            $lang_keys = $lang_keys->where('lang_key', 'like', '%'.preg_replace('/[^A-Za-z0-9\_]/', '', str_replace(' ', '_', strtolower($sort_search))).'%');
        }
        $lang_keys = $lang_keys->paginate(50);
        
        return view('backend.setup_configurations.languages.language_view', compact('language','lang_keys','sort_search'));
    }

    public function edit($id)
    {
        $language = Language::findOrFail($id);
        return view('backend.setup_configurations.languages.edit', compact('language'));
    }

    public function update(Request $request, $id)
    {
        if(Language::where('code', $request->code)->where('id', '!=', $id)->first()){
            flash(translate('This code is already used for another language'))->error();
            return back();
        }
        $language = Language::findOrFail($id);
        if (env('DEFAULT_LANGUAGE') == $language->code && env('DEFAULT_LANGUAGE') != $request->code) {
            flash(translate('Default language code can not be edited'))->error();
            return back();
        } elseif ($language->code == 'en' && $request->code != 'en') {
            flash(translate('English language code can not be edited'))->error();
            return back();
        }
        
        $language->name = $request->name;
        $language->code = $request->code;
        $language->app_lang_code = $request->app_lang_code; 
        $language->save();
        
        Cache::forget('app.languages');

        flash(translate('Language has been updated successfully'))->success();
        return redirect()->route('languages.index');
    }

    public function key_value_store(Request $request)
    {
        $language = Language::findOrFail($request->id);
        foreach ($request->values as $key => $value) {
            $translation_def = Translation::where('lang_key', $key)->where('lang', $language->code)->latest()->first();
            if($translation_def == null){
                $translation_def = new Translation;
                $translation_def->lang = $language->code;
                $translation_def->lang_key = $key;
                $translation_def->lang_value = $value;
                $translation_def->save();
            }
            else {
                $translation_def->lang_value = $value;
                $translation_def->save();
            }
        }
        Cache::forget('translations-'.$language->code);
        flash(translate('Translations updated for ').$language->name)->success();
        return back();
    }

    public function update_status(Request $request)
    {
        $language = Language::findOrFail($request->id);
        if($language->code == env('DEFAULT_LANGUAGE') && $request->status == 0) {
            flash(translate('Default language can not be inactive'))->error();
            return 1;
        }
        $language->status = $request->status;
        if($language->save()){
            flash(translate('Status updated successfully'))->success();
            return 1;
        }
        return 0;
    }

    public function update_rtl_status(Request $request)
    {
        $language = Language::findOrFail($request->id);
        $language->rtl = $request->status;
        if($language->save()){
            flash(translate('RTL status updated successfully'))->success();
            return 1;
        }
        return 0;
    }

    public function destroy($id)
    {
        $language = Language::findOrFail($id);
        if (env('DEFAULT_LANGUAGE') == $language->code) {
            flash(translate('Default language can not be deleted'))->error();
        } elseif($language->code == 'en') {
            flash(translate('English language can not be deleted'))->error();
        }
        else {
            if($language->code == Session::get('locale')){
                Session::put('locale', env('DEFAULT_LANGUAGE'));
            }
            Language::destroy($id);
            flash(translate('Language has been deleted successfully'))->success();
        }
        return redirect()->route('languages.index');
    }


    //App-Translation
    public function importEnglishFile(Request $request){
        $path = Storage::disk('local')->put('app-translations', $request->lang_file);

        $contents = file_get_contents(public_path($path));
        
        try {
            foreach(json_decode($contents) as $key => $value){
                AppTranslation::updateOrCreate(
                    ['lang' => 'en', 'lang_key' => $key],
                    ['lang_value' => $value]
                );
            }
        } catch (\Throwable $th) {
            //throw $th;
        }

        flash(translate('Translation keys has been imported successfully. Go to App Translation for more..'))->success();
        return back();
    }

    public function showAppTranlsationView(Request $request, $id)
    {
        $sort_search = null;
        $language = Language::findOrFail($id);
        $lang_keys = AppTranslation::where('lang', 'en');
        if ($request->has('search')){
            $sort_search = $request->search;
            $lang_keys = $lang_keys->where('lang_key', 'like', '%'.$sort_search.'%');
        }
        $lang_keys = $lang_keys->paginate(50);
        return view('backend.setup_configurations.languages.app_translation', compact('language','lang_keys','sort_search'));
    }

    public function storeAppTranlsation(Request $request){
        $language = Language::findOrFail($request->id);
        foreach ($request->values as $key => $value) {
            AppTranslation::updateOrCreate(
                ['lang' => $language->app_lang_code, 'lang_key' => $key],
                ['lang_value' => $value]
            );
        }
        flash(translate('App Translations updated for ').$language->name)->success();
        return back();
    }

    public function exportARBFile($id){
        $language = Language::findOrFail($id);
        try {
            // Write into the json file
            $filename = "app_{$language->app_lang_code}.arb";
            $contents = AppTranslation::where('lang', $language->app_lang_code)->pluck('lang_value', 'lang_key')->toJson();
            
            return response()->streamDownload(function () use ($contents) {
                echo $contents;
            }, $filename);
        } catch (\Exception $e) {
            dd($e);
        }
    }
}
