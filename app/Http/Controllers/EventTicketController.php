<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Libraries\Authenticate;
use Illuminate\Support\Facades\DB;
use App\Models\Master;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

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
                $sSQL = 'SELECT * FROM event_tickets WHERE event_id =:event_id AND active = 1 AND is_deleted = 0';
                $ResponseData['event_tickets'] = DB::select($sSQL, array('event_id' => $aPost['event_id']));
                foreach ($ResponseData['event_tickets'] as $value) {
                    $value->count = 0;
                    $value->Error = "";

                    $sql = "SELECT COUNT(id) AS TotalBookedTickets FROM booking_details WHERE event_id=:event_id AND ticket_id=:ticket_id";
                    $TotalTickets = DB::select($sql, array("event_id" => $aPost['event_id'], "ticket_id" => $value->id));

                    $value->TotalBookedTickets = ((sizeof($TotalTickets) > 0) && (isset($TotalTickets[0]->TotalBookedTickets))) ? $TotalTickets[0]->TotalBookedTickets : 0;

                    $value->strike_out_price = ($value->early_bird == 1) ? $value->ticket_price : 0;

                    $discount_ticket_price = 0;
                    $total_discount = 0;
                    if ($value->discount === 1) { //percentage
                        $total_discount = ($value->ticket_price * ($value->discount_value / 100));
                        $discount_ticket_price = $value->ticket_price - $total_discount;
                    } else if ($value->discount === 2) { //amount
                        $total_discount = $value->discount_value;
                        $discount_ticket_price = $value->ticket_price - $value->discount_value;
                    }
                    $value->discount_ticket_price = $discount_ticket_price;
                    $value->total_discount = $total_discount;
                }


                $Sql = "SELECT name,start_time,city FROM events WHERE id=:event_id";
                $EventData = DB::select($Sql, array('event_id' => $aPost['event_id']));
                foreach ($EventData as $key => $event) {
                    $event->display_name = !empty($event->name) ? $event->name : "";
                    $event->start_date = (!empty($event->start_time)) ? gmdate("d M Y", $event->start_time) : 0;
                    $event->city_name = !empty($event->city) ? $master->getCityName($event->city) : "";
                }
                $ResponseData['EventData'] = $EventData;


                $ResposneCode = 200;
                $message = 'Request processed successfully';

            } else {
                $ResposneCode = 400;
                $message = $field . ' is empty';
                // dd($message);
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

                    if (!empty($TicketId)) {
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
                            'id' => $TicketId
                        );

                        // dd($Binding);
                        $SQL = 'UPDATE event_tickets SET ticket_name=:ticket_name,ticket_status = :ticket_status,total_quantity = :total_quantity,ticket_price = :ticket_price,payment_to_you = :payment_to_you,ticket_sale_start_date = :ticket_sale_start_date,ticket_sale_end_date = :ticket_sale_end_date,advanced_settings=:advanced_settings,player_of_fee = :player_of_fee,player_of_gateway_fee = :player_of_gateway_fee,min_booking = :min_booking,max_booking = :max_booking,ticket_description = :ticket_description,msg_attendance = :msg_attendance,minimum_donation_amount= :minimum_donation_amount,early_bird=:early_bird,no_of_tickets=:no_of_tickets,start_time=:start_time,end_time=:end_time,discount=:discount,discount_value=:discount_value,category=:category WHERE id=:id';
                        DB::update($SQL, $Binding);

                        $ResposneCode = 200;
                        $message = 'Event Ticket Updated Successfully';

                    } else {
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
                        );
                        // dd($Binding);
                        $SQL2 = 'INSERT INTO event_tickets (event_id,ticket_name,ticket_status,total_quantity,ticket_price,payment_to_you,ticket_sale_start_date,ticket_sale_end_date,advanced_settings,player_of_fee,player_of_gateway_fee,min_booking,max_booking,ticket_description,msg_attendance,minimum_donation_amount,early_bird,no_of_tickets,start_time,end_time,discount,discount_value,category) VALUES(:event_id,:ticket_name,:ticket_status,:total_quantity,:ticket_price,:payment_to_you,:ticket_sale_start_date,:ticket_sale_end_date,:advanced_settings,:player_of_fee,:player_of_gateway_fee,:min_booking,:max_booking,:ticket_description,:msg_attendance,:minimum_donation_amount,:early_bird,:no_of_tickets,:start_time,:end_time,:discount,:discount_value,:category)';

                        DB::select($SQL2, $Binding);

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

            // $sSQL = 'DELETE FROM event_tickets WHERE id=:id ';
            // $ResponseData= DB::delete($sSQL,
            //     array(
            //         'id' => $request->ticket_id
            //     )
            // );
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

                $Auth = new Authenticate();
                $Auth->apiLog($request);
                $TotalAttendee = isset($request->total_attendee) && !empty($request->total_attendee) ? $request->total_attendee : 0;
                $AllTickets = isset($request->AllTickets) && !empty($request->AllTickets) ? $request->AllTickets : [];
                // dd($AllTickets);

                $sSQL = 'SELECT * FROM event_form_question WHERE event_id =:event_id AND question_status = 1 ORDER BY sort_order';
                $FormQuestions = DB::select($sSQL, array('event_id' => $aPost['event_id']));


                foreach ($FormQuestions as $value) {
                    $value->ActualValue = "";
                    $value->Error = "";
                    $value->TicketId = 0;

                    if ($value->id == 170) {
                        // dd(($value->question_form_option));
                    }
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
                }
                // dd($FormQuestions);

                if (!empty($AllTickets)) {
                    foreach ($AllTickets as $ticket) {
                        // dd($ticket);
                        for ($i = 0; $i < $ticket['count']; $i++) {
                            $FinalFormQuestions[$ticket['id']][$i] = $FormQuestions;
                        }
                    }
                }
                // dd($FinalFormQuestions);

                $ResponseData['FormQuestions'] = $FinalFormQuestions;

                // $sql = "SELECT COUNT(id) FROM ticket_booking WHERE event_id=:event_id AND ticket_id=:ticket_id";
                // $ResponseData['TotalBookedTickets'] = $TotalBookedTickets;

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
            if (empty($aPost['event_id'])) {
                $empty = true;
                $field = 'Event Id';
            }
            if (empty($aPost['FormQuestions'])) {
                $empty = true;
                $field = 'Form Questions';
            }

            if (!$empty) {
                $Auth = new Authenticate();
                $Auth->apiLog($request);

                $EventId = isset($aPost['event_id']) && !empty($aPost['event_id']) ? $aPost['event_id'] : 0;
                $TotalAttendee = isset($request->total_attendees) && !empty($request->total_attendees) ? $request->total_attendees : 0;
                $FormQuestions = isset($request->FormQuestions) && !empty($request->FormQuestions) ? $request->FormQuestions : [];
                $AllTickets = isset($request->AllTickets) && !empty($request->AllTickets) ? $request->AllTickets : [];
                $TotalPrice = isset($request->TotalPrice) && !empty($request->TotalPrice) ? $request->TotalPrice : 0;
                $TotalDiscount = isset($request->TotalDiscount) && !empty($request->TotalDiscount) ? $request->TotalDiscount : 0;
                $ExtraPricing = isset($request->ExtraPricing) && !empty($request->ExtraPricing) ? $request->ExtraPricing : [];

                $UserId = $aToken["data"]->ID;
                $TotalTickets = 0;

                if (!empty($TotalPrice)) {
                    #event_booking
                    $Binding1 = array(
                        "event_id" => $EventId,
                        "user_id" => $UserId,
                        "booking_date" => strtotime("now"),
                        "total_amount" => $TotalPrice,
                        "total_discount" => $TotalDiscount
                    );
                    $Sql1 = "INSERT INTO event_booking (event_id,user_id,booking_date,total_amount,total_discount) VALUES (:event_id,:user_id,:booking_date,:total_amount,:total_discount)";
                    DB::insert($Sql1, $Binding1);
                    $BookingId = DB::getPdo()->lastInsertId();

                    #booking_details
                    $BookingDetailsIds = [];

                    foreach ($AllTickets as $ticket) {
                        if (!empty($ticket["count"])) {
                            $Binding2 = [];
                            $Sql2 = "";
                            $Binding2 = array(
                                "booking_id" => $BookingId,
                                "event_id" => $EventId,
                                "user_id" => $UserId,
                                "ticket_id" => $ticket["id"],
                                "quantity" => $ticket["count"],
                                "ticket_amount" => $ticket["ticket_price"],
                                "ticket_discount" => $ticket["ticket_discount"],
                                "booking_date" => strtotime("now"),
                            );
                            $Sql2 = "INSERT INTO booking_details (booking_id,event_id,user_id,ticket_id,quantity,ticket_amount,ticket_discount,booking_date) VALUES (:booking_id,:event_id,:user_id,:ticket_id,:quantity,:ticket_amount,:ticket_discount,:booking_date)";
                            DB::insert($Sql2, $Binding2);
                            #Get the last inserted id of booking_details
                            $BookingDetailsId = DB::getPdo()->lastInsertId();

                            $BookingDetailsIds[$ticket["id"]] = $BookingDetailsId;
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
                                "ticket_id" => $value["ticket_id"],
                                "quantity" => 0,
                                "ticket_amount" => $value["value"],
                                "ticket_discount" => 0,
                                "booking_date" => strtotime("now"),
                                "question_id" => $value["question_id"],
                                "attendee_number" => $value["aNumber"]
                            );
                            $Sql4 = "INSERT INTO booking_details (booking_id,event_id,user_id,ticket_id,quantity,ticket_amount,ticket_discount,booking_date,question_id,attendee_number) VALUES (:booking_id,:event_id,:user_id,:ticket_id,:quantity,:ticket_amount,:ticket_discount,:booking_date,:question_id,:attendee_number)";
                            DB::insert($Sql4, $Binding4);

                            #ADD COUNT IN extra_pricing_booking TABLE
                            $Binding5 = [];
                            $Sql5 = "";
                            $CurrentSoldCount = 0;

                            #Check If Question Id & Option Id Exists In extra_pricing_booking Table Or Not. If Yes Then Get The Current Count
                            if (!empty($value["count"])) {
                                $SqlExist = "SELECT id,current_count,option_id FROM extra_pricing_booking WHERE question_id =:question_id AND option_id=:option_id";
                                $Exist = DB::select($SqlExist, array("question_id" => $value["question_id"], "option_id" => $value["option_id"]));

                                if (sizeof($Exist) > 0) {
                                    #UPDATE THE RECORD GET CURRENT COUNT FROM SAME TABLE
                                    $ExistId = $Exist[0]->id;
                                    $SoldCount = $Exist[0]->current_count;
                                    $CurrentSoldCount = $SoldCount + 1;

                                    if ($value["count"] >= $CurrentSoldCount) {
                                        $Binding5 = array(
                                            "event_id" => $EventId,
                                            "booking_id" => $BookingId,
                                            "user_id" => $UserId,
                                            "ticket_id" => $value["ticket_id"],
                                            "total_count" => $value["count"],
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
                                        "ticket_id" => $value["ticket_id"],
                                        "question_id" => $value["question_id"],
                                        "option_id" => $value["option_id"],
                                        "total_count" => $value["count"],
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
                                if ($value['ActualValue'] !== "") {

                                    if ($value['question_form_type'] == "file") {
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
                                    $IdBookingDetails = isset($BookingDetailsIds[$value['TicketId']]) ? $BookingDetailsIds[$value['TicketId']] : 0;
                                    if (!empty($IdBookingDetails)) {
                                        $Binding3 = array(
                                            "booking_details_id" => $IdBookingDetails,
                                            "field_name" => $value['question_form_name'],
                                            "field_value" => ($value['question_form_type'] == "file") ? $address_proof_doc_upload : $value['ActualValue']
                                        );

                                        $Sql3 = "INSERT INTO attendee_details (booking_details_id,field_name,field_value) VALUES (:booking_details_id,:field_name,:field_value)";
                                        DB::insert($Sql3, $Binding3);
                                    }
                                }
                            }
                        }
                    }
                }
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

    function GetEventBookingTickets(Request $request)
    {
        $ResponseData = $FinalFormQuestions = [];
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
                $master = new Master();
                $Auth = new Authenticate();
                $Auth->apiLog($request);
                $TicketBookingArr = [];

                $EventId = isset($aPost['event_id']) ? $aPost['event_id'] : 0;
                $UserId = $aToken['data']->ID ? $aToken['data']->ID : 20;
                // return $UserId;

                // $SQL = "SELECT *,
                //     (SELECT ticket_name FROM event_tickets WHERE id=bd.ticket_id) AS TicketName
                //     FROM booking_details AS bd
                //     WHERE user_id=:user_id AND event_id=:event_id";
                // $BookingData = DB::select($SQL, array('user_id' => $UserId, 'event_id' => $EventId));
                // $key = 0;
                // foreach ($BookingData as $ticket) {
                //     // dd($ticket->quantity);
                //     $attendee_names = [];
                //     for ($i = 1; $i <= $ticket->quantity; $i++) {
                //         $sql1 = "SELECT field_value FROM attendee_details WHERE booking_details_id = :booking_details_id AND (field_name = 'first_name') LIMIT :start, :end";
                //         $FirstName = DB::select($sql1, array('booking_details_id' => $ticket->id, 'start' => $i - 1, 'end' => $i));
                //         $sql2 = "SELECT field_value FROM attendee_details WHERE booking_details_id = :booking_details_id AND (field_name = 'last_name') LIMIT :start, :end";
                //         $LastName = DB::select($sql2, array('booking_details_id' => $ticket->id, 'start' => $i - 1, 'end' => $i));

                //         // echo $i; echo "<pre>";echo ($FirstName[0]->field_value);
                //         // $ticket->attendee_name ="";
                //         // $ticket->attendee_name = (sizeof($FirstName) > 0) ? ((sizeof($LastName)>0) ? $FirstName[0]->field_value." ".$LastName[0]->field_value : $FirstName[0]->field_value ): "";

                //         // // echo $key;echo "<pre>";print_r($ticket);
                //         // $TicketBookingArr[$key] = $ticket;

                //         $attendee_names[] = (sizeof($FirstName) > 0) ? ((sizeof($LastName) > 0) ? $FirstName[0]->field_value . " " . $LastName[0]->field_value : $FirstName[0]->field_value) : ""; // Add attendee
                //         // $ticket->i_val = $i;
                //         $ticket->attendee_name = (sizeof($attendee_names) > 0) ? $attendee_names : [];

                //         $TicketBookingArr[$key] = $ticket;
                //         $key++;
                //     }


                //     //
                // }
                // echo "<pre>";
                // print_r($TicketBookingArr);
                // die;


                $SQL = "SELECT *,
                (SELECT ticket_name FROM event_tickets WHERE id=bd.ticket_id) AS TicketName,
                (SELECT name FROM events WHERE id=bd.event_id) AS EventName,
                (SELECT banner_image FROM events WHERE id=bd.event_id) AS banner_image

                FROM booking_details AS bd
                WHERE bd.user_id=:user_id AND bd.event_id=:event_id";
                $BookingData = DB::select($SQL, array('user_id' => $UserId, 'event_id' => $EventId));
                $TicketBookingArr = [];
                foreach ($BookingData as $ticket) {
                    if ($ticket->quantity > 1) {
                        for ($j = 0; $j < $ticket->quantity; $j++) {
                            $ticketCopy = clone $ticket;
                            $attendee_names = "";
                            $sql1 = "SELECT field_value FROM attendee_details WHERE booking_details_id = :booking_details_id AND (field_name = 'first_name') LIMIT :start, :end";
                            $FirstName = DB::select($sql1, array('booking_details_id' => $ticket->id, 'start' => $j, 'end' => 1));
                            $sql2 = "SELECT field_value FROM attendee_details WHERE booking_details_id = :booking_details_id AND (field_name = 'last_name') LIMIT :start, :end";
                            $LastName = DB::select($sql2, array('booking_details_id' => $ticket->id, 'start' => $j, 'end' => 1));

                            $attendee_name = (sizeof($FirstName) > 0) ? ((sizeof($LastName) > 0) ? $FirstName[0]->field_value . " " . $LastName[0]->field_value : $FirstName[0]->field_value) : ""; // Add attendee

                            if (!empty($attendee_name)) {
                                if (!empty($attendee_names)) {
                                    $attendee_names .= ", ";
                                }
                                $attendee_names .= $attendee_name;
                            }

                            $ticketCopy->attendee_name = $attendee_names;
                            $TicketBookingArr[] = $ticketCopy;
                        }
                    } else {
                        $attendee_names = "";
                        $sql1 = "SELECT field_value FROM attendee_details WHERE booking_details_id = :booking_details_id AND (field_name = 'first_name') LIMIT :start, :end";
                        $FirstName = DB::select($sql1, array('booking_details_id' => $ticket->id, 'start' => 0, 'end' => 1));
                        $sql2 = "SELECT field_value FROM attendee_details WHERE booking_details_id = :booking_details_id AND (field_name = 'last_name') LIMIT :start, :end";
                        $LastName = DB::select($sql2, array('booking_details_id' => $ticket->id, 'start' => 0, 'end' => 1));

                        $attendee_name = (sizeof($FirstName) > 0) ? ((sizeof($LastName) > 0) ? $FirstName[0]->field_value . " " . $LastName[0]->field_value : $FirstName[0]->field_value) : ""; // Add attendee

                        if (!empty($attendee_name)) {
                            $ticket->attendee_name = $attendee_name;
                        }

                        $TicketBookingArr[] = $ticket;
                    }
                }

                foreach ($TicketBookingArr as $event) {
                    // $event->TicketName =
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
                    $uniqueId = $EventId . "-" . $event->id . "-" . $event->booking_date;
                    $event->unique_ticket_id = $uniqueId;

                }
                // dd($BookingData);
                $ResponseData['BookingData'] = $TicketBookingArr;

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

                $EventId = isset($request->event_id) ? $request->event_id : 0;
                $TicketId = isset($request->ticket_id) ? $request->ticket_id : 0;
                $AttenddeeName = isset($request->attendee_name) ? $request->attendee_name : "";
                $BookingDetailId = isset($request->booking_detail_id) ? $request->booking_detail_id : 0;

                $data = [
                    'title' => "Booking Detail Id : " . $BookingDetailId,
                    'content' => 'This is a sample PDF generated using Laravel and Dompdf.'
                ];

                $pdf = PDF::loadView('pdf_template', $data);

                $PdfName = $EventId . $TicketId . $BookingDetailId . time() . '.pdf';
                // dd($PdfName);
                // $pdf->download($PdfName);
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

}


