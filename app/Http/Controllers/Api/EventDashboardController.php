<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Libraries\Authenticate;
use Illuminate\Support\Facades\DB;
use \stdClass;
use App\Exports\AttendeeDetailsDataExport;
use App\Exports\RemittanceDetailsDataExport;
use Excel;
use App\Models\Master;
use DateTime;


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
                $Ticket = isset($aPost['Ticket']) ? $aPost['Ticket'] : 0;
                $FromDate = isset($aPost['from_date']) ? strtotime($aPost['from_date']) : 0;
                $ToDate = isset($aPost['to_date']) ? strtotime($aPost['to_date']) : 0;


                if (!empty($Filter)) {
                    switch ($Filter) {
                        case 'today':
                            $StartDate = strtotime(date('Y-m-d 00:00:00'));
                            $EndDate = strtotime(date('Y-m-d 23:59:59'));
                            break;

                        case 'week':
                            $StartDate = strtotime(date('Y-m-d 00:00:00', strtotime('monday this week')));
                            $EndDate = strtotime(date('Y-m-d 23:59:59', strtotime('sunday this week')));
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
                // $SQL1 = "SELECT IFNULL(SUM(b.quantity), 0) AS NetSales
                //     FROM booking_details AS b
                //     LEFT JOIN event_booking AS e ON b.booking_id = e.id
                //     WHERE b.event_id =:event_id AND e.transaction_status IN (1,3)";
                // if ($Filter !== "") {
                //     if (isset($StartDate) && isset($EndDate)) {
                //         $SQL1 .= ' AND b.booking_date BETWEEN ' . $StartDate . ' AND ' . $EndDate;
                //     }
                // }
                // if (!empty($Ticket)) {
                //     $SQL1 .= ' AND b.ticket_id =' . $Ticket;
                // }
                // if (!empty($FromDate) && !empty($ToDate)) {
                //     $SQL1 .= ' AND b.booking_date BETWEEN ' . $FromDate . ' AND ' . $ToDate;
                // }
                // // dd($SQL1);
                // $TotalBooking = DB::select($SQL1, array('event_id' => $EventId));
                // $NetSales = (count($TotalBooking) > 0) ? $TotalBooking[0]->NetSales : 0;
               
                // $ResponseData['NetSales'] = $NetSales;
               
                //---------------- Total Participant -----------

                $SQL6 = "SELECT IFNULL(COUNT(e.id), 0) AS TotalParticipant  
                FROM attendee_booking_details AS a 
                LEFT JOIN booking_details AS b ON b.id=a.booking_details_id
                LEFT JOIN event_booking AS e ON b.booking_id = e.id
                WHERE b.event_id =:event_id AND e.transaction_status IN (1,3)";
                
                if ($Filter !== "") {
                    if (isset($StartDate) && isset($EndDate)) {
                        $SQL6 .= " AND b.booking_date BETWEEN " . $StartDate . " AND " . $EndDate;
                    }
                }
                if (!empty($Ticket)) {
                    $SQL6 .= ' AND b.ticket_id =' . $Ticket;
                }
                if (!empty($FromDate) && !empty($ToDate)) {
                    $SQL6 .= ' AND b.booking_date BETWEEN ' . $FromDate . ' AND ' . $ToDate;
                }
                $TotalParticipantData = DB::select($SQL6, array('event_id' => $EventId));
                $ResponseData['TotalParticipant'] = !empty($TotalParticipantData) ? $TotalParticipantData[0]->TotalParticipant : 0;
                $ResponseData['NetSales'] = !empty($TotalParticipantData) ? $TotalParticipantData[0]->TotalParticipant : 0;

                // 79,105,110 DISTINCT(user_id) replace it with id
                // -------------------------------------Registration && Net Earnings
                // $SQL2 = "SELECT COUNT(e.id) AS TotalRegistration,SUM(e.total_amount) AS TotalAmount FROM event_booking AS e 
                // LEFT JOIN booking_details AS b ON b.booking_id = e.id
                // WHERE e.event_id =:event_id AND e.transaction_status IN (1,3)";

                $SQL2 = "SELECT DISTINCT(e.id) AS TotalRegistration ,e.total_amount AS TotalAmount,e.transaction_status FROM booking_details AS b LEFT JOIN event_booking AS e ON b.booking_id = e.id WHERE b.event_id =:event_id AND e.transaction_status IN (1,3)";
              
                if ($Filter !== "") {
                    if (isset($StartDate) && isset($EndDate)) {
                        $SQL2 .= ' AND b.booking_date BETWEEN ' . $StartDate . ' AND ' . $EndDate;
                    }
                }
                if (!empty($Ticket)) {
                    $SQL2 .= ' AND b.ticket_id =' . $Ticket;
                }
                if (!empty($FromDate) && !empty($ToDate)) {
                    $SQL2 .= ' AND b.booking_date BETWEEN ' . $FromDate . ' AND ' . $ToDate;
                }
                $TotalRegistration = DB::select($SQL2, array('event_id' => $EventId));
                
                $total_amount = 0;
                if(!empty($TotalRegistration)){
                    foreach($TotalRegistration as $res){
                        $total_amount += !empty($res->TotalAmount) ? $res->TotalAmount : 0;
                    }
                }
                $ResponseData['TotalRegistration'] = (count($TotalRegistration) > 0) ? count($TotalRegistration) : 0;
                $ResponseData['TotalAmount'] = !empty($total_amount) ? $total_amount : 0;

                // -------------------------------------Event Capacity
                // Total Tickets
                $SQL3 = "SELECT IFNULL(SUM(total_quantity), 0) AS TotalTickets FROM event_tickets WHERE event_id =:event_id AND is_deleted=0";
                $TotalTickets = DB::select($SQL3, array('event_id' => $EventId));
                $ResponseData['TotalTickets'] = (count($TotalTickets) > 0) ? $TotalTickets[0]->TotalTickets : 0;


                // Sold Tickets
                $SQL4 = "SELECT IFNULL(SUM(quantity),0) AS TotalBookedTickets FROM booking_details AS bd
                LEFT JOIN event_booking AS eb ON bd.booking_id = eb.id
                WHERE bd.event_id=:event_id AND eb.transaction_status IN (1,3)";
                $TotalBookedTickets = DB::select($SQL4, array("event_id" => $EventId));
                $ResponseData['TotalBookedTickets'] = (count($TotalBookedTickets) > 0) ? $TotalBookedTickets[0]->TotalBookedTickets : 0;

                // -------------------------------------Conversion Rate
                // Total Registration Users
                $SQL5 = "SELECT COUNT(id) AS TotalRegistrationUsers FROM event_booking WHERE event_id =:event_id";
                $TotalRegistration = DB::select($SQL5, array('event_id' => $EventId));
                $TotalRegistrationCount = (count($TotalRegistration) > 0) ? $TotalRegistration[0]->TotalRegistrationUsers : 0;

                // Total Registration Users With Success
                $SQL6 = "SELECT COUNT(e.id) AS TotalRegistrationUsersWithSuccess FROM event_booking AS e 
                LEFT JOIN booking_details AS b ON b.booking_id = e.id
                WHERE e.event_id =:event_id AND e.transaction_status IN (1,3)";
                if ($Filter !== "") {
                    if (isset($StartDate) && isset($EndDate)) {
                        $SQL6 .= ' AND b.booking_date BETWEEN ' . $StartDate . ' AND ' . $EndDate;
                    }
                }
                if (!empty($Ticket)) {
                    $SQL6 .= ' AND b.ticket_id =' . $Ticket;
                }
                if (!empty($FromDate) && !empty($ToDate)) {
                    $SQL6 .= ' AND b.booking_date BETWEEN ' . $FromDate . ' AND ' . $ToDate;
                }
                $TotalRegistrationUsersWithSuccess = DB::select($SQL6, array('event_id' => $EventId));
                $TotalRegistrationUsersWithSuccessCount = (count($TotalRegistrationUsersWithSuccess) > 0) ? $TotalRegistrationUsersWithSuccess[0]->TotalRegistrationUsersWithSuccess : 0;

                // Calculate percentage
                $percentage = ($TotalRegistrationUsersWithSuccessCount > 0 && $TotalRegistrationCount > 0) ?
                    round(($TotalRegistrationUsersWithSuccessCount / $TotalRegistrationCount) * 100, 2) : 0;

                $ResponseData['TotalRegistrationCount'] = $TotalRegistrationCount;
                $ResponseData['TotalRegistrationUsersWithSuccess'] = $TotalRegistrationUsersWithSuccessCount;
                $ResponseData['SuccessPercentage'] = $percentage;

                // ALL TICKETS
                $SQL7 = "SELECT * FROM event_tickets WHERE event_id = :event_id AND active=1";
                $TicketData = DB::select($SQL7, array('event_id' => $EventId));
                $ResponseData['TicketData'] = (count($TicketData) > 0) ? $TicketData : [];

                // PAGE VIEWS
                $SQL8 = "SELECT COUNT(id) AS TotalPageViews FROM page_views WHERE event_id =:event_id";
                if ($Filter !== "") {
                    if (isset($StartDate) && isset($EndDate)) {
                        $SQL8 .= ' AND last_updated_datetime BETWEEN ' . $StartDate . ' AND ' . $EndDate;
                    }
                }
                // if (!empty($Ticket)) {
                //     $SQL8 .= ' AND b.ticket_id =' . $Ticket;
                // }
                if (!empty($FromDate) && !empty($ToDate)) {
                    $SQL8 .= ' AND last_updated_datetime BETWEEN ' . $FromDate . ' AND ' . $ToDate;
                }
                $TotalPageViews = DB::select($SQL8, array('event_id' => $EventId));
                $ResponseData['TotalPageViews'] = (count($TotalPageViews) > 0) ? $TotalPageViews[0]->TotalPageViews : 0;

                // New added - Remittance Amount
                $Sql9 = "SELECT gross_amount FROM remittance_management WHERE event_id = :event_id AND active = 1";
                $RemittanceData = DB::select($Sql9, array('event_id' => $EventId));
                $ResponseData['RemittanceAmount'] = !empty($RemittanceData) ? $RemittanceData[0]->gross_amount : 0;
                // dd($ResponseData['RemittanceAmount']);

                //------------

                $sql = "SELECT e.event_id,e.cart_details FROM attendee_booking_details AS a 
                LEFT JOIN booking_details AS b ON a.booking_details_id = b.id
                LEFT JOIN event_booking AS e ON b.booking_id = e.id";
                
                $sql .= " WHERE b.event_id = :event_id AND e.transaction_status IN(1,3)";
                
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
                // dd($sql);
                $AttendeeData = DB::select($sql, array('event_id' => $EventId));
                // dd($AttendeeData);
                
                $Applied_Coupon_Amount = $Organiser_amount = $Payment_Gateway_GST = $Payment_gateway_charges = $total_payment_gateway = $Platform_fee = $Platform_Fee_GST = $Convenience_fee = $Convenience_Fee_GST = 0;
                if(!empty($AttendeeData)){
                    foreach($AttendeeData as $res){
                        $card_details_array = json_decode($res->cart_details);

                        // Applied Coupon Amount
                        $Applied_Coupon_Amount = isset($card_details_array[0]->appliedCouponAmount) && !empty($card_details_array[0]->appliedCouponAmount) ? ($card_details_array[0]->appliedCouponAmount * $card_details_array[0]->count)  : '0.00';  
                        
                        if(isset($card_details_array[0]->appliedCouponAmount) && !empty($card_details_array[0]->appliedCouponAmount)){
                            $Organiser_amount += isset($card_details_array[0]->to_organiser) && !empty($card_details_array[0]->to_organiser) ? ($card_details_array[0]->to_organiser - $card_details_array[0]->appliedCouponAmount) : 0;
                        }else{
                            $Organiser_amount += isset($card_details_array[0]->to_organiser) && !empty($card_details_array[0]->to_organiser) ? $card_details_array[0]->to_organiser : 0;
                        }

                        $Payment_Gateway_GST += isset($card_details_array[0]->Payment_Gateway_GST_18) && !empty($card_details_array[0]->Payment_Gateway_GST_18) ? ($card_details_array[0]->Payment_Gateway_GST_18 * $card_details_array[0]->count)  : '0.00';
                        $Payment_gateway_charges += isset($card_details_array[0]->Payment_Gateway_Charges) && !empty($card_details_array[0]->Payment_Gateway_Charges) ? ($card_details_array[0]->Payment_Gateway_Charges * $card_details_array[0]->count)  : '0.00';

                       
                        $Platform_fee += isset($card_details_array[0]->Platform_Fee) && !empty($card_details_array[0]->Platform_Fee) ? ($card_details_array[0]->Platform_Fee * $card_details_array[0]->count)  : '0.00';
                        $Platform_Fee_GST += isset($card_details_array[0]->Platform_Fee_GST_18) && !empty($card_details_array[0]->Platform_Fee_GST_18) ? ($card_details_array[0]->Platform_Fee_GST_18 * $card_details_array[0]->count)  : '0.00';

                        $Convenience_fee += isset($card_details_array[0]->Convenience_Fee) && !empty($card_details_array[0]->Convenience_Fee) ? 
                                        ($card_details_array[0]->Convenience_Fee * $card_details_array[0]->count)  : '0.00';
                        $Convenience_Fee_GST += isset($card_details_array[0]->Convenience_Fee_GST_18) && !empty($card_details_array[0]->Convenience_Fee_GST_18) ? ($card_details_array[0]->Convenience_Fee_GST_18 * $card_details_array[0]->count)  : '0.00';
                    }
                }

                $total_payment_gateway = ($Payment_Gateway_GST + $Payment_gateway_charges);
                $total_convenience_fee = ($Platform_fee + $Platform_Fee_GST + $Convenience_fee + $Convenience_Fee_GST);

                // dd($Organiser_amount);
                $ResponseData['OrganiserAmount'] = !empty($Organiser_amount) ? number_format($Organiser_amount,2) : 0;
                $ResponseData['TotalPaymentGateway'] = !empty($total_payment_gateway) ? number_format($total_payment_gateway,2) : 0;
                $ResponseData['TotalConvenience'] = !empty($total_convenience_fee) ? number_format($total_convenience_fee,2) : 0;
                

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

                $TransactionID = isset($aPost['TransactionID']) ? $aPost['TransactionID'] : '';

                $Page = (isset($aPost['page'])) ? $aPost['page'] : 1;
                $Limit = (isset($aPost['limit'])) ? $aPost['limit'] : 10;

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
                         u.mobile,
                         eb.booking_pay_id,(select txnid from booking_payment_details where id = eb.booking_pay_id) as transaction_id
                     FROM event_booking AS eb 
                     LEFT JOIN booking_details AS bd ON bd.booking_id = eb.id
                     LEFT JOIN users AS u ON u.id = eb.user_id
                     WHERE eb.event_id=:event_id ";
               
                if (!empty($TransactionStatus)) {
                    if ($TransactionStatus == 101)
                        $TransactionStatus = 0;
                    if ($TransactionStatus == 1)
                        $TransactionStatus = 1;
                    if ($TransactionStatus == 3)
                        $TransactionStatus = 3;
                    if ($TransactionStatus == 102)
                        $TransactionStatus = "1,3";

                    $sql .= " AND eb.transaction_status IN (" . $TransactionStatus . ")";
                }
                if ($SearchUser) {
                    $sql .= " AND (u.firstname LIKE '%" . $SearchUser . "%' OR u.lastname LIKE '%" . $SearchUser . "%')";
                }
                if (!empty($FromDate)) {
                    $sql .= " AND bd.booking_date >= " . $FromDate;
                }
                if (!empty($ToDate)) {
                    $sql .= " AND bd.booking_date <= " . $ToDate;
                }

                if(!empty($TransactionID)){
                    $sql .= " AND eb.booking_pay_id = (select id from booking_payment_details where txnid LIKE '%" . $TransactionID . "%') "; 
                }

                // if (!empty($FromDate) && !empty($ToDate)) {
                //     $sql .= " AND eb.booking_date BETWEEN '.$FromDate.' AND " . $ToDate;
                // }
                $sql .= " GROUP BY bd.booking_id";
                $sql .= " ORDER BY eb.id DESC";
                // dd($sql);

                $TotalCount = DB::select($sql, array('event_id' => $EventId));

                $Offset = ($Page * $Limit) - $Limit;
                if($Limit > 0) {
                   $sql .= " limit ".$Offset.",".$Limit." ";
                }

                $UserData = DB::select($sql, array('event_id' => $EventId));

                foreach ($UserData as $key => $value) {
                    $value->booking_date = !empty($value->booking_date) ? date("d-m-Y H:i A", ($value->booking_date)) : '';
                }
                $ResponseData['UserData'] = (count($UserData) > 0) ? $UserData : [];
                $ResponseData['TotalRecord'] = !empty($TotalCount) ? count($TotalCount) : 0;

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
        // dd($request);
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
                $couponUsedFlag = isset($aPost['coupon_used_flag']) ? $aPost['coupon_used_flag'] : 0;
                $TransactionID = isset($aPost['TransactionID']) ? $aPost['TransactionID'] : '';

                $Page = (isset($aPost['page'])) ? $aPost['page'] : 1;
                $Limit = (isset($aPost['limit'])) ? $aPost['limit'] : 30;

                $sql = "SELECT *,a.id AS aId,e.total_amount,(SELECT ticket_name FROM event_tickets WHERE id=a.ticket_id) AS TicketName,(SELECT et.ticket_name FROM event_tickets et WHERE et.id = a.ticket_id) AS category_name, (SELECT ticket_status FROM event_tickets WHERE id=a.ticket_id) AS ticket_status,e.booking_pay_id,(select txnid from booking_payment_details where id = e.booking_pay_id) as transaction_id FROM attendee_booking_details AS a 
                LEFT JOIN booking_details AS b ON a.booking_details_id = b.id
                LEFT JOIN event_booking AS e ON b.booking_id = e.id";
                
                //------- applied coupon code handle condition
                if (isset($couponUsedFlag) && !empty($couponUsedFlag)) {
                    $sql .= " LEFT JOIN applied_coupons AS ac ON ac.event_id = b.event_id AND ac.booking_detail_id = a.booking_details_id";
                }

                $sql .= " WHERE b.event_id = :event_id ";
                // ORDER BY a.id DESC";

                if (!empty($TransactionStatus)) {
                    if ($TransactionStatus == 101)
                        $TransactionStatus = 0;
                    if ($TransactionStatus == 1)
                        $TransactionStatus = 1;
                    if ($TransactionStatus == 3)
                        $TransactionStatus = 3;
                    if ($TransactionStatus == 102)
                        $TransactionStatus = "1,3";

                    $sql .= " AND e.transaction_status IN (" . $TransactionStatus . ")";
                }
                if (!empty($EventBookingId)) {
                    $sql .= " AND b.booking_id =" . $EventBookingId;
                }
                if (!empty($ParticipantName)) {
                    $sql .= " AND (a.firstname LIKE '%" . $ParticipantName . "%' OR a.lastname LIKE '%" . $ParticipantName . "%')";
                }
            
                if (!empty($RegistrationID)) {
                    $sql .= " AND a.registration_id LIKE '%" . $RegistrationID . "%'";
                }

                if (!empty($MobileNumber)) {
                    $sql .= " AND a.mobile = " . $MobileNumber;
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
                if (!empty($FromDate)) {
                    $sql .= " AND b.booking_date >= " . $FromDate;
                }
                if (!empty($ToDate)) {
                    $sql .= " AND b.booking_date <= " . $ToDate;
                }
                // if (!empty($FromDate) && !empty($ToDate)) {
                //     $sql .= " AND b.booking_date BETWEEN '.$FromDate.' AND " . $ToDate;
                // }
                // dd($TransactionID);
                if(!empty($TransactionID)){
                    $sql .= " AND e.booking_pay_id = (select id from booking_payment_details where txnid LIKE '%" . $TransactionID . "%') "; 
                }
                
                //------- applied coupon code handle condition
                if (isset($couponUsedFlag) && !empty($couponUsedFlag)) {
                    $sql .= " AND ac.coupon_id = ".$couponUsedFlag;
                }

                $sql .= " ORDER BY a.id DESC";
                // dd($sql);
                $TotalCount = DB::select($sql, array('event_id' => $EventId));

                $Offset = ($Page * $Limit) - $Limit;
                if($Limit > 0) {
                   $sql .= " limit ".$Offset.",".$Limit." ";
                }

                $AttendeeData = DB::select($sql, array('event_id' => $EventId));
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
                }

                // dd($AttendeeData);

                $ResponseData['AttendeeData'] = (count($AttendeeData) > 0) ? $AttendeeData : [];
                $ResponseData['TotalRecord'] = !empty($TotalCount) ? count($TotalCount) : 0;

                // ALL TICKETS
                $sql = "SELECT * FROM event_tickets WHERE event_id = :event_id AND active=1";
                $TicketData = DB::select($sql, array('event_id' => $EventId));
                $ResponseData['TicketData'] = (count($TicketData) > 0) ? $TicketData : [];
                // dd($ResponseData['AttendeeData']);

               
                //------------- Attendee details excel generate
                if (!empty($AttendeeData)) {
                   // $ResponseData['attendee_details_excel'] = EventDashboardController::attendeeNetsalesExcellData($AttendeeData, $EventId);
                   // $ResponseData['remittance_details_excel'] = EventDashboardController::remittanceDetailsExcellData($AttendeeData, $EventId);

                } else {
                    $ResponseData['attendee_details_excel'] = '';
                    $ResponseData['remittance_details_excel'] = '';
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

    public function attendeeNetsalesExcellData(Request $request)
    {
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $empty = false;
        $field = '';
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        $master = new Master();
        $excel_url = '';
       
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
                $couponUsedFlag = isset($aPost['coupon_used_flag']) ? $aPost['coupon_used_flag'] : 0;
                
                $Command = isset($aPost['command']) ? $aPost['command'] : '';
                $TransactionID = isset($aPost['TransactionID']) ? $aPost['TransactionID'] : '';

                $sql = "SELECT *,a.id AS aId,e.total_amount,(SELECT ticket_name FROM event_tickets WHERE id=a.ticket_id) AS TicketName,(SELECT et.ticket_name FROM event_tickets et WHERE et.id = a.ticket_id) AS category_name, (SELECT ticket_status FROM event_tickets WHERE id=a.ticket_id) AS ticket_status FROM attendee_booking_details AS a 
                LEFT JOIN booking_details AS b ON a.booking_details_id = b.id
                LEFT JOIN event_booking AS e ON b.booking_id = e.id";
                
                //------- applied coupon code handle condition
                if (isset($couponUsedFlag) && !empty($couponUsedFlag)) {
                    $sql .= " LEFT JOIN applied_coupons AS ac ON ac.event_id = b.event_id AND ac.booking_detail_id = a.booking_details_id";
                }

                $sql .= " WHERE b.event_id = :event_id ";
                // ORDER BY a.id DESC";

                if (!empty($TransactionStatus)) {
                    if ($TransactionStatus == 101)
                        $TransactionStatus = 0;
                    if ($TransactionStatus == 1)
                        $TransactionStatus = 1;
                    if ($TransactionStatus == 3)
                        $TransactionStatus = 3;
                    if ($TransactionStatus == 102)
                        $TransactionStatus = "1,3";

                    $sql .= " AND e.transaction_status IN (" . $TransactionStatus . ")";
                }
                if (!empty($EventBookingId)) {
                    $sql .= " AND b.booking_id =" . $EventBookingId;
                }
                if (!empty($ParticipantName)) {
                    $sql .= " AND (a.firstname LIKE '%" . $ParticipantName . "%' OR a.lastname LIKE '%" . $ParticipantName . "%')";
                }
            
                if (!empty($RegistrationID)) {
                    $sql .= " AND a.registration_id LIKE '%" . $RegistrationID . "%'";
                }

                if (!empty($MobileNumber)) {
                    $sql .= " AND a.mobile = " . $MobileNumber;
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
                
                //------- applied coupon code handle condition
                if (isset($couponUsedFlag) && !empty($couponUsedFlag)) {
                    $sql .= " AND ac.coupon_id = ".$couponUsedFlag;
                }

                if(!empty($TransactionID)){
                    $sql .= " AND e.booking_pay_id = (select id from booking_payment_details where txnid LIKE '%".$TransactionID."%') "; 
                }

                $sql .= " ORDER BY a.id DESC";
                // dd($sql);
                $AttendeeData = DB::select($sql, array('event_id' => $EventId));
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
                }

                // dd($AttendeeData);
                // $ResponseData['AttendeeData'] = (count($AttendeeData) > 0) ? $AttendeeData : [];

                //------------- Attendee details excel generate
                if (!empty($AttendeeData)) {

                    if($Command == 'revenue'){
                        $ResponseData['remittance_details_excel'] = EventDashboardController::remittanceDetailsExcellData($AttendeeData, $EventId);
                    }else if($Command == 'attendee'){

                        $ExcellDataArray = [];
                        $sql = "SELECT id,question_label,question_form_type,question_form_name,(select name from events where id = event_form_question.event_id) as event_name FROM event_form_question WHERE event_id = :event_id AND question_status = 1 order by sort_order asc";
                        $EventQuestionData = DB::select($sql, array('event_id' => $EventId));
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
                            array("id" => 101198, "question_label" => "Race Category", "question_form_type" => "text", "ActualValue" => "")
                        );

                        //dd(json_encode($new_array));
                        $ageCategory_array  = array( array("id" => 101199, "question_label" => "Age Category", "question_form_type" => "age_category", "ActualValue" => ""));
                        $utmCapning_array  = array( array("id" => 101186, "question_label" => "UTM Campaign", "question_form_type" => "text", "ActualValue" => ""));
                       
                        $main_array = array_merge($card_array, $EventQuestionData);
                       
                        $event_name = !empty($EventQuestionData) ? $EventQuestionData[0]->event_name : '';
                        // dd($AttendeeData);
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
                            $sql = "SELECT txnid,payment_mode,payment_status,created_datetime,(select mihpayid from booking_payment_log where booking_payment_details.id = booking_det_id) as mihpayid FROM booking_payment_details WHERE id =:booking_pay_id ";
                            $paymentDetails = DB::select($sql, array('booking_pay_id' => $res1->booking_pay_id));
                            //dd($paymentDetails);
                            $tran_id = !empty($paymentDetails) ? $paymentDetails[0]->txnid : '';
                            $payment_mode = !empty($paymentDetails) ? $paymentDetails[0]->payment_mode : '';
                            $payment_status = !empty($paymentDetails) ? $paymentDetails[0]->payment_status : '';
                            $mihpayid = !empty($paymentDetails) ? $paymentDetails[0]->mihpayid : '';
                            $booking_datetime = !empty($paymentDetails) ? date('d-m-Y h:i:s A', $paymentDetails[0]->created_datetime) : '';

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
                                                            $label = $option['label'];
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
                                                    $aTemp->answer_value = date('d-m-Y',strtotime($val->ActualValue));
                                                }else{
                                                    $aTemp->answer_value = htmlspecialchars($val->ActualValue);
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
                                        $aTemp->answer_value = !empty($res1->TicketName) ? $res1->TicketName : '';
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

                        $url = env('APP_URL') . '/public/';
                        //dd($url);
                        
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
                   
                        $header_data_array = json_decode(json_encode($main_array));

                        $filename = "attendee_" . $event_name . '_' . time();
                        $path = 'attendee_details_excell/' . date('Ymd') . '/';
                        $data = Excel::store(new AttendeeDetailsDataExport($ExcellDataArray, $header_data_array), $path . '/' . $filename . '.xlsx', 'excel_uploads');
                        $ResponseData['attendee_details_excel'] = url($path) . "/" . $filename . ".xlsx";
                    }
                } else {
                    $ResponseData['attendee_details_excel'] = '';
                    $ResponseData['remittance_details_excel'] = '';
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

    function remittanceDetailsExcellData($AttendeeData, $EventId)
    {
         // dd($AttendeeData);
        $excel_url = '';
        $AttendeeDataArray = [];

        if (!empty($AttendeeData)) {
            foreach ($AttendeeData as $key => $res) {

                $sql = "SELECT txnid,payment_status,(select mihpayid from booking_payment_log where booking_payment_details.id = booking_det_id) as mihpayid FROM booking_payment_details WHERE id =:booking_pay_id ";
                $paymentDetails = DB::select($sql, array('booking_pay_id' => $res->booking_pay_id));
                //dd($paymentDetails);
                // $tran_id = !empty($paymentDetails) ? $paymentDetails[0]->txnid : '';
                $payment_status = !empty($paymentDetails) ? $paymentDetails[0]->payment_status : '';
                $mihpayid = !empty($paymentDetails) ? $paymentDetails[0]->mihpayid : '';
                $txnid = !empty($paymentDetails) ? $paymentDetails[0]->txnid : '';

                $card_details_array = json_decode($res->cart_details);
                // dd($card_details_array);

                $aTemp = new stdClass;
                $aTemp->firstname = $res->firstname;
                $aTemp->lastname = $res->lastname;
                $aTemp->email = $res->email;
                $aTemp->registration_id = $res->registration_id;
                $aTemp->booking_date = $res->booking_date;
                $aTemp->payu_id = $mihpayid;
                $aTemp->payment_status = $payment_status;
                $aTemp->transaction_id = $txnid;

                $aTemp->mobile = $res->mobile;
                $aTemp->category_name = $res->category_name; 
                $ExcPriceTaxesStatus = isset($card_details_array[0]->ExcPriceTaxesStatus) && !empty($card_details_array[0]->ExcPriceTaxesStatus) ? $card_details_array[0]->ExcPriceTaxesStatus : 0;

                if($ExcPriceTaxesStatus == 1){
                    $aTemp->taxes_status = 'Inclusive';
                }else if($ExcPriceTaxesStatus == 2){
                    $aTemp->taxes_status = 'Exclusive';
                }else{
                    $aTemp->taxes_status = '';
                }

                $WhoPayYtcrFee = isset($card_details_array[0]->player_of_fee) && !empty($card_details_array[0]->player_of_fee) ? $card_details_array[0]->player_of_fee : 0;
                $WhoPayPaymentGatewayFee = isset($card_details_array[0]->player_of_gateway_fee) && !empty($card_details_array[0]->player_of_gateway_fee) ? $card_details_array[0]->player_of_gateway_fee : 0;

                if($WhoPayYtcrFee == 1 && $res->ticket_status !== 2){
                    $aTemp->Pass_Bare = 'Organiser';
                }else if($WhoPayYtcrFee == 2 && $res->ticket_status !== 2){
                    $aTemp->Pass_Bare = 'Participant';
                }else{
                   $aTemp->Pass_Bare = ''; 
                }

                if($WhoPayPaymentGatewayFee == 2 && $res->ticket_status !== 2){
                    $aTemp->Pg_Bare = 'Participant';
                }else if($WhoPayPaymentGatewayFee == 1 && $res->ticket_status !== 2){
                    $aTemp->Pg_Bare = 'Organiser';
                }else{
                   $aTemp->Pg_Bare = ''; 
                }

                if($res->ticket_status == 1){
                   $aTemp->category_type = 'Paid';
                }else if($res->ticket_status == 2){
                   $aTemp->category_type = 'Free';
                }else{
                   $aTemp->category_type = '';
                }

                  
                    $aTemp->Ticket_count = isset($card_details_array[0]->count) && !empty($card_details_array[0]->count) ? $card_details_array[0]->count : 0;
                    $aTemp->Single_ticket_price = isset($card_details_array[0]->Main_Price) && !empty($card_details_array[0]->Main_Price) ? 
                    ($card_details_array[0]->Main_Price * $card_details_array[0]->count) : '0.00';
                    
                    $aTemp->Ticket_price = isset($card_details_array[0]->ticket_price) && !empty($card_details_array[0]->ticket_price) ? $card_details_array[0]->ticket_price : '0.00';
                    // $aTemp->Ticket_price = isset($res->ticket_price) && !empty($res->ticket_price) ? $res->ticket_price : '0.00'; 
                   
                    $aTemp->Convenience_fee = isset($card_details_array[0]->Convenience_Fee) && !empty($card_details_array[0]->Convenience_Fee) ? 
                    ($card_details_array[0]->Convenience_Fee * $card_details_array[0]->count)  : '0.00';

                    $aTemp->Platform_fee = isset($card_details_array[0]->Platform_Fee) && !empty($card_details_array[0]->Platform_Fee) ? 
                    ($card_details_array[0]->Platform_Fee * $card_details_array[0]->count)  : '0.00';

                    $aTemp->Payment_gateway_charges = isset($card_details_array[0]->Payment_Gateway_Charges) && !empty($card_details_array[0]->Payment_Gateway_Charges) ? ($card_details_array[0]->Payment_Gateway_Charges * $card_details_array[0]->count)  : '0.00';
                    
                    //----------- total platform fee
                    if(isset($card_details_array[0]->Extra_Amount_Payment_Gateway) && !empty($card_details_array[0]->Extra_Amount_Payment_Gateway)){
                        $aTemp->Total_Platform_Fee = isset($card_details_array[0]->Total_Platform_Fee) && !empty($card_details_array[0]->Total_Platform_Fee) ? (($card_details_array[0]->Total_Platform_Fee * $card_details_array[0]->count) + $card_details_array[0]->Extra_Amount_Payment_Gateway)  : '0.00';
                    }else{
                        $aTemp->Total_Platform_Fee = isset($card_details_array[0]->Total_Platform_Fee) && !empty($card_details_array[0]->Total_Platform_Fee) ? ($card_details_array[0]->Total_Platform_Fee * $card_details_array[0]->count)  : '0.00';
                    }      
                  
                    $aTemp->Registration_Fee_GST = isset($card_details_array[0]->Registration_Fee_GST) && !empty($card_details_array[0]->Registration_Fee_GST) ? ($card_details_array[0]->Registration_Fee_GST * $card_details_array[0]->count)  : '0.00';
                   
                    $aTemp->Convenience_Fee_GST = isset($card_details_array[0]->Convenience_Fee_GST_18) && !empty($card_details_array[0]->Convenience_Fee_GST_18) ? ($card_details_array[0]->Convenience_Fee_GST_18 * $card_details_array[0]->count)  : '0.00';
                 
                    $aTemp->Platform_Fee_GST = isset($card_details_array[0]->Platform_Fee_GST_18) && !empty($card_details_array[0]->Platform_Fee_GST_18) ? ($card_details_array[0]->Platform_Fee_GST_18 * $card_details_array[0]->count)  : '0.00';
                   
                    $aTemp->Payment_Gateway_GST = isset($card_details_array[0]->Payment_Gateway_GST_18) && !empty($card_details_array[0]->Payment_Gateway_GST_18) ? ($card_details_array[0]->Payment_Gateway_GST_18 * $card_details_array[0]->count)  : '0.00';
                    
                    //----------- total taxes
                    if(isset($card_details_array[0]->Extra_Amount_Payment_Gateway_Gst) && !empty($card_details_array[0]->Extra_Amount_Payment_Gateway_Gst)){
                        $aTemp->Total_Platform_Fee = isset($card_details_array[0]->Total_Taxes) && !empty($card_details_array[0]->Total_Taxes) ? (($card_details_array[0]->Total_Taxes * $card_details_array[0]->count) + $card_details_array[0]->Extra_Amount_Payment_Gateway_Gst)  : '0.00';
                    }else{
                        $aTemp->Total_Platform_Fee = isset($card_details_array[0]->Total_Taxes) && !empty($card_details_array[0]->Total_Taxes) ? ($card_details_array[0]->Total_Taxes * $card_details_array[0]->count)  : '0.00';
                    }    

                    //----------- Final total amount
                    if(isset($card_details_array[0]->Extra_Amount_Payment_Gateway_Gst) && !empty($card_details_array[0]->Extra_Amount_Payment_Gateway_Gst) && !empty($card_details_array[0]->Extra_Amount_Payment_Gateway)){
                        $aTemp->Final_total_amount = isset($card_details_array[0]->BuyerPayment) && !empty($card_details_array[0]->BuyerPayment) ? (($card_details_array[0]->BuyerPayment * $card_details_array[0]->count) + $card_details_array[0]->Extra_Amount_Payment_Gateway_Gst) + ($card_details_array[0]->Extra_Amount_Payment_Gateway)  : '0.00';
                    }else{
                        $aTemp->Final_total_amount = isset($card_details_array[0]->BuyerPayment) && !empty($card_details_array[0]->BuyerPayment) ? ($card_details_array[0]->BuyerPayment * $card_details_array[0]->count)  : '0.00';
                    }   

                    // Extra Amount
                    
                    $aTemp->Extra_amount = isset($card_details_array[0]->Extra_Amount) && !empty($card_details_array[0]->Extra_Amount) ? ($card_details_array[0]->Extra_Amount)  : '0.00'; 

                    $aTemp->Extra_amount_pg_charges = isset($card_details_array[0]->Extra_Amount_Payment_Gateway) && !empty($card_details_array[0]->Extra_Amount_Payment_Gateway) ? ($card_details_array[0]->Extra_Amount_Payment_Gateway * $card_details_array[0]->count)  : '0.00';  
                   
                    $aTemp->Extra_amount_pg_GST = isset($card_details_array[0]->Extra_Amount_Payment_Gateway_Gst) && !empty($card_details_array[0]->Extra_Amount_Payment_Gateway_Gst) ? ($card_details_array[0]->Extra_Amount_Payment_Gateway_Gst * $card_details_array[0]->count)  : '0.00';  

                    // Applied Coupon Amount
                    $aTemp->Applied_Coupon_Amount = isset($card_details_array[0]->appliedCouponAmount) && !empty($card_details_array[0]->appliedCouponAmount) ? ($card_details_array[0]->appliedCouponAmount * $card_details_array[0]->count)  : '0.00';  
                    
                    if(isset($card_details_array[0]->appliedCouponAmount) && !empty($card_details_array[0]->appliedCouponAmount)){
                        $aTemp->Organiser_amount = isset($card_details_array[0]->to_organiser) && !empty($card_details_array[0]->to_organiser) ? ($card_details_array[0]->to_organiser - $card_details_array[0]->appliedCouponAmount) : 0;
                    }else{
                        $aTemp->Organiser_amount = isset($card_details_array[0]->to_organiser) && !empty($card_details_array[0]->to_organiser) ? $card_details_array[0]->to_organiser : 0;
                    }
              
                $AttendeeDataArray[] = $aTemp;
            }

           // dd($AttendeeDataArray);

            $url = env('APP_URL') . '/public/';
            $filename = "Revenue_report_" . time();
            $path = 'attendee_details_excell/' . date('Ymd') . '/';
            $data = Excel::store(new RemittanceDetailsDataExport($AttendeeDataArray), $path . '/' . $filename . '.xlsx', 'excel_uploads');
            $excel_url = url($path) . "/" . $filename . ".xlsx";

        }
        // dd($AttendeeDataArray);
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
                
                $UserName = isset($aPost['user_name']) ? $aPost['user_name'] : 0;
                $TransactionStatus = isset($aPost['TransactionStatus']) ? $aPost['TransactionStatus'] : "";
                $Email = isset($aPost['email']) ? $aPost['email'] : 0;
                $FromDate = isset($aPost['from_date']) ? strtotime(date("Y-m-d", strtotime($aPost['from_date']))) : 0;
                $ToDate = isset($aPost['to_date']) ? strtotime(date("Y-m-d 23:59:59", strtotime($aPost['to_date']))) : 0;
                $TransactionID = isset($aPost['TransactionID']) ? $aPost['TransactionID'] : '';

                $Page = (isset($aPost['page'])) ? $aPost['page'] : 1;
                $Limit = (isset($aPost['limit'])) ? $aPost['limit'] : 30;

                $sql = "SELECT p.id AS paymentId,p.txnid,p.amount,p.payment_status,p.created_datetime,u.id AS userId,u.firstname,u.lastname,u.email,u.mobile 
                        FROM booking_payment_details AS p 
                        LEFT JOIN users AS u ON u.id=p.created_by
                        WHERE p.event_id=:event_id ";

                if (!empty($UserName)) {
                    $sql .= " AND (p.firstname LIKE '%" . $UserName . "%' OR p.lastname LIKE '%" . $UserName . "%')";
                }
                
                if (!empty($Email)) {
                    $sql .= " AND p.email LIKE '%" . $Email . "%'";
                }

                if (!empty($TransactionID)) {
                    $sql .= " AND p.txnid LIKE '%" . $TransactionID . "%'";
                }

                if (!empty($FromDate)) {
                    $sql .= " AND p.created_datetime >= " . $FromDate;
                }
              
                if (!empty($ToDate)) {
                    $sql .= " AND p.created_datetime <= " . $ToDate;
                }

                if (!empty($TransactionStatus)) {
                    if ($TransactionStatus == 101)
                        $TransactionStatus = 'initiate';
                    if ($TransactionStatus == 1)
                        $TransactionStatus = 'success';
                    if ($TransactionStatus == 3)
                        $TransactionStatus = 'free';
                    if ($TransactionStatus == 2)
                        $TransactionStatus = 'fail';

                    $sql .= " AND p.payment_status = '".$TransactionStatus."' ";
                }

                $TotalCount = DB::select($sql, array('event_id' => $EventId));

                $Offset = ($Page * $Limit) - $Limit;
                if($Limit > 0) {
                   $sql .= " ORDER BY p.id DESC limit ".$Offset.",".$Limit." ";
                }

                $PaymentData = DB::select($sql, array('event_id' => $EventId));

                foreach ($PaymentData as $key => $value) {
                    $value->created_datetime = !empty($value->created_datetime) ? date("d-m-Y H:i A", ($value->created_datetime)) : '';
                }
                $ResponseData['PaymentData'] = (count($PaymentData) > 0) ? $PaymentData : [];
                $ResponseData['TotalRecord'] = !empty($TotalCount) ? count($TotalCount) : 0;

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

    function getCategoryWiseData(Request $request)
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
                $RegistrationFilter = isset($aPost['filter']) ? $aPost['filter'] : "week";
                $Ticket = isset($aPost['Ticket']) ? $aPost['Ticket'] : 0;
                $FromDate = isset($aPost['from_date']) ? strtotime($aPost['from_date']) : 0;
                $ToDate = isset($aPost['to_date']) ? strtotime($aPost['to_date']) : 0;

                if (!empty($Filter)) {
                    switch ($Filter) {
                        case 'today':
                            $StartDate = strtotime(date('Y-m-d 00:00:00'));
                            $EndDate = strtotime(date('Y-m-d 23:59:59'));
                            break;

                        case 'week':
                            $StartDate = strtotime(date('Y-m-d 00:00:00', strtotime('monday this week')));
                            $EndDate = strtotime(date('Y-m-d 23:59:59', strtotime('sunday this week')));
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
                
                // (select SUM(e.total_amount) AS TotalAmount from booking_details as bd left join event_booking as eb on eb.id=bd.booking_id and eb.transaction_status IN (1,3)) as TotalAmount // SUM(e.total_amount) AS TotalAmount
                
                // $SQL1 = "SELECT b.ticket_id,e.booking_date,SUM(b.quantity) AS TicketCount,(SELECT ticket_name FROM event_tickets WHERE id=b.ticket_id) AS TicketName,(SELECT total_quantity FROM event_tickets WHERE id=b.ticket_id) AS total_quantity,(SELECT ticket_price FROM event_tickets WHERE id=b.ticket_id) AS TicketPrice, SUM(e.total_amount) AS TotalAmount
                // FROM booking_details AS b
                // LEFT JOIN event_booking AS e ON b.booking_id = e.id
                // WHERE b.event_id =:event_id AND e.transaction_status IN (1,3)";
                // AND bd.ticket_amount != '0.00' AND bd.quantity != 0

                // $SQL1 = "SELECT e.id,e.booking_date,bd.ticket_id,(SELECT ticket_name FROM event_tickets WHERE id = bd.ticket_id) AS TicketName,(SELECT total_quantity FROM event_tickets WHERE id = bd.ticket_id) AS total_quantity,SUM(bd.quantity) AS TicketCount,SUM(e.total_amount) AS TotalAmount,(SELECT ticket_price FROM event_tickets WHERE id = bd.ticket_id) AS TicketPrice
                // FROM event_booking AS e LEFT JOIN booking_details AS bd ON bd.booking_id = e.id
                // WHERE e.event_id =:event_id AND e.transaction_status IN (1,3) AND bd.quantity != 0 "; //AND bd.ticket_amount != '0.00'

                $SQL1 = "SELECT e.id,e.booking_date,bd.ticket_id,(SELECT ticket_name FROM event_tickets WHERE id = bd.ticket_id) AS TicketName,(SELECT total_quantity FROM event_tickets WHERE id = bd.ticket_id) AS total_quantity, SUM(e.total_amount) AS TotalAmount,(SELECT ticket_price FROM event_tickets WHERE id = bd.ticket_id) AS TicketPrice
                FROM event_booking AS e LEFT JOIN booking_details AS bd ON bd.booking_id = e.id
                WHERE e.event_id =:event_id AND e.transaction_status IN (1,3) AND bd.quantity != 0";
                
                if ($Filter !== "") {
                    if (isset($StartDate) && isset($EndDate)) {
                        $SQL1 .= " AND bd.booking_date BETWEEN " . $StartDate . " AND " . $EndDate;
                    }
                }
                if (!empty($Ticket)) {
                    $SQL1 .= ' AND bd.ticket_id =' . $Ticket;
                }
                if (!empty($FromDate) && !empty($ToDate)) {
                    $SQL1 .= ' AND bd.booking_date BETWEEN ' . $FromDate . ' AND ' . $ToDate; 
                }

                $SQL1 .= " GROUP BY bd.ticket_id"; // e.id,
                $params = array('event_id' => $EventId);

                // dd($SQL1);
                $BookingData = DB::select($SQL1, $params);
                // dd($BookingData);
                $TicketCount = 0;
                foreach ($BookingData as $key => $value) {
                    
                    $TicketID = isset($value->ticket_id) && !empty($value->ticket_id) ? $value->ticket_id : 0;
                    if($TicketID){
                        $SQL2 = 'SELECT COUNT(a.id) AS TicketCount
                        FROM attendee_booking_details AS a 
                        LEFT JOIN booking_details AS b ON b.id=a.booking_details_id
                        LEFT JOIN event_booking AS e ON b.booking_id = e.id
                        WHERE b.event_id = '.$EventId.' AND b.ticket_id = '.$TicketID.' AND e.transaction_status IN (1,3)';

                        if ($Filter !== "") {
                            if (isset($StartDate) && isset($EndDate)) {
                                $SQL2 .= " AND b.booking_date BETWEEN " . $StartDate . " AND " . $EndDate;
                            }
                        }
                        if (!empty($Ticket)) {
                            $SQL2 .= ' AND b.ticket_id =' . $Ticket;
                        }
                        if (!empty($FromDate) && !empty($ToDate)) {
                            $SQL2 .= ' AND b.booking_date BETWEEN ' . $FromDate . ' AND ' . $ToDate; 
                        }
                        $TotalCountData = DB::select($SQL2, array());
                        $TicketCount = !empty($TotalCountData) ? $TotalCountData[0]->TicketCount : 0;
                    }else{
                        $TicketCount = 0;
                    }
                    
                    // dd($TotalCountData);
                  
                    $value->TicketCount = $TicketCount;  // (int) $value->TicketCount;
                   
                    $value->TotalTicketPrice = $value->TotalAmount;
                    $value->PendingCount = ((int)$value->total_quantity-(int)$TicketCount);
                    $value->SingleTicketPrice = $value->TicketPrice;
                   
                }
                $ResponseData['BookingData'] = (count($BookingData) > 0) ? $BookingData : [];

               //dd($ResponseData['BookingData']);
                $AllDates = [];
                $FinalBarChartData = [];


                $start_date_time = $end_date_time = "";
                if ($RegistrationFilter != "") {
                    $start_date_time = strtotime(date('Y-m-d 00:00:00', strtotime('monday this week')));
                    $end_date_time = strtotime(date('Y-m-d 23:59:59', strtotime('sunday this week')));
                }
                if ($RegistrationFilter != "") {
                    
                    $currentDate = isset($StartDate) ? $StartDate : $start_date_time;
                    $end = isset($EndDate) ? $EndDate : $end_date_time;

                    while ($currentDate <= $end) {
                        $AllDates[] = date('Y-m-d', $currentDate);
                        $currentDate = strtotime('+1 day', $currentDate);
                    }

                    foreach ($AllDates as $key => $dates) {
                        $sSQL = $start_date = $strtotime_start_today = $end_date = $strtotime_end_today = "";
                        $BarChartData = [];
                        $start_date = date('Y-m-d 00:00:00', strtotime($dates));
                        $strtotime_start_today = strtotime($start_date);

                        $end_date = date('Y-m-d 23:59:59', strtotime($dates));
                        $strtotime_end_today = strtotime($end_date);

                        $sSQL = "SELECT SUM(b.quantity) AS TicketCount
                        FROM booking_details AS b
                        LEFT JOIN event_booking AS e ON b.booking_id = e.id
                        WHERE b.event_id =:event_id AND e.transaction_status IN (1,3) AND b.booking_date BETWEEN :strtotime_start_today AND :strtotime_end_today ";
                        $params = array('event_id' => $EventId, 'strtotime_start_today' => $strtotime_start_today, 'strtotime_end_today' => $strtotime_end_today);
                        $BarChartData = DB::select($sSQL, $params);

                        // dd($BarChartData);
                        $FinalBarChartData[$key] = ["date" => $dates, "count" => count($BarChartData) > 0 ? (int) $BarChartData[0]->TicketCount : 0];
                    }
                }
                $ResponseData['FinalBarChartData'] = $FinalBarChartData;

                // Male/Female Graph
                $SQL2 = "SELECT a.ticket_id,a.attendee_details,b.event_id
                FROM attendee_booking_details AS a 
                LEFT JOIN booking_details AS b ON b.id=a.booking_details_id
                LEFT JOIN event_booking AS e ON b.booking_id = e.id
                WHERE b.event_id =:event_id AND e.transaction_status IN (1,3)";
                if ($Filter !== "") {
                    if (isset($StartDate) && isset($EndDate)) {
                        $SQL2 .= " AND b.booking_date BETWEEN " . $StartDate . " AND " . $EndDate;
                    }
                }
                if (!empty($Ticket)) {
                    $SQL2 .= ' AND b.ticket_id =' . $Ticket;
                }
                if (!empty($FromDate) && !empty($ToDate)) {
                    $SQL2 .= ' AND b.booking_date BETWEEN ' . $FromDate . ' AND ' . $ToDate;
                }
                $params = array('event_id' => $EventId);
                $PieChartData = DB::select($SQL2, $params);
                // dd($PieChartData);

                $maleCount = 0;
                $femaleCount = 0;
                $otherCount = 0;
                $ageCategoryCount = 0;
                $ageCategory = [];

                foreach ($PieChartData as $k => $item) {
                    $attendeeDetails = json_decode(json_decode($item->attendee_details, true));

                    foreach ($attendeeDetails as $detail) {

                        if ($detail->question_form_name == 'gender') {
                            $genderOptions = json_decode($detail->question_form_option, true);
                            $actualValue = $detail->ActualValue;
                            // dd($detail);
                            foreach ($genderOptions as $option) {
                                if ($option['id'] == $actualValue) {
                                    if ($option['label'] == 'Male') {
                                        $maleCount++;
                                    } elseif ($option['label'] == 'Female') {
                                        $femaleCount++;
                                    } elseif ($option['label'] == 'Other') {
                                        $otherCount++;
                                    }
                                }
                            }
                        }

                        if ($detail->question_form_type == 'age_category') {
                            $ageCategoryCount++;
                            $ageCategory[$k] = $detail->data[0];
                        }

                        // if ($detail->question_form_name == 'question_form_name' && $detail->is_subquestion == 1) {
                        //     $subQuestion = json_decode($detail->question_form_option, true);
                        //     dd($subQuestion);
                        //     $subQuestionName = $subQuestion[0]['label'];
                        //     $subQuestionId = $subQuestion[0]['id'];
                        //     $subQuestionValue = $detail->ActualValue;
                        //     $subQuestionCount = 0;
                        // }
                    }
                }

                $result = [];
                $countMap = [];

                foreach ($ageCategory as $key => $value) {
                    if (isset($countMap[$value->id])) {
                        $countMap[$value->id]->count++;
                    } else {
                        $value->count = 1;
                        $countMap[$value->id] = $value;
                        $result[$key] = $value;
                    }
                }

                // Reset keys to be sequential if needed
                $result = array_values($result);
                // dd($ageCategoryCount,$result);

                $ResponseData['maleCount'] = $maleCount;
                $ResponseData['femaleCount'] = $femaleCount;
                $ResponseData['otherCount'] = $otherCount;
                $ResponseData['ageCategoryCount'] = $ageCategoryCount;
                $ResponseData['ageCategory'] = $result;

                // Utm code Functionality Dashboard: Analysis of how registrants found the event (social media, direct link, email campaigns)
                // $SQL3 = "SELECT e.utm_campaign, SUM(b.quantity) AS total_quantity
                //         FROM booking_details AS b 
                //         LEFT JOIN event_booking AS e ON b.booking_id = e.id
                //         WHERE b.event_id = :event_id 
                //         AND e.transaction_status IN (1, 3) 
                //         AND e.utm_campaign IS NOT NULL
                //         AND e.utm_campaign <> ''";
                $SQL3 = "SELECT e.utm_campaign,COUNT(a.id) AS total_quantity
                FROM attendee_booking_details AS a 
                LEFT JOIN booking_details AS b ON b.id=a.booking_details_id
                LEFT JOIN event_booking AS e ON b.booking_id = e.id
                WHERE b.event_id =:event_id AND e.transaction_status IN (1,3) AND e.utm_campaign IS NOT NULL AND e.utm_campaign <> '' ";

                if ($Filter !== "") {
                    if (isset($StartDate) && isset($EndDate)) {
                        $SQL3 .= " AND b.booking_date BETWEEN " . $StartDate . " AND " . $EndDate;
                    }
                }
                if (!empty($Ticket)) {
                    $SQL3 .= ' AND b.ticket_id =' . $Ticket;
                }
                if (!empty($FromDate) && !empty($ToDate)) {
                    $SQL3 .= ' AND b.booking_date BETWEEN ' . $FromDate . ' AND ' . $ToDate;
                }
                $SQL3 .= " GROUP BY e.utm_campaign";

                $utmCode = DB::select($SQL3, array('event_id' => $EventId));
                 
                // $SQL4 = "SELECT e.utm_campaign, SUM(b.quantity) AS total_quantity
                //         FROM booking_details AS b 
                //         LEFT JOIN event_booking AS e ON b.booking_id = e.id
                //         WHERE b.event_id = :event_id 
                //         AND e.transaction_status IN (1, 3) AND e.utm_campaign = '' ";
                $SQL4 = "SELECT e.utm_campaign,COUNT(a.id) AS total_quantity
                FROM attendee_booking_details AS a 
                LEFT JOIN booking_details AS b ON b.id=a.booking_details_id
                LEFT JOIN event_booking AS e ON b.booking_id = e.id
                WHERE b.event_id =:event_id AND e.transaction_status IN (1,3) AND e.utm_campaign = '' ";

                if ($Filter !== "") {
                    if (isset($StartDate) && isset($EndDate)) {
                        $SQL4 .= " AND b.booking_date BETWEEN " . $StartDate . " AND " . $EndDate;
                    }
                }
                if (!empty($Ticket)) {
                    $SQL4 .= ' AND b.ticket_id =' . $Ticket;
                }
                if (!empty($FromDate) && !empty($ToDate)) {
                    $SQL4 .= ' AND b.booking_date BETWEEN ' . $FromDate . ' AND ' . $ToDate;
                }
                // dd($SQL4);
                $utmWebCode = DB::select($SQL4, array('event_id' => $EventId));
               
                if(!empty($utmWebCode)){
                    $UtmArray = array_merge($utmCode,$utmWebCode);
                    foreach($UtmArray as $res){
                        if($res->utm_campaign == ''){
                            $res->utm_campaign = 'Races Website';
                        }
                        $res->total_quantity = (int)$res->total_quantity;
                    }
                    $ResponseData['utmCode'] = $UtmArray; 
                }else{
                    foreach($UtmArray as $res){
                        if($res->utm_campaign == ''){
                            $res->total_quantity = (int)$res->total_quantity;
                        }
                    }
                    $ResponseData['utmCode'] = $utmCode;
                }
                // $new_array = array_merge($utmCode,$utmWebCode);

                // Coupon Code Data
                // $SQL4 = "SELECT COUNT(coupon_id) AS CouponCount,(SELECT id FROM event_coupon WHERE id=coupon_id) AS coupon_id, (SELECT discount_name FROM event_coupon WHERE id=coupon_id) AS DiscountName, (SELECT discount_code FROM event_coupon_details WHERE event_coupon_id=coupon_id) AS DiscountCode, (SELECT no_of_discount FROM event_coupon_details WHERE event_coupon_id=coupon_id) AS TotalDiscountCode
                //         FROM applied_coupons 
                //         WHERE event_id=:event_id";
                $SQL4 = "SELECT COUNT(ac.coupon_id) AS CouponCount,(SELECT id FROM event_coupon WHERE id=ac.coupon_id) AS coupon_id, (SELECT discount_name FROM event_coupon WHERE id=ac.coupon_id) AS DiscountName, (SELECT discount_code FROM event_coupon_details WHERE event_coupon_id=ac.coupon_id) AS DiscountCode, (SELECT no_of_discount FROM event_coupon_details WHERE event_coupon_id=coupon_id) AS TotalDiscountCode
                        FROM applied_coupons as ac
                        WHERE event_id=:event_id";
                if ($Filter !== "") {
                    if (isset($StartDate) && isset($EndDate)) {
                        $SQL4 .= " AND created_at BETWEEN " . $StartDate . " AND " . $EndDate;
                    }
                }
                if (!empty($Ticket)) {
                    $SQL4 .= ' AND ticket_ids =' . $Ticket;
                }
                if (!empty($FromDate) && !empty($ToDate)) {
                    $SQL4 .= ' AND created_at BETWEEN ' . $FromDate . ' AND ' . $ToDate;
                }
                $SQL4 .= " GROUP BY coupon_id";
                $CouponCodes = DB::select($SQL4, array('event_id' => $EventId));
                if(!empty($CouponCodes)){
                    foreach($CouponCodes as $res){

                        $SQL5 = "SELECT ac.id,COUNT(ac.coupon_id) AS Coupon_Count
                        FROM applied_coupons as ac left join event_booking as eb on eb.id=ac.booking_id 
                        WHERE ac.coupon_id = ".$res->coupon_id." AND eb.transaction_status IN (1,3) ";
                        $aResult = DB::select($SQL5, array());
                        //dd($aResult);

                        $res->CouponCount = !empty($aResult) ? $aResult[0]->Coupon_Count : $res->CouponCount;
                    }

                }
                $ResponseData['CouponCodes'] = $CouponCodes;
                // dd($CouponCodes);


                // Custom questions 
                // $SQL5 = "SELECT GROUP_CONCAT(id SEPARATOR ', ') AS Ids,GROUP_CONCAT(general_form_id SEPARATOR ', ') AS GIds FROM event_form_question WHERE event_id=:event_id AND question_form_name=:question_form_name AND question_form_option !=:question_form_option AND question_form_type=:question_form_type";
                // $EventFormQuestions = DB::select($SQL5, array('event_id' => $EventId, 'question_form_name' => 'sub_question', 'question_form_option' => '', 'question_form_type' => 'select'));
                $SQL5 = "SELECT GROUP_CONCAT(id SEPARATOR ', ') AS Ids,GROUP_CONCAT(general_form_id SEPARATOR ', ') AS GIds FROM event_form_question WHERE event_id=:event_id AND is_custom_form=:is_custom_form ";
                $EventFormQuestions = DB::select($SQL5, array('event_id' => $EventId, 'is_custom_form' => 0));

                $QuestionIds = (count($EventFormQuestions) > 0) ? $EventFormQuestions[0]->Ids : "";
                // $GeneralQuestionIds = (count($EventFormQuestions) > 0) ? $EventFormQuestions[0]->GIds : "";

                $SQL6 = "SELECT a.ticket_id,a.attendee_details,b.event_id,a.id as attendeeId,e.id as booking_id
                FROM attendee_booking_details AS a 
                LEFT JOIN booking_details AS b ON b.id=a.booking_details_id
                LEFT JOIN event_booking AS e ON b.booking_id = e.id
                WHERE b.event_id =:event_id AND e.transaction_status IN (1,3)";
                if ($Filter !== "") {
                    if (isset($StartDate) && isset($EndDate)) {
                        $SQL6 .= " AND b.booking_date BETWEEN " . $StartDate . " AND " . $EndDate;
                    }
                }
                if (!empty($Ticket)) {
                    $SQL6 .= ' AND b.ticket_id =' . $Ticket;
                }
                if (!empty($FromDate) && !empty($ToDate)) {
                    $SQL6 .= ' AND b.booking_date BETWEEN ' . $FromDate . ' AND ' . $ToDate;
                }
                $bind = array('event_id' => $EventId);
                $CustomQue = DB::select($SQL6, $bind);
                $CustomQuestions = [];
                $questionIdsArray = explode(',', $QuestionIds);
                // dd($CustomQue);
                foreach ($CustomQue as $key => $value) {
                    $attendee_details = json_decode(json_decode($value->attendee_details));
                    // dd($attendee_details);

                    foreach ($attendee_details as $key => $attendee) {
                        if (in_array($attendee->id, $questionIdsArray)) {
                            if ($attendee->ActualValue != "" && $attendee->question_form_option != "") {
                                $question_form_option = json_decode($attendee->question_form_option);

                                $CustomQuestions[$attendee->id][] = $attendee;
                            }
                        }
                    }
                }

                $CountArray = array();
                // dd($CustomQuestions);
                // foreach ($variable as $key => $value) {
                //     $CountArray[$key] = ;
                // }
                $result = [];

                foreach ($CustomQuestions as $key => $items) {

                    foreach ($items as $item) {

                        $actualValue = $item->ActualValue;
                        $question_label = $item->question_label;

                        $options = json_decode($item->question_form_option, true);
                         //echo '<pre>';
                        //dd($options);
                        // print_r($options);
                        $label = "";
                        $limit = 0;
                        $limit_flag = false;
                        $labels = [];

                        if($item->question_form_type == 'select'){

                            $SQL2 = "SELECT question_form_option
                            FROM event_form_question 
                            WHERE id =:que_id ";
                           
                            $params = array('que_id' => $item->id);
                            $QueData = DB::select($SQL2, $params);
                           
                            $new_array = [];
                            $questionSizArray = !empty($QueData) ? json_decode($QueData[0]->question_form_option) : [];
                            if(!empty($questionSizArray)){
                                foreach($questionSizArray as $val){
                                    $new_array[$val->id] = isset($val->count) ? $val->count : 0;
                                }
                            }

                            // dd($new_array); 
                            foreach ($options as $option) {

                                if (in_array($option['id'], explode(',', $actualValue))) {
                                    $labels[] = $option['label'];

                                    $final_limit = (isset($new_array[$option['id']])) ? $new_array[$option['id']] : 0;
                                    $limit =  (int)$final_limit;
                                    $limit_flag = true;
                                }
                                
                                $label = implode(', ', $labels);
                            }
                        }else{
                             foreach ($options as $option) {

                                if (in_array($option['id'], explode(',', $actualValue))) {
                                    $labels[] = $option['label'];
                                    $limit = isset($option["count"]) ? (int)$option["count"] : 0 ;
                                    $limit_flag = true;
                                }
                                $label = implode(', ', $labels);
                            }
                        }

                       

                        if (!isset($CountArray[$key])) {
                            $CountArray[$key] = [
                                "question_label" => $question_label,
                                // "limit_flag" => $limit_flag,
                               // "final_count" => count($CountArray[$key][$actualValue]["count"]),
                            ];
                        }
                        // dd($CountArray[$key]);

                        if (!isset($CountArray[$key][$actualValue])) {
                            $CountArray[$key][$actualValue] = ["label" => $label, "count" => 0, "limit" => $limit];
                        }
                        // dd($CountArray[$key][$actualValue]);

                        $CountArray[$key][$actualValue]["count"]++;
                        // $CountArray[$key][$actualValue]["limit"]++;
                    }//die;
                }

               //dd($CountArray);
                $ResponseData['CountArray'] = $CountArray;

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


    function ChangePaymentStatus(Request $request)
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

            $booking_details_id = !empty($aPost['booking_details_id']) ? $aPost['booking_details_id'] : 0;
            // dd($booking_details_id);

            $SQL5 = "SELECT booking_pay_id FROM event_booking as eb left join booking_details as bd on bd.booking_id = eb.id WHERE bd.id=:booking_details_id ";
            $aResult = DB::select($SQL5, array('booking_details_id' => $booking_details_id));
             // dd($aResult);

            $booking_pay_id = !empty($aResult) ? $aResult[0]->booking_pay_id : 0;

            if (!$empty) {

                if(!empty($booking_pay_id )){
                    $Bindings = array(
                        "payment_status" => 'initiate',
                        "change_status_manual" => 1,
                        "id" => $booking_pay_id
                    );

                    $SQL = "UPDATE booking_payment_details SET payment_status =:payment_status, change_status_manual =:change_status_manual WHERE id=:id";
                    DB::update($SQL, $Bindings);

                    $Bindings1 = array(
                        "transaction_status" => 0,
                        "booking_pay_id" => $booking_pay_id
                    );

                    $SQL1 = "UPDATE event_booking SET transaction_status =:transaction_status WHERE booking_pay_id=:booking_pay_id";
                    DB::update($SQL1, $Bindings1);

                    $ResposneCode = 200;
                    $message = 'Payment status successfully';

                }
                      
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


}
