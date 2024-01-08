<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\PickupPoint;
use App\Models\PickupPointTranslation;

use Illuminate\Support\Facades\Auth;

class PickupPointController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        $shop_id = Auth::user()->shop->id;
        //dd($user);
        $sort_search = null;
        $pickup_points = PickupPoint::where('shop_id', $shop_id)->orderBy('created_at', 'desc');
        if ($request->has('search')) {
            $sort_search = $request->search;
            $pickup_points = $pickup_points->where('name', 'like', '%' . $sort_search . '%');
        }
        $pickup_points = $pickup_points->paginate(10);

        //dd($pickup_points);
        
        return view('seller.pickup_point.index', compact('pickup_points', 'sort_search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('seller.pickup_point.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd($request);
        $pickup_point = new PickupPoint;
        $pickup_point->name = $request->name;
        $pickup_point->address = $request->address;
        $pickup_point->phone = $request->phone;
        $pickup_point->pick_up_status = $request->pick_up_status;
        $pickup_point->manager = $request->manager;
        $pickup_point->staff_id = 0;

        $pickup_point->shop_id = Auth::user()->shop->id;

        $pickup_point->manager = $request->manager;

        if ($pickup_point->save()) {

            $pickup_point_translation = PickupPointTranslation::firstOrNew(['lang' => 'es', 'pickup_point_id' => $pickup_point->id]);
            //dd($pickup_point_translation);
            $pickup_point_translation->name = $request->name;
            $pickup_point_translation->address = $request->address;
            $pickup_point_translation->save();

            return redirect()->route('seller.pick_up_points.index')->with('success', translate('PicupPoint has been inserted successfully'));
        }

        return back()->with('danger', translate('Something went wrong'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $lang           = $request->lang;
        $pickup_point   = PickupPoint::findOrFail($id);
        return view('seller.pickup_point.edit', compact('pickup_point', 'lang'));
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
        $pickup_point = PickupPoint::findOrFail($id);

        $pickup_point->name = $request->name;
        $pickup_point->address = $request->address;

        $pickup_point->phone = $request->phone;
        $pickup_point->pick_up_status = $request->pick_up_status;
        $pickup_point->manager = $request->manager;
        if ($pickup_point->save()) {

            $pickup_point_translation = PickupPointTranslation::firstOrNew(['lang' => 'es',  'pickup_point_id' => $pickup_point->id]);
            $pickup_point_translation->name = $request->name;
            $pickup_point_translation->address = $request->address;
            $pickup_point_translation->save();

            return redirect()->route('seller.pick_up_points.index')->with('success', translate('PicupPoint has been updated successfully'));
        }

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
        $pickup_point = PickupPoint::findOrFail($id);
        $pickup_point->pickup_point_translations()->delete();

        if (PickupPoint::destroy($id))
            return redirect()->route('seller.pick_up_points.index')->with('success', translate('PicupPoint has been deleted successfully'));

        return back()->with('danger', translate('Something went wrong'));
    }
}
