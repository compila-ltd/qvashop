<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NegotiableTransportation;
use App\Models\User;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Address;

class NegotiableTransportationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user_search_id = null;

        $negotiable_transportations = NegotiableTransportation::orderBy('id', 'desc');

        $users = User::orderBy('email', 'asc')->get();

        if ($request->user_search_id != null) {
            $user_search_id = $request->user_search_id;
            $negotiable_transportations = $negotiable_transportations->where('user_id', $user_search_id);
        }
        
        $negotiable_transportations = $negotiable_transportations->paginate(10);
        //dd($negotiable_transportations);
        
        return view('backend.negotiable_transportation.index', compact('negotiable_transportations', 'users', 'user_search_id'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::orderBy('email', 'asc')->get();

        return view('backend.negotiable_transportation.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $shopIds = $request->input('shop_ids');

        $shippingCosts = $request->input('shipping_costs');

        foreach ($shopIds as $index => $shopId) {
            $negotiableTransportation = new NegotiableTransportation;
            $negotiableTransportation->user_id = $request->user_id;
            $negotiableTransportation->shop_id = $shopId;
            $negotiableTransportation->cost = $shippingCosts[$index]; // Acceder al costo correspondiente
            $negotiableTransportation->status = 1;
            $negotiableTransportation->save();
        }
        
        return redirect()->route('negotiable_transportation.index')->with('success', translate('Negotiable transportation has been inserted successfully'));
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
        $negotiable_transportation = NegotiableTransportation::findOrFail($id);
        
        return view('backend.negotiable_transportation.edit', compact('negotiable_transportation'));
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
        $negotiable_transportation = NegotiableTransportation::findOrFail($id);

        $negotiable_transportation->cost = $request->shipping_cost;

        if ($negotiable_transportation->save()) 
            return redirect()->route('negotiable_transportation.index')->with('success', translate('Negotiable transportation has been updated successfully'));


        return back()->with('danger', translate('Something went wrong'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(NegotiableTransportation::destroy($id))
            return redirect()->route('negotiable_transportation.index')->with('success', translate('Negotiable transportation has been deleted successfully'));
        
        return back()->with('danger', translate('Something went wrong'));
    }

    public function get_cart_products(Request $request)
    {
        $cartProducts = Cart::where('user_id', $request->userId)->orderBy('owner_id')->get();

        $addressData = '';

        if (!$cartProducts->isEmpty()) {
            $addressId = $cartProducts[0]->address_id;
        
            $address = Address::find($addressId);

            if ($address) {
                $countryName = $address->country->name;
                $stateName = $address->state->name;
                $cityName = $address->city->name;
                $phoneNumber = $address->phone;
                $addressData = $address->address . ', ' . $cityName . ', ' . $stateName . ', ' . $countryName . ', Teléfono: ' . $phoneNumber;
            }
        }

        $groupedCartProducts = [];

        foreach ($cartProducts as $cartProduct) {
            $ownerId = $cartProduct->owner_id;

            $product = Product::find($cartProduct->product_id);
            $productName = $product->name;
            $productNegotiableTransportation = $product->negotiable_transportation;
    
            $shopId = $ownerId == 1 ? 1 : Shop::where('user_id', $ownerId)->value('id');
            $shopName = $ownerId == 1 ? 'QvaShop' : Shop::where('user_id', $ownerId)->value('name');
    
            // Agregar los datos al array agrupado
            $groupedCartProducts[$ownerId][] = [
                'shop_id' => $shopId,
                'shop_name' => $shopName,
                'product_name' => $productName,
                'product_negotiable_transportation' => $productNegotiableTransportation,
                'quantity' => $cartProduct->quantity
            ];
        }
        
        return response()->json([
            'address' => $addressData,
            'cart_products' => $groupedCartProducts
        ]);
    }
}
