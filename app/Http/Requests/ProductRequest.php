<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'          => 'required|max:255',
            'category_id'   => 'required',
            'unit'          => 'required',
            'min_qty'       => 'required|numeric',
            'unit_price'    => 'required|numeric',
            'discount'      => 'required|numeric',
            'current_stock' => 'required|numeric',
        ];
    }

    /**
     * Get the validation messages of rules that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required'             => 'Product name is required',
            'category_id.required'      => 'Category is required',
            'unit.required'             => 'Unit field is required',
            'min_qty.required'          => 'Minimum purchase quantity is required',
            'min_qty.numeric'           => 'Minimum purchase must be numeric',
            'unit_price.required'       => 'Unit price is required',
            'unit_price.numeric'        => 'Unit price must be numeric',
            'discount.required'         => 'Discount is required',
            'discount.numeric'          => 'Discount must be numeric',
            'current_stock.required'    => 'Current stock is required',
            'current_stock.numeric'     => 'Current stock must be numeric',
        ];
    }
}
