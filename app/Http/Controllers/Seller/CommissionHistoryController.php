<?php

namespace App\Http\Controllers\Seller;

use Illuminate\Http\Request;
use App\Models\CommissionHistory;
use Auth;

class CommissionHistoryController extends Controller
{
    public function index(Request $request) {
        $seller_id = null;
        $date_range = null;
        
        $commission_history = CommissionHistory::where('seller_id', Auth::user()->id)->orderBy('created_at', 'desc');
        
        if ($request->date_range) {
            $date_range = $request->date_range;
            $date_range1 = explode(" / ", $request->date_range);
            $commission_history = $commission_history->where('created_at', '>=', $date_range1[0]);
            $commission_history = $commission_history->where('created_at', '<=', $date_range1[1]);
        }
        
        $commission_history = $commission_history->paginate(10);
        return view('seller.commission_history.index', compact('commission_history', 'seller_id', 'date_range'));
    }
}
