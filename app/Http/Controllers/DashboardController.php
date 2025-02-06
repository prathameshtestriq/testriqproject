<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Libraries\Numberformate; 
use \stdClass;

class DashboardController extends Controller
{
    public function clear_search()
    {
        session::forget('filter');
        session::forget('category');
        session::forget('event_name');
        session::forget('from_date');
        session::forget('to_date');
        return redirect('/dashboard');
    }
    public function dashboard_details(Request $request)
    {
        // dd($request->all());
        $aReturn = [];
        $aReturn['search_filter'] = '';
        $aReturn['search_category'] = '';
        $aReturn['search_event_name'] = '';
        $aReturn['search_from_date'] = '';
        $aReturn['search_to_date'] = '';

        if (isset($request->form_type) && $request->form_type == 'search_dashboard') {
            //  dd($request->name);
            session(['filter' => $request->search_filter]);
            session(['category' => $request->category]);
            session(['event_name' => $request->event_name]);
            session(['from_date' => $request->from_date]);
            session(['to_date' => $request->to_date]);


            return redirect('/dashboard');
        }

        $numberFormate = new Numberformate();
        $aReturn['search_filter'] = (!empty(session('filter'))) ? session('filter') : [];
        $aReturn['search_category'] = (!empty(session('category'))) ? session('category') : '';
        $aReturn['search_event_name'] = (!empty(session('event_name'))) ? session('event_name') : '';
        $aReturn['search_from_date'] = (!empty(session('from_date'))) ? strtotime(session('from_date')) : '';
        $aReturn['search_to_date'] = (!empty(session('to_date'))) ? strtotime(session('to_date')) : '';
        // dd($aReturn['search_event_name']);
        // dd( $aReturn['search_event_name'] );

        if (!empty($aReturn['search_filter'])) {
            switch ($aReturn['search_filter']) {
                case 'Today':
                    $StartDate = strtotime(date('Y-m-d 00:00:00'));
                    $EndDate = strtotime(date('Y-m-d 23:59:59'));
                    break;

                case 'Week':
                    $StartDate = strtotime(date('Y-m-d 00:00:00', strtotime('monday this week')));
                    $EndDate = strtotime(date('Y-m-d 23:59:59', strtotime('sunday this week')));
                    break;

                case 'Month':
                    $StartDate = strtotime(date('Y-m-01'));
                    $EndDate = strtotime(date('Y-m-t'));
                    break;

                default:
                    break;
            }
            // dd($StartDate, $EndDate);
        }

        //------------------------------- Net Sales && Total Participants
        $SQL1 = "SELECT IFNULL(SUM(b.quantity), 0) AS NetSales 
        FROM booking_details AS b
        LEFT JOIN event_booking AS e ON b.booking_id = e.id
        WHERE e.transaction_status IN (1,3)";
        if ($aReturn['search_filter'] !== "") {
            if (isset($StartDate) && isset($EndDate)) {
                $SQL1 .= ' AND b.booking_date BETWEEN ' . $StartDate . ' AND ' . $EndDate;
            }
        }
        if (!empty($aReturn['search_category'])) {
            $SQL1 .= ' AND b.ticket_id =' . $aReturn['search_category'];
        }
        if (!empty($aReturn['search_from_date']) && !empty($aReturn['search_to_date'])) {
            $SQL1 .= ' AND b.booking_date BETWEEN ' . $aReturn['search_from_date'] . ' AND ' . $aReturn['search_to_date'];
        }
        if (!empty($aReturn['search_event_name'])) {
            $SQL1 .= ' AND e.event_id =' . $aReturn['search_event_name'];
        }
        // dd($SQL1);
        $TotalBooking = DB::select($SQL1, array());
        $NetSales = (count($TotalBooking) > 0) ? $TotalBooking[0]->NetSales : 0;
        $aReturn['NetSales'] = $NetSales;


        #-------------------------------- Registration && Net Earnings
        // $SQL2 = "SELECT COUNT(e.id) AS TotalRegistration,SUM(e.total_amount) AS TotalAmount FROM event_booking AS e 
        // LEFT JOIN booking_details AS b ON b.booking_id = e.id
        // WHERE e.transaction_status IN (1,3)";
        
        // if ($aReturn['search_filter'] !== "") {
        //     if (isset($StartDate) && isset($EndDate)) {
        //         $SQL2 .= ' AND e.booking_date BETWEEN ' . $StartDate . ' AND ' . $EndDate;
        //     }
        // }
        // if (!empty($aReturn['search_category'])) {
        //     $SQL2 .= ' AND b.ticket_id =' . $aReturn['search_category'];
        // }
       
        // if (!empty($aReturn['search_from_date']) && !empty($aReturn['search_to_date'])) {
        //     $SQL2 .= ' AND e.booking_date BETWEEN ' . $aReturn['search_from_date'] . ' AND ' . $aReturn['search_to_date'];
        // }
        // if (!empty($aReturn['search_event_name'])) {
        //     $SQL2 .= ' AND e.event_id =' . $aReturn['search_event_name'];
        // }
         
        $SQL2 = "SELECT DISTINCT(e.id) AS TotalRegistration ,e.total_amount AS TotalAmount,e.transaction_status FROM booking_details AS b LEFT JOIN event_booking AS e ON b.booking_id = e.id WHERE e.transaction_status IN (1,3)";
              
        if ($aReturn['search_filter'] !== "") {
            if (isset($StartDate) && isset($EndDate)) {
                $SQL2 .= ' AND b.booking_date BETWEEN ' . $StartDate . ' AND ' . $EndDate;
            }
        }
        if (!empty($aReturn['search_category'])) {
            $SQL2 .= ' AND b.ticket_id =' . $aReturn['search_category'];
        }
        if (!empty($aReturn['search_from_date']) && !empty($aReturn['search_to_date'])) {
            $SQL2 .= ' AND b.booking_date BETWEEN ' . $aReturn['search_from_date'] . ' AND ' . $aReturn['search_to_date'];
        }
        if (!empty($aReturn['search_event_name'])) {
            $SQL2 .= ' AND b.event_id =' . $aReturn['search_event_name'];
        }
        
        $TotalRegistration = DB::select($SQL2, array());
        
        $total_net_earning_amount = 0;
        if(!empty($TotalRegistration)){
            foreach($TotalRegistration as $res){
                $total_net_earning_amount += !empty($res->TotalAmount) ? $res->TotalAmount : 0;
            }
        }

        // echo $SQL2; die;
        $TotalRegistration = DB::select($SQL2, array());
        $aReturn['TotalSuccessRegistration'] = !empty($TotalRegistration) ? count($TotalRegistration) : 0;
        $aReturn['TotalAmount1'] = (count($TotalRegistration) > 0) ? $TotalRegistration[0]->TotalAmount : 0;
        // $aReturn['TotalAmount'] = number_format($total_net_earning_amount, 2);
        
        $aReturn['TotalAmount'] =  $numberFormate->formatInIndianCurrency($total_net_earning_amount);
        // dd($aReturn['TotalAmount']);

        //---------------------------------- Total Registration Users
        $SQL5 = "SELECT COUNT(id) AS TotalRegistrationUsers FROM event_booking WHERE 1=1";
       
        if (!empty($aReturn['search_event_name'])) {
            $SQL5 .= ' AND event_id =' . $aReturn['search_event_name'];
        }
        if ($aReturn['search_filter'] !== "") {
            if (isset($StartDate) && isset($EndDate)) {
                $SQL5 .= ' AND booking_date BETWEEN ' . $StartDate . ' AND ' . $EndDate;
            }
        }
        // if (!empty($aReturn['search_category'])) {
        //     $SQL5 .= ' AND id = IFNULL((select booking_id from booking_details where booking_id = event_booking.id and ticket_id = '.$aReturn['search_category'].'),0)';
        // }
       
        if (!empty($aReturn['search_from_date']) && !empty($aReturn['search_to_date'])) {
            $SQL5 .= ' AND booking_date BETWEEN ' . $aReturn['search_from_date'] . ' AND ' . $aReturn['search_to_date'];
        }
         //dd($SQL5);
        $TotalRegistration = DB::select($SQL5, array());
        $TotalRegistrationCount = (count($TotalRegistration) > 0) ? $TotalRegistration[0]->TotalRegistrationUsers : 0;

        // Total Registration Users With Success  And Conversion Rate
        $SQL6 = "SELECT COUNT(e.id) AS TotalRegistrationUsersWithSuccess FROM event_booking AS e 
        LEFT JOIN booking_details AS b ON b.booking_id = e.id
        WHERE  e.transaction_status IN (1,3)";
        if ($aReturn['search_filter'] !== "") {
            if (isset($StartDate) && isset($EndDate)) {
                $SQL6 .= ' AND b.booking_date BETWEEN ' . $StartDate . ' AND ' . $EndDate;
            }
        }
        if (!empty($aReturn['search_category'])) {
            $SQL6 .= ' AND b.ticket_id =' . $aReturn['search_category'];
        }
        if (!empty($aReturn['search_from_date']) && !empty($aReturn['search_to_date'])) {
            $SQL6 .= ' AND b.booking_date BETWEEN ' . $aReturn['search_from_date'] . ' AND ' . $aReturn['search_to_date'];
        }
        if (!empty($aReturn['search_event_name'])) {
            $SQL6 .= ' AND e.event_id =' . $aReturn['search_event_name'];
        }
        // dd($SQL6); 
        $TotalRegistrationUsersWithSuccess = DB::select($SQL6, array());
        // dd($TotalRegistrationUsersWithSuccess);
        $TotalRegistrationUsersWithSuccessCount = (count($TotalRegistrationUsersWithSuccess) > 0) ? $TotalRegistrationUsersWithSuccess[0]->TotalRegistrationUsersWithSuccess : 0;

        // Calculate percentage
        $percentage = ($TotalRegistrationUsersWithSuccessCount > 0 && $TotalRegistrationCount > 0) ?
            round(($TotalRegistrationUsersWithSuccessCount / $TotalRegistrationCount) * 100, 2) : 0;

        $aReturn['TotalRegistrationCount'] = $TotalRegistrationCount;
        $aReturn['TotalRegistrationUsersWithSuccess'] = $TotalRegistrationUsersWithSuccessCount;
        $aReturn['SuccessPercentage'] = $percentage;
        // dd($aReturn['SuccessPercentage']);

        //------------------------------- PAGE VIEWS
        $SQL8 = "SELECT COUNT(id) AS TotalPageViews FROM page_views WHERE 1=1";
        if ($aReturn['search_filter'] !== "") {
            if (isset($StartDate) && isset($EndDate)) {
                $SQL8 .= ' AND last_updated_datetime BETWEEN ' . $StartDate . ' AND ' . $EndDate;
            }
        }

        if (!empty($aReturn['search_from_date']) && !empty($aReturn['search_to_date'])) {
            $SQL8 .= ' AND last_updated_datetime BETWEEN ' . $aReturn['search_from_date'] . ' AND ' . $aReturn['search_to_date'];
        }
        if (!empty($aReturn['search_event_name'])) {
            $SQL8 .= ' AND event_id =' . $aReturn['search_event_name'];
        }
        $TotalPageViews = DB::select($SQL8, array());
        $aReturn['TotalPageViews'] = (count($TotalPageViews) > 0) ? $TotalPageViews[0]->TotalPageViews : 0;


        #-------------------------- Total Number Of Events
        $SQL9 = 'SELECT Count(id) as TotalEventCount FROM events WHERE active = 1 AND deleted = 0';
        if (!empty($aReturn['search_event_name'])) {
            $SQL9 .= ' AND id =' . $aReturn['search_event_name'];
        }

        if ($aReturn['search_filter'] !== "") {
            if (isset($StartDate) && isset($EndDate)) {
                $SQL9 .= ' AND start_time BETWEEN ' . $StartDate . ' AND ' . $EndDate;
            }
        }
        if (!empty($aReturn['search_from_date']) && !empty($aReturn['search_to_date'])) {
            // $SQL9 .= ' AND start_time BETWEEN ' . $aReturn['search_from_date'] . ' AND ' . $aReturn['search_to_date'];
            $SQL9 .= ' AND start_time >= ' . $aReturn['search_from_date'] . ' AND end_time < ' . $aReturn['search_to_date'];
        }
        if (!empty($aReturn['search_category'])) {
            $SQL9 .= ' AND id = (select event_id from event_tickets where event_id = events.id and id = '.$aReturn['search_category'].')';
        }
        // dd($SQL9);
        $TotalNumberEvents = DB::select($SQL9, array());
        $aReturn['TotalNumberEvents'] = (count($TotalNumberEvents) > 0) ? $TotalNumberEvents[0]->TotalEventCount : 0;
        // dd(   $aReturn['TotalNumberEvents'] );

        #------------------------ Live Events
        $SQL10 = 'SELECT Count(id) as TotalLiveEventCount FROM events WHERE active = 1 AND event_info_status = 1';
        if (!empty($aReturn['search_event_name'])) {
            $SQL10 .= ' AND id =' . $aReturn['search_event_name'];
        }
        if ($aReturn['search_filter'] !== "") {
            if (isset($StartDate) && isset($EndDate)) {
                $SQL10 .= ' AND start_time BETWEEN ' . $StartDate . ' AND ' . $EndDate;
            }
        }
        if (!empty($aReturn['search_from_date']) && !empty($aReturn['search_to_date'])) {
            $SQL10 .= ' AND start_time >= ' . $aReturn['search_from_date'] . ' AND end_time < ' . $aReturn['search_to_date'];
        }
        if (!empty($aReturn['search_category'])) {
            $SQL10 .= ' AND id = (select event_id from event_tickets where event_id = events.id and id = '.$aReturn['search_category'].')';
        }

        $TotalNumberLiveEvents = DB::select($SQL10, array());
        $aReturn['TotalNumberLiveEvents'] = (count($TotalNumberLiveEvents) > 0) ? $TotalNumberLiveEvents[0]->TotalLiveEventCount : 0;
        // dd(    $aReturn['TotalNumberLiveEvents']  );

        #---------------------------- Draft Events
        $SQL101 = 'SELECT Count(id) as TotalDraftEventCount FROM events WHERE active = 1 AND event_info_status = 3';
        if (!empty($aReturn['search_event_name'])) {
            $SQL101 .= ' AND id =' . $aReturn['search_event_name'];
        }
        if ($aReturn['search_filter'] !== "") {
            if (isset($StartDate) && isset($EndDate)) {
                $SQL101 .= ' AND start_time BETWEEN ' . $StartDate . ' AND ' . $EndDate;
            }
        }
        if (!empty($aReturn['search_from_date']) && !empty($aReturn['search_to_date'])) {
            $SQL101 .= ' AND start_time >= ' . $aReturn['search_from_date'] . ' AND end_time < ' . $aReturn['search_to_date'];
        }
        if (!empty($aReturn['search_category'])) {
            $SQL101 .= ' AND id = (select event_id from event_tickets where event_id = events.id and id = '.$aReturn['search_category'].')';
        }

        $TotalNumberDraftEvents = DB::select($SQL101, array());
        $aReturn['TotalNumberDraftEvents'] = (count($TotalNumberDraftEvents) > 0) ? $TotalNumberDraftEvents[0]->TotalDraftEventCount : 0;
        // dd(    $aReturn['TotalNumberDraftEvents']  );

        #------------------------------ Private Events
        $SQL102 = 'SELECT Count(id) as TotalPrivateEventCount FROM events WHERE active = 1 AND event_info_status = 2';
        if (!empty($aReturn['search_event_name'])) {
            $SQL102 .= ' AND id =' . $aReturn['search_event_name'];
        }
        if ($aReturn['search_filter'] !== "") {
            if (isset($StartDate) && isset($EndDate)) {
                $SQL102 .= ' AND start_time BETWEEN ' . $StartDate . ' AND ' . $EndDate;
            }
        }
        if (!empty($aReturn['search_from_date']) && !empty($aReturn['search_to_date'])) {
            $SQL102 .= ' AND start_time >= ' . $aReturn['search_from_date'] . ' AND end_time < ' . $aReturn['search_to_date'];
        }
        if (!empty($aReturn['search_category'])) {
            $SQL102 .= ' AND id = (select event_id from event_tickets where event_id = events.id and id = '.$aReturn['search_category'].')';
        }

        $TotalNumberPrivateEvents = DB::select($SQL102, array());
        $aReturn['TotalNumberPrivateEvents'] = (count($TotalNumberPrivateEvents) > 0) ? $TotalNumberPrivateEvents[0]->TotalPrivateEventCount : 0;
        // dd(    $aReturn['TotalNumberPrivateEvents']  );

        #-------------------------------------- Total Number Users
        $SQL11 = 'SELECT Count(id) as TotalUserCount FROM users WHERE is_active = 1 AND is_deleted = 0';
        
        if ($aReturn['search_filter'] !== "") {
            if (isset($StartDate) && isset($EndDate)) {
                $SQL11 .= ' AND created_at BETWEEN ' . $StartDate . ' AND ' . $EndDate;
            }
        }
        if (!empty($aReturn['search_from_date']) && !empty($aReturn['search_to_date'])) {
            $SQL11 .= ' AND created_at BETWEEN ' . $aReturn['search_from_date'] . ' AND ' . $aReturn['search_to_date'];
        }

        if (!empty($aReturn['search_event_name'])) {
            $SQL11 .= ' AND id = (select created_by from events where id = '.$aReturn['search_event_name'].') ';
        }

        // if (!empty($aReturn['search_category'])) {
        //     $SQL11 .= ' AND id = IFNULL((select user_id from booking_details where user_id = users.id and ticket_id = '.$aReturn['search_category'].'),0)';
        // }

        $TotalNumberUsers = DB::select($SQL11, array());
       // dd($TotalNumberUsers);
        $aReturn['TotalNumberUsers'] = (count($TotalNumberUsers) > 0) ? $TotalNumberUsers[0]->TotalUserCount : 0;
        // dd(   $aReturn['TotalNumberUsers'] );

        #------------------------------------------- Remitted Amount
        $SQL12 = 'SELECT sum(amount_remitted) as amount_remitted FROM remittance_management WHERE active = 1';

        if ($aReturn['search_filter'] !== "") {
            if (isset($StartDate) && isset($EndDate)) {
                $SQL12 .= ' AND remittance_date BETWEEN ' . $StartDate . ' AND ' . $EndDate;
            }
        }
        if (!empty($aReturn['search_from_date']) && !empty($aReturn['search_to_date'])) {
            $SQL12 .= ' AND remittance_date BETWEEN ' . $aReturn['search_from_date'] . ' AND ' . $aReturn['search_to_date'];
        }
        if (!empty($aReturn['search_event_name'])) {
            $SQL12 .= ' AND event_id =' . $aReturn['search_event_name'];
        }

        $TotalRemittedAmount1 = DB::select($SQL12, array());
       
        $TotalRemittedAmount2 = (count($TotalRemittedAmount1) > 0) ? $TotalRemittedAmount1[0]->amount_remitted : 0;
        
        $aReturn['TotalRemittedAmount'] = $numberFormate->formatInIndianCurrency($TotalRemittedAmount2);
        //dd($aReturn['TotalRemittedAmount']);

        //----------------------------------- Payment Count
        $sql17 = "SELECT count(p.id) AS paymentId FROM booking_payment_details AS p 
        LEFT JOIN users AS u ON u.id=p.created_by
        WHERE 1=1";
      
        if ($aReturn['search_filter'] !== "") {
            if (isset($StartDate) && isset($EndDate)) {
                $sql17 .= ' AND p.created_datetime BETWEEN ' . $StartDate . ' AND ' . $EndDate;
            }
        }
        if (!empty($aReturn['search_from_date']) && !empty($aReturn['search_to_date'])) {
            $sql17 .= ' AND p.created_datetime BETWEEN ' . $aReturn['search_from_date'] . ' AND ' . $aReturn['search_to_date'];
        }
        if (!empty($aReturn['search_event_name'])) {
            $sql17 .= ' AND p.event_id =' . $aReturn['search_event_name'];
        }
        // if (!empty($aReturn['search_category'])) {
        //     $sql17 .= ' AND p.id = IFNULL((select booking_pay_id from event_booking as eb left join booking_details as bd on bd.booking_id = eb.id where eb.booking_pay_id = p.id and bd.ticket_id = '.$aReturn['search_category'].'),0)';
        // }

        $sql17 .= ' ORDER BY p.id DESC';

        $PaymentData = DB::select($sql17, array());
        $aReturn['PaymentData'] = (count($PaymentData) > 0) ? $PaymentData[0]->paymentId : [];
        // dd( $aReturn['PaymentData']);

        $SQL13 = "SELECT e.id,e.booking_date,bd.ticket_id,(SELECT ticket_name FROM event_tickets WHERE id = bd.ticket_id) AS TicketName,(SELECT total_quantity FROM event_tickets WHERE id = bd.ticket_id) AS total_quantity,SUM(bd.quantity) AS TicketCount,SUM(e.total_amount) AS TotalAmount,(SELECT ticket_price FROM event_tickets WHERE id = bd.ticket_id) AS TicketPrice
                FROM event_booking AS e LEFT JOIN booking_details AS bd ON bd.booking_id = e.id
                WHERE e.transaction_status IN (1,3) AND bd.ticket_amount != '0.00' AND bd.quantity != 0 ";

        if ($aReturn['search_filter'] !== "") {
            if (isset($StartDate) && isset($EndDate)) {
                $SQL13 .= " AND e.booking_date BETWEEN " . $StartDate . " AND " . $EndDate;
            }
        }
        if (!empty($aReturn['search_category'])) {
            $SQL13 .= ' AND bd.ticket_id =' . $aReturn['search_category'];
        }
        if (!empty($aReturn['search_from_date']) && !empty($aReturn['search_to_date'])) {
            $SQL13 .= ' AND e.booking_date BETWEEN ' . $aReturn['search_from_date'] . ' AND ' . $aReturn['search_to_date'];
        }

        if (!empty($aReturn['search_event_name'])) {
            $SQL13 .= ' AND e.event_id =' . $aReturn['search_event_name'];
        } else {
            $SQL13 .= ' AND 1=2';
        }

        $SQL13 .= " GROUP BY bd.ticket_id"; // e.id,

        $BookingData = DB::select($SQL13, array());

        // dd($BookingData);
        // $net_earning_amt = 0;
        if(!empty($BookingData)) {
            foreach ($BookingData as $key => $value) {
                $value->TicketCount = (int) $value->TicketCount;
                $value->TotalTicketPrice = $value->TicketCount * $value->TicketPrice;
                $value->PendingCount = ((int)$value->total_quantity-(int)$value->TicketCount);
                $value->SingleTicketPrice = $value->TicketPrice;
                // $net_earning_amt += $value->TotalAmount;
            }
        }
        
        $aReturn['BookingData'] = (count($BookingData) > 0) ? $BookingData : [];

        #Total active Event
        $SQL = "SELECT id,name FROM events WHERE active=1 AND deleted = 0";
        $aReturn['EventsData'] = DB::select($SQL, array());


        #Total active category/ticket name  
        $SQL = "SELECT id,ticket_name FROM event_tickets WHERE active=1 AND is_deleted = 0";
        $aReturn['TicketsData'] = DB::select($SQL, array());
        //dd( $aReturn);    

        # Coupons Details
        $SQL14 = "SELECT COUNT(ac.coupon_id) AS CouponCount,(SELECT id FROM event_coupon WHERE id=ac.coupon_id) AS coupon_id, (SELECT discount_name FROM event_coupon WHERE id=ac.coupon_id) AS DiscountName, (SELECT discount_code FROM event_coupon_details WHERE event_coupon_id=ac.coupon_id) AS DiscountCode, (SELECT no_of_discount FROM event_coupon_details WHERE event_coupon_id=coupon_id) AS TotalDiscountCode
        FROM applied_coupons as ac
        WHERE 1=1";
        if ($aReturn['search_filter'] !== "") {
            if (isset($StartDate) && isset($EndDate)) {
                $SQL14 .= " AND created_at BETWEEN " . $StartDate . " AND " . $EndDate;
            }
        }
        if (!empty($aReturn['search_category'])) {
            $SQL14 .= ' AND ticket_ids =' . $aReturn['search_category'];
        }
        if (!empty( $aReturn['search_from_date']) && !empty($aReturn['search_to_date'] )) {
            $SQL14 .= ' AND created_at BETWEEN ' . $aReturn['search_from_date'] . ' AND ' . $aReturn['search_to_date'] ;
        }

        if (!empty( $aReturn['search_event_name'] )) {
            $SQL14 .= ' AND event_id =' .  $aReturn['search_event_name'] ;
        }
        $SQL14 .= " GROUP BY coupon_id";
        $CouponCodes = DB::select($SQL14, array());
        if (!empty($CouponCodes)) {
            foreach ($CouponCodes as $res) {
                if (empty($res->coupon_id)) {
                    $res->CouponCount = 0; // Default value if coupon_id is missing
                    continue;
                }
                $SQL15 = "SELECT ac.id,COUNT(ac.coupon_id) AS Coupon_Count
                        FROM applied_coupons as ac left join event_booking as eb on eb.id=ac.booking_id 
                        WHERE ac.coupon_id = " . $res->coupon_id . " AND eb.transaction_status IN (1,3) ";
                $aResult = DB::select($SQL15, array());
                //dd($aResult);

                $res->CouponCount = !empty($aResult) ? $aResult[0]->Coupon_Count : $res->CouponCount;
            }

        }
        $aReturn['CouponCodes'] = $CouponCodes;
        // dd( $aReturn['CouponCodes'] );

        // male female graph
        $SQL16 = "SELECT a.ticket_id,a.attendee_details,b.event_id
        FROM attendee_booking_details AS a 
        LEFT JOIN booking_details AS b ON b.id=a.booking_details_id
        LEFT JOIN event_booking AS e ON b.booking_id = e.id
        WHERE e.transaction_status IN (1,3)";
        if ($aReturn['search_filter'] !== "") {
            if (isset($StartDate) && isset($EndDate)) {
                $SQL16 .= " AND b.booking_date BETWEEN " . $StartDate . " AND " . $EndDate;
            }
        }

        if (!empty($aReturn['search_category'])) {
            $SQL16 .= ' AND b.ticket_id =' . $aReturn['search_category'];
        }
        if (!empty($aReturn['search_from_date']) && !empty($aReturn['search_to_date'])) {
            $SQL16 .= ' AND b.booking_date BETWEEN ' . $aReturn['search_from_date'] . ' AND ' . $aReturn['search_to_date'];
        }

        if (!empty($aReturn['search_event_name'])) {
            $SQL16 .= ' AND b.event_id =' . $aReturn['search_event_name'];
        }
       

        $PieChartData = DB::select($SQL16, array());
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


        $aReturn['maleCount'] = $maleCount;
        $aReturn['femaleCount'] = $femaleCount;
        $aReturn['otherCount'] = $otherCount;
        // $aReturn['ageCategoryCount'] = $ageCategoryCount;
        $aReturn['ageCategory'] = $result;
        // dd($aReturn);

        // $sql17 = "SELECT e.event_id,e.cart_details FROM attendee_booking_details AS a 
        // LEFT JOIN booking_details AS b ON a.booking_details_id = b.id
        // LEFT JOIN event_booking AS e ON b.booking_id = e.id";

        $sql17 = "SELECT e.cart_details, e.booking_pay_id, e.total_amount, e.transaction_status, b.ticket_amount, b.event_id, a.id AS aId, a.ticket_id, a.bulk_upload_flag, a.cart_detail
        FROM 
            attendee_booking_details a
        LEFT JOIN 
            booking_details AS b ON a.booking_details_id = b.id
        INNER JOIN 
            event_booking AS e ON b.booking_id = e.id
        LEFT JOIN 
            booking_payment_details bpd ON bpd.id = e.booking_pay_id    
        WHERE 1=1";
        
        $sql17 .= " AND e.transaction_status IN(1,3)";

        if (!empty($aReturn['search_event_name'])) {
            $sql17 .= ' AND b.event_id = ' . $aReturn['search_event_name'];
        }
       
        if ($aReturn['search_filter'] !== "") {
            if (!empty($StartDate) && empty($EndDate)) {
                $sql17 .= " AND b.booking_date >= " . $StartDate;
            }
            if (empty($StartDate) && !empty($EndDate)) {
                $sql17 .= " AND b.booking_date <= " . $EndDate;
            }
            if (isset($StartDate) && isset($EndDate)) {
                $sql17 .= " AND b.booking_date BETWEEN " . $StartDate . " AND " . $EndDate;
            }
        }

        if (!empty($aReturn['search_category'])) {
            $sql17 .= ' AND b.ticket_id =' . $aReturn['search_category'];
        }
        if (!empty($aReturn['search_from_date']) && !empty($aReturn['search_to_date'])) {
            $sql17 .= ' AND b.booking_date BETWEEN ' . $aReturn['search_from_date'] . ' AND ' . $aReturn['search_to_date'];
        }

        $sql17 .= " ORDER BY a.id DESC";
        // dd($sql17);
        $AttendeeData = DB::select($sql17, array());
        // dd($AttendeeData);
        
        $Applied_Coupon_Amount = $Organiser_amount = $Payment_Gateway_GST = $Payment_gateway_charges = $total_payment_gateway = $Platform_fee = $Platform_Fee_GST = $Convenience_fee = $Convenience_Fee_GST = $Final_total_amount = 0;
                
        $card_details_array = []; $cart_details_array = [];
      
        if(!empty($AttendeeData)){
            foreach($AttendeeData as $res){
                
                //----- simple booking
                $card_details_array = isset($res->cart_details) && !empty($res->cart_details) ? json_decode($res->cart_details) : [];
                // dd($card_details_array);
                if(!empty($card_details_array) && $res->bulk_upload_flag == 0){
                    foreach($card_details_array as $details){
                        if (!isset($details->to_organiser)) {
                            $details->to_organiser = 0; // Initialize it with a default value
                        }
                        if (!isset($details->early_bird)) {
                            $details->early_bird = 0; // Initialize it with a default value
                        }

                        if($res->ticket_id == $details->id){
                    
                            // Applied Coupon Amount
                            $Applied_Coupon_Amount = isset($details->appliedCouponAmount) && !empty($details->appliedCouponAmount) ? ($details->appliedCouponAmount)  : 0;  

                        
                            if(!empty($details->OrgPayment) && $details->OrgPayment != "NaN"){
                                $details->to_organiser = isset($details->OrgPayment) && !empty($details->OrgPayment) && $res->transaction_status == 1 ? floatval($details->OrgPayment) : 0;
                            }else{
                                if($details->early_bird == 1 && !empty($details->discount_value) && $res->transaction_status == 1){
                                    $details->to_organiser = (floatval($details->to_organiser) - $details->discount_value);
                                } 
                            }

                            if(isset($details->appliedCouponAmount) && !empty($details->appliedCouponAmount)){
                                $Organiser_amount += isset($details->to_organiser) && !empty($details->to_organiser) ? ($details->to_organiser - $details->appliedCouponAmount) : 0;
                            }else{
                                $Organiser_amount += isset($details->to_organiser) && !empty($details->to_organiser) ? $details->to_organiser : 0;
                            }

                            $Payment_Gateway_GST += isset($details->Payment_Gateway_GST_18) && !empty($details->Payment_Gateway_GST_18) && !empty($res->total_amount) && $res->transaction_status == 1 ? floatval($details->Payment_Gateway_GST_18)  : 0;
                            
                            $Payment_gateway_charges += isset($details->Payment_Gateway_Charges) && !empty($details->Payment_Gateway_Charges) && !empty($res->total_amount) && $res->transaction_status == 1 ? floatval($details->Payment_Gateway_Charges)  : 0;
                        
                            $Platform_fee += isset($details->Platform_Fee) && !empty($details->Platform_Fee) && $res->transaction_status == 1 ? ($details->Platform_Fee)  : 0;

                            $Platform_Fee_GST += isset($details->Platform_Fee_GST_18) && !empty($details->Platform_Fee_GST_18) && $res->transaction_status == 1 ? floatval($details->Platform_Fee_GST_18)  : 0;

                            $Convenience_fee += isset($details->Convenience_Fee) && !empty($details->Convenience_Fee) && $res->transaction_status == 1 ? floatval($details->Convenience_Fee)  : 0;

                            $Convenience_Fee_GST += isset($details->Convenience_Fee_GST_18) && !empty($details->Convenience_Fee_GST_18) && $res->transaction_status == 1 ? floatval($details->Convenience_Fee_GST_18)  : 0;

                            //----------- Final total amount
                            if(isset($details->Extra_Amount_Payment_Gateway_Gst) && !empty($details->Extra_Amount_Payment_Gateway_Gst) && !empty($details->Extra_Amount_Payment_Gateway) && !empty($details->Extra_Amount) && !empty($res->total_amount)){
                            
                                $Final_total_amount += isset($details->BuyerPayment) && !empty($details->BuyerPayment)
                                    ? (floatval($details->BuyerPayment)) +
                                    floatval($details->Extra_Amount_Payment_Gateway_Gst) +
                                    floatval($details->Extra_Amount_Payment_Gateway) +
                                    floatval($details->Extra_Amount)
                                    : '0.00';
                            }else{
                                $Final_total_amount += isset($details->BuyerPayment) && !empty($details->BuyerPayment) && !empty($details->Extra_Amount) && !empty($res->total_amount) ? ($details->BuyerPayment + floatval($details->Extra_Amount))  : '0.00';
                            }   

                        }
                    }
                }
           
                //----- bulk upload
                $cart_details_array = isset($res->cart_detail) && !empty($res->cart_detail) ? json_decode($res->cart_detail) : [];
                 // dd($cart_details_array);
                 if(!empty($cart_details_array) && $res->bulk_upload_flag == 1 && !empty($res->total_amount)){
                    $Payment_Gateway_GST += isset($cart_details_array->Payment_Gateway_GST) && !empty($cart_details_array->Payment_Gateway_GST) ? floatval($cart_details_array->Payment_Gateway_GST)  : 0;
                            
                    $Payment_gateway_charges += isset($cart_details_array->Payment_gateway_charges) && !empty($cart_details_array->Payment_gateway_charges) ? floatval($cart_details_array->Payment_gateway_charges)  : 0;

                    $Platform_fee     += isset($cart_details_array->Platform_fee) ? floatval($cart_details_array->Platform_fee) : 0;
                    $Platform_Fee_GST += isset($cart_details_array->Platform_Fee_GST) ? floatval($cart_details_array->Platform_Fee_GST) : 0;

                    $Convenience_fee     += isset($cart_details_array->Convenience_fee) ? floatval($cart_details_array->Convenience_fee) : 0;
                    $Convenience_Fee_GST += isset($cart_details_array->Convenience_Fee_GST) ? floatval($cart_details_array->Convenience_Fee_GST) : 0;
                    $Organiser_amount += isset($cart_details_array->Organiser_amount) ? floatval($cart_details_array->Organiser_amount) : 0;    
                    $Final_total_amount += isset($cart_details_array->Final_total_amount) && !empty($cart_details_array->Final_total_amount) ? floatval($cart_details_array->Final_total_amount)  : 0;      
                }
                // dd($TotalBuyerAmount);
            }
        }
        
        // dd($Final_total_amount);
        // dd($Payment_Gateway_GST,$Payment_gateway_charges);
        $total_payment_gateway = ($Payment_Gateway_GST + $Payment_gateway_charges);
        $total_convenience_fee = ($Platform_fee + $Platform_Fee_GST + $Convenience_fee + $Convenience_Fee_GST);
           
       // dd($total_convenience_fee);
        // $aReturn['OrganiserAmount'] = !empty($Organiser_amount) ? number_format($Organiser_amount,2) : 0;
        $aReturn['OrganiserAmount'] = !empty($Organiser_amount) ? $numberFormate->formatInIndianCurrency($Organiser_amount) : 0;
        $aReturn['TotalPaymentGateway'] = !empty($total_payment_gateway) ? $numberFormate->formatInIndianCurrency($total_payment_gateway) : 0;
        $aReturn['TotalConvenience'] = !empty($total_convenience_fee) ? $numberFormate->formatInIndianCurrency($total_convenience_fee) : 0;
        
        $aReturn['NetEarningAmt'] = !empty($Final_total_amount) ? $numberFormate->formatInIndianCurrency($Final_total_amount) : 0;

        //-------------- Daily Category Count 

            $FinalBarChartData = [];
            $start_date_time = $end_date_time = "";
            if ($aReturn['search_filter'] != "") {
                $start_date_time = strtotime(date('Y-m-d 00:00:00', strtotime('monday this week')));
                $end_date_time = strtotime(date('Y-m-d 23:59:59', strtotime('sunday this week')));
            }
            if ($aReturn['search_filter'] != "") {

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
                    WHERE e.transaction_status IN (1,3) AND b.booking_date BETWEEN :strtotime_start_today AND :strtotime_end_today ";
                    
                    if (!empty($aReturn['search_event_name'])) {
                        $sSQL .= ' AND b.event_id =' . $aReturn['search_event_name'];
                    }

                    $params = array('strtotime_start_today' => $strtotime_start_today, 'strtotime_end_today' => $strtotime_end_today);
                    $BarChartData = DB::select($sSQL, $params);

                    // dd($BarChartData);
                    $FinalBarChartData[$key] = ["date" => $dates, "count" => count($BarChartData) > 0 ? (int) $BarChartData[0]->TicketCount : 0];
                }
            }
            // dd($FinalBarChartData);
           
