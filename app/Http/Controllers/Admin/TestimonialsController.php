<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestimonialsController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index()
    {
        $testimonials = Testimonial::orderBy('created_at', 'desc')->get();

        return view('admin.testimonials', [
            'testimonials' => $testimonials
        ]);
    }

    public function toggleStatus($testimonial_id)
    {
        if (!$this->request->ajax()) {
            abort(404);
        }

        $response = [];

        $updateSuccess = DB::table('testimonials')
            ->where('id', '=', $testimonial_id)
            ->update(['flag_active' => DB::raw('CASE WHEN flag_active = 1 THEN 0 ELSE 1 END')]);

        if ($updateSuccess) {
            $response['data'] = Testimonial::find($testimonial_id);
        } else {
            $response['message'] = 'Could not update testimonial status';
        }

        $response['success'] = $updateSuccess;

        return response()->json($response);
    }

    public function delete($testimonial_id)
    {
        if (!$this->request->ajax()) {
            abort(404);
        }

        $response = ['success' => false];

        if (is_array($testimonial_id)) {
            $response['message'] = 'Unable to determine which testimonial to delete.';
        } else {
            $testimonial_id = (int) $testimonial_id;
            $deleteCount = Testimonial::destroy($testimonial_id);

            if ($deleteCount === 1) {
                $response['success'] = true;
                $response['data'] = ['deleted' => $testimonial_id];
            } else {
                $response['message'] = 'Unable to delete testimonial. Please try again later.';
            }
        }

        return response()->json($response);
    }
}
