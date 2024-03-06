<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\LoginModel;
use App\Models\AdminUserRight;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->admin_user_rights = new AdminUserRight();
    }

    public function index(Request $request)
    {
        // dd($request);
        if ($request->post('command') == 'login') {
            $request->validate([
                'email' => 'required|email:rfc,dns',
                'password' => 'required|min:5'
            ]);
            $aResult = LoginModel::where(array('email' => $request['email'], 'password' => md5($request['password']), 'deleted' => 0))->first();
            // dd($aResult);
            if ($aResult) {
                if ($aResult->deleted == 0) {

                    if ($aResult->active == 1) {
                        if (($aResult->superadmin == 1) || ($aResult->subadmin == 1) || ($aResult->subadmin == 2)) {
                            $sessArry = array(
                                'id' => $aResult->id,
                                'emp_id' => $aResult->emp_id,
                                'superuser' => $aResult->superadmin,
                                'subadmin' => $aResult->subadmin,
                                'admin_loggedin' => 1,
                                'firstname' => $aResult->firstname,
                                'lastname' => $aResult->lastname,
                                'gender' => $aResult->gender,
                                'password' => $aResult->password,
                                'theme_mode' => $aResult->theme_mode,
                                'email' => $aResult->email,
                                'company' => $aResult->company_id,
                            );
                            // dd($sessArry['subadmin']);
                            $aModules = $this->admin_user_rights->get_user_modules($aResult->id, $aResult->superadmin);
                            // $aDivisions = $this->admin_user_rights->get_user_divisions($aResult->id, $aResult->superadmin);

                            Session::put('modules', $aModules);
                            // Session::put('divisions', $aDivisions);
                            Session::put('logged_in', $sessArry);

                            // dd(Session::all());
                            // return redirect('/biometric_attendance');
                            return redirect('/dashboard');
                        }
                    } else {
                        $request->session()->flash('error', 'User is De-activate !');
                        return redirect('/');
                    }
                } else {
                    $request->session()->flash('error', 'User is Deleted. Please contact Admin !');
                    return redirect('/');
                }
            } else {
                $request->session()->flash('error', 'Invalid Username or Password !');
                return redirect('/');
            }
        }
        return view('login');
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect('/');
    }
}