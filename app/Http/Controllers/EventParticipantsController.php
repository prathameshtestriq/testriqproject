<?php

namespace App\Http\Controllers;
use App\Exports\ParticipantsEventExport;
use App\Exports\ParticipantsEventRevenueExport;
use App\Exports\AttendeeDetailsDataExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Master;
use \stdClass;

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
        session()->forget('event_name');
        return redirect('/participants_event/'.$event_id);
    }
    public function index(Request $request, $event_id=0)
    {
        // dd($event_id);
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
        $Return['search_event'] = '';
 
       
        if (isset($request->form_type) && $request->form_type == 'search_participant_event') {
        //  dd($request->category);
            session(['participant_name' => $request->participant_name]);
            session(['transaction_status' => $request->transaction_status]);
            session(['registration_id' => $request->registration_id]);
            session(['mobile_no' => $request->mobile_no]);
            session(['email_id' => $request->email_id]);
            session(['category' => $request->category]);
            session(['start_booking_date' => $request->start_booking_date]);
            session(['end_booking_date' => $request->end_booking_date]);
            session(['transaction_order_id' => $request->transaction_order_id]);
            session(['event_name' => $request->event_name]);
            return redirect('/participants_event/'.$event_id);
        }

        $Return['search_participant_name'] = (!empty(session('participant_name'))) ? session('participant_name') : '';
        $transaction_status = session('transaction_status');
        $Return['search_transaction_status'] = (isset($transaction_status) && $transaction_status != '') ? $transaction_status : '';
        $Return['search_registration_id'] = (!empty(session('registration_id'))) ? session('registration_id') : '';
        $Return['search_mobile_no'] = (!empty(session('mobile_no'))) ? session('mobile_no') : '';
        $Return['search_email_id'] = (!empty(session('email_id'))) ? session('email_id') : '';
        $Return['search_category'] = (!empty(session('category'))) ? session('category') : '';
        $Return['search_start_booking_date'] = (!empty(session('start_booking_date'))) ?  session('start_booking_date') : '';
        $Return['search_end_booking_date'] = (!empty(session('end_booking_date'))) ? session('end_booking_date'): '';
        $Return['search_transaction_order_id'] = (!empty(session('transaction_order_id'))) ? session('transaction_order_id'): '';
        $Return['search_event'] = (!empty(session('event_name'))) ? session('event_name'): '';
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
        
        $serach_event_id = '';
        if(!empty($Return['search_event'])){
            $serach_event_id = $Return['search_event'];
        }
        
        $sSQL1 = 'SELECT count(a.id) as count
            FROM attendee_booking_details a
            LEFT JOIN booking_details AS b ON a.booking_details_id = b.id
            Inner JOIN event_booking AS e ON b.booking_id = e.id
            WHERE 1=1';
        // dd($sSQL);
        if(!empty($event_id)) {
            $sSQL1 .= ' AND b.event_id = '.$event_id;
        }

        if(!empty($serach_event_id)) {
            $sSQL1 .= ' AND b.event_id = '.$serach_event_id;
        }
        $sSQL1 .= ' '.$FiltersSql.' ORDER BY a.id DESC';

        $CountsResult = DB::select($sSQL1, array());
        
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
            WHERE 1=1';

        if(!empty($event_id)) {
            $sSQL .= ' AND b.event_id = '.$event_id;
        }
        if(!empty($serach_event_id)) {
            $sSQL .= ' AND b.event_id = '.$serach_event_id;
        }
        $sSQL .= ' '.$FiltersSql.' ORDER BY a.id DESC';

        if ($Limit > 0) {
            $sSQL .= ' LIMIT ' . $Return['Offset'] . ',' . $Limit;
        }
        $Return['event_participants']  = DB::select($sSQL, array());
         
        // $Return['event_participants']  = DB::select($sSQL, array());
        // dd($Return['event_participants']);
        $sql = 'SELECT name FROM events where id ='.$event_id;
        $Return['event_name'] = DB::select($sql,array());
        // dd( $Return['event_name']);
        $Return['event_id']   = !empty($event_id) ? $event_id : 0;

        $sql = 'SELECT et.ticket_name FROM event_tickets et WHERE 1=1 GROUP BY et.ticket_name';
        $Return['Categories'] = DB::select($sql,array());
        // dd($Return['Categories']);
        // dd($Return['event_id']);
        $Return['Paginator'] = new LengthAwarePaginator( $Return['event_participants'], $CountRows, $Limit, $PageNo);
        $Return['Paginator']->setPath(request()->url());

        //----------------
        // dd($Return['search_event']);

        $sql = "SELECT *,a.id AS aId,e.total_amount,(SELECT ticket_name FROM event_tickets WHERE id=a.ticket_id) AS TicketName,(SELECT et.ticket_name FROM event_tickets et WHERE et.id = a.ticket_id) AS category_name, (SELECT ticket_status FROM event_tickets WHERE id=a.ticket_id) AS ticket_status FROM attendee_booking_details AS a 
                LEFT JOIN booking_details AS b ON a.booking_details_id = b.id
                LEFT JOIN event_booking AS e ON b.booking_id = e.id WHERE 1=1 ";
        
        if(!empty($event_id)){
            $sql .= " AND b.event_id = ".$event_id." ";
        }

        if(!empty($Return['search_event'])) {
            $sql .= ' AND b.event_id = '.$Return['search_event'];
        }

        if(!empty($Return['search_participant_name'])){
            $sql .= ' AND (LOWER((CONCAT(a.firstname, " ", a.lastname))) LIKE \'%' . strtolower($Return['search_participant_name']) . '%\')';
        } 

        if(!empty($Return['search_transaction_status'])){
            $sql .= ' AND e.transaction_status = '.$Return['search_transaction_status'];
        }

        if(!empty($Return['search_registration_id'])){
            $sql .= ' AND (LOWER(a.registration_id) LIKE \'%' . strtolower($Return['search_registration_id']) . '%\')';
        }  

        if(!empty( $Return['search_email_id'])){
            $sql .= ' AND (LOWER(a.email) LIKE \'%' . strtolower($Return['search_email_id']) . '%\')';
            $sql .= ' OR (LOWER(a.mobile) LIKE \'%' . strtolower($Return['search_email_id']) . '%\')'; 
        } 

        if(!empty( $Return['search_category'])){
            $sql .= ' AND (LOWER((SELECT et.ticket_name FROM event_tickets et WHERE et.id = a.ticket_id)) LIKE \'%' . strtolower($Return['search_category']) . '%\')';
        } 

        if(!empty( $Return['search_transaction_order_id'])){
            $sql .= ' AND (LOWER((SELECT bpd.txnid FROM booking_payment_details bpd WHERE bpd.id = e.booking_pay_id)) LIKE \'%' . strtolower($Return['search_transaction_order_id']) . '%\')';
        } 

        if(!empty($Return['search_start_booking_date'])){
            $startdate = strtotime($Return['search_start_booking_date']);
            $sql .= " AND e.booking_date >= "." $startdate";
        }

        if(!empty($Return['search_end_booking_date'])){
            $endDate = strtotime($Return['search_end_booking_date']);
            $sql .= " AND e.booking_date <="." $endDate";
        }

        // dd($sql);
        $sql .= " ORDER BY a.id DESC";
        $AttendeeData = DB::select($sql, array());

        // dd($AttendeeData);

        foreach ($AttendeeData as $key => $value) {
            $value->booking_date = !empty($value->created_at) ? date("d-m-Y H:i A", ($value->created_at)) : '';

            if(!empty($value->attendee_details)){
                // dd(json_decode(json_decode($value->attendee_details)));
                $new_mobile_no = '';
                foreach(json_decode(json_decode($value->attendee_details)) as $res){
                    if($res->question_form_type == 'mobile' && $res->question_label == 'Mobile Number'){
                        $new_mobile_no = $res->ActualValue;
                        break;
                    }
                }
                $value->mobile = $new_mobile_no;
            }
        }
        
        if(!empty($serach_event_id) || !empty($event_id)){
            $Return['ParticipantsExcelLink'] = EventParticipantsController::participants_excel_export($AttendeeData, $event_id, $serach_event_id);
        }
        
        // $Return['ParticipantsExcelLink'] = '';
        // dd($Return['ParticipantsExcelLink']);
        $SQL = "SELECT id,name FROM events WHERE active=1 AND deleted = 0";
        $Return['EventsData'] = DB::select($SQL, array());

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
        // dd($event_id);
    }

    function participants_excel_export($AttendeeData,$event_id, $serach_event_id)
    {         
       // dd($Return['search_participant_name']);
        $master = new Master();
       
        // dd($AttendeeData);

        if (!empty($AttendeeData)) {

            $ExcellDataArray = [];
            $sql = "SELECT id,question_label,question_form_type,question_form_name,(select name from events where id = event_form_question.event_id) as event_name FROM event_form_question WHERE question_status = 1 ";
           
            if(!empty($event_id)){
                $sql .= " AND event_id = ".$event_id." ";
            }

            if(!empty($serach_event_id)) {
                $sql .= ' AND event_id = '.$serach_event_id;
            }

            $sql .= ' order by sort_order asc';
            $EventQuestionData = DB::select($sql, array());
            // dd($EventQuestionData);

            $card_array = array(
                array("id" => 101190, "question_label" => "Transaction/Order ID", "question_form_type" => "text", "ActualValue" => ""),
                array("id" => 101191, "question_label" => "Registration ID", "question_form_type" => "text", "ActualValue" => ""),
                array("id" => 101193, "question_label" => "Payu ID", "question_form_type" => "text", "ActualValue" => ""),
                array("id" => 101194, "question_label" => "Free/Paid", "question_form_type" => "text", "ActualValue"=> ""),
                array("id" => 101187, "question_label" => "Coupon Code", "question_form_type" => "text", "ActualValue" => ""),
                array("id" => 101195, "question_label" => "Total Amount", "question_form_type" => "text", "ActualValue"=> ""),
                array("id" => 101196, "question_label" => "Payment Status", "question_form_type" => "text", "ActualValue"=> ""),
                array("id" => 101197, "question_label" => "Booking Date/Time", "question_form_type" => "text", "ActualValue" => ""),
                array("id" => 101198, "question_label" => "Race Category", "question_form_type" => "text", "ActualValue" => "")
            );
            $ageCategory_array  = array( array("id" => 101199, "question_label" => "Age Category", "question_form_type" => "age_category", "ActualValue" => ""));
            $utmCapning_array  = array( array("id" => 101186, "question_label" => "UTM Campaign", "question_form_type" => "text", "ActualValue" => ""));
            $main_array = array_merge($card_array, $EventQuestionData);
            
            $event_name = !empty($EventQuestionData) ? $EventQuestionData[0]->event_name : '';
            $label = '';
            $show_age_category = $show_coupon_code = $show_utm = 0;
            
             foreach ($AttendeeData as $key => $res1) {

                //----------- get coupon code
                $sql = "SELECT id,(select ed.discount_code from event_coupon as ec left join event_coupon_details as ed on ed.event_coupon_id = ec.id where ec.id = applied_coupons.coupon_id) as coupon_name FROM applied_coupons WHERE event_id = :event_id AND booking_detail_id=:booking_detail_id ";

                $aCouponResult = DB::select($sql, array('event_id' => $res1->event_id, 'booking_detail_id' => $res1->booking_details_id));
                // dd($aCouponResult); 

               
                $attendee_details_array = json_decode(json_decode($res1->attendee_details), true);
                // $attendee_details_array = $res1->attendee_details;
                $final_attendee_details_array = json_encode(array_merge($attendee_details_array, $card_array, $ageCategory_array, $utmCapning_array));

                //-----------------------------
                $sql = "SELECT txnid,payment_mode,payment_status,created_datetime,(select mihpayid from booking_payment_log where booking_payment_details.id = booking_det_id) as mihpayid FROM booking_payment_details WHERE id =:booking_pay_id ";
                $paymentDetails = DB::select($sql, array('booking_pay_id' => $res1->booking_pay_id));
                //dd($paymentDetails);
                $tran_id = !empty($paymentDetails) ? $paymentDetails[0]->txnid : '';
                $payment_mode = !empty($paymentDetails) ? $paymentDetails[0]->payment_mode : '';
                $payment_status = !empty($paymentDetails) ? $paymentDetails[0]->payment_status : '';
                $mihpayid = !empty($paymentDetails) ? $paymentDetails[0]->mihpayid : '';
                $booking_datetime = !empty($paymentDetails) ? date('d-m-Y h:i:s A', $paymentDetails[0]->created_datetime) : '';

                // dd(json_decode($final_attendee_details_array));
                //-----------------------------
                foreach (json_decode($final_attendee_details_array) as $val) {
                    if (isset($val->question_label)) {

                        $aTemp = new stdClass;
                        $aTemp->question_form_type = $val->question_form_type;
                        $aTemp->question_label = $val->question_label;
                        $labels = [];

                        if ($val->question_label != 'Registration ID' || $val->question_label != 'Payu ID') {
                            if (!empty($val->question_form_option)) {
                                $question_form_option = json_decode($val->question_form_option, true);
                               
                                if($val->question_form_type == "radio" || $val->question_form_type == "select"){
                                    if(isset($val->ActualValue) && !empty($val->ActualValue)){
                                        foreach ($question_form_option as $option) {
                                            if ($option['id'] === (int) $val->ActualValue) {
                                                $label = $option['label'];
                                                break;
                                            }
                                        }
                                    }
                                    
                                }else if($val->question_form_type == "checkbox"){
                                    if(isset($val->ActualValue) && !empty($val->ActualValue)){
                                        foreach ($question_form_option as $option) {
                                            if (in_array($option['id'], explode(',', $val->ActualValue))) {
                                                 $labels[] = $option['label'];
                                            }
                                        }
                                        $label = implode(', ', $labels);
                                    }
                                  
                                }
                                
                                $aTemp->answer_value = $label;
                            } else {
                                if ($val->question_form_type == "countries") {
                                    $aTemp->answer_value = !empty($val->ActualValue) ? $master->getCountryName($val->ActualValue) : "";
                                } else if ($val->question_form_type == "states") {
                                    $aTemp->answer_value = !empty($val->ActualValue) ? $master->getStateName($val->ActualValue) : "";
                                } else if ($val->question_form_type == "cities") {
                                    $aTemp->answer_value = !empty($val->ActualValue) ? $master->getCityName($val->ActualValue) : "";
                                } else {
                                    
                                    if($val->question_form_type == "age_category"){
                                       
                                        $aTemp->question_label = 'Age Category';
                                        if(!empty($val->data)){
                                            $show_age_category = 1;
                                            $aTemp->answer_value = htmlspecialchars($val->data[0]->age_category);
                                        }else{ $aTemp->answer_value = ''; }
                                    }else if($val->question_form_type == "date"){
                                        $aTemp->answer_value = date('d-m-Y',strtotime($val->ActualValue));
                                    }else{
                                        $aTemp->answer_value = isset($val->ActualValue) ? htmlspecialchars($val->ActualValue) : '';
                                    }
                                   
                                }
                            }
                        }
                        //-------------------------------------
                       
                        if ($val->question_label == 'Transaction/Order ID') {
                            $aTemp->answer_value = $tran_id;
                        }

                        if ($val->question_label == 'Registration ID') {
                            $aTemp->answer_value = !empty($res1->registration_id) ? $res1->registration_id : '';
                        }

                        if ($val->question_label == 'Payu ID') {
                            $aTemp->answer_value = $mihpayid;
                        }

                        if($val->question_label == 'Payment Status'){
                            $aTemp->answer_value = ucfirst($payment_status);
                        }

                        if ($val->question_label == 'Booking Date/Time') {
                            $aTemp->answer_value = $booking_datetime;
                        }

                        if($val->question_label == 'Total Amount'){
                            $aTemp->answer_value = !empty($res1->total_amount) ? number_format($res1->total_amount,2) : '0.00';
                        }

                        if($val->question_label == 'Free/Paid'){
                            $aTemp->answer_value = !empty($res1->ticket_amount) && $res1->ticket_amount > 0 ? 'PAID' : 'FREE';
                        }

                        if($val->question_label == 'Race Category'){
                            $aTemp->answer_value = !empty($res1->TicketName) ? $res1->TicketName : '';
                        }

                        if($val->question_label == 'UTM Campaign'){
                           
                            if(!empty($res1->utm_campaign)){
                                $show_utm = 1;
                                $aTemp->answer_value = $res1->utm_campaign;
                            }else{ $aTemp->answer_value = ''; }
                          
                        }

                        if($val->question_label == 'Coupon Code'){
                            
                            if(!empty($aCouponResult)){
                                $show_coupon_code = 1;
                                $aTemp->answer_value = $aCouponResult[0]->coupon_name;
                            }else{ $aTemp->answer_value = ''; }
                        }

                        //-------------------------------------
                        $ExcellDataArray[$key][] = $aTemp;
                      }
                    }
                }

                if($show_age_category == 1){
                    $main_array = array_merge($main_array, $ageCategory_array);
                } else{
                    $main_array = array_merge($main_array);
                }

                if($show_utm == 1){
                    $main_array = array_merge($main_array, $utmCapning_array);
                } else{
                    $main_array = array_merge($main_array);
                }
                $header_data_array = json_decode(json_encode($main_array));

                // dd($ExcellDataArray);
                $filename = "participant_report_" . $event_name."_".time();
                $path = 'attendee_details_excell/' . date('Ymd') . '/';
                $data = Excel::store(new AttendeeDetailsDataExport($ExcellDataArray, $header_data_array), $path . '/' . $filename . '.xlsx', 'excel_uploads');
                $excel_url = url($path) . "/" . $filename . ".xlsx";
                //dd($excel_url); 
                return $excel_url;
        }
        
    }

    public function export_participants_revenue(Request $request,$event_id)
    {         
        $filename = "revenue_report_" . time();
        return Excel::download(new ParticipantsEventRevenueExport($event_id),  $filename.'.xlsx');
    }

    public function view(Request $request,$event_id,$attendance_id){
        $sSQL = 'SELECT abd.attendee_details FROM attendee_booking_details as abd where id=:id';
        $aResult = DB::select($sSQL, array('id'=>$attendance_id));
       
        $Return['attendance_booking_details'] = !empty($aResult) ? $aResult : [];
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
                    } elseif (array_key_exists($questionLabel, $request->input('select', []))) {  
                        $question['ActualValue'] = $request->input('select')[$questionLabel];
                    } elseif (array_key_exists($questionLabel, $request->input('radio', []))) {   
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
