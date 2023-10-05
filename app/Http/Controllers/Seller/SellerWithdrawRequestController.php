<?php

namespace App\Http\Controllers\Seller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SellerWithdrawRequest;
use App\Models\Shop;

class SellerWithdrawRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $seller_withdraw_requests = SellerWithdrawRequest::where('user_id', Auth::user()->id)->latest()->paginate(9);
        return view('seller.money_withdraw_requests.index', compact('seller_withdraw_requests'));
    }

    /**
     * Store a withdraw request
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $seller_withdraw_request = new SellerWithdrawRequest;
        $seller_withdraw_request->user_id = Auth::user()->id;
        $seller_withdraw_request->amount = $request->amount;
        $seller_withdraw_request->message = $request->message;
        $seller_withdraw_request->status = '0';
        $seller_withdraw_request->viewed = '0';

        $amount_request = SellerWithdrawRequest::where('user_id', $seller_withdraw_request->user_id)->where('status', '0')->sum('amount');
        $amount_to_pay = Shop::where('user_id', $seller_withdraw_request->user_id)->value('admin_to_pay');

        if(($amount_request + $seller_withdraw_request->amount) > $amount_to_pay)
            return back()->with('danger', translate('The total amount of requests cannot be greater than the pending balance'));

        if ($seller_withdraw_request->save()) {
            return redirect()->route('seller.money_withdraw_requests.index')->with('success', translate('Request has been sent successfully'));
        } else {
            return back()->with('error', translate('Something went wrong'));
        }
    }
}
