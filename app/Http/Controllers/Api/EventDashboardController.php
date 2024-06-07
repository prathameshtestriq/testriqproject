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
                $sql = "SELECT SUM(b.quantity) AS NetSales FROM booking_details AS b
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

                // dd($TotalRegistration);

                // Revenue
                // $sql = "SELECT SUM(total_amount) AS TotalRevenue FROM event_booking WHERE event_id =
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

                $FromDate = isset($aPost['from_date']) ? strtotime(date("Y-m-d", strtotime($aPost['from_date']))) : 0;
                $ToDate = isset($aPost['to_date']) ? strtotime(date("Y-m-d 23:59:59", strtotime($aPost['to_date']))) : 0;

                $sql = "SELECT 
                        eb.user_id,
                        eb.booking_date,
                        SUM(eb.total_amount) AS TotalAmount,
                        bd.TotalTickets,
                        u.id,
                        u.firstname,
                        u.lastname
                    FROM event_booking AS eb 
                    LEFT JOIN users AS u ON u.id = eb.user_id
                    LEFT JOIN (
                        SELECT 
                            b_d.user_id, 
                            b_d.event_id, 
                            SUM(b_d.quantity) AS TotalTickets
                        FROM booking_details AS b_d
                        LEFT JOIN event_booking AS e_b ON e_b.id=b_d.booking_id
                        LEFT JOIN users AS uu ON uu.id = e_b.user_id
                        WHERE e_b.transaction_status = 1";
                // if ($SearchUser) {
                //     $sql .= " AND uu.firstname LIKE '%" . $SearchUser . "%' OR uu.lastname LIKE '%" . $SearchUser . "%'";
                // }
                $sql .= " GROUP BY b_d.user_id, b_d.event_id
                    ) AS bd ON bd.user_id = eb.user_id AND bd.event_id = eb.event_id
                    WHERE eb.event_id = :event_id 
                    AND eb.transaction_status = 1";

                if ($SearchUser) {
                    $sql .= " AND u.firstname LIKE '%" . $SearchUser . "%' OR u.lastname LIKE '%" . $SearchUser . "%'";
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
                $sql .= " GROUP BY eb.user_id";
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

                $FromDate = isset($aPost['from_date']) ? strtotime(date("Y-m-d", strtotime($aPost['from_date']))) : 0;
                $ToDate = isset($aPost['to_date']) ? strtotime(date("Y-m-d 23:59:59", strtotime($aPost['to_date']))) : 0;
                $now = strtotime("now");

                $sql = "SELECT *,(SELECT ticket_name FROM event_tickets WHERE id=a.ticket_id) AS TicketName FROM attendee_booking_details AS a 
                LEFT JOIN booking_details AS b ON a.booking_details_id = b.id
                LEFT JOIN event_booking AS e ON b.booking_id = e.id
                WHERE b.event_id = :event_id AND e.transaction_status = 1";
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
}
