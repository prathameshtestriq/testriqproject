<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Libraries\Authenticate;
use Illuminate\Support\Facades\DB;

class EventTicketController extends Controller
{
    public function geteventticket(Request $request){
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
                $sSQL = 'SELECT * FROM event_tickets Where event_id =:event_id order by sort_order asc';
                $ResponseData = DB::select($sSQL, array(
                    'event_id' => $request->event_id
                ));

                $ResposneCode = 200;
                $message = 'Request processed successfully';

            }else {
                $ResposneCode = 400;
                $message = $field . ' is empty';
                // dd($message);
            }

        }else {
            $ResposneCode = $aToken['code'];
            $message = $aToken['message'];
        }

        $response = [
            'data' => $ResponseData,
            'message' => $message
        ];

        return response()->json($response, $ResposneCode);
    }
    public function addediteventticket(Request $request){
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $empty = false;
        $flag = true;
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        // dd($aToken);
        if ($aToken['code'] == 200) {
            $aPosts = $request->all();
            $Auth = new Authenticate();
            $Auth->apiLog($request);

            foreach ($aPosts as $aPost) {
                if (empty($aPost['ticket_name'])) {
                    $empty = true;
                    $field = 'Ticket Name';
                }
                if (empty($aPost['ticket_type'])) {
                    $empty = true;
                    $field = 'Ticket Type';
                }
                if (empty($aPost['total_quantity'])) {
                    $empty = true;
                    $field = 'Total Quantity';
                }
                if (empty($aPost['ticket_sale_start_date'])) {
                    $empty = true;
                    $field = 'Ticket Sale Start Date';
                }
                if (empty($aPost['ticket_sale_end_date'])) {
                    $empty = true;
                    $field = 'Ticket Sale End Date';
                }


                if (!$empty) {
                    //ticket_type=[1=>paid,2=>free,3=>donation]
                    $tickettype = $aPost['ticket_type'];
                    if($tickettype == 1) {
                    if(!empty($aPost['ticket_price'])){
                        $aPost['minimum_donation_amount']=0;
                    }else{
                        $field = 'Ticket Price';
                        $flag = false;
                    }
                    }
                    if($tickettype == 2) {
                        if(!empty($aPost['min_booking']) && !empty($aPost['max_booking'])){
                        $aPost['ticket_price'] = 0;
                        $aPost['minimum_donation_amount']=0;
                        }else{
                            $field = 'Min Max Booking';
                            $flag = false;
                        }
                    }
                    if($tickettype == 3) {
                        if(!empty($aPost['minimum_donation_amount'])){
                        $aPost['ticket_price'] = 0;
                        $aPost['min_booking'] = 0;
                        $aPost['max_booking'] = 0;
                        }else{
                        $field = 'Min Donation Amount';
                        $flag = false;
                        }
                    }


                    // dd($flag);
                    if($flag){
                        $EventTicketId = isset($request->id) ? $request->id : 0;
                        if(!empty($EventTicketId)){
                            // dd("here");
                            $Binding = array(
                                // 'event_id' => $aPost['event_id'],
                                'ticket_name' => $aPost['ticket_name'],
                                'ticket_type' => $aPost['ticket_type'],
                                'total_quantity' => $aPost['total_quantity'],
                                'ticket_price' => $aPost['ticket_price'],
                                'payment_to_you' => $aPost['payment_to_you'],
                                'ticket_sale_start_date' => strtotime(date('Y-m-d H:i', strtotime($aPost['ticket_sale_start_date']))),
                                'ticket_sale_end_date' => strtotime(date('Y-m-d H:i', strtotime($aPost['ticket_sale_end_date']))),
                                'player_of_fee' => $aPost['player_of_fee'],
                                'player_of_gateway_fee' => $aPost['player_of_gateway_fee'],
                                'min_booking' => $aPost['min_booking'],
                                'max_booking' => $aPost['max_booking'],
                                'ticket_description' => $aPost['ticket_description'],
                                'msg_attendance' => $aPost['msg_attendance'],
                                'minimum_donation_amount' => $aPost['minimum_donation_amount'],
                                'id' => $request->id
                            );
                            // dd($Binding);
                            $SQL = 'UPDATE event_tickets SET ticket_name=:ticket_name,ticket_type = :ticket_type,total_quantity = :total_quantity,ticket_price = :ticket_price,payment_to_you = :payment_to_you,ticket_sale_start_date = :ticket_sale_start_date,ticket_sale_end_date = :ticket_sale_end_date,player_of_fee = :player_of_fee,player_of_gateway_fee = :player_of_gateway_fee,min_booking = :min_booking,max_booking = :max_booking,ticket_description = :ticket_description,msg_attendance = :msg_attendance,minimum_donation_amount= :minimum_donation_amount WHERE id=:id';
                            DB::update($SQL, $Binding);

                            $ResposneCode = 200;
                            $message = 'Event Ticket Update Successfully';

                        }else{
                            if (empty($aPost['event_id'])) {
                                $empty = true;
                                $field = 'Event Id';
                            }
                            if(!$empty){
                                $Binding = array(
                                'event_id' => $aPost['event_id'],
                                'ticket_name' => $aPost['ticket_name'],
                                'ticket_type' => $aPost['ticket_type'],
                                'total_quantity' => $aPost['total_quantity'],
                                'ticket_price' => $aPost['ticket_price'],
                                'payment_to_you' => $aPost['payment_to_you'],
                                'ticket_sale_start_date' => strtotime(date('Y-m-d H:i', strtotime($aPost['ticket_sale_start_date']))),
                                'ticket_sale_end_date' => strtotime(date('Y-m-d H:i', strtotime($aPost['ticket_sale_end_date']))),
                                'player_of_fee' => $aPost['player_of_fee'],
                                'player_of_gateway_fee' => $aPost['player_of_gateway_fee'],
                                'min_booking' => $aPost['min_booking'],
                                'max_booking' => $aPost['max_booking'],
                                'ticket_description' => $aPost['ticket_description'],
                                'msg_attendance' => $aPost['msg_attendance'],
                                'minimum_donation_amount' => $aPost['minimum_donation_amount']
                                );
                                // dd($Binding);
                                $SQL2 = 'INSERT INTO event_tickets (event_id,ticket_name,ticket_type,total_quantity,ticket_price,payment_to_you,ticket_sale_start_date,ticket_sale_end_date,player_of_fee,player_of_gateway_fee,min_booking,max_booking,ticket_description,msg_attendance,minimum_donation_amount) VALUES(:event_id,:ticket_name,:ticket_type,:total_quantity,:ticket_price,:payment_to_you,:ticket_sale_start_date,:ticket_sale_end_date,:player_of_fee,:player_of_gateway_fee,:min_booking,:max_booking,:ticket_description,:msg_attendance,:minimum_donation_amount)';

                                DB::select($SQL2, $Binding);

                                $ResposneCode = 200;
                                $message = 'Event Ticket Insert Successfully';
                            } else {
                                $ResposneCode = 400;
                                $message = $field  . ' is empty';
                            }
                        }
                    } else {
                        $ResposneCode = 400;
                        $message = $field  . ' is empty';
                    }
                } else {
                    $ResposneCode = 400;
                    $message = $field . ' is empty';
                    // dd($message);
                }
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
    public function EventTicketDelete(Request $request){
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

            $sSQL = 'DELETE FROM event_tickets WHERE id=:id ';
            $ResponseData= DB::delete($sSQL,
                array(
                    'id' => $request->id
                )
            );
            $ResposneCode = 200;
            $message = 'Event Ticket Deleted Successfully';

        }else {
            $ResposneCode = $aToken['code'];
            $message = $aToken['message'];
        }

        $response = [
            'data' => $ResponseData,
            'message' => $message
        ];

        return response()->json($response, $ResposneCode);
    }
    public function getticketdetail(Request $request){
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

            $sSQL = 'SELECT * FROM event_tickets Where id =:id';
            $ResponseData = DB::select($sSQL, array(
                'id' => $request->id
            ));

            $ResposneCode = 200;
            $message = 'Request processed successfully';

        }else {
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
