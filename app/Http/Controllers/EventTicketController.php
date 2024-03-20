<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Libraries\Authenticate;
use Illuminate\Support\Facades\DB;

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

        if ($aToken['code'] == 200) {
            $aPost = $request->all();
            $Auth = new Authenticate();
            $Auth->apiLog($request);
            if (empty($aPost['event_id'])) {
                $empty = true;
                $field = 'Event Id';
            }

            if (!$empty) {
                $sSQL = 'SELECT * FROM event_tickets Where event_id =:event_id';
                $ResponseData = DB::select(
                    $sSQL,
                    array(
                        'event_id' => $request->event_id
                    )
                );
                foreach ($ResponseData as $key => $value) {
                   $value->count = 0;
                }

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
                                'max_booking' => isset($aPost['max_booking']) ?$aPost['max_booking'] : 0,
                                'ticket_description' => isset($aPost['ticket_description']) ? $aPost['ticket_description'] : "",
                                'msg_attendance' => isset($aPost['msg_attendance']) ? $aPost['msg_attendance'] : "",
                                'minimum_donation_amount' => isset($aPost['minimum_donation_amount']) ? $aPost['minimum_donation_amount'] : 0,

                                'early_bird'=>isset($aPost['early_bird']) ? $aPost['early_bird'] : 0,
                                'no_of_tickets'=>isset($aPost['no_of_tickets']) ? $aPost['no_of_tickets'] : 0,
                                'start_time' => $EBStartTime,
                                'end_time' => $EBEndTime,
                                'discount'=>isset($aPost['discount']) ? $aPost['discount'] : 0,
                                'discount_value'=>isset($aPost['discount_value']) ? $aPost['discount_value'] : 0,

                                'id' => $TicketId
                            );

                            // dd($Binding);
                            $SQL = 'UPDATE event_tickets SET ticket_name=:ticket_name,ticket_status = :ticket_status,total_quantity = :total_quantity,ticket_price = :ticket_price,payment_to_you = :payment_to_you,ticket_sale_start_date = :ticket_sale_start_date,ticket_sale_end_date = :ticket_sale_end_date,advanced_settings=:advanced_settings,player_of_fee = :player_of_fee,player_of_gateway_fee = :player_of_gateway_fee,min_booking = :min_booking,max_booking = :max_booking,ticket_description = :ticket_description,msg_attendance = :msg_attendance,minimum_donation_amount= :minimum_donation_amount,early_bird=:early_bird,no_of_tickets=:no_of_tickets,start_time=:start_time,end_time=:end_time,discount=:discount,discount_value=:discount_value WHERE id=:id';
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
                                'max_booking' => isset($aPost['max_booking']) ?$aPost['max_booking'] : 0,
                                'ticket_description' => isset($aPost['ticket_description']) ? $aPost['ticket_description'] : "",
                                'msg_attendance' => isset($aPost['msg_attendance']) ? $aPost['msg_attendance'] : "",
                                'minimum_donation_amount' => isset($aPost['minimum_donation_amount']) ? $aPost['minimum_donation_amount'] : 0,

                                'early_bird'=>isset($aPost['early_bird']) ? $aPost['early_bird'] : 0,
                                'no_of_tickets'=>isset($aPost['no_of_tickets']) ? $aPost['no_of_tickets'] : 0,
                                'start_time' => $EBStartTime,
                                'end_time' => $EBEndTime,
                                'discount'=>isset($aPost['discount']) ? $aPost['discount'] : 0,
                                'discount_value'=>isset($aPost['discount_value']) ? $aPost['discount_value'] : 0,
                            );
                            // dd($Binding);
                            $SQL2 = 'INSERT INTO event_tickets (event_id,ticket_name,ticket_status,total_quantity,ticket_price,payment_to_you,ticket_sale_start_date,ticket_sale_end_date,advanced_settings,player_of_fee,player_of_gateway_fee,min_booking,max_booking,ticket_description,msg_attendance,minimum_donation_amount,early_bird,no_of_tickets,start_time,end_time,discount,discount_value) VALUES(:event_id,:ticket_name,:ticket_status,:total_quantity,:ticket_price,:payment_to_you,:ticket_sale_start_date,:ticket_sale_end_date,:advanced_settings,:player_of_fee,:player_of_gateway_fee,:min_booking,:max_booking,:ticket_description,:msg_attendance,:minimum_donation_amount,:early_bird,:no_of_tickets,:start_time,:end_time,:discount,:discount_value)';

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
                $value->ticket_start_time = (!empty($value->ticket_sale_start_date)) ? date("h:i", $value->ticket_sale_start_date) : 0;

                $value->ticket_end_date = !empty($value->ticket_sale_end_date) ? date("Y-m-d", $value->ticket_sale_end_date) : 0;
                $value->ticket_end_time = (!empty($value->ticket_sale_end_date)) ? date("h:i", $value->ticket_sale_end_date) : 0;

                //EARLY BIRD
                $value->eb_start_date = !empty($value->start_time) ? date("Y-m-d", $value->start_time) : 0;
                $value->eb_start_time = (!empty($value->start_time)) ? date("h:i", $value->start_time) : 0;

                $value->eb_end_date = !empty($value->end_time) ? date("Y-m-d", $value->end_time) : 0;
                $value->eb_end_time = (!empty($value->end_time)) ? date("h:i", $value->end_time) : 0;
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

}
