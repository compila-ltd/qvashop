<?php

namespace App\Http\Controllers;

use App\Http\Requests\CouponRequest;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\User;

class CouponController extends Controller
{
    public function __construct() {
        // Staff Permission Check
        $this->middleware(['permission:view_all_coupons'])->only('index');
        $this->middleware(['permission:add_coupon'])->only('create');
        $this->middleware(['permission:edit_coupon'])->only('edit');
        $this->middleware(['permission:delete_coupon'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $coupons = Coupon::where('user_id', User::where('user_type', 'admin')->first()->id)->orderBy('id','desc')->get();
        return view('backend.marketing.coupons.index', compact('coupons'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.marketing.coupons.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CouponRequest $request)
    {   
        $user_id = User::where('user_type', 'admin')->first()->id;
        Coupon::create($request->validated() + [
            'user_id' => $user_id,
        ]);
        flash(translate('Coupon has been saved successfully'))->success();
        return redirect()->route('coupon.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $coupon = Coupon::findOrFail(decrypt($id));
        return view('backend.marketing.coupons.edit', compact('coupon'));
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
        flash(translate('Coupon has been updated successfully'))->success();
        return redirect()->route('coupon.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Coupon::destroy($id);
        flash(translate('Coupon has been deleted successfully'))->success();
        return redirect()->route('coupon.index');
    }

    public function get_coupon_form(Request $request)
    {
        if($request->coupon_type == "product_base") {
            $admin_id = \App\Models\User::where('user_type', 'admin')->first()->id;
            $products = filter_products(\App\Models\Product::where('user_id', $admin_id))->get();
            return view('partials.coupons.product_base_coupon', compact('products'));
        }
        elseif($request->coupon_type == "cart_base"){
            return view('partials.coupons.cart_base_coupon');
        }
    }

    public function get_coupon_form_edit(Request $request)
    {
        if($request->coupon_type == "product_base") {
            $coupon = Coupon::findOrFail($request->id);
            $admin_id = \App\Models\User::where('user_type', 'admin')->first()->id;
            $products = filter_products(\App\Models\Product::where('user_id', $admin_id))->get();
            return view('partials.coupons.product_base_coupon_edit',compact('coupon', 'products'));
        }
        elseif($request->coupon_type == "cart_base"){
            $coupon = Coupon::findOrFail($request->id);
            return view('partials.coupons.cart_base_coupon_edit',compact('coupon'));
        }
    }

}
