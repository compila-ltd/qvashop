<?php

namespace App\Http\Controllers\Seller;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Category;
use App\Models\ProductTax;
use App\Models\ProductTranslation;
use App\Models\Upload;
use App\Services\ProductTaxService;
use Auth;

class DigitalProductController  extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $products = Product::where('user_id', Auth::user()->id)->where('digital', 1)->orderBy('created_at', 'desc')->paginate(10);
        return view('seller.product.digitalproducts.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(addon_is_activated('seller_subscription')){
            if(seller_package_validity_check()){
                $categories = Category::where('digital', 1)->get();
                return view('seller.product.digitalproducts.create', compact('categories'));
            }
            else {
                flash(translate('Please upgrade your package.'))->warning();
                return back();
            }
        }
        $categories = Category::where('digital', 1)->get();
        return view('seller.product.digitalproducts.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(addon_is_activated('seller_subscription')){
            if(!seller_package_validity_check()){
                flash(translate('Please upgrade your package.'))->warning();
                return redirect()->route('seller.digitalproducts');
            }
        }

        $product                    = new Product;
        $product->name              = $request->name;
        $product->added_by          = $request->added_by;
        $product->user_id           = Auth::user()->id;
        $product->category_id       = $request->category_id;
        $product->digital           = 1;
        $product->photos            = $request->photos;
        $product->thumbnail_img     = $request->thumbnail_img;

        $tags = array();
        if($request->tags[0] != null){
            foreach (json_decode($request->tags[0]) as $key => $tag) {
                array_push($tags, $tag->value);
            }
        }
        $product->tags = implode(',', $tags);

        $product->description       = $request->description;
        $product->unit_price        = $request->unit_price;
        $product->purchase_price    = $request->purchase_price;
        $product->discount          = $request->discount;
        $product->discount_type     = $request->discount_type;

        $product->meta_title        = $request->meta_title;
        $product->meta_description  = $request->meta_description;
        $product->meta_img          = $request->meta_img;

        $product->file_name = $request->file;

        $product->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name)).'-'.rand(10000,99999);

        if($product->save()){
            $request->merge(['product_id' => $product->id]);
            //VAT & Tax
            if ($request->tax_id) {
                (new ProductTaxService)->store($request->only([
                    'tax_id', 'tax', 'tax_type', 'product_id'
                ]));
            }
            
            $product_stock              = new ProductStock;
            $product_stock->product_id  = $product->id;
            $product_stock->variant     = '';
            $product_stock->price       = $request->unit_price;
            $product_stock->sku         = '';
            $product_stock->qty         = 0;
            $product_stock->save();

            // Product Translations
            $product_translation                = ProductTranslation::firstOrNew(['lang' => env('DEFAULT_LANGUAGE'), 'product_id' => $product->id]);
            $product_translation->name          = $request->name;
            $product_translation->description   = $request->description;
            $product_translation->save();

            flash(translate('Digital Product has been inserted successfully'))->success();
            return redirect()->route('seller.digitalproducts');
        }
        else{
            flash(translate('Something went wrong'))->error();
            return back();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $categories = Category::where('digital', 1)->get();
        $lang = $request->lang;
        $product = Product::find($id);
        return view('seller.product.digitalproducts.edit', compact('categories', 'product', 'lang'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $product                    = Product::findOrFail($id);
        if($request->lang == env("DEFAULT_LANGUAGE")){
            $product->name          = $request->name;
            $product->description   = $request->description;
        }

        $product->user_id           = Auth::user()->id;
        $product->category_id       = $request->category_id;
        $product->digital           = 1;
        $product->photos            = $request->photos;
        $product->thumbnail_img     = $request->thumbnail_img;

        $tags = array();
        if($request->tags[0] != null){
            foreach (json_decode($request->tags[0]) as $key => $tag) {
                array_push($tags, $tag->value);
            }
        }
        $product->tags = implode(',', $tags);

        $product->unit_price        = $request->unit_price;
        $product->purchase_price    = $request->purchase_price;
        $product->discount          = $request->discount;
        $product->discount_type     = $request->discount_type;

        $product->meta_title        = $request->meta_title;
        $product->meta_description  = $request->meta_description;
        $product->meta_img          = $request->meta_img;
        $product->slug              = strtolower($request->slug);

        // if($request->hasFile('file')){
        //     $product->file_name = $request->file('file')->getClientOriginalName();
        //     $product->file_path = $request->file('file')->store('uploads/products/digital');
        // }

        $product->file_name = $request->file;

        // Delete From Product Stock
        foreach ($product->stocks as $key => $stock) {
            $stock->delete();
        }
        
        if($product->save()){
            $request->merge(['product_id' => $product->id]);
            //VAT & Tax
            if ($request->tax_id) {
                ProductTax::where('product_id', $product->id)->delete();
                (new ProductTaxService)->store($request->only([
                    'tax_id', 'tax', 'tax_type', 'product_id'
                ]));
            }
            // Insert Into Product Stock
            $product_stock              = new ProductStock;
            $product_stock->product_id  = $product->id;
            $product_stock->variant     = '';
            $product_stock->price       = $request->unit_price;
            $product_stock->sku         = '';
            $product_stock->qty         = 0;
            $product_stock->save();
            
            // Product Translations
            $product_translation                = ProductTranslation::firstOrNew(['lang' => $request->lang, 'product_id' => $product->id]);
            $product_translation->name          = $request->name;
            $product_translation->description   = $request->description;
            $product_translation->save();

            flash(translate('Digital Product has been updated successfully'))->success();
            if(Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff'){
                return back();
            }
            else{
                return back();
            }
        }
        else{
            flash(translate('Something went wrong'))->error();
            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        $product->product_translations()->delete();
        $product->stocks()->delete();

        Product::destroy($id);

        flash(translate('Product has been deleted successfully'))->success();
        return redirect()->route('seller.digitalproducts');
    }


    public function download(Request $request){
        $product = Product::findOrFail(decrypt($request->id));
        if(Auth::user()->id == $product->user_id){
            $upload = Upload::findOrFail($product->file_name);
            if (env('FILESYSTEM_DRIVER') == "s3") {
                return \Storage::disk('s3')->download($upload->file_name, $upload->file_original_name.".".$upload->extension);
            }
            else {
                if (file_exists(base_path('public/'.$upload->file_name))) {
                    return response()->download(base_path('public/'.$upload->file_name));
                }
            }
        }
        else {
            abort(404);
        }
    }
}
