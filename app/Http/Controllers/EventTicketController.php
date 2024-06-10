<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Libraries\Authenticate;
use Illuminate\Support\Facades\DB;
use App\Models\Master;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use App\Libraries\Emails;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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
                // -------------------------------------------------

                $sSQL = 'SELECT * FROM event_tickets WHERE event_id = :event_id AND active = 1 AND is_deleted = 0 AND ticket_sale_start_date <= :now_start AND ticket_sale_end_date >= :now_end';
                $ResponseData['event_tickets'] = DB::select($sSQL, array('event_id' => $aPost['event_id'], 'now_start' => $now, 'now_end' => $now));

                foreach ($ResponseData['event_tickets'] as $value) {
                    $value->count = 0;
                    $value->Error = "";

                    $value->display_ticket_name = !empty($value->ticket_name) ? (strlen($value->ticket_name) > 40 ? ucwords(substr($value->ticket_name, 0, 80)) . "..." : ucwords($value->ticket_name)) : "";

                    $sql = "SELECT COUNT(id) AS TotalBookedTickets FROM booking_details WHERE event_id=:event_id AND ticket_id=:ticket_id";
                    $TotalTickets = DB::select($sql, array("event_id" => $aPost['event_id'], "ticket_id" => $value->id));

                    $value->TotalBookedTickets = ((sizeof($TotalTickets) > 0) && (isset($TotalTickets[0]->TotalBookedTickets))) ? $TotalTickets[0]->TotalBookedTickets : 0;
                    $value->show_early_bird = 0;

                    $value->discount_ticket_price = 0;
                    $value->total_discount = 0;

                    if ($value->early_bird == 1 && $value->TotalBookedTickets <= $value->no_of_tickets && $value->start_time <= $now && $value->end_time >= $now) {
                        $value->show_early_bird = 1;
                        $value->strike_out_price = ($value->early_bird == 1) ? $value->ticket_price : 0;

                        if ($value->discount === 1) { //percentage
                            $value->total_discount = ($value->ticket_price * ($value->discount_value / 100));
                            $value->discount_ticket_price = $value->ticket_price - $value->total_discount;
                        } else if ($value->discount === 2) { //amount
                            $value->total_discount = $value->discount_value;
                            $value->discount_ticket_price = $value->ticket_price - $value->discount_value;
                        }
                    }
                }
                // -------------------------------------------------
                $Sql = "SELECT name,start_time,city FROM events WHERE id=:event_id";
                $EventData = DB::select($Sql, array('event_id' => $aPost['event_id']));
                foreach ($EventData as $key => $event) {
                    $event->display_name = !empty($event->name) ? $event->name : "";
                    $event->start_date = (!empty($event->start_time)) ? gmdate("d M Y", $event->start_time) : 0;
                    $event->city_name = !empty($event->city) ? $master->getCityName($event->city) : "";
                }
                $ResponseData['EventData'] = $EventData;

                // -------------------------------------------------
                $ResponseData['YTCR_FEE_PERCENT'] = config('custom.ytcr_fee_percent');
                $ResponseData['PLATFORM_FEE_PERCENT'] = config('custom.platform_fee_percent');
                $ResponseData['PAYMENT_GATEWAY_FEE_PERCENT'] = config('custom.payment_gateway_fee_percent');
                $ResponseData['PAYMENT_GATEWAY_GST_PERCENT'] = config('custom.payment_gateway_gst_percent');
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

                    if (!empty($TicketId)) {   // update data
                        // dd("here");
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
                            'id' => $TicketId
                        );

                        // dd($Binding);
                        $SQL = 'UPDATE event_tickets SET ticket_name=:ticket_name,ticket_status = :ticket_status,total_quantity = :total_quantity,ticket_price = :ticket_price,payment_to_you = :payment_to_you,ticket_sale_start_date = :ticket_sale_start_date,ticket_sale_end_date = :ticket_sale_end_date,advanced_settings=:advanced_settings,player_of_fee = :player_of_fee,player_of_gateway_fee = :player_of_gateway_fee,min_booking = :min_booking,max_booking = :max_booking,ticket_description = :ticket_description,msg_attendance = :msg_attendance,minimum_donation_amount= :minimum_donation_amount,early_bird=:early_bird,no_of_tickets=:no_of_tickets,start_time=:start_time,end_time=:end_time,discount=:discount,discount_value=:discount_value,category=:category,apply_age_limit=:apply_age_limit,age_start=:age_start,age_end=:age_end WHERE id=:id';
                        DB::update($SQL, $Binding);

                        $ResposneCode = 200;
                        $message = 'Event Ticket Updated Successfully';

                    } else {                // insert data
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
                            'age_end' => isset($aPost['age_end']) ? $aPost['age_end'] : 0
                        );
                        // dd($Binding);
                        $SQL2 = 'INSERT INTO event_tickets (event_id,ticket_name,ticket_status,total_quantity,ticket_price,payment_to_you,ticket_sale_start_date,ticket_sale_end_date,advanced_settings,player_of_fee,player_of_gateway_fee,min_booking,max_booking,ticket_description,msg_attendance,minimum_donation_amount,early_bird,no_of_tickets,start_time,end_time,discount,discount_value,category,apply_age_limit,age_start,age_end) VALUES(:event_id,:ticket_name,:ticket_status,:total_quantity,:ticket_price,:payment_to_you,:ticket_sale_start_date,:ticket_sale_end_date,:advanced_settings,:player_of_fee,:player_of_gateway_fee,:min_booking,:max_booking,:ticket_description,:msg_attendance,:minimum_donation_amount,:early_bird,:no_of_tickets,:start_time,:end_time,:discount,:discount_value,:category,:apply_age_limit,:age_start,:age_end)';

                        DB::select($SQL2, $Binding);

                        //---------- add form question to aplay new ticket id
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

                        //---------------------

                        $ResposneCode = 200;
                        $message = 'Event Ticket Inserted Successfully';

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
                $CollectGst = 0;
                $PriceTaxesStatus = 0;
                $sql3 = "SELECT collect_gst,prices_taxes_status FROM events WHERE id=:event_id";
                $CollectGstArr = DB::select($sql3, array("event_id" => $aPost['event_id']));
                if (count($CollectGstArr) > 0) {
                    $CollectGst = $CollectGstArr[0]->collect_gst;
                    $PriceTaxesStatus = $CollectGstArr[0]->prices_taxes_status;
                }
                $ResponseData['CollectGst'] = $CollectGst;
                $ResponseData['PriceTaxesStatus'] = $PriceTaxesStatus;

                // -------------------------------------------------

                // $sSQL = 'SELECT * FROM event_form_question WHERE event_id =:event_id AND question_status = 1 ORDER BY sort_order';
                // $FormQuestions = DB::select($sSQL, array('event_id' => $aPost['event_id']));
                // if (count($FormQuestions) > 0) {

                // dd($FormQuestions);

                if (!empty($AllTickets)) {
                    foreach ($AllTickets as $ticket) {
                        // dd($ticket);
                        $sSQL = 'SELECT * 
                                    FROM event_form_question 
                                    WHERE event_id = :event_id 
                                    AND FIND_IN_SET(:ticket_id, ticket_details)
                                    AND question_status = 1 
                                    ORDER BY sort_order';

                        $FormQuestions = DB::select($sSQL, [
                            'event_id' => $aPost['event_id'],
                            'ticket_id' => $ticket['id']
                        ]);

                        if (count($FormQuestions) > 0) {
                        } else {
                            $sSQL = 'SELECT * FROM event_form_question WHERE event_id =:event_id AND question_status = 1 ORDER BY sort_order';
                            $FormQuestions = DB::select($sSQL, array('event_id' => $aPost['event_id']));
                        }

                        foreach ($FormQuestions as $value) {
                            $hasCountriesQuestion = $hasStatesQuestion = false;
                            // echo "<pre>";print_r($value);
                            $value->ActualValue = "";
                            $value->Error = "";
                            $value->TicketId = 0;

                            if (!empty($value->question_form_option)) {
                                $jsonString = $value->question_form_option;
                                $array = json_decode($jsonString, true);

                                foreach ($array as &$item) { // Note the "&" before $item to modify it directly
                                    if (isset($item['count']) && !empty($item["count"])) {
                                        $sql = "SELECT current_count FROM extra_pricing_booking WHERE question_id=:question_id AND option_id=:option_id";
                                        $SoldItems = DB::select($sql, array("question_id" => $value->id, "option_id" => $item["id"]));
                                        if (count($SoldItems) > 0) {
                                            $currentCount = $SoldItems[0]->current_count;
                                            // Adding current_count to the $item array
                                            $item['current_count'] = $currentCount;
                                        }
                                    }
                                }
                                // Unset $item to avoid potential conflicts with other loops
                                unset($item);

                                $updatedJsonString = json_encode($array);

                                // Assign the updated JSON string back to $value->question_form_option
                                $value->question_form_option = $updatedJsonString;
                            }

                            // nationality and country array
                            if ($value->question_form_type == 'countries') {
                                $sql = "SELECT id,name AS label FROM countries WHERE flag=1";
                                $countries = DB::select($sql);

                                $value->question_form_option = json_encode($countries);
                            }

                            $hasCountriesQuestion = !empty(array_filter($FormQuestions, function ($value) {
                                return $value->question_form_type == 'countries';
                            }));

                            $hasStatesQuestion = !empty(array_filter($FormQuestions, function ($value) {
                                return $value->question_form_type == 'states';
                            }));

                            if (!$hasCountriesQuestion && $hasStatesQuestion && $value->question_form_type == 'states') {
                                $sql = "SELECT id,name FROM states WHERE flag=1";
                                $states = DB::select($sql);

                                $value->question_form_option = json_encode($states);
                            }
                        }

                        // dd($FormQuestions);

                        for ($i = 0; $i < $ticket['count']; $i++) {
                            $FinalFormQuestions[$ticket['id']][$i] = $FormQuestions;
                        }
                    }
                }
                // dd($FinalFormQuestions);

                $ResponseData['FormQuestions'] = $FinalFormQuestions;

                $ResponseData['YTCR_FEE_PERCENT'] = config('custom.ytcr_fee_percent');
                $ResponseData['PLATFORM_FEE_PERCENT'] = config('custom.platform_fee_percent');
                $ResponseData['PAYMENT_GATEWAY_FEE_PERCENT'] = config('custom.payment_gateway_fee_percent');
                $ResponseData['PAYMENT_GATEWAY_GST_PERCENT'] = config('custom.payment_gateway_gst_percent');

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

    function BookTickets(Request $request)
    {
        $ResponseData = $FinalFormQuestions = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $empty = false;
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        // dd($request->FormQuestions);

        if ($aToken['code'] == 200) {
            $aPost = $request->all();
            if (empty($aPost['booking_pay_id'])) {
                $empty = true;
                $field = 'Booking Payment Id';
            }
            // if (empty($aPost['FormQuestions'])) {
            //     $empty = true;
            //     $field = 'Form Questions';
            // }


            if (!$empty) {
                $Auth = new Authenticate();
                $Auth->apiLog($request);

                $BookingPaymentId = !empty($aPost['booking_pay_id']) ? $aPost['booking_pay_id'] : 0;
                $sql = "SELECT * FROM temp_booking_ticket_details WHERE booking_pay_id =:booking_pay_id";
                $BookingPayment = DB::select($sql, array('booking_pay_id' => $BookingPaymentId));
                // dd($BookingPayment);

                if (count($BookingPayment) > 0) {
                    $EventId = $BookingPayment[0]->event_id;
                    $TotalAttendee = $BookingPayment[0]->total_attendees;
                    $FormQuestions = !empty($BookingPayment[0]->FormQuestions) ? json_decode($BookingPayment[0]->FormQuestions)  : [];
                    $AllTickets = !empty($BookingPayment[0]->AllTickets) ? json_decode($BookingPayment[0]->AllTickets)  : [];
                    $TotalPrice = $BookingPayment[0]->TotalPrice;
                    $TotalDiscount = $BookingPayment[0]->TotalDiscount;
                    $ExtraPricing = !empty($BookingPayment[0]->ExtraPricing) ? json_decode($BookingPayment[0]->ExtraPricing)  : [];
                    $UtmCampaign = $BookingPayment[0]->UtmCampaign;
                    $GstArray = !empty($BookingPayment[0]->GstArray) ? json_decode($BookingPayment[0]->GstArray)  : [];
                    $TransactionStatus = 1;//!empty($BookingPayment[0]->TransactionStatus) ? json_decode($BookingPayment[0]->TransactionStatus)  : [];

                    // dd($EventId);
                    // $EventId = isset($aPost['event_id']) && !empty($aPost['event_id']) ? $aPost['event_id'] : 0;
                    // $TotalAttendee = isset($request->total_attendees) && !empty($request->total_attendees) ? $request->total_attendees : 0;
                    // $FormQuestions = isset($request->FormQuestions) && !empty($request->FormQuestions) ? $request->FormQuestions : [];
                    // $AllTickets = isset($request->AllTickets) && !empty($request->AllTickets) ? $request->AllTickets : [];
                    // $TotalPrice = isset($request->TotalPrice) && !empty($request->TotalPrice) ? $request->TotalPrice : 0;
                    // $TotalDiscount = isset($request->TotalDiscount) && !empty($request->TotalDiscount) ? $request->TotalDiscount : 0;
                    // $ExtraPricing = isset($request->ExtraPricing) && !empty($request->ExtraPricing) ? $request->ExtraPricing : [];
                    // $UtmCampaign = isset($request->UtmCampaign) && !empty($request->UtmCampaign) ? $request->UtmCampaign : "";
                    // $GstArray = isset($request->GstArray) && !empty($request->GstArray) ? $request->GstArray : [];

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
                        "transaction_status"=> $TransactionStatus
                    );
                    $Sql1 = "INSERT INTO event_booking (event_id,user_id,booking_date,total_amount,total_discount,utm_campaign,cart_details,transaction_status) VALUES (:event_id,:user_id,:booking_date,:total_amount,:total_discount,:utm_campaign,:cart_details,:transaction_status)";
                    DB::insert($Sql1, $Binding1);
                    $BookingId = DB::getPdo()->lastInsertId();

                    #booking_details
                    $BookingDetailsIds = [];

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
                            DB::insert($Sql2, $Binding2);
                            #Get the last inserted id of booking_details
                            $BookingDetailsId = DB::getPdo()->lastInsertId();

                            $BookingDetailsIds[$ticket->id] = $BookingDetailsId;

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

                                $Sql7 = "SELECT discount_type FROM event_coupon_details WHERE event_coupon_id=:event_coupon_id";
                                $Binding7 = array("event_coupon_id" => $appliedCouponId);
                                $Result = DB::select($Sql7, $Binding7);
                                $IsDiscountOneTime = 0;

                                if (count($Result) > 0) {
                                    $IsDiscountOneTime = $Result[0]->discount_type;
                                    $Sql8 = "";
                                    $Binding8 = [];
                                    if ($IsDiscountOneTime == 1) {
                                        $Binding8 = array("event_coupon_id" => $appliedCouponId);
                                        $Sql8 = "UPDATE event_coupon_details SET end_coupon=1 WHERE event_coupon_id=:event_coupon_id";
                                        DB::update($Sql8, $Binding8);
                                    }
                                }
                            }
                        }
                    }
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
                    $IdBookingDetails = 0;

                    foreach ($FormQuestions as $key => $arrays) {
                        foreach ($arrays as $subArray) {
                            $separatedArrays[] = json_encode($subArray);
                        }
                    }
                    foreach ($separatedArrays as $key => $value) {
                        $subArray = [];
                        $subArray = json_decode($value);
                        $TicketId = 0;
                        // dd($subArray);
                        foreach ($subArray as $key => $sArray) {
                            if (isset($sArray->question_form_name)) {
                                if ($sArray->question_form_name == 'first_name') {
                                    $first_name = $sArray->ActualValue;
                                } elseif ($sArray->question_form_name == 'last_name') {
                                    $last_name = $sArray->ActualValue;
                                } elseif ($sArray->question_form_name == 'email') {
                                    $email = $sArray->ActualValue;
                                }
                            }
                            if (empty($TicketId)) {
                                $TicketId = !empty($sArray->TicketId) ? $sArray->TicketId : 0;
                            }

                        }
                        // die;
                        $IdBookingDetails = isset($BookingDetailsIds[$TicketId]) ? $BookingDetailsIds[$TicketId] : 0;
                        $sql = "INSERT INTO attendee_booking_details (booking_details_id,ticket_id,attendee_details,email,firstname,lastname,created_at) VALUES (:booking_details_id,:ticket_id,:attendee_details,:email,:firstname,:lastname,:created_at)";
                        $Bind1 = array(
                            "booking_details_id" => $IdBookingDetails,
                            "ticket_id" => $TicketId,
                            "attendee_details" => json_encode($value),
                            "email" => $email,
                            "firstname" => $first_name,
                            "lastname" => $last_name,
                            "created_at" => strtotime("now")
                        );
                        DB::insert($sql, $Bind1);
                    }
                    // -------------------------------------------END ATTENDEE DETAIL
                    foreach ($FormQuestions as $Form) {
                        $TotTickets = count($Form);
                        $TotalTickets += $TotTickets;
                        foreach ($Form as $Question) {
                            // echo "<pre>";print_r($Question);
                            foreach ($Question as $value) {
                                // dd($BookingDetailsIds,$value['ticket_id']);
                                $Binding3 = [];
                                $Sql3 = "";
                                $IdBookingDetails = 0;
                                if ((isset($value->ActualValue)) && ($value->ActualValue !== "")) {

                                    if ($value->question_form_type == "file") {
                                        // $_FILES = $value['ActualValue'];
                                        $allowedExts = array('jpeg', 'jpg', "png", "gif", "bmp", "pdf");
                                        $is_valid = false;
                                        $filename = $address_proof_doc_upload = '';
                                        // if (is_array($value['ActualValue']) && !empty($value['ActualValue']["name"])) {
                                        //     // Validate file extension
                                        //     $address_proof_doc_upload_temp = explode(".", $value['ActualValue']["name"]);
                                        //     $address_proof_type = strtolower(end($address_proof_doc_upload_temp));
                                        //     if (!in_array($address_proof_type, $allowedExts)) {
                                        //         $filename = 'Address proof document';
                                        //         $is_valid = true;
                                        //     }

                                        //     // Move uploaded file to destination
                                        //     if (!$is_valid) {
                                        //         $Path = public_path('uploads/user_documents/');
                                        //         $address_proof_doc_upload = strtotime('now') . '.' . pathinfo($value['ActualValue']["name"], PATHINFO_EXTENSION);
                                        //         move_uploaded_file($value['ActualValue']["tmp_name"], $Path . $address_proof_doc_upload);
                                        //     }
                                        // }
                                    }
                                    // $IdBookingDetails = isset($BookingDetailsIds[$value['TicketId']]) ? $BookingDetailsIds[$value['TicketId']] : 0;

                                    // if (!empty($IdBookingDetails)) {
                                    //     $Binding3 = array(
                                    //         "booking_details_id" => $IdBookingDetails,
                                    //         "field_name" => $value['question_form_name'],
                                    //         "field_value" => ($value['question_form_type'] == "file") ? $address_proof_doc_upload : $value['ActualValue']
                                    //     );

                                    //     $Sql3 = "INSERT INTO attendee_details (booking_details_id,field_name,field_value) VALUES (:booking_details_id,:field_name,:field_value)";
                                    //     DB::insert($Sql3, $Binding3);
                                    // }
                                }
                            }
                        }
                    }
                    // }
                    $ResposneCode = 200;
                    $message = 'Request processed successfully';
                    $EventUrl = isset($request->EventUrl) && !empty($request->EventUrl) ? $request->EventUrl : "";
                    // $MessageContent = $this->sendBookingMail($UserId, $UserEmail, $EventId, $EventUrl, $TotalAttendee);
                    $this->sendBookingMail($UserId, $UserEmail, $EventId, $EventUrl, $TotalAttendee);
                    // $ResponseData['MessageContent'] = $MessageContent;
                }else{
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

    function sendBookingMail($UserId, $UserEmail, $EventId, $EventUrl, $TotalNoOfTickets)
    {
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

        $sql3 = "SELECT * FROM organizer WHERE user_id=:user_id";
        $Organizer = DB::select($sql3, ['user_id' => $UserId]);
        $OrgName = "";
        if (count($Organizer) > 0) {
            $OrgName = $Organizer[0]->name;
        }

        $ConfirmationEmail = array(
            // "USERID" => $UserId,
            // "EMAIL" => $UserEmail,
            "FIRSTNAME" => $User[0]->firstname,
            "LASTNAME" => $User[0]->lastname,
            "EVENTID" => $EventId,
            "EVENTNAME" => $Event[0]->name,
            "EVENTSTARTDATE" => (!empty($Event[0]->start_time)) ? date('d-m-Y', ($Event[0]->start_time)) : "",
            "EVENTSTARTTIME" => (!empty($Event[0]->start_time)) ? date('H:i A', ($Event[0]->start_time)) : "",
            "EVENTENDDATE" => (!empty($Event[0]->end_time)) ? date('d-m-Y', ($Event[0]->end_time)) : "",
            "EVENTENDTIME" => (!empty($Event[0]->end_time)) ? date('H:i A', ($Event[0]->end_time)) : "",
            "YTCRTEAM" => "Ytcr Team",
            "EVENTURL" => $EventUrl,
            "COMPANYNAME" => $OrgName,
            "TOTALTICKETS" => $TotalNoOfTickets,
            "VENUE" => $Venue,

            // venue,cost,registration id,ticket name,ticket type,t-shirt size(is available)
        );
        // dd($ConfirmationEmail);
        $Subject = "";
        $sql = "SELECT * FROM `event_communication` WHERE `event_id`=:event_id AND UPPER(subject_name)=:subject_name";
        $Communications = DB::select($sql, ["event_id" => $EventId, "subject_name" => strtoupper("Confirmation Email")]);
        if (count($Communications) > 0) {
            $MessageContent = $Communications[0]->message_content;
            $Subject = $Communications[0]->subject_name;
        } else {
            $MessageContent = "<p>Hello {FIRSTNAME},</p><p>Your event ticket booking is successful.</p><p>Thank you for booking for {EVENTNAME}.</p><p>Regards,</p><p>{YTCRTEAM}</p>";
            $Subject = "Confirmation Email";
        }

        foreach ($ConfirmationEmail as $key => $value) {
            $placeholder = '{' . $key . '}';
            $MessageContent = str_replace($placeholder, $value, $MessageContent);
        }

        // Output the filled message
        // dd($MessageContent);
        $Email = new Emails();
        // $Email->send_booking_mail($UserId,$UserEmail,$MessageContent,$Subject);
        // return $ConfirmationEmail;
        return;
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

                $SQL = "SELECT eb.*,
                (SELECT SUM(quantity) FROM booking_details WHERE user_id=eb.user_id AND event_id=eb.event_id) AS TotalCount,
                (SELECT name FROM events WHERE id=eb.event_id) AS EventName,
                (SELECT start_time FROM events WHERE id=eb.event_id) AS EventStartTime,
                (SELECT end_time FROM events WHERE id=eb.event_id) AS EventEndTime,
                (SELECT city FROM events WHERE id=eb.event_id) AS EventCity,
                (SELECT banner_image FROM events WHERE id=eb.event_id) AS banner_image
                    FROM event_booking AS eb
                    WHERE user_id=:user_id
                    GROUP BY eb.event_id";
                $BookingData = DB::select($SQL, array('user_id' => $UserId));

                // dd($BookingData);

                foreach ($BookingData as $event) {
                    $event->name = !empty($event->EventName) ? ucwords($event->EventName) : "";
                    $event->display_name = !empty($event->EventName) ? (strlen($event->EventName) > 40 ? ucwords(substr($event->EventName, 0, 40)) . "..." : ucwords($event->EventName)) : "";
                    $event->start_date = (!empty($event->EventStartTime)) ? gmdate("d M Y", $event->EventStartTime) : 0;
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
                (SELECT ticket_name FROM event_tickets WHERE id=bd.ticket_id) AS TicketName,
                (SELECT ticket_status FROM event_tickets WHERE id=bd.ticket_id) AS TicketStatus,
                (SELECT name FROM events WHERE id=bd.event_id) AS EventName,
                (SELECT banner_image FROM events WHERE id=bd.event_id) AS banner_image
                 FROM `attendee_booking_details` AS a
                LEFT JOIN booking_details AS bd ON bd.id=a.booking_details_id
                LEFT JOIN event_booking AS e ON e.id=bd.booking_id
                WHERE bd.event_id=:event_id AND e.event_id=:event_id1 AND bd.quantity !=0 AND bd.user_id=:user_id ";
                $BookingData = DB::select($sql, array('user_id' => $UserId, 'event_id' => $EventId, 'event_id1' => $EventId));
                // dd($BookingData);
                foreach ($BookingData as $event) {

                    $event->TicketName = !empty($event->TicketName) ? (strlen($event->TicketName) > 40 ? ucwords(substr($event->TicketName, 0, 40)) . "..." : ucwords($event->TicketName)) : "";
                    $event->booking_start_date = (!empty($event->booking_date)) ? gmdate("d M Y", $event->booking_date) : 0;
                    $event->booking_time = (!empty($event->booking_date)) ? date("h:i A", $event->booking_date) : "";

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
        // dd($aToken);
        // $aToken['code'] = 200;
        if ($aToken['code'] == 200) {
            $aPost = $request->all();

            if (!$empty) {
                $master = new Master();
                $Auth = new Authenticate();
                $Auth->apiLog($request);

                $UserId = $aToken['data']->ID;
                // dd($UserId);
                $sql1 = "SELECT CONCAT(firstname,' ',lastname) AS username FROM users WHERE id=:user_id";
                $User = DB::select($sql1, ['user_id' => $UserId]);
                $Username = (sizeof($User) > 0) ? $User[0]->username : '';
                // dd($Username);

                $Venue = "";
                $TicketArr = isset($request->ticket) ? $request->ticket : []; // ticket array
                // dd($TicketArr);

                $EventId = isset($TicketArr["event_id"]) ? $TicketArr["event_id"] : 0;
                $TicketId = isset($TicketArr["ticket_id"]) ? $TicketArr["ticket_id"] : 0;
                $AttenddeeName = isset($TicketArr["attendee_name"]) ? $TicketArr["attendee_name"] : "";
                $BookingDetailId = isset($TicketArr["booking_detail_id"]) ? $TicketArr["booking_detail_id"] : 0;
                $EventLink = isset($request->event_link) ? $request->event_link : "";

                if (!empty($EventId)) {
                    $sql2 = "SELECT start_time,end_time,address,city,state,country,pincode FROM events WHERE id=:event_id";
                    $Event = DB::select($sql2, ['event_id' => $EventId]);
                    // dd($Event);
                    if (sizeof($Event) > 0) {
                        foreach ($Event as $key => $event) {
                            $event->start_date = (!empty($event->start_time)) ? gmdate("d M Y", $event->start_time) : 0;
                            $event->end_date = (!empty($event->end_time)) ? gmdate("d M Y", $event->end_time) : 0;
                            $event->start_time_event = (!empty($event->start_time)) ? date("h:i A", $event->start_time) : "";
                            $event->end_date_event = (!empty($event->end_time)) ? date("h:i A", $event->end_time) : 0;

                            $Venue .= ($event->address !== "") ? $event->address . ", " : "";
                            $Venue .= ($event->city !== "") ? $master->getCityName($event->city) . ", " : "";
                            $Venue .= ($event->state !== "") ? $master->getStateName($event->state) . ", " : "";
                            $Venue .= ($event->country !== "") ? $master->getCountryName($event->country) . ", " : "";
                            $Venue .= ($event->pincode !== "") ? $event->pincode . ", " : "";

                            $event->Venue = $Venue;
                        }
                    }
                }

                $sql3 = "SELECT id,name,logo_image FROM organizer WHERE user_id=:user_id";
                $Organizer = DB::select($sql3, ['user_id' => $UserId]);
                // dd($Organizer[0]);

                if (count($Organizer) > 0) {
                    foreach ($Organizer as $key => $value) {
                        $value->logo_image = !empty($value->logo_image) ? url('/') . 'organiser/logo_image/' . $value->logo_image : "";
                    }
                }
                // dd($Organizer);
                // Generate QR code
                $qrCode = base64_encode(QrCode::format('png')->size(200)->generate($TicketArr['unique_ticket_id']));
                // $qrCode = "";
                $data = [
                    'ticket_details' => $TicketArr,
                    'event_details' => (sizeof($Event) > 0) ? $Event[0] : [],
                    'org_details' => (sizeof($Organizer) > 0) ? $Organizer[0] : [],
                    'Username' => $Username,
                    'EventLink' => $EventLink,
                    'QrCode' => $qrCode
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

                    $SQL = "SELECT ecd.*, ec.*, ec.id AS coupon_id,
                    (SELECT COUNT(id) FROM applied_coupons WHERE coupon_id = ecd.event_coupon_id AND event_id = ecd.event_id) AS coupon_count
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

}