            $aReturn['FinalBarChartDateData'] = [];
            $aReturn['FinalBarChartCountData'] = [];
            if(!empty($FinalBarChartData)){
                $DateArray = array_column($FinalBarChartData,'date');
                $CountArray = array_column($FinalBarChartData,'count');

                $quotedDates = array_map(function($date) {
                    return '"' . $date . '"';
                }, $DateArray);

                $aReturn['FinalBarChartDateData'] = implode(', ', $quotedDates);
                $aReturn['FinalBarChartCountData'] = implode(', ', $CountArray);
                // dd($aReturn['FinalBarChartCountData']);
            }

        // -------------------------------- Utm code
            $SQL3 = "SELECT e.utm_campaign, SUM(b.quantity) AS total_quantity
                    FROM booking_details AS b 
                    LEFT JOIN event_booking AS e ON b.booking_id = e.id
                    WHERE e.transaction_status IN (1, 3) 
                    AND e.utm_campaign IS NOT NULL
                    AND e.utm_campaign <> ''";
           
            if ($aReturn['search_filter'] !== "") {
                if (isset($StartDate) && isset($EndDate)) {
                    $SQL3 .= " AND b.booking_date BETWEEN " . $StartDate . " AND " . $EndDate;
                }
            }
           
            if (!empty($aReturn['search_from_date']) && !empty($aReturn['search_to_date'])) {
                $SQL16 .= ' AND b.booking_date BETWEEN ' . $aReturn['search_from_date'] . ' AND ' . $aReturn['search_to_date'];
            }

