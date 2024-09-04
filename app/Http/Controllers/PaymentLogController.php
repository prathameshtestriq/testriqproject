<?php

namespace App\Http\Controllers;

use App\Exports\PaymentLogExport;
use App\Models\PaymentLogModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;

class PaymentLogController extends Controller
{
    public function clear_search()
    {
        session::forget('user_name');
        session::forget('start_payment_date');
        session::forget('end_payment_date');
        session::forget('transaction_id_payment');
        session::forget('transaction_status_payment');

        return redirect('/payment_log');
    }

    public function index(Request $request){

        $aReturn = array();
        $aReturn['search_user_name'] = '';
        $aReturn['search_start_payment_date'] = '';
        $aReturn['search_end_payment_date'] = '';
        $aReturn['search_transaction_id_payment'] = '';
        $aReturn['search_transaction_status_payment'] = '';

        if(isset($request->form_type) && $request->form_type ==  'search_payment') {
            session(['user_name' => $request->name]);
            session(['start_payment_date' => $request->start_payment_date]);
            session(['end_payment_date' => $request->end_payment_date]);
            session(['transaction_id_payment' => $request->transaction_id_payment]); 
            session(['transaction_status_payment' => $request->transaction_status_payment]);
            
            return redirect('/payment_log');
        }
        $aReturn['search_user_name'] =  (!empty(session('user_name'))) ? session('user_name') : '';
        $aReturn['search_start_payment_date'] = (!empty(session('start_payment_date'))) ?  session('start_payment_date') : '';
        $aReturn['search_end_payment_date'] = (!empty(session('end_payment_date'))) ? session('end_payment_date'): '';
        $aReturn['search_transaction_id_payment'] = (!empty(session('transaction_id_payment'))) ?  session('transaction_id_payment') : '';
        $aReturn['search_transaction_status_payment'] = (!empty(session('transaction_status_payment'))) ? session('transaction_status_payment'): '';
    
        // dd( $aReturn['search_transaction_id_payment']  , $aReturn['search_transaction_status_payment']);
        
        $CountRows = PaymentLogModel::get_count($aReturn);
        $PageNo = request()->input('page', 1);
        $Limit = config('custom.per_page');
        $aReturn['Offset'] = ($PageNo - 1) * $Limit;

        $aReturn["payment_array"] = PaymentLogModel::get_all($Limit, $aReturn);  
        // dd($aReturn["payment_array"]); 
    
        $aReturn['Paginator'] = new LengthAwarePaginator($aReturn['payment_array'], $CountRows, $Limit, $PageNo);
        $aReturn['Paginator']->setPath(request()->url());

        return view('payment_log.list',$aReturn);
    }

    public function export_payment_log(Request $request)
    {         
        $filename = "payment_report_" . time();
        return Excel::download(new PaymentLogExport(), $filename.'.xlsx');
    }

}
