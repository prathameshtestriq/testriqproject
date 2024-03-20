<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

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
            //  dd($request->name);
           session(['name' => $request->name]);
            session(['mobile' => $request->mobile]);

            return redirect('/users');
        }
        $aReturn['search_name'] =  (!empty(session('name'))) ? session('name') : '';
         $aReturn['mobile'] =  (!empty(session('mobile'))) ? session('mobile') : '';

        // dd($aReturn);
        $CountRows=User::get_count($aReturn);
        $PageNo = request()->input('page', 1);
        $Limit = config('custom.per_page');
        // $Limit = 3;

        $aReturn['Offset'] = ($PageNo - 1) * $Limit;

        $aReturn["user_array"] =User::get_all($Limit,$aReturn);

        $aReturn['Paginator'] = new LengthAwarePaginator($aReturn['user_array'], $CountRows, $Limit, $PageNo);
        $aReturn['Paginator']->setPath(request()->url());
        // dd($aReturn);
        return view('users.list',$aReturn);
    }

    public function add_edit(Request $request, $iId = 0)
    {

        $aReturn = [
            'id' => '',
            'firstname' => '',
            'lastname' => '',
            'email' => '',
            'mobile' => '',
            'username' => '',
            'password' => '',
            'is_active' => 1,
            'type' => []
        ];


        if ($request->has('form_type') && $request->form_type == 'add_edit_user') {

            $rules = [
                'firstname' => 'required',
                'lastname' => 'required',
                'mobile' => 'required|digits:10',
                'status' => 'required',
                'type' => 'required'
            ];


            if ($iId > 0) {
                $rules['email'] = 'required|email:rfc,dns';
                $rules['password'] = 'nullable|confirmed|min:5';
            } else {
                $rules['email'] = 'required|email:rfc,dns|unique:users';
                $rules['password'] = 'required|confirmed|min:5';
            }


            $validator = Validator::make($request->all(), $rules);


            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            if ($iId > 0) {
                $result = User::update_user($iId, $request);
                $successMessage = 'User updated successfully';
            } else {

                $result = User::add_user($request);
                $successMessage = 'User added successfully';
            }

            return redirect('/users')->with('success', $successMessage);
        } else {

            if ($iId > 0) {
                $user = User::find($iId);
                if ($user) {
                    $aReturn = $user->toArray();
                }

            }


            $userRoles = DB::table('master_roles')->get()->toArray(); // Convert collection to array
            $aReturn['type'] = $userRoles;

            // Return the view with data
            return view('users.create', $aReturn);
        }
    }



    // public function get_country_info(Request $request)
    // {
    //     $country_data=array();
    //     if($request->country_id==1)
    //     {
    //         $country_id=!empty($request->country_id)?$request->country_id:0;
    //         $post = array('country_id' => $country_id);
    //         $country_data=Master_farmer::get_country_info($country_id,$post);
    //     }

    //     if($country_data){
    //         return $country_data;
    //     }else{
    //         return [];
    //     }
    // }

    public function change_active_status(Request $request)
    {
        $aReturn = User::change_status($request);
       // dd($aReturn);
        return $aReturn;
    }

    public function delete_user($iId)
    {
            User::remove_user($iId);
          return redirect(url('/users'))->with('success','User deleted successfully');
    }
}
