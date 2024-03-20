<?php

namespace App\Http\Controllers\Seller;

use Combinations;
use Carbon\Carbon;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductTax;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\AttributeValue;
use App\Services\ProductService;

use App\Models\ProductTranslation;
use App\Services\ProductTaxService;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProductRequest;
use App\Services\ProductStockService;
use Illuminate\Support\Facades\Artisan;
use App\Services\ProductFlashDealService;

class ProductController extends Controller
{
    protected $productService;
    protected $productTaxService;
    protected $productFlashDealService;
    protected $productStockService;

    public function __construct(
        ProductService $productService,
        ProductTaxService $productTaxService,
        ProductFlashDealService $productFlashDealService,
        ProductStockService $productStockService
    ) {
        $this->productService = $productService;
        $this->productTaxService = $productTaxService;
        $this->productFlashDealService = $productFlashDealService;
        $this->productStockService = $productStockService;
    }

    /**
     * Product Index
     */
    public function index(Request $request)
    {
        $search = null;
        $products = Product::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc');
        if ($request->has('search')) {
            $search = $request->search;
            $products = $products->where('name', 'like', '%' . $search . '%');
        }
        $products = $products->paginate(10);

        return view('seller.product.products.index', compact('products', 'search'));
    }

    /**
     * Create a product
     */
    public function create(Request $request)
    {
        if (addon_is_activated('seller_subscription')) {
            if (seller_package_validity_check()) {
                $categories = Category::where('parent_id', 0)
                    ->with('childrenCategories')
                    ->get();
                return view('seller.product.products.create', compact('categories'));
            } else {
                return back()->with('warning', translate('Please upgrade your package.'));
            }
        }

        $categories = Category::where('parent_id', 0)
            ->with('childrenCategories')
            ->get();

        return view('seller.product.products.create', compact('categories'));
    }

    /**
     * Store a product
     */
    public function store(ProductRequest $request)
    {
        /*
        if( !$request->has('shipping_type') ){
            $product->shipping_type = "flat_rate";
        }
        */
        
        if (addon_is_activated('seller_subscription')) {
            if (!seller_package_validity_check()) {
                return redirect()->route('seller.products')->with('warning', translate('Please upgrade your package.'));
            }
        }

        $product = $this->productService->store($request->except([
            '_token',
            'sku',
            'choice',
            'tax_id',
            'tax',
            'tax_type',
            'flash_deal_id',
            'flash_discount',
            'flash_discount_type'
        ]));
        $request->merge(['product_id' => $product->id]);

        //VAT & Tax
        if ($request->tax_id) {
            $this->productTaxService->store($request->only([
                'tax_id',
                'tax',
                'tax_type',
                'product_id'
            ]));
        }

        //Product Stock
        $this->productStockService->store($request->only([
            'colors_active',
            'colors',
            'choice_no',
            'unit_price',
            'sku',
            'current_stock',
            'product_id'
        ]), $product);

        // Product Translations
        $request->merge(['lang' => env('DEFAULT_LANGUAGE')]);
        ProductTranslation::create($request->only([
            'lang',
            'name',
            'unit',
            'description',
            'product_id'
        ]));

        Artisan::call('view:clear');
        Artisan::call('cache:clear');

        return redirect()->route('seller.products')->with('success', translate('Product has been inserted successfully'));
    }

    public function edit(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        if (Auth::user()->id != $product->user_id)
            return back()->with('warning', translate('This product is not yours.'));

        $lang = $request->lang;
        $tags = json_decode($product->tags);
        $categories = Category::where('parent_id', 0)
            ->with('childrenCategories')
            ->get();

        return view('seller.product.products.edit', compact('product', 'categories', 'tags', 'lang'));
    }

    /**
     * Update a product
     */
    public function update(ProductRequest $request, Product $product)
    {
        if( !$request->has('shipping_type') ){
            $product->shipping_type="flat_rate";
        }
        //Product
        $product = $this->productService->update($request->except([
            '_token',
            'sku',
            'choice',
            'tax_id',
            'tax',
            'tax_type',
            'flash_deal_id',
            'flash_discount',
            'flash_discount_type'
        ]), $product);

        //Product Stock
        foreach ($product->stocks as $key => $stock) {
            $stock->delete();
        }

        $request->merge(['product_id' => $product->id]);
        $this->productStockService->store($request->only([
            'colors_active',
            'colors',
            'choice_no',
            'unit_price',
            'sku',
            'current_stock',
            'product_id'
        ]), $product);

        //VAT & Tax
        if ($request->tax_id) {
            ProductTax::where('product_id', $product->id)->delete();
            $request->merge(['product_id' => $product->id]);
            $this->productTaxService->store($request->only([
                'tax_id',
                'tax',
                'tax_type',
                'product_id'
            ]));
        }

        // Product Translations
        ProductTranslation::where('lang', $request->lang)
            ->where('product_id', $request->product_id)
            ->update($request->only([
                'lang',
                'name',
                'unit',
                'description',
                'product_id'
            ]));

        Artisan::call('view:clear');
        Artisan::call('cache:clear');

        return back()->with('success', translate('Product has been updated successfully'));
    }

