<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\AizUploadController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProductQueryController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\DigitalProductController;
use App\Http\Controllers\CustomerPackageController;
use App\Http\Controllers\CustomerProductController;
use App\Http\Controllers\Payments\QvaPayController;
use App\Http\Controllers\PurchaseHistoryController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Payments\LightningController;

/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register web routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "web" middleware group. Now create something great!
  |
 */

Route::get('/refresh-csrf', function () {
    return csrf_token();
});

// Sitemap generator
Route::get('/sitemap.xml', [SitemapController::class, 'generate'])->name('sitemap-generator');

// AIZ Uploader
Route::controller(AizUploadController::class)->group(function () {
    Route::post('/aiz-uploader', 'show_uploader');
    Route::post('/aiz-uploader/upload', 'upload');
    Route::get('/aiz-uploader/get_uploaded_files', 'get_uploaded_files');
    Route::post('/aiz-uploader/get_file_by_ids', 'get_preview_files');
    Route::get('/aiz-uploader/download/{id}', 'attachment_download')->name('download_attachment');
});

// Auth ROutes
Auth::routes(['verify' => true]);

// Login
Route::controller(LoginController::class)->group(function () {
    Route::get('/logout', 'logout');
    Route::get('/social-login/redirect/{provider}', 'redirectToProvider')->name('social.login');
    Route::get('/social-login/{provider}/callback', 'handleProviderCallback')->name('social.callback');
});

// Email Verification
Route::controller(VerificationController::class)->group(function () {
    Route::get('/email/resend', 'resend')->name('verification.resend.new');
    Route::get('/verification-confirmation/{code}', 'verification_confirmation')->name('email.verification.confirmation');
});

// General Routes
Route::controller(HomeController::class)->group(function () {

    Route::get('/email_change/callback', 'email_change_callback')->name('email_change.callback');
    Route::post('/password/reset/email/submit', 'reset_password_with_code')->name('password.update.new');
    Route::get('/users/login', 'login')->name('user.login');
    Route::get('/users/registration', 'registration')->name('user.registration');
    Route::post('/users/login/cart', 'cart_login')->name('cart.login.submit');

    //Home Page
    Route::get('/', 'index')->name('home');

    // AJAX Calls
    Route::group(['prefix' => 'home/section'], function () {
        Route::post('/featured', 'load_featured_section')->name('home.section.featured');
        Route::post('/best_selling', 'load_best_selling_section')->name('home.section.best_selling');
        Route::post('/home_categories', 'load_home_categories_section')->name('home.section.home_categories');
        Route::post('/best_sellers', 'load_best_sellers_section')->name('home.section.best_sellers');
    });

    //category dropdown menu ajax call
    Route::post('/category/nav-element-list', 'get_category_items')->name('category.elements');

    //Flash Deal Details Page
    Route::get('/flash-deals', 'all_flash_deals')->name('flash-deals');
    Route::get('/flash-deal/{slug}', 'flash_deal_details')->name('flash-deal-details');

    // Show product page
    Route::get('/product/{slug}', 'product')->name('product');
    Route::post('/product/variant_price', 'variant_price')->name('products.variant_price');
    Route::get('/shop/{slug}', 'shop')->name('shop.visit');
    Route::get('/shop/{slug}/{type}', 'filter_shop')->name('shop.visit.type');

    Route::get('/customer-packages', 'premium_package_index')->name('customer_packages_list_show');

    Route::get('/brands', 'all_brands')->name('brands.all');
    Route::get('/categories', 'all_categories')->name('categories.all');
    Route::get('/sellers', 'all_seller')->name('sellers');
    Route::get('/coupons', 'all_coupons')->name('coupons.all');
    Route::get('/inhouse', 'inhouse_products')->name('inhouse.all');

    // Policies
    Route::get('/seller-policy', 'sellerpolicy')->name('sellerpolicy');
    Route::get('/return-policy', 'returnpolicy')->name('returnpolicy');
    Route::get('/support-policy', 'supportpolicy')->name('supportpolicy');
    Route::get('/terms', 'terms')->name('terms');
    Route::get('/privacy-policy', 'privacypolicy')->name('privacypolicy');

    Route::get('/track-your-order', 'trackOrder')->name('orders.track');
});

