<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

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


        $aReturn['search_filter'] = (!empty(session('filter'))) ? session('filter') : '';
        $aReturn['search_category'] = (!empty(session('category'))) ? session('category') : '';
        $aReturn['search_event_name'] = (!empty(session('event_name'))) ? session('event_name') : '';
        $aReturn['search_from_date'] = (!empty(session('from_date'))) ? strtotime(session('from_date')) : '';
        $aReturn['search_to_date'] = (!empty(session('to_date'))) ? strtotime(session('to_date')) : '';

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

        //Net Sales && Total Participants
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


        #Registration && Net Earnings
        $SQL2 = "SELECT COUNT(e.id) AS TotalRegistration,SUM(e.total_amount) AS TotalAmount FROM event_booking AS e 
        LEFT JOIN booking_details AS b ON b.booking_id = e.id
        WHERE e.transaction_status IN (1,3)";
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
            $SQL2 .= ' AND e.event_id =' . $aReturn['search_event_name'];
        }
        $TotalRegistration = DB::select($SQL2, array());
        $aReturn['TotalRegistration'] = (count($TotalRegistration) > 0) ? $TotalRegistration[0]->TotalRegistration : 0;
        $aReturn['TotalAmount1'] = (count($TotalRegistration) > 0) ? $TotalRegistration[0]->TotalAmount : 0;
        $aReturn['TotalAmount'] = number_format($aReturn['TotalAmount1'], 2);
        // dd($aReturn['TotalAmount']);

        // Total Registration Users
        $SQL5 = "SELECT COUNT(id) AS TotalRegistrationUsers FROM event_booking WHERE 1=1";
        if (!empty($aReturn['search_event_name'])) {
            $SQL5 .= ' AND event_id =' . $aReturn['search_event_name'];
        }
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
        $TotalRegistrationUsersWithSuccess = DB::select($SQL6, array());
        $TotalRegistrationUsersWithSuccessCount = (count($TotalRegistrationUsersWithSuccess) > 0) ? $TotalRegistrationUsersWithSuccess[0]->TotalRegistrationUsersWithSuccess : 0;

        // Calculate percentage
        $percentage = ($TotalRegistrationUsersWithSuccessCount > 0 && $TotalRegistrationCount > 0) ?
            round(($TotalRegistrationUsersWithSuccessCount / $TotalRegistrationCount) * 100, 2) : 0;

        $aReturn['TotalRegistrationCount'] = $TotalRegistrationCount;
        $aReturn['TotalRegistrationUsersWithSuccess'] = $TotalRegistrationUsersWithSuccessCount;
        $aReturn['SuccessPercentage'] = $percentage;
        // dd($aReturn['SuccessPercentage']);

        // PAGE VIEWS
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


        #Total Number Of Events
        $SQL9 = 'SELECT Count(id) as TotalEventCount FROM events WHERE active = 1 AND deleted = 0';
        if (!empty($aReturn['search_event_name'])) {
            $SQL9 .= ' AND id =' . $aReturn['search_event_name'];
        }
        $TotalNumberEvents = DB::select($SQL9, array());
        $aReturn['TotalNumberEvents'] = (count($TotalNumberEvents) > 0) ? $TotalNumberEvents[0]->TotalEventCount : 0;
        // dd(   $aReturn['TotalNumberEvents'] );

        #Live Events
        $SQL10 = 'SELECT Count(id) as TotalLiveEventCount FROM events WHERE active = 1 AND event_info_status = 1';
        if (!empty($aReturn['search_event_name'])) {
            $SQL10 .= ' AND id =' . $aReturn['search_event_name'];
        }
        $TotalNumberLiveEvents = DB::select($SQL10, array());
        $aReturn['TotalNumberLiveEvents'] = (count($TotalNumberLiveEvents) > 0) ? $TotalNumberLiveEvents[0]->TotalLiveEventCount : 0;
        // dd(    $aReturn['TotalNumberLiveEvents']  );

        #Draft Events
        $SQL10 = 'SELECT Count(id) as TotalDraftEventCount FROM events WHERE active = 1 AND event_info_status = 3';
        if (!empty($aReturn['search_event_name'])) {
            $SQL10 .= ' AND id =' . $aReturn['search_event_name'];
        }
        $TotalNumberDraftEvents = DB::select($SQL10, array());
        $aReturn['TotalNumberDraftEvents'] = (count($TotalNumberDraftEvents) > 0) ? $TotalNumberDraftEvents[0]->TotalDraftEventCount : 0;
        // dd(    $aReturn['TotalNumberDraftEvents']  );

        #Private Events
        $SQL10 = 'SELECT Count(id) as TotalPrivateEventCount FROM events WHERE active = 1 AND event_info_status = 2';
        if (!empty($aReturn['search_event_name'])) {
            $SQL10 .= ' AND id =' . $aReturn['search_event_name'];
        }
        $TotalNumberPrivateEvents = DB::select($SQL10, array());
        $aReturn['TotalNumberPrivateEvents'] = (count($TotalNumberPrivateEvents) > 0) ? $TotalNumberPrivateEvents[0]->TotalPrivateEventCount : 0;
        // dd(    $aReturn['TotalNumberPrivateEvents']  );

        #Total Number Users
        $SQL11 = 'SELECT Count(id) as TotalUserCount FROM users WHERE is_active = 1 AND is_deleted = 0';
        $TotalNumberUsers = DB::select($SQL11, array());
        $aReturn['TotalNumberUsers'] = (count($TotalNumberUsers) > 0) ? $TotalNumberUsers[0]->TotalUserCount : 0;
        // dd(   $aReturn['TotalNumberUsers'] );

        #Remitted Amount
        $SQL12 = 'SELECT sum(amount_remitted) as amount_remitted FROM remittance_management WHERE active = 1';
        $TotalRemittedAmount1 = DB::select($SQL12, array());
        $TotalRemittedAmount2 = (count($TotalRemittedAmount1) > 0) ? $TotalRemittedAmount1[0]->amount_remitted : 0;
        ;
        $aReturn['TotalRemittedAmount'] = number_format($TotalRemittedAmount2, 2);
        //dd($aReturn['TotalRemittedAmount']);

        // payment count
        $sql17 = "SELECT count(p.id) AS paymentId FROM booking_payment_details AS p 
        LEFT JOIN users AS u ON u.id=p.created_by
        WHERE 1=1";
        if (!empty($aReturn['search_event_name'])) {
            $sql17 .= ' AND p.event_id =' . $aReturn['search_event_name'];
        }
        $sql17 .= ' ORDER BY p.id DESC';
        $PaymentData = DB::select($sql17, array());
    
        $aReturn['PaymentData'] = (count($PaymentData) > 0) ? $PaymentData[0]->paymentId : [];
        // dd( $aReturn['PaymentData']);


        #Category Booking Data bar chart table details and barchart details
        $SQL13 = "SELECT b.ticket_id,b.event_id,e.booking_date,SUM(b.quantity) AS TicketCount,(SELECT ticket_name FROM event_tickets WHERE id=b.ticket_id) AS TicketName,(SELECT total_quantity FROM event_tickets WHERE id=b.ticket_id) AS total_quantity,(SELECT ticket_price FROM event_tickets WHERE id=b.ticket_id) AS TicketPrice
        FROM booking_details AS b
        LEFT JOIN event_booking AS e ON b.booking_id = e.id
        WHERE e.transaction_status IN (1,3)";
        if ($aReturn['search_filter'] !== "") {
            if (isset($StartDate) && isset($EndDate)) {
                $SQL13 .= " AND b.booking_date BETWEEN " . $StartDate . " AND " . $EndDate;
            }
        }
        if (!empty($aReturn['search_category'])) {
            $SQL13 .= ' AND b.ticket_id =' . $aReturn['search_category'];
        }
        if (!empty($aReturn['search_from_date']) && !empty($aReturn['search_to_date'])) {
            $SQL13 .= ' AND b.booking_date BETWEEN ' . $aReturn['search_from_date'] . ' AND ' . $aReturn['search_to_date'];
        }

        if (!empty($aReturn['search_event_name'])) {
            $SQL13 .= ' AND b.event_id =' . $aReturn['search_event_name'];
        } else {
            $SQL13 .= ' AND 1=2';
        }
        $SQL13 .= " GROUP BY b.ticket_id";
        $BookingData = DB::select($SQL13, array());

        foreach ($BookingData as $key => $value) {
            $value->TicketCount = (int) $value->TicketCount;
            $value->TotalTicketPrice = $value->TicketCount * $value->TicketPrice;
            $value->PendingCount = ((int)$value->total_quantity-(int)$value->TicketCount);
            $value->SingleTicketPrice = $value->TicketPrice;
        }
        $aReturn['BookingData'] = (count($BookingData) > 0) ? $BookingData : [];
        // dd( $aReturn['BookingData']);

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
        // $aReturn['ageCategory'] = $result;
        // dd($aReturn['maleCount'], $aReturn['femaleCount'],   $aReturn['otherCount'] ,$aReturn['ageCategoryCount'], $aReturn['ageCategory'] );


        $sql17 = "SELECT e.event_id,e.cart_details FROM attendee_booking_details AS a 
        LEFT JOIN booking_details AS b ON a.booking_details_id = b.id
        LEFT JOIN event_booking AS e ON b.booking_id = e.id";
        
        $sql17 .= " WHERE e.transaction_status IN(1,3)";
       

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

        if (!empty($aReturn['search_event_name'])) {
            $sql17 .= ' AND b.event_id =' . $aReturn['search_event_name'];
        }

        $sql17 .= " ORDER BY a.id DESC";
        // dd($sql);
        $AttendeeData = DB::select($sql17, array());
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
        $aReturn['OrganiserAmount'] = !empty($Organiser_amount) ? $Organiser_amount : 0;
        $aReturn['TotalPaymentGateway'] = !empty($total_payment_gateway) ? $total_payment_gateway : 0;
        $aReturn['TotalConvenience'] = !empty($total_convenience_fee) ? number_format($total_convenience_fee,2) : 0;
       
     

        return view('dashboard.admin_dashboard', $aReturn);
    }
}
