<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RefundRequestCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                $refund_label = '';
                if($data->refund_status == 1) {
                    $refund_label = 'Approved';
                } elseif($data->refund_status == 2) {
                    $refund_label = 'Rejected';
                }else {
                    $refund_label = 'PENDING';
                }

                return [
                    'id' => (int)$data->id,
                    'user_id' => (int)$data->user_id,
                    'order_code' => $data->order == null ? "" : $data->order->code,
                    'product_name' => $data->orderDetail != null && $data->orderDetail->product != null ? $data->orderDetail->product->getTranslation('name', 'en') : "",
                    'product_price' => $data->orderDetail != null ? single_price($data->orderDetail->price) : "",
                    'refund_status' => (int) $data->refund_status,
                    'refund_label' => $refund_label,
                    'seller_approval' => $data->seller_approval,
                    'reject_reason' => $data->reject_reason,
                    'reason' => $data->reason,
                    'date' => date('d-m-Y', strtotime($data->created_at)),
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
