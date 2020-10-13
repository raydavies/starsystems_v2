<?php
namespace App\Http\Controllers;

use App\Http\Requests\RequestInfoFormRequest;
use App\Models\Customer;
use App\Models\Grade;
use App\Models\State;
use Mail;

class RequestInfoController extends Controller
{
    const POSTMASTER_EMAIL = 'postmaster@starlearningsystems.com';
    const POSTMASTER_NAME = 'James Anderson';
        
	/**
	 * Loads the 'request more information' form.
	 *
	 * @return Illuminate\View\View
	 */
	public function create()
	{
		return view('request_info', [
			'customer' => new Customer(),
			'grades' => Grade::all(),
			'states' => State::all(),
		]);
	}

	/**
	 * Validates the form request and sends an email.
	 *
	 * @param RequestInfoFormRequest $request
	 * @return Illuminate\View\View
	 */
	public function store(RequestInfoFormRequest $request)
	{
		$customer = Customer::where('email', $request->get('email'))->first();
		if (!$customer instanceof Customer) {
			$customer = new Customer($request->all());

			if (!$customer->save()) {
				return back()->withInput()->with('alert', ['status' => 'danger', 'message' => 'There was an error submitting your request. Please try again later.']);
			}

			$subject = 'Interactive Curriculum Information Request - New Customer';
		} else {
			$subject = 'Interactive Curriculum Information Request - Existing Customer';
		}

		Mail::send('email.request_info', [
			'customer' => $customer
		], function($message) use ($customer, $subject) {
			$message->from($customer->email, $customer->name);
			$message->to(self::POSTMASTER_EMAIL, self::POSTMASTER_NAME)->subject($subject);
		});

		return redirect()->route('request_info')->with('alert', ['status' => 'success', 'message' => "Thank you for your interest! We'll get back to you with more information shortly."]);
	}
}
