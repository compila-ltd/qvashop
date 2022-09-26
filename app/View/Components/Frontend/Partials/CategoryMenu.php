<?php

namespace App\View\Components\Frontend\Partials;

use App\Models\Category;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Cache;

class CategoryMenu extends Component
{
    public $categories;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->categories = Cache::remember('categories', 86400, function () {
            return Category::where('level', 0)->orderBy('order_level', 'desc')->get()->take(11);
        });
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.frontend.partials.category-menu');
    }
}
