<?php

/*
|--------------------------------------------------------------------------
| Affiliate Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Admin

use App\Http\Controllers\ClubPointController;

Route::group(['prefix' =>'admin', 'middleware' => ['auth', 'admin']], function(){
    Route::controller(ClubPointController::class)->group(function () {
        Route::get('club-points/configuration', 'configure_index')->name('club_points.configs');
        Route::get('club-points/index', 'index')->name('club_points.index');
        Route::get('set-club-points', 'set_point')->name('set_product_points');
        Route::post('set-club-points/store', 'set_products_point')->name('set_products_point.store');
        Route::post('set-club-points-for-all_products/store', 'set_all_products_point')->name('set_all_products_point.store');
        Route::get('set-club-points/{id}', 'set_point_edit')->name('product_club_point.edit');
        Route::get('club-point-details/{id}', 'club_point_detail')->name('club_point.details');
        Route::post('set-club-points/update/{id}', 'update_product_point')->name('product_point.update');
        Route::post('club-point-convert-rate/store', 'convert_rate_store')->name('point_convert_rate_store');
    });
});

//FrontEnd
Route::group(['middleware' => ['user', 'verified']], function(){
    Route::controller(ClubPointController::class)->group(function () {
        Route::get('earning-points', 'userpoint_index')->name('earnng_point_for_user');
        Route::post('convert-point-into-wallet', 'convert_point_into_wallet')->name('convert_point_into_wallet');
    });
});
