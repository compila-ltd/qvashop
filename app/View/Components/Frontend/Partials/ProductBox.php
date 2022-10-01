<?php

namespace App\View\Components\Frontend\Partials;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;

class ProductBox extends Component
{
    public $product;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($product)
    {
        // Cache this product for 5 minutes
        $this->product = Cache::remember('product_' . $product, 300, function() use ($product) {
            return Product::find($product);
        });
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.frontend.partials.product-box');
    }
}
