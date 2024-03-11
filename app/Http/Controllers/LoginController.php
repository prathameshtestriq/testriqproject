<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\LoginModel;
use App\Models\AdminUserRight;
use App\Models\Athelete;

class LoginController extends Controller
{
    public function __construct()
    {
        // $this->admin_user_rights = new AdminUserRight();
    }

    public function index(Request $request)
    {

        if ($request->post('command') == 'login') {
            $request->validate([
                'email' => 'required',
                'password' => 'required|min:5'
            ]);
            //echo md5($request['password']);
        //     dd($request);
            $aResult = LoginModel::where(array('email' => $request['email'], 'password' => md5($request['password'])))->first();
          //  dd($aResult);

            if ($aResult) {
                if ($aResult->deleted == 0) {

                if ($aResult->is_active == 1) {
                    if ($aResult->type == 1)  {
                        $sessArry = array();
                        $sessArry = array(
                            'id' => $aResult->id,
                            'firstname'=>$aResult->firstname,
                            'lastname'=>$aResult->lastname,
                            'username'=>$aResult->username,
                            'password' => $aResult->password,
                            'type' => $aResult->type,
                            'user_login'=>1,
                            'theme_mode' => $aResult->theme_mode,
                            'email' => $aResult->email,
                            'contact_number'=>$aResult->email,
                            'local_vendor_company' => $aResult->local_vendor_company,
                        );

                        Session::put('logged_in', $sessArry);
                        //dd(Session::all());
                        return redirect('/dashboard');
                    } else {
                        $request->session()->flash('error', 'User is not superadmin !');
                        return redirect('/admin');
                    }
                } else {
                    $request->session()->flash('error', 'User is De-activate !');
                    return redirect('/admin');
                }
            }else{
                $request->session()->flash('error', 'User is Deleted. Please contact Admin !');
                    return redirect('/admin');
            }

            } else {
                $request->session()->flash('error', 'Invalid Username or Password !');
                return redirect('/admin');
            }
        }
        return view('login');
    }

    public function logout(Request $request)
    {
      
        $request->session()->flush();
        return redirect('/admin');
    }

   
}