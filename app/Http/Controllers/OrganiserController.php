<?php

namespace App\Http\Controllers;

use App\Models\MasterOrganiser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Pagination\LengthAwarePaginator;

class OrganiserController extends Controller
{
    // public function clear_search()
    // {
    //     session::forget('role_name');
    //     session::forget('role_status');
    //     return redirect('/role_master');
    // }

    public function index(Request $request)
    {
        $a_return = array();
        // $a_return['search_role_name'] = '';
        // $a_return['search_role_status'] = '';

        // if (isset($request->form_type) && $request->form_type == 'search_role_master') {
           
        //     session(['role_name' => $request->role_name]);
        //     session(['role_status' => $request->role_status]);

        //     return redirect('/role_master');
        // }
        // $a_return['search_role_name'] = (!empty(session('role_name'))) ? session('role_name') : '';
        // $role_status = session('role_status');
        // $a_return['search_role_status'] = (isset($role_status) && $role_status != '') ? $role_status : '';
   

        $CountRows = MasterOrganiser::get_count($a_return);
        // dd($CountRows);
        $PageNo = request()->input('page', 1);
        $Limit = config('custom.per_page');
        // $Limit = 3;
        $a_return['Offset'] = ($PageNo - 1) * $Limit;
        
       
        $a_return["OrganiserDetails"] = MasterOrganiser::get_all($Limit,$a_return);

        //dd($a_return);
    //  dd($a_return["Remittance"][0]);
        $a_return['Paginator'] = new LengthAwarePaginator( $a_return["OrganiserDetails"], $CountRows, $Limit, $PageNo);
        $a_return['Paginator']->setPath(request()->url());

        return view('organiser_master.list',$a_return);
    }

   
    
}
