<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ChangePasswordRequest;
use Illuminate\Support\Facades\Auth;

class PasswordController extends Controller
{
    protected $request;
    
    public function index()
    {
        return view('admin/change-password');
    }
    
    public function update(ChangePasswordRequest $request)
    {
        $user = Auth::user();
        
        $userId = $user->id;
        $rememberMe = (bool) $user->remember_token;
        $user->password = bcrypt($request->get('password'));
        
        //save new password, then re-log in to ensure session is updated
        $user->save();
        Auth::loginUsingId($userId, $rememberMe);
        return redirect('admin/dashboard')->with('alert', array('status' => 'success', 'message' => 'Password successfully updated!'));
    }
}
