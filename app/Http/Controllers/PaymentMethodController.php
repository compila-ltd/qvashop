<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Models\Currency;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class PaymentMethodController extends Controller
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

    public function payment_method()
    {
        $payment_methods = PaymentMethod::all();
        return view('backend.setup_configurations.payment_method.index', compact('payment_methods'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $currencies = Currency::where('status', '1')->get();
        //dd($currencies);
        return view('backend.setup_configurations.payment_method.create', compact('currencies'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $status = 0;
        $automatic = 0;

        if (isset($request->status)) 
            $status = 1;
        
        unset($request->status);

        if (isset($request->automatic)) 
            $automatic = 1;
        
        unset($request->automatic);

        $payment_method = new PaymentMethod();
        $payment_method->name = $request->name;
        $payment_method->short_name = $request->short_name;
        $payment_method->photo = $request->photo;
        $payment_method->currency_id = $request->currency_id;
        $payment_method->exchange_rate = $request->exchange_rate;
        $payment_method->status = $status;
        $payment_method->automatic = $automatic;

        if ($payment_method->save())
            return redirect()->route('payment_method.index')->with('success', translate('Payment Method added successfully'));

        return redirect()->route('payment_method.index')->with('error', translate('Something went wrong'));

        //dd($request);

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
        //dd($request);
        $payment_method = PaymentMethod::findOrFail($id);
        $currencies = Currency::where('status', '1')->get();
        
        return view('backend.setup_configurations.payment_method.edit', compact('payment_method', 'currencies'));
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
        //dd($request);
        $status = 0;
        $automatic = 0;

        if (isset($request->status)) 
            $status = 1;
        
        unset($request->status);

        if (isset($request->automatic)) 
            $status = 1;
        
        unset($request->automatic);

        $payment_method = PaymentMethod::find($request->id);

        //dd($payment_method);

        $payment_method->name = $request->name;
        $payment_method->short_name = $request->short_name;
        $payment_method->photo = $request->photo;
        $payment_method->currency_id = $request->currency_id;
        $payment_method->exchange_rate = $request->exchange_rate;
        $payment_method->status = $status;
        $payment_method->automatic = $automatic;

        if ($payment_method->save())
            return redirect()->route('payment_method.index')->with('success', translate('Payment method successfully updated'));

        return redirect()->route('payment_method.index')->with('error', translate('Something went wrong'));
    }

    public function activation_payment_method(Request $request)
    {
        //dd($request);
        $payment_method = PaymentMethod::findOrFail($request->id);

        $payment_method->status = $request->status;

        if ($payment_method->save()) {
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
            return 1;
        }
        return 0;
    } 

    public function automatic_payment_method(Request $request)
    {
        //dd($request);
        $payment_method = PaymentMethod::findOrFail($request->id);

        $payment_method->automatic = $request->status;

        if ($payment_method->save()) {
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
            return 1;
        }
        return 0;
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
