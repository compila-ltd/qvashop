<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductQueryController extends Controller
{

    /**
     * Retrieve queries that belongs to current seller
     */
    public function index()
    {
        $queries = ProductQuery::where('seller_id', Auth::id())->latest()->paginate(20);
        return view('backend.support.product_query.index', compact('queries'));
    }

    /**
     * Retrieve specific query using query id.
     */
    public function show($id)
    {
        $query = ProductQuery::find(decrypt($id));
        return view('backend.support.product_query.show', compact('query'));
    }

    /**
     * store products queries through the ProductQuery model
     * data comes from product details page
     * authenticated user can leave queries about the product
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'question' => 'required|string',
        ]);
        $product = Product::find($request->product);

        $query = new ProductQuery();
        $query->customer_id = Auth::id();
        $query->seller_id = $product->user_id;
        $query->product_id = $product->id;
        $query->question = $request->question;
        $query->save();

        return redirect()->back()->with('success', translate('Your query has been submittes successfully'));
    }

    /**
     * Store reply against the question from Admin panel
     */

    public function reply(Request $request, $id)
    {
        $this->validate($request, [
            'reply' => 'required',
        ]);
        $query = ProductQuery::find($id);
        $query->reply = $request->reply;
        $query->save();

        return redirect()->route('product_query.index')->with('success', translate('Replied successfully!'));
    }
}
