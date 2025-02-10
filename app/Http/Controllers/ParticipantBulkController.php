<?php

namespace App\Http\Controllers;

use App\Exports\participantworkExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;
use App\Imports\ParticipantBulkDetailsImport;
use App\Models\Master;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use App\Libraries\Emails;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use stdClass;

class ParticipantBulkController extends Controller
{
    
    public function clear_search(){
        session::forget('search_event');
        return redirect('/participan_work_upload');
    }

    public function index(){
        $a_return = [];
        $a_return['search_event'] = '';
        $a_return['HeaderData'] = [];
        $a_return['ParticipantsExcelLink'] = '';
        $a_return['group_name'] = '';
        
        $a_return['search_event'] = (!empty(session('search_event'))) ? session('search_event') : '';

        $SQL = "SELECT id,name FROM events WHERE active=1 AND deleted = 0";
        $a_return['EventsData'] = DB::select($SQL, array());
        
        $SQL1 = "SELECT id,productinfo,txnid,created_datetime,amount,payment_status,event_id,created_by,bulk_upload_group_name FROM booking_payment_details WHERE bulk_upload_flag = 1";
        if(isset($a_return['search_event']) && !empty($a_return['search_event']))
            $SQL1 .= " AND event_id = ".$a_return['search_event']."";
        else
            $SQL1 .= " AND 1=2";
        $SQL1 .= " order by id desc";
        $ParticipantDetails = DB::select($SQL1, array());

        if(!empty($ParticipantDetails)){
            foreach($ParticipantDetails as $res){
                $SQL1 = "SELECT (select sum(bd.quantity) as tot_count from booking_details as bd where bd.booking_id = event_booking.id) as tot_count FROM event_booking WHERE booking_pay_id = ".$res->id." ";
                $ParticipantCount = DB::select($SQL1, array());
                // dd($ParticipantCount);
                $res->participant_count = !empty($ParticipantCount) ? $ParticipantCount[0]->tot_count : 0;
            }
        }
        $a_return['ParticipantDetails'] = $ParticipantDetails;
        
        if(!empty($a_return['search_event'])){
            $SQL1 = "SELECT question_form_name,question_label,question_form_type,question_form_option FROM event_form_question WHERE event_id = ".$a_return['search_event']."  ORDER BY sort_order ASC";  // AND question_form_name != 'sub_question'
            $a_return['HeaderData'] = DB::select($SQL1, array());
             if(!empty($a_return['HeaderData']))
            foreach($a_return['HeaderData'] as $res){
                if($res->question_form_name == 'sub_question'){
                   $res->question_form_name = strtolower(str_replace(" ", "_", $res->question_label));
                }
            }
            
            if(!empty($a_return['HeaderData'])){
                foreach($a_return['HeaderData'] as $res){
                    
                    if($res->question_form_type == "mobile"){
                        $res->answer_value    = '9088232618 (10 Digit Required)';
                    }else if($res->question_form_type == "date"){
                        $res->answer_value    = '(YYYY-MM-DD) Format Only';
                    }else if((!empty($res->question_form_option)) && ($res->question_form_type == "radio" || $res->question_form_type == "select")){
                        $question_form_option = json_decode($res->question_form_option);
                        $option_label = array_column($question_form_option,"label");
                         // dd($option_label);
                        $res->answer_value    = 'Use only ('.implode(", ",$option_label).')'; 
                    }else if($res->question_form_type == "countries"){
                        $res->answer_value    = 'India';
                    }else if($res->question_form_type == "states"){
                        $res->answer_value    = 'Maharashtra';
                    }else if($res->question_form_type == "cities"){
                        $res->answer_value    = 'Nashik';
                    }else{
                       $res->answer_value    = ''; 
                    }
                }
            }
        }
            // Neha working 15-11-24 start work   
            $SQL1 = 'SELECT id,ticket_name,ticket_status,ticket_price,total_quantity,ticket_sale_start_date,ticket_sale_end_date,early_bird,event_id FROM event_tickets WHERE is_deleted = 0 AND active = 1 ';
            if(isset($a_return['search_event']) && !empty($a_return['search_event']))
                $SQL1 .= " AND event_id = ".$a_return['search_event']."";
            else
                $SQL1 .= " AND 1=2";
            $SQL1 .= ' ORDER BY sort_order ASC';
            $a_return['Ticket_details'] = DB::select($SQL1,array());
            // dd( $a_return['Ticket details']);
        // neha end work 

        // dd($ParticipantDetails);
        return view('particpant_bulk_upload.list',$a_return);
    }

