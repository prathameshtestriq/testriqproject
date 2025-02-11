<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Libraries\Authenticate;
use Illuminate\Support\Facades\DB;
use App\Models\Master;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use App\Libraries\Emails;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Libraries\Numberformate; 
use \stdClass;

class EventTicketController extends Controller
{
    public function geteventticket(Request $request)
    {
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $empty = false;
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        // dd($aToken);
        // $aToken['code'] = 200;
        if ($aToken['code'] == 200) {
            $aPost = $request->all();
            $Auth = new Authenticate();
            $Auth->apiLog($request);
            // $aPost['event_id'] = 48;
            if (empty($aPost['event_id'])) {
                $empty = true;
                $field = 'Event Id';
            }
            $master = new Master();
            if (!$empty) {
                $now = strtotime("now");
                $UserId = $aToken['data']->ID;
                // -------------------------------------------------
                $OrgGstPercentage = 0;
                $sql1 = "SELECT gst_percentage FROM organizer WHERE user_id=:user_id";
                $Org = DB::select($sql1, array("user_id" => $UserId));
                if (count($Org) > 0) {
                    $OrgGstPercentage = $Org[0]->gst_percentage;
                }
               // $ResponseData['OrgGstPercentage'] = $OrgGstPercentage;

                // -------------------------------------------------
                $TicketYtcrBasePrice = 0;
                $sql2 = "SELECT ticket_ytcr_base_price FROM event_settings WHERE event_id=:event_id";
                $TicketYtcr = DB::select($sql2, array("event_id" => $aPost['event_id']));
                if (count($TicketYtcr) > 0) {
                    $TicketYtcrBasePrice = $TicketYtcr[0]->ticket_ytcr_base_price;
                }
               // $ResponseData['TicketYtcrBasePrice'] = $TicketYtcrBasePrice;

                // -------------------------------------------------
                $CollectGst = 0;
                $PriceTaxesStatus = 0;
                $sql3 = "SELECT collect_gst,prices_taxes_status FROM events WHERE id=:event_id";
                $CollectGstArr = DB::select($sql3, array("event_id" => $aPost['event_id']));
                // dd($CollectGstArr);
                if (count($CollectGstArr) > 0) {
                    $CollectGst = $CollectGstArr[0]->collect_gst;
                    $PriceTaxesStatus = $CollectGstArr[0]->prices_taxes_status;
                }
                $ResponseData['CollectGst'] = $CollectGst;
                $ResponseData['PriceTaxesStatus'] = $PriceTaxesStatus;
                //-------------------------------------------------
                
                $Sql = "SELECT id,name,start_time,city,event_registration_status,overall_limit,prices_taxes_status FROM events WHERE id=:event_id";
                $EventData = DB::select($Sql, array('event_id' => $aPost['event_id']));

                $overall_limit = !empty($EventData) ? (int)$EventData[0]->overall_limit : 0;
                
                //--------- total booking registration count for payment status (success & free)
                $sSQL = 'SELECT count(id) as total_bookings FROM event_booking WHERE event_id =:event_id AND transaction_status IN(1,3)';
                $aResult = DB::select($sSQL, array('event_id' => $aPost['event_id']));

                $total_booking_registration = !empty($aResult) ? (int)$aResult[0]->total_bookings : 0;

                foreach ($EventData as $key => $event) {
                    $event->display_name = !empty($event->name) ? $event->name : "";
                    $event->start_date = (!empty($event->start_time)) ? date("d M Y", $event->start_time) : 0;
                    $event->city_name = !empty($event->city) ? $master->getCityName($event->city) : "";
                    $event->total_booking_registration = $total_booking_registration;
                    $event->overall_limit = !empty($event->overall_limit) ? (int)$event->overall_limit : 0;

                    if($event->prices_taxes_status == 2){
                        $event->prices_taxes_status = 'Exclusive of Taxes';
                    }else{
                        $event->prices_taxes_status = 'Inclusive of Taxes';
                    }

                    //----------- event overall limit flag
                    if(!empty($event->overall_limit)){
                    
                        $sSQL = "SELECT IFNULL(COUNT(e.id), 0) AS total_bookings  
                        FROM attendee_booking_details AS a 
                        LEFT JOIN booking_details AS b ON b.id=a.booking_details_id
                        LEFT JOIN event_booking AS e ON b.booking_id = e.id
                        WHERE b.event_id =:event_id AND e.transaction_status IN (1,3)";

                        $aResult = DB::select($sSQL, array('event_id' => $event->id));
                        
                        if(!empty($aResult) && (int)$aResult[0]->total_bookings >= (int)$event->overall_limit){
                            $event->event_overall_limit_flag = 1;
                        }else{
                            $event->event_overall_limit_flag = 0;
                        }
                    }else{
                        $event->event_overall_limit_flag = 0;
                    }
                }
                $ResponseData['EventData'] = $EventData;

                //----------------------------------------

                $sSQL = 'SELECT * FROM event_tickets WHERE event_id = :event_id AND active = 1 AND is_deleted = 0 AND ticket_sale_start_date <= :now_start AND ticket_sale_end_date >= :now_end order by sort_order asc';

                $ResponseData['event_tickets'] = DB::select($sSQL, array('event_id' => $aPost['event_id'], 'now_start' => $now, 'now_end' => $now));
                $ticket_calculation_details = [];
                $limit_exceed_flag = $limit_exceed = 0;

                foreach ($ResponseData['event_tickets'] as $value) {
                    $value->count = 0;
                    $value->Error = "";

                    $value->display_ticket_name = !empty($value->ticket_name) ? (strlen($value->ticket_name) > 40 ? ucwords(substr($value->ticket_name, 0, 80)) . "..." : ucwords($value->ticket_name)) : "";

                    // $sql = "SELECT SUM(quantity) AS TotalBookedTickets FROM booking_details WHERE event_id=:event_id AND ticket_id=:ticket_id";
                    // $sql = "SELECT SUM(quantity) AS TotalBookedTickets FROM booking_details AS bd
                    //         LEFT JOIN event_booking AS eb ON bd.booking_id = eb.id
                    //         WHERE bd.event_id=:event_id AND bd.ticket_id=:ticket_id AND eb.transaction_status IN (1,3)";
                    $sql = "SELECT COUNT(a.id) AS TotalBookedTickets
                    FROM attendee_booking_details AS a 
                    LEFT JOIN booking_details AS b ON b.id=a.booking_details_id
                    LEFT JOIN event_booking AS e ON b.booking_id = e.id
                    WHERE b.event_id =:event_id AND b.ticket_id=:ticket_id AND e.transaction_status IN (1,3)";

                    $TotalTickets = DB::select($sql, array("event_id" => $aPost['event_id'], "ticket_id" => $value->id));

                    $value->TotalBookedTickets = ((sizeof($TotalTickets) > 0) && (isset($TotalTickets[0]->TotalBookedTickets))) ? $TotalTickets[0]->TotalBookedTickets : 0;

                    $value->RemainingTickets = $RemainingTickets = 0;
                    if ($value->total_quantity > $value->TotalBookedTickets) {
                        $RemainingTickets = $value->total_quantity - $value->TotalBookedTickets;
                        if ($RemainingTickets <= 10) {
                            $value->RemainingTickets = $RemainingTickets;
                        }
                    }
                    $value->show_early_bird = 0;

                    $value->discount_ticket_price = 0;
                    $value->total_discount = 0;
                    $loc_total_discount = 0;
                    if ($value->early_bird == 1 && $value->TotalBookedTickets <= $value->no_of_tickets && $value->start_time <= $now && $value->end_time >= $now) {
                        $value->show_early_bird = 1;
                        $value->strike_out_price = ($value->early_bird == 1) ? $value->ticket_price : 0;

                        if ($value->discount == 1) { //percentage
                            $value->total_discount = ($value->ticket_price * ($value->discount_value / 100));
                            // $value->total_discount = !empty($loc_total_discount) ? number_format($loc_total_discount,2) : '0.00';
                            $value->discount_ticket_price = $value->ticket_price - $value->total_discount;
                        } else if ($value->discount == 2) { //amount
                            $value->total_discount = $value->discount_value; // !empty($value->discount_value) ? number_format($loc_total_discount,2)  : '0.00';
                            $value->discount_ticket_price = $value->ticket_price - $value->discount_value;
                        }
                    }

                    $current_time = time();
                    $two_days_later = $current_time + (2 * 24 * 60 * 60);
                    if (!empty($value->ticket_sale_end_date) && $value->ticket_sale_end_date <= $two_days_later) {
                        $value->ticket_sale_end_date = date("d M Y H:i A", $value->ticket_sale_end_date);
                    } else {
                        $value->ticket_sale_end_date = "";
                    }

                    $ticket_calculation_details = $value->ticket_calculation_details = !empty($value->ticket_calculation_details) ? json_decode($value->ticket_calculation_details) : [];

                    if(!empty($ticket_calculation_details)){
                        $value->OrgGstPercentage = $ticket_calculation_details->convenience_fees_gst_percentage;
                        $value->TicketYtcrBasePrice = $ticket_calculation_details->convenience_fee_base;
                        $value->YTCR_FEE_PERCENT = config('custom.ytcr_fee_percent');
                        $value->PLATFORM_FEE_PERCENT = $ticket_calculation_details->platform_fees_5_each;
                        $value->PAYMENT_GATEWAY_FEE_PERCENT = $ticket_calculation_details->gst_on_platform_fees;
                        $value->PAYMENT_GATEWAY_GST_PERCENT = $ticket_calculation_details->payment_gateway_gst;

                        $value->total_buyer = $ticket_calculation_details->total_buyer;
                        $value->to_organiser = $ticket_calculation_details->to_organiser;
                        $value->registration_18_percent_GST = $ticket_calculation_details->registration_18_percent_GST;
                    }

                    //----------------------------
                    if(!empty($overall_limit) && !empty($value->min_booking)){
                        $limit_exceed = ($total_booking_registration + (int)$value->min_booking);
                        if($limit_exceed > $overall_limit){
                            $limit_exceed_flag = 1;
                        }else{
                            $limit_exceed_flag = 0;
                        }
                    }else{ $limit_exceed_flag = 0; }

                    $value->limit_exceed_count = $limit_exceed;
                    $value->limit_exceed_flag  = $limit_exceed_flag;
                    
                }
              // dd($ResponseData['event_tickets']);
                

                // ---------- get races category charges
                $sql4 = "SELECT id,registration_amount,convenience_fee,platform_fee,payment_gateway_fee FROM race_category_charges WHERE event_id=:event_id";
                $chargesResult = DB::select($sql4, array('event_id' => $aPost['event_id']));
                $ResponseData['race_category_charges_details'] = !empty($chargesResult) ? $chargesResult : [];

                // -------------------------------------------------
                // $ResponseData['YTCR_FEE_PERCENT'] = config('custom.ytcr_fee_percent');
                // $ResponseData['PLATFORM_FEE_PERCENT'] = config('custom.platform_fee_percent');
                // $ResponseData['PAYMENT_GATEWAY_FEE_PERCENT'] = config('custom.payment_gateway_fee_percent');
                // $ResponseData['PAYMENT_GATEWAY_GST_PERCENT'] = config('custom.payment_gateway_gst_percent');
                // -------------------------------------------------
                $ResposneCode = 200;
                $message = 'Request processed successfully';

            } else {
                $ResposneCode = 400;
                $message = $field . ' is empty';
            }
        } else {
            $ResposneCode = $aToken['code'];
            $message = $aToken['message'];
        }

        $response = [
            'data' => $ResponseData,
            'message' => $message
        ];

        return response()->json($response, $ResposneCode);
    }

    public function addediteventticket(Request $request)
    {
        //ticket_status
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $empty = false;
        $flag = true;
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        // dd($aToken);
        if ($aToken['code'] == 200) {
            $aPost = $request->all();
            $Auth = new Authenticate();
            $Auth->apiLog($request);

            // foreach ($aPosts as $aPost) {
            if (empty($aPost['ticket_name'])) {
                $empty = true;
                $field = 'Ticket Name';
            }
            if (empty($aPost['ticket_status'])) {
                $empty = true;
                $field = 'Ticket Status';
            }
            if (empty($aPost['total_quantity'])) {
                $empty = true;
                $field = 'Total Quantity';
            }
            // if (empty($aPost['ticket_price'])) {
            //     $empty = true;
            //     $field = 'Total Price';
            // }
            if (empty($aPost['start_date'])) {
                $empty = true;
                $field = 'Ticket Sale Start Date';
            }
            if (empty($aPost['end_date'])) {
                $empty = true;
                $field = 'Ticket Sale End Date';
            }


            if (!$empty) {
                //ticket_status=[1=>paid,2=>free,3=>donation]
                $tickettype = $aPost['ticket_status'];
                if ($tickettype == 1) {
                    if (!empty($aPost['ticket_price'])) {
                        // $aPost['minimum_donation_amount'] = 0;
                    } else {
                        $field = 'Ticket Price';
                        $flag = false;
                    }
                }
                if ($tickettype == 2) {
                    if (!empty($aPost['min_booking']) && !empty($aPost['max_booking'])) {
                        $aPost['ticket_price'] = 0;
                        // $aPost['minimum_donation_amount'] = 0;
                    } else {
                        $field = 'Min Max Booking';
                        $flag = false;
                    }
                }
                if ($tickettype == 3) {
                    if (!empty($aPost['minimum_donation_amount'])) {
                        $aPost['ticket_price'] = 0;
                        // $aPost['min_booking'] = 0;
                        // $aPost['max_booking'] = 0;
                    } else {
                        $field = 'Min Donation Amount';
                        $flag = false;
                    }
                }


                // dd($flag);
                if ($flag) {
                    $EventId = isset($request->event_id) ? $request->event_id : 0;
                    $TicketId = isset($request->ticket_id) ? $request->ticket_id : 0;

                    $TicketStartTime = $TicketEndTime = 0;
                    $StartDate = isset($request->start_date) && !empty($request->start_date) ? $request->start_date : 0;
                    $StartTime = isset($request->start_time) && !empty($request->start_time) ? $request->start_time : 0;
                    if (!empty($StartDate) && !empty($StartTime)) {
                        $start_date_time_string = $StartDate . ' ' . $StartTime;
                        $TicketStartTime = strtotime($start_date_time_string);
                    } else if (!empty($StartDate) && empty($StartTime)) {
                        $TicketStartTime = strtotime($StartDate);
                    }

                    $EndDate = isset($request->end_date) && !empty($request->end_date) ? $request->end_date : 0;
                    $EndTime = isset($request->end_time) && !empty($request->end_time) ? $request->end_time : 0;
                    if (!empty($EndDate) && !empty($EndTime)) {
                        $end_date_time_string = $EndDate . ' ' . $EndTime;
                        $TicketEndTime = strtotime($end_date_time_string);
                    } else if (!empty($EndDate) && empty($EndTime)) {
                        $TicketEndTime = strtotime($EndDate);
                    }

                    //EARLY BIRD DATES
                    $EBStartTime = $EBEndTime = 0;
                    $EBStartDate = isset($request->eb_start_date) && !empty($request->eb_start_date) ? $request->eb_start_date : 0;
                    $EBStartTime = isset($request->eb_start_time) && !empty($request->eb_start_time) ? $request->eb_start_time : 0;
                    if (!empty($EBStartDate) && !empty($EBStartTime)) {
                        $eb_start_date_time_string = $EBStartDate . ' ' . $EBStartTime;
                        $EBStartTime = strtotime($eb_start_date_time_string);
                    } else if (!empty($EBStartDate) && empty($EBStartTime)) {
                        $EBStartTime = strtotime($EBStartDate);
                    }

                    $EBEndDate = isset($request->eb_end_date) && !empty($request->eb_end_date) ? $request->eb_end_date : 0;
                    $EBEndTime = isset($request->eb_end_time) && !empty($request->eb_end_time) ? $request->eb_end_time : 0;
                    if (!empty($EBEndDate) && !empty($EBEndTime)) {
                        $eb_end_date_time_string = $EBEndDate . ' ' . $EBEndTime;
                        $EBEndTime = strtotime($eb_end_date_time_string);
                    } else if (!empty($EBEndDate) && empty($EBEndTime)) {
                        $EBEndTime = strtotime($EBEndDate);
                    }

                    $TicketCalculationDetails = isset($request->ticket_calculation_details) && !empty($request->ticket_calculation_details) ?json_encode($request->ticket_calculation_details) : [];

                    if (!empty($TicketId)) {   // update data
                        // dd("here");

                        $SQL = "SELECT ticket_name FROM event_tickets WHERE LOWER(ticket_name) = :ticket_name AND event_id = :event_id AND id != :edit_id";
                        $IsExist = DB::select($SQL, array('ticket_name' => strtolower($aPost['ticket_name']), "event_id" => $EventId, "edit_id" => $TicketId));

                        if (empty($IsExist)) {

                            $Binding = array(
                                // 'event_id' => $aPost['event_id'],
                                'ticket_name' => isset($aPost['ticket_name']) ? $aPost['ticket_name'] : "",
                                'ticket_status' => isset($aPost['ticket_status']) ? $aPost['ticket_status'] : 2,
                                'total_quantity' => isset($aPost['total_quantity']) ? $aPost['total_quantity'] : 0,
                                'ticket_price' => isset($aPost['ticket_price']) ? $aPost['ticket_price'] : 0,
                                'payment_to_you' => isset($aPost['payment_to_you']) ? $aPost['payment_to_you'] : "",
                                'ticket_sale_start_date' => $TicketStartTime,
                                'ticket_sale_end_date' => $TicketEndTime,
                                'advanced_settings' => isset($aPost['advanced_settings']) ? $aPost['advanced_settings'] : 0,
                                'player_of_fee' => isset($aPost['player_of_fee']) ? $aPost['player_of_fee'] : 0,
                                'player_of_gateway_fee' => isset($aPost['player_of_gateway_fee']) ? $aPost['player_of_gateway_fee'] : 0,
                                'min_booking' => isset($aPost['min_booking']) ? $aPost['min_booking'] : 0,
                                'max_booking' => isset($aPost['max_booking']) ? $aPost['max_booking'] : 0,
                                'ticket_description' => isset($aPost['ticket_description']) ? $aPost['ticket_description'] : "",
                                'msg_attendance' => isset($aPost['msg_attendance']) ? $aPost['msg_attendance'] : "",
                                'minimum_donation_amount' => isset($aPost['minimum_donation_amount']) ? $aPost['minimum_donation_amount'] : 0,
                                'early_bird' => isset($aPost['early_bird']) ? $aPost['early_bird'] : 0,
                                'no_of_tickets' => isset($aPost['no_of_tickets']) ? $aPost['no_of_tickets'] : 0,
                                'start_time' => $EBStartTime,
                                'end_time' => $EBEndTime,
                                'discount' => isset($aPost['discount']) ? $aPost['discount'] : 0,
                                'discount_value' => isset($aPost['discount_value']) ? $aPost['discount_value'] : 0,
                                'category' => isset($aPost['category']) ? $aPost['category'] : 0,
                                'apply_age_limit' => isset($aPost['apply_age_limit']) ? $aPost['apply_age_limit'] : 0,
                                'age_start' => isset($aPost['age_start']) ? $aPost['age_start'] : 0,
                                'age_end' => isset($aPost['age_end']) ? $aPost['age_end'] : 0,
                                'ticket_calculation_details' => $TicketCalculationDetails,
                                'id' => $TicketId
                            );

                            // dd($Binding);
                            $SQL = 'UPDATE event_tickets SET ticket_name=:ticket_name,ticket_status = :ticket_status,total_quantity = :total_quantity,ticket_price = :ticket_price,payment_to_you = :payment_to_you,ticket_sale_start_date = :ticket_sale_start_date,ticket_sale_end_date = :ticket_sale_end_date,advanced_settings=:advanced_settings,player_of_fee = :player_of_fee,player_of_gateway_fee = :player_of_gateway_fee,min_booking = :min_booking,max_booking = :max_booking,ticket_description = :ticket_description,msg_attendance = :msg_attendance,minimum_donation_amount= :minimum_donation_amount,early_bird=:early_bird,no_of_tickets=:no_of_tickets,start_time=:start_time,end_time=:end_time,discount=:discount,discount_value=:discount_value,category=:category,apply_age_limit=:apply_age_limit,age_start=:age_start,age_end=:age_end,ticket_calculation_details=:ticket_calculation_details WHERE id=:id';
                            DB::update($SQL, $Binding);

                            $ResposneCode = 200;
                            $message = 'Event Ticket Updated Successfully';
                            $ResponseData = 0;
                        } else {
                            $ResposneCode = 200;
                            $message = "Ticket name is already exists, please use another name.";
                            $ResponseData = 1;
                        }

                    } else {                // insert data

                        $SQL = "SELECT ticket_name FROM event_tickets WHERE LOWER(ticket_name) = :ticket_name AND event_id = :event_id";
                        $IsExist = DB::select($SQL, array('ticket_name' => strtolower($aPost['ticket_name']), "event_id" => $EventId));

                        if (empty($IsExist)) {

                            $Binding = array(
                                'event_id' => $EventId,
                                'ticket_name' => isset($aPost['ticket_name']) ? $aPost['ticket_name'] : "",
                                'ticket_status' => isset($aPost['ticket_status']) ? $aPost['ticket_status'] : 2,
                                'total_quantity' => isset($aPost['total_quantity']) ? $aPost['total_quantity'] : 0,
                                'ticket_price' => isset($aPost['ticket_price']) ? $aPost['ticket_price'] : 0,
                                'payment_to_you' => isset($aPost['payment_to_you']) ? $aPost['payment_to_you'] : "",
                                'ticket_sale_start_date' => $TicketStartTime,
                                'ticket_sale_end_date' => $TicketEndTime,
                                'advanced_settings' => isset($aPost['advanced_settings']) ? $aPost['advanced_settings'] : 0,
                                'player_of_fee' => isset($aPost['player_of_fee']) ? $aPost['player_of_fee'] : 0,
                                'player_of_gateway_fee' => isset($aPost['player_of_gateway_fee']) ? $aPost['player_of_gateway_fee'] : 0,
                                'min_booking' => isset($aPost['min_booking']) ? $aPost['min_booking'] : 0,
                                'max_booking' => isset($aPost['max_booking']) ? $aPost['max_booking'] : 0,
                                'ticket_description' => isset($aPost['ticket_description']) ? $aPost['ticket_description'] : "",
                                'msg_attendance' => isset($aPost['msg_attendance']) ? $aPost['msg_attendance'] : "",
                                'minimum_donation_amount' => isset($aPost['minimum_donation_amount']) ? $aPost['minimum_donation_amount'] : 0,
                                'early_bird' => isset($aPost['early_bird']) ? $aPost['early_bird'] : 0,
                                'no_of_tickets' => isset($aPost['no_of_tickets']) ? $aPost['no_of_tickets'] : 0,
                                'start_time' => $EBStartTime,
                                'end_time' => $EBEndTime,
                                'discount' => isset($aPost['discount']) ? $aPost['discount'] : 0,
                                'discount_value' => isset($aPost['discount_value']) ? $aPost['discount_value'] : 0,
                                'category' => isset($aPost['category']) ? $aPost['category'] : 0,
                                'apply_age_limit' => isset($aPost['apply_age_limit']) ? $aPost['apply_age_limit'] : 0,
                                'age_start' => isset($aPost['age_start']) ? $aPost['age_start'] : 0,
                                'age_end' => isset($aPost['age_end']) ? $aPost['age_end'] : 0,
                                'ticket_calculation_details' => $TicketCalculationDetails,
                            );
                            // dd($Binding);
                            $SQL2 = 'INSERT INTO event_tickets (event_id,ticket_name,ticket_status,total_quantity,ticket_price,payment_to_you,ticket_sale_start_date,ticket_sale_end_date,advanced_settings,player_of_fee,player_of_gateway_fee,min_booking,max_booking,ticket_description,msg_attendance,minimum_donation_amount,early_bird,no_of_tickets,start_time,end_time,discount,discount_value,category,apply_age_limit,age_start,age_end,ticket_calculation_details) VALUES(:event_id,:ticket_name,:ticket_status,:total_quantity,:ticket_price,:payment_to_you,:ticket_sale_start_date,:ticket_sale_end_date,:advanced_settings,:player_of_fee,:player_of_gateway_fee,:min_booking,:max_booking,:ticket_description,:msg_attendance,:minimum_donation_amount,:early_bird,:no_of_tickets,:start_time,:end_time,:discount,:discount_value,:category,:apply_age_limit,:age_start,:age_end,:ticket_calculation_details)';

                            DB::select($SQL2, $Binding);

                            //---------- add form question to aplay new ticket id on 07-08-24
                            $last_inserted_id = DB::getPdo()->lastInsertId();

                            $Sql = 'SELECT id,ticket_details FROM event_form_question WHERE question_status = 1 and apply_ticket = 1 and event_id = ' . $EventId . '  ';
                            $aResult = DB::select($Sql);

                            if (!empty($aResult)) {
                                foreach ($aResult as $res) {
                                    $new_tickets_ids = !empty($res->ticket_details) ? $res->ticket_details . ',' . $last_inserted_id : "";

                                    $up_sSQL = 'UPDATE event_form_question SET `ticket_details` =:ticketDetailsIds WHERE `event_id`=:eventId and `id` =:Id and apply_ticket = 1 ';
                                    DB::update(
                                        $up_sSQL,
                                        array(
                                            'ticketDetailsIds' => $new_tickets_ids,
                                            'eventId' => $EventId,
                                            'Id' => $res->id
                                        )
                                    );

                                }
                            }
                            //--------------- end -----------------------

                            $ResposneCode = 200;
                            $message = 'Event Ticket Inserted Successfully';
                            $ResponseData = 0;
                        } else {
                            $ResposneCode = 200;
                            $message = "Ticket name is already exists, please use another name.";
                            $ResponseData = 1;
                        }

                    }
                } else {
                    $ResposneCode = 400;
                    $message = $field . ' is empty';
                }
            } else {
                $ResposneCode = 400;
                $message = $field . ' is empty';
            }
            // }
        } else {
            $ResposneCode = $aToken['code'];
            $message = $aToken['message'];
        }

        $response = [
            'data' => $ResponseData,
            'message' => $message
        ];

        return response()->json($response, $ResposneCode);
    }

