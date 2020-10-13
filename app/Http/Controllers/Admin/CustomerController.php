<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index()
    {
        $customerCount = Customer::count();
        $page = $this->request->has('page') ? $this->request->get('page') : 1;
        $totalPages = ceil($customerCount / 10);
        $offset = ($page > 1) ? ($page - 1) * 10 : 0;

        $customers = Customer::orderBy('created_at', 'desc')
            ->offset($offset)    
            ->limit(10)
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
}