// Language Switch
Route::post('/language', [LanguageController::class, 'changeLanguage'])->name('language.change');

// Currency Switch
Route::post('/currency', [CurrencyController::class, 'changeCurrency'])->name('currency.change');

// Classified Product
Route::controller(CustomerProductController::class)->group(function () {
    Route::get('/customer-products', 'customer_products_listing')->name('customer.products');
    Route::get('/customer-products?category={category_slug}', 'search')->name('customer_products.category');
    Route::get('/customer-products?city={city_id}', 'search')->name('customer_products.city');
    Route::get('/customer-products?q={search}', 'search')->name('customer_products.search');
    Route::get('/customer-product/{slug}', 'customer_product')->name('customer.product');
});

// Search
Route::controller(SearchController::class)->group(function () {
    Route::get('/search', 'index')->name('search');
    Route::get('/search?keyword={search}', 'index')->name('suggestion.search');
    Route::post('/ajax-search', 'ajax_search')->name('search.ajax');
    Route::get('/category/{category_slug}', 'listingByCategory')->name('products.category');
    Route::get('/brand/{brand_slug}', 'listingByBrand')->name('products.brand');
});

// Cart
Route::controller(CartController::class)->group(function () {
    Route::get('/cart', 'index')->name('cart');
    Route::post('/cart/show-cart-modal', 'showCartModal')->name('cart.showCartModal');
    Route::post('/cart/addtocart', 'addToCart')->name('cart.addToCart');
    Route::post('/cart/removeFromCart', 'removeFromCart')->name('cart.removeFromCart');
    Route::post('/cart/updateQuantity', 'updateQuantity')->name('cart.updateQuantity');
});

// Compare
Route::controller(CompareController::class)->group(function () {
    Route::get('/compare', 'index')->name('compare');
    Route::get('/compare/reset', 'reset')->name('compare.reset');
    Route::post('/compare/addToCompare', 'addToCompare')->name('compare.addToCompare');
});

// Subscribe
Route::resource('subscribers', SubscriberController::class);

Route::group(['middleware' => ['user', 'verified', 'unbanned']], function () {
    Route::controller(HomeController::class)->group(function () {
        Route::get('/dashboard', 'dashboard')->name('dashboard');
        Route::get('/profile', 'profile')->name('profile');
        Route::post('/new-user-verification', 'new_verify')->name('user.new.verify');
        Route::post('/new-user-email', 'update_email')->name('user.change.email');
        Route::post('/user/update-profile', 'userProfileUpdate')->name('user.profile.update');
    });
    Route::get('/all-notifications', [NotificationController::class, 'index'])->name('all-notifications');
});

