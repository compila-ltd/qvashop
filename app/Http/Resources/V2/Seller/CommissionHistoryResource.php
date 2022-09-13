<?php

namespace App\Http\Resources\V2\Seller;

use Illuminate\Http\Resources\Json\JsonResource;

class CommissionHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $order_code = 'Order Deleted';
        if(isset($this->order)){
            $order_code = $this->order->code;
        }
        return [
            'id' => $this->id,
            'order_code' => $order_code,
            'admin_commission' => $this->admin_commission,
            'seller_earning' => format_price($this->seller_earning),
            'created_at' => date('d-m-Y', strtotime($this->created_at)),
        ];
    }
}
