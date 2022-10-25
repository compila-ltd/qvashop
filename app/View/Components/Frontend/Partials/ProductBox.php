<?php

namespace App\View\Components\Frontend\Partials;

use Illuminate\View\Component;

class ProductBox extends Component
{
    public $product;

    /**
     * Get the Product ID
     *
     * @return void
     */
    public function __construct($product)
    {
        $this->product = $product;
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
