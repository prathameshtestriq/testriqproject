<?php

namespace App\Http\Controllers;

use App\Exports\RemittanceManagementExport;
use App\Exports\RemittanceSampleExport;
use App\Imports\RemittanceDetailsImport;
use App\Models\RemittanceManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;

class RemittanceManagementController extends Controller
{
    public function clear_search()
    {
        session::forget('remittance_name');
        session::forget('start_remittance_date');
        session::forget('end_remittance_date');
        session::forget('remittance_status');
        session::forget('event');
        return redirect('/remittance_management');
    }

    public function index(Request $request)
    {
        $a_return = array();
        $a_return['search_remittance_name'] = '';
        $a_return['search_start_remittance_date'] = '';
        $a_return['search_end_remittance_date'] = '';
        $a_return['search_remittance_status'] = '';
        $a_return['search_event_id'] = '';
        

        if (isset($request->form_type) && $request->form_type == 'search_remittance_management') {
            session(['remittance_name' => $request->remittance_name]);
            session(['start_remittance_date' => $request->start_remittance_date]);
            session(['end_remittance_date' => $request->end_remittance_date]);
            session(['remittance_status' => $request->remittance_status]);
            session(['event' => $request->event]);

            return redirect('/remittance_management');
        }
        $a_return['search_remittance_name'] = (!empty(session('remittance_name'))) ? session('remittance_name') : '';
        $a_return['search_start_remittance_date'] = (!empty(session('start_remittance_date'))) ?  session('start_remittance_date') : '';
        $a_return['search_end_remittance_date'] = (!empty(session('end_remittance_date'))) ? session('end_remittance_date'): '';
        $remittance_status = session('remittance_status');
        $a_return['search_remittance_status'] = (isset($remittance_status) && $remittance_status != '') ? $remittance_status : '';
        $a_return['search_event_id'] = (!empty(session('event'))) ? session('event'): '';

        $CountRows = RemittanceManagement::get_count($a_return);
        // dd($CountRows);
        $PageNo = request()->input('page', 1);
        $Limit = config('custom.per_page');
        // $Limit = 3;
        $a_return['Offset'] = ($PageNo - 1) * $Limit;
        
       
        $a_return["Remittance"] = RemittanceManagement::get_all_remittance($Limit,$a_return);
    //  dd($a_return["Remittance"][0]);
        $a_return['Paginator'] = new LengthAwarePaginator($a_return['Remittance'], $CountRows, $Limit, $PageNo);
        $a_return['Paginator']->setPath(request()->url());

        $SQL = "SELECT id,name FROM events WHERE active=1 AND deleted = 0";
        $a_return['EventsData'] = DB::select($SQL, array());

        return view('remittancemanagement.list',$a_return);
    }

    public function add_edit(Request $request, $iId = 0){
        $a_return['remittance_name'] = '';
        $a_return['remittance_date'] = '';
        $a_return['gross_amount'] = '';
        $a_return['service_charge'] = '';
        $a_return['Sgst'] = '';
        $a_return['Cgst'] = '';
        $a_return['Igst'] = '';
        $a_return['deductions'] = '';
        $a_return['Tcs'] = '';
        $a_return['Tds'] = '';
        $a_return['amount_remitted'] = '';
        $a_return['bank_reference'] = '';
        $a_return['event_id'] = '';
       
     

        if (isset($request->form_type) && $request->form_type == 'add_edit_remittance_management') {
            $rules = [
                'remittance_name' => 'required|unique:remittance_management,remittance_name,'.$iId.',id',
                'remittance_date' => 'required',
                'event'=> 'required',
                'gross_amount' => 'required|numeric',
                // 'service_charge' => 'numeric',
                // 'Sgst' => 'numeric',
                // 'Cgst' => 'numeric',
                // 'Igst' => 'numeric',
                // 'deductions' => 'numeric',
                // 'Tcs' => 'numeric',
                // 'Tds' => 'numeric',
                // 'amount_remitted' => 'numeric',
                // 'bank_reference' => 'regex:/^[a-zA-Z\s]+$/',
            ];

            $message = [
                'bank_reference.regex' => 'The bank reference must contain only letters.',
            ]; 
            $request->validate($rules,$message);

            if ($iId > 0) {
                RemittanceManagement::update_remittance_management($iId, $request);
                $successMessage = ' Remittance Management Details Updated Successfully';
            }else{
              
                RemittanceManagement::add_remittance_management($request);
                $successMessage = 'Remittance Management Details Added Successfully';
            }
            return redirect('/remittance_management')->with('success', $successMessage);

        }else{
            if($iId > 0){
            //   #SHOW EXISTING DETAILS ON EDIT
              $sSQL = 'SELECT id,remittance_name,remittance_date,gross_amount,service_charge,Sgst,Cgst,Igst,deductions,Tds, amount_remitted, bank_reference,event_id,Tcs FROM remittance_management WHERE id=:id';
              $remittance_management_details = DB::select($sSQL, array( 'id' => $iId));
              $a_return = (array)$remittance_management_details[0];
            //   dd(  $remittance_management_details);
            }
        }      
        
        $SQL = "SELECT id,name FROM events WHERE active=1 AND deleted = 0";
        $a_return['EventsData'] = DB::select($SQL, array());
       
        return view('remittancemanagement.create',$a_return);
    }

    public function delete_remittance_management($iId){
        RemittanceManagement::delete_remittance_management($iId);
        return redirect(url('/remittance_management'))->with('success', 'remittance management deleted successfully');
    }

    public function change_active_status(Request $request)
    {
        $aReturn = RemittanceManagement::change_status_remittance_management($request);
        // dd($aReturn);

        $successMessage  = 'Status changed successfully';
        $sucess = 'true';
        $aReturn = [];
        $aReturn['message'] =  $successMessage ;
        $aReturn['sucess'] = $sucess;
        return $aReturn;
    }
    
    //-------------- remittance Export ---------------------
    public function export_remittance_management(){
        $filename = "remittance_report_" . time();
        return Excel::download(new RemittanceManagementExport(),  $filename.'.xlsx');
    }
  

      //-------------- remittance import ---------------------
      public function import_remittance_management(Request $request)
      {

        $rules = [
            'rem_file' => 'required'
        ];

        $messages = [
            'rem_file.required' => 'The file is required. Please upload a file.'
        ];
        $request->validate($rules,$messages);
        
        $AllowedFormats = array('csv','xlsx','xls');
        if(in_array(request()->file('rem_file')->getClientOriginalExtension(), $AllowedFormats)){
            Excel::import(new RemittanceDetailsImport(), request()->file('rem_file'), \Maatwebsite\Excel\Excel::XLSX);
            return redirect()->route('remittance_management_index');
        }else{
            return redirect()->route('remittance_management_index')->with('error', 'Invalid file format. Allowed formats '.implode(', ',$AllowedFormats) );   
        }    
      }   
}
