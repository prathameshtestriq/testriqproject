<?php

namespace App\Http\Controllers;

use App\Exports\RegistrationSuccessfulExport;
use App\Models\RegistrationSuccessfulModel;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;


class RegistrationSuccessfulController extends Controller
{

    public function clear_search($event_id)
    {

        session()->forget('registration_user_name');
        session()->forget('registration_transaction_status');
        session()->forget('start_registration_booking_date');
        session()->forget('end_registration_booking_date');
        session()->forget('registration_email_id');
        session()->forget('registration_mobile_no');
        

       
        return redirect('/registration_successful/'.$event_id);
    }
    public function index(Request $request, $event_id = 0){
        $Return['search_registration_user_name'] = '';
        $Return['search_registration_transaction_status'] = '';
        $Return['search_registration_email'] = '';
        $Return['search_registration_mobile'] = '';
        $Return['search_start_registration_booking_date'] = '';
        $Return['search_end_registration_booking_date'] = '';
       
        if (isset($request->form_type) && $request->form_type == 'search_registration_successful') {
            
            session(['registration_user_name' => $request->registration_user_name]);
            session(['registration_transaction_status' => $request->registration_transaction_status]);
            session(['registration_email_id' => $request->registration_email_id]);
            session(['registration_mobile_no' => $request->registration_mobile_no]);
            session(['start_registration_booking_date' => $request->start_registration_booking_date]);
            session(['end_registration_booking_date' => $request->end_registration_booking_date]);
        
            return redirect('/registration_successful/'.$event_id);
        }
    
        $Return['search_registration_user_name'] = (!empty(session('registration_user_name'))) ? session('registration_user_name') : '';
        $transaction_status = session('registration_transaction_status');
        $Return['search_registration_transaction_status'] = (isset($transaction_status) && $transaction_status != '') ? $transaction_status : '';
        $Return['search_registration_email'] = (!empty(session('registration_email_id'))) ? session('registration_email_id') : '';
        $Return['search_registration_mobile'] = (!empty(session('registration_mobile_no'))) ? session('registration_mobile_no') : '';
        $Return['search_start_registration_booking_date'] = (!empty(session('start_registration_booking_date'))) ?  session('start_registration_booking_date') : '';
        $Return['search_end_registration_booking_date'] = (!empty(session('end_registration_booking_date'))) ? session('end_registration_booking_date'): '';
        
       if($event_id >0){
        
            $CountRows = RegistrationSuccessfulModel::get_count_event_registration($event_id,$Return);
            $PageNo = request()->input('page', 1);
            $Limit = config('custom.per_page');
            // $Limit = 3;
            $Return['Offset'] = ($PageNo - 1) * $Limit;
            $Return["Registration_successful"] = RegistrationSuccessfulModel::get_all_event_registration($Limit,$event_id,$Return);
           
        }else{
            
            $CountRows = RegistrationSuccessfulModel::get_all_count($event_id,$Return);
            $PageNo = request()->input('page', 1);
            $Limit = config('custom.per_page');
            $Return['Offset'] = ($PageNo - 1) * $Limit;   
            $Return["Registration_successful"] = RegistrationSuccessfulModel::get_all($Limit,$event_id,$Return);
          
        }    
        $sql = 'SELECT name FROM events where id ='.$event_id;
        $Return['event_name'] = DB::select($sql,array());
        // dd( $Return['event_name']);
       
        $Return['event_id']   = !empty($event_id) ? $event_id : 0;
        $Return['Paginator'] = new LengthAwarePaginator($Return["Registration_successful"] , $CountRows, $Limit, $PageNo);
        $Return['Paginator']->setPath(request()->url());
      
        return view('registration_successful.list',$Return);
    }

    public function export_registration_successful(Request $request,$event_id)
    {         
        $filename = "registration_report_" . time();
        return Excel::download(new RegistrationSuccessfulExport($event_id), $filename.'.xlsx');
    }
}
