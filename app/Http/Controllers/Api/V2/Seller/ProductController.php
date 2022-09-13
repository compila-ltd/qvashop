<?php

namespace App\Http\Controllers\Api\V2\Seller;

use App\Http\Resources\V2\ReviewCollection;
use App\Http\Resources\V2\Seller\ProductCollection;
use App\Http\Resources\V2\Seller\ProductResource;
use App\Http\Resources\V2\Seller\ProductReviewCollection;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\ProductTax;
use App\Models\Review;
use App\Models\User;
use App\Services\ProductStockService;
use App\Services\ProductTaxService;
use Artisan;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->where('user_id', auth()->user()->id)->paginate(10);
        return new ProductCollection($products);
    }

    public function edit()
    {
        $product = Product::where('user_id', auth()->user()->id)->first();
        return new ProductResource($product);
    }

    public function change_status(Request $request)
    {
        $product = Product::where('user_id', auth()->user()->id)
            ->where('id', $request->id)
            ->update([
                'published' => $request->status
            ]);

        if ($product == 0) {
            return $this->failed(translate('This product is not yours'));
        }
        return ($request->status == 1) ?
            $this->success(translate('Product has been published successfully')) :
            $this->success(translate('Product has been unpublished successfully'));
    }

    public function change_featured_status(Request $request)
    {
        $product = Product::where('user_id', auth()->user()->id)
            ->where('id', $request->id)
            ->update([
                'seller_featured' => $request->featured_status
            ]);

        if ($product == 0) {
          return  $this->failed(translate('This product is not yours'));
        }

        return ($request->featured_status == 1) ?
            $this->success(translate('Product has been featured successfully')) :
            $this->success(translate('Product has been unfeatured successfully'));
    }

    public function duplicate($id)
    {
        $product = Product::findOrFail($id);
        
        if (auth()->user()->id != $product->user_id) {
            return $this->failed(translate('This product is not yours'));
        }
        if (addon_is_activated('seller_subscription')) {
            if (!seller_package_validity_check(auth()->user()->id)) {
                return $this->failed(translate('Please upgrade your package'));
            }
        }

        if (auth()->user()->id == $product->user_id) {
            $product_new = $product->replicate();
            $product_new->slug = $product_new->slug . '-' . Str::random(5);
            $product_new->save();

            //Store in Product Stock Table
            (new ProductStockService)->product_duplicate_store($product->stocks, $product_new);

            //Store in Product Tax Table
            (new ProductTaxService)->product_duplicate_store($product->taxes, $product_new);

            return $this->success(translate('Product has been duplicated successfully'));
        }
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if (auth()->user()->id != $product->user_id) {
            return $this->failed(translate('This product is not yours'));
        }

        $product->product_translations()->delete();
        $product->stocks()->delete();
        $product->taxes()->delete();

        if (Product::destroy($id)) {
            Cart::where('product_id', $id)->delete();

            return $this->success(translate('Product has been deleted successfully'));

            Artisan::call('view:clear');
            Artisan::call('cache:clear');
        }
    }

    public function product_reviews()
    {
        $reviews = Review::orderBy('id', 'desc')
            ->join('products', 'reviews.product_id', '=', 'products.id')
            ->join('users','reviews.user_id','=','users.id')
            ->where('products.user_id', auth()->user()->id)
            ->select('reviews.id','reviews.rating','reviews.comment','reviews.status','reviews.updated_at','products.name as product_name','users.id as user_id','users.name','users.avatar')
            ->distinct()
            ->paginate(1);
        
       return new ProductReviewCollection($reviews);
    }

    public function remainingUploads(){
        
        $remaining_uploads=(max(0, auth()->user()->shop->product_upload_limit - auth()->user()->products()->count()) );
        return response()->json([
            'ramaining_product'=> $remaining_uploads,
        ]);
    }

}
