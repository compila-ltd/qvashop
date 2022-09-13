<?php

namespace App\Http\Controllers\Api\V2\Seller;

use App\Http\Requests\CouponRequest;
use App\Http\Resources\V2\Seller\CouponResource;
use App\Http\Resources\V2\Seller\ProductCollection;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\Product;
use Auth;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $coupons = Coupon::where('user_id', auth()->user()->id)->orderBy('id','desc')->get();
        return CouponResource::collection($coupons);
    }
    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CouponRequest $request)
    {
        $user_id = auth()->user()->id;
        Coupon::create($request->validated() + [
            'user_id' => $user_id,
        ]);

        return $this->success(translate('Coupon has been saved successfully'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $coupon = Coupon::where('id', $id)->where('user_id', auth()->user()->id)->first();
        // dd($coupon);
        return new CouponResource($coupon);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CouponRequest $request, Coupon $coupon)
    {
        $coupon->update($request->validated());

        return $this->success(translate('Coupon has been updated successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Coupon::where('id', '=', $id)->where('user_id', auth()->user()->id)->delete();
       
        return $this->success(translate('Coupon has been deleted successfully'));
    }

    public function coupon_for_product(Request $request)
    {
        
        if($request->coupon_type == "product_base") {
            
            $products = Product::where('name','LIKE',"%".$request->name."%")->where('user_id', auth()->user()->id)->paginate(10);
            // $products = filter_products(Product::where('user_id', auth()->user()->id))->get();
            return new ProductCollection($products);
        }
    }
}
