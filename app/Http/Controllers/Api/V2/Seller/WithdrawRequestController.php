<?php

namespace App\Http\Controllers\Api\V2\Seller;

use App\Http\Resources\V2\Seller\SellerWithdrawResource;
use Illuminate\Http\Request;
use App\Models\SellerWithdrawRequest;
use Auth;
use Response;

class WithdrawRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $seller_withdraw_requests = SellerWithdrawRequest::where('user_id', auth()->user()->id)->latest()->paginate(10);
        return SellerWithdrawResource::collection($seller_withdraw_requests);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if (auth()->user()->shop->admin_to_pay > 5) {
            if ($request->amount >= get_setting('minimum_seller_amount_withdraw') && $request->amount <= Auth::user()->shop->admin_to_pay) {
                $seller_withdraw_request = new SellerWithdrawRequest;
                $seller_withdraw_request->user_id = auth()->user()->id;
                $seller_withdraw_request->amount = $request->amount;
                $seller_withdraw_request->message = $request->message;
                $seller_withdraw_request->status = '0';
                $seller_withdraw_request->viewed = '0';

                $seller_withdraw_request->save();

                return $this->success(translate('Request has been sent successfully'));
            } else {
                return $this->failed(translate('Invalid amount'));
            }
        } else {
            return $this->failed(translate('You do not have enough balance to send withdraw request'));
        }
    }
}
