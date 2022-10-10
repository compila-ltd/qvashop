<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\BlogCategory;
use App\Models\Blog;

class SitemapController extends Controller
{
    // generate an updated Sitema in basepath/public/sitemap.xml
    public function generate()
    {
        // get Static Pages


        // get Categories
        $categories = Category::orderBy('order_level', 'desc')->get();

        // get products
        $products = Product::with('reviews', 'brand', 'stocks', 'user', 'user.shop')->where('auction_product', 0)->where('approved', 1)->get();

        // get blog categories
        $blog_categories = BlogCategory::all();
        
        // get blog posts
        $blogs = Blog::orderBy('created_at', 'desc');
        
        // get blog tags


        // get Brands


        // generate sitemap.xml
        return response(view('frontend.sitemap', compact('categories', 'products', 'blog_categories', 'blogs')), 200, [
            'Content-Type' => 'application/xml'
        ]);
    }
}
