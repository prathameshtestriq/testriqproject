<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Libraries\Authenticate;
use Illuminate\Support\Facades\DB;


class EventDashboardController extends Controller
{
    function getInsights(Request $request)
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
                $UserId = $aToken['data']->ID ? $aToken['data']->ID : 0;

                // Net Sales
                $sql = "SELECT SUM(b.quantity) AS NetSales 
                    FROM booking_details AS b
                    LEFT JOIN event_booking AS e ON b.booking_id = e.id
                    WHERE b.event_id =:event_id AND e.transaction_status = 1";
                $TotalBooking = DB::select($sql, array('event_id' => $EventId));

                $NetSales = (count($TotalBooking) > 0) ? $TotalBooking[0]->NetSales : 0;
                $ResponseData['NetSales'] = $NetSales;

                // Registration
                $sql = "SELECT COUNT(DISTINCT(user_id)) AS TotalRegistration,SUM(total_amount) AS TotalAmount FROM event_booking WHERE event_id =:event_id AND transaction_status = 1";
                $TotalRegistration = DB::select($sql, array('event_id' => $EventId));
                $ResponseData['TotalRegistration'] = (count($TotalRegistration) > 0) ? $TotalRegistration[0]->TotalRegistration : 0;
                $ResponseData['TotalAmount'] = (count($TotalRegistration) > 0) ? $TotalRegistration[0]->TotalAmount : 0;

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

    function getRegisteredUsers(Request $request)
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
                $UserId = $aToken['data']->ID ? $aToken['data']->ID : 0;

                $SearchUser = isset($aPost['user_name']) ? $aPost['user_name'] : 0;
                $TransactionStatus = isset($aPost['TransactionStatus']) ? $aPost['TransactionStatus'] : "";

                $FromDate = isset($aPost['from_date']) ? strtotime(date("Y-m-d", strtotime($aPost['from_date']))) : 0;
                $ToDate = isset($aPost['to_date']) ? strtotime(date("Y-m-d 23:59:59", strtotime($aPost['to_date']))) : 0;

                $sql = "SELECT 
                         eb.id AS EventBookingId,
                         eb.user_id,
                         eb.booking_date,
                         eb.total_amount AS TotalAmount,
                         eb.transaction_status,
                         SUM(bd.quantity) AS TotalTickets,
                         u.id,
                         u.firstname,
                         u.lastname,
                         u.email,
                         u.mobile
                     FROM event_booking AS eb 
                     LEFT JOIN booking_details AS bd ON bd.booking_id = eb.id
                     LEFT JOIN users AS u ON u.id = eb.user_id
                     WHERE eb.event_id=:event_id ";
                if (!empty($TransactionStatus)) {
                    if ($TransactionStatus == 3)
                        $TransactionStatus = 0;
                    $sql .= " AND eb.transaction_status= " . $TransactionStatus;
                }
                if ($SearchUser) {
                    $sql .= " AND (u.firstname LIKE '%" . $SearchUser . "%' OR u.lastname LIKE '%" . $SearchUser . "%')";
                }
                if (!empty($FromDate) && empty($ToDate)) {
                    $sql .= " AND eb.booking_date >= " . $FromDate;
                }
                if (empty($FromDate) && !empty($ToDate)) {
                    $sql .= " AND eb.booking_date <= " . $ToDate;
                }
                if (!empty($FromDate) && !empty($ToDate)) {
                    $sql .= " AND eb.booking_date BETWEEN '.$FromDate.' AND " . $ToDate;
                }
                $sql .= " GROUP BY bd.booking_id";
                $sql .= " ORDER BY eb.id DESC";
                // dd($sql);
                $UserData = DB::select($sql, array('event_id' => $EventId));

                foreach ($UserData as $key => $value) {
                    $value->booking_date = !empty($value->booking_date) ? date("Y-m-d H:i A", ($value->booking_date)) : '';
                }
                $ResponseData['UserData'] = (count($UserData) > 0) ? $UserData : [];

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

    function getNetSales(Request $request)
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
                $UserId = $aToken['data']->ID ? $aToken['data']->ID : 0;
                $user_id = isset($aPost['user_id']) ? $aPost['user_id'] : 0;
                $TicketId = isset($aPost['ticket_id']) ? $aPost['ticket_id'] : 0;

                // EventBookingId
                $EventBookingId = isset($aPost['EventBookingId']) ? $aPost['EventBookingId'] : 0;

                $FromDate = isset($aPost['from_date']) ? strtotime(date("Y-m-d", strtotime($aPost['from_date']))) : 0;
                $ToDate = isset($aPost['to_date']) ? strtotime(date("Y-m-d 23:59:59", strtotime($aPost['to_date']))) : 0;
                $now = strtotime("now");

