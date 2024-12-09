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
use App\Libraries\Numberformate;

class EventParticipantsController extends Controller
{
    public function __construct()
    {
        ini_set('memory_limit', '950M');
        ini_set('max_execution_time', 300);
    }
    public function clear_search($event_id,$dashboard_id)
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
        session()->forget('event_name_paticipant');

        if($event_id > 0 && $dashboard_id == 0){
            return redirect('/participants_event/'.$event_id);
        }else{
            return redirect('/participants_event/'.$event_id.'/'.$dashboard_id);
        }

      
    }
    public function index(Request $request, $event_id=0,$dashboard_id = 0)
    {
        
        $numberFormate = new Numberformate();
        ini_set('max_execution_time', 1000);
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


        $selectedStatus = $request->list_transaction_status;
        $event_booking_id = $request->event_booking_id;
        $bookingPaymentDetails = $request->booking_payment_details_id;
        // dd($event_booking_id);
        if(!empty($event_booking_id)){
            foreach ($event_booking_id as $eventBookingId) {
                // dd($eventBookingId);
            //     // Get the selected status for the specific event_booking_id
                $status = $selectedStatus[$eventBookingId];
                //  dd($status, $eventBookingId);
                $ssql = 'UPDATE event_booking SET transaction_status = :transaction_status WHERE id=:id';
                $bindings = array(
                    'transaction_status' =>  $status,
                    'id' =>   $eventBookingId
                );
                // dd($bindings);
                $Result = DB::update($ssql, $bindings);

                // dd($status, $bookingPaymentDetails[$eventBookingId]);
                if($status == 0) {
                $booking_status = "Initiate";
                }elseif ($status == 1) {
                    $booking_status = "Success";
                }elseif ($status == 2) {
                    $booking_status = "Failure";
                }elseif ($status == 3) {
                    $booking_status = "Free";
                }elseif ($status == 4) {
                    $booking_status = "Refund";
                }
                // dd($booking_status);
                $ssql = 'UPDATE booking_payment_details SET payment_status = :payment_status WHERE id=:id';
                $bindings = array(
                    'payment_status' => $booking_status,
                    'id' =>  $bookingPaymentDetails[$eventBookingId]
                );
                // dd($bindings);
                $Result = DB::update($ssql, $bindings);
                $successMessage = "Transaction/Payment  Status Successfully";
            }
            
            return redirect('/participants_event/'. $event_id)->with('success', $successMessage);
            
        }    
       
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
            session(['event_name_paticipant' => $request->event_name]);
            if($event_id > 0 && $dashboard_id == 0){
                return redirect('/participants_event/'.$event_id);
            }else{
                return redirect('/participants_event/'.$event_id.'/'.$dashboard_id);
            }
        
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
        $Return['search_event'] = (!empty(session('event_name_paticipant'))) ? session('event_name_paticipant'): '';
        // dd(session('transaction_status'),  $Return['search_transaction_status'] );
        // dd( $Return['search_category'] );
        $FiltersSql = '';
        if(!empty( $Return['search_participant_name'])){
            $FiltersSql .= ' AND (LOWER((CONCAT(a.firstname, " ", a.lastname))) LIKE \'%' . strtolower($Return['search_participant_name']) . '%\')';
        } 

        // if(!empty( $Return['search_transaction_status'])){
        //     $FiltersSql .= ' AND (LOWER(e.transaction_status) LIKE \'%' . strtolower($Return['search_transaction_status']) . '%\')';
        // } 
        if (isset($Return['search_transaction_status']) && $Return['search_transaction_status'] !== '') {
            if ($Return['search_transaction_status'] == 1) { // Special case for Success/Free
                $FiltersSql .= ' AND (e.transaction_status IN (1, 3))'; // Success = 1, Free = 3
            } else {
                $FiltersSql .= ' AND e.transaction_status = ' . intval($Return['search_transaction_status']);
            }
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

        // if(!empty( $Return['search_category'])){
        //     $FiltersSql .= ' AND (LOWER((SELECT et.ticket_name FROM event_tickets et WHERE et.id = a.ticket_id)) LIKE \'%' . strtolower($Return['search_category']) . '%\')';
        // } 
        if(!empty( $Return['search_category'])){
            $FiltersSql .= ' AND a.ticket_id = '.$Return['search_category'];
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

        if(!empty($Return['search_transaction_order_id'])){
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
        // dd($CountRows);
    
        // $sSQL = 'SELECT a.*,e.booking_date,e.booking_pay_id, e.transaction_status, b.event_id,a.id AS aId, CONCAT(a.firstname, " ", a.lastname) AS user_name,
        //     (SELECT et.ticket_name FROM event_tickets et WHERE et.id = a.ticket_id) AS category_name,
        //     (SELECT bpd.txnid FROM booking_payment_details bpd WHERE bpd.id = e.booking_pay_id) AS Transaction_order_id,
        //     (SELECT bpd.amount FROM booking_payment_details bpd WHERE bpd.id = e.booking_pay_id) AS amount,
        //     (SELECT bpt.mihpayid FROM booking_payment_log bpt WHERE bpt.txnid = Transaction_order_id) AS payu_id
        //     FROM attendee_booking_details a
        //     LEFT JOIN booking_details AS b ON a.booking_details_id = b.id
        //     Inner JOIN event_booking AS e ON b.booking_id = e.id
        //     WHERE 1=1';
        $sSQL= 'SELECT a.registration_id,
                e.id As event_booking_id,
                a.email,a.mobile,a.id, 
                a.bulk_upload_flag,
                e.cart_details,
                a.final_ticket_price,
                e.booking_date, 
                e.booking_pay_id, 
                e.transaction_status, 
                b.event_id, 
                a.ticket_id,
                b.ticket_amount,
                a.id AS aId, 
                CONCAT(a.firstname, " ", a.lastname) AS user_name,
                et.ticket_name AS category_name,
                bpd.txnid AS Transaction_order_id,
                bpd.amount AS amount,
                bpd.id As booking_payment_details_id,
                bpt.mihpayid AS payu_id,
                et.early_bird,et.no_of_tickets,et.start_time,et.end_time,et.discount,et.discount_value
            FROM attendee_booking_details a
            LEFT JOIN booking_details b ON a.booking_details_id = b.id
            INNER JOIN event_booking e ON b.booking_id = e.id
            LEFT JOIN event_tickets et ON et.id = a.ticket_id
            LEFT JOIN booking_payment_details bpd ON bpd.id = e.booking_pay_id
            LEFT JOIN booking_payment_log bpt ON bpt.txnid = bpd.txnid
            WHERE 1=1 ';

        if(!empty($event_id)) {
            $sSQL .= ' AND b.event_id = '.$event_id;
        }
        if(!empty($serach_event_id)) {
            $sSQL .= ' AND b.event_id = '.$serach_event_id;
        }
        // $sSQL .= ' '.$FiltersSql.' ORDER BY a.id DESC';
        $sSQL .= ' ' . $FiltersSql; // Add any custom filters here
        $sSQL .= ' GROUP BY a.id'; // Group by unique ID
        $sSQL .= ' ORDER BY a.id DESC';

        if ($Limit > 0) {
            $sSQL .= ' LIMIT ' . $Return['Offset'] . ',' . $Limit;
        }
       // dd($sSQL);
        $event_participants  = DB::select($sSQL, array());
        // dd($event_participants);
        $final_ticket_amount = $TotalBookedTickets1 = $early_bird_total_discount = 0;
        foreach($event_participants  as $value){
            if($value->bulk_upload_flag == 0 && !empty($value->cart_details)){
                // dd(json_decode($value->cart_details));
                foreach(json_decode($value->cart_details) as $res){
                    if(!empty($res->ticket_price) && $value->ticket_id == $res->id){
                        $Extra_Amount = isset($res->Extra_Amount) && !empty($res->Extra_Amount) ? floatval($res->Extra_Amount) : 0;
                        $Extra_Amount_Payment_Gateway = isset($res->Extra_Amount_Payment_Gateway) && !empty($res->Extra_Amount_Payment_Gateway) ? floatval($res->Extra_Amount_Payment_Gateway) : 0;
                        $Extra_Amount_Payment_Gateway_Gst = isset($res->Extra_Amount_Payment_Gateway_Gst) && !empty($res->Extra_Amount_Payment_Gateway_Gst) ? floatval($res->Extra_Amount_Payment_Gateway_Gst) : 0;

                        $final_ticket_amount = (floatval($res->BuyerPayment) + $Extra_Amount + $Extra_Amount_Payment_Gateway + $Extra_Amount_Payment_Gateway_Gst);
                    }
                }
                $value->total_amount = $numberFormate->formatInIndianCurrency($final_ticket_amount);
            }else{
                $value->total_amount = $numberFormate->formatInIndianCurrency($value->final_ticket_price);
            }
            $value->amount = $numberFormate->formatInIndianCurrency($value->amount);
            $value->ticket_amount = $numberFormate->formatInIndianCurrency($value->ticket_amount);

            //-------------------
            $now = strtotime("now");

            $sql10 = "SELECT COUNT(a.id) AS TotalBookedTickets
                    FROM attendee_booking_details AS a 
                    LEFT JOIN booking_details AS b ON b.id=a.booking_details_id
                    LEFT JOIN event_booking AS e ON b.booking_id = e.id
                    WHERE b.event_id =:event_id AND b.ticket_id=:ticket_id AND e.transaction_status IN (1,3)";
            $TotalTickets1 = DB::select($sql10, array("event_id" => $event_id, "ticket_id" => $value->ticket_id));
            $TotalBookedTickets1 = !empty($TotalTickets1) ? $TotalTickets1[0]->TotalBookedTickets : 0;
            // dd($TotalBookedTickets1);
       
            if ($value->early_bird == 1 && $TotalBookedTickets1 <= $value->no_of_tickets && $value->start_time <= $now && $value->end_time >= $now) {
                if ($value->discount == 1) { //percentage
                    $value->total_discount = ($value->ticket_price * ($value->discount_value / 100));
                } else if ($value->discount == 2) { //amount
                    $value->total_discount = $value->discount_value;
                }
            }else{
               $value->total_discount = 0; 
            }

        }    
         
        $Return['event_participants'] = (count($event_participants) > 0) ? $event_participants : [];
        // $Return['event_participants']  = DB::select($sSQL, array());
        // dd($Return['event_participants']);

        
        $sql = 'SELECT name FROM events where id ='.$event_id;
        $Return['event_name'] = DB::select($sql,array());
        // dd( $Return['event_name']);
        $Return['event_id']   = !empty($event_id) ? $event_id : 0;
        $Return['dashboard_id']   = !empty($dashboard_id) ? $dashboard_id : 0;

        $sql = 'SELECT et.ticket_name,et.id FROM event_tickets et WHERE 1=1 GROUP BY et.ticket_name';
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
          
            if($value->bulk_upload_flag == 0 && !empty($value->cart_details)){
                        // dd(json_decode($value->cart_details));
                        foreach(json_decode($value->cart_details) as $res){
                            if(!empty($res->ticket_price) && $value->ticket_id == $res->id){
                                $Extra_Amount = isset($res->Extra_Amount) && !empty($res->Extra_Amount) ? floatval($res->Extra_Amount) : 0;
                                $Extra_Amount_Payment_Gateway = isset($res->Extra_Amount_Payment_Gateway) && !empty($res->Extra_Amount_Payment_Gateway) ? floatval($res->Extra_Amount_Payment_Gateway) : 0;
                                $Extra_Amount_Payment_Gateway_Gst = isset($res->Extra_Amount_Payment_Gateway_Gst) && !empty($res->Extra_Amount_Payment_Gateway_Gst) ? floatval($res->Extra_Amount_Payment_Gateway_Gst) : 0;

                                $final_ticket_amount = (floatval($res->BuyerPayment) + $Extra_Amount + $Extra_Amount_Payment_Gateway + $Extra_Amount_Payment_Gateway_Gst);
                            }
                        }
                $value->total_amount = $final_ticket_amount;
            }else{
                $value->total_amount = $value->final_ticket_price;
            }
              
        }
        // $AttendeeData = [];
        $Return['ParticipantsExcelLink'] = EventParticipantsController::participants_excel_export($AttendeeData, $event_id, $serach_event_id);
        
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
        
        ini_set('max_execution_time', '-1');

        // dd($AttendeeData);

        if (!empty($AttendeeData)) {

            $ExcellDataArray = [];
            $sql = "SELECT id,question_label,question_form_type,question_form_name,(select name from events where id = event_form_question.event_id) as event_name FROM event_form_question WHERE question_status = 1";
           
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
                    array("id" => 101198, "question_label" => "Race Category", "question_form_type" => "text", "ActualValue" => ""),
                    array("id" => 101241, "question_label" => "Bulk Upload Group Name", "question_form_type" => "text", "ActualValue" => "")
                );
            $ageCategory_array  = array( array("id" => 101199, "question_label" => "Age Category", "question_form_type" => "age_category", "ActualValue" => ""));
            $utmCapning_array  = array( array("id" => 101186, "question_label" => "UTM Campaign", "question_form_type" => "text", "ActualValue" => ""));
           
            $main_array = array_merge($card_array, $EventQuestionData);
            // dd($main_array);

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
                $sql = "SELECT txnid,payment_mode,payment_status,created_datetime,(select mihpayid from booking_payment_log where booking_payment_details.id = booking_det_id) as mihpayid,bulk_upload_group_name FROM booking_payment_details WHERE id =:booking_pay_id ";
                $paymentDetails = DB::select($sql, array('booking_pay_id' => $res1->booking_pay_id));
                //dd($paymentDetails);
                $tran_id = !empty($paymentDetails) ? $paymentDetails[0]->txnid : '';
                $payment_mode = !empty($paymentDetails) ? $paymentDetails[0]->payment_mode : '';
                $payment_status = !empty($paymentDetails) ? $paymentDetails[0]->payment_status : '';
                $mihpayid = !empty($paymentDetails) ? $paymentDetails[0]->mihpayid : '';
                $booking_datetime = !empty($paymentDetails) ? date('d-m-Y h:i:s A', $paymentDetails[0]->created_datetime) : '';
                $bulk_upload_group_name = !empty($paymentDetails) && !empty($paymentDetails[0]->bulk_upload_group_name) ? $paymentDetails[0]->bulk_upload_group_name : '';

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
                                $label = '';
                                if($val->question_form_type == "radio" || $val->question_form_type == "select"){
                                    if(isset($val->ActualValue) && !empty($val->ActualValue)){
                                        foreach ($question_form_option as $option) {
                                            if ($option['id'] === (int) $val->ActualValue) {
                                                $label = !empty($option['label']) ? str_replace("&#8377;", "₹", $option['label']) : '';
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
                                        $aTemp->answer_value = isset($val->ActualValue) && !empty($val->ActualValue) ? date('d-m-Y',strtotime($val->ActualValue)) : '';
                                    }else{
                                        
                                        if ($val->question_form_type == "textarea") {
                                            $aTemp->answer_value = preg_replace('/[^A-Za-z0-9 \-]/', '', $val->ActualValue);
                                        }else{ // text

                                            $aTemp->answer_value = htmlspecialchars($val->ActualValue);
                                            $aTemp->answer_value = str_replace("&#233;", "é", $aTemp->answer_value);
                                        }
                                       
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
                            $aTemp->answer_value = !empty($res1->TicketName) ? str_replace("&#233;", "é", $res1->TicketName) : '';
                            // dd($aTemp->answer_value);
                        }

                        if($val->question_label == 'Bulk Upload Group Name'){
                            $aTemp->answer_value = !empty($bulk_upload_group_name) ? $bulk_upload_group_name : '';
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
                // dd($ExcellDataArray);

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
                 // dd($main_array);
                $header_data_array = json_decode(json_encode($main_array));

                // dd($ExcellDataArray);
                $filename = "participant_report_" . $event_name."_".time();
                $path = 'attendee_details_excell/' . date('Ymd') . '/';
               
                $data = Excel::store(new AttendeeDetailsDataExport($ExcellDataArray, $header_data_array), $path . '/' . $filename . '.xlsx', 'excel_uploads');
                $excel_url = url($path) . "/" . $filename . ".xlsx";
                // dd($excel_url); 
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
        $sSQL = 'SELECT id,name,state_id FROM cities WHERE state_id =' .$state_id;
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
                    if (!array_key_exists('child_question_ids', $question)) {
                        continue; // Skip this iteration if the key is missing
                    }
                    // Check if the form has inputs corresponding to the question
                    $questionLabel = $question['question_label'];   
                   
                    if (array_key_exists($questionLabel, $request->input('text', []))) {
                        $question['ActualValue'] = $request->input('text')[$questionLabel];
                    } elseif (array_key_exists($questionLabel, $request->input('checkbox', []))) {
                        $actualValueArray = $request->input('checkbox')[$questionLabel] ?? [];
                        // Convert the array to a comma-separated string
                        $question['ActualValue'] = is_array($actualValueArray) 
                            ? implode(',', $actualValueArray) 
                            : $actualValueArray;

                    } elseif ($question['question_form_type'] == 'date' && $request->has('date')) {
                        $question['ActualValue'] = $request->input('date');
                    } elseif (array_key_exists($questionLabel, $request->input('textarea', []))) {
                        // Handling textarea input (address)
                        $question['ActualValue'] = $request->input('textarea')[$questionLabel];
                        
                    }elseif (array_key_exists($questionLabel, $request->input('select', []))) {  
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
                    } elseif ($question['question_form_type'] == 'file' && $request->has('upload_file')){
                      
                        // Retrieve the file from the request
                        $file = $request->file('upload_file');
                     
                        // Define the upload path
                        $uploadPath = public_path('uploads/attendee_documents/');
                        // Generate a unique file name
                        $fileName = time() . '-' . $file->getClientOriginalName();
                       
                        // Move the file to the target directory
                        $file->move($uploadPath, $fileName);

                        // Optionally, return the file path or save it in the database
                        $filePath = 'uploads/attendee_documents/' . $fileName;
                        $question['ActualValue'] = $fileName;
                        
                        
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

    //-------------- new added changes races category
    public function change_category(Request $request,$event_id,$attendance_id){

        $sSQL = 'SELECT abd.attendee_details FROM attendee_booking_details as abd where id=:id';
        $aResult = DB::select($sSQL, array('id'=>$attendance_id));
       
        $Return['attendance_booking_details'] = !empty($aResult) ? $aResult : [];
        $Return['event_id'] = $event_id;
        $Return['attendance_id'] = $attendance_id;

        $sSQL = 'SELECT id, ticket_name, ticket_price FROM event_tickets WHERE active = 1 AND is_deleted = 0 AND event_id=:event_id';
        $Return["races_category"] = DB::select($sSQL, array('event_id' => $event_id));

        return view('participants_event.participant_change_races_category',$Return);
    }
    
    public function edit_races_category(Request $request,$event_id,$attendance_id){
        // dd($request->all());

        if (isset($request->form_type) && $request->form_type == 'edit_category') {

            $eventId = !empty($request->event_id) ? $request->event_id : 0;
            $attendanceId = !empty($request->attendance_id) ? $request->attendance_id : 0;
            $ticketId = !empty($request->sel_ticket_id) ? $request->sel_ticket_id : 0;

            // dd($eventId,$attendanceId,$ticketId);
            $sSQL = 'SELECT abd.booking_details_id,(select booking_id from booking_details where id = abd.booking_details_id) as event_booking_id,ticket_id FROM attendee_booking_details as abd where id =:id';
            $aResult = DB::select($sSQL, array('id'=> $attendanceId));
         
            $event_booking_id = !empty($aResult) ? $aResult[0]->event_booking_id : 0;
            $booking_details_id = !empty($aResult) ? $aResult[0]->booking_details_id : 0;
            $previous_ticket_id = !empty($aResult) ? $aResult[0]->ticket_id : 0;
            // dd($event_booking_id,$booking_details_id,$previous_ticket_id);

            $sSQL = 'SELECT * FROM event_tickets WHERE active = 1 AND event_id=:event_id AND id=:ticketId';
            $aNewTicketResult = DB::select($sSQL, array('event_id' => $event_id, "ticketId" => $ticketId));
           
            $new_ticket_price = !empty($aNewTicketResult) ? $aNewTicketResult[0]->ticket_price : 0;
            $new_ticket_calculation_details = !empty($aNewTicketResult) ? $aNewTicketResult[0]->ticket_calculation_details : 0;
            
            //----------------- new added for early bird discount apply
            $new_early_bird    = !empty($aNewTicketResult) ? $aNewTicketResult[0]->early_bird : 0;
            $new_no_of_tickets = !empty($aNewTicketResult) ? $aNewTicketResult[0]->no_of_tickets : 0;
            $new_start_time    = !empty($aNewTicketResult) ? $aNewTicketResult[0]->start_time : 0;
            $new_end_time      = !empty($aNewTicketResult) ? $aNewTicketResult[0]->end_time : 0;
            // dd($new_early_bird,$new_no_of_tickets,$new_start_time,$new_end_time);
            $now = strtotime("now");

            $sql10 = "SELECT COUNT(a.id) AS TotalBookedTickets
                    FROM attendee_booking_details AS a 
                    LEFT JOIN booking_details AS b ON b.id=a.booking_details_id
                    LEFT JOIN event_booking AS e ON b.booking_id = e.id
                    WHERE b.event_id =:event_id AND b.ticket_id=:ticket_id AND e.transaction_status IN (1,3)";
            $TotalTickets1 = DB::select($sql10, array("event_id" => $event_id, "ticket_id" => $ticketId));

            $TotalBookedTickets1 = !empty($TotalTickets1) ? $TotalTickets1[0]->TotalBookedTickets : 0;
           
            $early_bird_total_discount = 0;
            if ($new_early_bird == 1 && $TotalBookedTickets1 <= $new_no_of_tickets && $new_start_time <= $now && $new_end_time >= $now) {
              
                if ($aNewTicketResult[0]->discount == 1) { //percentage
                    $early_bird_total_discount = ($value->ticket_price * ($aNewTicketResult[0]->discount_value / 100));
                    $new_ticket_price          = ($new_ticket_price - $early_bird_total_discount);
                } else if ($aNewTicketResult[0]->discount == 2) { //amount
                    $early_bird_total_discount = $aNewTicketResult[0]->discount_value; 
                    $new_ticket_price         = ($new_ticket_price - $early_bird_total_discount);
                }
            }
            // dd($new_ticket_price);

            //----------Event Booking table update entry
            $event_booking_sql = 'SELECT id,booking_pay_id,total_amount,cart_details,(select amount from booking_payment_details where id = event_booking.booking_pay_id) as booking_payment_amount FROM event_booking WHERE event_id=:event_id AND id=:bookId';
            $aEventBookingResult = DB::select($event_booking_sql, array('event_id' => $event_id, "bookId" => $event_booking_id));
            $booking_pay_id = !empty($aEventBookingResult) ? $aEventBookingResult[0]->booking_pay_id : 0;
            $cart_details = !empty($aEventBookingResult) ? json_decode($aEventBookingResult[0]->cart_details) : 0;

            $booking_payment_amount = !empty($aEventBookingResult) ? $aEventBookingResult[0]->booking_payment_amount : 0;
            $event_booking_amount   = !empty($aEventBookingResult) ? $aEventBookingResult[0]->total_amount : 0;
            // dd($booking_payment_amount);

            $Convenience_Fees_Gst_Percentage = 18;
            $GST_On_Platform_Fees = 18;
            $Payment_Gateway_Gst = 18;
            $GstPercentage = 0;

            $ConvenienceFeeBase = $NewPlatformFee = $NewPaymentGatewayFee = $Convenience_Fee_Amount = $BasePriceGst = $Basic_Amount_Gst = 0;
            $GST_On_Convenience_Fees = $Total_Convenience_Fees = $GST_On_Platform_Fees_Amount = $Total_Platform_Fees = $Net_Registration_Amount = $Payment_Gateway_Buyer = $Payment_Gateway_gst_amount = $Total_Payment_Gateway = $BuyerPayment = $totalPlatformFee = $totalTaxes = $Excel_Extra_Amount_Payment_Gateway = $Excel_Extra_Amount_Payment_Gateway_Gst = $Extra_Amount_Payment_Gateway = $Extra_Amount_Payment_Gateway_Gst = $previous_final_ticket_amount = $new_ticket_amount  = $new_final_ticket_amount = 0;
            $flag = 0;
            if(!empty($cart_details)){
                foreach($cart_details as $res){
                    if($res->id == $previous_ticket_id){

                        $previous_final_ticket_amount = $res->BuyerPayment;
                      
                        $sql = "SELECT COUNT(a.id) AS TotalBookedTickets
                        FROM attendee_booking_details AS a 
                        LEFT JOIN booking_details AS b ON b.id=a.booking_details_id
                        LEFT JOIN event_booking AS e ON b.booking_id = e.id
                        WHERE b.event_id =:event_id AND b.ticket_id=:ticket_id AND e.transaction_status IN (1,3)";
                        $TotalTicketsResult = DB::select($sql, array("event_id" => $eventId, "ticket_id" => $ticketId));
                        // dd($TotalTicketsResult);

                        $res->id = (int)$ticketId;
                        $res->age_end = !empty($aNewTicketResult) ? $aNewTicketResult[0]->age_end : 0;
                        $res->category = !empty($aNewTicketResult) ? $aNewTicketResult[0]->category : 0;
                        $res->discount = !empty($aNewTicketResult) ? $aNewTicketResult[0]->discount : 0;
                        $res->end_time = !empty($aNewTicketResult) ? $aNewTicketResult[0]->discount : 0;
                        $res->age_start = !empty($aNewTicketResult) ? $aNewTicketResult[0]->age_start : 0;
                        $res->Main_Price = !empty($aNewTicketResult) ? $new_ticket_price : 0;
                        $res->early_bird = !empty($aNewTicketResult) ? $aNewTicketResult[0]->early_bird : 0;
                     
                        $res->sort_order = !empty($aNewTicketResult) ? $aNewTicketResult[0]->sort_order : 0;
                        $res->start_time = !empty($aNewTicketResult) ? $aNewTicketResult[0]->start_time : 0;
                        $res->max_booking = !empty($aNewTicketResult) ? $aNewTicketResult[0]->max_booking : 0;
                        $res->min_booking = !empty($aNewTicketResult) ? $aNewTicketResult[0]->min_booking : 0;
                        $res->ticket_name = !empty($aNewTicketResult) ? $aNewTicketResult[0]->ticket_name : '';

                        $Sql1 = 'SELECT id,registration_amount,convenience_fee,platform_fee,payment_gateway_fee FROM race_category_charges WHERE event_id =:event_id';
                        $aCatChargesResult = DB::select($Sql1, array('event_id' => $eventId));

                        if(!empty($aCatChargesResult)){
                            for ($i=0; $i < count($aCatChargesResult); $i++) { 
                                // dd($aCatChargesResult[$i]->convenience_fee);
                                if ($aCatChargesResult[$i]->registration_amount >= floatval($new_ticket_price)){
                                    //console.log($aCatChargesResultDetails[i]['convenience_fee']);
                                    $ConvenienceFeeBase = ($aCatChargesResult[$i]->convenience_fee);
                                    $NewPlatformFee = ($aCatChargesResult[$i]->platform_fee);       // 5 Rs
                                    $NewPaymentGatewayFee = ($aCatChargesResult[$i]->payment_gateway_fee); // 1.85 %
                                    break;
                                }else if($i == (count($aCatChargesResult)-1) && $aCatChargesResult[$i]->registration_amount <= floatval($new_ticket_price)){
                                    //console.log($aCatChargesResult[i]['convenience_fee']);
                                    $ConvenienceFeeBase = ($aCatChargesResult[$i]->convenience_fee);
                                    $NewPlatformFee = ($aCatChargesResult[$i]->platform_fee);       // 5 Rs
                                    $NewPaymentGatewayFee = ($aCatChargesResult[$i]->payment_gateway_fee); // 1.85 %
                                    break;
                                }
                            }
                        }

                        $Sql1 = 'SELECT name,collect_gst,prices_taxes_status FROM events WHERE active = 1 and id = ' . $eventId . ' ';
                        $event_Result = DB::select($Sql1);
                        // dd($event_Result);
                        $NewPlatformFee = ($NewPlatformFee * $res->count);

                        if ($event_Result[0]->collect_gst == 1 && $event_Result[0]->prices_taxes_status == 2) {  // get for organization page
                            $GstPercentage = 18;
                        }else{
                            $GstPercentage = 0;
                        }

                        if ($event_Result[0]->collect_gst == 1 && $event_Result[0]->prices_taxes_status == 2) {
                            $BasePriceGst = floatval($new_ticket_price) != 0 ? floatval($new_ticket_price) * ($GstPercentage / 100) : 0; // GST %
                            $Basic_Amount_Gst = (floatval($BasePriceGst) + floatval($new_ticket_price));
                            // dd($BasePriceGst,$Basic_Amount_Gst,$new_ticket_price,$GstPercentage);
                        } else {
                            $BasePriceGst = '0.00';
                            $Basic_Amount_Gst = floatval($new_ticket_price); // registration amt
                        }
                        // dd($Basic_Amount_Gst);
                        if((int)$ConvenienceFeeBase == 30 || (int)$ConvenienceFeeBase == 40 || (int)$ConvenienceFeeBase == 10){
                            $Convenience_Fee_Amount = (int)$ConvenienceFeeBase;
                        }else{
                            $Convenience_Fee_Amount = $Basic_Amount_Gst * ((int)$ConvenienceFeeBase / 100);  
                        }

                        $GST_On_Convenience_Fees = floatval($Convenience_Fee_Amount) * ($Convenience_Fees_Gst_Percentage / 100); // GST 18%
                        $Total_Convenience_Fees = (floatval($Convenience_Fee_Amount) + $GST_On_Convenience_Fees);
                        $GST_On_Platform_Fees_Amount = $NewPlatformFee * ($GST_On_Platform_Fees / 100); // GST 18%
                        $Total_Platform_Fees = (floatval($NewPlatformFee) + floatval($GST_On_Platform_Fees_Amount));
                        $Net_Registration_Amount = (floatval($Basic_Amount_Gst) + floatval($Total_Convenience_Fees) + floatval($Total_Platform_Fees));

                        // dd($Convenience_Fee_Amount,$GST_On_Convenience_Fees,$Basic_Amount_Gst);
                        if((int)$aNewTicketResult[0]->player_of_fee == 1 && (int)$aNewTicketResult[0]->player_of_gateway_fee == 1) {  //Organiser + Organiser
        
                            $Payment_Gateway_Buyer = $Basic_Amount_Gst * ($NewPaymentGatewayFee / 100); // 1.85%
                            $Payment_Gateway_gst_amount = $Payment_Gateway_Buyer * ($Payment_Gateway_Gst / 100); //18%
                            $Total_Payment_Gateway = (floatval($Payment_Gateway_Buyer) + floatval($Payment_Gateway_gst_amount));
                            $BuyerPayment = $Basic_Amount_Gst;  // yes
                            $totalPlatformFee = 0;
                            $totalTaxes = floatval($BasePriceGst);
                            
                            //------------- additional amt calculation 
                            $additional_amount = !empty($total_extra_amount) ? $total_extra_amount : 0; 

                            //-------------- for revenue excel report
                            if(!empty($additional_amount)){
                                $Excel_Extra_Amount_Payment_Gateway = $additional_amount * ($NewPaymentGatewayFee / 100); // 1.85%
                                $Excel_Extra_Amount_Payment_Gateway_Gst = $Excel_Extra_Amount_Payment_Gateway * ($Payment_Gateway_Gst / 100); //18%
                            }
                           

                        }else if((int)$aNewTicketResult[0]->player_of_fee == 2 && (int)$aNewTicketResult[0]->player_of_gateway_fee == 2) {  // Participant + Participant
                            
                            $Payment_Gateway_Buyer = $Net_Registration_Amount * ($NewPaymentGatewayFee / 100); // 1.85%
                            $Payment_Gateway_gst_amount = $Payment_Gateway_Buyer * ($Payment_Gateway_Gst / 100); //18%
                            $Total_Payment_Gateway = (floatval($Payment_Gateway_Buyer) + floatval($Payment_Gateway_gst_amount));
                            $BuyerPayment = (floatval($Total_Payment_Gateway) + floatval($Net_Registration_Amount));
                            // dd($Convenience_Fee_Amount, $NewPlatformFee, $Payment_Gateway_Buyer);
                            $totalPlatformFee = floatval($Convenience_Fee_Amount) + floatval($NewPlatformFee) + floatval($Payment_Gateway_Buyer);
                            $totalTaxes = floatval($BasePriceGst) + floatval($GST_On_Convenience_Fees) + floatval($GST_On_Platform_Fees_Amount) + floatval($Payment_Gateway_gst_amount);
                            // dd($Convenience_Fee_Amount,$NewPlatformFee,$Payment_Gateway_Buyer);
                            
                            //--------------- additional amt calculation
                            if(!empty($total_extra_amount)){
                                $additional_amount = !empty($total_extra_amount) ? $total_extra_amount : 0; 
                                $Extra_Amount_Payment_Gateway = $total_extra_amount * ($NewPaymentGatewayFee / 100); // 1.85%
                                $Extra_Amount_Payment_Gateway_Gst = $Extra_Amount_Payment_Gateway * ($Payment_Gateway_Gst / 100); //18%
                                
                                //-------------- for revenue excel report
                                $Excel_Extra_Amount_Payment_Gateway = $additional_amount * ($NewPaymentGatewayFee / 100); // 1.85%
                                $Excel_Extra_Amount_Payment_Gateway_Gst = $Excel_Extra_Amount_Payment_Gateway * ($Payment_Gateway_Gst / 100); //18%
                            }

                        }else if((int)$aNewTicketResult[0]->player_of_fee == 1 && (int)$aNewTicketResult[0]->player_of_gateway_fee == 2) { // Organiser + Participant
                            
                            $Payment_Gateway_Buyer = $Basic_Amount_Gst * ($NewPaymentGatewayFee / 100); // 1.85%
                            $Payment_Gateway_gst_amount = $Payment_Gateway_Buyer * ($Payment_Gateway_Gst / 100); //18%
                            $Total_Payment_Gateway = (floatval($Payment_Gateway_Buyer) + floatval($Payment_Gateway_gst_amount));
                            $BuyerPayment = (floatval($Basic_Amount_Gst) + floatval($Total_Payment_Gateway));
                            $totalPlatformFee = floatval($Payment_Gateway_Buyer);
                            $totalTaxes = floatval($BasePriceGst) + floatval($Payment_Gateway_gst_amount);

                            //------------- additional amt calculation
                            $additional_amount = !empty($total_extra_amount) ? $total_extra_amount : 0; 

                            //-------------- for revenue excel report
                            if(!empty($additional_amount)){
                                $Excel_Extra_Amount_Payment_Gateway = $additional_amount * ($NewPaymentGatewayFee / 100); // 1.85%
                                $Excel_Extra_Amount_Payment_Gateway_Gst = $Excel_Extra_Amount_Payment_Gateway * ($Payment_Gateway_Gst / 100); //18%
                            }

                        }else if((int)$aNewTicketResult[0]->player_of_fee == 2 && (int)$aNewTicketResult[0]->player_of_gateway_fee == 1) { // Participant + Organiser
                            
                            //--------------- additional amt calculation
                            if(!empty($total_extra_amount)){
                                $additional_amount = !empty($total_extra_amount) ? $total_extra_amount : 0; 
                                $Payment_Gateway_Buyer = ($additional_amount + $Net_Registration_Amount) * ($NewPaymentGatewayFee / 100); // 1.85%

                                //-------------- for revenue excel report
                                $Excel_Extra_Amount_Payment_Gateway = $additional_amount * ($NewPaymentGatewayFee / 100); // 1.85%
                                $Excel_Extra_Amount_Payment_Gateway_Gst = $Excel_Extra_Amount_Payment_Gateway * ($Payment_Gateway_Gst / 100); //18%
                            }else{
                                $Payment_Gateway_Buyer = $Net_Registration_Amount * ($NewPaymentGatewayFee / 100); // 1.85%
                            }

                            $Payment_Gateway_gst_amount = $Payment_Gateway_Buyer * ($Payment_Gateway_Gst / 100); //18%
                            $Total_Payment_Gateway = (floatval($Payment_Gateway_Buyer) + floatval($Payment_Gateway_gst_amount));
                            $BuyerPayment = (floatval($Basic_Amount_Gst) + floatval($Total_Convenience_Fees) + floatval($Total_Platform_Fees) );
                            $totalPlatformFee = floatval($Convenience_Fee_Amount) + floatval($NewPlatformFee);
                            $totalTaxes = floatval($BasePriceGst) + floatval($GST_On_Convenience_Fees) + floatval($GST_On_Platform_Fees_Amount);
                        }
                        
                        $loc_ticket_calculation_details = !empty($new_ticket_calculation_details) ? json_decode($new_ticket_calculation_details) : '';
                        // dd($loc_ticket_calculation_details);

                        $new_ticket_amount  = $BuyerPayment;
                        //---------
                        $res->total_buyer = $BuyerPayment;
                        $res->BuyerPayment = $BuyerPayment;
                        // $res->Extra_Amount = $additional_amount;
                        $res->Platform_Fee = $NewPlatformFee;
                        $res->ticket_price = !empty($aNewTicketResult) ? $new_ticket_price : 0;
                        $res->to_organiser = !empty($loc_ticket_calculation_details) ? $loc_ticket_calculation_details->to_organiser : 0;
                        $res->no_of_tickets = !empty($aNewTicketResult) ? $aNewTicketResult[0]->no_of_tickets : 0;
                        $res->player_of_fee = !empty($aNewTicketResult) ? $aNewTicketResult[0]->player_of_fee : 0;
                        $res->ticket_status = !empty($aNewTicketResult) ? $aNewTicketResult[0]->ticket_status : 0;
                        $res->discount_value = !empty($aNewTicketResult) ? $aNewTicketResult[0]->discount_value : 0;
                        $res->msg_attendance = !empty($aNewTicketResult) ? $aNewTicketResult[0]->msg_attendance : 0;
                        $res->payment_to_you = !empty($aNewTicketResult) ? $aNewTicketResult[0]->payment_to_you : 0;
                        $res->total_discount = !empty($aNewTicketResult) ? $aNewTicketResult[0]->discount : 0;
                        $res->total_quantity = !empty($aNewTicketResult) ? $aNewTicketResult[0]->total_quantity : 0;
                        $res->Convenience_Fee = $Convenience_Fee_Amount;
                        $res->RegistrationFee = $BasePriceGst;
                        $res->apply_age_limit = !empty($aNewTicketResult) ? $aNewTicketResult[0]->apply_age_limit : 0;
                        $res->show_early_bird = !empty($aNewTicketResult) ? $aNewTicketResult[0]->early_bird : 0;
                        $res->ticket_discount = !empty($aNewTicketResult) ? $aNewTicketResult[0]->discount : 0;
                        $res->advanced_settings = !empty($aNewTicketResult) ? $aNewTicketResult[0]->advanced_settings : 0;
                        $res->ticket_show_price = !empty($aNewTicketResult) ? $new_ticket_price : 0;
                        $res->TotalBookedTickets = !empty($TotalTicketsResult) ? $TotalTicketsResult[0]->TotalBookedTickets : 0;
                        $res->ticket_description = !empty($aNewTicketResult) ? $aNewTicketResult[0]->ticket_description : 0;
                        $res->ExcPriceTaxesStatus  = $event_Result[0]->prices_taxes_status;
                        $res->Payment_Gateway_Fee  = $NewPaymentGatewayFee;
                        $res->Platform_Fee_GST_18  = $GST_On_Platform_Fees_Amount;
                        $res->TicketYtcrBasePrice  = $ConvenienceFeeBase;
                        $res->display_ticket_name = !empty($aNewTicketResult) ? $aNewTicketResult[0]->ticket_name : 0;
                        $res->PLATFORM_FEE_PERCENT  = $res->PLATFORM_FEE_PERCENT;
                        $res->PaymentGatewayAmount  = 0;
                        $res->ticket_sale_end_date = !empty($aNewTicketResult) ? $aNewTicketResult[0]->ticket_sale_end_date : 0;
                        $res->PaymentGatewayWithGst  = 0;
                        $res->player_of_gateway_fee = !empty($aNewTicketResult) ? $aNewTicketResult[0]->player_of_gateway_fee : 0;
                        $res->Convenience_Fee_GST_18  = $GST_On_Convenience_Fees;
                        $res->Payment_Gateway_GST_18  = $Payment_Gateway_gst_amount;
                        $res->ticket_sale_start_date = !empty($aNewTicketResult) ? $aNewTicketResult[0]->ticket_sale_start_date : 0;
                        $res->Payment_Gateway_Charges  = round($Payment_Gateway_Buyer,2) ;
                        $res->minimum_donation_amount = !empty($aNewTicketResult) ? $aNewTicketResult[0]->minimum_donation_amount : 0;
                        //$res->Extra_Amount_Payment_Gateway  = $Extra_Amount_Payment_Gateway;
                        $res->BuyerAmtWithoutPaymentGateway  = '0.00';
                        $res->appliedCouponAmount  = 0;
                        $res->Registration_Fee_GST = !empty($loc_ticket_calculation_details) ? $loc_ticket_calculation_details->registration_18_percent_GST : 0;

                        //$res->Extra_Amount_Payment_Gateway_Gst  = $Extra_Amount_Payment_Gateway_Gst;

                        // $res->Excel_Extra_Amount_Payment_Gateway = $Excel_Extra_Amount_Payment_Gateway;
                        // $res->Excel_Extra_Amount_Payment_Gateway_Gst = $Excel_Extra_Amount_Payment_Gateway_Gst;
                        
                        $res->ticket_calculation_details = json_decode($new_ticket_calculation_details);
                      
                        //---------- Main amount update
                        $new_final_ticket_amount = (floatval($booking_payment_amount) + floatval($new_ticket_amount)) - floatval($previous_final_ticket_amount);

                    }else{
                    //---------- Main amount update
                       $new_final_ticket_amount = (floatval($booking_payment_amount) + floatval($new_ticket_amount)) - floatval($previous_final_ticket_amount);

                    }
                    
                    //---------- Main amount update
                    // echo $res->id.'-------'.$ticketId;
                    // if($res->id == $ticketId){
                    //     $new_final_ticket_amount = $booking_payment_amount;
                    //     $flag = 1;
                    // }

                }//die;
            }
            // dd($cart_details);
            // dd($booking_payment_amount,$new_ticket_amount,$previous_final_ticket_amount);
            
            // ---------- Event Booking table update entry
            $payment_booking_sql = 'UPDATE booking_payment_details SET amount =:amount WHERE id=:id';
            $bindings = array(
                'amount' =>  $new_final_ticket_amount,
                'id'     =>  $booking_pay_id
            );
            DB::update($payment_booking_sql, $bindings);  
            
            // ---------- Event Booking table update entry
            $event_booking_sql = 'UPDATE event_booking SET total_amount =:total_amount, cart_details =:cart_details WHERE id=:id';
            $bindings1 = array(
                'total_amount' =>  $new_final_ticket_amount,
                'cart_details' =>  json_encode($cart_details),
                'id'           =>   $event_booking_id
            );
            DB::update($event_booking_sql, $bindings1);  

            //---------- Booking details table update entry
            $booking_sql = 'UPDATE booking_details SET ticket_id =:new_ticket_id, ticket_amount =:ticket_amount WHERE id=:id';
            $bindings2 = array(
                'new_ticket_id' =>  $ticketId,
                'ticket_amount' =>  $new_ticket_price,
                'id' =>   $booking_details_id
            );
            DB::update($booking_sql, $bindings2);

            //---------- Attendee Booking details table update entry
            $attendee_booking_sql = 'UPDATE attendee_booking_details SET ticket_id =:new_ticket_id, ticket_price =:ticket_price, final_ticket_price =:final_ticket_price, category_change_flag =:category_change_flag, category_change_date =:category_change_date WHERE id=:id';
            $bindings3 = array(
                'new_ticket_id' =>  $ticketId,
                'ticket_price'  =>  floatval($new_ticket_price),
                'final_ticket_price'  =>  floatval($BuyerPayment),
                'category_change_flag' => 1,
                'category_change_date' => time(),
                'id' =>   $attendanceId
            );
            // dd($bindings1);
            DB::update($attendee_booking_sql, $bindings3);

        }
        $successMessage = 'Races Category Update Successfully';
        return redirect('/participants_event/'.$event_id)->with('success', $successMessage);
        
    }
    
    

}
