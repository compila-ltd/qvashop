<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\WalletCollection;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function balance()
    {
        $user = User::find(auth()->user()->id);
        $latest = Wallet::where('user_id', auth()->user()->id)->latest()->first();
        return response()->json([
            'balance' => format_price($user->balance),
            'last_recharged' => $latest == null ? "Not Available" : $latest->created_at->diffForHumans(),
        ]);
    }

    public function walletRechargeHistory()
    {
        return new WalletCollection(Wallet::where('user_id', auth()->user()->id)->latest()->paginate(10));
    }

    public function processPayment(Request $request)
    {
        $order = new OrderController;
        $user = User::find($request->user_id);

        if ($user->balance >= $request->amount) {
            
            $response =  $order->store($request, true);            
            $decoded_response = $response->original;
            if ($decoded_response['result'] == true) { // only decrease user balance with a success
                $user->balance -= $request->amount;
                $user->save();            
            }

            return $response;

        } else {
            return response()->json([
                'result' => false,
                'combined_order_id' => 0,
                'message' => translate('Insufficient wallet balance')
            ]);
        }
    }

    public function offline_recharge(Request $request)
    {
        $wallet = new Wallet;
        $wallet->user_id = auth()->user()->id;
        $wallet->amount = $request->amount;
        $wallet->payment_method = $request->payment_option;
        $wallet->payment_details = $request->trx_id;
        $wallet->approval = 0;
        $wallet->offline_payment = 1;
        $wallet->reciept = $request->photo;
        $wallet->save();
       // flash(translate('Offline Recharge has been done. Please wait for response.'))->success();
        //return redirect()->route('wallet.index');
        return response()->json([
            'result' => true,
            'message' => translate('Offline Recharge has been done. Please wait for response.')
        ]);
    }

}
