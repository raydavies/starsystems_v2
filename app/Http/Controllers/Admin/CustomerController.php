<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    protected $request;

    const RECORDS_PER_PAGE = 15;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index()
    {
        $customerCount = Customer::count();
        $page = $this->request->has('page') ? $this->request->get('page') : 1;
        $totalPages = ceil($customerCount / self::RECORDS_PER_PAGE);
        $offset = ($page > 1) ? ($page - 1) * self::RECORDS_PER_PAGE : 0;

        $customers = Customer::orderBy('created_at', 'desc')
            ->offset($offset)    
            ->limit(self::RECORDS_PER_PAGE)
            ->get();
        
        return view('admin.customers', [
            'customers' => $customers,
            'currentPage' => (int) $page,
            'totalPages' => (int) $totalPages,
        ]);
    }

    public function get(Customer $customer)
    {
        return view('admin.customer', [
            'customer' => $customer,
        ]);
    }

    public function delete($customer_id)
    {
        if (!$this->request->ajax()) {
            abort(404);
        }

        $response = ['success' => false];

        $customer_id = (int) $customer_id;
        $deleteCount = Customer::destroy($customer_id);

        if ($deleteCount === 1) {
            $response['success'] = true;
            $response['data'] = ['deleted' => $customer_id];
            $this->request->session()->flash('alert', ['status' => 'success', 'message' => 'Successfully deleted customer']);
        } else {
            $response['message'] = 'Unable to delete customer. Please try again later.';
            $this->request->session()->flash('alert', ['status' => 'danger', 'message' => 'Unable to delete customer. Please try again later.']);
        }

        return response()->json($response);
    }
}