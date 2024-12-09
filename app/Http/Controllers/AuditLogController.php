<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Session;

class AuditLogController extends Controller
{
    public function clear_search()
    {
        session::forget('transaction_id');
        session::forget('email_address_audit');
       
        return redirect('/audit_log');
    }
    public function index(Request $request){
       
        // Booking_payment_details
        $aReturn = array();
        $aReturn['search_transaction_id'] = '';
        $aReturn['search_email_address_audit'] = '';
      


        if (isset($request->form_type) && $request->form_type == 'search_audit_log') {
            session(['transaction_id' => $request->search_transaction_id]);
            session(['email_address_audit' => $request->search_email_address]);

            return redirect('/audit_log');
        }
        $aReturn['search_transaction_id'] = (!empty(session('transaction_id'))) ? session('transaction_id') : '';
        $aReturn['search_email_address_audit'] = (!empty(session('email_address_audit'))) ? session('email_address_audit') : '';
       
        
        if(!empty($aReturn['search_transaction_id'])){
            // Booking Payment Details
            if (!empty( $aReturn['search_transaction_id'])) {
                $sql = 'SELECT bpd.*,(SELECT name FROM events as e where e.id =bpd.event_id ) AS event_name FROM booking_payment_details bpd Where 1=1';
                if (!empty($aReturn['search_transaction_id'])) {
                    $sql .= ' AND LOWER(bpd.txnid) = \'' . strtolower($aReturn['search_transaction_id']) . '\'';
                }
        
                $ad_log_array = DB::select($sql);

                $aReturn["event_name"] =  !empty($ad_log_array[0]) ? $ad_log_array[0]->event_name : '';
                $aReturn["user_name"] =  !empty($ad_log_array[0]) ? $ad_log_array[0]->firstname.' '. $ad_log_array[0]->lastname : '' ;

                $bookingIds = [];
                foreach ($ad_log_array as $booking) {
                    $bookingIds[] = $booking->id;
                }
            // dd($bookingIds);
            
            }else{
                $ad_log_array = [];
            }
            $aReturn["ad_log_array"] = $ad_log_array;
        
        
            // Temp Booking Ticket Details
            if (!empty($bookingIds)) {
                $ids = implode(',', $bookingIds); // Convert array to comma-separated string
            
                $sql2 = "SELECT tbt.*, (SELECT name FROM events AS e WHERE e.id = tbt.event_id) AS event_name 
                        FROM temp_booking_ticket_details tbt 
                        WHERE tbt.booking_pay_id IN ($ids)"; // Apply the WHERE condition
            
                $tem_booking_ticket_array = DB::select($sql2);
            
            } else {
                $tem_booking_ticket_array = []; // Handle case where there are no bookings
            }
            $aReturn["tem_booking_ticket_array"] = $tem_booking_ticket_array;


            // Event Booking Details
            if (!empty($bookingIds)) {
                $ids = implode(',', $bookingIds); // Convert array to comma-separated string
            
                $sql2 = "SELECT eb.*, (SELECT name FROM events AS e WHERE e.id = eb.event_id) AS event_name 
                        FROM event_booking eb
                        WHERE eb.booking_pay_id IN ($ids)"; // Apply the WHERE condition
            
                $event_booking_array = DB::select($sql2);
            
            } else {
                $event_booking_array = []; // Handle case where there are no bookings
            }
            $aReturn["event_booking_array"] = $event_booking_array;
            


            // Booking Details
            $eventBookingIds = [];
            foreach ($event_booking_array as $eventBooking) {
                $eventBookingIds[] = $eventBooking->id;
            }
            if (!empty($eventBookingIds)) {
                $eb_ids = implode(',', $eventBookingIds); // Convert array to comma-separated string
            
                // Second query to fetch booking_details
                $sql3 = "SELECT bd.*, (SELECT name FROM events AS e WHERE e.id = bd.event_id) AS event_name  
                        FROM booking_details bd 
                        WHERE bd.booking_id IN ($eb_ids)";
            
                $booking_details_array = DB::select($sql3);
            } else {
                $booking_details_array = []; // Handle case where there are no event bookings
            } 
            $aReturn["booking_details_array"] = $booking_details_array;
          
        

            // Attendance Booking Details
            $BookingDetailsIds = [];
            foreach ($booking_details_array as $booking_details) {     
                $BookingDetailsIds[] = $booking_details->id;
            }
            if (!empty($BookingDetailsIds)) {
                $bd_ids = implode(',', $BookingDetailsIds); // Convert array to comma-separated string
            
                // Second query to fetch booking_details
                $sql3 = "SELECT abd.* 
                        FROM attendee_booking_details abd 
                        WHERE abd.booking_details_id IN ($bd_ids)";
                if(!empty( $aReturn['search_email_address_audit'])){
                    
                    $sql3 .= ' AND LOWER(abd.email) = \'' . strtolower($aReturn['search_email_address_audit']) . '\'';
                } 
            
                $attendee_booking_details_array = DB::select($sql3);
            
            } else {
                $attendee_booking_details_array = []; // Handle case where there are no event bookings
            } 
            $aReturn["attendee_booking_details_array"] = $attendee_booking_details_array;
            // dd( $aReturn["attendee_booking_details_array"]);


            // Email Log
            $emails = [];
            foreach ($attendee_booking_details_array as $attendee) {
                if (!empty($attendee->email)) {
                    $emails[] = $attendee->email;
                }
            }
            if (!empty($emails)) {
                $email_list = implode(',', array_fill(0, count($emails), '?')); // Prepare placeholders
                $sql = "SELECT * FROM email_log el WHERE el.send_mail_to IN ($email_list)";
                $email_log = DB::select($sql, $emails); // Pass emails as parameters to prevent SQL injection
            } else {
                $email_log = []; // No emails to check
            }
            $aReturn["email_log"] = $email_log;


            // Booking Details
            $event_bid = [];
            foreach ($event_booking_array as $eventBooking) {
                $event_bid[] = $eventBooking->id;
            }
            if (!empty($eventBookingIds)) {
                $event_booking_ids = implode(',', $event_bid); // Convert array to comma-separated string
            
                // Second query to fetch booking_details
                $sql3 = "SELECT ac.*, (SELECT name FROM events AS e WHERE e.id = ac.event_id) AS event_name,(SELECT discount_code FROM event_coupon WHERE id=ac.coupon_id) AS DiscountCode   
                        FROM applied_coupons ac 
                        WHERE ac.booking_id IN ($event_booking_ids)";
            
                $applied_coupon_array = DB::select($sql3);
            } else {
                $applied_coupon_array = []; // Handle case where there are no event bookings
            } 
            $aReturn["applied_coupon_array"] = $applied_coupon_array;
           

        }else{
            // Attendance Booking Details
            if (!empty( $aReturn['search_email_address_audit'])) {
                $sql3 = "SELECT abd.* 
                        FROM attendee_booking_details abd 
                        WHERE 1=1";
                if(!empty( $aReturn['search_email_address_audit'])){    
                    $sql3 .= ' AND LOWER(abd.email) = \'' . strtolower($aReturn['search_email_address_audit']) . '\'';
                } 
                $attendee_booking_details_array = DB::select($sql3);
            } else {
                $attendee_booking_details_array = []; // Handle case where there are no event bookings
            } 
            $aReturn["attendee_booking_details_array"] = $attendee_booking_details_array;


            // Email Log
            $emails = [];
            foreach ($attendee_booking_details_array as $attendee) {
                if (!empty($attendee->email)) {
                    $emails[] = $attendee->email;
                }
            }
            if (!empty($emails)) {
                $email_list = implode(',', array_fill(0, count($emails), '?')); // Prepare placeholders
                $sql = "SELECT * FROM email_log el WHERE el.send_mail_to IN ($email_list)";
                $email_log = DB::select($sql, $emails); // Pass emails as parameters to prevent SQL injection
            } else {
                $email_log = []; // No emails to check
            }
            $aReturn["email_log"] = $email_log;


            // Booking Details
            $BookingDetailsIds = [];
            foreach ($attendee_booking_details_array as $attendee) {
                if (!empty($attendee->booking_details_id)) {
                    $BookingDetailsIds[] = $attendee->booking_details_id;
                }
            }
            if (!empty($BookingDetailsIds)) {
                $bd_ids = implode(',', $BookingDetailsIds); // Convert array to comma-separated string
            
                // Second query to fetch booking_details
                $sql3 = "SELECT bd.*, (SELECT name FROM events AS e WHERE e.id = bd.event_id) AS event_name  
                        FROM booking_details bd 
                        WHERE bd.id IN ($bd_ids)";
            
                $booking_details_array = DB::select($sql3);
            } else {
                $booking_details_array = []; // Handle case where there are no event bookings
            } 
            $aReturn["booking_details_array"] = $booking_details_array;
            // dd($aReturn["booking_details_array"] );


            // Event Booking Details
            $EventDetailIds = [];
            foreach ($booking_details_array as $booking_details) {     
                $EventDetailIds[] = $booking_details->booking_id;
            }
            if (!empty($EventDetailIds)) {
                $ids = implode(',', $EventDetailIds); // Convert array to comma-separated string
            
                $sql2 = "SELECT eb.*, (SELECT name FROM events AS e WHERE e.id = eb.event_id) AS event_name 
                        FROM event_booking eb
                        WHERE eb.id IN ($ids)"; // Apply the WHERE condition
            
                $event_booking_array = DB::select($sql2);
            
            } else {
                $event_booking_array = []; // Handle case where there are no bookings
            }
            $aReturn["event_booking_array"] = $event_booking_array;
            // dd($aReturn["event_booking_array"]);

            // Booking Payment Details
            $BookingPaymentIds = [];
            foreach ($event_booking_array as $eventBooking) {
                $BookingPaymentIds[] = $eventBooking->booking_pay_id;
            }
            if (!empty($BookingPaymentIds)) {
                $Eventids = implode(',', $BookingPaymentIds);
                $sql = "SELECT bpd.*, 
                        (SELECT name FROM events as e WHERE e.id = bpd.event_id) AS event_name 
                    FROM booking_payment_details bpd 
                    WHERE bpd.id IN ($Eventids)";
             
                $ad_log_array = DB::select($sql);


                $aReturn["event_name"] =  !empty($ad_log_array[0]) ? $ad_log_array[0]->event_name : '';
                $aReturn["user_name"] =  !empty($ad_log_array[0]) ? $ad_log_array[0]->firstname.' '. $ad_log_array[0]->lastname : '' ;

                $booking_Ids = [];
                foreach ($ad_log_array as $booking) {
                    $booking_Ids[] = $booking->id;
                }
            
            }else{
                $ad_log_array = [];
            }
            $aReturn["ad_log_array"] = $ad_log_array;
            

            // Temp Booking Ticket Details
            if (!empty($booking_Ids)) {
                $ids = implode(',', $booking_Ids); // Convert array to comma-separated string
            
                $sql2 = "SELECT tbt.*, (SELECT name FROM events AS e WHERE e.id = tbt.event_id) AS event_name 
                        FROM temp_booking_ticket_details tbt 
                        WHERE tbt.booking_pay_id IN ($ids)"; // Apply the WHERE condition
            
                $tem_booking_ticket_array = DB::select($sql2);
            
            } else {
                $tem_booking_ticket_array = []; // Handle case where there are no bookings
            }
            $aReturn["tem_booking_ticket_array"] = $tem_booking_ticket_array;


             // Applied coupons
             $eventbooking_ids = [];
            foreach ($event_booking_array as $eventBooking) {
                $eventbooking_ids[] = $eventBooking->id;
            }
             if (!empty($eventbooking_ids)) {
                $esb_ids = implode(',', $eventbooking_ids); // Convert array to comma-separated string
            // dd($esb_ids);
                $sql2 = "SELECT ac.*, (SELECT name FROM events AS e WHERE e.id = ac.event_id) AS event_name,(SELECT discount_code FROM event_coupon WHERE id=ac.coupon_id) AS DiscountCode 
                        FROM applied_coupons ac 
                        WHERE ac.booking_id IN ($esb_ids)"; // Apply the WHERE condition
            
                $applied_coupon_array = DB::select($sql2);
            
            } else {
                $applied_coupon_array = []; // Handle case where there are no bookings
            }
            $aReturn["applied_coupon_array"] = $applied_coupon_array;

            

    
        } 

    
        return view('audit_log.list', $aReturn);
    } 
}