    public function EventTicketDelete(Request $request)
    {
        //dd($request);
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $empty = false;
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        // dd($aToken);

        if ($aToken['code'] == 200) {
            $aPost = $request->all();
            $Auth = new Authenticate();
            $Auth->apiLog($request);

            $EventId = isset($request->event_id) ? $request->event_id : 0;

            //-------------- if ticket is deleted for this condition to event form question in ticket id delete.
            $Sql = 'SELECT id,ticket_details FROM event_form_question WHERE question_status = 1 and event_id = ' . $EventId . '  ';
            $aResult = DB::select($Sql);

            if (!empty($aResult)) {
                foreach ($aResult as $res) {

                    if (!empty($res->ticket_details)) {
                        $new_tickets_id_array = explode(",", $res->ticket_details);
                        $arrayKey = array_search($request->ticket_id, $new_tickets_id_array);
                        unset($new_tickets_id_array[$arrayKey]);

                        $new_tickets_ids = !empty($new_tickets_id_array) ? implode(",", $new_tickets_id_array) : "";
                        //dd($new_tickets_ids);
                        if (!empty($new_tickets_ids)) {
                            $up_sSQL = 'UPDATE event_form_question SET `ticket_details` =:ticketDetailsIds WHERE `event_id`=:eventId and `id` =:Id ';  //and apply_ticket = 1
                            DB::update(
                                $up_sSQL,
                                array(
                                    'ticketDetailsIds' => $new_tickets_ids,
                                    'eventId' => $EventId,
                                    'Id' => $res->id
                                )
                            );
                        }

                    }

                }
            }


            $sSQL = 'UPDATE event_tickets SET is_deleted = 1 WHERE id=:id ';
            $ResponseData = DB::update(
                $sSQL,
                array(
                    'id' => $request->ticket_id
                )
            );
            $ResposneCode = 200;
            $message = 'Event Ticket Deleted Successfully';

        } else {
            $ResposneCode = $aToken['code'];
            $message = $aToken['message'];
        }

        $response = [
            'data' => $ResponseData,
            'message' => $message
        ];

        return response()->json($response, $ResposneCode);
    }

    public function getTicketDetail(Request $request)
    {
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $empty = false;
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        // dd($aToken);

        if ($aToken['code'] == 200) {
            $aPost = $request->all();
            $Auth = new Authenticate();
            $Auth->apiLog($request);

            $sSQL = 'SELECT * FROM event_tickets WHERE id =:id AND is_deleted = 0 AND active = 1';
            $Ticket = DB::select(
                $sSQL,
                array(
                    'id' => $request->ticket_id
                )
            );

            foreach ($Ticket as $key => $value) {
                $value->ticket_start_date = !empty($value->ticket_sale_start_date) ? date("Y-m-d", $value->ticket_sale_start_date) : 0;
                $value->ticket_start_time = (!empty($value->ticket_sale_start_date)) ? date("H:i", $value->ticket_sale_start_date) : 0;

                $value->ticket_end_date = !empty($value->ticket_sale_end_date) ? date("Y-m-d", $value->ticket_sale_end_date) : 0;
                $value->ticket_end_time = (!empty($value->ticket_sale_end_date)) ? date("H:i", $value->ticket_sale_end_date) : 0;

                //EARLY BIRD
                $value->eb_start_date = !empty($value->start_time) ? date("Y-m-d", $value->start_time) : 0;
                $value->eb_start_time = (!empty($value->start_time)) ? date("H:i", $value->start_time) : 0;

                $value->eb_end_date = !empty($value->end_time) ? date("Y-m-d", $value->end_time) : 0;
                $value->eb_end_time = (!empty($value->end_time)) ? date("H:i", $value->end_time) : 0;
            }
            $ResponseData['Ticket'] = $Ticket;
            $ResposneCode = 200;
            $message = 'Request processed successfully';

        } else {
            $ResposneCode = $aToken['code'];
            $message = $aToken['message'];
        }

        $response = [
            'data' => $ResponseData,
            'message' => $message
        ];

        return response()->json($response, $ResposneCode);
    }

    function getFormQuestions(Request $request)
    {
        $ResponseData = $FinalFormQuestions = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $empty = false;
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        // dd($aToken);

        if ($aToken['code'] == 200) {
            $aPost = $request->all();
            if (empty($aPost['event_id'])) {
                $empty = true;
                $field = 'Event Id';
            }

            if (!$empty) {
                $UserId = $aToken['data']->ID;
                $Auth = new Authenticate();
                $Auth->apiLog($request);
                $TotalAttendee = isset($request->total_attendee) && !empty($request->total_attendee) ? $request->total_attendee : 0;
                $AllTickets = isset($request->AllTickets) && !empty($request->AllTickets) ? $request->AllTickets : [];
                // dd($AllTickets);
                $ResponseData['FormQuestions'] = $FinalFormQuestions;
                // $ResponseData['FormQuestions'] = $AllTickets;
                // -------------------------------------------------
                $OrgGstPercentage = 0;
                $sql1 = "SELECT gst_percentage FROM organizer WHERE user_id=:user_id";
                $Org = DB::select($sql1, array("user_id" => $UserId));
                if (count($Org) > 0) {
                    $OrgGstPercentage = $Org[0]->gst_percentage;
                }
                $ResponseData['OrgGstPercentage'] = $OrgGstPercentage;

                // -------------------------------------------------
                $TicketYtcrBasePrice = 0;
                $sql2 = "SELECT ticket_ytcr_base_price FROM event_settings WHERE event_id=:event_id";
                $TicketYtcr = DB::select($sql2, array("event_id" => $aPost['event_id']));
                if (count($TicketYtcr) > 0) {
                    $TicketYtcrBasePrice = $TicketYtcr[0]->ticket_ytcr_base_price;
                }
                $ResponseData['TicketYtcrBasePrice'] = $TicketYtcrBasePrice;

                // -------------------------------------------------
                $CollectGst = $PriceTaxesStatus = $EventStartTime = $AllowUniqueRegistration = 0;
                $sql3 = "SELECT collect_gst,prices_taxes_status,start_time,allow_unique_registration FROM events WHERE id=:event_id";
                $CollectGstArr = DB::select($sql3, array("event_id" => $aPost['event_id']));
                if (count($CollectGstArr) > 0) {
                    $CollectGst = $CollectGstArr[0]->collect_gst;
                    $PriceTaxesStatus = $CollectGstArr[0]->prices_taxes_status;
                    $EventStartTime = $CollectGstArr[0]->start_time;
                    $AllowUniqueRegistration = $CollectGstArr[0]->allow_unique_registration;
                }
                $ResponseData['CollectGst'] = $CollectGst;
                $ResponseData['PriceTaxesStatus'] = $PriceTaxesStatus;
                $ResponseData['EventStartTime'] = $EventStartTime;
                $ResponseData['AllowUniqueRegistration'] = $AllowUniqueRegistration;

                // -------------------------------------------------

                $sSQL = 'SELECT id,title,terms_conditions FROM event_terms_conditions WHERE event_id =:event_id AND status = 1 LIMIT 1';
                $TermsConditions = DB::select($sSQL, array('event_id' => $aPost['event_id']));
                $ResponseData['TermsConditions'] = $TermsConditions;

                //------------------------- get exsting email ids
                // $sSQL = 'SELECT distinct(ad.email) as email_ids FROM event_booking as eb left join booking_details as bd on bd.booking_id = eb.id left join attendee_booking_details as ad on ad.booking_details_id = bd.id  WHERE eb.event_id =:event_id AND eb.transaction_status IN(1,3)';
                // $aEmailResult = DB::select($sSQL, array('event_id' => $aPost['event_id']));
                
                // // dd($aEmailResult);
                // $EmailIdsArray = [];
                // $MobileNoArray = [];

                // if(!empty($aEmailResult)){
                //     $emailArray     = array_column($aEmailResult,'email_ids');
                //     $FilteredEmails = array_filter($emailArray);
                //     $EmailIdsArray  = array_values($FilteredEmails);
                // }

                // $sSQL1 = 'SELECT distinct(ad.mobile) as mobile_no FROM event_booking as eb left join booking_details as bd on bd.booking_id = eb.id left join attendee_booking_details as ad on ad.booking_details_id = bd.id  WHERE eb.event_id =:event_id AND eb.transaction_status IN(1,3)';
                // $aMobileResult = DB::select($sSQL1, array('event_id' => $aPost['event_id']));

                // if(!empty($aMobileResult)){
                //     $mobileArray    = array_column($aMobileResult,'mobile_no');
                //     $FilteredMobile = array_filter(array_unique($mobileArray));
                //     $MobileNoArray  = array_values($FilteredMobile);
                // }
                
                // $ResponseData['DuplicatedEmailIds'] = $EmailIdsArray;
                // $ResponseData['DuplicatedMobileNo'] = $MobileNoArray;

                $SQL5 = "SELECT GROUP_CONCAT(id SEPARATOR ', ') AS Ids,GROUP_CONCAT(general_form_id SEPARATOR ', ') AS GIds FROM event_form_question WHERE event_id=:event_id AND is_custom_form=:is_custom_form ";
                $EventFormQuestions = DB::select($SQL5, array('event_id' => $aPost['event_id'], 'is_custom_form' => 0));

                $QuestionIds = (count($EventFormQuestions) > 0) ? $EventFormQuestions[0]->Ids : "";
                $questionIdsArray = !empty($QuestionIds) ? explode(',', $QuestionIds) : [];
               // dd($AllTickets);
                if (!empty($AllTickets)) {
                    foreach ($AllTickets as $ticket) {
                        // dd($ticket['id']);
                        $sSQL = 'SELECT * 
                                    FROM event_form_question 
                                    WHERE event_id = :event_id 
                                    AND FIND_IN_SET(:ticket_id, ticket_details)
                                    AND question_status = 1 
                                    ORDER BY sort_order,parent_question_id';

                        $FormQuestions = DB::select($sSQL, [
                            'event_id' => $aPost['event_id'],
                            'ticket_id' => isset($ticket['id']) && !empty($ticket['id']) ? $ticket['id'] : 0
                        ]);

                        if (count($FormQuestions) > 0) {
                            // dd('sdsd');
                        } else {   // get all questions data
                            $sSQL = 'SELECT * FROM event_form_question WHERE event_id =:event_id AND question_status = 1 ORDER BY sort_order,parent_question_id';
                            $FormQuestions = DB::select($sSQL, array('event_id' => $aPost['event_id']));
                        }
                        // dd($FormQuestions);
                       
                        //-------------- T-shirt size limit check ----------
                        $SQL6 = "SELECT a.ticket_id,a.attendee_details,b.event_id,a.id as attendeeId,e.id as booking_id
                        FROM attendee_booking_details AS a 
                        LEFT JOIN booking_details AS b ON b.id=a.booking_details_id
                        LEFT JOIN event_booking AS e ON b.booking_id = e.id
                        WHERE b.event_id =:event_id AND e.transaction_status IN (1,3)";
                       
                        if (!empty($Ticket)) {
                            $SQL6 .= ' AND b.ticket_id =' .$ticket['id'];
                        }
                       
                        $bind = array('event_id' => $aPost['event_id']);
                        $CustomQue = DB::select($SQL6, $bind);
                        $CustomQuestions = [];
                         $questionIdsArray = !empty($QuestionIds) ? explode(',', $QuestionIds) : [];
                        //dd($CustomQue);
                        
                        if(!empty($CustomQue)){
                            foreach ($CustomQue as $key => $value) {
                                $attendee_details = json_decode(json_decode($value->attendee_details));
                                foreach ($attendee_details as $key => $attendee) {
                                    if (in_array($attendee->id, $questionIdsArray)) {
                                        if ($attendee->ActualValue != "" && $attendee->question_form_option != "") {
                                            $question_form_option = json_decode($attendee->question_form_option);
                                            $CustomQuestions[$attendee->id][] = $attendee;
                                        }
                                    }
                                }
                            }
                        }
                       
                        // dd($CustomQuestions);
                        $CountArray = array();
                        
                        if(!empty($CustomQuestions)){
                            foreach ($CustomQuestions as $key => $items) {

                                foreach ($items as $item) {

                                    $actualValue = $item->ActualValue;
                                    $question_label = $item->question_label;

                                    $options = json_decode($item->question_form_option, true);
                                    $label = "";
                                    $limit = 0;
                                    $limit_flag = false;
                                    $labels = [];
                                    $option_id = 0;

                                    if($item->question_form_type == 'select'){

                                        $new_array = [];
                                        $questionSizArray = !empty($QueData) ? json_decode($QueData[0]->question_form_option) : [];
                                        if(!empty($questionSizArray)){
                                            foreach($questionSizArray as $val){
                                                $new_array[$val->id] = $val->count;
                                            }
                                        }

                                        // dd($new_array); 
                                        foreach ($options as $option) {
                                            if (in_array($option['id'], explode(',', $actualValue))) {
                                                $labels[] = $option['label'];

                                                $final_limit = (isset($new_array[$option['id']])) ? $new_array[$option['id']] : 0;
                                                $limit =  (int)$final_limit;
                                                $limit_flag = true;
                                                $option_id = $option['id'];
                                            } 
                                            $label = implode(', ', $labels);
                                        }

                                        if (!isset($CountArray[$actualValue])) {
                                            $CountArray[$actualValue] = ["id" => $option_id,  "label" => $label, "count" => 0];
                                        }

                                        $CountArray[$actualValue]["count"]++;
                                    }
                                }//die;
                            }
                        }

                        $finalArray = [];
                        if(!empty($CountArray)){
                            foreach($CountArray as $item) {
                               $finalArray[$item['id']] = $item['count'];
                            } 
                        }

                        // dd($finalArray);
                        //-----------------------
                        foreach ($FormQuestions as $value) {
                            $hasCountriesQuestion = $hasStatesQuestion = false;
                            // echo "<pre>";print_r($value);
                            $value->ActualValue = "";
                            $value->Error = "";
                            $value->TicketId = 0;

                       
                            if (!empty($value->question_form_option) && $value->question_form_type == "select") {
                                $jsonString = $value->question_form_option;
                                $array = json_decode($jsonString, true);
                            // dd($array);
                               
                                if(!empty($array)){
                                   
                                    foreach ($array as $parentItemKey => &$item) { 

                                    // Note the "&" before $item to modify it directly // epb.current_count
                                        if (isset($item['count']) && !empty($item["count"])) {
                                            // $sql = "SELECT COUNT(eb.id) as current_count FROM extra_pricing_booking as epb left join event_booking as eb on eb.id = epb.booking_id WHERE epb.question_id=:question_id AND epb.option_id=:option_id AND eb.transaction_status IN(1,3)";
                                            // $SoldItems = DB::select($sql, array("question_id" => $value->id, "option_id" => $item["id"]));
                                            // if (count($SoldItems) > 0) {
                                            //     $currentCount = !empty($SoldItems) ? $SoldItems[0]->current_count : 0;
                                            //     // Adding current_count to the $item array
                                            //     $item['current_count'] = $currentCount;  // json array to extra key (rx. red,blue) array
                                            //     $item['select_count']  = 0;
                                            // }
                                        
                                            $final_count = (isset($finalArray[$item["id"]])) ? $finalArray[$item["id"]] : 0;
                                            $item['current_count'] = $final_count;

                                        }
                                    }
                                    // Unset $item to avoid potential conflicts with other loops
                                    unset($item);
                                }

                                $updatedJsonString = json_encode($array);

                                // Assign the updated JSON string back to $value->question_form_option
                                $value->question_form_option = $updatedJsonString;
                            }

                            // nationality and country array
                            if ($value->question_form_type == 'countries') { 
                                $sql = "SELECT id,name AS label FROM countries WHERE flag=1 order by name asc";
                                $countries = DB::select($sql);

                                $value->question_form_option = json_encode($countries);
                                $value->ActualValue = "101";
                            }

                            if ($value->question_form_type == 'states') {
                                $sql = "SELECT id,name FROM states WHERE country_id = 101 order by name asc";
                                $states = DB::select($sql);

                                $value->question_form_option = json_encode($states);
                            }
                            
                            if($value->hint_type == 2){
                               $value->hint_image = !empty($value->question_hint) ? url('/').'/uploads/hint_image/'.$value->question_hint : '';
                            }else{
                               $value->hint_image = '';  
                            }
                            // $hasCountriesQuestion = !empty(array_filter($FormQuestions, function ($value) {
                            //     return $value->question_form_type == 'countries';
                            // }));

                            // $hasStatesQuestion = !empty(array_filter($FormQuestions, function ($value) {
                            //     return $value->question_form_type == 'states';
                            // }));

                            // if (!$hasCountriesQuestion && $hasStatesQuestion && $value->question_form_type == 'states') {
                            //     $sql = "SELECT id,name FROM states WHERE flag=1";
                            //     $states = DB::select($sql);

                            //     $value->question_form_option = json_encode($states);
                            // }
                        }

                        // dd($FormQuestions);
                        if(isset($ticket['count'])){
                           for ($i = 0; $i < $ticket['count']; $i++) {
                             if (count($FormQuestions) > 0)
                                $FinalFormQuestions[$ticket['id']][$i] = $FormQuestions;
                           } 
                        }
                        
                    }
                }
                // dd($FinalFormQuestions);

                $ResponseData['FormQuestions'] = $FinalFormQuestions;

                // ---------- get races category charges
                $sql4 = "SELECT id,registration_amount,convenience_fee,platform_fee,payment_gateway_fee FROM race_category_charges WHERE event_id=:event_id";
                $chargesResult = DB::select($sql4, array('event_id' => $aPost['event_id']));
                $ResponseData['race_category_charges_details'] = !empty($chargesResult) ? $chargesResult : [];

                $ResponseData['YTCR_FEE_PERCENT'] = config('custom.ytcr_fee_percent');
                $ResponseData['PLATFORM_FEE_PERCENT'] = config('custom.platform_fee_percent');
                $ResponseData['PAYMENT_GATEWAY_FEE_PERCENT'] = config('custom.payment_gateway_fee_percent');
                $ResponseData['PAYMENT_GATEWAY_GST_PERCENT'] = config('custom.payment_gateway_gst_percent');

                $ResponseData['MAX_UPLOAD_FILE_SIZE'] = config('custom.booking_form_file_max_upload_size');

                // $sql = "SELECT COUNT(id) FROM ticket_booking WHERE event_id=:event_id AND ticket_id=:ticket_id";
                // $ResponseData['TotalBookedTickets'] = $TotalBookedTickets;

                $ResposneCode = 200;
                $message = 'Request processed successfully';
                // } else {
                //     $ResposneCode = 200;
                //     $message = 'Form Questions is empty';
                // }
            } else {
                $ResposneCode = 400;
                $message = $field . ' is empty';
            }
        } else {
            $ResposneCode = $aToken['code'];
            $message = $aToken['message'];
        }

        $response = [
            'data' => $ResponseData,
            'message' => $message
        ];

        return response()->json($response, $ResposneCode);
    }

