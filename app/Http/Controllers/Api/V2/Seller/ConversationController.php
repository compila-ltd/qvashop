<?php

namespace App\Http\Controllers\Api\V2\Seller;

use App\Http\Resources\V2\MessageCollection;
use App\Http\Resources\V2\Seller\ConversationCollection;
use App\Http\Resources\V2\Seller\ConversationResource;
use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\BusinessSetting;
use App\Models\Message;
use Auth;
use App\Models\Product;
use Mail;
use App\Mail\ConversationMailManager;
use DB;

class ConversationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (BusinessSetting::where('type', 'conversation_system')->first()->value == 1) {

            //SELECT sender_id, receiver_id, title, MAX(created_at) AS max_created_at FROM `conversations` WHERE receiver_id = 3 GROUP BY sender_id order by max_created_at desc;
            // $conversations = Conversation::select('sender_id', 'receiver_id', 'title', DB::raw("MAX(created_at) as max_created_at"))
            //     ->where('receiver_id', '=', auth()->user()->id)
            //     ->orderBy('max_created_at', 'DESC')
            //     ->groupBy('sender_id')
            //     ->get();
            $conversations = Conversation::where('receiver_id', auth()->user()->id)
                ->orderBy('created_at', 'desc')
                ->get();
            return  ConversationResource::collection($conversations);
        } else {
            return $this->failed(translate('Conversation is disabled at this moment'));
        }
    }


    public function send_message_to_customer(Request $requrest)
    {
        $message = new Message();
        $conversation = Conversation::find($requrest->conversation_id)->where("receiver_id",auth()->user()->id)->first();

        if($conversation){
        $message->conversation_id = $requrest->conversation_id;
        $message->user_id = auth()->user()->id;
        $message->message = $requrest->message;
        $message->save();

        return $this->success(translate('Message send successfully'));
        }else{
            return $this->failed(translate('You can not send this message.'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $conversation = Conversation::findOrFail(decrypt($id));
        if ($conversation->sender_id == auth()->user()->id) {
            $conversation->sender_viewed = 1;
        } elseif ($conversation->receiver_id == auth()->user()->id) {
            $conversation->receiver_viewed = 1;
        }
        $conversation->save();

        return new ConversationCollection($conversation);
    }

    public function showMessages($id)
    {
        $conversation = Conversation::findOrFail($id);
        if ($conversation->receiver_id == auth()->user()->id) {
            $messages = Message::where("conversation_id",$id)->orderBy('created_at', 'DESC')->get();

            return new MessageCollection($messages);
        } else {

            return $this->failed(translate('You can not see this message.'));

        }
        
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $conversation = Conversation::findOrFail(decrypt($id));
        foreach ($conversation->messages as $key => $message) {
            $message->delete();
        }
        if (Conversation::destroy(decrypt($id))) {
            flash(translate('Conversation has been deleted successfully'))->success();
            return back();
        }
    }
}
