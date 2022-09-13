<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\ProductCollection;
use App\Http\Resources\V2\ProductMiniCollection;
use App\Http\Resources\V2\ShopCollection;
use App\Http\Resources\V2\ShopDetailsCollection;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\Request;
use App\Utility\SearchUtility;
use Cache;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $shop_query = Shop::query();

        if ($request->name != null && $request->name != "") {
            $shop_query->where("name", 'like', "%{$request->name}%");
            SearchUtility::store($request->name);
        }

        return new ShopCollection($shop_query->whereIn('user_id', verified_sellers_id())->paginate(10));

        //remove this , this is for testing
        //return new ShopCollection($shop_query->paginate(10));
    }

    public function info($id)
    {
        return new ShopDetailsCollection(Shop::where('id', $id)->first());
    }

    public function shopOfUser($id)
    {
        return new ShopCollection(Shop::where('user_id', $id)->get());
    }

    public function allProducts($id)
    {
        $shop = Shop::findOrFail($id);
        return new ProductCollection(Product::where('user_id', $shop->user_id)->where('published',1)->latest()->paginate(10));
    }

    public function topSellingProducts($id)
    {
        $shop = Shop::findOrFail($id);
        
        return Cache::remember("app.top_selling_products-$id", 86400, function () use ($shop){
            return new ProductMiniCollection(Product::where('user_id', $shop->user_id)->where('published',1)->orderBy('num_of_sale', 'desc')->limit(10)->get());
        });
    }

    public function featuredProducts($id)
    {
        $shop = Shop::findOrFail($id);

        return Cache::remember("app.featured_products-$id", 86400, function () use ($shop){
            return new ProductMiniCollection(Product::where(['user_id' => $shop->user_id, 'featured' => 1])->where('published',1)->latest()->limit(10)->get());
        });
    }

    public function newProducts($id)
    {
        $shop = Shop::findOrFail($id);

        return Cache::remember("app.new_products-$id", 86400, function () use ($shop){
            return new ProductMiniCollection(Product::where('user_id', $shop->user_id)->where('published',1)->orderBy('created_at', 'desc')->limit(10)->get());
        });
    }

    public function brands($id)
    {

    }
}
