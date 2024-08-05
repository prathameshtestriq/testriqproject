<?php

namespace App\Http\Controllers;

use App\Exports\RegistrationSuccessfulExport;
use App\Models\RegistrationSuccessfulModel;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Pagination\LengthAwarePaginator;


class RegistrationSuccessfulController extends Controller
{

    public function clear_search($event_id)
    {

        session()->forget('registration_user_name');
        session()->forget('registration_transaction_status');
        session()->forget('start_registration_booking_date');
        session()->forget('end_registration_booking_date');
       
        return redirect('/registration_successful/'.$event_id);
    }
    public function index(Request $request, $event_id){
        $Return['search_registration_user_name'] = '';
        $Return['search_registration_transaction_status'] = '';
        $Return['search_start_registration_booking_date'] = '';
        $Return['search_end_registration_booking_date'] = '';
       

        if (isset($request->form_type) && $request->form_type == 'search_registration_successful') {
         
            session(['registration_user_name' => $request->registration_user_name]);
            session(['registration_transaction_status' => $request->registration_transaction_status]);
            session(['start_registration_booking_date' => $request->start_registration_booking_date]);
            session(['end_registration_booking_date' => $request->end_registration_booking_date]);
        
            return redirect('/registration_successful/'.$event_id);
        }
       
        $Return['search_registration_user_name'] = (!empty(session('registration_user_name'))) ? session('registration_user_name') : '';
        $transaction_status = session('registration_transaction_status');
        $Return['search_registration_transaction_status'] = (isset($transaction_status) && $transaction_status != '') ? $transaction_status : '';
        $Return['search_start_registration_booking_date'] = (!empty(session('start_registration_booking_date'))) ?  session('start_registration_booking_date') : '';
        $Return['search_end_registration_booking_date'] = (!empty(session('end_registration_booking_date'))) ? session('end_registration_booking_date'): '';
        
        $CountRows = RegistrationSuccessfulModel::get_count($event_id,$Return);

        $PageNo = request()->input('page', 1);
        $Limit = config('custom.per_page');
        // $Limit = 3;
        $Return['Offset'] = ($PageNo - 1) * $Limit;


        $Return["Registration_successful"] = RegistrationSuccessfulModel::get_all($Limit,$event_id,$Return);
        $Return['event_id']   = $event_id;
        $Return['Paginator'] = new LengthAwarePaginator($Return["Registration_successful"] , $CountRows, $Limit, $PageNo);
        $Return['Paginator']->setPath(request()->url());

      
        return view('registration_successful.list',$Return);
    }

    public function export_registration_successful(Request $request,$event_id)
    {         
        
        return Excel::download(new RegistrationSuccessfulExport($event_id), 'Registration successful.xlsx');
    }
}
