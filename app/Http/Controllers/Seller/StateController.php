<?php

namespace App\Http\Controllers\Seller;

use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\State;
use App\Models\ShopState;
use App\Models\Country;

use Illuminate\Support\Facades\Auth;

class StateController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['permission:manage_shipping_states'])->only('index', 'edit');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $shop = Auth::user()->shop;

        $sort_country = $request->sort_country;
        $sort_state = $request->sort_state;

        $state_queries = State::query();
        
        if ($request->sort_state) {
            $state_queries->where('name', 'like', "%$sort_state%");
        }
        if ($request->sort_country) {
            $state_queries->where('country_id', $request->sort_country);
        }else{
            $state_queries->where('country_id', 1);
        }

        $states = $state_queries->orderBy('status', 'desc')->paginate(16);

        // Para cada estado, verifica si existe una entrada en shop_states
        foreach ($states as $state) {
            // Busca la entrada en shop_states para el estado y la tienda específicos
            $shop_state = ShopState::where('state_id', $state->id)
                ->where('shop_id', $shop->id)
                ->first();

            // Si existe la entrada en shop_states, actualiza el estado
            if ($shop_state) {
                $state->status = $shop_state->status;
            } else {
                // Si no existe la entrada en shop_states, establece el estado como inactivo (0)
                $state->status = 0;
            }
        }        

        //dd($states);

        return view('seller.states.index', compact('states', 'sort_country', 'sort_state'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $state = new State;
        $state->name = $request->name;
        $state->country_id = $request->country_id;
        $state->save();

        return back()->with('success', translate('State has been inserted successfully'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $state  = State::findOrFail($id);
        $countries = Country::where('status', 1)->get();

        return view('backend.setup_configurations.states.edit', compact('countries', 'state'));
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
        $state = State::findOrFail($id);
        $state->name        = $request->name;
        $state->country_id  = $request->country_id;
        $state->save();

        return back()->with('success', 'State has been updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        State::destroy($id);
        return redirect()->route('states.index')->with('success', 'State has been deleted successfully');
    }

    public function updateStatus(Request $request)
    {
        $shop = Auth::user()->shop;
        $state = ShopState::where('shop_id', $shop->id)->where('state_id', $request->id)->first();
        //dd($state);

        if($state){
            $state->status = $request->status;

            $state->save();
        }else{
            $new_state = new ShopState;
            $new_state->shop_id = $shop->id;
            $new_state->state_id = $request->id;

            $new_state->save();
        }

/*
        if ($state->status) {
            foreach ($state->cities as $city) {
                $city->status = 1;
                $city->save();
            }
        }
*/
        return 1;
    }
}
