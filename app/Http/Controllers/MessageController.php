<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\ConversationMailManager;
use App\Models\Message;
use App\Models\Product;
use App\Models\Conversation;
use Mail;
use Auth;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $message = new Message;
        $message->conversation_id = $request->conversation_id;
        $message->user_id = Auth::user()->id;
        $message->message = $request->message;

        $conversation = $message->conversation;

        $send_message_to = '';

        if ($conversation->sender_id == Auth::user()->id) {
            $conversation->receiver_viewed ="0";
            $send_message_to = 'shop';
        }
        elseif($conversation->receiver_id == Auth::user()->id) {
            $conversation->sender_viewed ="0";
            $send_message_to = 'client';
        }
        $conversation->save();

        $user_type = $conversation->receiver->user_type;

        if ($message->save()) {
            $this->send_message_to_seller($conversation, $message, $user_type, $send_message_to);
        }
        
        return back();
    }

    public function send_message_to_seller($conversation, $message, $user_type, $send_message_to)
    {
        $array['view'] = 'emails.conversation';
        $array['subject'] = 'Sobre el producto: ' . $conversation->title;
        $array['from'] = env('MAIL_FROM_ADDRESS');
        $array['content'] = '¡Hola! Tiene un mensaje del usuario: ' . Auth::user()->name . '.';
        $array['sender'] = Auth::user()->name;

        if($send_message_to == 'shop'){
            if ($user_type == 'admin') {
                $array['link'] = route('conversations.admin_show', encrypt($conversation->id));
            } else {
                $array['link'] = route('seller.conversations.show', encrypt($conversation->id));
            }
        }
        else{
            $array['link'] = route('conversations.show', encrypt($conversation->id));
        }
        

        $array['details'] = $message->message;

        try {
            if($send_message_to == 'shop')
                Mail::to($conversation->receiver->email)->queue(new ConversationMailManager($array));
            else
                Mail::to($conversation->sender->email)->queue(new ConversationMailManager($array));
        } catch (\Exception $e) {
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
