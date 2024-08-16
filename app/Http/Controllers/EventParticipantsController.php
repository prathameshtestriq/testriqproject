<?php

namespace App\Http\Controllers;
use App\Exports\ParticipantsEventExport;
use App\Exports\ParticipantsEventRevenueExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;


use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class EventParticipantsController extends Controller
{

    public function clear_search($event_id)
    {
        session()->forget('participant_name');
        session()->forget('transaction_status');
        session()->forget('registration_id');
        session()->forget('mobile_no');
        session()->forget('email_id');
        session()->forget('category');
        session()->forget('start_booking_date');
        session()->forget('end_booking_date');
        session()->forget('transaction_order_id');
        return redirect('/participants_event/'.$event_id);
    }
    public function index(Request $request, $event_id=0)
    {
        $Return = array();
        $Return['search_participant_name'] = '';
        $Return['search_transaction_status'] = '';
        $Return['search_registration_id'] = '';
        $Return['search_mobile_no'] = '';
        $Return['search_email_id'] = ''; 
        $Return['search_category'] = '';
        $Return['search_start_booking_date'] = '';
        $Return['search_end_booking_date'] = '';
        $Return['search_transaction_order_id'] = '';
 
       
        if (isset($request->form_type) && $request->form_type == 'search_participant_event') {
         
            session(['participant_name' => $request->participant_name]);
            session(['transaction_status' => $request->transaction_status]);
            session(['registration_id' => $request->registration_id]);
            session(['mobile_no' => $request->mobile_no]);
            session(['email_id' => $request->email_id]);
            session(['category' => $request->category]);
            session(['start_booking_date' => $request->start_booking_date]);
            session(['end_booking_date' => $request->end_booking_date]);
            session(['transaction_order_id' => $request->transaction_order_id]);
            return redirect('/participants_event/'.$event_id);
        }

        $Return['search_participant_name'] = (!empty(session('participant_name'))) ? session('participant_name') : '';
        // $Return['search_transaction_status'] = (!empty(session('transaction_status'))) ? session('transaction_status'): '';
        $transaction_status = session('transaction_status');
        $Return['search_transaction_status'] = (isset($transaction_status) && $transaction_status != '') ? $transaction_status : '';
        $Return['search_registration_id'] = (!empty(session('registration_id'))) ? session('registration_id') : '';
        $Return['search_mobile_no'] = (!empty(session('mobile_no'))) ? session('mobile_no') : '';
        $Return['search_email_id'] = (!empty(session('email_id'))) ? session('email_id') : '';
        $Return['search_category'] = (!empty(session('category'))) ? session('category') : '';
        $Return['search_start_booking_date'] = (!empty(session('start_booking_date'))) ?  session('start_booking_date') : '';
        $Return['search_end_booking_date'] = (!empty(session('end_booking_date'))) ? session('end_booking_date'): '';
        $Return['search_transaction_order_id'] = (!empty(session('transaction_order_id'))) ? session('transaction_order_id'): '';
        
        // dd(session('transaction_status'),  $Return['search_transaction_status'] );
        

        $FiltersSql = '';
        if(!empty( $Return['search_participant_name'])){
            $FiltersSql .= ' AND (LOWER((CONCAT(a.firstname, " ", a.lastname))) LIKE \'%' . strtolower($Return['search_participant_name']) . '%\')';
        } 

        if(isset( $Return['search_transaction_status'])){
            $FiltersSql .= ' AND (LOWER(e.transaction_status) LIKE \'%' . strtolower($Return['search_transaction_status']) . '%\')';
        } 

        if(!empty( $Return['search_registration_id'])){
            $FiltersSql .= ' AND (LOWER(a.registration_id) LIKE \'%' . strtolower($Return['search_registration_id']) . '%\')';
        } 

        if(!empty( $Return['search_mobile_no'])){
            $FiltersSql .= ' AND (LOWER(a.mobile) LIKE \'%' . strtolower($Return['search_mobile_no']) . '%\')';
        } 

        if(!empty( $Return['search_email_id'])){
            $FiltersSql .= ' AND (LOWER(a.email) LIKE \'%' . strtolower($Return['search_email_id']) . '%\')';
            $FiltersSql .= ' OR (LOWER(a.mobile) LIKE \'%' . strtolower($Return['search_email_id']) . '%\')';
            
        } 

        if(!empty( $Return['search_category'])){
            $FiltersSql .= ' AND (LOWER((SELECT et.ticket_name FROM event_tickets et WHERE et.id = a.ticket_id)) LIKE \'%' . strtolower($Return['search_category']) . '%\')';
        } 

        if(!empty($Return['search_start_booking_date'])){
            $startdate = strtotime($Return['search_start_booking_date']);
            $FiltersSql .= " AND e.booking_date >= "." $startdate";
            // dd($sSQL);
        }

        if(!empty($Return['search_end_booking_date'])){
            $endDate = strtotime($Return['search_end_booking_date']);
            $FiltersSql .= " AND e.booking_date <="." $endDate";
            // dd($sSQL);
        }

        if(!empty( $Return['search_transaction_order_id'])){
            $FiltersSql .= ' AND (LOWER((SELECT bpd.txnid FROM booking_payment_details bpd WHERE bpd.id = e.booking_pay_id)) LIKE \'%' . strtolower($Return['search_transaction_order_id']) . '%\')';
        } 
        $PageNo = request()->input('page', 1);
        $Limit = config('custom.per_page');  
        // $Limit = 3;
        $Return['Offset'] = ($PageNo - 1) * $Limit;
        if($event_id > 0){
            $sSQL = 'SELECT count(a.id) as count
                FROM attendee_booking_details a
                LEFT JOIN booking_details AS b ON a.booking_details_id = b.id
                Inner JOIN event_booking AS e ON b.booking_id = e.id
                WHERE b.event_id = '.$event_id.''. $FiltersSql.' ORDER BY a.id DESC';
            // dd($sSQL);
            $CountsResult = DB::select($sSQL, array());

            $CountRows = 0;
            if (!empty($CountsResult)) {
                $CountRows = $CountsResult[0]->count;
            }
            // dd($count);
        
            $sSQL = 'SELECT a.*,e.booking_date,e.booking_pay_id, e.transaction_status, b.event_id,a.id AS aId, CONCAT(a.firstname, " ", a.lastname) AS user_name,
                (SELECT et.ticket_name FROM event_tickets et WHERE et.id = a.ticket_id) AS category_name,
                (SELECT bpd.txnid FROM booking_payment_details bpd WHERE bpd.id = e.booking_pay_id) AS Transaction_order_id,
                (SELECT bpd.amount FROM booking_payment_details bpd WHERE bpd.id = e.booking_pay_id) AS amount,
                (SELECT bpt.mihpayid FROM booking_payment_log bpt WHERE bpt.txnid = Transaction_order_id) AS payu_id
                FROM attendee_booking_details a
                LEFT JOIN booking_details AS b ON a.booking_details_id = b.id
                Inner JOIN event_booking AS e ON b.booking_id = e.id
                WHERE b.event_id = '.$event_id.''.$FiltersSql.' ORDER BY a.id DESC ';

            if ($Limit > 0) {
                $sSQL .= ' LIMIT ' . $Return['Offset'] . ',' . $Limit;
            }
            $Return['event_participants']  = DB::select($sSQL, array());
            // dd( $Return['event_participants']);
        }else{
            $sSQL = 'SELECT count(a.id) as count
            FROM attendee_booking_details a
            LEFT JOIN booking_details AS b ON a.booking_details_id = b.id
            Inner JOIN event_booking AS e ON b.booking_id = e.id
            WHERE 1=1'. $FiltersSql.' ORDER BY a.id DESC';
            // dd($sSQL);
            $CountsResult = DB::select($sSQL, array());

            $CountRows = 0;
            if (!empty($CountsResult)) {
                $CountRows = $CountsResult[0]->count;
            }
            $sSQL = 'SELECT a.*,e.booking_date,e.booking_pay_id, e.transaction_status, b.event_id,a.id AS aId, CONCAT(a.firstname, " ", a.lastname) AS user_name,
            (SELECT et.ticket_name FROM event_tickets et WHERE et.id = a.ticket_id) AS category_name,
            (SELECT bpd.txnid FROM booking_payment_details bpd WHERE bpd.id = e.booking_pay_id) AS Transaction_order_id,
            (SELECT bpd.amount FROM booking_payment_details bpd WHERE bpd.id = e.booking_pay_id) AS amount,
            (SELECT bpt.mihpayid FROM booking_payment_log bpt WHERE bpt.txnid = Transaction_order_id) AS payu_id
            FROM attendee_booking_details a
            LEFT JOIN booking_details AS b ON a.booking_details_id = b.id
            Inner JOIN event_booking AS e ON b.booking_id = e.id
            WHERE 1=1'.$FiltersSql.' ORDER BY a.id DESC ';
            if ($Limit > 0) {
                $sSQL .= ' LIMIT ' . $Return['Offset'] . ',' . $Limit;
            }
            $aResult = DB::select($sSQL, array());
            $Return['event_participants']  = !empty($aResult) ? $aResult : []
            
        }    

        // $Return['event_participants']  = DB::select($sSQL, array());
        // dd($Return['event_participants']);
        $sql = 'SELECT name FROM events where id ='.$event_id;
        $Return['event_name'] = DB::select($sql,array());
        // dd( $Return['event_name']);
        $Return['event_id']   = $event_id;

        // dd($Return['event_id']);
        $Return['Paginator'] = new LengthAwarePaginator( $Return['event_participants'], $CountRows, $Limit, $PageNo);
        $Return['Paginator']->setPath(request()->url());
       
       
        return view('participants_event.list', $Return);
    }

    public function delete_participants_event($event_id,$iId){
       if (!empty($iId)) {
           $sSQL = 'DELETE FROM `event_booking` WHERE id=:id';
           $Result = DB::delete(
               $sSQL,
               array(
                   'id' => $iId
               )
           );
           // dd($Result);
       }
       return redirect(url('participants_event/'.$event_id))->with('success', 'Participants event user deleted successfully');

    }


    public function export_event_participants(Request $request,$event_id)
    {         
        $filename = "participant_report_" . time();
        return Excel::download(new ParticipantsEventExport($event_id),  $filename.'.xlsx');
    }

    public function export_participants_revenue(Request $request,$event_id)
    {         
        $filename = "revenue_report_" . time();
        return Excel::download(new ParticipantsEventRevenueExport($event_id),  $filename.'.xlsx');
    }

    public function view(Request $request,$event_id,$attendance_id){
        $sSQL = 'SELECT abd.attendee_details FROM attendee_booking_details as abd where id=:id';
        $Return['attendance_booking_details'] = DB::select($sSQL, array('id'=>$attendance_id)); 
        $Return['event_id'] = $event_id;
        $Return['attendance_id'] = $attendance_id;

        $sSQL = 'SELECT id, name FROM countries WHERE 1=1';
        $Return["countries"] = DB::select($sSQL, array());

        $sSQL = 'SELECT id, name,country_id FROM states WHERE 1=1';
        $Return["states"] = DB::select($sSQL, array());

        $sSQL = 'SELECT id,name,state_id FROM cities WHERE 1=1';
        $Return["cities"] = DB::select($sSQL, array());

        // $sSQL = 'SELECT id, name,country_id FROM states WHERE country_id ='. 101;
        // $aReturn["states"] = DB::select($sSQL, array());

        return view('participants_event.participant_question',$Return);
    }
    public function get_states(Request $request,$country_id){
        $sSQL = 'SELECT id, name,country_id FROM states WHERE country_id ='. $country_id;
        $Return["states"] = DB::select($sSQL, array());
        // dd($Return["states"]);
        return $Return;
    }
    public function get_cities(Request $request,$state_id){
        $sSQL = 'SELECT id,name,state_id FROM cities WHERE state_id =' .$state_id.' AND country_id = '. 101;
        $Return["cities"] = DB::select($sSQL, array());
        return $Return;
    }
    public function Edit_question(Request $request,$event_id,$attendance_id){
            // Extract data from the request
            if (isset($request->form_type) && $request->form_type == 'edit_question') {
    
                $jsonString = $request->dataArray;
                $dataString = json_decode($jsonString, true);
                $dataArray = json_decode($dataString, true);
                // dd($dataArray);
               
                foreach ($dataArray as &$question) {
                    // Check if the form has inputs corresponding to the question
                    $questionLabel = $question['question_label'];   
                   
                    if (array_key_exists($questionLabel, $request->input('text', []))) {
                        $question['ActualValue'] = $request->input('text')[$questionLabel];
                    } elseif ($question['question_form_type'] == 'date' && $request->has('date')) {
                        $question['ActualValue'] = $request->input('date');
                    } elseif ($question['question_form_type'] == 'textarea' && $request->has('textarea')) {
                        $question['ActualValue'] = $request->input('textarea');
                    } elseif ($question['question_form_type'] == 'select' && $request->input('select', [])) {  
                        $question['ActualValue'] = $request->input('select')[$questionLabel];
                    } elseif ($question['question_form_type'] == 'radio' && $request->input('radio', [])) {   
                        $question['ActualValue'] = $request->input('radio')[$questionLabel];   
                    } elseif ($question['question_form_type'] == 'countries' && $request->has('countries')) {
                        $question['ActualValue'] = $request->input('countries');
                    } elseif ($question['question_form_type'] == 'states' && $request->has('states')) {
                        $question['ActualValue'] = $request->input('states');
                    } elseif ($question['question_form_type'] == 'cities' && $request->has('cities')) {
                        $question['ActualValue'] = $request->input('cities');
                    } elseif (array_key_exists($questionLabel, $request->input('mobile', []))) {
                        $question['ActualValue'] = $request->input('mobile')[$questionLabel];  
                    }
                }
                // dd($dataArray);
                $JsonStrings = json_encode($dataArray);
                $updatedJsonString = json_encode($JsonStrings);
                // dd($updatedJsonString);
                
                
                // dd($updatedJsonString);
                $ssql = 'UPDATE attendee_booking_details SET 
                firstname = :firstname,
                lastname =:lastname,
                attendee_details = :attendee_details,
                created_at =:created_at
                WHERE id=:id';
        
                $bindings = array(
                    'firstname' => !empty($request->input('text')['First Name'])? $request->input('text')['First Name']:' ',
                    'lastname' => !empty($request->input('text')['Last Name'])?$request->input('text')['Last Name']:' ',
                    'attendee_details' => !empty($updatedJsonString )? $updatedJsonString :' ',
                    'created_at'=>strtotime('now'),
                    'id' => $attendance_id
                );
                // dd($bindings);
                $Result = DB::update($ssql, $bindings);
                $successMessage = '  Attendance Details Update Successfully';
                return redirect('/participants_event/'.$event_id)->with('success', $successMessage);
            }
        
     
        return redirect(url('participants_event/'.$event_id));
        // ->with('success', 'Participants event user deleted successfully');
    }
    
    

}
