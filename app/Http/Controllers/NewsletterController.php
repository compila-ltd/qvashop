<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\EmailManager;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NewsletterController extends Controller
{
    public function __construct()
    {
        // Staff Permission Check
        $this->middleware(['permission:send_newsletter'])->only('index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::all();
        $subscribers = Subscriber::all();
        return view('backend.marketing.newsletters.index', compact('users', 'subscribers'));
    }

    /*
    * Send newsletter to users and subscribers
    */
    public function send(Request $request)
    {
        if (env('MAIL_USERNAME') != null) {

            //sends newsletter to selected users
            if ($request->has('user_emails')) {
                foreach ($request->user_emails as $email) {
                    $array['view'] = 'emails.newsletter';
                    $array['subject'] = $request->subject;
                    $array['from'] = env('MAIL_FROM_ADDRESS');
                    $array['content'] = $request->content;

                    try {
                        Mail::to($email)->queue(new EmailManager($array));
                    } catch (\Exception $e) {
                    }
                }
            }

            //sends newsletter to subscribers
            if ($request->has('subscriber_emails')) {
                foreach ($request->subscriber_emails as $email) {
                    $array['view'] = 'emails.newsletter';
                    $array['subject'] = $request->subject;
                    $array['from'] = env('MAIL_FROM_ADDRESS');
                    $array['content'] = $request->content;

                    try {
                        Mail::to($email)->queue(new EmailManager($array));
                    } catch (\Exception $e) {
                    }
                }
            }
            
        } else {
            flash(translate('Please configure SMTP first'))->error();
            return back();
        }

        return redirect()->route('admin.dashboard')->with('success', translate('Newsletter has been send'));
    }

    /*
     * Send newsletter to test email
     */
    public function testEmail(Request $request)
    {
        $array['view'] = 'emails.newsletter';
        $array['subject'] = "SMTP Test";
        $array['from'] = env('MAIL_FROM_ADDRESS');
        $array['content'] = "This is a test email.";

        try {
            Mail::to($request->email)->queue(new EmailManager($array));
        } catch (\Exception $e) {
            dd($e);
        }

        flash(translate('An email has been sent.'))->success();
        return back();
    }
}