    public function export_event_participants_work(Request $request)
    {         
        // dd($request->all());
        $a_return = [];
        $a_return['search_event'] = '';
        $a_return['ParticipantsExcelLink'] = '';
        $a_return['group_name'] = '';
        
        if (isset($request->form_type) && $request->form_type == 'Participant_work_upload') {
            session(['search_event' => $request->search_event]);
        }
        $a_return['search_event'] = (!empty(session('search_event'))) ? session('search_event') : '';
      
        if(!empty($a_return['search_event']) ){
            // $a_return['ParticipantsExcelLink'] = Excel::download(new participantworkExport($a_return['search_event']), $filename);
            $a_return['ParticipantsExcelLink'] = ParticipantBulkController::participants_excel_export($a_return['search_event']);
        }else{
            $Message = 'Please select event name';
            return redirect('/participan_work_upload')->with('error', $Message);
        }

        $SQL = "SELECT id,name FROM events WHERE active=1 AND deleted = 0";
        $a_return['EventsData'] = DB::select($SQL, array());

        $SQL1 = "SELECT id,productinfo,txnid,created_datetime,amount,payment_status,event_id,created_by,bulk_upload_group_name FROM booking_payment_details WHERE bulk_upload_flag = 1 AND event_id = ".$a_return['search_event']." order by id desc";
        $ParticipantDetails = DB::select($SQL1, array());

        if(!empty($ParticipantDetails)){
            foreach($ParticipantDetails as $res){
                $SQL1 = "SELECT (select sum(bd.quantity) as tot_count from booking_details as bd where bd.booking_id = event_booking.id) as tot_count FROM event_booking WHERE booking_pay_id = ".$res->id." ";
                $ParticipantCount = DB::select($SQL1, array());
                // dd($ParticipantCount);
                $res->participant_count = !empty($ParticipantCount) ? $ParticipantCount[0]->tot_count : 0;
            }
        }
        $a_return['ParticipantDetails'] = $ParticipantDetails;

        $SQL1 = "SELECT question_form_name,question_label,question_form_type,question_form_option FROM event_form_question WHERE event_id = ".$a_return['search_event']." ORDER BY sort_order ASC"; // AND question_form_name != 'sub_question'
        $a_return['HeaderData'] = DB::select($SQL1, array());
        if(!empty($a_return['HeaderData']))
            foreach($a_return['HeaderData'] as $res){
                if($res->question_form_name == 'sub_question'){
                   $res->question_form_name = strtolower(str_replace(" ", "_", $res->question_label));
                }
            }
        // dd($a_return['HeaderData']);
        
        $aTemp = new stdClass;
        $SampleDataDetails = [];
        if(!empty($a_return['HeaderData'])){
            foreach($a_return['HeaderData'] as $res){
                
                if($res->question_form_type == "mobile"){
                    $res->answer_value    = '9088232618 (10 Digit Required)';
                }else if($res->question_form_type == "date"){
                    $res->answer_value    = '(YYYY-MM-DD) Format Only';
                }else if((!empty($res->question_form_option)) && ($res->question_form_type == "radio" || $res->question_form_type == "select")){
                    $question_form_option = json_decode($res->question_form_option);
                    $option_label = array_column($question_form_option,"label");
                     // dd($option_label);
                    $res->answer_value    = 'Use only ('.implode(", ",$option_label).')'; 
                }else if($res->question_form_type == "countries"){
                    $res->answer_value    = 'India';
                }else if($res->question_form_type == "states"){
                    $res->answer_value    = 'Maharashtra';
                }else if($res->question_form_type == "cities"){
                    $res->answer_value    = 'Nashik';
                }else{
                   $res->answer_value    = ''; 
                }
            }
        }
        // dd($a_return['HeaderData']);
        
        //neha working 15-11-24
            $SQL1 = 'SELECT id,ticket_name,ticket_status,ticket_price,total_quantity,ticket_sale_start_date,ticket_sale_end_date,early_bird,event_id FROM event_tickets WHERE is_deleted = 0 AND active = 1 ';
            if(isset($a_return['search_event']) && !empty($a_return['search_event']))
                $SQL1 .= " AND event_id = ".$a_return['search_event']."";
            else
                $SQL1 .= " AND 1=2";
            $SQL1 .= ' ORDER BY sort_order ASC';
            $a_return['Ticket_details'] = DB::select($SQL1,array());
            
        // neha end working 

        return view('particpant_bulk_upload.list',$a_return);
    }

    function participants_excel_export($event_name)
    {   
        $filename = "participant_Excel_" .$event_name;
        $path = 'attendee_details_excell/' . date('Ymd') . '/';
        $data = Excel::store(new participantworkExport(), $path . '/' . $filename . '.xlsx', 'excel_uploads');
        $excel_url = url($path) . "/" . $filename . ".xlsx";
        // dd($excel_url); 
        return $excel_url;
    }

