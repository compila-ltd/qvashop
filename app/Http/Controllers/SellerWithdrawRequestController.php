<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SellerWithdrawRequest;

class SellerWithdrawRequestController extends Controller
{
    // Staff Permission Check
    public function __construct()
    {
        $this->middleware(['permission:view_seller_payout_requests'])->only('index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $seller_withdraw_requests = SellerWithdrawRequest::latest()->paginate(15);
        return view('backend.sellers.seller_withdraw_requests.index', compact('seller_withdraw_requests'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $seller_withdraw_request = new SellerWithdrawRequest;
        $seller_withdraw_request->user_id = Auth::user()->shop->id;
        $seller_withdraw_request->amount = $request->amount;
        $seller_withdraw_request->message = $request->message;
        $seller_withdraw_request->status = '0';
        $seller_withdraw_request->viewed = '0';

        if ($seller_withdraw_request->save()) {
            return redirect()->route('withdraw_requests.index')->with('success', translate('Request has been sent successfully'));
        }

        return back()->with('danger', translate('Something went wrong'));
    }

    // Payment Modal
    public function payment_modal(Request $request)
    {
        $user = User::findOrFail($request->id);
        $seller_withdraw_request = SellerWithdrawRequest::where('id', $request->seller_withdraw_request_id)->first();
        return view('backend.sellers.seller_withdraw_requests.payment_modal', compact('user', 'seller_withdraw_request'));
    }

    // message Modal
    public function message_modal(Request $request)
    {
        $seller_withdraw_request = SellerWithdrawRequest::findOrFail($request->id);
        if (Auth::user()->user_type == 'seller')
            return view('frontend.partials.withdraw_message_modal', compact('seller_withdraw_request'));
        elseif (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff')
            return view('backend.sellers.seller_withdraw_requests.withdraw_message_modal', compact('seller_withdraw_request'));
    }
}
