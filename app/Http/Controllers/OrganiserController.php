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

    public function clear_search()
    {
        session::forget('organiser_name');
        session::forget('gst_number');
        session::forget('organiser_user_name');
        return redirect('/organiser_master');
    }

    public function index(Request $request)
    {
        $a_return = array();
        $a_return['search_organiser_name'] = '';
        $a_return['search_gst_number'] = '';
        $a_return['search_organiser_user_name'] = '';

        if (isset($request->form_type) && $request->form_type == 'search_user') {
           
            session(['organiser_name' => $request->organiser_name]);
            session(['gst_number' => $request->gst_number]);
            session(['organiser_user_name' => $request->organiser_user_name]);
            
            return redirect('/organiser_master');
        }
        $a_return['search_organiser_name'] = (!empty(session('organiser_name'))) ? session('organiser_name') : '';
        $a_return['search_gst_number'] = (!empty(session('gst_number'))) ? session('gst_number') : '';
        $a_return['search_organiser_user_name'] = (!empty(session('organiser_user_name'))) ? session('organiser_user_name') : '';
 
       
        $CountRows = MasterOrganiser::get_count($a_return);
        // dd($CountRows);
        $PageNo = request()->input('page', 1);
        $Limit = config('custom.per_page');
        // $Limit = 3;
        $a_return['Offset'] = ($PageNo - 1) * $Limit;
        
       
        $a_return["OrganiserDetails"] = MasterOrganiser::get_all($Limit,$a_return);
        $s_sql = 'SELECT u.id, CONCAT(u.firstname, " ", u.lastname) as name, u.is_active FROM users u WHERE u.is_active = 1 GROUP BY u.firstname, u.lastname';
        $a_return["UserDetails"] = DB::select($s_sql,array());
        // dd($a_return["UserDetails"] );
    //  dd($a_return["Remittance"][0]);
        $a_return['Paginator'] = new LengthAwarePaginator( $a_return["OrganiserDetails"], $CountRows, $Limit, $PageNo);
        $a_return['Paginator']->setPath(request()->url());

        return view('organiser_master.list',$a_return);
    }

    public function edit_organiser(Request $request){
       return view('organiser_master.edit_organiser');

    }

   
    
}
