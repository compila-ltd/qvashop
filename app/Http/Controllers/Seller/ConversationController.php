<?php

namespace App\Http\Controllers\Seller;

use App\Models\Message;
use App\Mail\ConversationMailManager;
use App\Models\Conversation;
use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use Illuminate\Support\Facades\Auth;
use Mail;

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
            $conversations = Conversation::where('sender_id', Auth::user()->id)->orWhere('receiver_id', Auth::user()->id)->orderBy('updated_at', 'desc')->paginate(5);
            return view('seller.conversations.index', compact('conversations'));
        }

        return back()->with('warning', translate('Conversation is disabled at this moment'));
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
        if ($conversation->sender_id == Auth::user()->id) {
            $conversation->sender_viewed = 1;
        } elseif ($conversation->receiver_id == Auth::user()->id) {
            $conversation->receiver_viewed = 1;
        }
        $conversation->save();

        return view('seller.conversations.show', compact('conversation'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function refresh(Request $request)
    {
        $conversation = Conversation::findOrFail(decrypt($request->id));
        /*
        if ($conversation->sender_id == Auth::user()->id) {
            $conversation->sender_viewed = 1;
            $conversation->save();
        } else {
            $conversation->receiver_viewed = 1;
            $conversation->save();
        }
        */
        return view('frontend.partials.messages', compact('conversation'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function message_store(Request $request)
    {
        $message = new Message;
        $message->conversation_id = $request->conversation_id;
        $message->user_id = Auth::user()->id;
        $message->message = $request->message;
        $message->save();
        $conversation = $message->conversation;

        $conversation->sender_viewed = "0";
        $conversation->receiver_viewed = "1";

        $user_type = $conversation->sender->user_type;

        if ($conversation->save()) {
            $this->send_message_to_client($conversation, $message, $user_type);
        }

        return back();
    }

    public function send_message_to_client($conversation, $message, $user_type)
    {
        $array['view'] = 'emails.conversation';
        $array['subject'] = 'Sobre el producto: ' . $conversation->title;
        $array['from'] = env('MAIL_FROM_ADDRESS');
        $array['content'] = '¡Hola! Tiene un mensaje de la tienda: ' . Auth::user()->name . '.';
        $array['sender'] = Auth::user()->name;

        $array['link'] = route('conversations.show', encrypt($conversation->id));

        $array['details'] = $message->message;

        try {
            Mail::to($conversation->sender->email)->queue(new ConversationMailManager($array));
        } catch (\Exception $e) {
        }
    }
}
