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
        session::forget('country'); 
        session::forget('state'); 
        session::forget('city'); 
        session::forget('status'); 
        session::forget('gender'); 
        session::forget('role'); 
        session::forget('rows'); 
        return redirect('/users');
    }

    public function index(Request $request){
        $aReturn = array();
        $aReturn['search_name'] = '';
        $aReturn['search_email_id'] ='';
        $aReturn['search_mobile'] = '';
        $aReturn['search_country'] = '';
        $aReturn['search_state'] = '';
        $aReturn['search_city'] = '';
        $aReturn['search_gender'] = '';
        $aReturn['search_rows'] = '';
        $aReturn['search_role'] = '';

        if(isset($request->form_type) && $request->form_type ==  'search_user'){
            // dd($request->gender);
           session(['name' => $request->name]);
           session(['email' => $request->email_id]);
           session(['mobile' => $request->mobile_no]);
           session(['country' => $request->country]);
           session(['state' => $request->state]);
           session(['city' => $request->city]);
           session(['status' => $request->status]);
           session(['gender' => $request->gender]);
           session(['role' => $request->role]);
           session(['rows' => $request->rows]);

            return redirect('/users');
        }
        $aReturn['search_name'] =  (!empty(session('name'))) ? session('name') : '';
        $aReturn['search_email_id'] = (!empty(session('email'))) ? session('email') : '';
        $aReturn['search_mobile'] =  (!empty(session('mobile'))) ? session('mobile') : '';
        $status = session('status');
        $aReturn['search_status'] = (isset($status) && $status != '') ? $status : '';
        $aReturn['search_country'] =  (!empty(session('country'))) ? session('country') :'';
        $aReturn['search_state'] =  (!empty(session('state'))) ? session('state') : '';
        $aReturn['search_city'] =  (!empty(session('city'))) ? session('city') : '';
        $aReturn['search_gender'] =  (!empty(session('gender'))) ? session('gender') : '';
        $aReturn['search_role'] =  (!empty(session('role'))) ? session('role') : '';
        $aReturn['search_rows'] =  (!empty(session('rows'))) ? session('rows') : '';

        // dd($aReturn['search_country']);
        $CountRows=User::get_count($aReturn);
        $PageNo = request()->input('page', 1);
        $Limit = !empty($aReturn['search_rows']) ? $aReturn['search_rows'] : config('custom.per_page');
        // $Limit = 3;

        $aReturn['Offset'] = ($PageNo - 1) * $Limit;

        $aReturn["user_array"] =User::get_all($Limit,$aReturn);
        // dd($aReturn["user_array"]);
        // $sSQL = 'SELECT id, name,country_id FROM states WHERE country_id ='. 101;
        // $aReturn["states"] = DB::select($sSQL, array());
  
        $sSQL = 'SELECT id, name FROM countries WHERE 1=1';
        $aReturn["countries"] = DB::select($sSQL, array());

        $sSQL = 'SELECT id,name FROM role_master WHERE status = 1 AND is_deleted = 0';
        $aReturn["role_details"] = DB::select($sSQL, array());

        $aReturn['Paginator'] = new LengthAwarePaginator($aReturn['user_array'], $CountRows, $Limit, $PageNo);
        $aReturn['Paginator']->setPath(request()->url());
        // dd($aReturn);
        return view('users.list',$aReturn);
    }



    public function add_edit(Request $request, $iId = 0){
        $aReturn['id'] = '';
        $aReturn['firstname'] = '';
        $aReturn['lastname'] = '';
        $aReturn['email'] = '';
        $aReturn['contact_number'] = '';
        $aReturn['username'] = '';
        $aReturn['password'] = '';
        $aReturn['type'] = '';
        $aReturn['Tds'] = '';
        $aReturn['country'] = '';
        $aReturn['state'] = '';
        $aReturn['city'] = '';
        $aReturn['gender'] = '';
        $aReturn['role'] = '';
       
        if ($request->has('form_type') && $request->form_type == 'add_edit_user') {

            $rules = [
                'contact_number' => 'required|digits:10',
                'city'=>'required',
                'country'=>'required',
                'state'=>'required',
                'dob' => [
                    'required',
                    'date',
                    function ($attribute, $value, $fail) {
                        $minAge = 18;
                        $maxAge = 99;
                        $birthDate = \Carbon\Carbon::parse($value);
                        $age = $birthDate->age;
            
                        if ($age < $minAge || $age > $maxAge) {
                            $fail('You must be between 18 and 99 years old.');
                        }
                    }
                ],
                'type' => 'required',
                'gender'=> 'required',
            ];

            if ($iId > 0) {
                $rules['firstname'] = 'required|regex:/^[a-zA-Z\s]+$/';
                $rules['lastname'] = 'required';           
                $rules['email'] = 'required|email:rfc,dns|unique:users,email,'.$iId.',Id';
                $rules['password'] = 'nullable|confirmed|min:5';
            } else { 
                $rules['firstname'] = 'required';
                $rules['lastname'] = 'required';
                $rules['email'] = 'required|email:rfc,dns|unique:users';
                $rules['password'] = 'required|confirmed|min:5';
            }

          

            $request->validate($rules);


            if ($iId > 0) {
                User::update_user($iId, $request);
                $successMessage = 'User updated successfully';
            }else{
                User::add_user($request);
                $successMessage = 'User added successfully';
            }
            return redirect('/users')->with('success', $successMessage);

        }else{
            if ($iId > 0) { 

                $Sql = 'SELECT id,firstname,lastname,email,state,city,mobile,is_active,dob,country,type,role,(SELECT name FROM countries WHERE id = u.country)As country_name,(SELECT name FROM states WHERE id = u.state)As state_name,(SELECT name FROM cities WHERE id = u.city)As city_name,gender FROM  users u WHERE id = '.$iId.' ';
                $aResult = DB::select($Sql);
                $aReturn['edit_data'] = !empty($aResult) ? $aResult[0] : [];
                // dd( $aReturn['edit_data']);
            }  
        }      
         
   
          $sSQL = 'SELECT id, name FROM countries WHERE 1=1';
          $aReturn["countries"] = DB::select($sSQL, array());
  
          $sSQL = 'SELECT id,name FROM role_master WHERE status = 1 AND is_deleted = 0';
          $aReturn["role_details"] = DB::select($sSQL, array());
       
          return view('users.create', $aReturn);
    }

 
    public function get_states(Request $request){
        $countryId = $request->get('country_id');
        // dd($countryId);
        $sSQL = 'SELECT id, name,country_id FROM states WHERE country_id ='. $countryId;
        $states = DB::select($sSQL, array());
        return response()->json($states);
        //dd($Return["states"]);
        // return $Return;
    }
    public function get_cities(Request $request){
        $stateId = $request->get('state_id');
        $sSQL = 'SELECT id,name,state_id FROM cities WHERE state_id =' .$stateId;
        $cities = DB::select($sSQL, array());
        return response()->json($cities);
        // return $Return;
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