                $sql = "SELECT *,a.id AS aId,(SELECT ticket_name FROM event_tickets WHERE id=a.ticket_id) AS TicketName FROM attendee_booking_details AS a 
                LEFT JOIN booking_details AS b ON a.booking_details_id = b.id
                LEFT JOIN event_booking AS e ON b.booking_id = e.id
                WHERE b.event_id = :event_id ";
                // ORDER BY a.id DESC";
                if (!empty($EventBookingId)) {
                    $sql .= " AND b.booking_id =" . $EventBookingId;
                }
                // dd($sql);
                if (!empty($TicketId)) {
                    $sql .= " AND a.ticket_id =" . $TicketId;
                }
                if ($user_id != 0) {
                    $sql .= " AND b.user_id =" . $user_id;
                }
                if (!empty($FromDate) && empty($ToDate)) {
                    $sql .= " AND b.booking_date >= " . $FromDate;
                }
                if (empty($FromDate) && !empty($ToDate)) {
                    $sql .= " AND b.booking_date <= " . $ToDate;
                }
                if (!empty($FromDate) && !empty($ToDate)) {
                    $sql .= " AND b.booking_date BETWEEN '.$FromDate.' AND " . $ToDate;
                }
                $sql .= " ORDER BY a.id DESC";
                $AttendeeData = DB::select($sql, array('event_id' => $EventId));
                foreach ($AttendeeData as $key => $value) {
                    $value->booking_date = !empty($value->created_at) ? date("Y-m-d H:i A", ($value->created_at)) : '';
                }

                $ResponseData['AttendeeData'] = (count($AttendeeData) > 0) ? $AttendeeData : [];

                // ALL TICKETS
                $sql = "SELECT * FROM event_tickets WHERE event_id = :event_id AND active=1";
                $TicketData = DB::select($sql, array('event_id' => $EventId));
                $ResponseData['TicketData'] = (count($TicketData) > 0) ? $TicketData : [];

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

    function getBookingDetails(Request $request)
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
                $UserId = $aToken['data']->ID ? $aToken['data']->ID : 0;
                $BookingId = isset($aPost['BookingId']) ? $aPost['BookingId'] : 0;
                $BookingDetailId = isset($aPost['BookingDetailId']) ? $aPost['BookingDetailId'] : 0;

                $sql = "SELECT *,a.id AS attendeeId,b.ticket_discount AS TicketDiscount,(SELECT ticket_name FROM event_tickets WHERE id=a.ticket_id) AS TicketName,
                (SELECT txnid FROM booking_payment_details WHERE id=e.booking_pay_id) AS OrderId
                FROM attendee_booking_details AS a 
                LEFT JOIN booking_details AS b ON a.booking_details_id = b.id
                LEFT JOIN event_booking AS e ON b.booking_id = e.id
                WHERE b.event_id =:event_id AND a.id=:BookingId AND a.booking_details_id=:BookingDetailId";
                $params = array('event_id' => $EventId, 'BookingId' => $BookingId, 'BookingDetailId' => $BookingDetailId);
                $BookingDetails = DB::select($sql, $params);

                foreach ($BookingDetails as $value) {
                    // ticket registration number. generate it using -> (event_id + booking_id + timestamp)
                    // registration id
                    $uniqueId = 0;
                    $uniqueId = $EventId . "-" . $value->attendeeId . "-" . $value->booking_date;
                    $value->unique_ticket_id = $uniqueId;

                    //Booking date
                    $value->booking_start_date = (!empty($value->booking_date)) ? gmdate("d M Y", $value->booking_date) : 0;
                    $value->booking_time = (!empty($value->booking_date)) ? date("h:i A", $value->booking_date) : "";

                    // get extra amounts
                    $attendee_details = json_decode(json_decode($value->attendee_details));
                    // dd($attendee_details);
                    $amount_details = [];
                    $extra_details = [];


                    // Iterate through attendee details to separate the amounts
                    foreach ($attendee_details as $detail) {
                        if ($detail->question_form_type == 'amount') {
                            $amount_details[] = $detail;
                        }
                        if ($detail->question_form_name == 'drink_preferences') {
                            $extra_details[] = $detail;
                        }
                        if ($detail->question_form_name == 'breakfast_preferences') {
                            $extra_details[] = $detail;
                        }
                    }

                    foreach ($amount_details as $key => $value1) {
                        $value1->question_label = ucwords($value1->question_label);
                    }
                    // dd($extra_details);
                    $value->amount_details = $amount_details;
                    $value->extra_details = $extra_details;
                }
                // dd($BookingDetails);
                $ResponseData['BookingDetails'] = $BookingDetails;

                // $TicketDetails = [];

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
