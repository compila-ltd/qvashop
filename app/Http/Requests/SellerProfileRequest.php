<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SellerProfileRequest extends FormRequest
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
        $newPasswordRule        = 'sometimes';
        $confirmPasswordRule    = 'sometimes';
        if ($this->request->get('new_password') != null && $this->request->get('confirm_password') != null) {
            $newPasswordRule       = ['min:6'];
            $newPasswordRule       = ['min:6'];
        }
        return [
            'name'              => ['required', 'max:191'],
            'new_password'      => $newPasswordRule,
            'confirm_password'  => $confirmPasswordRule,
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
            'name.required'         => translate('Name is required'),
            'new_password.min'      => translate('Minimum 6 characters'),
            'confirm_password.min'  => translate('Minimum 6 characters'),
        ];
    }
}
