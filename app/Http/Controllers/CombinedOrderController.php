<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\CombinedOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CombinedOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $combined_orders = CombinedOrder::where('user_id', Auth::user()->id)->orderBy('id', 'desc')->paginate(9);
        return view('frontend.user.combined_order', compact('combined_orders'));
    }

}