            if (!empty($aReturn['search_category'])) {
                $SQL3 .= ' AND b.ticket_id =' . $aReturn['search_category'];
            }

            if (!empty($FromDate) && !empty($ToDate)) {
                $SQL3 .= ' AND b.booking_date BETWEEN ' . $FromDate . ' AND ' . $ToDate;
            }

            if (!empty($aReturn['search_event_name'])) {
                $SQL3 .= ' AND b.event_id =' . $aReturn['search_event_name'];
            }

            $SQL3 .= " GROUP BY e.utm_campaign";

            $utmCode = DB::select($SQL3, array());
            $aReturn['utmCode'] = $utmCode;
            // dd($aReturn['utmCode']);

        // -------------------------------- Dyanamic question table

            $SQL5 = "SELECT GROUP_CONCAT(id SEPARATOR ', ') AS Ids,GROUP_CONCAT(general_form_id SEPARATOR ', ') AS GIds FROM event_form_question WHERE is_custom_form=:is_custom_form ";
            $EventFormQuestions = DB::select($SQL5, array('is_custom_form' => 0));

            if (!empty($aReturn['search_event_name'])) {
                $SQL5 .= ' AND event_id =' . $aReturn['search_event_name'];
            }else{
                $SQL5 .= ' AND 1=2';
            }