    public function event_participan_bulk_upload(Request $request)
    { 
        // dd($request); 
        ini_set('max_execution_time', 0);
        $a_return = [];
        $aResult['ParticipantsExcelLink'] = '';
        $userId = 314; // 4;  // support@youtoocanrun.com  account
        $participant_file = !empty($request->participant_file) ? $request->file('participant_file') : '';
        $group_name = !empty($request->group_name) ? $request->group_name : '';
        // dd($group_name);

        $event_id = (!empty(session('search_event'))) ? session('search_event') : '';
         // dd($event_id);
       
        if(empty($event_id)){
            $Message = 'Please download participant excel';
            return redirect('/participan_work_upload')->with('error', $Message);

        }else if(!empty($event_id) && empty($participant_file)){
            $Message = 'Please select file';
            return redirect('/participan_work_upload')->with('error', $Message);

        }else if(!empty($participant_file)){
            $data['userId'] = $userId;
            $data['event_id'] = $event_id;
            $data['group_name'] = $group_name;
            $import = new ParticipantBulkDetailsImport($data);
            Excel::import($import, request()->file('participant_file'));

            // dd($import->returnData['DataFound'],$import->returnData['emailAddressNotFound']);

            $Message = 'Participant Details Uploaded Successfully.';
            $aResult = [
                'message' => $Message,
                'success_count' => isset($import->returnData['DataFound']) && !empty($import->returnData['DataFound']) ? $import->returnData['DataFound'] : 0,
                'fail_count' => isset($import->returnData['emailAddressNotFound']) && !empty($import->returnData['emailAddressNotFound']) ? $import->returnData['emailAddressNotFound'] : 0
            ];
            // dd($aResult);
            return redirect('/participan_work_upload')->with('success', $aResult);
        }
       
        // return view('particpant_bulk_upload.list',$a_return);
    }

    public function event_participant_send_email(Request $request)
    {
       //dd($request);
        ini_set('max_execution_time', 0);
        $Booking_payment_id = !empty($request->id) ? $request->id : 0;
        $event_id           = !empty($request->event_id) ? $request->event_id : 0;
        $userId             = !empty($request->created_by) ? $request->created_by : 0;

            if(!empty($Booking_payment_id))
               $ParticipantSendEmail = ParticipantBulkController::participants_send_email($Booking_payment_id,$event_id,$userId);

        $aResult = [
            'message' => 'Participants email send successfully'
        ];
        // return redirect(url('/participan_work_upload'))->with('success', $aResult);
        return $aResult;
    }


