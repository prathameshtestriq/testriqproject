<?php

namespace App\Http\Controllers;

use App\Libraries\Curlcall;
use Illuminate\Http\Request;
use App\Libraries\Mysecurity;
use Illuminate\Support\Facades\Session;
use App\Models\AdminModel;
use App\Models\Module;
use App\Models\AdminUserRight;
use App\Models\Master_farmer;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function clear_search(){
        session::forget('name');
        return redirect('/users');
    }
    public function index(Request $request){
        $aReturn = array();
        $aReturn['search_name'] = '';

        if(isset($request->form_type) && $request->form_type ==  'search_user'){
            // dd($request->name);
           session(['name' => $request->name]);

            return redirect('/users');
        }
        $aReturn['search_name'] =  (!empty(session('name'))) ? session('name') : '';

        $CountRows=User::get_count($aReturn);
        $PageNo = request()->input('page', 1);
        $Limit = config('custom.per_page');
        $aReturn['Offset'] = ($PageNo - 1) * $Limit;

        $aReturn["user_array"] =User::get_all($Limit,$aReturn);

        $aReturn['Paginator'] = new LengthAwarePaginator($aReturn['user_array'], $CountRows, $Limit, $PageNo);
        $aReturn['Paginator']->setPath(request()->url());
        // dd($aReturn);
        return view('users.list',$aReturn);
    }

    public function add_edit(Request $request, $iId = 0)
    {
        // dd($request->all());
        $aReturn['id'] = '';
        $aReturn['firstname'] = '';
        $aReturn['lastname'] = '';
        $aReturn['email'] = '';
        $aReturn['mobile'] = '';
        $aReturn['username'] = '';
        $aReturn['password'] = '';
        $aReturn['is_active'] = 0;
        $aReturn['type'] = '';

        if (isset($request->form_type) && $request->form_type == 'add_edit_user') {
            // dd($request->all());
            // dd($country_id);
            # VALIDATION RULES
            $Rules = [
                'username' => 'required',
                'firstname' => 'required',
                'lastname' => 'required',
                'contact_number' => 'required|digits:10',
                'status' => 'required',
                'type' => 'required'
            ];

            if ($iId > 0) {
                # UPDATE
                $Rules['email'] = 'required|email:rfc,dns|unique:users,email,' . $iId . ',id';
                $messages = [
                    'email.unique' => 'The given email already exists.'
                ];

                $request->validate($Rules, $messages);

                $Result = User::update_user($iId, $request);
                $SuccessMessage = 'User updated successfully';
            } else {
                // dd("in add");

                # ADD
                $Rules['email'] = 'required|email:rfc,dns|unique:users';
                $Rules['password'] = 'required|confirmed|min:5';
                $messages = [
                    'email.unique' => 'The given email already exists.'
                ];
//dd($request->all());


                $request->validate($Rules, $messages);
//dd($request->all());

                // if ($validator->fails()) {
                //     return redirect()->back()->withErrors($validator)->withInput();
                // }

                $Result = User::add_user($request);
                $SuccessMessage = 'User added successfully';
            }
            return redirect('/users')->with('success', $SuccessMessage);
        } else {
            // EDIT
            if ($iId > 0) {
                $sSQL = 'SELECT * FROM users WHERE id=:id';
                $Users = DB::select($sSQL, array('id' => $iId));
                // dd($Users);
                $aReturn = (array)$Users[0];
            }
        }

        $sSQL = 'SELECT * FROM `master_roles`';
        $aReturn['type'] = DB::select($sSQL);

        // $sSQL='SELECT * FROM master_countries';
        // $aReturn['master_country']=DB::select($sSQL);

        return view('users.create', $aReturn);
    }


    public function get_country_info(Request $request)
    {
        $country_data=array();
        if($request->country_id==1)
        {
            $country_id=!empty($request->country_id)?$request->country_id:0;
            $post = array('country_id' => $country_id);
            $country_data=Master_farmer::get_country_info($country_id,$post);
        }

        if($country_data){
            return $country_data;
        }else{
            return [];
        }
    }

    public function change_active_status(Request $request)
    {
        $aReturn = User::change_status($request);
        return $aReturn;
    }

    public function delete_user($iId)
    {
            User::remove_user($iId);
          return redirect(url('/users'))->with('success','User deleted successfully');
    }
}
