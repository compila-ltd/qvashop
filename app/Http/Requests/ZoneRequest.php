<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ZoneRequest extends FormRequest
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
            'name' => ['required'],
            'status' => ['required'],
            'country_id' => ['required']
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'status'    => 1
        ]);
    }
}
