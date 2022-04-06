<?php
namespace App\Http\Controllers;

use App\Http\Requests\ContactFormRequest;
use Mail;

class ContactController extends Controller
{
    const POSTMASTER_EMAIL = 'postmaster@starlearningsystems.com';
    const POSTMASTER_NAME = 'James Anderson';
        
	/**
	 * Loads the contact us form.
	 *
	 * @return Illuminate\View\View
	 */
	public function create()
	{
		return view('contact');
	}

	/**
	 * Validates the form request and sends an email.
	 *
	 * @param ContactFormRequest $request
	 * @return Illuminate\View\View
	 */
	public function store(ContactFormRequest $request)
	{
		$name = $request->get('first_name') . ' ' . $request->get('last_name');
		$email = $request->get('email');
		$subject = $request->get('subject');

		try {
			Mail::send('email.contact', array(
				'name' => $name,
				'email' => $email,
				'user_message' => $request->get('message')
			), function($message) use ($name, $email, $subject) {
				$message->from($email, $name);
				$message->to(self::POSTMASTER_EMAIL, self::POSTMASTER_NAME)->subject($subject);
			});
		} catch (\Exception $e) {
			return back()->withInput()->with('alert', ['status' => 'danger', 'message' => 'There was an error submitting your request. Please try again later.']);
		}

		return redirect('contact')->with('alert', array('status' => 'success', 'message' => 'Thank you for your feedback!'));
	}
}
