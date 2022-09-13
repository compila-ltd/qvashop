<?php

/*
|--------------------------------------------------------------------------
| Auction Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\AuctionProductController;
use App\Http\Controllers\AuctionProductBidController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;

//Admin
Route::group(['prefix' =>'admin', 'middleware' => ['auth', 'admin']], function(){
    // Auction product lists
    Route::controller(AuctionProductController::class)->group(function () {
        Route::get('auction/all-products', 'all_auction_product_list')->name('auction.all_products');
        Route::get('auction/inhouse-products', 'inhouse_auction_products')->name('auction.inhouse_products');
        Route::get('auction/seller-products', 'seller_auction_products')->name('auction.seller_products');

        Route::get('/auction-product/create', 'product_create_admin')->name('auction_product_create.admin');
        Route::post('/auction-product/store', 'product_store_admin')->name('auction_product_store.admin');
        Route::get('/auction_products/edit/{id}', 'product_edit_admin')->name('auction_product_edit.admin');
        Route::post('/auction_products/update/{id}', 'product_update_admin')->name('auction_product_update.admin');
        Route::get('/auction_products/destroy/{id}', 'product_destroy_admin')->name('auction_product_destroy.admin');

        // Sales
        Route::get('/auction_products-orders', 'admin_auction_product_orders')->name('auction_products_orders');
    });
    Route::controller(AuctionProductBidController::class)->group(function () {
        Route::get('/product-bids/{id}', 'product_bids_admin')->name('product_bids.admin');
        Route::get('/product-bids/destroy/{id}', 'bid_destroy_admin')->name('product_bids_destroy.admin');
    });
});

Route::group(['prefix' => 'seller', 'middleware' => ['seller', 'verified', 'user']], function() {
    Route::controller(AuctionProductController::class)->group(function () {
        Route::get('/auction_products', 'auction_product_list_seller')->name('auction_products.seller.index');

        Route::get('/auction-product/create', 'product_create_seller')->name('auction_product_create.seller');
        Route::post('/auction-product/store', 'product_store_seller')->name('auction_product_store.seller');
        Route::get('/auction_products/edit/{id}', 'product_edit_seller')->name('auction_product_edit.seller');
        Route::post('/auction_products/update/{id}', 'product_update_seller')->name('auction_product_update.seller');
        Route::get('/auction_products/destroy/{id}', 'product_destroy_seller')->name('auction_product_destroy.seller');

        Route::get('/auction_products-orders', 'seller_auction_product_orders')->name('auction_products_orders.seller');
    });
    Route::controller(AuctionProductBidController::class)->group(function () {
        Route::get('/product-bids/{id}', 'product_bids_seller')->name('product_bids.seller');
        Route::get('/product-bids/destroy/{id}', 'bid_destroy_seller')->name('product_bids_destroy.seller');
    });
});

Route::group(['middleware' => ['auth']], function() {
    Route::resource('auction_product_bids', AuctionProductBidController::class);

    Route::post('/auction/cart/show-cart-modal', [CartController::class, 'showCartModalAuction'])->name('auction.cart.showCartModal');
    Route::get('/auction/purchase_history', [AuctionProductController::class, 'purchase_history_user'])->name('auction_product.purchase_history');
});

Route::post('/home/section/auction_products', [HomeController::class, 'load_auction_products_section'])->name('home.section.auction_products');

Route::controller(AuctionProductController::class)->group(function () {
    Route::get('/auction-product/{slug}', 'auction_product_details')->name('auction-product');
    Route::get('/auction-products', 'all_auction_products')->name('auction_products.all');
});
