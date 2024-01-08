<?php

namespace App\Http\Controllers\Seller;

use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\ShopState;
use App\Models\City;
use App\Models\ShopCity;
use App\Models\CityTranslation;
use App\Models\State;

use Illuminate\Support\Facades\Auth;

class CityController extends Controller
{
    public function __construct()
    {
        // Staff Permission Check
        //$this->middleware(['permission:manage_shipping_cities'])->only('index', 'create', 'destroy');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $shop = Auth::user()->shop;

        $shop_active_states = ShopState::where('shop_id', $shop->id)
        ->where('status', 1)
        ->get();

        //dd($shop_active_states);

        $cities = City::whereIn('state_id', $shop_active_states->pluck('state_id'))->paginate(16);

        foreach ($cities as $city) {
            // Busca la entrada en ShopCity para la tienda y la ciudad específicos
            $shop_city = ShopCity::where('city_id', $city->id)
                ->where('shop_id', $shop->id)
                ->first();

            // Si existe la entrada en ShopCity, actualiza la información
            if ($shop_city) {
                $city->cost = $shop_city->cost;
                $city->status = $shop_city->status;
            } else {
                // Si no existe la entrada en ShopCity, establece la información por defecto
                $city->cost = 0; 
                $city->status = 0; 
            }
        }

        //dd($cities);

        return view('seller.cities.index', compact('cities'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $city = new City;

        $city->name = $request->name;
        $city->cost = $request->cost;
        $city->state_id = $request->state_id;
        $city->save();

        return back()->with('success', translate('City has been inserted successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        //dd($id);
        $shop = Auth::user()->shop;
        $city = ShopCity::where('shop_id', $shop->id)->where('city_id', $id)->first();

        if($city){
            $city_temp = City::where('id', $city->city_id)->first();
            $city->name = $city_temp->name;
        }else{
            $city = City::where('id', $id)->first();
            $city->cost = 0.00;
        }

        return view('seller.cities.edit', compact('city'));
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
        $shop = Auth::user()->shop;
        $city = ShopCity::where('shop_id', $shop->id)->where('city_id', $id)->first();

        if($city){
            $city->cost = $request->cost;

            $city->save();
        }else{
            $new_city = new ShopCity;
            $new_city->shop_id = $shop->id;
            $new_city->city_id = $id;
            $new_city->cost = $request->cost;

            $new_city->save();
        }

        return back()->with('success', 'Se ha actualizado el costo de envío satisfactoriamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $city = City::findOrFail($id);
        $city->city_translations()->delete();
        City::destroy($id);

        return redirect()->route('cities.index')->with('success', translate('City has been deleted successfully'));
    }

    public function updateStatus(Request $request)
    {
        //dd($request);

        $shop = Auth::user()->shop;
        $city = ShopCity::where('shop_id', $shop->id)->where('city_id', $request->id)->first();
        //dd($state);

        if($city){
            $city->status = $request->status;

            $city->save();
        }else{
            $new_city = new ShopCity;
            $new_city->shop_id = $shop->id;
            $new_city->city_id = $request->id;
            $new_city->cost = 0.00;
            $new_city->status = $request->status;

            $new_city->save();
        }

        return 1;
    }
}