    public function participants_send_email($BookPayId,$EventId,$UserId){
        // dd($BookPayId,$EventId);
        ini_set('max_execution_time', 0);
        if(!empty($BookPayId)){
            
            $TeamName = $Participant_2_name = $Participant_3_name = $Participant_4_name = $preferred_date = $run_category = ''; 
            $master = new Master();
            $sql1 = "SELECT * FROM users WHERE id=:user_id";
            $User = DB::select($sql1, ['user_id' => $UserId]);

            $sql2 = "SELECT * FROM events WHERE id=:event_id";
            $Event = DB::select($sql2, ['event_id' => $EventId]);
            $Venue = $OrgName = "";

            if (count($Event) > 0) {
                $Venue .= ($Event[0]->address !== "") ? $Event[0]->address . ", " : "";
                $Venue .= ($Event[0]->city !== "") ? $master->getCityName($Event[0]->city) . ", " : "";
                $Venue .= ($Event[0]->state !== "") ? $master->getStateName($Event[0]->state) . ", " : "";
                $Venue .= ($Event[0]->country !== "") ? $master->getCountryName($Event[0]->country) . ", " : "";
                $Venue .= ($Event[0]->pincode !== "") ? $Event[0]->pincode . ", " : "";
            }

            if(!empty($Event) && isset($Event[0]->event_type) && $Event[0]->event_type == 2){
                $Venue = 'Virtual Event';
            }
   
            $sql3 = "SELECT * FROM organizer WHERE user_id=:user_id";
            $Organizer = DB::select($sql3, ['user_id' => $UserId]);
            if (count($Organizer) > 0) {
                $OrgName = !empty($Organizer[0]->name) ? ucfirst($Organizer[0]->name) : '';
            }

            //------ ticket registration id and race category
            $sql2 = "select bd.id,bd.ticket_id,eb.cart_details from event_booking as eb left join booking_details as bd on bd.booking_id = eb.id WHERE bd.event_id = :event_id AND eb.booking_pay_id =:booking_pay_id order by bd.booking_id"; // GROUP BY bd.booking_id
            $booking_detail_Result = DB::select($sql2, array('event_id' => $EventId, 'booking_pay_id' => $BookPayId));
            // dd($booking_detail_Result);
            $bookingDetIdArray  = !empty($booking_detail_Result) ? array_column($booking_detail_Result,'id') : [];
            $ticketIdArray      = !empty($booking_detail_Result) ? array_column($booking_detail_Result,'ticket_id') : [];
            // dd($bookingDetIdArray,$ticket_id);
            $cart_details = !empty($booking_detail_Result[0]->cart_details) ? json_decode($booking_detail_Result[0]->cart_details) : [];
            $ticket_total_amount = !empty($cart_details) && isset($cart_details[0]->BuyerPayment) ? $cart_details[0]->BuyerPayment : '0.00';
 
            $EventName = !empty($Event[0]->name) ? str_replace(" ", "_", $Event[0]->name) : '';
            // $EventUrl = url('/e').'/'.$EventName;
            $EventUrl = 'https://racesregistrations.com/e/'.$EventName;
            $TotalNoOfTickets = 1; // !empty($ticketIdArray) ? count($ticketIdArray) : 0;
            $TotalPrice = '';
            // dd($TotalNoOfTickets);
           
            $emailPlaceholders_array = $FinalEmailArray = [];  $acutal_value = ''; $labels = [];  $label = '';
            if(!empty($bookingDetIdArray)){
                $SQL1 = "SELECT id as attendee_id,ticket_id,email,firstname,lastname,registration_id,ticket_price,final_ticket_price,(select ticket_calculation_details from event_tickets where id = attendee_booking_details.ticket_id) as ticket_calculation_details,(select ticket_name from event_tickets where id = attendee_booking_details.ticket_id) as ticket_name,attendee_details FROM attendee_booking_details WHERE booking_details_id IN(".implode(",",$bookingDetIdArray).")";
                $tAttendeeResult = DB::select($SQL1, array());
                $attendee_details = !empty($tAttendeeResult[0]->attendee_details) ? json_decode(json_decode($tAttendeeResult[0]->attendee_details)) : '';
                // dd($tAttendeeResult);

                //--------- Send emails to participants also along with registering person
                if (!empty($tAttendeeResult)){
                    foreach ($tAttendeeResult as $res) {
                        $attendee_email = !empty($res->email) ? $res->email : '';
                        $attendee_firstname = !empty($res->firstname) ? $res->firstname : '';
                        $attendee_details_result = !empty($res->attendee_details) ? json_decode(json_decode($res->attendee_details)) : '';
                        
                        $single_ticket_price = !empty($res->ticket_price) ? $res->ticket_price : '0.00';
                        $final_ticket_price  = !empty($res->final_ticket_price) ? $res->final_ticket_price : '0.00';

                        if(!empty($res->ticket_calculation_details)){
                            $ticket_calculation_details = !empty($res->ticket_calculation_details) ? json_decode($res->ticket_calculation_details) : '';
                            $TotalPrice = !empty($ticket_calculation_details->total_buyer) ? $ticket_calculation_details->total_buyer : 0;
                        }
                        // dd($res->ticket_id);

                        if(!empty($attendee_details_result)){
                            foreach($attendee_details_result as $res1){
                                if($res1->question_form_name == "enter_team_name"){
                                    $TeamName = $res1->ActualValue;
                                }
                                if($res1->question_form_name == "participant_2_name"){
                                    $Participant_2_name = $res1->ActualValue;
                                }
                                if($res1->question_form_name == "participant_3_name"){
                                    $Participant_3_name = $res1->ActualValue;
                                }
                                if($res1->question_form_name == "participant_4_name"){
                                    $Participant_4_name = $res1->ActualValue;
                                }

                                if($res1->question_form_name == "preferred_date_for_the_carnival"){
                                    $preferred_date_json = json_decode($res1->question_form_option);
                                    foreach ($preferred_date_json as $item) {
                                        if ($item->id == $res1->ActualValue){
                                            $preferred_date = $item->label;
                                            break;
                                        }
                                    }
                                }

                                if($res1->question_form_name == "select_your_run_category"){
                                    $run_category_json = json_decode($res1->question_form_option);
                                    foreach ($run_category_json as $item) {
                                        if ($item->id == $res1->ActualValue){
                                            $run_category = $item->label;
                                            break;
                                        }
                                    }
                                }
                                
                                //------------------ new added on 15-11-24  (Email Placeholder Replace)
                                $sql2 = "SELECT question_form_name,placeholder_name,(select question_form_type from event_form_question where id = email_placeholders.question_id) as question_form_type,(select question_form_option from event_form_question where id = email_placeholders.question_id) as question_form_option FROM email_placeholders WHERE status = 1 AND question_form_name =:question_form_name";

                                // if(empty($res1->parent_question_id)){
                                //     $sql2 .= " AND question_form_name = '".$res1->question_form_name."' ";
                                // }else{
                                //     $sql2 .= " AND question_form_name = LOWER(REPLACE('".$res1->question_label."', ' ', '_')) ";
                                // }
                                if(empty($res->parent_question_id)){
                                    $emailPlaceHolderResult = DB::select($sql2, array('question_form_name' => $res1->question_form_name));
                                }else{
                                    $emailPlaceHolderResult = DB::select($sql2, array('question_form_name' => strtolower(str_replace(" ", "_", $res->question_label))));
                                }
                             
                                // $emailPlaceHolderResult = DB::select($sql2, []);

                                if(!empty($emailPlaceHolderResult) && $emailPlaceHolderResult[0]->question_form_type != "file"){
                                    $question_form_option = !empty($emailPlaceHolderResult[0]->question_form_option) ? json_decode($emailPlaceHolderResult[0]->question_form_option, true) : [];
                                    $label = ''; $labels = []; $acutal_value = '';

                                    if($emailPlaceHolderResult[0]->question_form_type == "countries"){
                                        $acutal_value = !empty($res1->ActualValue) ? $master->getCountryName($res1->ActualValue) : "";
                                    }else if ($emailPlaceHolderResult[0]->question_form_type == "states") {
                                        $acutal_value = !empty($res1->ActualValue) ? $master->getStateName($res1->ActualValue) : "";
                                    }else if ($emailPlaceHolderResult[0]->question_form_type == "cities") {
                                        $acutal_value = !empty($res1->ActualValue) ? $master->getCityName($res1->ActualValue) : "";
                                    }else if($emailPlaceHolderResult[0]->question_form_type == "date"){
                                        $acutal_value = !empty($res1->ActualValue) ? date('d-m-Y',strtotime($res1->ActualValue)) : '';
                                    }else if($emailPlaceHolderResult[0]->question_form_type == "radio" || $emailPlaceHolderResult[0]->question_form_type == "select"){
                                      
                                        if(!empty($res1->ActualValue) && !empty($question_form_option)){
                                            foreach ($question_form_option as $option) {
                                                if ($option['id'] === (int) $res1->ActualValue) {
                                                    $label = $option['label'];
                                                    break;
                                                }
                                            }
                                            $acutal_value = !empty($label) ? $label : '';
                                        }
                                    }else if($emailPlaceHolderResult[0]->question_form_type == "checkbox"){
                                        if(isset($res1->ActualValue) && !empty($res1->ActualValue)){
                                            foreach ($question_form_option as $option) {
                                                if (in_array($option['id'], explode(',', $res1->ActualValue))) {
                                                    $labels[] = $option['label'];
                                                }
                                            }
                                            $label = implode(', ', $labels);
                                        }
                                        $acutal_value = !empty($label) ? $label : '';
                                    }else{                                              // [text/email/textarea/amount/time]
                                        $acutal_value = !empty($res1->ActualValue) ? $res1->ActualValue : '';
                                    }

                                    $emailPlaceholders_array[] = [$emailPlaceHolderResult[0]->placeholder_name => trim(ucfirst($acutal_value))];
                                }

                            }
                        }

                        if(!empty($emailPlaceholders_array)){
                            foreach ($emailPlaceholders_array as $item) {  
                                $key = key($item);
                                $value = reset($item);
                                $FinalEmailArray[$key] = $value;
                            }
                        } 
                        
                        $ConfirmationEmail = array(
                            "USERNAME" => !empty($res->firstname) && !empty($res->lastname) ? ucfirst($res->firstname). ' ' . ucfirst($res->lastname) : '',
                            "FIRSTNAME" => !empty($res->firstname) ? ucfirst($res->firstname) : '',
                            "LASTNAME" => !empty($res->lastname) ? ucfirst($res->lastname) : '',
                            "EVENTID" => $EventId,
                            "EVENTNAME" => $Event[0]->name,
                            "EVENTSTARTDATE" => (!empty($Event[0]->start_time)) ? date('d-m-Y', ($Event[0]->start_time)) : "",
                            "EVENTSTARTTIME" => (!empty($Event[0]->start_time)) ? date('H:i A', ($Event[0]->start_time)) : "",
                            "EVENTENDDATE" => (!empty($Event[0]->end_time)) ? date('d-m-Y', ($Event[0]->end_time)) : "",
                            "EVENTENDTIME" => (!empty($Event[0]->end_time)) ? date('H:i A', ($Event[0]->end_time)) : "",
                            "YTCRTEAM" => "YouTooCanRun Team",
                            "EVENTURL" => $EventUrl,
                            "COMPANYNAME" => $OrgName,
                            "TOTALTICKETS" => $TotalNoOfTickets,
                            "VENUE" => $Venue,
                            "TOTALAMOUNT" => !empty($final_ticket_price) ? '₹ '.$final_ticket_price : '₹ '.$ticket_total_amount,
                            "TICKETAMOUNT" => !empty($final_ticket_price) ? '₹ '.$final_ticket_price : '₹ 0.00',
                            "REGISTRATIONID" => !empty($res->registration_id) ? $res->registration_id : '',
                            "RACECATEGORY" => !empty($res->ticket_name) ? ucfirst($res->ticket_name) : '',
                            "TEAMNAME"       => isset($TeamName) && !empty($TeamName) ? ucfirst($TeamName) : '',
                            "2NDPARTICIPANT" => isset($Participant_2_name) && !empty($Participant_2_name) ? ucfirst($Participant_2_name) : '',
                            "3RDPARTICIPANT" => isset($Participant_3_name) && !empty($Participant_3_name) ? ucfirst($Participant_3_name) : '',
                            "4THPARTICIPANT" => isset($Participant_4_name) && !empty($Participant_4_name) ? ucfirst($Participant_4_name) : '',
                            "PREFERREDDATE"  => isset($preferred_date) && !empty($preferred_date) ? $preferred_date : '',
                            "RUNCATEGORY"    => isset($run_category) && !empty($run_category) ? ucfirst($run_category) : ''
                        );
                        // echo '<pre>'; print_r($ConfirmationEmail);

                        if(!empty($FinalEmailArray))
                            $ConfirmationEmail = array_merge($ConfirmationEmail,$FinalEmailArray);
                      
                        $sql = "SELECT * FROM `event_communication` WHERE `event_id`=:event_id AND email_type = 1";
                        $Communications = DB::select($sql, ["event_id" => $EventId]);
                        if(!empty($Communications)){
                            $MessageContent = $Communications[0]->message_content;
                            $Subject = $Communications[0]->subject_name;
                        }else{
                            $MessageContent = "Dear " . $first_name . " " . $last_name . ",
                                 <br/><br/>
                                Thank you for registering for " . $Event[0]->name . "! We are thrilled to have you join us.
                                 <br/><br/>
                                Event Details:
                                 <br/><br/>
                                ● Date: " . $ConfirmationEmail["EVENTSTARTDATE"] . "<br/>
                                ● Time: " . $ConfirmationEmail["EVENTSTARTTIME"] . "<br/>
                                ● Location: " . $Venue . "<br/>
                                <br/><br/>
                                Please find your registration details and ticket attached to this email. If you have any questions or need further information, feel free to contact us.
                                 <br/><br/>
                                We look forward to seeing you at the event!
                                 <br/><br/>
                                Best regards,<br/>
                                " . $Event[0]->name . " Team";
                            $Subject = "Event Registration Confirmation - " . $Event[0]->name . "";
                        }

                        foreach ($ConfirmationEmail as $key => $value) {
                            if (isset($key)) {
                                $placeholder = '{' . $key . '}';
                                $MessageContent = str_replace($placeholder, $value, $MessageContent);
                            }
                        }

                        if(!empty($Communications) && !empty($Communications[0]->content_image)){
                            $image_path = url('/').'/uploads/communication_email_images/'.$Communications[0]->content_image;
                            $attach_image = '<img src="'.str_replace(" ", "%20", $image_path).'" alt="Image">';
                            $MessageContent .= ' <br/><br/>';
                            $MessageContent .= $attach_image;
                        }

                        // dd($MessageContent);
                        // echo '<br>'; echo $MessageContent; die;
                        $generatePdf = ParticipantBulkController::generateParticipantPDF($EventId,$UserId,$res->ticket_id,$res->attendee_id,$EventUrl,$final_ticket_price);
                        // dd($generatePdf);
                        $Email = new Emails();
                        $Email->send_email_participant($UserId, $attendee_email, $MessageContent, $Subject, $generatePdf, $EventId);
                
                    }//die;
                }


            }

        }
    }