            $QuestionIds = (count($EventFormQuestions) > 0) ? $EventFormQuestions[0]->Ids : "";
            // dd($QuestionIds);
            $SQL6 = "SELECT a.ticket_id,a.attendee_details,b.event_id
                FROM attendee_booking_details AS a 
                LEFT JOIN booking_details AS b ON b.id=a.booking_details_id
                LEFT JOIN event_booking AS e ON b.booking_id = e.id
                WHERE e.transaction_status IN (1,3)";

                if (!empty($aReturn['search_event_name'])) {
                    $SQL6 .= ' AND b.event_id =' . $aReturn['search_event_name'];
                }else{
                    $SQL6 .= ' AND 1=2';
                }
                
                if ($aReturn['search_filter'] !== "") {
                    if (isset($StartDate) && isset($EndDate)) {
                        $SQL6 .= " AND b.booking_date BETWEEN " . $StartDate . " AND " . $EndDate;
                    }
                }
                if (!empty($aReturn['search_category'])) {
                    $SQL6 .= ' AND b.ticket_id =' . $aReturn['search_category'];
                }
                if (!empty($FromDate) && !empty($ToDate)) {
                    $SQL6 .= ' AND b.booking_date BETWEEN ' . $FromDate . ' AND ' . $ToDate;
                }
             
