<?php

namespace App\Http\Controllers\Api\V2\Seller;

use Illuminate\Http\Request;
use App\Models\SellerPackagePayment;
use App\Models\SellerPackage;

class SellerPackagePaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function offline_payment_request(){
        $package_payment_requests = SellerPackagePayment::where('offline_payment',1)->orderBy('id', 'desc')->paginate(10);
        return view('manual_payment_methods.seller_package_payment_request', compact('package_payment_requests'));
    }

    public function offline_payment_approval(Request $request)
    {
        $package_payment    = SellerPackagePayment::findOrFail($request->id);
        $package_details    = SellerPackage::findOrFail($package_payment->seller_package_id);
        $package_payment->approval      = $request->status;
        if($package_payment->save()){
            $seller                                 = $package_payment->user->seller;
            $seller->seller_package_id              = $package_payment->seller_package_id;
            $seller->invalid_at                     = date('Y-m-d', strtotime( $seller->invalid_at. ' +'. $package_details->duration .'days'));
            if($seller->save()){
                return 1;
            }
        }
        return 0;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
