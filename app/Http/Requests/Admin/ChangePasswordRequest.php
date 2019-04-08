<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class ChangePasswordRequest extends Request
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

    public function messages()
    {
        return [
            'old_password.required' => 'The current password field is required.',
            'old_password.password' => 'Current password is incorrect.',
            'password.required' => 'The new password field is required.',
            'password.min' => 'Your new password must be at least 6 characters.',
            'password.different' => 'Your new password must not match your current password.',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->sanitize();

        return [
            'old_password' => 'required|password',
            'password' => 'required|min:6|confirmed|different:old_password',
            'password_confirmation' => 'same:password'
        ];
    }

    /**
     * Filter the inputs from the request.
     *
     * @return void
     */
    public function sanitize()
    {
        $inputs = $this->all();

        foreach ($inputs as $field => $value) {
            $inputs[$field] = filter_var($value, FILTER_SANITIZE_STRING);
        }

        $this->replace($inputs);
    }
}