    public function generateParticipantPDF($EventId,$UserId,$TicketId,$attendeeId,$EventUrl,$TotalPrice)
    {
        // dd($EventId,$UserId,$TicketId,$attendeeId);
        ini_set('max_execution_time', 0);
        $master = new Master();
        $sql1 = "SELECT CONCAT(firstname,' ',lastname) AS username,email,mobile FROM users WHERE id=:user_id";
        $User = DB::select($sql1, ['user_id' => $UserId]);

        $Venue = "";
        if (!empty($attendeeId)) {
            $sql = "SELECT firstname,lastname,email,attendee_details,registration_id,created_at,(select ticket_name from event_tickets where id = attendee_booking_details.ticket_id) as ticket_name FROM attendee_booking_details WHERE id=:attendee_id";
            $AttendeeData = DB::select($sql, ['attendee_id' => $attendeeId]);
            if (!empty($AttendeeData)) {
                $AttenddeeDetails = $AttendeeData[0]->attendee_details;
                $UniqueTicketId   = $AttendeeData[0]->registration_id;
                $attendee_details = json_decode(json_decode($AttenddeeDetails));
                // dd($EventId);
                $amount_details = $extra_details = [];

                $sql1 = "SELECT question_label,question_form_type,question_form_name,question_label,form_id FROM event_form_question WHERE event_id =:event_id AND is_custom_form = 0  "; // AND question_form_name != 'sub_question'
                $QuestionData = DB::select($sql1, ['event_id' => $EventId]);
                // dd($QuestionData);

                $TicketArr = [ 
                                "TicketName" => !empty($AttendeeData) ? $AttendeeData[0]->ticket_name : '',
                                "firstname" => !empty($AttendeeData) ? $AttendeeData[0]->firstname : '',
                                "lastname" => !empty($AttendeeData) ? $AttendeeData[0]->lastname : '',
                                "email" => !empty($AttendeeData) ? $AttendeeData[0]->email : '',
                                "unique_ticket_id" => !empty($AttendeeData) ? $AttendeeData[0]->registration_id : '',
                                "booking_date" => !empty($AttendeeData) ? $AttendeeData[0]->created_at : '',
                                "ticket_amount" => !empty($TotalPrice) ? $TotalPrice : ''
                            ];
                // dd($TicketArr);
               // dd($QuestionData,$attendee_details);
                // Iterate through attendee details to separate the amounts
                $extra_details = []; $quetion_id = $question_label = $question_form_type = ''; $form_id = 0;
                if(!empty($QuestionData)){
                    foreach($QuestionData as $res){
                        $aTemp = new stdClass;
                        // echo $res->form_id.'aaaaaa<br>';
                        $form_id = $res->form_id;
                        foreach ($attendee_details as $detail) {
                            // echo $detail->question_form_name.'---------'.$detail->question_form_type.'--------'.$detail->ActualValue.'<br>';
                            $labels = []; $label = ''; 
                            
                            if($res->question_form_name != 'sub_question' && $res->question_form_type != 'amount' && $res->question_form_type != 'select' && $res->question_form_type != 'text'){
                                if ($detail->question_form_name == $res->question_form_name && $detail->ActualValue != '') {
                                    // echo 'bbb<br>';
                                    if(!array_search($detail->id, array_column($extra_details, 'id'))){
                                        $question_form_option = json_decode($detail->question_form_option, true);
                                        // dd($question_form_option);
                                        if($detail->question_form_type == 'radio'){
                                          
                                            $label = '';
                                            foreach ($question_form_option as $option) {
                                                if ($option['id'] === (int)$detail->ActualValue) {
                                                    $label = $option['label'];
                                                    break;
                                                }
                                            }
                                            $aTemp->ActualValue    = $label;
                                        }else if($detail->question_form_type == 'checkbox'){
                                            foreach ($question_form_option as $option) {
                                                if (in_array($option['id'], explode(',', $detail->ActualValue))) {
                                                    $labels[] = $option['label'];
                                                }
                                            }
                                            $aTemp->ActualValue   = implode(', ', $labels);
                                        }
                                        else{
                                            $aTemp->ActualValue    = '';
                                        }
                                      
                                        $aTemp->id             = $detail->id; 
                                        $aTemp->question_label = $detail->question_label; 
                                        $aTemp->question_form_type = $detail->question_form_type;
                                        $extra_details[] = $aTemp;
                                        break;
                                    }

                                }
                            }
                            else{
                                $label = ''; 
                                // echo $question_form_name.'----------'.$detail->question_form_name.'<br>';

                                if($res->question_form_type == 'select' &&  !empty($detail->question_form_option) && $detail->question_form_type == 'select' && $detail->ActualValue !== ''){
                                    // echo $res->question_form_type.'11<br>';
                                    if(!array_search($detail->id, array_column($extra_details, 'id'))){
                                        $question_form_option = json_decode($detail->question_form_option, true); 
                                        
                                        foreach ($question_form_option as $option) {
                                            if ($option['id'] === (int)$detail->ActualValue) {
                                                $label = $option['label'];
                                                break;
                                            }
                                        }
                                        $aTemp->ActualValue    = $label;

                                        $aTemp->id             = $detail->id; 
                                        $aTemp->question_label = $detail->question_label; 
                                        $aTemp->question_form_type = $detail->question_form_type;
                                        $extra_details[] = $aTemp;
                                        break;
                                    }
                                }else if($res->question_form_type == 'amount' && $detail->question_form_type == 'amount' && $detail->ActualValue !== ''){
                                    if(!array_search($detail->id, array_column($extra_details, 'id'))){
                                        $aTemp->ActualValue    = $detail->ActualValue;
                                        $aTemp->id             = $detail->id; 
                                        $aTemp->question_label = $detail->question_label; 
                                        $aTemp->question_form_type = $detail->question_form_type;
                                        $extra_details[] = $aTemp;
                                        break;
                                    } 
                                }else if($form_id == '999999' && $res->question_form_type == 'text' && $detail->question_form_type == 'text' && ($res->question_form_name == $detail->question_form_name) && $detail->ActualValue !== '' ){
                                    if(!array_search($detail->id, array_column($extra_details, 'id'))){
                                        $aTemp->ActualValue    = $detail->ActualValue;
                                        $aTemp->id             = $detail->id; 
                                        $aTemp->question_label = $detail->question_label; 
                                        $aTemp->question_form_type = $detail->question_form_type;
                                        $extra_details[] = $aTemp;
                                        break;
                                    } 
                                } 
                            }
                        }

                    }//die;
                }
                // dd($extra_details);
            }
        }

        $created_by = 0;
        if (!empty($EventId)) {
            $sql2 = "SELECT name,start_time,end_time,address,city,state,country,pincode,created_by,event_type FROM events WHERE id=:event_id";
            $Event = DB::select($sql2, ['event_id' => $EventId]);
            // dd($Event);
            if (sizeof($Event) > 0) {
                foreach ($Event as $key => $event) {
                    $event->name = (!empty($event->name)) ? $event->name : '';
                    $event->start_date = (!empty($event->start_time)) ? date("d M Y", $event->start_time) : 0;
                    $event->end_date = (!empty($event->end_time)) ? date("d M Y", $event->end_time) : 0;
                    $event->start_time_event = (!empty($event->start_time)) ? date("h:i A", $event->start_time) : "";
                    $event->end_date_event = (!empty($event->end_time)) ? date("h:i A", $event->end_time) : 0;

                    $Venue .= ($event->address !== "") ? $event->address . ", " : "";
                    $Venue .= ($event->city !== "") ? $master->getCityName($event->city) . ", " : "";
                    $Venue .= ($event->state !== "") ? $master->getStateName($event->state) . ", " : "";
                    $Venue .= ($event->country !== "") ? $master->getCountryName($event->country) . ", " : "";
                    $Venue .= ($event->pincode !== "") ? $event->pincode . ", " : "";
                    $event->Venue = $Venue;
                }
                $created_by = $Event[0]->created_by;
            }
        }

        $Organizer = [];
        if (!empty($created_by)) {
            $sql3 = "SELECT id,name,logo_image FROM organizer WHERE user_id=:user_id";
            $Organizer = DB::select($sql3, ['user_id' => $created_by]);
        }

        if (!empty($Organizer))
            foreach ($Organizer as $key => $value) {
                $value->logo_image = !empty($value->logo_image) ? url('/') . 'organiser/logo_image/' . $value->logo_image : "";
            }

        // Generate QR code
        $qrCode = base64_encode(QrCode::format('png')->size(200)->generate($UniqueTicketId));
        // dd($qrCode);
        $data = [
                'ticket_details' => $TicketArr,
                'event_details' => (sizeof($Event) > 0) ? $Event[0] : [],
                'org_details' => (sizeof($Organizer) > 0) ? $Organizer[0] : [],
                'user_details' => (sizeof($User) > 0) ? $User[0] : [],
                'EventLink' => $EventUrl,
                'QrCode' => $qrCode,
                'amount_details' => $amount_details,
                'extra_details' => $extra_details
        ];
        // dd($data);
        $pdf = PDF::loadView('pdf_template', $data);
        $PdfName = $EventId . $TicketId . time() . '.pdf';
        $pdf->save(public_path('ticket_pdf/' . $PdfName));
        // $PdfPath = url('/') . "/ticket_pdf/" . $PdfName;
        $PdfPath = public_path('ticket_pdf/' . $PdfName);
        // dd($PdfPath);
        return $PdfPath;
    }