                $CustomQue = DB::select($SQL6, array());
                // dd($CustomQue);
                $CustomQuestions = [];
                $questionIdsArray = explode(',', $QuestionIds);
 
                if(!empty($CustomQue)){
                    foreach ($CustomQue as $key => $value) {
                        $attendee_details = json_decode(json_decode($value->attendee_details));
                        if(!empty($attendee_details)){
                            foreach ($attendee_details as $key => $attendee) {
                                if (isset($attendee->id) && in_array($attendee->id, $questionIdsArray)) {
                                    if (!empty($attendee->ActualValue) && !empty($attendee->question_form_option)) {
                                        $question_form_option = json_decode($attendee->question_form_option);
                                        $CustomQuestions[$attendee->id][] = $attendee;
                                    }
                                }
                            }   
                        }
                       
                    }
                }
                $CountArray = array(); $result = [];

                if(!empty($CustomQuestions)){
                    foreach ($CustomQuestions as $key => $items) {
                        foreach ($items as $item) {

                            $actualValue = $item->ActualValue;
                            $question_label = $item->question_label;
                            $options = json_decode($item->question_form_option, true);
                          
                            $label = ""; $limit = ""; $limit_flag = false; $labels = [];
                            foreach ($options as $option) {
                                
                                if (in_array($option['id'], explode(',', $actualValue))) {
                                    $labels[] = $option['label'];
                                    $limit_flag = true;
                                }
                                $label = implode(', ', $labels);
                            }

                            if (!isset($CountArray[$key])) {
                                $CountArray[$key] = [
                                    "question_label" => $question_label
                                ];
                            }
                    
                            if (!isset($CountArray[$key][$actualValue])) {
                                $CountArray[$key][$actualValue] = ["label" => $label, "count" => 0, "limit" => $limit];
                            }
                          
                            $CountArray[$key][$actualValue]["count"]++;
                        }//die;
                   }
                }

