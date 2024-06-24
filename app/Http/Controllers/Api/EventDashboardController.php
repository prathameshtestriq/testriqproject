<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Libraries\Authenticate;
use Illuminate\Support\Facades\DB;
use \stdClass;
use App\Exports\AttendeeDetailsDataExport;
use Excel;
use App\Models\Master;

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
                $Filter = isset($aPost['filter']) ? $aPost['filter'] : "";

                if (!empty($Filter)) {
                    switch ($Filter) {
                        case 'today':
                            $StartDate = strtotime(date('Y-m-d 00:00:00'));
                            $EndDate = strtotime(date('Y-m-d 23:59:59'));
                            break;

                        case 'week':
                            $StartDate = strtotime(date('Y-m-d 00:00:00', strtotime('monday this week')));
                            $EndDate = strtotime(date('Y-m-d 23:59:59', strtotime('saturday this week')));
                            break;

                        case 'month':
                            $StartDate = strtotime(date('Y-m-01'));
                            $EndDate = strtotime(date('Y-m-t'));
                            break;

                        default:
                            break;
                    }
                    // dd($StartDate, $EndDate);
                }

                // -------------------------------------Net Sales && Total Participants
                $sql = "SELECT IFNULL(SUM(b.quantity), 0) AS NetSales 
                    FROM booking_details AS b
                    LEFT JOIN event_booking AS e ON b.booking_id = e.id
                    WHERE b.event_id =:event_id AND e.transaction_status IN (1,3)";
                if ($Filter !== "") {
                    if (isset($StartDate) && isset($EndDate)) {
                        $sql .= ' AND b.booking_date BETWEEN ' . $StartDate . ' AND ' . $EndDate;
                    }
                }
                $TotalBooking = DB::select($sql, array('event_id' => $EventId));
                $NetSales = (count($TotalBooking) > 0) ? $TotalBooking[0]->NetSales : 0;
                $ResponseData['NetSales'] = $NetSales;

                // 79,105,110 DISTINCT(user_id) replace it with id
                // -------------------------------------Registration && Net Earnings
                $sql = "SELECT COUNT(id) AS TotalRegistration,SUM(total_amount) AS TotalAmount FROM event_booking WHERE event_id =:event_id AND transaction_status IN (1,3)";
                if ($Filter !== "") {
                    if (isset($StartDate) && isset($EndDate)) {
                        $sql .= ' AND booking_date BETWEEN ' . $StartDate . ' AND ' . $EndDate;
                    }
                }
                $TotalRegistration = DB::select($sql, array('event_id' => $EventId));
                $ResponseData['TotalRegistration'] = (count($TotalRegistration) > 0) ? $TotalRegistration[0]->TotalRegistration : 0;
                $ResponseData['TotalAmount'] = (count($TotalRegistration) > 0) ? $TotalRegistration[0]->TotalAmount : 0;

                // -------------------------------------Event Capacity
                // Total Tickets
                $sql = "SELECT IFNULL(SUM(total_quantity), 0) AS TotalTickets FROM event_tickets WHERE event_id =:event_id AND is_deleted=0";
                $TotalTickets = DB::select($sql, array('event_id' => $EventId));
                $ResponseData['TotalTickets'] = (count($TotalTickets) > 0) ? $TotalTickets[0]->TotalTickets : 0;


                // Sold Tickets
                $sql = "SELECT IFNULL(SUM(quantity),0) AS TotalBookedTickets FROM booking_details AS bd
                LEFT JOIN event_booking AS eb ON bd.booking_id = eb.id
                WHERE bd.event_id=:event_id AND eb.transaction_status IN (1,3)";
                $TotalBookedTickets = DB::select($sql, array("event_id" => $EventId));
                $ResponseData['TotalBookedTickets'] = (count($TotalBookedTickets) > 0) ? $TotalBookedTickets[0]->TotalBookedTickets : 0;

                // -------------------------------------Conversion Rate
                // Total Registration Users
                $sql = "SELECT COUNT(id) AS TotalRegistrationUsers FROM event_booking WHERE event_id =:event_id";
                $TotalRegistration = DB::select($sql, array('event_id' => $EventId));
                $TotalRegistrationCount = (count($TotalRegistration) > 0) ? $TotalRegistration[0]->TotalRegistrationUsers : 0;

                // Total Registration Users With Success
                $sql = "SELECT COUNT(id) AS TotalRegistrationUsersWithSuccess FROM event_booking WHERE event_id =:event_id AND transaction_status IN (1,3)";
                if ($Filter !== "") {
                    if (isset($StartDate) && isset($EndDate)) {
                        $sql .= ' AND booking_date BETWEEN ' . $StartDate . ' AND ' . $EndDate;
                    }
                }
                $TotalRegistrationUsersWithSuccess = DB::select($sql, array('event_id' => $EventId));
                $TotalRegistrationUsersWithSuccessCount = (count($TotalRegistrationUsersWithSuccess) > 0) ? $TotalRegistrationUsersWithSuccess[0]->TotalRegistrationUsersWithSuccess : 0;

                // Calculate percentage
                $percentage = ($TotalRegistrationUsersWithSuccessCount > 0 && $TotalRegistrationCount > 0) ?
                    round(($TotalRegistrationUsersWithSuccessCount / $TotalRegistrationCount) * 100, 2) : 0;

                $ResponseData['TotalRegistrationCount'] = $TotalRegistrationCount;
                $ResponseData['TotalRegistrationUsersWithSuccess'] = $TotalRegistrationUsersWithSuccessCount;
                $ResponseData['SuccessPercentage'] = $percentage;


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
                    if ($TransactionStatus == 101)
                        $TransactionStatus = 0;
                    if ($TransactionStatus == 1)
                        $TransactionStatus = "1,3";


                    $sql .= " AND eb.transaction_status IN (" . $TransactionStatus . ")";
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

                // EventBookingId
                $TransactionStatus = isset($aPost['TransactionStatus']) ? $aPost['TransactionStatus'] : "";
                $EventBookingId = isset($aPost['EventBookingId']) ? $aPost['EventBookingId'] : 0;
                $ParticipantName = isset($aPost['participant_name']) ? $aPost['participant_name'] : 0;
                $RegistrationID = isset($aPost['reg_id']) ? $aPost['reg_id'] : 0;
                $MobileNumber = isset($aPost['mobile_number']) ? $aPost['mobile_number'] : 0;
                $Email = isset($aPost['email']) ? $aPost['email'] : 0;
                $TicketId = isset($aPost['ticket_id']) ? $aPost['ticket_id'] : 0;
                $FromDate = isset($aPost['from_date']) ? strtotime(date("Y-m-d", strtotime($aPost['from_date']))) : 0;
                $ToDate = isset($aPost['to_date']) ? strtotime(date("Y-m-d 23:59:59", strtotime($aPost['to_date']))) : 0;

                $sql = "SELECT *,a.id AS aId,(SELECT ticket_name FROM event_tickets WHERE id=a.ticket_id) AS TicketName FROM attendee_booking_details AS a 
                LEFT JOIN booking_details AS b ON a.booking_details_id = b.id
                LEFT JOIN event_booking AS e ON b.booking_id = e.id
                WHERE b.event_id = :event_id ";
                // ORDER BY a.id DESC";

                if (!empty($TransactionStatus)) {
                    if ($TransactionStatus == 101)
                        $TransactionStatus = 0;
                    if ($TransactionStatus == 1)
                        $TransactionStatus = "1,3";

                    $sql .= " AND e.transaction_status IN (" . $TransactionStatus . ")";
                }
                if (!empty($EventBookingId)) {
                    $sql .= " AND b.booking_id =" . $EventBookingId;
                }
                if (!empty($ParticipantName)) {
                    $sql .= " AND (a.firstname LIKE '%" . $ParticipantName . "%' OR a.lastname LIKE '%" . $ParticipantName . "%')";
                }
                // if (!empty($ParticipantName)) {
                //     $nameParts = explode(' ', $ParticipantName);
                //     $sql .= " AND (";
                //     foreach ($nameParts as $index => $namePart) {
                //         if ($index > 0) {
                //             $sql .= " OR ";
                //         }
                //         $sql .= "(a.firstname LIKE '%" . $ParticipantName . "%'"." OR a.lastname LIKE '%" . $ParticipantName . "%'".")";
                //     }
                //     $sql .= ")";
                // }
                if (!empty($RegistrationID)) {
                    $sql .= " AND a.registration_id LIKE '%" . $RegistrationID . "%'";
                }

                if (!empty($MobileNumber)) {
                    $sql .= " AND a.mobile_number = " . $MobileNumber;
                }
                if (!empty($Email)) {
                    $sql .= " AND a.email LIKE '%" . $Email . "%'";
                }
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

                //------------- Attendee details excel generate
                if (!empty($AttendeeData)) {
                    $ResponseData['attendee_details_excel'] = EventDashboardController::attendeeNetsalesExcellData($AttendeeData, $EventId);
                } else {
                    $ResponseData['attendee_details_excel'] = '';
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
                    $value->booking_start_date = (!empty($value->booking_date)) ? date("d M Y", $value->booking_date) : 0;
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

    function attendeeNetsalesExcellData($AttendeeData, $EventId)
    {
        //dd($AttendeeData);
        $master = new Master();
        $excel_url = '';
        if (!empty($AttendeeData)) {

            $ExcellDataArray = [];
            $sql = "SELECT id,question_label,question_form_type,question_form_name,(select name from events where id = event_form_question.event_id) as event_name FROM event_form_question WHERE event_id = :event_id AND question_status = 1";
            $EventQuestionData = DB::select($sql, array('event_id' => $EventId));

            $event_name = !empty($EventQuestionData) ? $EventQuestionData[0]->event_name : '';

            $label = '';
            foreach ($AttendeeData as $key => $res1) {
                $attendee_details_array = json_decode($res1->attendee_details, true);

                // dd(json_decode($attendee_details_array));
                foreach (json_decode($attendee_details_array) as $val) {
                    if (isset($val->question_label)) {

                        $aTemp = new stdClass;
                        $aTemp->question_label = $val->question_label;

                        if (!empty($val->question_form_option)) {
                            $question_form_option = json_decode($val->question_form_option, true);
                            // dd($question_form_option);
                            foreach ($question_form_option as $option) {
                                //dd($val->ActualValue);
                                if ($option['id'] === (int) $val->ActualValue) {
                                    $label = $option['label'];
                                    break;
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
                                $aTemp->answer_value = $val->ActualValue;
                            }

                        }
                        $ExcellDataArray[$key][] = $aTemp;
                    }
                }
            }
            // dd($ExcellDataArray);

            $url = env('APP_URL') . '/public/';
            //dd($url);

            // $filename = "attendee_sheet_".time();
            $filename = "attendee_" . $event_name . '_' . time();
            $path = 'attendee_details_excell/';
            $data = Excel::store(new AttendeeDetailsDataExport($ExcellDataArray, $EventQuestionData), $path . '/' . $filename . '.xlsx', 'excel_uploads');
            $excel_url = url($path) . "/" . $filename . ".xlsx";

        }
        return $excel_url;
    }

    function getPaymentLog(Request $request)
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

                $sql = "SELECT p.id AS paymentId,p.txnid,p.amount,p.payment_status,p.created_datetime,u.id AS userId,u.firstname,u.lastname,u.email,u.mobile,(SELECT mihpayid FROM booking_payment_log WHERE p.id=booking_det_id) AS payId
                        FROM booking_payment_details AS p 
                        LEFT JOIN users AS u ON u.id=p.created_by
                        WHERE p.event_id=:event_id ORDER BY p.id DESC";
                $PaymentData = DB::select($sql, array('event_id' => $EventId));

                foreach ($PaymentData as $key => $value) {
                    $value->created_datetime = !empty($value->created_datetime) ? date("Y-m-d H:i A", ($value->created_datetime)) : '';
                }
                $ResponseData['PaymentData'] = (count($PaymentData) > 0) ? $PaymentData : [];

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