    public function delete_participant($iId)
    {
      // dd($iId);
        $SQL1 = "SELECT id FROM event_booking WHERE booking_pay_id =".$iId;
        $eventData = DB::select($SQL1, array());

        $SQL1 = "SELECT id FROM booking_details WHERE booking_id =".$eventData[0]->id;
        $eventBookingData = DB::select($SQL1, array());
        
        if(!empty($eventBookingData)){
            $event_booking_array = !empty($eventBookingData) ? array_column($eventBookingData, 'id') : [];

            $SQL1 = "SELECT id FROM attendee_booking_details WHERE booking_details_id IN(".implode(",", $event_booking_array).")";
            $attendeeData = DB::select($SQL1, array());

            $attendee_array = !empty($attendeeData) ? array_column($attendeeData, 'id') : [];
            // dd($attendee_array);

            $sSQL11 = 'DELETE FROM `attendee_booking_details` WHERE id IN('.implode(",", $attendee_array).')';
            DB::delete($sSQL11,array());

            $sSQL12 = 'DELETE FROM `booking_details` WHERE id IN('.implode(",", $event_booking_array).')';
            DB::delete($sSQL12,array());

            $sSQL14 = 'DELETE FROM `event_booking` WHERE booking_pay_id ='.$iId;
            DB::delete($sSQL14,array());

            $sSQL15 = 'DELETE FROM `booking_payment_details` WHERE id ='.$iId;
            DB::delete($sSQL15,array());
        }else{
            $sSQL14 = 'DELETE FROM `event_booking` WHERE booking_pay_id ='.$iId;
            DB::delete($sSQL14,array());

            $sSQL15 = 'DELETE FROM `booking_payment_details` WHERE id ='.$iId;
            DB::delete($sSQL15,array());
        }
       
        $aResult = [
            'message' => 'Record deleted successfully'
        ];
        return redirect(url('/participan_work_upload'))->with('success', $aResult);
    }
}
