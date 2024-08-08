<?php

namespace App\Http\Controllers;

use App\Exports\UserExport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function clear_search(){
        session::forget('name');
        session::forget('email');
        session::forget('mobile'); 
        session::forget('state'); 
        session::forget('city'); 
        session::forget('status'); 
        session::forget('gender'); 
        session::forget('rows'); 
        return redirect('/users');
    }

    public function index(Request $request){
        $aReturn = array();
        $aReturn['search_name'] = '';
        $aReturn['search_email_id'] ='';
        $aReturn['search_mobile'] = '';
        $aReturn['search_state'] = '';
        $aReturn['search_city'] = '';
        $aReturn['search_gender'] = '';
        $aReturn['search_rows'] = '';

        if(isset($request->form_type) && $request->form_type ==  'search_user'){
            // dd($request->gender);
           session(['name' => $request->name]);
           session(['email' => $request->email_id]);
           session(['mobile' => $request->mobile_no]);
           session(['state' => $request->state]);
           session(['city' => $request->city]);
           session(['status' => $request->status]);
           session(['gender' => $request->gender]);
           session(['rows' => $request->rows]);

            return redirect('/users');
        }
        $aReturn['search_name'] =  (!empty(session('name'))) ? session('name') : '';
        $aReturn['search_email_id'] = (!empty(session('email'))) ? session('email') : '';
        $aReturn['search_mobile'] =  (!empty(session('mobile'))) ? session('mobile') : '';
        $status = session('status');
        $aReturn['search_status'] = (isset($status) && $status != '') ? $status : '';
        $aReturn['search_state'] =  (!empty(session('state'))) ? session('state') : '';
        $aReturn['search_city'] =  (!empty(session('city'))) ? session('city') : '';
        $aReturn['search_gender'] =  (!empty(session('gender'))) ? session('gender') : '';
        $aReturn['search_rows'] =  (!empty(session('rows'))) ? session('rows') : '';


        // dd($aReturn);
        $CountRows=User::get_count($aReturn);
        $PageNo = request()->input('page', 1);
        $Limit = !empty($aReturn['search_rows']) ? $aReturn['search_rows'] : config('custom.per_page');
        // $Limit = 3;

        $aReturn['Offset'] = ($PageNo - 1) * $Limit;

        $aReturn["user_array"] =User::get_all($Limit,$aReturn);

        $sSQL = 'SELECT id, name,country_id FROM states WHERE country_id ='. 101;
        $aReturn["states"] = DB::select($sSQL, array());


        $aReturn['Paginator'] = new LengthAwarePaginator($aReturn['user_array'], $CountRows, $Limit, $PageNo);
        $aReturn['Paginator']->setPath(request()->url());
        // dd($aReturn);
        return view('users.list',$aReturn);
    }

    public function get_cities(Request $request,$state_id){
        $sSQL = 'SELECT id,name,state_id FROM cities WHERE state_id =' .$state_id.' AND country_id = '. 101;
        $aReturn["cities"] = DB::select($sSQL, array());
        return $aReturn;
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
            'type' => ''
        ];
         // dd($aReturn);
 
        if ($request->has('form_type') && $request->form_type == 'add_edit_user') {

            $rules = [
                'mobile' => 'required|digits:10',
                'status' => 'required',
                'type' => 'required'
            ];

            

            if ($iId > 0) {
                $rules['firstname'] = 'required';
                $rules['lastname'][] = Rule::unique('users')->ignore($iId, 'id')->where(function ($query) {
                    return $query->where('firstname', request()->input('firstname'));
                });
                $rules['email'] = 'required|email:rfc,dns|unique:users,email,'.$iId.',Id';
                $rules['password'] = 'nullable|confirmed|min:5';
            } else {
                $rules['firstname'] = 'required';
                $rules['lastname'] = [
                    'required',
                    Rule::unique('users')->where(function ($query) {
                        return $query->where('firstname', request()->input('firstname'));
                    }),
                ];
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
                // $user = User::find($iId);

                // if ($user) {
                //     $aReturn = $user->toArray();
                // }

                $Sql = 'SELECT id,firstname,lastname,email,mobile,is_active,type FROM  users WHERE id = '.$iId.' ';
                $aResult = DB::select($Sql);
                $aReturn['edit_data'] = !empty($aResult) ? $aResult[0] : [];
            }
          

            // $userRoles = DB::table('master_roles')->get()->toArray(); // Convert collection to array
            // $aReturn['type'] = $userRoles;
            // dd($aReturn);
            // Return the view with data
            return view('users.create', $aReturn);
        }
    }

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

    public function export_excel(Request $request)
    {       
        $filename = "user_report_" . time();
        return Excel::download(new UserExport(),  $filename.'.xlsx');
    }
}
