<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Notifications\EmailVerificationNotification;

class ShopController extends Controller
{
    public function __construct()
    {
        $this->middleware('user', ['only' => ['index']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shop = Auth::user()->shop;

        return view('seller.shop', compact('shop'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Auth::check()) {
            if ((Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'customer'))
                return back()->with('danger', translate('Admin or Customer can not be a seller'));

            if (Auth::user()->user_type == 'seller')
                return back()->with('danger', translate('This user already a seller'));
        }

        return view('frontend.seller_form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = null;
        
        if (!Auth::check()) {
            
            if (User::where('email', $request->email)->first() != null)
            {
                return back()->with('danger', translate('Email already exists!'));
            }
                
            if ($request->password == $request->password_confirmation) {
                $user = new User;
                $user->name = $request->name;
                $user->email = $request->email;
                $user->user_type = "seller";
                $user->password = Hash::make($request->password);
                $user->save();
            } else {
                return back()->with('danger', translate('Sorry! Password did not match.'));
            }
        } else {
            $user = User::find(Auth::user()->id);
            if ($user->customer != null) {
                $user->customer->delete();
            }
            $user->user_type = "seller";
            $user->save();
        }

        if (Shop::where('user_id', $user->id)->first() == null) {

            $shop = new Shop;
            $shop->user_id = $user->id;
            $shop->name = $request->name;
            $shop->address = $request->address;
            $shop->slug = preg_replace('/\s+/', '-', $request->name);

            if ($shop->save()) {
                auth()->login($user, false);
                if (BusinessSetting::where('type', 'email_verification')->first()->value != 1) {
                    $user->email_verified_at = date('Y-m-d H:m:s');
                    $user->save();
                } else {
                    $user->notify(new EmailVerificationNotification());
                }
                return redirect()->route('shops.index')->with('success', translate('Your Shop has been created successfully!'));
            } else {
                $user->user_type == 'customer';
                $user->save();
            }
        }

        return back()->with('danger', translate('Sorry! Something went wrong.'));
    }
}