                $aReturn['CountArray'] = $CountArray;
               // dd($aReturn['CountArray']);

                // Remittance Details
                $s_sql15 = 'SELECT remittance_name,remittance_date,gross_amount,Sgst,Cgst,Igst,deductions,Tds,amount_remitted,event_id,(SELECT name FROM events as e where e.id =rm.event_id ) AS event_name FROM remittance_management rm where 1=1';

                if (!empty($aReturn['search_event_name'])) {
                    $s_sql15 .= ' AND rm.event_id =' . $aReturn['search_event_name'];
                }else{
                    $s_sql15 .= ' AND 1=2';
                }

                $aReturn['Remittance_details'] = DB::select($s_sql15);
 
                // Marketing Details    
                $s_sql16 = 'SELECT campaign_name,campaign_type,count,start_date,end_date,event_id,(SELECT name FROM events e WHERE m.event_id = e.id) As event_name FROM marketing m where 1=1';

                if (!empty($aReturn['search_event_name'])) {
                    $s_sql16 .= ' AND m.event_id =' . $aReturn['search_event_name'];
                }else{
                    $s_sql16 .= ' AND 1=2';
                }

                $aReturn['Marketing_details'] = DB::select($s_sql16);
 
        return view('dashboard.admin_dashboard', $aReturn);
    }

    public function db_backup(){
        $databaseType = 'mysql';
        $FileName = 'RacesBackup_' . date('Y_m_d_h_i_s') . '.sql';
        
         // Path where the backup will be saved
        $backupPath = public_path("/database_backup/{$FileName}");
        // dd($backupPath);
        // $backupPath  = asset()
        // Get the database credentials from the .env file
        $DB_USERNAME = env('DB_USERNAME', 'root');
        $DB_PASSWORD = env('DB_PASSWORD', 'your_db_password'); // Default to your password
        $DB_DATABASE = env('DB_DATABASE', 'your_database_name');
       
        if ($databaseType === 'mysql') {
            
            exec("mysqldump -u {$DB_USERNAME} -p{$DB_PASSWORD} {$DB_DATABASE} > {$backupPath}");
        } elseif ($databaseType === 'pgsql') {
            exec("pg_dump -U your_db_user your_database > {$backupPath}");
        }
        // Return the backup file as a download and delete it afterward
        return response()->download($backupPath, $FileName)->deleteFileAfterSend();
    }

    public function sidebarajax(Request $request,$user_id = 0){
        // dd($request->all());
        $sql = 'SELECT toggle_sidebar FROM users Where id = '.$user_id;
        $a_return = DB::select($sql,array());
        // dd($a_return[0]->toggle_sidebar);
        if($a_return[0]->toggle_sidebar == 1){
            $status  = 0;
        }else{
            $status = 1;
        }
        // dd( $status);
        if($request->user_id > 0){
            $sql = 'UPDATE users SET toggle_sidebar = :toggle_sidebar WHERE id = :user_id';
            $a_return = DB::update( $sql,
            array( 'toggle_sidebar' => $status,'user_id' => $request->user_id ));
            // dd( $a_return);
        } 
        return $a_return;
    }

    public function EventInfoStatus(Request $request ,$event_id,$flag){
        
        session(['event_info_status' => $flag]);
        session(['name' => $event_id]);
        return redirect('/event');
    }
}
