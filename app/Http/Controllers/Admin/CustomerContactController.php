<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerContactController extends Controller
{
    public function store(Request $request)
	{
        $validatedData = $request->validate([
            'action' => 'required|max:255',
            'customer_id' => 'required|integer',
            'details' => 'required',
        ]);

        $contact = new ContactHistory($validatedData);
        $contact->user_id = Auth::id();
        
        if ($contact->save()) {
			return redirect()->route('admin.customer', ['customer' => $contact->customer_id])->with('alert',
				['status' => 'success', 'message' => 'Successfully added customer contact note']
			);
        } else {
            return back()->withInput()->with('alert',
                ['status' => 'danger', 'message' => 'There was an error saving this note. Please try again later.']
            );
        }
	}
}