    /**
     * SKU Combinations
     */
    public function sku_combination(Request $request)
    {
        $options = array();
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $colors_active = 1;
            array_push($options, $request->colors);
        } else {
            $colors_active = 0;
        }

        $unit_price = $request->unit_price;
        $product_name = $request->name;

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $data = array();
                foreach ($request[$name] as $key => $item) {
                    array_push($data, $item);
                }
                array_push($options, $data);
            }
        }

        $combinations = make_combinations($options);
        return view('backend.product.products.sku_combinations', compact('combinations', 'unit_price', 'colors_active', 'product_name'));
    }

    public function sku_combination_edit(Request $request)
    {
        $product = Product::findOrFail($request->id);

        $options = array();
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $colors_active = 1;
            array_push($options, $request->colors);
        } else {
            $colors_active = 0;
        }

        $product_name = $request->name;
        $unit_price = $request->unit_price;

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $data = array();
                foreach ($request[$name] as $key => $item) {
                    array_push($data, $item);
                }
                array_push($options, $data);
            }
        }

        $combinations = make_combinations($options);
        return view('backend.product.products.sku_combinations_edit', compact('combinations', 'unit_price', 'colors_active', 'product_name', 'product'));
    }

    public function add_more_choice_option(Request $request)
    {
        $all_attribute_values = AttributeValue::with('attribute')->where('attribute_id', $request->attribute_id)->get();
        $html = '';
        foreach ($all_attribute_values as $row) {
            $html .= '<option value="' . $row->value . '">' . $row->value . '</option>';
        }
        echo json_encode($html);
    }

    public function updatePublished(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->published = $request->status;
        if (addon_is_activated('seller_subscription') && $request->status == 1) {
            $shop = $product->user->shop;
            if (
                $shop->package_invalid_at == null
                || Carbon::now()->diffInDays(Carbon::parse($shop->package_invalid_at), false) < 0
                || $shop->product_upload_limit <= $shop->user->products()->where('published', 1)->count()
            ) {
                return 2;
            }
        }
        $product->save();
        return 1;
    }

    public function updateFeatured(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->seller_featured = $request->status;
        if ($product->save()) {
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
            return 1;
        }
        return 0;
    }

    public function updateOnlyPickupPoint(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->only_pickup_point = $request->status;
        if ($product->save()) {
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
            return 1;
        }
        return 0;
    }

    public function updateNegotiableTransportation(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->negotiable_transportation = $request->status;
        if ($product->save()) {
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
            return 1;
        }
        return 0;
    }

    public function duplicate($id)
    {
        $product = Product::find($id);
        if (Auth::user()->id != $product->user_id)
            return back()->with('warning', translate('This product is not yours.'));

        if (addon_is_activated('seller_subscription')) {
            if (!seller_package_validity_check()) {
                return back()->with('warning', translate('Please upgrade your package.'));
            }
        }

        if (Auth::user()->id == $product->user_id) {
            $product_new = $product->replicate();
            $product_new->slug = $product_new->slug . '-' . Str::random(5);
            $product_new->save();

            //Product Stock
            $this->productStockService->product_duplicate_store($product->stocks, $product_new);

            //VAT & Tax
            $this->productTaxService->product_duplicate_store($product->taxes, $product_new);

            return redirect()->route('seller.products')->with('success', translate('Product has been duplicated successfully'));
        }

        return back()->with('warning', translate('This product is not yours.'));
    }

    /**
     * Destroy this product
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if (Auth::user()->id != $product->user_id)
            return back()->with('warning', translate('This product is not yours.'));

        $product->product_translations()->delete();
        $product->stocks()->delete();
        $product->taxes()->delete();

        if (Product::destroy($id)) {
            Cart::where('product_id', $id)->delete();
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
            return back()->with('success', translate('Product has been deleted successfully'));
        }

        return back()->with('danger', translate('Something went wrong'));
    }
}
