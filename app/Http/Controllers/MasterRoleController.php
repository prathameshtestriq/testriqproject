<?php

namespace App\Http\Controllers;

use App\Models\MasterRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Pagination\LengthAwarePaginator;

class MasterRoleController extends Controller
{
    public function clear_search()
    {
        session::forget('role_name');
        session::forget('role_status');
        return redirect('/role_master');
    }

    public function index(Request $request)
    {
        $a_return = array();
        $a_return['search_role_name'] = '';
        $a_return['search_role_status'] = '';

        if (isset($request->form_type) && $request->form_type == 'search_role_master') {
           
            session(['role_name' => $request->role_name]);
            session(['role_status' => $request->role_status]);

            return redirect('/role_master');
        }
        $a_return['search_role_name'] = (!empty(session('role_name'))) ? session('role_name') : '';
        $role_status = session('role_status');
        $a_return['search_role_status'] = (isset($role_status) && $role_status != '') ? $role_status : '';
   

        $CountRows = MasterRole::get_count($a_return);
        // dd($CountRows);
        $PageNo = request()->input('page', 1);
        $Limit = config('custom.per_page');
        // $Limit = 3;
        $a_return['Offset'] = ($PageNo - 1) * $Limit;
        
       
        $a_return["role_master"] = MasterRole::get_all($Limit,$a_return);
    //  dd($a_return["Remittance"][0]);
        $a_return['Paginator'] = new LengthAwarePaginator( $a_return["role_master"], $CountRows, $Limit, $PageNo);
        $a_return['Paginator']->setPath(request()->url());

        return view('role_master.list',$a_return);
    }

    public function add_edit(Request $request, $iId = 0){
        $a_return['name'] = '';      

        if (isset($request->form_type) && $request->form_type == 'add_edit_role_master') {
            $rules = [
                'role_name' => 'required'
            ];

            $request->validate($rules);


            if ($iId > 0) {
                MasterRole::update_role_master($iId, $request);
                $successMessage = ' Role Master Details Updated Successfully';
            }else{
              
                MasterRole::add_role_master($request);
                $successMessage = 'Role Master Details Added Successfully';
            }
            return redirect('/role_master')->with('success', $successMessage);

        }else{
            if($iId > 0){
            //   #SHOW EXISTING DETAILS ON EDIT
              $sSQL = 'SELECT id,name FROM role_master WHERE id=:id';
              $role_master_details = DB::select($sSQL, array( 'id' => $iId));
              $a_return = (array)$role_master_details[0];
             //dd($a_return );
            }
          }      
        
          
        return view('role_master.create',$a_return);
    }

    public function delete_role_master($iId){
        MasterRole::delete_role_master($iId);
        return redirect(url('/role_master'))->with('success', 'Role Master deleted successfully');
    }

    public function change_active_status(Request $request)
    {
        $aReturn = MasterRole::change_status_Role($request);
        // dd($aReturn);
        $successMessage  = 'Status changed successfully';
        $sucess = 'true';
        $aReturn = [];
        $aReturn['message'] =  $successMessage ;
        $aReturn['sucess'] = $sucess;
        return $aReturn;
    }
    
}
