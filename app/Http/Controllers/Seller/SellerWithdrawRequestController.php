<?php

namespace App\Http\Controllers\Seller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SellerWithdrawRequest;

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

        if ($seller_withdraw_request->save()) {
            return redirect()->route('seller.money_withdraw_requests.index')->with('success', translate('Request has been sent successfully'));
        } else {
            return back()->with('error', translate('Something went wrong'));
        }
    }
}