    function GetEventTermsConditions(Request $request)
    {
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $empty = false;
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        // dd($aToken);

        if ($aToken['code'] == 200) {
            $aPost = $request->all();
            if (empty($aPost['event_id'])) {
                $empty = true;
                $field = 'Event Id';
            }

            if (!$empty) {
                $UserId = $aToken['data']->ID;
                $Auth = new Authenticate();
                $Auth->apiLog($request);
                $EventName = $ATermsConditions = $NewTermsConditions = "";
                $EventSql = "SELECT name FROM events AS e WHERE e.id=:event_id";
                $Events = DB::select($EventSql, array('event_id' => $aPost['event_id']));
                if (count($Events) > 0) {
                    $EventName = $Events[0]->name;
                }
                // $ResponseData['EventName'] = $EventName;

                $OrgEmail = '';
                $OrgSql = "SELECT email FROM organizer AS e WHERE e.user_id=:user_id";
                $Org = DB::select($OrgSql, array('user_id' => $UserId));
                if (count($Org) > 0) {
                    $OrgEmail = $Org[0]->email;
                }
                // $ResponseData['OrgEmail'] = $OrgEmail;


                $sSQL = 'SELECT id,title,terms_conditions FROM event_terms_conditions WHERE event_id =:event_id AND status = 1 LIMIT 1';
                $TermsConditions = DB::select($sSQL, array('event_id' => $aPost['event_id']));

                if (count($TermsConditions) > 0) {
                    $ATermsConditions = $TermsConditions[0]->terms_conditions;
                }
                $content = "";
                if (!empty($ATermsConditions)) {
                    // $NewTermsConditions = str_replace(("[Event Name]"), "<b>" . $EventName . "</b>", $ATermsConditions);
                    // $NewTermsConditions = str_replace(("[Event name]"), "<b>" . $EventName . "</b>", $ATermsConditions);
                    $content = Str::replace('[Event Name]', "<b>" . $EventName . "</b>", $ATermsConditions);
                    $content = Str::replace('[Event name]', "<b>" . $EventName . "</b>", $content);
                    $content = Str::replace("[organiser email id]", "<b>" . $OrgEmail . "</b>", $content);
                }

                $ResponseData['TermsConditions'] = $content;

                $ResposneCode = 200;
                $message = 'Request processed successfully';

            } else {
                $ResposneCode = 400;
                $message = $field . ' is empty';
            }
        } else {
            $ResposneCode = $aToken['code'];
            $message = $aToken['message'];
        }

        $response = [
            'data' => $ResponseData,
            'message' => $message
        ];

        return response()->json($response, $ResposneCode);
    }

    function BookTickets(Request $request,$booking_pay_id=0)
    {
        $ResponseData = $FinalFormQuestions = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $empty = false;
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        // dd($request->FormQuestions);

        if ($aToken['code'] == 200) {
            $aPost = $request->all();
            if (isset($aPost['booking_pay_id']) && empty($aPost['booking_pay_id'])) {
                $empty = true;
                $field = 'Booking Payment Id';
            }
            // if (empty($aPost['FormQuestions'])) {
            //     $empty = true;
            //     $field = 'Form Questions';
            // }


            if (!$empty || empty($booking_pay_id)) {
                $UserId = $aToken["data"]->ID;
                $Auth = new Authenticate();
                $Auth->apiLog($request, $UserId, "after payment");
                
                if(!empty($booking_pay_id)){
                    $BookingPaymentId = $booking_pay_id;
                }else{
                    $BookingPaymentId = !empty($aPost['booking_pay_id']) ? $aPost['booking_pay_id'] : 0;
                }
               
                $sql = "SELECT * FROM temp_booking_ticket_details WHERE booking_pay_id =:booking_pay_id";
                $BookingPayment = DB::select($sql, array('booking_pay_id' => $BookingPaymentId));


                // $sql1 = "SELECT payment_status FROM booking_payment_details WHERE id =:id";
                // $Status = DB::select($sql, array('id' => $BookingPaymentId));
                // dd($BookingPayment);

                if (count($BookingPayment) > 0) {
                    $EventId = $BookingPayment[0]->event_id;
                    $TotalAttendee = $BookingPayment[0]->total_attendees;
                    $FormQuestions = !empty($BookingPayment[0]->FormQuestions) ? json_decode($BookingPayment[0]->FormQuestions) : [];
                    $AllTickets = !empty($BookingPayment[0]->AllTickets) ? json_decode($BookingPayment[0]->AllTickets) : [];
                    $TotalPrice = $BookingPayment[0]->TotalPrice;
                    $TotalDiscount = $BookingPayment[0]->TotalDiscount;
                    $ExtraPricing = !empty($BookingPayment[0]->ExtraPricing) ? json_decode($BookingPayment[0]->ExtraPricing) : [];
                    $UtmCampaign = $BookingPayment[0]->UtmCampaign;
                    $GstArray = !empty($BookingPayment[0]->GstArray) ? json_decode($BookingPayment[0]->GstArray) : [];
                    $TransactionStatus = 0; //Initiated Transaction

                    if (empty($TotalPrice) || $TotalPrice == 0 || $TotalPrice == '0.00' || $TotalPrice == '0') {
                        $TransactionStatus = 3; // Free Transaction
                    }

                    $UserId = $aToken["data"]->ID;
                    $UserEmail = $aToken["data"]->email;
                    // dd($UserEmail);
                    $TotalTickets = 0;

                    // if (!empty($TotalPrice)) {
                    #event_booking
                    $Binding1 = array(
                        "event_id" => $EventId,
                        "user_id" => $UserId,
                        "booking_date" => strtotime("now"),
                        "total_amount" => $TotalPrice,
                        "total_discount" => $TotalDiscount,
                        "utm_campaign" => $UtmCampaign,
                        "cart_details" => json_encode($GstArray),
                        "transaction_status" => $TransactionStatus,
                        "booking_pay_id" => $BookingPaymentId
                    );
                    $Sql1 = "INSERT INTO event_booking (event_id,user_id,booking_date,total_amount,total_discount,utm_campaign,cart_details,transaction_status,booking_pay_id) VALUES (:event_id,:user_id,:booking_date,:total_amount,:total_discount,:utm_campaign,:cart_details,:transaction_status,:booking_pay_id)";
                    DB::insert($Sql1, $Binding1);
                    $BookingId = DB::getPdo()->lastInsertId();

                    #booking_details
                    $BookingDetailsIds = [];
                    $BookingDetailsTicketPrice = [];
                    $BookingDetailsTicketFinalPrice = [];
                    //dd($AllTickets);
                    foreach ($AllTickets as $ticket) {
                        if (!empty($ticket->count)) {
                            $Binding2 = [];
                            $Sql2 = "";
                            $Binding2 = array(
                                "booking_id" => $BookingId,
                                "event_id" => $EventId,
                                "user_id" => $UserId,
                                "ticket_id" => $ticket->id,
                                "quantity" => $ticket->count,
                                "ticket_amount" => $ticket->ticket_price,
                                "ticket_discount" => isset($ticket->ticket_discount) ? ($ticket->ticket_discount) : 0,
                                "booking_date" => strtotime("now"),
                            );
                            $Sql2 = "INSERT INTO booking_details (booking_id,event_id,user_id,ticket_id,quantity,ticket_amount,ticket_discount,booking_date) VALUES (:booking_id,:event_id,:user_id,:ticket_id,:quantity,:ticket_amount,:ticket_discount,:booking_date)";
                            $aResult = DB::insert($Sql2, $Binding2);
                            #Get the last inserted id of booking_details
                            $BookingDetailsId = DB::getPdo()->lastInsertId();

                            $BookingDetailsIds[$ticket->id] = $BookingDetailsId;
                            $BookingDetailsTicketPrice[$ticket->id] = $ticket->ticket_price;
                            if(!empty($ticket->ticket_price)){

                                $BookingDetailsTicketFinalPrice[$ticket->id] = (floatval($ticket->total_buyer) + floatval($ticket->Extra_Amount) + floatval($ticket->Extra_Amount_Payment_Gateway) + floatval($ticket->Extra_Amount_Payment_Gateway_Gst)) ; 
                            }
                           

                            $new_ticket_id = !empty($aResult[0]->ticket_id) ? $aResult[0]->ticket_id : $ticket->id;

                            // ADD IF COUPONS APPLY ON TICKET
                            $appliedCouponId = $appliedCouponAmount = 0;
                            $appliedCouponCode = "";

                            $appliedCouponId = (isset($ticket->appliedCouponId) && !empty($ticket->appliedCouponId)) ? $ticket->appliedCouponId : 0;
                            $appliedCouponAmount = (isset($ticket->appliedCouponAmount) && !empty($ticket->appliedCouponAmount)) ? $ticket->appliedCouponAmount : 0;
                            // $appliedCouponCode = (isset($ticket["appliedCouponCode"]) && !empty($ticket["appliedCouponCode"])) ? $ticket["appliedCouponCode"] : "";

                            if (!empty($appliedCouponId) && $appliedCouponAmount) {
                                $Binding6 = [];
                                $Sql6 = "";
                                $Binding6 = array(
                                    "event_id" => $EventId,
                                    "coupon_id" => $appliedCouponId,
                                    "ticket_ids" => $ticket->id,
                                    "amount" => $appliedCouponAmount,
                                    "created_by" => $UserId,
                                    "created_at" => strtotime("now"),
                                    "booking_id" => $BookingId,
                                    "booking_detail_id" => $BookingDetailsId
                                );
                                $Sql6 = "INSERT INTO applied_coupons (event_id,coupon_id,ticket_ids,amount,created_by,created_at,booking_id,booking_detail_id) VALUES (:event_id,:coupon_id,:ticket_ids,:amount,:created_by,:created_at,:booking_id,:booking_detail_id)";
                                DB::insert($Sql6, $Binding6);

                                $Sql7 = "";
                                $Binding7 = [];
                                
                                //-----------------
                                $Sql7 = "SELECT no_of_discount FROM event_coupon_details WHERE event_coupon_id=:event_coupon_id";
                                $Binding7 = array("event_coupon_id" => $appliedCouponId);
                                $Result = DB::select($Sql7, $Binding7);
                                $no_of_discount = 0;

                                //---------- new added used coupon code on 11-02-25
                                $SQL3 = "SELECT ec.id AS coupon_id,(SELECT COUNT(ac.coupon_id) FROM applied_coupons ac LEFT JOIN event_booking eb on ac.booking_id=eb.id WHERE ac.coupon_id = ecd.event_coupon_id AND eb.transaction_status IN (1,3) AND eb.event_id = ecd.event_id) AS coupon_count
                                FROM event_coupon_details AS ecd
                                JOIN event_coupon AS ec ON ecd.event_coupon_id = ec.id
                                WHERE ecd.event_id =:event_id
                                AND ecd.event_coupon_id =:eventCouponId
                                AND ec.coupon_status = 1";

                                $aCouponAppliedResult = DB::select($SQL3, array('event_id' => $EventId, 'eventCouponId' => $appliedCouponId));

                                if (!empty($Result) && !empty($aCouponAppliedResult)) {
                                   
                                    $no_of_discount    = $Result[0]->no_of_discount;
                                    $used_coupon_count = $aCouponAppliedResult[0]->coupon_count; 

                                    $Sql8 = "";
                                    $Binding8 = [];
                                    if ($no_of_discount <= $used_coupon_count) {
                                        $Binding8 = array("event_coupon_id" => $appliedCouponId);
                                        $Sql8 = "UPDATE event_coupon_details SET end_coupon=1 WHERE event_coupon_id=:event_coupon_id";
                                        DB::update($Sql8, $Binding8);
                                    }
                                }
                                
                                //---------------- temp hide
                                // $Sql7 = "SELECT discount_type FROM event_coupon_details WHERE event_coupon_id=:event_coupon_id";
                                // $Binding7 = array("event_coupon_id" => $appliedCouponId);
                                // $Result = DB::select($Sql7, $Binding7);
                                // $IsDiscountOneTime = 0;
           
                                // if (count($Result) > 0) {
                                //     $IsDiscountOneTime = $Result[0]->discount_type;
                                //     $Sql8 = "";
                                //     $Binding8 = [];
                                //     if ($IsDiscountOneTime == 1) {
                                //         $Binding8 = array("event_coupon_id" => $appliedCouponId);
                                //         $Sql8 = "UPDATE event_coupon_details SET end_coupon=1 WHERE event_coupon_id=:event_coupon_id";
                                //         DB::update($Sql8, $Binding8);
                                //     }
                                // }
                            }
                        }
                    }

                    //$BookingDetailsId = 3306 // temp 

                    #ADD EXTRA AMOUNT FOR PAYABLE FOR USER in booking_details
                    if (!empty($ExtraPricing)) {
                        foreach ($ExtraPricing as $value) {
                            $Binding4 = [];
                            $Sql4 = "";
                            $Binding4 = array(
                                "booking_id" => $BookingId,
                                "event_id" => $EventId,
                                "user_id" => $UserId,
                                "ticket_id" => $value->ticket_id,
                                "quantity" => 0,
                                "ticket_amount" => $value->value,
                                "ticket_discount" => 0,
                                "booking_date" => strtotime("now"),
                                "question_id" => $value->question_id,
                                "attendee_number" => $value->aNumber
                            );
                            $Sql4 = "INSERT INTO booking_details (booking_id,event_id,user_id,ticket_id,quantity,ticket_amount,ticket_discount,booking_date,question_id,attendee_number) VALUES (:booking_id,:event_id,:user_id,:ticket_id,:quantity,:ticket_amount,:ticket_discount,:booking_date,:question_id,:attendee_number)";
                            DB::insert($Sql4, $Binding4);

                            #ADD COUNT IN extra_pricing_booking TABLE
                            $Binding5 = [];
                            $Sql5 = "";
                            $CurrentSoldCount = 0;

                            #Check If Question Id & Option Id Exists In extra_pricing_booking Table Or Not. If Yes Then Get The Current Count
                            if (!empty($value->count)) {
                                $SqlExist = "SELECT id,current_count,option_id FROM extra_pricing_booking WHERE question_id =:question_id AND option_id=:option_id";
                                $Exist = DB::select($SqlExist, array("question_id" => $value->question_id, "option_id" => $value->option_id));

                                if (sizeof($Exist) > 0) {
                                    #UPDATE THE RECORD GET CURRENT COUNT FROM SAME TABLE
                                    $ExistId = $Exist[0]->id;
                                    $SoldCount = $Exist[0]->current_count;
                                    $CurrentSoldCount = $SoldCount + 1;

                                    if ($value->count >= $CurrentSoldCount) {
                                        $Binding5 = array(
                                            "event_id" => $EventId,
                                            "booking_id" => $BookingId,
                                            "user_id" => $UserId,
                                            "ticket_id" => $value->ticket_id,
                                            "total_count" => $value->count,
                                            "current_count" => $CurrentSoldCount,
                                            "last_booked_date" => strtotime('now'),
                                            "id" => $ExistId
                                        );
                                        $Sql5 = "UPDATE extra_pricing_booking SET
                                                  event_id = :event_id,
                                                  booking_id = :booking_id,
                                                  user_id = :user_id,
                                                  ticket_id = :ticket_id,
                                                  total_count = :total_count,
                                                  current_count = :current_count,
                                                  last_booked_date = :last_booked_date
                                                  WHERE id = :id";
                                        DB::update($Sql5, $Binding5);
                                    } else {
                                        // $ResposneCode = 400;
                                        // $message = 'The ' . $value["question_label"] . ' you want to add is out of stock.';
                                    }
                                } else {
                                    #ADD A NEW RECORD TO THE TABLE
                                    $CurrentSoldCount = 1;
                                    $Binding5 = array(
                                        "event_id" => $EventId,
                                        "booking_id" => $BookingId,
                                        "user_id" => $UserId,
                                        "ticket_id" => $value->ticket_id,
                                        "question_id" => $value->question_id,
                                        "option_id" => $value->option_id,
                                        "total_count" => $value->count,
                                        "current_count" => $CurrentSoldCount,
                                        "first_booked_date" => strtotime('now')
                                    );
                                    $Sql5 = "INSERT INTO extra_pricing_booking (event_id,booking_id,user_id,ticket_id,question_id,option_id,total_count,current_count,first_booked_date) VALUES (:event_id,:booking_id,:user_id,:ticket_id,:question_id,:option_id,:total_count,:current_count,:first_booked_date)";
                                    DB::insert($Sql5, $Binding5);
                                }
                            }

                        }
                    }
                    #ATTENDEE DETAILS
                    $separatedArrays = [];
                    $first_name = null;
                    $last_name = null;
                    $email = null;
                    $mobile = null;

                    $IdBookingDetails = 0;

                    // dd($FormQuestions);
                    foreach ($FormQuestions as $key => $arrays) {
                        foreach ($arrays as $subArray) {
                            $separatedArrays[] = json_encode($subArray);
                        }
                    }

                    // dd($separatedArrays);

                    foreach ($separatedArrays as $key => $value) {
                        $subArray = [];
                        $subArray = json_decode($value);
                        $TicketId = 0;
                        $participants_files = [];
                        // dd($subArray);
                        $isSendEmail = 0;
                        foreach ($subArray as $key => $sArray) {
                            if (isset($sArray->question_form_name)) {
                                if ($sArray->question_form_name == 'first_name') {
                                    $first_name = $sArray->ActualValue;
                                } elseif ($sArray->question_form_name == 'last_name') {
                                    $last_name = $sArray->ActualValue;
                                } elseif ($sArray->question_form_type == 'email') {
                                    // $email = $sArray->ActualValue;
                                    if($isSendEmail == 0){
                                        $email = $sArray->ActualValue;
                                        $isSendEmail = 1;
                                    }
                                } elseif ($sArray->question_form_type == 'mobile' && $sArray->question_label == 'Mobile Number') {
                                    $mobile = $sArray->ActualValue;
                                } elseif ($sArray->question_form_type == 'file') {
                                    $participants_files[] = $sArray->ActualValue;
                                }
                            }
                            if (empty($TicketId)) {
                                $TicketId = !empty($sArray->TicketId) ? $sArray->TicketId : 0;
                            }

                             //------------------ new quetision limit wise insert/update entry -------------------------------
                            //dd($new_ticket_id);
                            // $loc_ticket_id = !empty($TicketId) ? $TicketId : $new_ticket_id;
                           
                            // if(!empty($sArray->question_form_option) && $sArray->question_form_type == 'select' && $sArray->question_option_limit_flag == 1){
                            //     $subQuestionArray = json_decode($sArray->question_form_option);
                            //     // dd($subQuestionArray);
                            //     $total_count = 0; $label = '';
                            //     if(!empty($subQuestionArray)){
                            //         foreach($subQuestionArray as $res){
                            //             if($sArray->ActualValue == $res->id){
                            //               $total_count = $res->count;
                            //               $label = $res->label;
                            //             }
                            //         }
                            //     }
                            //     $loc_option_id = !empty($sArray->ActualValue) ? $sArray->ActualValue : 0 ;
                            //     // dd($loc_option_id);

                            //     $sql = "SELECT COUNT(id) as tot_count,current_count,id FROM extra_question_limit_booking WHERE event_id =:event_id AND ticket_id =:ticket_id AND option_id =:option_id";
                            //     $extraQuestionDet = DB::select($sql, array('event_id' => $EventId, 'ticket_id' => $loc_ticket_id, 'option_id' => $loc_option_id));
                                
                            //     // dd($extraQuestionDet);
                            //     if(!empty($extraQuestionDet) && $extraQuestionDet[0]->tot_count == 1){

                            //         $Binding5 = array(
                            //                 "total_count"   => $total_count,
                            //                 "current_count" => ($extraQuestionDet[0]->current_count)+1,
                            //                 "datetime"      => strtotime('now'),
                            //                 "id"            => $extraQuestionDet[0]->id
                            //         );
                            //         $Sql5 = "UPDATE extra_question_limit_booking SET
                            //                   total_count =:total_count,
                            //                   current_count =:current_count,
                            //                   datetime =:datetime
                            //                   WHERE id =:id";
                            //         DB::update($Sql5, $Binding5);

                            //     }else{
                            //         $Binding6 = array(
                            //                 "user_id" => $UserId,
                            //                 "event_id" => $EventId,
                            //                 "ticket_id" => $loc_ticket_id,
                            //                 "event_question_id" => $sArray->id,
                            //                 "label" => $label,
                            //                 "option_id" => $loc_option_id,
                            //                 "total_count" => $total_count,
                            //                 "current_count" => 1,
                            //                 "datetime" => strtotime('now')
                            //             );
                            //         $Sql6 = "INSERT INTO extra_question_limit_booking (user_id,event_id,ticket_id,event_question_id,label,option_id,total_count,current_count,datetime) VALUES (:user_id,:event_id,:ticket_id,:event_question_id,:label,:option_id,:total_count,:current_count,:datetime)";
                            //         DB::insert($Sql6, $Binding6);
                            //     }

                            // }
                            //------------------------------------
                        }
                        // die;
                        $IdBookingDetails = isset($BookingDetailsIds[$TicketId]) ? $BookingDetailsIds[$TicketId] : 0;
                        $SingleTicketAmount = isset($BookingDetailsTicketPrice[$TicketId]) ? $BookingDetailsTicketPrice[$TicketId] : 0;
                        $FinalTicketAmount = isset($BookingDetailsTicketFinalPrice[$TicketId]) ? $BookingDetailsTicketFinalPrice[$TicketId] : 0;
                        // echo $FinalTicketAmount.'<br>';
                        $sql = "INSERT INTO attendee_booking_details (booking_details_id,ticket_id,attendee_details,email,firstname,lastname,mobile,created_at,ticket_price,final_ticket_price) VALUES (:booking_details_id,:ticket_id,:attendee_details,:email,:firstname,:lastname,:mobile,:created_at,:ticket_price,:final_ticket_price)";
                        $Bind1 = array(
                            "booking_details_id" => !empty($IdBookingDetails) ? $IdBookingDetails : $BookingDetailsId,
                            "ticket_id" => !empty($TicketId) ? $TicketId : $new_ticket_id,
                            "attendee_details" => json_encode($value),
                            "email" => strtolower($email),
                            "firstname" => $first_name,
                            "lastname" => $last_name,
                            "mobile" => $mobile,
                            "created_at" => strtotime("now"),
                            "ticket_price" => !empty($SingleTicketAmount) ? $SingleTicketAmount : 0,
                            "final_ticket_price" => !empty($FinalTicketAmount) ? $FinalTicketAmount : 0
                        );
                        DB::insert($sql, $Bind1);
                        $attendeeId = DB::getPdo()->lastInsertId();
                        // dd($attendeeId);
                        // dd($participants_files);
                        // ---------save documents
                        if (!empty($participants_files)) {
                            foreach ($participants_files as $key => $value) {
                                $sSql = "INSERT INTO attendee_documents (attendee_booking_id,document_name) VALUES (:attendee_booking_id,:document_name)";
                                DB::update($sSql, array("attendee_booking_id" => $attendeeId, 'document_name' => $value));
                            }
                        }
                        // -------------

                        $booking_date = 0; $uniqueId = 0;
                        $bd_sql = "SELECT booking_date FROM booking_details WHERE id = :booking_details_id";
                        $bd_bind = DB::select($bd_sql, array("booking_details_id" => $BookingDetailsId));
                        if (count($bd_bind) > 0) {
                            $booking_date = $bd_bind[0]->booking_date;
                            $uniqueId = $EventId . "-" . $attendeeId . "-" . $booking_date;
                        }
                        
                        // dd($uniqueId,$IdBookingDetails,$booking_date);
                        $u_sql = "UPDATE attendee_booking_details SET registration_id=:registration_id WHERE id=:id";
                        $u_bind = DB::update($u_sql, array("registration_id" => $uniqueId, 'id' => $attendeeId));

                        //------------ new added
                        $sql1 = "SELECT ticket_name FROM event_tickets WHERE id = :ticket_id";
                        $aResult1 = DB::select($sql1, array("ticket_id" => !empty($TicketId) ? $TicketId : $new_ticket_id));
                        $new_ticket_name_array = !empty($aResult1) ? array_column($aResult1,"ticket_name") : [];
                        $new_registration_id_array[] = $uniqueId;
                        
                    }//die;

                    //------- new added for update ticket_names, registration_ids on 25-06-24 (because send email to reg no, tick name issue)
                    $loc_ticket_names = !empty($new_ticket_name_array) ? implode(", ", array_unique($new_ticket_name_array)) : '';
                    $loc_registration_id = !empty($new_registration_id_array) ? implode(", ", $new_registration_id_array) : '';
                     // dd($loc_ticket_names,$loc_registration_id);
                    $up_sql = "UPDATE booking_payment_details SET ticket_names =:ticket_names, registration_ids =:registration_ids  WHERE id=:id";
                    DB::update($up_sql, array("ticket_names" => $loc_ticket_names, "registration_ids" => $loc_registration_id, 'id' => $BookingPaymentId));

                    // -------------------------------------------END ATTENDEE DETAIL

                    $ResposneCode = 200;
                    $message = 'Request processed successfully';
                    $EventUrl = isset($request->EventUrl) && !empty($request->EventUrl) ? $request->EventUrl : "";

                } else {
                    $ResposneCode = 400;
                    $message = 'Invalid request';
                }
            } else {
                $ResposneCode = 400;
                $message = $field . ' is empty';
            }
        } else {
            $ResposneCode = $aToken['code'];
            $message = $aToken['message'];
        }

        $response = [
            'data' => $ResponseData,
            'message' => $message
        ];

        return response()->json($response, $ResposneCode);
    }

