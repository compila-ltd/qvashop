<?php

namespace App\Http\Resources\V2\Seller;

use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{

    public function toArray($request)
    {
        //dd(uploaded_asset($this->sender->avatar_original));
        $image="";
        $name="";
        if (auth()->user()->id == $this->sender_id) {
            $image = uploaded_asset($this->receiver->avatar_original);
            $name = $this->receiver->name;
        } else {
            $image = uploaded_asset($this->sender->avatar_original);
            $name = $this->sender->name;
        }
        return [
            'id'    => $this->id,
            'image' => $image,
            'name'  => $name,
            'title' => $this->title,
        ];
    }
}
