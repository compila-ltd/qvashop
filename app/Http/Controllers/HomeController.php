<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Shop;
use App\Models\User;
use App\Models\Brand;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\Category;
use App\Models\FlashDeal;
use App\Models\PickupPoint;
use Illuminate\Support\Str;
use App\Models\ProductQuery;
use Illuminate\Http\Request;
use Spatie\SchemaOrg\Schema;
use App\Models\AffiliateConfig;
use App\Models\CustomerPackage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Response;
use Illuminate\Auth\Events\PasswordReset;
use App\Mail\SecondEmailVerifyMailManager;
use Illuminate\Support\Facades\URL;

class HomeController extends Controller
{
    /**
     * Show the application frontend home.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // SEO & Schema
        $onlineStore = Schema::OnlineStore()
            ->name('QvaShop')
            ->logo(asset('android-chrome-512x512.png'))
            ->telephone('+17867918868')
            ->address('1470 W 40th St')
            ->email('contacto@qvashop.com')
            ->url(route('home'));

        // Featured
        $featured_categories = Cache::rememberForever('featured_categories', function () {
            return Category::where('featured', 1)->get();
        });

        // Todays Deal
        $todays_deal_products = Cache::rememberForever('todays_deal_products', function () {
            return filter_products(Product::where('published', 1)->where('todays_deal', '1'))->get();
        });

        // Newest products
        $newest_products = Cache::remember('newest_products', 7200, function () {
            return filter_products(Product::latest())->limit(12)->get();
        });

        // Flash Deals
        $flash_deal = Cache::remember('flash_deal', 7200, function () {
            return FlashDeal::where('status', 1)->where('featured', 1)->first();
        });

        return view('frontend.index', compact('onlineStore', 'featured_categories', 'todays_deal_products', 'newest_products', 'flash_deal'));
    }

    /**
     * User Login
     */
    public function login()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('frontend.user_login');
    }

    /**
     * User Registration
     */
    public function registration(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        if ($request->has('referral_code') && addon_is_activated('affiliate_system')) {
            try {
                $affiliate_validation_time = AffiliateConfig::where('type', 'validation_time')->first();
                $cookie_minute = 30 * 24;
                if ($affiliate_validation_time) {
                    $cookie_minute = $affiliate_validation_time->value * 60;
                }

                Cookie::queue('referral_code', $request->referral_code, $cookie_minute);
                $referred_by_user = User::where('referral_code', $request->referral_code)->first();

                //$affiliateController = new AffiliateController;
                //$affiliateController->processAffiliateStats($referred_by_user->id, 1, 0, 0, 0);

            } catch (\Exception $e) {
            }
        }
        return view('frontend.user_registration');
    }

    /**
     * Login from Cart
     */
    public function cart_login(Request $request)
    {
        $user = null;
        if ($request->get('phone') != null) {
            $user = User::whereIn('user_type', ['customer', 'seller'])->where('phone', "+{$request['country_code']}{$request['phone']}")->first();
        } elseif ($request->get('email') != null) {
            $user = User::whereIn('user_type', ['customer', 'seller'])->where('email', $request->email)->first();
        }

        if ($user != null) {
            if (Hash::check($request->password, $user->password)) {
                if ($request->has('remember')) {
                    auth()->login($user, true);
                } else {
                    auth()->login($user, false);
                }
            } else {
                flash(translate('Invalid email or password!'))->warning();
            }
        } else {
            flash(translate('Invalid email or password!'))->warning();
        }

        return back();
    }

    /**
     * Show the customer/seller dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        if (Auth::user()->user_type == 'seller') {
            return redirect()->route('seller.dashboard');
        } elseif (Auth::user()->user_type == 'customer') {
            return view('frontend.user.customer.dashboard');
        } elseif (Auth::user()->user_type == 'delivery_boy') {
            return view('delivery_boys.frontend.dashboard');
        } else {
            abort(404);
        }
    }

    public function profile(Request $request)
    {
        if (Auth::user()->user_type == 'seller') {
            return redirect()->route('seller.profile.index');
        } elseif (Auth::user()->user_type == 'delivery_boy') {
            return view('delivery_boys.frontend.profile');
        } else {
            return view('frontend.user.profile');
        }
    }

    // Update User Profile
    public function userProfileUpdate(Request $request)
    {
        $user = User::find(Auth::user()->id);

        $user->name = $request->name;
        $user->address = $request->address;
        $user->country = $request->country;
        $user->city = $request->city;
        $user->postal_code = $request->postal_code;
        $user->phone = $request->phone;

        if ($request->new_password != null && ($request->new_password == $request->confirm_password))
            $user->password = Hash::make($request->new_password);

        $user->avatar_original = $request->photo;
        $user->save();

        return back()->with('success', translate('Your Profile has been updated successfully!'));
    }

    // Flash Deals
    public function flash_deal_details($slug)
    {
        // Cache this for 5 minutes
        $flash_deal = Cache::remember('flash_deal_details_' . $slug, 300, function () use ($slug) {
            return FlashDeal::where('slug', $slug)->first();
        });

        if ($flash_deal != null)
            return view('frontend.flash_deal_details', compact('flash_deal'));

        abort(404);
    }

    // Load Featured Products
    public function load_featured_section()
    {
        // Cache featured products
        $featured_products = Cache::remember('featured_products', 3600, function () {
            return filter_products(Product::where('published', 1)->where('featured', '1'))->limit(12)->get();
        });

        return view('frontend.partials.featured_products_section', compact('featured_products'));
    }

    // Best Selling products
    public function load_best_selling_section()
    {
        $best_selling_products = Cache::remember('best_selling_products', 86400, function () {
            return filter_products(Product::where('published', 1)->orderBy('num_of_sale', 'desc'))->limit(20)->get();
        });

        return view('frontend.partials.best_selling_section', compact('best_selling_products'));
    }

    /**
     * Load auction products
     */
    public function load_auction_products_section()
    {
        if (!addon_is_activated('auction'))
            return;

        return view('auction.frontend.auction_products_section');
    }

    // Home categories
    public function load_home_categories_section()
    {
        $categories = [];
        $home_categories = json_decode(get_setting('home_categories'));

        // Get Every category and cache it for a week
        foreach ($home_categories as &$home_category) {
            $category = Cache::remember('home_category_' . $home_category, 10080, function () use ($home_category) {
                return Category::find($home_category);
            });

            $categories[] = $category;
        }

        return view('frontend.partials.home_categories_section', compact('categories'));
    }

    // Best Sellers
    public function load_best_sellers_section()
    {
        $best_selers = Cache::remember('best_selers', 86400, function () {
            return Shop::where('verification_status', 1)->orderBy('num_of_sale', 'desc')->take(20)->get();
        });

        return view('frontend.partials.best_sellers_section', compact('best_selers'));
    }

    // Track Order Page
    public function trackOrder(Request $request)
    {
        if ($request->has('order_code')) {
            $order = Order::where('code', $request->order_code)->first();
            if ($order != null) {
                return view('frontend.track_order', compact('order'));
            }
        }
        return view('frontend.track_order');
    }

    // Product Page
    public function product(Request $request, $slug)
    {
        // Cache product by 5 minutes
        $detailedProduct = Cache::remember('product_' . $slug, 300, function () use ($slug) {
            return Product::with('reviews', 'brand', 'stocks', 'user', 'user.shop')->where('auction_product', 0)->where('slug', $slug)->where('approved', 1)->first();
        });

        $product_queries = ProductQuery::where('product_id', $detailedProduct->id)->where('customer_id', '!=', Auth::id())->latest('id')->paginate(10);
        $total_query = ProductQuery::where('product_id', $detailedProduct->id)->count();

        // Pagination using Ajax
        if (request()->ajax())
            return Response::json(View::make('frontend.partials.product_query_pagination', array('product_queries' => $product_queries))->render());

        if ($detailedProduct != null && $detailedProduct->published) {

            // SEO && Schema Data
            $schemaProduct = Schema::product()
                ->name($detailedProduct->name)
                ->itemCondition('NewCondition')
                ->url(URL::current());

            if ($request->has('product_referral_code') && addon_is_activated('affiliate_system')) {
                $affiliate_validation_time = AffiliateConfig::where('type', 'validation_time')->first();
                $cookie_minute = 30 * 24;
                if ($affiliate_validation_time) {
                    $cookie_minute = $affiliate_validation_time->value * 60;
                }
                Cookie::queue('product_referral_code', $request->product_referral_code, $cookie_minute);
                Cookie::queue('referred_product_id', $detailedProduct->id, $cookie_minute);

                $referred_by_user = User::where('referral_code', $request->product_referral_code)->first();

                // TODO: Implement Affliate System
                // $affiliateController = new AffiliateController;
                // $affiliateController->processAffiliateStats($referred_by_user->id, 1, 0, 0, 0);
            }

            if ($detailedProduct->digital == 1) {
                return view('frontend.digital_product_details', compact('detailedProduct', 'product_queries', 'total_query'));
            } else {
                return view('frontend.product_details', compact('schemaProduct', 'detailedProduct', 'product_queries', 'total_query'));
            }
        }

        abort(404);
    }

    // Shop Page
    public function shop($slug)
    {
        // Cache Shop for 5 minutes
        $shop = Cache::remember('shop_' . $slug, 300, function () use ($slug) {
            return Shop::where('slug', $slug)->first();
        });

        if ($shop != null) {
            if ($shop->verification_status != 0) {
                return view('frontend.seller_shop', compact('shop'));
            } else {
                return view('frontend.seller_shop_without_verification', compact('shop'));
            }
        }
        abort(404);
    }

    // Filter Shop
    public function filter_shop($slug, $type)
    {
        // Cache Shop for 5 minutes
        //$shop  = Shop::where('slug', $slug)->first();
        $shop = Cache::remember('shop_' . $slug, 300, function () use ($slug) {
            return Shop::where('slug', $slug)->first();
        });

        if ($shop != null && $type != null) {
            return view('frontend.seller_shop', compact('shop', 'type'));
        }
        abort(404);
    }

    // All Categories
    public function all_categories(Request $request)
    {
        // Cache All categpries by 30 minutes
        $categories = Cache::remember('all_categories_level_0_ordered', 1800, function () {
            return Category::where('level', 0)->orderBy('order_level', 'desc')->get();
        });

        return view('frontend.all_category', compact('categories'));
    }

    // All Brands
    public function all_brands(Request $request)
    {
        // Cache All Brands by 30 minutes
        $categories = Cache::remember('all_categories', 1800, function () {
            return Category::all();
        });

        // Cache all brands by 30 minutes
        //$brands = Brand::all();
        $brands = Cache::remember('all_brands', 1800, function () {
            return Brand::all();
        });

        return view('frontend.all_brand', compact('categories', 'brands'));
    }

    /**
     * Top 10 Settings
     */
    public function top_10_settings(Request $request)
    {
        foreach (Category::all() as $key => $category) {
            if (is_array($request->top_categories) && in_array($category->id, $request->top_categories)) {
                $category->top = 1;
                $category->save();
            } else {
                $category->top = 0;
                $category->save();
            }
        }

        foreach (Brand::all() as $key => $brand) {
            if (is_array($request->top_brands) && in_array($brand->id, $request->top_brands)) {
                $brand->top = 1;
                $brand->save();
            } else {
                $brand->top = 0;
                $brand->save();
            }
        }

        return redirect()->route('home_settings.index')->with('success', translate('Top 10 categories and brands have been updated successfully'));
    }

    public function variant_price(Request $request)
    {
        $product = Product::find($request->id);
        $str = '';
        $quantity = 0;
        $tax = 0;
        $max_limit = 0;

        if ($request->has('color'))
            $str = $request['color'];

        if (json_decode($product->choice_options) != null) {
            foreach (json_decode($product->choice_options) as $key => $choice) {
                if ($str != null) {
                    $str .= '-' . str_replace(' ', '', $request['attribute_id_' . $choice->attribute_id]);
                } else {
                    $str .= str_replace(' ', '', $request['attribute_id_' . $choice->attribute_id]);
                }
            }
        }

        $product_stock = $product->stocks->where('variant', $str)->first();

        $price = $product_stock->price;

        if ($product->wholesale_product) {
            $wholesalePrice = $product_stock->wholesalePrices->where('min_qty', '<=', $request->quantity)->where('max_qty', '>=', $request->quantity)->first();
            if ($wholesalePrice) {
                $price = $wholesalePrice->price;
            }
        }

        $quantity = $product_stock->qty;
        $max_limit = $product_stock->qty;

        if ($quantity >= 1 && $product->min_qty <= $quantity) {
            $in_stock = 1;
        } else {
            $in_stock = 0;
        }

        //Product Stock Visibility
        if ($product->stock_visibility_state == 'text') {
            if ($quantity >= 1 && $product->min_qty < $quantity) {
                $quantity = translate('In Stock');
            } else {
                $quantity = translate('Out Of Stock');
            }
        }

        //discount calculation
        $discount_applicable = false;

        if ($product->discount_start_date == null) {
            $discount_applicable = true;
        } elseif (
            strtotime(date('d-m-Y H:i:s')) >= $product->discount_start_date &&
            strtotime(date('d-m-Y H:i:s')) <= $product->discount_end_date
        ) {
            $discount_applicable = true;
        }

        if ($discount_applicable) {
            if ($product->discount_type == 'percent') {
                $price -= ($price * $product->discount) / 100;
            } elseif ($product->discount_type == 'amount') {
                $price -= $product->discount;
            }
        }

        // taxes
        foreach ($product->taxes as $product_tax) {
            if ($product_tax->tax_type == 'percent') {
                $tax += ($price * $product_tax->tax) / 100;
            } elseif ($product_tax->tax_type == 'amount') {
                $tax += $product_tax->tax;
            }
        }

        $price += $tax;

        return array(
            'price' => single_price($price * $request->quantity),
            'quantity' => $quantity,
            'digital' => $product->digital,
            'variation' => $str,
            'max_limit' => $max_limit,
            'in_stock' => $in_stock
        );
    }

    // Seller Policy
    public function sellerpolicy()
    {
        // cache this for a week
        $policy = Cache::remember('seller_policy_page', 10080, function () {
            return Page::where('slug', 'seller_policy_page')->first();
        });

        return view("frontend.policies.sellerpolicy", compact('page'));
    }

    // Return Policy
    public function returnpolicy()
    {
        // cache this for a week
        $page = Cache::remember('return_policy_page', 10080, function () {
            return Page::where('type', 'return_policy_page')->first();
        });

        return view("frontend.policies.returnpolicy", compact('page'));
    }

    // Support Policy
    public function supportpolicy()
    {
        // cache this for a week
        $page = Cache::remember('support_policy_page', 10080, function () {
            return Page::where('type', 'support_policy_page')->first();
        });

        return view("frontend.policies.supportpolicy", compact('page'));
    }

    // Terms
    public function terms()
    {
        // cache this for a week
        $page = Cache::remember('terms_page', 10080, function () {
            return Page::where('type', 'terms_conditions_page')->first();
        });

        return view("frontend.policies.terms", compact('page'));
    }

    // Privacy Policy
    public function privacypolicy()
    {
        // cache this for a week
        $page = Cache::remember('privacy_policy_page', 10080, function () {
            return Page::where('type', 'privacy_policy_page')->first();
        });

        return view("frontend.policies.privacypolicy", compact('page'));
    }

    // Pick up Points
    public function get_pick_up_points(Request $request)
    {
        // Cache this for a week
        $pick_up_points = Cache::remember('pick_up_points', 10080, function () {
            return PickUpPoint::all();
        });

        return view('frontend.partials.pick_up_points', compact('pick_up_points'));
    }

    // Category Items
    // TODO: Cleanup nested foreachs
    public function get_category_items(Request $request)
    {
        // Cache for 5 minutes
        $category = Cache::remember('category_items_' . $request->id, 300, function () use ($request) {
            return Category::findOrFail($request->id);
        });

        return view('frontend.partials.category_elements', compact('category'));
    }

    // Premium Package index
    public function premium_package_index()
    {
        // Cache this for a day
        $customer_packages = Cache::remember('premium_packages', 1440, function () {
            return CustomerPackage::all();
        });

        return view('frontend.user.customer_packages_lists', compact('customer_packages'));
    }


    // Ajax call
    public function new_verify(Request $request)
    {
        $email = $request->email;
        if (isUnique($email) == '0') {
            $response['status'] = 2;
            $response['message'] = 'Email already exists!';
            return json_encode($response);
        }

        $response = $this->send_email_change_verification_mail($request, $email);
        return json_encode($response);
    }


    // Form request
    public function update_email(Request $request)
    {
        $email = $request->email;
        if (isUnique($email)) {
            $this->send_email_change_verification_mail($request, $email);
            return back()->with('success', translate('A verification mail has been sent to the mail you provided us with.'));
        }

        return back()->with('warning', translate('Email already exists!'));
    }

    // Email verification
    public function send_email_change_verification_mail($request, $email)
    {
        $response['status'] = 0;
        $response['message'] = 'Unknown';

        $verification_code = Str::random(32);

        $array['subject'] = 'Email Verification';
        $array['from'] = env('MAIL_FROM_ADDRESS');
        $array['content'] = 'Verify your account';
        $array['link'] = route('email_change.callback') . '?new_email_verificiation_code=' . $verification_code . '&email=' . $email;
        $array['sender'] = Auth::user()->name;
        $array['details'] = "Email Second";

        $user = User::find(Auth::user()->id);
        $user->new_email_verificiation_code = $verification_code;
        $user->save();

        try {
            Mail::to($email)->queue(new SecondEmailVerifyMailManager($array));
            $response['status'] = 1;
            $response['message'] = translate("Your verification mail has been Sent to your email.");
        } catch (\Exception $e) {
            $response['status'] = 0;
            $response['message'] = $e->getMessage();
        }

        return $response;
    }

    // Email change Callback
    public function email_change_callback(Request $request)
    {
        if ($request->has('new_email_verificiation_code') && $request->has('email')) {
            $verification_code_of_url_param =  $request->input('new_email_verificiation_code');
            $user = User::where('new_email_verificiation_code', $verification_code_of_url_param)->first();

            if ($user != null) {

                $user->email = $request->input('email');
                $user->new_email_verificiation_code = null;
                $user->save();

                auth()->login($user, true);

                // Route definition on user type
                $route = ($user->user_type == 'seller') ? 'seller.dashboard' : 'dashboard';

                return redirect()->route($route)->with('success', translate('Email Changed successfully'));
            }
        }

        return redirect()->route('dashboard')->with('warning', translate('Email was not verified. Please resend your mail!'));
    }

    // Reset Password
    public function reset_password_with_code(Request $request)
    {
        if (($user = User::where('email', $request->email)->where('verification_code', $request->code)->first()) != null) {

            if ($request->password == $request->password_confirmation) {

                $user->password = Hash::make($request->password);
                $user->email_verified_at = date('Y-m-d h:m:s');
                $user->save();

                event(new PasswordReset($user));
                auth()->login($user, true);

                if (auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'staff')
                    return redirect()->route('admin.dashboard')->with('success', translate('Password updated successfully'));

                return redirect()->route('home')->with('success', translate('Password updated successfully'));
            }

            return view('auth.passwords.reset')->with('warning', "Password and confirm password didn't match");
        }

        return view('auth.passwords.reset')->with('danger', "Verification code mismatch");
    }

    // All Flash Deals
    public function all_flash_deals()
    {
        $today = strtotime(date('Y-m-d H:i:s'));

        // Cache all flash deals
        $flash_deals = Cache::remember('all_flash_deals', 10080, function () use ($today) {
            return FlashDeal::where('status', 1)
                ->where('start_date', "<=", $today)
                ->where('end_date', ">", $today)
                ->orderBy('created_at', 'desc')
                ->get();
        });

        $data['all_flash_deals'] = $flash_deals;

        return view("frontend.flash_deal.all_flash_deal_list", $data);
    }

    // Sellers
    public function all_seller(Request $request)
    {
        $shops = Shop::whereIn('user_id', verified_sellers_id())->paginate(15);
        return view('frontend.shop_listing', compact('shops'));
    }

    // Coupons
    public function all_coupons(Request $request)
    {
        $coupons = Coupon::where('start_date', '<=', strtotime(date('d-m-Y')))->where('end_date', '>=', strtotime(date('d-m-Y')))->paginate(15);
        return view('frontend.coupons', compact('coupons'));
    }

    // Inhouse Products
    public function inhouse_products(Request $request)
    {
        $products = filter_products(Product::where('added_by', 'admin'))->with('taxes')->paginate(12)->appends(request()->query());
        return view('frontend.inhouse_products', compact('products'));
    }
}
