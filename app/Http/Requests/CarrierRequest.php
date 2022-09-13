<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CarrierRequest extends FormRequest
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
            'carrier_name'      => 'required|max:255',
            'transit_time'      => 'required|max:255',
            'delimiter1.*'      => 'required',
            'delimiter2.*'      => 'required',
            'carrier_price.*.*' => 'required',
            'zones'             => 'required',
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
            'carrier_name.required' => translate('Carrier name is required'),
            'carrier_name.max'      => translate('Max 255 characters'),
            'transit_time.required'  => translate('Transit time is required'),
            'delimiter1.*.required'    => translate('Delimiter1 is required'),
            'delimiter2.*.required'       => translate('Delimiter2 is required'),
            'carrier_price.*.*.required'    => translate('Carrier price is required'),
            'zones.required'                => translate('Zone is required. If zone is not created, then create zone at first'),
        ];
    }
}
