<?php

namespace App\View\Components\Frontend\Partials;

use Illuminate\View\Component;

class CategoryElements extends Component
{

    public $category_utility;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->category_utility = "";
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.frontend.partials.category-elements');
    }
}
