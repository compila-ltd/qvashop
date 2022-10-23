<?php

namespace App\Http\Controllers\Seller;

use PDF;
use Excel;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\ProductsImport;
use Illuminate\Support\Facades\Auth;

class ProductBulkUploadController extends Controller
{
    public function index()
    {
        if (Auth::user()->shop->verification_status)
            return view('seller.product.product_bulk_upload.index');

        return back()->with('warning', translate('Your shop is not verified yet!'));
    }

    // TODO: Install PDF Package
    public function pdf_download_category()
    {
        $categories = Category::all();
        return PDF::loadView('backend.downloads.category', ['categories' => $categories], [], [])->download('category.pdf');
    }

    public function pdf_download_brand()
    {
        $brands = Brand::all();
        return PDF::loadView('backend.downloads.brand', ['brands' => $brands], [], [])->download('brands.pdf');
    }

    public function bulk_upload(Request $request)
    {
        if ($request->hasFile('bulk_file')) {
            $import = new ProductsImport;
            Excel::import($import, request()->file('bulk_file'));
        }

        return back();
    }
}
