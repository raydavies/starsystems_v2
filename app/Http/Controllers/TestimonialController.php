<?php
namespace App\Http\Controllers;

use App\Http\Requests\CreateTestimonialRequest;
use App\Models\Testimonial;
use App\Models\State;
use Illuminate\Http\Request;
use DB;
use Mail;

class TestimonialController extends Controller
{
	protected $request;

	public function __construct(Request $request)
	{
		$this->request = $request;
	}

	/**
	 * Loads the testimonials page.
	 *
	 * @return Illuminate\View\View
	 */
	public function get()
	{
		$testimonials = Testimonial::where('flag_active', 1)
            ->orderBy('created_at', 'desc')
            ->get();

		return view('testimonials', [
			'testimonials' => $testimonials
		]);
	}

	/**
	 * Loads the create new testimonial form.
	 *
	 * @return Illuminate\View\View
	 */
	public function create()
	{
		return view('testimonials_create', [
			'testimonial' => new Testimonial(),
			'states' => State::all()
		]);
	}

	/**
	 * Validates the form request and saves the testimonial.
	 *
	 * @param CreateTestimonialRequest $request
	 * @return Illuminate\View\View
	 */
	public function store(CreateTestimonialRequest $request)
	{
		$testimonial = new Testimonial($request->all());

		if ($testimonial->save()) {
			try {
				Mail::send('email.testimonial', array(
					'testimonial' => $testimonial
				), function($message) {
					$message->from('automailer@starlearningsystems.com');
					$message->to(ContactController::POSTMASTER_EMAIL, ContactController::POSTMASTER_NAME)->subject('New Customer Testimonial');
				});
			} catch (\Exception $e) {
				error_log(sprintf(
					"Unable to send new testimonial notification email: %s",
					$e->getMessage()
				));
			}

			return redirect('testimonials')->with('alert',
				['status' => 'success', 'message' => 'Thank you for submitting your testimonial!']
			);
		} else {
			return back()->withInput()->with('alert',
				['status' => 'danger', 'message' => 'There was an error saving your testimonial. Please try again later.']
			);
		}
	}
}