    function sendBookingMail($UserId, $UserEmail, $EventId, $EventUrl, $TotalNoOfTickets, $TotalPrice, $BookingPayId, $flag, $attendee_array, $send_email_status=0)
    {
        // dd($TotalPrice);
        // $Email1 = new Emails();
        // $Email1->save_email_log('test email1', 'startshant@gmail.com', 'log test', $UserEmail, $flag);

        // dd($UserId, $UserEmail, $EventId, $EventUrl, $TotalNoOfTickets, $TotalPrice, $BookingPayId, $flag, $attendee_array);
        $master = new Master();
        $sql1 = "SELECT * FROM users WHERE id=:user_id";
        $User = DB::select($sql1, ['user_id' => $UserId]);

        $sql2 = "SELECT * FROM events WHERE id=:event_id";
        $Event = DB::select($sql2, ['event_id' => $EventId]);
        $Venue = "";
        if (count($Event) > 0) {
            $Venue .= ($Event[0]->address !== "") ? $Event[0]->address . ", " : "";
            $Venue .= ($Event[0]->city !== "") ? $master->getCityName($Event[0]->city) . ", " : "";
            $Venue .= ($Event[0]->state !== "") ? $master->getStateName($Event[0]->state) . ", " : "";
            $Venue .= ($Event[0]->country !== "") ? $master->getCountryName($Event[0]->country) . ", " : "";
            $Venue .= ($Event[0]->pincode !== "") ? $Event[0]->pincode . ", " : "";
            // $Venue = $Event[0]->address.", ".$Event[0]->city.", ".$Event[0]->state.", ".$Event[0]->country.", ".$Event[0]->pincode;
        }

        if(!empty($Event) && isset($Event[0]->event_type) && $Event[0]->event_type == 2){
            $Venue = 'Virtual Event';
        }

        $sql3 = "SELECT * FROM organizer WHERE user_id=:user_id";
        $Organizer = DB::select($sql3, ['user_id' => $UserId]);
        $OrgName = "";
        if (count($Organizer) > 0) {
            $OrgName = !empty($Organizer[0]->name) ? ucfirst($Organizer[0]->name) : '' ;
        }

        //------ ticket registration id and race category
        $sql2 = "select bd.id,bd.ticket_id,eb.cart_details from event_booking as eb left join booking_details as bd on bd.booking_id = eb.id left join attendee_booking_details as abd on abd.booking_details_id = bd.id WHERE bd.event_id = :event_id AND eb.booking_pay_id =:booking_pay_id order by bd.booking_id asc limit 1"; // GROUP BY bd.booking_id
        $booking_detail_Result = DB::select($sql2, array('event_id' => $EventId, 'booking_pay_id' => $BookingPayId));
        // dd($booking_detail_Result);
        $booking_detail_id = !empty($booking_detail_Result) ? $booking_detail_Result[0]->id : 0;
        $ticket_id         = !empty($booking_detail_Result) ? $booking_detail_Result[0]->ticket_id : 0;
        $cart_details = !empty($booking_detail_Result) ? json_decode($booking_detail_Result[0]->cart_details) : [];
        $single_ticket_price = !empty($cart_details) ? $cart_details[0]->BuyerPayment : '0.00';
         // dd($single_ticket_price);

        $SQL1 = "SELECT id,ticket_id,email,firstname,lastname,registration_id,(select ticket_name from event_tickets where id = attendee_booking_details.ticket_id) as ticket_name,attendee_details FROM attendee_booking_details WHERE booking_details_id =:booking_details_id";
        $tAttendeeResult = DB::select($SQL1, array('booking_details_id' => $booking_detail_id));
        //dd($tAttendeeResult, $BookingPayId , $booking_detail_id, $flag , $EventId);
        $registration_ids = $ticket_names = '';
        
        $attendee_id = !empty($tAttendeeResult) ? $tAttendeeResult[0]->id : 0;
        $attendee_details = !empty($tAttendeeResult[0]->attendee_details) ? json_decode(json_decode($tAttendeeResult[0]->attendee_details)) : '';
        // dd($attendee_details);

        $emailPlaceholders_array = $FinalEmailArray = []; 
        $TeamName = $Participant_2_name = $Participant_3_name = $Participant_4_name = $preferred_date = $run_category = ''; 
        if(!empty($attendee_details)){
            foreach($attendee_details as $res){
                if($res->question_form_name == "enter_team_name"){
                    $TeamName = $res->ActualValue;
                }
                if($res->question_form_name == "participant_2_name"){
                    $Participant_2_name = $res->ActualValue;
                }
                if($res->question_form_name == "participant_3_name"){
                    $Participant_3_name = $res->ActualValue;
                }
                if($res->question_form_name == "participant_4_name"){
                    $Participant_4_name = $res->ActualValue;
                }

                if($res->question_form_name == "preferred_date_for_the_carnival"){
                    $preferred_date_json = json_decode($res->question_form_option);
                    foreach ($preferred_date_json as $item) {
                        if ($item->id == $res->ActualValue){
                            $preferred_date = $item->label;
                            break;
                        }
                    }
                }

                if($res->question_form_name == "select_your_run_category"){
                    $run_category_json = json_decode($res->question_form_option);
                    foreach ($run_category_json as $item) {
                        if ($item->id == $res->ActualValue){
                            $run_category = $item->label;
                            break;
                        }
                    }
                }

                //------------------ new added on 15-11-24  (Email Placeholder Replace)
                $sql2 = "SELECT question_form_name,placeholder_name,(select question_form_type from event_form_question where id = email_placeholders.question_id) as question_form_type,(select question_form_option from event_form_question where id = email_placeholders.question_id) as question_form_option FROM email_placeholders WHERE status = 1 AND event_id = ".$EventId." ";

                if(empty($res->parent_question_id)){
                    $sql2 .= " AND question_form_name = '".$res->question_form_name."' ";
                }else{
                    $sql2 .= " AND question_form_name = LOWER(REPLACE('".$res->question_label."', ' ', '_')) ";
                }
             
                $emailPlaceHolderResult = DB::select($sql2, []);
                // dd($emailPlaceHolderResult);

                if(!empty($emailPlaceHolderResult) && $emailPlaceHolderResult[0]->question_form_type != "file"){
                    $question_form_option = !empty($emailPlaceHolderResult[0]->question_form_option) ? json_decode($emailPlaceHolderResult[0]->question_form_option, true) : [];
                    $label = ''; $labels = []; $acutal_value = '';

                    if($emailPlaceHolderResult[0]->question_form_type == "countries"){
                        $acutal_value = !empty($res->ActualValue) ? $master->getCountryName($res->ActualValue) : "";
                    }else if ($emailPlaceHolderResult[0]->question_form_type == "states") {
                        $acutal_value = !empty($res->ActualValue) ? $master->getStateName($res->ActualValue) : "";
                    }else if ($emailPlaceHolderResult[0]->question_form_type == "cities") {
                        $acutal_value = !empty($res->ActualValue) ? $master->getCityName($res->ActualValue) : "";
                    }else if($emailPlaceHolderResult[0]->question_form_type == "date"){
                        $acutal_value = !empty($res->ActualValue) ? date('d-m-Y',strtotime($res->ActualValue)) : '';
                    }else if($emailPlaceHolderResult[0]->question_form_type == "radio" || $emailPlaceHolderResult[0]->question_form_type == "select"){
                      
                        if(!empty($res->ActualValue) && !empty($question_form_option)){
                            foreach ($question_form_option as $option) {
                                if ((int)$option['id'] === (int)$res->ActualValue) {
                                    $label = $option['label'];
                                    break;
                                }
                            }
                            $acutal_value = !empty($label) ? $label : '';
                        }
                    }else if($emailPlaceHolderResult[0]->question_form_type == "checkbox"){
                        if(isset($res->ActualValue) && !empty($res->ActualValue)){
                            foreach ($question_form_option as $option) {
                                if (in_array($option['id'], explode(',', $res->ActualValue))) {
                                    $labels[] = $option['label'];
                                }
                            }
                            $label = implode(', ', $labels);
                        }
                        $acutal_value = !empty($label) ? $label : '';
                    }else{                                              // [text/email/textarea/amount/time]
                        $acutal_value = !empty($res->ActualValue) ? $res->ActualValue : '';
                    }

                    $emailPlaceholders_array[] = [$emailPlaceHolderResult[0]->placeholder_name => trim(ucfirst($acutal_value))];
                }

            }
        }
        // dd($emailPlaceholders_array);
        
        if(!empty($emailPlaceholders_array)){
            foreach ($emailPlaceholders_array as $item) {  
                $key = key($item);
                $value = reset($item);
                $FinalEmailArray[$key] = $value;
            }
        } 
         // dd($FinalEmailArray);

        if (!empty($tAttendeeResult)) {
            $registration_ids_array = array_column($tAttendeeResult, "registration_id");
            $registration_ids = implode(", ", $registration_ids_array);

            $ticket_ids_array = array_column($tAttendeeResult, "ticket_name");
            $ticket_names = implode(", ", array_unique($ticket_ids_array));

        }
        //dd($registration_ids,$ticket_names);
        if ($flag == 2 && !empty($attendee_array)) {
            $user_name = $attendee_array['username'];
            $first_name = $attendee_array['firstname'];
            $last_name = $attendee_array['lastname'];
            $registration_id = $attendee_array['registration_id'];
            $ticket_names = $attendee_array['ticket_name'];
        } else {
            $user_name = $User[0]->firstname . " " . $User[0]->lastname;
            $first_name = !empty($tAttendeeResult) && $tAttendeeResult[0]->firstname ? $tAttendeeResult[0]->firstname : $User[0]->firstname;
            $last_name = !empty($tAttendeeResult) && $tAttendeeResult[0]->lastname ? $tAttendeeResult[0]->lastname : $User[0]->lastname;
            $registration_id = $registration_ids;  
            $ticket_names = $ticket_names;   
        }

        //------------ new added
        $sql5 = "SELECT ticket_names,registration_ids FROM booking_payment_details WHERE id=:id";
        $BookingPaymentDetails = DB::select($sql5, ['id' => $BookingPayId]);
       
        $loc_registration_id = !empty($BookingPaymentDetails) ? $BookingPaymentDetails[0]->registration_ids : '';
        $loc_ticket_names    = !empty($BookingPaymentDetails) ? $BookingPaymentDetails[0]->ticket_names : '';

        $ConfirmationEmail = array(
            // "USERID" => $UserId,
            "USERNAME" => ucfirst($user_name),
            "FIRSTNAME" => ucfirst($first_name),
            "LASTNAME" => ucfirst($last_name),
            "EVENTID" => $EventId,
            "EVENTNAME" => ucfirst($Event[0]->name),
            "EVENTSTARTDATE" => (!empty($Event[0]->start_time)) ? date('d-m-Y', ($Event[0]->start_time)) : "",
            "EVENTSTARTTIME" => (!empty($Event[0]->start_time)) ? date('H:i A', ($Event[0]->start_time)) : "",
            "EVENTENDDATE" => (!empty($Event[0]->end_time)) ? date('d-m-Y', ($Event[0]->end_time)) : "",
            "EVENTENDTIME" => (!empty($Event[0]->end_time)) ? date('H:i A', ($Event[0]->end_time)) : "",
            "YTCRTEAM" => "YouTooCanRun Team",
            "EVENTURL" => $EventUrl,
            "COMPANYNAME" => $OrgName,
            "TOTALTICKETS" => $TotalNoOfTickets,
            "VENUE" => $Venue,
            "TOTALAMOUNT" => $TotalPrice,
            "TICKETAMOUNT" => !empty($single_ticket_price) ? '₹ '.$single_ticket_price : $TotalPrice,
            "REGISTRATIONID" => !empty($registration_id) ? $registration_id : $loc_registration_id, //!empty($registration_ids) ? $registration_ids : '', 
            "RACECATEGORY" => !empty($ticket_names) ? ucfirst($ticket_names) : ucfirst($loc_ticket_names), // !empty($ticket_names) ? $ticket_names : ''
            "TEAMNAME"       => isset($TeamName) && !empty($TeamName) ? ucfirst($TeamName) : '',
            "2NDPARTICIPANT" => isset($Participant_2_name) && !empty($Participant_2_name) ? ucfirst($Participant_2_name) : '',
            "3RDPARTICIPANT" => isset($Participant_3_name) && !empty($Participant_3_name) ? ucfirst($Participant_3_name) : '',
            "4THPARTICIPANT" => isset($Participant_4_name) && !empty($Participant_4_name) ? ucfirst($Participant_4_name) : '',
            "PREFERREDDATE"  => isset($preferred_date) && !empty($preferred_date) ? $preferred_date : '',
            "RUNCATEGORY"    => isset($run_category) && !empty($run_category) ? ucfirst($run_category) : ''
        );

        if(!empty($FinalEmailArray))
            $ConfirmationEmail = array_merge($ConfirmationEmail,$FinalEmailArray);

        // dd($ConfirmationEmail); 
        $Subject = "";
        //--------------- new added as per client requirement (pokemon event - Family Run then only)
        if($ticket_id == 108 || $ticket_id == 109){ 
           
            $MessageContent = "<p>Dear {USERNAME},
                <br><br>Congratulations! Your registration for the <strong>Pokémon Carnival 2025</strong> is confirmed. We’re excited to welcome you to a world of Pokémon-themed activities, games, and unforgettable experiences.
                <br><br>Your details are:<br><br>
                <strong>Name : {FIRSTNAME} {LASTNAME}</strong><br>
                <strong>Category : {RACECATEGORY}</strong><br>";
                
            if($ticket_id == 109){
                $MessageContent .= "<strong>Accompanying Parent Name : {ACCOMPANY_PARENT_NAME}</strong><br>
                <strong>Accompanying Sibling 1 Name (if selected) : {SIBLING_NAME_1}</strong><br>
                <strong>Accompanying Sibling 2 Name (if selected) : {SIBLING_NAME_2}</strong><br>";
            }else{
                $MessageContent .= "<strong>Accompanying Parent Name : </strong><br>
                <strong>Accompanying Sibling 1 Name (if selected) : </strong><br>
                <strong>Accompanying Sibling 2 Name (if selected) : </strong><br>"; 
            }

            $MessageContent .= "<strong>Timing : 3:00 pm to 10:00 pm</strong><br>
                <strong>Registration ID : {REGISTRATIONID}</strong><br>
                <strong>Location : Jio World Garden, Bandra Kurla Complex, Mumbai</strong><br>
                <strong>Preferred Date for attending Pokémon Carnival : {PREFERREDDATE}</strong><br>
                <strong>Cost : {TOTALAMOUNT}</strong><br><br>
                If you have any questions, feel free to reach out to us at support@youtoocanrun.com. We can’t wait to see you at the starting line!</p><br>
                <p>Best regards,<br/>
                <strong>Team Pokémon Carnival and Run</strong></p>";

            $Subject = "Your Pokémon Carnival 2025 Entry is Confirmed!";
        }else{
            $sql = "SELECT * FROM `event_communication` WHERE `event_id`=:event_id AND email_type = 1";
            $Communications = DB::select($sql, ["event_id" => $EventId]); // "subject_name" => strtoupper("Registration Confirmation")
            // dd($Communications);
            if (count($Communications) > 0) {
                $MessageContent = $Communications[0]->message_content;
                $Subject = $Communications[0]->subject_name;
            } else {
                $MessageContent = "Dear " . ucfirst($first_name) . " " . ucfirst($last_name) . ",
                     <br/><br/>
                    Thank you for registering for " . ucfirst($Event[0]->name) . "! We are thrilled to have you join us.
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
                    Best regards,<br/>if(!empty($FinalEmailArray))
            $ConfirmationEmail = array_merge($ConfirmationEmail,$FinalEmailArray);
                    " . ucfirst($Event[0]->name) . " Team";
                $Subject = "Event Registration Confirmation - " . ucfirst($Event[0]->name) . "";
            }
        }
        
        // dd($ConfirmationEmail);
        foreach ($ConfirmationEmail as $key => $value) {
            if (isset($key)) {
                $placeholder = '{' . $key . '}';
                $MessageContent = str_replace($placeholder, $value, $MessageContent);
            }
        }

        // attach image
        if(!empty($Communications) && !empty($Communications[0]->content_image)){

            $image_path = url('/').'/uploads/communication_email_images/'.$Communications[0]->content_image;
            $attach_image = '<img src="'.str_replace(" ", "%20", $image_path).'" alt="Image">';
            $MessageContent .= ' <br/><br/>';
            $MessageContent .= $attach_image;
        }

        // Output the filled message
        // dd($MessageContent,$Subject); 
        // echo $MessageContent; die;
       
        //--------------- new added for generate pdf ----------------
        $generatePdf = EventTicketController::generateParticipantPDF($EventId,$UserId,$ticket_id,$attendee_id,$EventUrl,$TotalPrice,$booking_detail_id);
        // dd($generatePdf);
      
        $Email = new Emails();
        $Email->send_booking_mail($UserId, $UserEmail, $MessageContent, $Subject, $flag, $send_email_status, $generatePdf, $EventId);


        //--------- Send emails to participants also along with registering person
        // dd($tAttendeeResult);
        if (!empty($tAttendeeResult) && $flag == 1) {
            foreach ($tAttendeeResult as $res) {
                
                $emailPlaceholders_array = $FinalEmailArray = []; $label = $acutal_value = ''; $labels = []; 
                $attendee_email = !empty($res->email) ? $res->email : '';
                $attendee_firstname = !empty($res->firstname) ? $res->firstname : '';

                $attendee_details_result = !empty($res->attendee_details) ? json_decode(json_decode($res->attendee_details)) : '';
                // dd($attendee_details);
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
                        $sql2 = "SELECT question_form_name,placeholder_name,(select question_form_type from event_form_question where id = email_placeholders.question_id) as question_form_type,(select question_form_option from event_form_question where id = email_placeholders.question_id) as question_form_option FROM email_placeholders WHERE status = 1 AND question_form_name =:question_form_name AND event_id = ".$EventId." ";

                        // if(empty($res1->parent_question_id)){
                        //     $sql2 .= " AND question_form_name = '".$res1->question_form_name."' ";
                        // }else{
                        //     $sql2 .= " AND question_form_name = LOWER(REPLACE('".$res1->question_label."', ' ', '_')) ";
                        // }
                        if(empty($res->parent_question_id)){
                            $emailPlaceHolderResult = DB::select($sql2, array('question_form_name' => $res1->question_form_name));
                        }else{
                            $emailPlaceHolderResult = DB::select($sql2, array('question_form_name' => strtolower(str_replace(" ", "_", $res1->question_label))));
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

                    } // foreach end
                } //end if

                if(!empty($emailPlaceholders_array)){
                    foreach ($emailPlaceholders_array as $item) {  
                        $key = key($item);
                        $value = reset($item);
                        $FinalEmailArray[$key] = $value;
                    }
                } 

                //dd($FinalEmailArray);
                
                $ConfirmationEmail = array(
                    // "USERID" => $UserId,
                    "USERNAME" => !empty($res->firstname) && !empty($res->lastname) ? ucfirst($res->firstname) . ' ' . ucfirst($res->lastname) : ucfirst($user_name),
                    "FIRSTNAME" => !empty($res->firstname) ? ucfirst($res->firstname) : ucfirst($first_name),
                    "LASTNAME" => !empty($res->lastname) ? ucfirst($res->lastname) : ucfirst($last_name),
                    "EVENTID" => $EventId,
                    "EVENTNAME" => $Event[0]->name,
                    "EVENTSTARTDATE" => (!empty($Event[0]->start_time)) ? date('d-m-Y', ($Event[0]->start_time)) : "",
                    "EVENTSTARTTIME" => (!empty($Event[0]->start_time)) ? date('H:i A', ($Event[0]->start_time)) : "",
                    "EVENTENDDATE" => (!empty($Event[0]->end_time)) ? date('d-m-Y', ($Event[0]->end_time)) : "",
                    "EVENTENDTIME" => (!empty($Event[0]->end_time)) ? date('H:i A', ($Event[0]->end_time)) : "",
                    "YTCRTEAM" => "YouTooCanRun Team",
                    "EVENTURL" => $EventUrl,
                    "COMPANYNAME" => $OrgName,
                    "TOTALTICKETS" => 1,  //$TotalNoOfTickets, (because one attendee purchase on a single ticket)
                    "VENUE" => $Venue,
                    "TOTALAMOUNT" => $TotalPrice,
                    "TICKETAMOUNT" => !empty($single_ticket_price) ? '₹ '.$single_ticket_price : '₹ '.$TotalPrice,
                    "REGISTRATIONID" => !empty($res->registration_id) ? $res->registration_id : $registration_id,
                    "RACECATEGORY" => !empty($res->ticket_name) ? ucfirst($res->ticket_name) : ucfirst($ticket_names),
                    "TEAMNAME"       => isset($TeamName) && !empty($TeamName) ? ucfirst($TeamName) : '',
                    "2NDPARTICIPANT" => isset($Participant_2_name) && !empty($Participant_2_name) ? ucfirst($Participant_2_name) : '',
                    "3RDPARTICIPANT" => isset($Participant_3_name) && !empty($Participant_3_name) ? ucfirst($Participant_3_name) : '',
                    "4THPARTICIPANT" => isset($Participant_4_name) && !empty($Participant_4_name) ? ucfirst($Participant_4_name) : '',
                    "PREFERREDDATE"  => isset($preferred_date) && !empty($preferred_date) ? $preferred_date : '',
                    "RUNCATEGORY"    => isset($run_category) && !empty($run_category) ? ucfirst($run_category) : ''
                );

                if(!empty($FinalEmailArray))
                    $ConfirmationEmail = array_merge($ConfirmationEmail,$FinalEmailArray);

                $Subject = "";
                //--------------- new added as per client requirement
                if($ticket_id == 108 || $ticket_id == 109){
                    $MessageContent = "<p>Dear {USERNAME},
                    <br><br>Congratulations! Your registration for the <strong>Pokémon Carnival 2025</strong> is confirmed. We’re excited to welcome you to a world of Pokémon-themed activities, games, and unforgettable experiences.
                    <br><br>Your details are:<br><br>
                    <strong>Name : {FIRSTNAME} {LASTNAME}</strong><br>
                    <strong>Category : {RACECATEGORY}</strong><br>";
                    
                    if($ticket_id == 109){
                        $MessageContent .= "<strong>Accompanying Parent Name : {ACCOMPANY_PARENT_NAME}</strong><br>
                        <strong>Accompanying Sibling 1 Name (if selected) : {SIBLING_NAME_1}</strong><br>
                        <strong>Accompanying Sibling 2 Name (if selected) : {SIBLING_NAME_2}</strong><br>";
                    }else{
                        $MessageContent .= "<strong>Accompanying Parent Name : </strong><br>
                        <strong>Accompanying Sibling 1 Name (if selected) : </strong><br>
                        <strong>Accompanying Sibling 2 Name (if selected) : </strong><br>"; 
                    }

                    $MessageContent .= "<strong>Timing : 3:00 pm to 10:00 pm</strong><br>
                        <strong>Registration ID : {REGISTRATIONID}</strong><br>
                        <strong>Location : Jio World Garden, Bandra Kurla Complex, Mumbai</strong><br>
                        <strong>Preferred Date for attending Pokémon Carnival : {PREFERREDDATE}</strong><br>
                        <strong>Cost : {TOTALAMOUNT}</strong><br><br>
                        If you have any questions, feel free to reach out to us at support@youtoocanrun.com. We can’t wait to see you at the starting line!</p><br>
                        <p>Best regards,<br/>
                        <strong>Team Pokémon Carnival and Run</strong></p>";

                    $Subject = "Your Pokémon Carnival 2025 Entry is Confirmed!";
                }else{
                    $sql = "SELECT * FROM `event_communication` WHERE `event_id`=:event_id AND email_type = 1";
                    $Communications = DB::select($sql, ["event_id" => $EventId]); // "subject_name" => strtoupper("Registration Confirmation")
                    // dd($Communications);
                    if (count($Communications) > 0) {
                        $MessageContent = $Communications[0]->message_content;
                        $Subject = $Communications[0]->subject_name;
                    } else {
                        $MessageContent = "Dear " . ucfirst($first_name) . " " . ucfirst($last_name) . ",
                             <br/><br/>
                            Thank you for registering for " . ucfirst($Event[0]->name) . "! We are thrilled to have you join us.
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
                            " . ucfirst($Event[0]->name) . " Team";
                        $Subject = "Event Registration Confirmation - " . ucfirst($Event[0]->name) . "";
                    }
                }
         
                foreach ($ConfirmationEmail as $key => $value) {
                    if (isset($key)) {
                        $placeholder = '{' . $key . '}';
                        $MessageContent = str_replace($placeholder, $value, $MessageContent);
                    }
                }

                // attach image
                if(!empty($Communications) && !empty($Communications[0]->content_image)){

                    $image_path = url('/').'/uploads/communication_email_images/'.$Communications[0]->content_image;
                    $attach_image = '<img src="'.str_replace(" ", "%20", $image_path).'" alt="Image">';
                    $MessageContent .= ' <br/><br/>';
                    $MessageContent .= $attach_image;
                }

                //echo $MessageContent.'<br><br>';
                if (!empty($attendee_email) && strtolower($UserEmail) != strtolower($attendee_email)) {

                    $generatePdf = EventTicketController::generateParticipantPDF($EventId,$UserId,$res->ticket_id,$res->id,$EventUrl,$TotalPrice,$booking_detail_id);
                   
                    $Email = new Emails();
                    $Email->send_booking_mail($UserId, $attendee_email, $MessageContent, $Subject, $flag, $send_email_status, $generatePdf, $EventId);
                }

            }//die;
        }

