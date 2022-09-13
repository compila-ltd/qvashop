<?php

namespace App\Http\Controllers\Seller;

use App\Models\Payment;
use Auth;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $payments = Payment::where('seller_id', Auth::user()->id)->paginate(9);
        return view('seller.payment_history', compact('payments'));
    }
}
