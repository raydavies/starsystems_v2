<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class RequestInfoFormRequest extends Request
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
		$this->sanitize();

		return [
            'name' => 'required|max:255',
            'state_province' => 'nullable|max:2',
			'email' => 'required|email',
            'phone_home' => 'required',
            'child_name' => 'nullable|max:255',
            'grade' => 'nullable|digits:1',
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
			$filtered = trim(filter_var($value, FILTER_SANITIZE_STRING));
			$inputs[$field] = $filtered === "" ? null : $filtered;
		}

		$this->replace($inputs);
	}

    /**
     * Adds custom error messages to validator.
     *
     * @return array
     */
	public function messages()
    {
        return [
			'digits' => 'This field must contain a valid grade level',
			'email' => 'This field must contain a valid email address',
		];
    }
}
