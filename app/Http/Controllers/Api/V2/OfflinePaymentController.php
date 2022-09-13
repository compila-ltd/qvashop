<?php

namespace App\Http\Controllers\Api\V2;

use App\Models\Order;
use Illuminate\Http\Request;


class OfflinePaymentController extends Controller
{
    public function submit(Request $request)
    {
        $order = Order::find($request->order_id);

        if($request->name != null && $request->amount != null && $request->trx_id != null){
            $data['name']   = $request->name;
            $data['amount'] = $request->amount;
            $data['trx_id'] = $request->trx_id;
            $data['photo']  = $request->photo;
        }
        else {
            return response()->json([
                'result' => false,
                'message' => translate('Something went wrong')
            ]);
        }

        $order->manual_payment_data = json_encode($data);
        $order->payment_type = $request->payment_option;
        $order->payment_status = 'Submitted';
        $order->manual_payment = 1;

        $order->save();
        return response()->json([
            'result' => true,
            'message' => translate('Submitted Successfully')
        ]);
    }
}
