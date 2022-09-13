<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PurchaseHistoryItemsCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {

                $refund_section = false;
                $refund_button = false;
                $refund_label = "";
                $refund_request_status = 99;
                if (addon_is_activated('refund_request')) {
                    $refund_section = true;
                    $no_of_max_day = get_setting('refund_request_time');
                    $last_refund_date = $data->created_at->addDays($no_of_max_day);
                    $today_date = \Carbon\Carbon::now();
                    if ($data->product != null &&
                        $data->product->refundable != 0 &&
                        $data->refund_request == null &&
                        $today_date <= $last_refund_date &&
                        $data->payment_status == 'paid' &&
                        $data->delivery_status == 'delivered') {
                        $refund_button = true;
                    } else if ($data->refund_request != null && $data->refund_request->refund_status == 0) {
                        $refund_label = "Pending";
                        $refund_request_status = $data->refund_request->refund_status;
                    } else if ($data->refund_request != null && $data->refund_request->refund_status == 2) {
                        $refund_label = "Rejected";
                        $refund_request_status = $data->refund_request->refund_status;
                    } else if ($data->refund_request != null && $data->refund_request->refund_status == 1) {
                        $refund_label = "Approved";
                        $refund_request_status = $data->refund_request->refund_status;
                    } else if ($data->product->refundable != 0) {
                        $refund_label = "N/A";
                    } else {
                        $refund_label = "Non-refundable";
                    }
                }
                return [
                    'id' => $data->id,
                    'product_id' => $data->product->id,
                    'product_name' => $data->product->name,
                    'variation' => $data->variation,
                    'price' => format_price($data->price),
                    'tax' => format_price($data->tax),
                    'shipping_cost' => format_price($data->shipping_cost),
                    'coupon_discount' => format_price($data->coupon_discount),
                    'quantity' => (int)$data->quantity,
                    'payment_status' => $data->payment_status,
                    'payment_status_string' => ucwords(str_replace('_', ' ', $data->payment_status)),
                    'delivery_status' => $data->delivery_status,
                    'delivery_status_string' => $data->delivery_status == 'pending' ? "Order Placed" : ucwords(str_replace('_', ' ', $data->delivery_status)),
                    'refund_section' => $refund_section,
                    'refund_button' => $refund_button,
                    'refund_label' => $refund_label,
                    'refund_request_status' => $refund_request_status,
                ];
            })
        ];
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200
        ];
    }
}
