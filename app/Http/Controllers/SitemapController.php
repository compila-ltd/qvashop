<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class SitemapController extends Controller
{
    // generate an updated Sitema in basepath/public/sitemap.xml
    public function generate()
    {
        echo "generation of sitemap.xml started";

        // get Static Pages
        // get Categories
        $categories = Category::orderBy('order_level', 'desc')->get();

        // get products
        $products = Product::with('reviews', 'brand', 'stocks', 'user', 'user.shop')->where('auction_product', 0)->where('approved', 1)->get();

        // get blog posts
        // get blog categories
        // get blog tags

        // generate sitemap.xml

        return view('frontend.sitemap', compact('categories', 'products'));
    }
}