Route::group(['middleware' => ['customer', 'verified', 'unbanned']], function () {

    // Checkout Routes
    Route::group(['prefix' => 'checkout'], function () {
        Route::controller(CheckoutController::class)->group(function () {
            Route::get('/', 'get_shipping_info')->name('checkout.shipping_info');
            Route::any('/delivery_info', 'store_shipping_info')->name('checkout.store_shipping_infostore');
            Route::post('/payment_select', 'store_delivery_info')->name('checkout.store_delivery_info');
            Route::get('/order-confirmed', 'order_confirmed')->name('order_confirmed');
            Route::post('/payment', 'checkout')->name('payment.checkout');
            Route::post('/get_pick_up_points', 'get_pick_up_points')->name('shipping_info.get_pick_up_points');
            Route::get('/payment-select', 'get_payment_info')->name('checkout.payment_info');
            Route::post('/apply_coupon_code', 'apply_coupon_code')->name('checkout.apply_coupon_code');
            Route::post('/remove_coupon_code', 'remove_coupon_code')->name('checkout.remove_coupon_code');
            //Club point
            Route::post('/apply-club-point', 'apply_club_point')->name('checkout.apply_club_point');
            Route::post('/remove-club-point', 'remove_club_point')->name('checkout.remove_club_point');
        });
    });

    // Purchase History
    Route::resource('purchase_history', PurchaseHistoryController::class)->except('destroy');
    Route::controller(PurchaseHistoryController::class)->group(function () {
        Route::get('/purchase_history/details/{id}', 'purchase_history_details')->name('purchase_history.details');
        Route::get('/purchase_history/destroy/{id}', 'order_cancel')->name('purchase_history.destroy');
        Route::get('digital_purchase_history', 'digital_index')->name('digital_purchase_history.index');
    });

    // Wishlist
    Route::resource('wishlists', WishlistController::class);
    Route::post('/wishlists/remove', [WishlistController::class, 'remove'])->name('wishlists.remove');

    // Wallet
    Route::controller(WalletController::class)->group(function () {
        Route::get('/wallet', 'index')->name('wallet.index');
        Route::post('/recharge', 'recharge')->name('wallet.recharge');
    });

    // Support Ticket
    Route::resource('support_ticket', SupportTicketController::class);
    Route::post('support_ticket/reply', [SupportTicketController::class, 'seller_store'])->name('support_ticket.seller_store');

    // Customer Package
    Route::post('/customer_packages/purchase', [CustomerPackageController::class, 'purchase_package'])->name('customer_packages.purchase');

    // Customer Product
    Route::resource('customer_products', CustomerProductController::class)->except('edit', 'destroy');
    Route::controller(CustomerProductController::class)->group(function () {
        Route::get('/customer_products/{id}/edit', 'edit')->name('customer_products.edit');
        Route::post('/customer_products/published', 'updatePublished')->name('customer_products.published');
        Route::post('/customer_products/status', 'updateStatus')->name('customer_products.update.status');
        Route::get('/customer_products/destroy/{id}', 'destroy')->name('customer_products.destroy');
    });

    // Product Review
    Route::post('/product_review_modal', [ReviewController::class, 'product_review_modal'])->name('product_review_modal');

    // Digital Product
    Route::controller(DigitalProductController::class)->group(function () {
        Route::get('/digital-products/download/{id}', 'download')->name('digital-products.download');
    });
});

Route::group(['middleware' => ['auth']], function () {

    Route::get('invoice/{order_id}', [InvoiceController::class, 'invoice_download'])->name('invoice.download');

    // Reviews
    Route::resource('/reviews', ReviewController::class);

    // Product Conversation
    Route::resource('conversations', ConversationController::class)->except('destroy');
    Route::controller(ConversationController::class)->group(function () {
        Route::get('/conversations/destroy/{id}', 'destroy')->name('conversations.destroy');
        Route::post('conversations/refresh', 'refresh')->name('conversations.refresh');
    });

    // Product Query
    Route::resource('product-queries', ProductQueryController::class);
    Route::resource('messages', MessageController::class);

    //Address
    Route::resource('addresses', AddressController::class)->except('update', 'destroy');
    Route::controller(AddressController::class)->group(function () {
        Route::post('/get-states', 'getStates')->name('get-state');
        Route::post('/get-cities', 'getCities')->name('get-city');
        Route::post('/addresses/update/{id}', 'update')->name('addresses.update');
        Route::get('/addresses/destroy/{id}', 'destroy')->name('addresses.destroy');
        Route::get('/addresses/set_default/{id}', 'set_default')->name('addresses.set_default');
    });
});

// Shops routes
Route::resource('shops', ShopController::class)->except('store');
Route::post('/shops/store', [ShopController::class, 'store'])->name('shops.store');

// QvaPay WebHook
Route::get('/qvapay/payment/pay-success/' . config('qvapay.callback_secret'), [QvaPayController::class, 'success'])->name('payment.qvapay');
Route::get('/bitcoinln/check', [LightningController::class, 'check'])->name('payment.bitcoinln');

//Blog Section
Route::controller(BlogController::class)->group(function () {
    Route::get('/blog', 'all_blog')->name('blog');
    Route::get('/blog/{slug}', 'blog_details')->name('blog.details');
});

// Static Pages
Route::controller(PageController::class)->group(function () {
    Route::get('/mobile-page/{slug}', 'mobile_custom_page')->name('mobile.custom-pages');
    Route::get('/{slug}', 'show_custom_page')->name('custom-pages.show_custom_page');
});
