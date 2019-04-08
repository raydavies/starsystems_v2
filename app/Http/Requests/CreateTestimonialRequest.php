<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreateTestimonialRequest extends Request
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
			'state_province.required' => 'The state field is required.'
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
			'name' => 'required|max:255',
			'city' => 'required|max:255',
			'state_province' => 'required|max:2',
			'comment' => 'required'
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
