<?php

namespace App\Http\Resources\V2\Seller;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ConversationMessageCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $image = null;
        $is_seller_message = false;
        
        if($this->user != null){
            $image = uploaded_asset($this->user->avatar_original);
        }
        if($this->user->id == auth()->user->id) {
            $is_seller_message = true;
        }
        return [
            'image'             =>  $image,
            'id'                =>  $this->user->id,
            'name'              =>  $this->user->name,
            'message'           =>  $this->message,
            'is_seller_message' =>  $is_seller_message,
            'created_at'        =>  $this->created_at,
        ];
    }
}
