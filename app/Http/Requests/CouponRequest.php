<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class CouponRequest extends FormRequest
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
        $productsRule           = 'sometimes';
        $minBuyRule             = 'sometimes';
        $maxDiscountRule        = 'sometimes';
        if ($this->request->get('type') == 'product_base') {
            $productsRule       = 'required';
        }
        if ($this->request->get('type') == 'cart_base') {
            $minBuyRule         = ['required', 'numeric', 'min:1'];
            $maxDiscountRule    = ['required', 'numeric', 'min:1'];
        }
        return [
            'type'          => ['required'],
            'code'          => ['required', Rule::unique('coupons')->ignore($this->coupon), 'max:255',],
            'discount'      => ['required', 'numeric', 'min:1'],
            'discount_type' => ['required'],
            'product_ids'   => $productsRule,
            'min_buy'       => $minBuyRule,
            'max_discount'  => $maxDiscountRule,
            'date_range'    => ['required'],
            'start_date'    => ['required'],
            'end_date'      => ['required'],
            'details'       => ['required'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'type.required'         => translate('Coupon type is required'),
            'code.required'         => translate('Coupon code is required'),
            'code.unique'           => translate('Coupon already exist for this coupon code'),
            'code.max'              => translate('Max 255 characters'),
            'product_ids.required'  => translate('Product is required'),
            'discount.required'     => translate('Discount is required'),
            'discount.numeric'      => translate('Discount should be numeric type'),
            'discount.min'          => translate('Discount should be l or greater'),
            'discount_type.required' => translate('Discount type is required'),
            'min_buy.required'      => translate('Minimum shopping amount is required'),
            'min_buy.numeric'       => translate('Minimum shopping amount should be numeric type'),
            'min_buy.min'           => translate('Minimum shopping amount should be l or greater'),
            'max_discount.required' => translate('Max discount amount is required'),
            'max_discount.numeric'  => translate('Max discount amount should be numeric type'),
            'max_discount.min'      => translate('Max discount amount should be l or greater'),
            'date_range.required'   => translate('Date Range is required'),
        ];
    }


    protected function prepareForValidation()
    {
        $date_range = explode(" - ", $this->date_range);
        $start_date = '';
        $end_date = '';
        // dd($date_range);
        if($date_range[0]) {
            $start_date = strtotime($date_range[0]);
            $end_date = strtotime($date_range[1]);
        }
        $coupon_details = null;
        if ($this->type == "product_base") {
            $coupon_details = array();
            if($this->product_ids) {
                foreach ($this->product_ids as $product_id) {
                    $data['product_id'] = $product_id;
                    array_push($coupon_details, $data);
                }
            }
            $coupon_details = json_encode($coupon_details);
        } elseif ($this->type == "cart_base") {
            $data                     = array();
            $data['min_buy']          = $this->min_buy;
            $data['max_discount']     = $this->max_discount;
            $coupon_details           = json_encode($data);
        }

        $this->merge([
            'start_date'    => $start_date,
            'end_date'      => $end_date,
            'details'       => $coupon_details
        ]);
    }

    /**
     * Get the error messages for the defined validation rules.*
     * @return array
     */
    public function failedValidation(Validator $validator)
    {
        // dd($this->expectsJson());
        if ($this->expectsJson()) {
            throw new HttpResponseException(response()->json([
                'message' => $validator->errors()->all(),
                'result' => false
            ], 422));
        } else {
            throw (new ValidationException($validator))
                    ->errorBag($this->errorBag)
                    ->redirectTo($this->getRedirectUrl());
        }
    }
}
