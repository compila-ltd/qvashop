<?php

namespace App\Http\Controllers;

use App\Models\Color;
use App\Models\Attribute;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\AttributeValue;
use App\Models\AttributeTranslation;

class AttributeController extends Controller
{
    public function __construct()
    {
        // Staff Permission Check
        $this->middleware(['permission:view_product_attributes'])->only('index');
        $this->middleware(['permission:edit_product_attribute'])->only('edit');
        $this->middleware(['permission:delete_product_attribute'])->only('destroy');

        $this->middleware(['permission:view_product_attribute_values'])->only('show');
        $this->middleware(['permission:edit_product_attribute_value'])->only('edit_attribute_value');
        $this->middleware(['permission:delete_product_attribute_value'])->only('destroy_attribute_value');

        $this->middleware(['permission:view_colors'])->only('colors');
        $this->middleware(['permission:edit_color'])->only('edit_color');
        $this->middleware(['permission:delete_color'])->only('destroy_color');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $attributes = Attribute::orderBy('created_at', 'desc')->get();
        return view('backend.product.attribute.index', compact('attributes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $attribute = new Attribute;
        $attribute->name = $request->name;
        $attribute->save();

        $attribute_translation = AttributeTranslation::firstOrNew(['lang' => env('DEFAULT_LANGUAGE'), 'attribute_id' => $attribute->id]);
        $attribute_translation->name = $request->name;
        $attribute_translation->save();

        return redirect()->route('attributes.index')->with('success', translate('Attribute has been inserted successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data['attribute'] = Attribute::findOrFail($id);
        $data['all_attribute_values'] = AttributeValue::with('attribute')->where('attribute_id', $id)->get();

        // echo '<pre>';print_r($data['all_attribute_values']);die;

        return view("backend.product.attribute.attribute_value.index", $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $lang      = $request->lang;
        $attribute = Attribute::findOrFail($id);
        return view('backend.product.attribute.edit', compact('attribute', 'lang'));
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
        $attribute = Attribute::findOrFail($id);
        if ($request->lang == env("DEFAULT_LANGUAGE")) {
            $attribute->name = $request->name;
        }
        $attribute->save();

        $attribute_translation = AttributeTranslation::firstOrNew(['lang' => $request->lang, 'attribute_id' => $attribute->id]);
        $attribute_translation->name = $request->name;
        $attribute_translation->save();

        return back()->with('success', translate('Attribute has been updated successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $attribute = Attribute::findOrFail($id);
        foreach ($attribute->attribute_translations as $key => $attribute_translation) {
            $attribute_translation->delete();
        }
        Attribute::destroy($id);
        return redirect()->route('attributes.index')->with('success', translate('Attribute has been deleted successfully'));
    }

    // Store Attribute Value
    public function store_attribute_value(Request $request)
    {
        $attribute_value = new AttributeValue;
        $attribute_value->attribute_id = $request->attribute_id;
        $attribute_value->value = ucfirst($request->value);
        $attribute_value->save();

        return redirect()->route('attributes.show', $request->attribute_id)->with('success', translate('Attribute value has been inserted successfully'));
    }

    public function edit_attribute_value(Request $request, $id)
    {
        $attribute_value = AttributeValue::findOrFail($id);
        return view("backend.product.attribute.attribute_value.edit", compact('attribute_value'));
    }

    // Update Attribute Value
    public function update_attribute_value(Request $request, $id)
    {
        $attribute_value = AttributeValue::findOrFail($id);
        $attribute_value->attribute_id = $request->attribute_id;
        $attribute_value->value = ucfirst($request->value);
        $attribute_value->save();

        return back()->with('success', translate('Attribute value has been updated successfully'));
    }

    public function destroy_attribute_value($id)
    {
        $attribute_values = AttributeValue::findOrFail($id);
        AttributeValue::destroy($id);

        return redirect()->route('attributes.show', $attribute_values->attribute_id)->with('success', translate('Attribute value has been deleted successfully'));
    }

    public function colors(Request $request)
    {
        $sort_search = null;
        $colors = Color::orderBy('created_at', 'desc');

        if ($request->search != null) {
            $colors = $colors->where('name', 'like', '%' . $request->search . '%');
            $sort_search = $request->search;
        }
        $colors = $colors->paginate(10);

        return view('backend.product.color.index', compact('colors', 'sort_search'));
    }

    public function store_color(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'code' => 'required|unique:colors|max:255',
        ]);
        $color = new Color;
        $color->name = Str::replace(' ', '', $request->name);
        $color->code = $request->code;
        $color->save();

        return redirect()->route('colors')->with('success', translate('Color has been inserted successfully'));
    }

    // Edit Color
    public function edit_color(Request $request, $id)
    {
        $color = Color::findOrFail($id);
        return view('backend.product.color.edit', compact('color'));
    }

    /**
     * Update the color.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update_color(Request $request, $id)
    {
        $color = Color::findOrFail($id);

        $request->validate([
            'code' => 'required|unique:colors,code,' . $color->id,
        ]);

        $color->name = Str::replace(' ', '', $request->name);
        $color->code = $request->code;
        $color->save();

        return back()->with('success', translate('Color has been updated successfully'));
    }

    // destroy Color
    public function destroy_color($id)
    {
        Color::destroy($id);
        return redirect()->route('colors')->with('success', translate('Color has been deleted successfully'));
    }
}