        return;
    }

    public function generateParticipantPDF($EventId,$UserId,$TicketId,$attendeeId,$EventUrl,$TotalPrice,$booking_detail_id)
    {
        // dd($EventId,$UserId,$TicketId,$attendeeId,$TotalPrice);
        $master = new Master();
		$createdById = 0;
        $TicketArr = [];

		if (!empty($booking_detail_id)) {
			$Sql50 = 'SELECT booking_id FROM booking_details WHERE id = "' . $booking_detail_id . '" ';
			$aResult50 = DB::select($Sql50);
			if (!empty($aResult50)) {
				$bookingId = !empty($aResult50[0]->booking_id) ? $aResult50[0]->booking_id : 0;
				if (!empty($bookingId)) {
					$Sql60 = 'SELECT booking_pay_id FROM event_booking WHERE id = "' . $bookingId . '" ';
					$aResult60 = DB::select($Sql60);
					if (!empty($aResult60)) {
						$bookingPayId = !empty($aResult60[0]->booking_pay_id) ? $aResult60[0]->booking_pay_id : 0;
						if (!empty($bookingPayId)) {
							$Sql70 = 'SELECT created_by FROM booking_payment_details WHERE id = "' . $bookingPayId . '" ';
							$aResult70 = DB::select($Sql70);
							if (!empty($aResult70)) {
								$createdById = !empty($aResult70[0]->created_by) ? $aResult70[0]->created_by : 0;
							}
						}
					}
				}	
			}
		}
		$createdBy = !empty($createdById) ? $createdById : $UserId;
		
        $sql1 = "SELECT CONCAT(firstname,' ',lastname) AS username,email,mobile FROM users WHERE id=:user_id";
        $User = DB::select($sql1, ['user_id' => $createdBy]);

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

                $sql1 = "SELECT question_label,question_form_type,question_form_name,general_form_id FROM event_form_question WHERE event_id =:event_id AND is_custom_form = 0 AND show_on_ticket_pdf = 1";   // AND question_form_name != 'sub_question'
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
                if(!empty($QuestionData)){
                    foreach($QuestionData as $res){
                        foreach ($attendee_details as $detail) {
                            $aTemp = new stdClass;
                            $labels = [];
                            $question_form_option = json_decode($detail->question_form_option, true);
                            if ($detail->question_form_name == $res->question_form_name) {
                                
                                // dd($question_form_option);
                                if(($detail->question_form_type == 'radio' || $detail->question_form_type == 'select') && !empty($detail->ActualValue) && ($res->general_form_id == $res->general_form_id)){
                                    
                                    if(!array_search($detail->id, array_column($extra_details, 'id'))){
                                        $label = '';
                                        foreach ($question_form_option as $option) {
                                            if ($option['id'] === (int)$detail->ActualValue) {
                                                $label = $option['label'];
                                                break;
                                            }
                                        }
                                       
                                        $aTemp->id             = $detail->id;
                                        $aTemp->question_label = $detail->question_label;
                                        $aTemp->question_form_type = $detail->question_form_type;
                                        $aTemp->ActualValue    = $label;
                                        $extra_details[] = $aTemp;
                                         break;
                                    }
                                   
                                }else if($detail->question_form_type == 'checkbox' && !empty($detail->ActualValue) && ($res->general_form_id == $res->general_form_id)){
                                    if(!array_search($detail->id, array_column($extra_details, 'id'))){
                                        foreach ($question_form_option as $option) {
                                            if (in_array($option['id'], explode(',', $detail->ActualValue))) {
                                                $labels[] = $option['label'];
                                            }
                                        }
                                        $aTemp->id             = $detail->id;
                                        $aTemp->question_label = $detail->question_label;
                                        $aTemp->question_form_type = $detail->question_form_type;
                                        $aTemp->ActualValue   = implode(', ', $labels);
                                        $extra_details[] = $aTemp;
                                         break;
                                    }
                                }else{
                                   if(!empty($detail->ActualValue) && !array_search($detail->id, array_column($extra_details, 'id'))){
                                        $aTemp->id             = $detail->id;
                                        $aTemp->question_label = $detail->question_label;
                                        $aTemp->question_form_type = $detail->question_form_type;
                                        $aTemp->ActualValue    = $detail->ActualValue;
                                        $extra_details[] = $aTemp;
                                   }
                                }
                                 
                            }
                          

                        }
                    }
                     // dd($extra_details);
                }
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
		$qrCode = '';
		if(!empty($UniqueTicketId))
			$qrCode = base64_encode(QrCode::format('png')->size(200)->generate($UniqueTicketId));
        // dd($qrCode);
        if(!empty($TicketArr)){
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
        }
        
        // dd($data);
        $pdf = PDF::loadView('pdf_template', $data);
        $PdfName = $EventId . $TicketId . time() . '.pdf';
        $pdf->save(public_path('ticket_pdf/' . $PdfName));
        // $PdfPath = url('/') . "/ticket_pdf/" . $PdfName;
        $PdfPath = public_path('ticket_pdf/' . $PdfName);
        // dd($PdfPath);
        return $PdfPath;
    }

    function GetBookings(Request $request)
    {
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $empty = false;
        $field = '';
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        // dd($aToken);
        // $aToken['code'] = 200;
        if ($aToken['code'] == 200) {
            $aPost = $request->all();

            if (!$empty) {
                $master = new Master();
                $Auth = new Authenticate();
                $Auth->apiLog($request);

                $UserId = $aToken['data']->ID;

                // $SQL = "SELECT eb.*,
                // (SELECT name FROM events WHERE id=eb.event_id) AS EventName,
                // (SELECT start_time FROM events WHERE id=eb.event_id) AS EventStartTime,
                // (SELECT end_time FROM events WHERE id=eb.event_id) AS EventEndTime,
                // (SELECT city FROM events WHERE id=eb.event_id) AS EventCity,
                // (SELECT banner_image FROM events WHERE id=eb.event_id) AS banner_image
                //     FROM event_booking AS eb
                //     WHERE eb.user_id=:user_id
                //     AND eb.transaction_status IN (1,3)
                //     GROUP BY eb.event_id";
                // $BookingData = DB::select($SQL, array('user_id' => $UserId));

                $SQL2 = "SELECT eb.event_id ,(SELECT SUM(bd.quantity) 
                    FROM booking_details bd 
                    LEFT JOIN event_booking ebi ON bd.booking_id = ebi.id
                    WHERE
                    ebi.user_id=:user_id
                    AND ebi.transaction_status IN (1,3) AND 
                     eb.event_id = bd.event_id
                    ) AS TotalCount,
                    (SELECT name FROM events WHERE id=eb.event_id) AS EventName,
                    (SELECT start_time FROM events WHERE id=eb.event_id) AS EventStartTime,
                    (SELECT end_time FROM events WHERE id=eb.event_id) AS EventEndTime,
                    (SELECT city FROM events WHERE id=eb.event_id) AS EventCity,
                    (SELECT banner_image FROM events WHERE id=eb.event_id) AS banner_image
                    FROM event_booking AS eb
                    WHERE eb.user_id=:user_id2
                    AND eb.transaction_status IN (1,3)
                    GROUP BY eb.event_id";
                $BookingData = DB::select($SQL2, array('user_id' => $UserId,'user_id2' => $UserId));

                //dd($BookingData);
              
                $new_tot_count = 0;
                foreach ($BookingData as $event) {
                    // $SQL = "SELECT count(id) as tot_count FROM event_booking where user_id =:user_id and event_id = ".$event->event_id." and transaction_status IN (1,3) ";
                  
                    // $BookingData1 = DB::select($SQL, array('user_id' => $UserId));
                    //dd($BookingData1);
                   //   dd(array_column($BookingData, 'last_name'));
                   // $new_tot_count += $event->quantity;

                    $event->TotalCount = !empty($event->TotalCount) ? $event->TotalCount : 0;
                    $event->name = !empty($event->EventName) ? ucwords($event->EventName) : "";
                    $event->display_name = !empty($event->EventName) ? (strlen($event->EventName) > 40 ? ucwords(substr($event->EventName, 0, 40)) . "..." : ucwords($event->EventName)) : "";
                    $event->start_date = (!empty($event->EventStartTime)) ? date("d M Y", $event->EventStartTime) : 0;
                    $event->start_time_event = (!empty($event->EventStartTime)) ? date("h:i A", $event->EventStartTime) : "";
                    $event->city_name = !empty($event->EventCity) ? $master->getCityName($event->EventCity) : "";
                    $event->banner_image = !empty($event->banner_image) ? url('/') . '/uploads/banner_image/' . $event->banner_image . '' : '';
                }
                // $ResponseData['BookingData'] = $BookingData;

                $now = time(); // Get the current timestamp
                $pastEvents = [];
                $activeEvents = [];

                foreach ($BookingData as $booking) {
                    if ($booking->EventEndTime < $now) {
                        $pastEvents[] = $booking; // Push to past events array
                    } else {
                        $activeEvents[] = $booking; // Push to active events array
                    }
                }
                $ResponseData['pastEvents'] = $pastEvents;
                $ResponseData['activeEvents'] = $activeEvents;

                $ResposneCode = 200;
                $message = 'Request processed successfully';
            } else {
                $ResposneCode = 400;
                $message = $field . ' is empty';
            }
        } else {
            $ResposneCode = $aToken['code'];
            $message = $aToken['message'];
        }

        $response = [
            'data' => $ResponseData,
            'message' => $message
        ];

        return response()->json($response, $ResposneCode);
    }

    function GetEventBookingTickets(Request $request)
    {
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $empty = false;
        $field = '';
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        // dd($aToken);
        // $aToken['code'] = 200;
        if ($aToken['code'] == 200) {
            $aPost = $request->all();
            if (empty($aPost['event_id'])) {
                $empty = true;
                $field = 'Event Id';
            }

            if (!$empty) {
                $Auth = new Authenticate();
                $Auth->apiLog($request);

                $EventId = isset($aPost['event_id']) ? $aPost['event_id'] : 0;
                $UserId = $aToken['data']->ID ? $aToken['data']->ID : 20;

                $sql = "SELECT *,a.id AS attendeeId,
                (SELECT ticket_name FROM event_tickets WHERE id=bd.ticket_id AND a.ticket_id=id AND event_id=bd.event_id) AS TicketName,
                (SELECT ticket_status FROM event_tickets WHERE id=bd.ticket_id AND a.ticket_id=id AND event_id=bd.event_id) AS TicketStatus,
                (SELECT name FROM events WHERE id=bd.event_id) AS EventName,
                (SELECT start_time FROM events WHERE id=bd.event_id) AS EventStartDateTime,
                (SELECT banner_image FROM events WHERE id=bd.event_id) AS banner_image
                FROM attendee_booking_details AS a
                LEFT JOIN booking_details AS bd ON bd.id=a.booking_details_id
                LEFT JOIN event_booking AS e ON e.id=bd.booking_id
                WHERE bd.event_id=:event_id AND e.event_id=:event_id1 AND bd.quantity !=0 AND bd.user_id=:user_id AND e.transaction_status IN (1,3)";
                // dd($sql);
                $BookingData = DB::select($sql, array('user_id' => $UserId, 'event_id' => $EventId, 'event_id1' => $EventId));
                // dd($BookingData);
                foreach ($BookingData as $event) {

                    $event->TicketName = !empty($event->TicketName) ? (strlen($event->TicketName) > 40 ? ucwords(substr($event->TicketName, 0, 40)) . "..." : ucwords($event->TicketName)) : "";
                    $event->event_start_date = (!empty($event->EventStartDateTime)) ? date("d M Y", $event->EventStartDateTime) : 0;
                    $event->event_time = (!empty($event->EventStartDateTime)) ? date("h:i A", $event->EventStartDateTime) : "";

                    // $event->strike_out_price = 0;
                    $event->strike_out_price = ($event->ticket_discount != 0) ? ($event->ticket_amount - $event->ticket_discount) : $event->ticket_amount;

                    $event->name = !empty($event->EventName) ? ucwords($event->EventName) : "";
                    $event->display_name = !empty($event->EventName) ? (strlen($event->EventName) > 40 ? ucwords(substr($event->EventName, 0, 40)) . "..." : ucwords($event->EventName)) : "";
                    $event->banner_image = !empty($event->banner_image) ? url('/') . '/uploads/banner_image/' . $event->banner_image . '' : '';

                    // ticket registration number. generate it using -> (event_id + booking_id + timestamp)
                    $uniqueId = 0;
                    $uniqueId = $EventId . "-" . $event->attendeeId . "-" . $event->booking_date;
                    $event->unique_ticket_id = $uniqueId;

                    $event->attendee_name = $event->firstname . " " . $event->lastname;

                    $data = $this->getNewPricingDetails($event);
                    $event->PricingDetails = $data;

                    // $event->qrCode = base64_encode(QrCode::format('png')->size(200)->generate($event->unique_ticket_id));
                }
                $ResponseData['BookingData'] = $BookingData;

                $ResposneCode = 200;
                $message = 'Request processed successfully';
            } else {
                $ResposneCode = 400;
                $message = $field . ' is empty';
            }
        } else {
            $ResposneCode = $aToken['code'];
            $message = $aToken['message'];
        }

        $response = [
            'data' => $ResponseData,
            'message' => $message
        ];

        return response()->json($response, $ResposneCode);
    }

    function getNewPricingDetails($aData)
    {
        $filteredData = [];
        $newData = json_decode(json_decode($aData->attendee_details));

        foreach ($newData as $value) {
            if ($value->question_form_type == "amount" && !empty($value->ActualValue)) {
                $filteredData[] = [
                    'ticket_amount' => $value->ActualValue,
                    'QueLabel' => $value->question_label,
                    'attendee_id' => $aData->id
                ];
            }
            if ($value->question_form_type == "select" && $value->question_form_option !== "" && !empty($value->ActualValue)) {
                $FormOptions = json_decode($value->question_form_option);
                if (!empty($FormOptions)) {
                    foreach ($FormOptions as $form) {
                        if ($form->id == $value->ActualValue && isset($form->price) && !empty($form->price)) {
                            $filteredData[] = [
                                'ticket_amount' => $form->price,
                                'QueLabel' => $form->label,
                                'attendee_id' => $aData->id
                            ];
                        }
                    }
                }
            }
        }
        return $filteredData;
    }

    public function generatePDF(Request $request)
    {
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $empty = false;
        $field = '';
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        // dd($request);
        // $aToken['code'] = 200;
        if ($aToken['code'] == 200) {
            $aPost = $request->all();

            if (!$empty) {
                $master = new Master();
                $Auth = new Authenticate();
                $Auth->apiLog($request);

                $UserId = $aToken['data']->ID;
                // dd($UserId);
                $sql1 = "SELECT CONCAT(firstname,' ',lastname) AS username,email,mobile FROM users WHERE id=:user_id";
                $User = DB::select($sql1, ['user_id' => $UserId]);
                // $Username = (sizeof($User) > 0) ? $User[0]->username : '';
                // dd($Username);

                $Venue = "";
                $TicketArr = isset($request->ticket) ? $request->ticket : []; // ticket array
                // dd($TicketArr);
                //foreach ($TicketArr as $value) {
                    // dd($value);
                    // if (is_array($value)) {
                    //     $value["booking_start_date"] = (!empty($value["booking_date"]))? date("d M Y", $value["booking_date"]) : 0;
                    //     $value["booking_time"] = (!empty($value["booking_date"]))? date("h:i A", $value["booking_date"]) : "";
                    // } 
                //}


                $EventId = isset($TicketArr["event_id"]) ? $TicketArr["event_id"] : 0;
                $TicketId = isset($TicketArr["ticket_id"]) ? $TicketArr["ticket_id"] : 0;
                $attendeeId = isset($TicketArr["attendeeId"]) ? $TicketArr["attendeeId"] : 0;
                // dd($attendeeId);
                // attendeeId
                $AttenddeeName = isset($TicketArr["attendee_name"]) ? $TicketArr["attendee_name"] : "";
                $BookingDetailId = isset($TicketArr["booking_detail_id"]) ? $TicketArr["booking_detail_id"] : 0;
                
                $bookingPayId = isset($TicketArr["booking_pay_id"]) ? $TicketArr["booking_pay_id"] : 0;
                $sql2 = "SELECT amount FROM booking_payment_details WHERE id =:booking_pay_id";
                $PaymentDetails = DB::select($sql2, ['booking_pay_id' => $bookingPayId]);
               

                $EventLink = isset($request->event_link) ? $request->event_link : "";
                $AttendeeData = $AttenddeeDetails = [];
                // dd($final_paid_amount);
                if (!empty($attendeeId)) {
                    $sql = "SELECT attendee_details,final_ticket_price FROM attendee_booking_details WHERE id=:attendee_id";
                    $AttendeeData = DB::select($sql, ['attendee_id' => $attendeeId]);
               
                    $attendee_ticket_price = !empty($AttendeeData) ? $AttendeeData[0]->final_ticket_price : 0;
                         // dd($attendee_ticket_price);
                    if(!empty($attendee_ticket_price)){
                        $TicketArr["ticket_amount"] = !empty($attendee_ticket_price) ? '₹ '.$attendee_ticket_price : '₹ 0.00';
                    }else{
                        $TicketArr["ticket_amount"] = !empty($PaymentDetails) ? '₹ '.$PaymentDetails[0]->amount : '₹ 0.00';
                    }

                    if (count($AttendeeData) > 0) {
                        $AttenddeeDetails = $AttendeeData[0]->attendee_details;
                        $attendee_details = json_decode(json_decode($AttenddeeDetails));
                        // dd($EventId);
                        $amount_details = [];
                        $extra_details = [];

                        //--------------
                        $sql1 = "SELECT question_label,question_form_type,question_form_name FROM event_form_question WHERE event_id =:event_id AND is_custom_form = 0 AND show_on_ticket_pdf = 1";
                        $QuestionData = DB::select($sql1, ['event_id' => $EventId]);
                        // dd($QuestionData,$attendee_details);

                        // Iterate through attendee details to separate the amounts
                        if(!empty($QuestionData)){
                            foreach($QuestionData as $res){
                                foreach ($attendee_details as $detail) {
                                    $aTemp = new stdClass;
                                    $labels = [];
                                    if (($detail->question_form_name == $res->question_form_name) && !empty($detail->ActualValue)) {
                                        
                                        $question_form_option = json_decode($detail->question_form_option, true);
                                        if($detail->question_form_type == 'radio' || $detail->question_form_type == 'select'){
                                            if(!array_search($detail->id, array_column($extra_details, 'id'))){
                                                $label = '';
                                                foreach ($question_form_option as $option) {
                                                    if ($option['id'] === (int)$detail->ActualValue) {
                                                        $label = $option['label'];
                                                        break;
                                                    }
                                                }
                                                // dd($label);
                                                //$detail->ActualValue = $label;
                                                
                                                $aTemp->id             = $detail->id;
                                                $aTemp->question_label = $detail->question_label;
                                                $aTemp->question_form_type = $detail->question_form_type;
                                                $aTemp->ActualValue    = $label;
                                                $extra_details[] = $aTemp;
                                            }
                                        }else if($detail->question_form_type == 'checkbox'){
                                            if(!array_search($detail->id, array_column($extra_details, 'id'))){
                                                foreach ($question_form_option as $option) {
                                                    if (in_array($option['id'], explode(',', $detail->ActualValue))) {
                                                        $labels[] = $option['label'];
                                                    }
                                                }
                                                
                                                $aTemp->id             = $detail->id;
                                                $aTemp->question_label = $detail->question_label;
                                                $aTemp->question_form_type = $detail->question_form_type;
                                                $aTemp->ActualValue   = implode(', ', $labels);
                                                $extra_details[] = $aTemp;
                                            }
                                        }else{
                                            if(!array_search($detail->id, array_column($extra_details, 'id'))){
                                                
                                                $aTemp->id             = $detail->id;
                                                $aTemp->question_label = $detail->question_label;
                                                $aTemp->question_form_type = $detail->question_form_type;
                                                $aTemp->ActualValue    = $detail->ActualValue;
                                                $extra_details[] = $aTemp;
                                            }
                                        }
                                        
                                    }
                                }
                            }
                        }

                        // foreach ($attendee_details as $detail) {
                        //     if ($detail->question_form_type == 'amount') {
                        //         $amount_details[] = $detail;
                        //     }
                        //     if ($detail->question_form_name == 'drink_preferences') {
                        //         $extra_details[] = $detail;
                        //     }
                        //     if ($detail->question_form_name == 'breakfast_preferences') {
                        //         $extra_details[] = $detail;
                        //     }
                        // }

                        // if(!empty($extra_details)){
                        //     foreach
                        // }

                    }
                }
             // dd($extra_details);

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

                // dd($Organizer[0]);

                if (count($Organizer) > 0) {
                    foreach ($Organizer as $key => $value) {
                        $value->logo_image = !empty($value->logo_image) ? url('/') . 'organiser/logo_image/' . $value->logo_image : "";
                    }
                }
                // dd($Organizer);
                // Generate QR code
				$qrCode = '';
				if (!empty($TicketArr['unique_ticket_id'])) 
				{
					$qrCode = base64_encode(QrCode::format('png')->size(200)->generate($TicketArr['unique_ticket_id']));
                }
				// $qrCode = "";
                // dd($TicketArr);
                $data = [
                    'ticket_details' => $TicketArr,
                    'event_details' => (sizeof($Event) > 0) ? $Event[0] : [],
                    'org_details' => (sizeof($Organizer) > 0) ? $Organizer[0] : [],
                    'user_details' => (sizeof($User) > 0) ? $User[0] : [],
                    'EventLink' => $EventLink,
                    'QrCode' => $qrCode,
                    'amount_details' => $amount_details,
                    'extra_details' => $extra_details
                ];
                // dd($data);

                $pdf = PDF::loadView('pdf_template', $data);

                $PdfName = $EventId . $TicketId . $BookingDetailId . time() . '.pdf';

                $pdf->save(public_path('ticket_pdf/' . $PdfName));

                $PdfPath = url('/') . "/ticket_pdf/" . $PdfName;
                $ResponseData['pdf_link'] = $PdfPath;

                $ResposneCode = 200;
                $message = 'Request processed successfully';
            } else {
                $ResposneCode = 400;
                $message = $field . ' is empty';
            }
        } else {
            $ResposneCode = $aToken['code'];
            $message = $aToken['message'];
        }

        $response = [
            'data' => $ResponseData,
            'message' => $message
        ];

        return response()->json($response, $ResposneCode);
    }
    
    // ------- get coupon details - particular ticket wise data fetch
    public function getCoupons(Request $request)
    {
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $empty = false;
        $field = '';
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        // dd($aToken);
        // $aToken['code'] = 200;
        if ($aToken['code'] == 200) {
            $aPost = $request->all();

            if (!$empty) {
                $master = new Master();
                $Auth = new Authenticate();
                $Auth->apiLog($request);

                $UserId = $aToken['data']->ID;
                $now = strtotime("now");
                $EventId = (isset($aPost['event_id'])) ? $aPost['event_id'] : 0;
                $TicketIds = (isset($aPost['ticket_ids'])) ? $aPost['ticket_ids'] : 0;
                // show_public
                $ShowPublicFlag = (isset($aPost['show_public'])) ? $aPost['show_public'] : 0;

                $CouponsArr = [];

                if (!empty($EventId) && !empty($TicketIds)) {

					// (SELECT COUNT(id) FROM applied_coupons WHERE coupon_id = ecd.event_coupon_id AND event_id = ecd.event_id) AS coupon_count   // previous code, modified on 17-01-2025
                    $SQL = "SELECT ecd.*, ec.*, ec.id AS coupon_id,
					(SELECT COUNT(ac.coupon_id) FROM applied_coupons ac LEFT JOIN event_booking eb on ac.booking_id=eb.id WHERE ac.coupon_id = ecd.event_coupon_id AND eb.transaction_status IN (1,3) AND eb.event_id = ecd.event_id) AS coupon_count
                    FROM event_coupon_details AS ecd
                    JOIN event_coupon AS ec ON ecd.event_coupon_id = ec.id
                    WHERE ecd.event_id = :event_id
                    AND ec.coupon_status = 1";

                    if (empty($ShowPublicFlag)) {
                        $SQL .= " AND ec.show_public = 1";
                    }

                    $SQL .= " AND ecd.end_coupon = 0
                    AND FIND_IN_SET(:ticket_id, ecd.ticket_details) > 0
                    AND ecd.discount_from_datetime <= :now1
                    AND ecd.discount_to_datetime >= :now2
                    HAVING ecd.no_of_discount > coupon_count";

                    // dd($SQL,[
                    //     "event_id" => $EventId,
                    //     "ticket_id" =>14,
                    //     "now1" => $now,
                    //     "now2" => $now
                    // ]);

                    foreach ($TicketIds as $ticketId) {
                        $result = DB::select($SQL, [
                            "event_id" => $EventId,
                            "ticket_id" => $ticketId,
                            "now1" => $now,
                            "now2" => $now
                        ]);

                        if (!empty($result)) {
                            foreach ($result as $value) {
                                // Check if the ID is not already in the CouponsArr array
                                if (!in_array($value->id, array_column($CouponsArr, 'id'))) {
                                    // Add the current value to CouponsArr
                                    $CouponsArr[] = $value;
                                }
                            }
                        }
                    }
                    // die;
                    foreach ($CouponsArr as $coupon) {
                        $coupon->discount_from_datetime = (!empty($coupon->discount_from_datetime)) ? date("d F Y", $coupon->discount_from_datetime) : 0;
                        $coupon->discount_to_datetime = (!empty($coupon->discount_to_datetime)) ? date("d F Y", $coupon->discount_to_datetime) : 0;
                        $coupon->pending_coupon_count = ($coupon->no_of_discount - $coupon->coupon_count);
                    }
                }

                $ResponseData['Coupons'] = $CouponsArr;

                $ResposneCode = 200;
                $message = 'Request processed successfully';
            } else {
                $ResposneCode = 400;
                $message = $field . ' is empty';
            }
        } else {
            $ResposneCode = $aToken['code'];
            $message = $aToken['message'];
        }

        $response = [
            'data' => $ResponseData,
            'message' => $message
        ];

        return response()->json($response, $ResposneCode);
    }

    public function GetAgeCriteria(Request $request)
    {
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $empty = false;
        $field = '';
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        // dd($aToken['data']->ID);
        if ($aToken['code'] == 200) {
            $aPost = $request->all();

            if (empty($aPost['event_id'])) {
                $empty = true;
                $field = 'Event Id';
            }
            if (empty($aPost['ticket_id'])) {
                $empty = true;
                $field = 'Ticket Id';
            }
            if (empty($aPost['gender'])) {
                $empty = true;
                $field = 'Gender';
            }
            if (empty($aPost['age'])) {
                $empty = true;
                $field = 'Age';
            }
            if (!$empty) {

                $Auth = new Authenticate();
                $Auth->apiLog($request);

                $EventId = $aPost['event_id'];
                $TicketId = $aPost['ticket_id'];
                $Gender = $aPost['gender'];
                $Age = $aPost['age'];

                $Bindings = array(
                    "event_id" => $EventId,
                    "ticket_id" => $TicketId,
                    "gender" => $Gender,
                    "age" => $Age
                );
                $SQL1 = "SELECT id,age_category FROM `age_criteria` WHERE event_id=:event_id AND distance_category=:ticket_id AND gender=:gender AND :age BETWEEN age_start AND age_end";

                $AgeCriteriaData = DB::select($SQL1, $Bindings);

                $ResponseData['AgeCriteriaData'] = $AgeCriteriaData;

                $ResposneCode = 200;
                $message = 'Request processed successfully';
            } else {
                $ResposneCode = 400;
                $message = $field . ' is empty';
            }
        } else {
            $ResposneCode = $aToken['code'];
            $message = $aToken['message'];
        }

        $response = [
            'data' => $ResponseData,
            'message' => $message
        ];

        return response()->json($response, $ResposneCode);
    }
    
    //-------------- participant_send_multiple_email
    public function ParticipantSendMultipleEmail(Request $request)
    {   
        // dd($request);
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $empty = false;
        $field = '';
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        //dd($aToken);
        if ($aToken['code'] == 200) {
            $aPost = $request->all();
         
            $EventId   = !empty($request->event_id) ? $request->event_id : 0;
            $UserId    = $aToken['data']->ID;
            $EventUrl  = !empty($request->event_url) ? $request->event_url : '';
            $EmailType = !empty($request->email_type) ? $request->email_type : 1;
            $SubjectName = !empty($request->subject_name) ? $request->subject_name : '';
            $MessageContent   = !empty($request->message_content) ? $request->message_content : '';
            $ParticipantData = !empty($request->participant_data) ? json_decode($request->participant_data) : '';
            $participant_ids = !empty($ParticipantData) ? implode(",", $ParticipantData) : '';
          
            if(!empty($participant_ids)){

                $SQL = "SELECT id,booking_details_id,email,CONCAT(firstname, ' ', lastname) AS username,firstname,lastname,registration_id,(select ticket_amount from booking_details where id = attendee_booking_details.booking_details_id) as ticket_amount,(select ticket_name from event_tickets where id = attendee_booking_details.ticket_id) as ticket_name,bulk_upload_flag,ticket_price,final_ticket_price FROM attendee_booking_details WHERE id IN(".$participant_ids.") order by id desc";
                $attendeeResult = DB::select($SQL, array());
                // dd($attendeeResult);

                foreach($attendeeResult as $res){
                    
                    $attendee_email = !empty($res->email) ? $res->email : '';

                    if($EmailType != 5 || $EmailType != "5"){

                        $SQL1 = "SELECT (select booking_pay_id from event_booking where id = booking_details.booking_id) as booking_pay_id FROM booking_details WHERE id =:id";
                        $bookingDetResult = DB::select($SQL1, array('id' => $res->booking_details_id));
                         // dd($bookingDetResult);
                        $BookingPayId =  !empty($bookingDetResult) && $bookingDetResult[0]->booking_pay_id ? $bookingDetResult[0]->booking_pay_id : 0;
                        $attendee_id = !empty($res->id) ? $res->id : '';
                        $attendee_username = !empty($res->username) ? str_replace("\u{A0}", "", $res->username)  : '';
                        $ticket_amount = !empty($res->ticket_amount) ? '₹ '.$res->ticket_amount : '';
                        $attendee_firstname = !empty($res->firstname) ? str_replace("\u{A0}", "", $res->firstname) : '';
                        $attendee_lastname = !empty($res->lastname) ? str_replace("\u{A0}", "", $res->lastname) : '';
                        $attendee_registration_id = !empty($res->registration_id) ? $res->registration_id : '';
                        $attendee_ticket_name = !empty($res->ticket_name) ? $res->ticket_name : '';
                        
                        if($res->bulk_upload_flag == 0)
                            $ticket_paid_amount = !empty($res->ticket_amount) ? $res->ticket_amount : '0.00';
                        else
                            $ticket_paid_amount = !empty($attendeeResult) ? $attendeeResult[0]->ticket_price : '0.00';

                        $final_ticket_price = !empty($res->final_ticket_price) ? $res->final_ticket_price : 0;
                       
                        // dd($BookingPayId,$attendee_id ,$attendee_email,$attendee_username,$ticket_amount,$attendee_firstname,$attendee_lastname,$attendee_registration_id,$attendee_ticket_name,$ticket_paid_amount,$final_ticket_price);

                        $attendee_array = array("attendee_id" => $attendee_id, "firstname" => $attendee_firstname, "lastname" => $attendee_lastname, "username" => $attendee_username, "registration_id" => $attendee_registration_id, "ticket_name" => $attendee_ticket_name);

                        $this->ResendEmailDetails($UserId, $attendee_email, $EventId, $EventUrl, 1, $ticket_amount, $BookingPayId, $falg = 2, $attendee_array, $EmailType, $ticket_paid_amount, $final_ticket_price);
                 
                    }else if($EmailType == 5 || $EmailType == "5"){
                        // dd($UserId, $attendee_email, $MessageContent, $SubjectName, $falg = 2, 0, $EventId);    
                        $Email = new Emails();
                        $Email->send_participant_custom_mail($UserId, $attendee_email, $MessageContent, $SubjectName, $falg = 2, $EventId);
                    }

                    $ResponseData['data'] = 1;
                    $message = "Email send successfully";
                    $ResposneCode = 200;
                }

            } else {
                $ResponseData['data'] = 0;
                $message = "Email Not Found";
                $ResposneCode = 401;
            }

        } else {
            $ResposneCode = $aToken['code'];
            $message = $aToken['message'];
        }

        $response = [
            'success' => $ResposneCode,
            'data' => $ResponseData,
            'message' => $message
        ];

        return response()->json($response, $ResposneCode);
    }

    public function ResendEmailToAttendee(Request $request)
    {   
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $empty = false;
        $field = '';
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        //dd($aToken);
        if ($aToken['code'] == 200) {
            $aPost = $request->all();
            $EventId = !empty($request->event_id) ? $request->event_id : 0;
            $attendee_id = !empty($request->attendee_id) ? $request->attendee_id : 0;
            $EventUrl = !empty($request->event_url) ? $request->event_url : '';
            $EmailType = !empty($request->email_type) ? $request->email_type : 1;

            $UserId = $aToken['data']->ID;

            if (!empty($attendee_id)) {

                $SQL = "SELECT id,booking_details_id,email,CONCAT(firstname, ' ', lastname) AS username,firstname,lastname,registration_id,(select ticket_amount from booking_details where id = attendee_booking_details.booking_details_id) as ticket_amount,(select ticket_name from event_tickets where id = attendee_booking_details.ticket_id) as ticket_name,bulk_upload_flag,ticket_price,final_ticket_price FROM attendee_booking_details WHERE id =:id";
                $attendeeResult = DB::select($SQL, array('id' => $attendee_id));
                //dd($attendeeResult);

                $SQL1 = "SELECT (select booking_pay_id from event_booking where id = booking_details.booking_id) as booking_pay_id FROM booking_details WHERE id =:id";
                $bookingDetResult = DB::select($SQL1, array('id' => $attendeeResult[0]->booking_details_id));
                // dd($bookingDetResult);
                $BookingPayId =  !empty($bookingDetResult) && $bookingDetResult[0]->booking_pay_id ? $bookingDetResult[0]->booking_pay_id : 0;
                
                $attendee_id = !empty($attendeeResult) && $attendeeResult[0]->id ? $attendeeResult[0]->id : '';
                $attendee_email = !empty($attendeeResult) && $attendeeResult[0]->email ? $attendeeResult[0]->email : '';
                $attendee_username = !empty($attendeeResult) && $attendeeResult[0]->username ? $attendeeResult[0]->username : '';
                $ticket_amount = !empty($attendeeResult) && $attendeeResult[0]->ticket_amount ? '₹ ' . $attendeeResult[0]->ticket_amount : '';
                $attendee_firstname = !empty($attendeeResult) && $attendeeResult[0]->firstname ? $attendeeResult[0]->firstname : '';
                $attendee_lastname = !empty($attendeeResult) && $attendeeResult[0]->lastname ? $attendeeResult[0]->lastname : '';
                $attendee_registration_id = !empty($attendeeResult) && $attendeeResult[0]->registration_id ? $attendeeResult[0]->registration_id : '';
                $attendee_ticket_name = !empty($attendeeResult) && $attendeeResult[0]->ticket_name ? $attendeeResult[0]->ticket_name : '';
                
                if($attendeeResult[0]->bulk_upload_flag == 0)
                    $ticket_paid_amount = !empty($attendeeResult) && $attendeeResult[0]->ticket_amount ? $attendeeResult[0]->ticket_amount : '0.00';
                else
                    $ticket_paid_amount = !empty($attendeeResult) && $attendeeResult[0]->ticket_price ? $attendeeResult[0]->ticket_price : '0.00';

                $final_ticket_price = !empty($attendeeResult) && $attendeeResult[0]->final_ticket_price ? $attendeeResult[0]->final_ticket_price : 0;
                // dd($ticket_paid_amount);

                $attendee_array = array("attendee_id" => $attendee_id, "firstname" => $attendee_firstname, "lastname" => $attendee_lastname, "username" => $attendee_username, "registration_id" => $attendee_registration_id, "ticket_name" => $attendee_ticket_name);
                
                if (!empty($attendee_email)) {
                    // $this->sendBookingMail($UserId, $attendee_email, $EventId, $EventUrl, 1); 
                    $this->ResendEmailDetails($UserId, $attendee_email, $EventId, $EventUrl, 1, $ticket_amount, $BookingPayId, $falg = 2, $attendee_array, $EmailType, $ticket_paid_amount, $final_ticket_price);
                    $ResponseData['data'] = 1;
                    $message = "Email send successfully";
                    $ResposneCode = 200;
                } else {
                    $ResponseData['data'] = 0;
                    $message = "Email Not Found";
                    $ResposneCode = 401;
                }

            }

        } else {
            $ResposneCode = $aToken['code'];
            $message = $aToken['message'];
        }

        $response = [
            'success' => $ResposneCode,
            'data' => $ResponseData,
            'message' => $message
        ];

        return response()->json($response, $ResposneCode);
    }


    function ResendEmailDetails($UserId, $UserEmail, $EventId, $EventUrl, $TotalNoOfTickets, $TotalPrice, $BookingPayId, $flag, $attendee_array, $CommEmailType, $ticket_paid_amount, $final_ticket_price)
    {
        
        $numberFormate = new Numberformate();
        // dd($attendee_array,$ticket_paid_amount, $final_ticket_price);
        $master = new Master();
        $sql1 = "SELECT * FROM users WHERE id=:user_id";
        $User = DB::select($sql1, ['user_id' => $UserId]);

        $sql2 = "SELECT * FROM events WHERE id=:event_id";
        $Event = DB::select($sql2, ['event_id' => $EventId]);
        $Venue = "";
        if (count($Event) > 0) {
            $Venue .= ($Event[0]->address !== "") ? $Event[0]->address . ", " : "";
            $Venue .= ($Event[0]->city !== "") ? $master->getCityName($Event[0]->city) . ", " : "";
            $Venue .= ($Event[0]->state !== "") ? $master->getStateName($Event[0]->state) . ", " : "";
            $Venue .= ($Event[0]->country !== "") ? $master->getCountryName($Event[0]->country) . ", " : "";
            $Venue .= ($Event[0]->pincode !== "") ? $Event[0]->pincode . ", " : "";
            // $Venue = $Event[0]->address.", ".$Event[0]->city.", ".$Event[0]->state.", ".$Event[0]->country.", ".$Event[0]->pincode;
        }

        if(!empty($Event) && isset($Event[0]->event_type) && $Event[0]->event_type == 2){
            $Venue = 'Virtual Event';
        }

        $sql3 = "SELECT * FROM organizer WHERE user_id=:user_id";
        $Organizer = DB::select($sql3, ['user_id' => $UserId]);
        $OrgName = "";
        if (count($Organizer) > 0) {
            $OrgName = !empty($Organizer[0]->name) ? ucfirst($Organizer[0]->name) : '';
        }

        //------ ticket registration id and race category
        $sql2 = "select bd.id,bd.ticket_id,eb.cart_details,eb.total_amount from event_booking as eb left join booking_details as bd on bd.booking_id = eb.id left join attendee_booking_details as abd on abd.booking_details_id = bd.id WHERE bd.event_id = :event_id AND eb.booking_pay_id =:booking_pay_id order by bd.booking_id asc limit 1"; // GROUP BY bd.booking_id
        $booking_detail_Result = DB::select($sql2, array('event_id' => $EventId, 'booking_pay_id' => $BookingPayId));
        // dd($booking_detail_Result);
        $booking_detail_id = !empty($booking_detail_Result) ? $booking_detail_Result[0]->id : 0;
        $ticket_id         = !empty($booking_detail_Result) ? $booking_detail_Result[0]->ticket_id : 0;
        $total_ticket_amount  = !empty($booking_detail_Result) ? $booking_detail_Result[0]->total_amount : '0.00';

        $cart_details = !empty($booking_detail_Result[0]->cart_details) ? json_decode($booking_detail_Result[0]->cart_details) : [];

        if(!empty($final_ticket_price)){
            $ticket_total_amount = !empty($final_ticket_price) ? $final_ticket_price : '0.00';
        }else{
            $ticket_total_amount = !empty($cart_details) && isset($cart_details[0]->BuyerPayment) ? $cart_details[0]->BuyerPayment : '0.00';
        }
        
        // dd($ticket_total_amount);

        $SQL1 = "SELECT ticket_id,email,firstname,lastname,registration_id,(select ticket_name from event_tickets where id = attendee_booking_details.ticket_id) as ticket_name,attendee_details FROM attendee_booking_details WHERE booking_details_id =:booking_details_id";
        $tAttendeeResult = DB::select($SQL1, array('booking_details_id' => $booking_detail_id));
        //dd($tAttendeeResult, $BookingPayId , $booking_detail_id, $flag , $EventId);

        $attendee_details = !empty($tAttendeeResult[0]->attendee_details) ? json_decode(json_decode($tAttendeeResult[0]->attendee_details)) : '';
        // dd($attendee_details);
        $emailPlaceholders_array = $FinalEmailArray = []; 
        // ---------------- new added om 07-10-24
        $TeamName = $Participant_2_name = $Participant_3_name = $Participant_4_name = $preferred_date = $run_category = '';
        if(!empty($attendee_details)){
            foreach($attendee_details as $res){
                if($res->question_form_name == "enter_team_name"){
                    $TeamName = $res->ActualValue;
                }
                if($res->question_form_name == "participant_2_name"){
                    $Participant_2_name = $res->ActualValue;
                }
                if($res->question_form_name == "participant_3_name"){
                    $Participant_3_name = $res->ActualValue;
                }
                if($res->question_form_name == "participant_4_name"){
                    $Participant_4_name = $res->ActualValue;
                }

                if($res->question_form_name == "preferred_date_for_the_carnival"){
                    $preferred_date_json = json_decode($res->question_form_option);
                    foreach ($preferred_date_json as $item) {
                        if ($item->id == $res->ActualValue){
                            $preferred_date = $item->label;
                            break;
                        }
                    }
                }

                if($res->question_form_name == "select_your_run_category"){
                    $run_category_json = json_decode($res->question_form_option);
                    foreach ($run_category_json as $item) {
                        if ($item->id == $res->ActualValue){
                            $run_category = $item->label;
                            break;
                        }
                    }
                }

                //------------------ new added on 15-11-24  (Email Placeholder Replace)
                $sql2 = "SELECT question_form_name,placeholder_name,(select question_form_type from event_form_question where id = email_placeholders.question_id) as question_form_type,(select question_form_option from event_form_question where id = email_placeholders.question_id) as question_form_option FROM email_placeholders WHERE status = 1 AND question_form_name =:question_form_name AND event_id = ".$EventId." ";

                // if(empty($res->parent_question_id)){
                //     $sql2 .= " AND question_form_name = '".$res->question_form_name."' ";
                // }else{
                //     $sql2 .= " AND question_form_name = LOWER(REPLACE('".$res->question_label."', ' ', '_')) ";
                // }

                if(empty($res->parent_question_id)){
                    $emailPlaceHolderResult = DB::select($sql2, array('question_form_name' => $res->question_form_name));
                }else{
                    $emailPlaceHolderResult = DB::select($sql2, array('question_form_name' => strtolower(str_replace(" ", "_", $res->question_label))));
                }

                if(!empty($emailPlaceHolderResult) && $emailPlaceHolderResult[0]->question_form_type != "file"){
                    $question_form_option = !empty($emailPlaceHolderResult[0]->question_form_option) ? json_decode($emailPlaceHolderResult[0]->question_form_option, true) : [];
                    $label = ''; $labels = []; $acutal_value = '';

                    if($emailPlaceHolderResult[0]->question_form_type == "countries"){
                        $acutal_value = !empty($res->ActualValue) ? $master->getCountryName($res->ActualValue) : "";
                    }else if ($emailPlaceHolderResult[0]->question_form_type == "states") {
                        $acutal_value = !empty($res->ActualValue) ? $master->getStateName($res->ActualValue) : "";
                    }else if ($emailPlaceHolderResult[0]->question_form_type == "cities") {
                        $acutal_value = !empty($res->ActualValue) ? $master->getCityName($res->ActualValue) : "";
                    }else if($emailPlaceHolderResult[0]->question_form_type == "date"){
                        $acutal_value = !empty($res->ActualValue) ? date('d-m-Y',strtotime($res->ActualValue)) : '';
                    }else if($emailPlaceHolderResult[0]->question_form_type == "radio" || $emailPlaceHolderResult[0]->question_form_type == "select"){
                      
                        if(!empty($res->ActualValue) && !empty($question_form_option)){
                            foreach ($question_form_option as $option) {
                                if ($option['id'] === (int) $res->ActualValue) {
                                    $label = $option['label'];
                                    break;
                                }
                            }
                            $acutal_value = !empty($label) ? $label : '';
                        }
                    }else if($emailPlaceHolderResult[0]->question_form_type == "checkbox"){
                        if(isset($res->ActualValue) && !empty($res->ActualValue)){
                            foreach ($question_form_option as $option) {
                                if (in_array($option['id'], explode(',', $res->ActualValue))) {
                                    $labels[] = $option['label'];
                                }
                            }
                            $label = implode(', ', $labels);
                        }
                        $acutal_value = !empty($label) ? $label : '';
                    }else{                                              // [text/email/textarea/amount/time]
                        $acutal_value = !empty($res->ActualValue) ? $res->ActualValue : '';
                    }

                    $emailPlaceholders_array[] = [$emailPlaceHolderResult[0]->placeholder_name => trim(ucfirst($acutal_value))];
                }

            }
        }

        //dd($emailPlaceholders_array);
        if(!empty($emailPlaceholders_array)){
            foreach ($emailPlaceholders_array as $item) {  
                $key = key($item);
                $value = reset($item);
                $FinalEmailArray[$key] = $value;
            }
        } 
        // dd($FinalEmailArray);
       
       // dd($preferred_date,$run_category);
        $registration_ids = $ticket_names = '';
        if (!empty($tAttendeeResult)) {
            $registration_ids_array = array_column($tAttendeeResult, "registration_id");
            $registration_ids = implode(", ", $registration_ids_array);

            $ticket_ids_array = array_column($tAttendeeResult, "ticket_name");
            $ticket_names = implode(", ", array_unique($ticket_ids_array));
        }
        //dd($registration_ids,$ticket_names);
        if ($flag == 2 && !empty($attendee_array)) {
            $user_name = $attendee_array['username'];
            $first_name = $attendee_array['firstname'];
            $last_name = $attendee_array['lastname'];
            $registration_id = $attendee_array['registration_id'];
            $ticket_names = $attendee_array['ticket_name'];
        }

        //------------ new added
        $sql5 = "SELECT ticket_names,registration_ids FROM booking_payment_details WHERE id=:id";
        $BookingPaymentDetails = DB::select($sql5, ['id' => $BookingPayId]);
       
        $loc_registration_id = !empty($BookingPaymentDetails) ? $BookingPaymentDetails[0]->registration_ids : '';
        $loc_ticket_names    = !empty($BookingPaymentDetails) ? $BookingPaymentDetails[0]->ticket_names : '';

        $ConfirmationEmail = array(
    
            "USERNAME" => ucfirst($user_name),
            "FIRSTNAME" => ucfirst($first_name),
            "LASTNAME" => ucfirst($last_name),
            "EVENTID" => $EventId,
            "EVENTNAME" => ucfirst($Event[0]->name),
            "EVENTSTARTDATE" => (!empty($Event[0]->start_time)) ? date('d-m-Y', ($Event[0]->start_time)) : "",
            "EVENTSTARTTIME" => (!empty($Event[0]->start_time)) ? date('H:i A', ($Event[0]->start_time)) : "",
            "EVENTENDDATE" => (!empty($Event[0]->end_time)) ? date('d-m-Y', ($Event[0]->end_time)) : "",
            "EVENTENDTIME" => (!empty($Event[0]->end_time)) ? date('H:i A', ($Event[0]->end_time)) : "",
            "EVENTDATE" => (!empty($Event[0]->start_time)) ? date('d-m-Y', ($Event[0]->start_time)) : "",
            "EVENTTIME" => (!empty($Event[0]->start_time)) ? date('H:i A', ($Event[0]->start_time)) : "",
            "YTCRTEAM" => "YouTooCanRun Team",
            "EVENTURL" => $EventUrl,
            "COMPANYNAME" => $OrgName,
            "TOTALTICKETS" => $TotalNoOfTickets,
            "VENUE" => $Venue,
            "TOTALAMOUNT" => !empty($total_ticket_amount) ? '₹ '.$numberFormate->formatInIndianCurrency($total_ticket_amount) : '0.00',
            "TICKETAMOUNT" => !empty($ticket_paid_amount) ? '₹ '.$numberFormate->formatInIndianCurrency($ticket_paid_amount) : '0.00', 
            "REGISTRATIONID" => !empty($registration_id) ? $registration_id : $loc_registration_id, //!empty($registration_ids) ? $registration_ids : '', 
            "RACECATEGORY" => !empty($ticket_names) ? ucfirst($ticket_names) : ucfirst($loc_ticket_names), // !empty($ticket_names) ? $ticket_names : ''
            "TEAMNAME"       => isset($TeamName) && !empty($TeamName) ? ucfirst($TeamName) : '',
            "2NDPARTICIPANT" => isset($Participant_2_name) && !empty($Participant_2_name) ? ucfirst($Participant_2_name) : '',
            "3RDPARTICIPANT" => isset($Participant_3_name) && !empty($Participant_3_name) ? ucfirst($Participant_3_name) : '',
            "4THPARTICIPANT" => isset($Participant_4_name) && !empty($Participant_4_name) ? ucfirst($Participant_4_name) : '',
            "PREFERREDDATE"  => isset($preferred_date) && !empty($preferred_date) ? $preferred_date : '',
            "RUNCATEGORY"    => isset($run_category) && !empty($run_category) ? ucfirst($run_category) : ''
        );

        if(!empty($FinalEmailArray))
            $ConfirmationEmail = array_merge($ConfirmationEmail,$FinalEmailArray);

        // dd($ConfirmationEmail);

        $Subject = "";
        
         //--------------- new added as per client requirement
        if($ticket_id == 108 || $ticket_id == 109){
           
            $MessageContent = "<p>Dear {USERNAME},
                <br><br>Congratulations! Your registration for the <strong>Pokémon Carnival 2025</strong> is confirmed. We’re excited to welcome you to a world of Pokémon-themed activities, games, and unforgettable experiences.
                <br><br>Your details are:<br><br>
                <strong>Name : {FIRSTNAME} {LASTNAME}</strong><br>
                <strong>Category : {RACECATEGORY}</strong><br>";
                
            if($ticket_id == 109){
                $MessageContent .= "<strong>Accompanying Parent Name : {ACCOMPANY_PARENT_NAME}</strong><br>
                <strong>Accompanying Sibling 1 Name (if selected) : {SIBLING_NAME_1}</strong><br>
                <strong>Accompanying Sibling 2 Name (if selected) : {SIBLING_NAME_2}</strong><br>";
            }else{
                $MessageContent .= "<strong>Accompanying Parent Name : </strong><br>
                <strong>Accompanying Sibling 1 Name (if selected) : </strong><br>
                <strong>Accompanying Sibling 2 Name (if selected) : </strong><br>"; 
            }

            $MessageContent .= "<strong>Timing : 3:00 pm to 10:00 pm</strong><br>
                <strong>Registration ID : {REGISTRATIONID}</strong><br>
                <strong>Location : Jio World Garden, Bandra Kurla Complex, Mumbai</strong><br>
                <strong>Preferred Date for attending Pokémon Carnival : {PREFERREDDATE}</strong><br>
                <strong>Cost : {TOTALAMOUNT}</strong><br><br>
                If you have any questions, feel free to reach out to us at support@youtoocanrun.com. We can’t wait to see you at the starting line!</p><br>
                <p>Best regards,<br/>
                <strong>Team Pokémon Carnival and Run</strong></p>";

            $Subject = "Your Pokémon Carnival 2025 Entry is Confirmed!";
        }else{

            $sql = "SELECT * FROM `event_communication` WHERE `event_id`=:event_id AND status = 1";
            if(!empty($CommEmailType)){
                $sql .= ' AND comm_id = '.$CommEmailType.' ';
            }
            $Communications = DB::select($sql, ["event_id" => $EventId]); // "subject_name" => strtoupper("Registration Confirmation")
            // dd($Communications);
            $MessageContent = '';
            if (count($Communications) > 0) {
                $MessageContent = $Communications[0]->message_content;
                $Subject = $Communications[0]->subject_name;
            } else {
                $MessageContent = "Dear " . ucfirst($first_name) . " " . ucfirst($last_name) . ",
                     <br/><br/>
                    Thank you for registering for " . ucfirst($Event[0]->name) . "! We are thrilled to have you join us.
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
                    " . ucfirst($Event[0]->name) . " Team";

                if($CommEmailType == 1){
                    $Subject = "Event Registration Confirmation - " . ucfirst($Event[0]->name) . "";
                }else if($CommEmailType == 2){
                    $Subject = "BIB Collection Details - " . ucfirst($Event[0]->name) . "";
                }else if($CommEmailType == 3){
                    $Subject = "Event Day Details - " . ucfirst($Event[0]->name) . "";
                }else if($CommEmailType == 4){
                    $Subject = "Thank You Mailer - " . ucfirst($Event[0]->name) . "";
                }
            }
        }

        foreach ($ConfirmationEmail as $key => $value) {
            if (isset($key)) {
                $placeholder = '{' . $key . '}';
                $MessageContent = str_replace($placeholder, $value, $MessageContent);
            }
        }
       // dd($MessageContent);
        // echo $MessageContent; die;
        // attach image
        if(!empty($Communications) && !empty($Communications[0]->content_image)){

            $image_path = url('/').'/uploads/communication_email_images/'.$Communications[0]->content_image;
            $attach_image = '<img src="'.str_replace(" ", "%20", $image_path).'" alt="Image">';
            $MessageContent .= ' <br/><br/>';
            $MessageContent .= $attach_image;
        }
        // dd($MessageContent);
         //--------------- new added for generate pdf ----------------
        // dd($EventId,$UserId,$ticket_id,$attendee_array['attendee_id'],$EventUrl,$ticket_paid_amount);
        $generatePdf = EventTicketController::generateParticipantPDF($EventId,$UserId,$ticket_id,$attendee_array['attendee_id'],$EventUrl,'₹ '.$total_ticket_amount,$booking_detail_id);
         // dd($generatePdf);

        $Email = new Emails();
        $Email->send_booking_mail($UserId, $UserEmail, $MessageContent, $Subject, $flag, 0, $generatePdf, $EventId);

        return;
    }

    public function SendEmailPaymentSuccess(Request $request)
    {
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $empty = false;
        $field = '';
        $message = '';
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        // dd($aToken['data']->ID);
        if ($aToken['code'] == 200) {
            $aPost = $request->all();

            $EventId = !empty($request->event_id) ? $request->event_id : 0;
            $EventUrl = !empty($request->event_url) ? $request->event_url : '';
            $BookingPayId = !empty($request->booking_pay_id) ? $request->booking_pay_id : 0;

            $UserId = $aToken['data']->ID;

            if (!empty($BookingPayId)) {

                $SQL = "SELECT name as event_name,(select email from users where id = " . $UserId . ") as email FROM events WHERE id =:id";
                $aResult = DB::select($SQL, array('id' => $EventId, ));
                // dd($aResult);
                $user_email = !empty($aResult) && $aResult[0]->email ? $aResult[0]->email : '';
                $event_name = !empty($aResult) && $aResult[0]->event_name ? $aResult[0]->event_name : '';
                $event_url = $EventUrl . '/' . $event_name;

                $SQL1 = "SELECT total_attendees as no_of_tickets,TotalPrice as total_price,booking_pay_id FROM temp_booking_ticket_details WHERE booking_pay_id =:booking_pay_id";
                $TicketDetailsResult = DB::select($SQL1, array('booking_pay_id' => $BookingPayId));

                // dd($TicketDetailsResult,$user_email,$event_url);
                $no_of_tickets = !empty($TicketDetailsResult) && $TicketDetailsResult[0]->no_of_tickets ? $TicketDetailsResult[0]->no_of_tickets : 0;
                $total_price = !empty($TicketDetailsResult) && $TicketDetailsResult[0]->total_price ? '₹ ' . $TicketDetailsResult[0]->total_price : 0;
                $booking_pay_id = !empty($TicketDetailsResult) && $TicketDetailsResult[0]->booking_pay_id ? $TicketDetailsResult[0]->booking_pay_id : 0;

                $attendee_array = [];

               // echo $message1 = $EventId.'---'.$EventUrl.'---'.$BookingPayId.'---'.$user_email.'---'.$event_name.'---'.$event_url.'---'.$UserId.'---'.$no_of_tickets.'---'.$total_price; die;

                if (!empty($user_email)) {

                    $this->sendBookingMail($UserId, $user_email, $EventId, $event_url, $no_of_tickets, $total_price, $BookingPayId, $flag = 1, $attendee_array, $send_email_status = 1);
                    //$this->sendBookingMail($UserId, $user_email, $EventId, $EventUrl, 1); 
                    $up_sSQL = 'UPDATE booking_payment_details SET `send_email_flag` = 1 WHERE `id`=:booking_pay_id ';
                    DB::update(
                        $up_sSQL,
                        array(
                            'booking_pay_id' => $BookingPayId
                        )
                    );

                    $ResponseData['data'] = 1;
                    $message = "Email send successfully";
                    $ResposneCode = 200;
                } else {
                    $ResponseData['data'] = 0;
                    $message = "Email Not Found";
                    $ResposneCode = 401;
                }

            }

        } else {
            $ResposneCode = $aToken['code'];
            $message = $aToken['message'];
        }

        $response = [
            'success' => $ResposneCode,
            'data' => $ResponseData,
            'message' => $message
        ];

        return response()->json($response, $ResposneCode);
    }

    //-------- To send email organiser
    public function SendEmailOrganiserOnboardingConfirmation(Request $request)
    {
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $empty = false;
        $field = '';
        $message = '';
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        // dd($aToken['data']->ID);
        if ($aToken['code'] == 200) {
            $aPost = $request->all();

            $UserId = $aToken['data']->ID;

            $sql3 = "SELECT name,send_email_flag,(select email from users where id = organizer.user_id) as email FROM organizer WHERE user_id=:user_id";
            $Organizer = DB::select($sql3, ['user_id' => $UserId]);

            $Organizer_name = !empty($Organizer) && !empty($Organizer[0]->name) ? $Organizer[0]->name : '';
            $UserEmail = !empty($Organizer) && $Organizer[0]->email ? $Organizer[0]->email : '';
            $SendEmailFlag = !empty($Organizer) && $Organizer[0]->send_email_flag ? $Organizer[0]->send_email_flag : 0;

            $Subject = "Welcome to RACES - Organiser Onboarding Successful";

            $MessageContent = "Dear " . $Organizer_name . ",<br/><br/>
 
            Congratulations! Your onboarding as an organiser on RACES is now complete. We are excited to have you as part of our platform.<br/><br/>
             
            You can now start creating and managing your events through your organiser dashboard. Here are your login details:<br/><br/>
          
            For any assistance or to get started, please  contact our support team.<br/><br/>
             
            We look forward to your successful events and collaborations.<br/><br/>
             
            Best regards,<br/><br/>
             
            (For RACES)<br/>
            Team YouTooCanRun";

            if ($SendEmailFlag == 0) {
                // dd($MessageContent);
                $Email = new Emails();
                $Email->send_booking_mail($UserId, $UserEmail, $MessageContent, $Subject);

                //--------
                $up_sSQL = 'UPDATE organizer SET `send_email_flag` =:sendEmailFlag WHERE `user_id`=:user_id ';
                DB::update(
                    $up_sSQL,
                    array(
                        'sendEmailFlag' => 1,
                        'user_id' => $UserId
                    )
                );

                $ResponseData['data'] = 1;
                $message = "Email send successfully";
                $ResposneCode = 200;
            } else {
                $ResponseData['data'] = 0;
                $message = "Email Not Found";
                $ResposneCode = 401;
            }

        } else {
            $ResposneCode = $aToken['code'];
            $message = $aToken['message'];
        }

        $response = [
            'success' => $ResposneCode,
            'data' => $ResponseData,
            'message' => $message
        ];

        return response()->json($response, $ResposneCode);
    }

    //-------- To send email contact us
    public function SendEmailContactUs(Request $request)
    {
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $empty = false;
        $field = '';
        $message = '';
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        // dd($aToken['data']->ID);
        if ($aToken['code'] == 200) {
            $aPost = $request->all();
            $EventUrl = !empty($request->event_url) ? $request->event_url : '';

            $UserId = $aToken['data']->ID;

            if (empty($aPost['first_name'])) {
                $empty = true;
                $field = 'First Name';
            }
            if (empty($aPost['last_name'])) {
                $empty = true;
                $field = 'Last Name';
            }
            if (empty($aPost['email'])) {
                $empty = true;
                $field = 'Email';
            }

            if (!$empty) {

                $Email = new Emails();
                $Email->send_contactUs_mail($aPost['first_name'], $aPost['last_name'], $aPost['email'], $aPost['contact_no'], $aPost['message']);

                $ResponseData['data'] = 1;
                $message = "Email send successfully";
                $ResposneCode = 200;
            } else {
                $ResponseData['data'] = 0;
                $message = "Email Not Found";
                $ResposneCode = 401;
            }

        } else {
            $ResposneCode = $aToken['code'];
            $message = $aToken['message'];
        }

        $response = [
            'success' => $ResposneCode,
            'data' => $ResponseData,
            'message' => $message
        ];

        return response()->json($response, $ResposneCode);
    }

}


