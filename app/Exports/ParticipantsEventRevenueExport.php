<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use \stdClass;

class ParticipantsEventRevenueExport implements FromArray,WithStyles, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $eventId;

    public function __construct($eventId)
    {
        $this->eventId = $eventId;
    }

    public function array(): array
    {
        $eventId = $this->eventId;
        //dd($eventId);
        $participant_name = Session::has('participant_name') ? Session::get('participant_name') : '';
        $transaction_status = Session::has('transaction_status') ? Session::get('transaction_status') : '';
        $registration_id = Session::has('registration_id') ? Session::get('registration_id') : '';
        $mobile_no = Session::has('mobile_no') ? Session::get('mobile_no') : '';
        $email_id = Session::has('email_id') ? Session::get('email_id') : '';
        $category = Session::has('category') ? Session::get('category') : '';
        $start_booking_date = Session::has('start_booking_date') ? Session::get('start_booking_date') : '';
        $end_booking_date = Session::has('end_booking_date') ? Session::get('end_booking_date') : '';
        $event_name = Session::has('event_name') ? Session::get('event_name') : '';

        // Build the SQL query with search criteria
        // $sSQL = 'SELECT a.*,e.booking_date, e.cart_details,e.booking_pay_id,e.total_amount, e.transaction_status, b.ticket_amount, b.event_id,a.id AS aId, CONCAT(a.firstname, " ", a.lastname) AS user_name,
        // (SELECT et.ticket_name FROM event_tickets et WHERE et.id = a.ticket_id) AS category_name,
        // (SELECT bpd.txnid FROM booking_payment_details bpd WHERE bpd.id = e.booking_pay_id) AS Transaction_order_id,
        // (SELECT bpt.mihpayid FROM booking_payment_log bpt WHERE bpt.txnid = Transaction_order_id) AS payu_id
        // FROM attendee_booking_details a
        // LEFT JOIN booking_details AS b ON a.booking_details_id = b.id
        // Inner JOIN event_booking AS e ON b.booking_id = e.id
        // WHERE 1=1';

        $sSQL = "SELECT a.*, e.booking_date, e.cart_details, e.booking_pay_id, e.total_amount, e.transaction_status, b.ticket_amount, b.event_id, a.id AS aId, 
        CONCAT(a.firstname, ' ', a.lastname) AS user_name,
        (bpd.txnid) as Transaction_order_id,
        bpd.bulk_upload_group_name,
        (SELECT et.ticket_name FROM event_tickets et WHERE et.id = a.ticket_id) AS category_name,(SELECT ticket_status FROM event_tickets WHERE id=a.ticket_id) AS ticket_status
        FROM 
            attendee_booking_details a
        LEFT JOIN 
            booking_details AS b ON a.booking_details_id = b.id
        INNER JOIN 
            event_booking AS e ON b.booking_id = e.id
        LEFT JOIN 
            booking_payment_details bpd ON bpd.id = e.booking_pay_id    
        WHERE 
        1=1";

       
        // dd($eventId);
        if(isset($eventId) && !empty($eventId)){
            $sSQL .= ' AND b.event_id = '.$eventId;
        }

        // Add conditions based on session data
        if (!empty($participant_name)) {
            $sSQL .= ' AND (LOWER((CONCAT(a.firstname, " ", a.lastname))) LIKE \'%' . strtolower($participant_name) . '%\')';
        }
        if ($transaction_status !== '') {
            $sSQL .= ' AND e.transaction_status = '.$transaction_status;
        }
        if (!empty($registration_id)) {
            $sSQL .= ' AND a.registration_id LIKE \'%' . strtolower($registration_id) . '%\'';
        }
        if (!empty($mobile_no)) {
            $sSQL .= ' AND a.mobile LIKE \'%' . strtolower($mobile_no) . '%\'';
        }
        if (!empty($email_id)) {
            $sSQL .= ' AND a.email LIKE \'%' . strtolower($email_id) . '%\'';
        }
        if (!empty($category)) {
            $sSQL .= ' AND a.ticket_id = (SELECT id FROM event_tickets WHERE ticket_name LIKE \'%' . strtolower($category) . '%\')';
        }
        if (!empty($start_booking_date)) {
            $sSQL .= ' AND e.booking_date >= '. strtotime($start_booking_date);
        }
        if (!empty($end_booking_date)) {
            $sSQL .= ' AND e.booking_date <= '. strtotime($end_booking_date);
        }
        
        // if (!empty($event_name)) {
        //     $sSQL .= ' AND b.event_id = '.$event_name;
        // }

        $sSQL .= ' ORDER BY a.id DESC';
        // dd($sSQL);
        
        $event_participants = DB::select($sSQL, array());
        // dd($event_participants);
        $excelData = [];
        if (!empty($event_participants)) {
            foreach ($event_participants as $key => $res) {

                $sql = "SELECT txnid,payment_status,(select mihpayid from booking_payment_log where booking_payment_details.id = booking_det_id) as mihpayid FROM booking_payment_details WHERE id =:booking_pay_id ";
                $paymentDetails = DB::select($sql, array('booking_pay_id' => $res->booking_pay_id));
               
                // $tran_id = !empty($paymentDetails) ? $paymentDetails[0]->txnid : '';
                $payment_status = !empty($paymentDetails) ? $paymentDetails[0]->payment_status : '';
                $mihpayid = !empty($paymentDetails) ? $paymentDetails[0]->mihpayid : '';
                $txnid = !empty($paymentDetails) ? $paymentDetails[0]->txnid : '';

                $card_details_array = json_decode($res->cart_details);
             

                $aTemp = new stdClass;
                $aTemp->firstname = $res->firstname;
                $aTemp->lastname = $res->lastname;
                $aTemp->email = $res->email;
                $aTemp->registration_id = $res->registration_id;
                $aTemp->booking_date = $res->booking_date;
                $aTemp->payu_id = $mihpayid;
                $aTemp->payment_status = $payment_status;
                $aTemp->transaction_id = $txnid;
                $aTemp->bulk_upload_group_name = $res->bulk_upload_group_name;
                $aTemp->mobile = $res->mobile;
               
                $aTemp->category_name = $res->category_name; 

                $cart_details_array = isset($res->cart_detail) && !empty($res->cart_detail) ? json_decode($res->cart_detail) : [];
                // dd($cart_details_array);
               
                $ticket_count = $ExcPriceTaxesStatus = $WhoPayYtcrFee = $WhoPayPaymentGatewayFee = 0;
                if(!empty($card_details_array)){
                    foreach($card_details_array as $details){

                        if($res->ticket_id == $details->id){

                            $ExcPriceTaxesStatus = isset($details->ExcPriceTaxesStatus) && !empty($details->ExcPriceTaxesStatus) ? $details->ExcPriceTaxesStatus : 0;
                            $WhoPayYtcrFee = isset($details->player_of_fee) && !empty($details->player_of_fee) ? $details->player_of_fee : 0;
                            $WhoPayPaymentGatewayFee = isset($details->player_of_gateway_fee) && !empty($details->player_of_gateway_fee) ? $details->player_of_gateway_fee : 0;

                            //--------------
                            $ticket_count = $details->count; 
                            // $aTemp->Ticket_count = isset($details->count) && !empty($details->count) ? $details->count : 0;
                            $aTemp->Ticket_count = 1;
                            $aTemp->Single_ticket_price = isset($details->Main_Price) && is_numeric($details->Main_Price) && is_numeric($ticket_count) 
                            ? ($details->Main_Price) 
                            : '0.00';

                            $aTemp->Ticket_price = isset($details->ticket_price) && !empty($details->ticket_price) ? $details->ticket_price : '0.00';
                            $aTemp->Convenience_fee = isset($details->Convenience_Fee) && !empty($details->Convenience_Fee) ? ($details->Convenience_Fee)  : '0.00';
                            $aTemp->Platform_fee = isset($details->Platform_Fee) && !empty($details->Platform_Fee) ? 
                            ($details->Platform_Fee)  : '0.00';
                            $aTemp->Payment_gateway_charges = isset($details->Payment_Gateway_Charges) && !empty($details->Payment_Gateway_Charges) ? ($details->Payment_Gateway_Charges)  : '0.00';

                            //----------- total platform fee
                            if(isset($details->Extra_Amount_Payment_Gateway) && !empty($details->Extra_Amount_Payment_Gateway)){
                                $aTemp->Total_Platform_Fee = isset($details->Total_Platform_Fee) && !empty($details->Total_Platform_Fee) ? (($details->Total_Platform_Fee) + $details->Extra_Amount_Payment_Gateway)  : '0.00';
                            }else{
                                $aTemp->Total_Platform_Fee = isset($details->Total_Platform_Fee) && !empty($details->Total_Platform_Fee) ? ($details->Total_Platform_Fee)  : '0.00';
                            }      
                          
                            $aTemp->Registration_Fee_GST = isset($details->Registration_Fee_GST) && !empty($details->Registration_Fee_GST) ? ($details->Registration_Fee_GST)  : '0.00';
                            $aTemp->Convenience_Fee_GST = isset($details->Convenience_Fee_GST_18) && !empty($details->Convenience_Fee_GST_18) ? ($details->Convenience_Fee_GST_18)  : '0.00';
                            $aTemp->Platform_Fee_GST = isset($details->Platform_Fee_GST_18) && !empty($details->Platform_Fee_GST_18) ? ($details->Platform_Fee_GST_18)  : '0.00';
                            $aTemp->Payment_Gateway_GST = isset($details->Payment_Gateway_GST_18) && !empty($details->Payment_Gateway_GST_18) ? ($details->Payment_Gateway_GST_18)  : '0.00';

                             //----------- total taxes
                            if(isset($details->Extra_Amount_Payment_Gateway_Gst) && !empty($details->Extra_Amount_Payment_Gateway_Gst)){
                                $aTemp->Total_Platform_Fee = isset($details->Total_Taxes) && !empty($details->Total_Taxes) ? (($details->Total_Taxes) + $details->Extra_Amount_Payment_Gateway_Gst)  : '0.00';
                            }else{
                                $aTemp->Total_Platform_Fee = isset($details->Total_Taxes) && !empty($details->Total_Taxes) ? ($details->Total_Taxes)  : '0.00';
                            }   

                            //----------- Final total amount
                            if(isset($details->Extra_Amount_Payment_Gateway_Gst) && !empty($details->Extra_Amount_Payment_Gateway_Gst) && !empty($details->Extra_Amount_Payment_Gateway) && !empty($details->Extra_Amount)){
                                // $aTemp->Final_total_amount = isset($details->BuyerPayment) && !empty($details->BuyerPayment) ? (($details->BuyerPayment) + $details->Extra_Amount_Payment_Gateway_Gst) + ($details->Extra_Amount_Payment_Gateway) + floatval($details->Extra_Amount) : '0.00';

                                $aTemp->Final_total_amount = 
                                isset($details->BuyerPayment) && !empty($details->BuyerPayment)
                                    ? (floatval($details->BuyerPayment)) +
                                      floatval($details->Extra_Amount_Payment_Gateway_Gst) +
                                      floatval($details->Extra_Amount_Payment_Gateway) +
                                      floatval($details->Extra_Amount)
                                    : '0.00';
                            

                            }else{
                                $aTemp->Final_total_amount = isset($details->BuyerPayment) && !empty($details->BuyerPayment) && !empty($details->Extra_Amount) ? ($details->BuyerPayment + floatval($details->Extra_Amount))  : '0.00';
                            }   

                            //---------- Extra Amount
                            $aTemp->Extra_amount = isset($details->Extra_Amount) && !empty($details->Extra_Amount) ? ($details->Extra_Amount)  : 0; 
                            if(isset($details->Extra_Amount_Payment_Gateway) && isset($details->Extra_Amount_Payment_Gateway_Gst) && $details->Extra_Amount_Payment_Gateway_Gst > 0 && $details->Extra_Amount_Payment_Gateway_Gst > 0){

                                $aTemp->Extra_amount_pg_charges = !empty($details->Extra_Amount_Payment_Gateway) ? ($details->Extra_Amount_Payment_Gateway)  : 0;  
                                $aTemp->Extra_amount_pg_GST = !empty($details->Extra_Amount_Payment_Gateway_Gst) ? ($details->Extra_Amount_Payment_Gateway_Gst)  : 0; 
                            }else if(isset($details->Excel_Extra_Amount_Payment_Gateway) && isset($details->Excel_Extra_Amount_Payment_Gateway_Gst)){
                                $aTemp->Extra_amount_pg_charges = !empty($details->Excel_Extra_Amount_Payment_Gateway) ? ($details->Excel_Extra_Amount_Payment_Gateway)  : 0;  
                                $aTemp->Extra_amount_pg_GST = !empty($details->Excel_Extra_Amount_Payment_Gateway_Gst) ? ($details->Excel_Extra_Amount_Payment_Gateway_Gst)  : 0; 
                            }else{
                               $aTemp->Extra_amount_pg_charges = 0;
                               $aTemp->Extra_amount_pg_GST = 0;  
                            }
                            
                            // Applied Coupon Amount
                            $aTemp->Applied_Coupon_Amount = isset($details->appliedCouponAmount) && !empty($details->appliedCouponAmount) ? ($details->appliedCouponAmount)  : '0.00';  
                            $to_organiser_amt = isset($details->to_organiser) && !empty($details->to_organiser) ? ($details->to_organiser + $aTemp->Extra_amount) : 0;
                            
                            if(isset($details->appliedCouponAmount) && !empty($details->appliedCouponAmount) && $details->appliedCouponAmount > 0){
                                $aTemp->Organiser_amount = $to_organiser_amt ?  ($to_organiser_amt - $details->appliedCouponAmount) : 0;
                            }else if(isset($details->Excel_Extra_Amount_Payment_Gateway) && isset($details->Extra_Amount_Payment_Gateway_Gst)){
                                $aTemp->Organiser_amount = isset($to_organiser_amt) && !empty($to_organiser_amt) ? ($to_organiser_amt - $details->Excel_Extra_Amount_Payment_Gateway - $details->Extra_Amount_Payment_Gateway_Gst) : 0;
                            }
                            else{
                                $aTemp->Organiser_amount = isset($to_organiser_amt) && !empty($to_organiser_amt) ? $to_organiser_amt : 0;
                            }
                        }


                    }

                }

                if($res->bulk_upload_flag == 1){
                    $ExcPriceTaxesStatus = isset($cart_details_array->ExcPriceTaxesStatus) ? $cart_details_array->ExcPriceTaxesStatus : 0 ;
                    $WhoPayYtcrFee = isset($cart_details_array->Pass_Bare) ? $cart_details_array->Pass_Bare : 0 ;
                    $WhoPayPaymentGatewayFee = isset($cart_details_array->Pg_Bare) ? $cart_details_array->Pg_Bare : 0 ;

                    $ticket_count = 1;
                    $aTemp->Ticket_count = 1;

                    $aTemp->Single_ticket_price = isset($cart_details_array->Ticket_price) ? floatval($cart_details_array->Ticket_price)  : 0;
                    $aTemp->Ticket_price        = isset($cart_details_array->Ticket_price) ? floatval($cart_details_array->Ticket_price) : 0;
                    $aTemp->Convenience_fee     = isset($cart_details_array->Convenience_fee) ? floatval($cart_details_array->Convenience_fee) : 0;
                    $aTemp->Platform_fee        = isset($cart_details_array->Platform_fee) ? floatval($cart_details_array->Platform_fee) : 0;
                    $aTemp->Payment_gateway_charges = isset($cart_details_array->Payment_gateway_charges) ? floatval($cart_details_array->Payment_gateway_charges) : 0;
                    $aTemp->Registration_Fee_GST = isset($cart_details_array->Registration_Fee_GST) ? floatval($cart_details_array->Registration_Fee_GST) : 0;
                    $aTemp->Convenience_Fee_GST = isset($cart_details_array->Convenience_Fee_GST) ? floatval($cart_details_array->Convenience_Fee_GST) : 0;
                    $aTemp->Platform_Fee_GST = isset($cart_details_array->Platform_Fee_GST) ? floatval($cart_details_array->Platform_Fee_GST) : 0;
                    $aTemp->Payment_Gateway_GST = isset($cart_details_array->Payment_Gateway_GST) ? floatval($cart_details_array->Payment_Gateway_GST) : 0;
                    $aTemp->Final_total_amount = isset($cart_details_array->Final_total_amount) ? floatval($cart_details_array->Final_total_amount) : 0;
                    $aTemp->Extra_amount = isset($cart_details_array->Extra_amount) ? floatval($cart_details_array->Extra_amount) : 0;
                    $aTemp->Extra_amount_pg_charges = isset($cart_details_array->Extra_amount_pg_charges) ? floatval($cart_details_array->Extra_amount_pg_charges) : 0;
                    $aTemp->Extra_amount_pg_GST = isset($cart_details_array->Extra_amount_pg_GST) ? floatval($cart_details_array->Extra_amount_pg_GST) : 0;
                    $aTemp->Applied_Coupon_Amount = isset($cart_details_array->Applied_Coupon_Amount) ? floatval($cart_details_array->Applied_Coupon_Amount) : 0;
                    $aTemp->Organiser_amount = isset($cart_details_array->Organiser_amount) ? floatval($cart_details_array->Organiser_amount) : 0;
                }

                if($ExcPriceTaxesStatus == 1){
                    $aTemp->taxes_status = 'Inclusive';
                }else if($ExcPriceTaxesStatus == 2){
                    $aTemp->taxes_status = 'Exclusive';
                }else{
                    $aTemp->taxes_status = '';
                }

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

                //--------- pending
                if($res->ticket_status == 1){
                   $aTemp->category_type = 'Paid';
                }else if($res->ticket_status == 2){
                   $aTemp->category_type = 'Free';
                }else{
                   $aTemp->category_type = '';
                }
                
                //-----------------------------------------------------
               
                // free ticket
                if(empty($res->total_amount)){
                    $aTemp->Final_total_amount = 0;
                    $aTemp->Organiser_amount = 0;
                    $aTemp->Payment_Gateway_GST = 0;
                    $aTemp->Payment_gateway_charges = 0;
                    $aTemp->Platform_fee = 0;
                    $aTemp->Platform_Fee_GST = 0;
                }

               
                $AttendeeDataArray[] = $aTemp;

                
            }

            $excelData = [];
            $counter = 1;
            $totalSingleTicketPrice = 0;
            $totalTicketCount = 0;
            $totalTicketPrice = 0;
            $totalRegistrationFeeGST = 0;
            $totalAppliedCouponAmount = 0;
            $totalExtraAmount = 0;
            $totalExtraAmountPGCharges = 0;
            $totalExtraAmountPGGST = 0;
            $totalConvenienceFee = 0;
            $totalConvenienceFeeGST = 0;
            $totalPlatformFee = 0;
            $totalPlatformFeeGST = 0;
            $totalPaymentGatewayCharges = 0;
            $totalPaymentGatewayGST = 0;
            $totalOrganiserAmount = 0;
            $totalFinalAmount = 0;
            foreach ($AttendeeDataArray as $val) {
               
                 $excelData[] = array(
                    'Sr.No' =>  $counter,
                    'Firstname' => !empty($val->firstname) ? $val->firstname : '',
                    'Lastname' => !empty($val->lastname) ? $val->lastname : '',
                    'Email' =>!empty($val->email) ? $val->email : '',
                    'Mobile' => !empty($val->mobile) ? $val->mobile : '',
                    'bulk_upload_group_name' => !empty($val->bulk_upload_group_name) ? $val->bulk_upload_group_name : '', 
                    'Event Category' => !empty($val->category_name) ? $val->category_name : '',
                    'Type or Registration'=> !empty($val->category_type) ? $val->category_type : '',
                    'Transaction/Order ID'=> !empty($val->transaction_id) ? $val->transaction_id : '',
                    'Registration ID'=> !empty($val->registration_id) ? $val->registration_id : '',
                    'Payu ID'=> !empty($val->payu_id) ? $val->payu_id : '',
                    'Booking Date' => !empty($val->booking_date) ? date('d-m-Y H:i:s',$val->booking_date) : '',
                    'Payment Status' => !empty($val->payment_status) ? $val->payment_status : '',
                    'Inclusive/Exclusive' => !empty($val->taxes_status) ? $val->taxes_status : '',  
                    // 'Registration Price' => $val->Single_ticket_price,
                    'Count' => !empty($val->Ticket_count) ? $val->Ticket_count : '0',
                    // 'Registration Amount' => $val->Ticket_price,
                    'Ticket Amount' => !empty($val->Ticket_price) ? number_format($val->Ticket_price,2) : '0',
                    'Registration Fee GST' => !empty($val->Registration_Fee_GST) ? number_format($val->Registration_Fee_GST,2) : '0',
                    'Applied Coupon Amount' => !empty($val->Applied_Coupon_Amount) ? number_format($val->Applied_Coupon_Amount,2) : '0',
                    'Additional Amount' => !empty($val->Extra_amount) ? number_format($val->Extra_amount,2) : '0',
                    'Additional Amount Payment Gateway Charges' => !empty($val->Extra_amount_pg_charges) ? number_format($val->Extra_amount_pg_charges,2) : '0',
                    'Additional Amount Payment Gateway GST (18%)' => !empty($val->Extra_amount_pg_GST) ? number_format($val->Extra_amount_pg_GST,2) : '0',
                    // 'Passed on/Bare by' => $val->Pass_Bare,
                    'Convenience Fee - Paid By'=> !empty($val->Pass_Bare) ? $val->Pass_Bare : '',
                    'Payment Gateway - Paid By'=> !empty($val->Pg_Bare) ? $val->Pg_Bare : '',
                    'Convenience Fee' => !empty($val->Convenience_fee) ? number_format($val->Convenience_fee,2) : '0',
                    'Convenience Fee GST (18%)' => !empty($val->Convenience_Fee_GST) ? number_format($val->Convenience_Fee_GST,2) : '0',
                    'Platform Fee' => !empty($val->Platform_fee) ? $val->Platform_fee : '0',
                    'Platform Fee GST (18%)' => !empty($val->Platform_Fee_GST) ? number_format($val->Platform_Fee_GST,2) : '0',
                    'Payment Gateway Charges (1.85%)' => !empty($val->Payment_gateway_charges) ? number_format($val->Payment_gateway_charges,2) : '0',
                    'Payment Gateway GST (18%)' => !empty($val->Payment_Gateway_GST) ? number_format($val->Payment_Gateway_GST,2) : '0',
                    'Organiser Amount' => !empty($val->Organiser_amount) ? number_format($val->Organiser_amount,2) : '0',
                    'Final Amount' => !empty($val->Final_total_amount) ? number_format($val->Final_total_amount,2) : '0'
                );
    
                // $totalSingleTicketPrice += $val->Single_ticket_price;
                $totalTicketCount += $val->Ticket_count;
                $totalTicketPrice += $val->Ticket_price;
                $totalRegistrationFeeGST += $val->Registration_Fee_GST;
                $totalAppliedCouponAmount += $val->Applied_Coupon_Amount;
                $totalExtraAmount += $val->Extra_amount;
                $totalExtraAmountPGCharges += $val->Extra_amount_pg_charges;
                $totalExtraAmountPGGST += $val->Extra_amount_pg_GST;
                $totalConvenienceFee += $val->Convenience_fee;
                $totalConvenienceFeeGST += $val->Convenience_Fee_GST;
                $totalPlatformFee += $val->Platform_fee;
                $totalPlatformFeeGST += $val->Platform_Fee_GST;
                $totalPaymentGatewayCharges += $val->Payment_gateway_charges;
                $totalPaymentGatewayGST += $val->Payment_Gateway_GST;
                $totalOrganiserAmount += $val->Organiser_amount;
                $totalFinalAmount += $val->Final_total_amount;
                $counter++;
                
            }
      
            $excelData[] = array(
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                'Inclusive/Exclusive' =>'Total' ,
                // 'Registration Price' => !empty( $totalSingleTicketPrice)? $totalSingleTicketPrice :'0.00',
                'Count' => !empty($totalTicketCount)? number_format($totalTicketCount,2) :'0.00',
                'Registration Amount' => !empty($totalTicketPrice)? number_format($totalTicketPrice,2) :'0.00',
                'Registration Fee GST' =>!empty($totalRegistrationFeeGST)? number_format($totalRegistrationFeeGST,2) :'0.00',
                'Applied Coupon Amount' =>!empty($totalAppliedCouponAmount)? number_format($totalAppliedCouponAmount,2) :'0.00',
                'Additional Amount' => !empty($totalExtraAmount)? number_format($totalExtraAmount,2) :'0.00',
                'Additional Amount Payment Gateway Charges' => !empty($totalExtraAmountPGCharges)? number_format($totalExtraAmountPGCharges,2) :'0.00',
                'Additional Amount Payment Gateway GST (18%)' =>!empty($totalExtraAmountPGGST)? number_format($totalExtraAmountPGGST,2) :'0.00',
                // 'Passed on/Bare by' =>'',
                'Convenience Fee - Paid By' => '',
                'Payment Gateway - Paid By' =>'',
                'Convenience Fee' =>!empty($totalConvenienceFee)? number_format($totalConvenienceFee,2) :'0.00',
                'Convenience Fee GST (18%)' =>!empty($totalConvenienceFeeGST)? number_format($totalConvenienceFeeGST,2) :'0.00',
                'Platform Fee' =>!empty($totalPlatformFee)? number_format($totalPlatformFee,2) :'0.00',
                'Payment Gateway Charges' =>!empty($totalPlatformFeeGST)? number_format($totalPlatformFeeGST,2) :'0.00',
                'Payment Gateway Charges (1.85%)' =>!empty($totalPaymentGatewayCharges)? number_format($totalPaymentGatewayCharges,2) :'0.00',
                'Payment Gateway GST (18%)' =>!empty($totalPaymentGatewayGST)? number_format($totalPaymentGatewayGST,2) :'0.00',
                'Organiser Amount'=> !empty($totalOrganiserAmount)? number_format($totalOrganiserAmount,2) :'0.00',
                'Final Amount'=> !empty($totalFinalAmount)? number_format($totalFinalAmount,2) :'0.00'
            );        
          
            return $excelData;
        }else{
            return $excelData;
        }
       
       
      
    }

    public function headings(): array
    {
        // dd($event_name);
        $event_name = Session::has('event_name') ? Session::get('event_name') : '';

        $eventId = !empty($this->eventId) ? $this->eventId : $event_name;
        if(isset($eventId) && !empty($eventId)){
            $sSQL = 'SELECT name FROM events where id = '.$eventId;
            $aEventResult = DB::select($sSQL ,array()); 
            $event_name = !empty($aEventResult) ? $aEventResult[0]->name : ''; 
        }else{
            $event_name = 'All Events';
        }
            // Collect session data for filtering
            $participant_name = Session::has('participant_name') ? Session::get('participant_name') : '';
            $transaction_status = Session::has('transaction_status') ? Session::get('transaction_status') : '';
            $registration_id = Session::has('registration_id') ? Session::get('registration_id') : '';
            $mobile_no = Session::has('mobile_no') ? Session::get('mobile_no') : '';
            $email_id = Session::has('email_id') ? Session::get('email_id') : '';
            $category = Session::has('category') ? Session::get('category') : '';
            $start_booking_date = Session::has('start_booking_date') ? Session::get('start_booking_date') : '';
            $end_booking_date = Session::has('end_booking_date') ? Session::get('end_booking_date') : '';
    
            // Build the SQL query with search criteria
            $sSQL = 'SELECT count(a.id) as count FROM attendee_booking_details a
            LEFT JOIN booking_details AS b ON a.booking_details_id = b.id
            Inner JOIN event_booking AS e ON b.booking_id = e.id
            WHERE 1=1';

            if(isset($eventId) && !empty($eventId)){
                $sSQL .= ' AND b.event_id = '.$eventId;
            }

            // Add conditions based on session data
            if (!empty($participant_name)) {
                $sSQL .= ' AND (LOWER((CONCAT(a.firstname, " ", a.lastname))) LIKE \'%' . strtolower($participant_name) . '%\')';
            }
            if ($transaction_status !== '') {
                $sSQL .= ' AND e.transaction_status = '.$transaction_status;
            }
            if (!empty($registration_id)) {
                $sSQL .= ' AND a.registration_id LIKE \'%' . strtolower($registration_id) . '%\'';
            }
            if (!empty($mobile_no)) {
                $sSQL .= ' AND a.mobile LIKE \'%' . strtolower($mobile_no) . '%\'';
            }
            if (!empty($email_id)) {
                $sSQL .= ' AND a.email LIKE \'%' . strtolower($email_id) . '%\'';
            }
            if (!empty($category)) {
                $sSQL .= ' AND a.ticket_id = (SELECT id FROM event_tickets WHERE ticket_name LIKE \'%' . strtolower($category) . '%\')';
            }
            if (!empty($start_booking_date)) {
                $sSQL .= ' AND e.booking_date >= '. strtotime($start_booking_date);
            }
            if (!empty($end_booking_date)) {
                $sSQL .= ' AND e.booking_date <= '. strtotime($end_booking_date);
            }
    
            $sSQL .= ' ORDER BY a.id DESC';
    
            
            $event_participants = DB::select($sSQL, array());

        return [
            ['Event Name : ' . $event_name],
            ['Total Count : ' .$event_participants[0]->count],
            [],
            [
                'Sr.No',
                'Firstname',
                'Lastname',
                'Email',
                'Mobile',
                'Bulk Upload Group Name',
                'Event Category',
                'Type or Registration',
                'Transaction/Order ID',
                'Registration ID',
                'Payu ID',
                'Registration Date',
                'Payment Status',
                'GST-Inclusive/Exclusive',
                'Count',
                // 'Registration Amount Paid',
                'Ticket Amount',
                'Registration Fee GST',
                'Applied Coupon Amount',
                'Additional Amount',
                'Additional Amount Payment Gateway Charges',
                'Additional Amount Payment Gateway GST (18%)',
                // 'Passed on/Bare by',
                'Convenience Fee - Paid By',
                'Payment Gateway - Paid By',
                'Convenience Fee',
                'Convenience Fee GST (18%)',
                'Platform Fee',
                'Platform Fee GST (18%)',
                'Payment Gateway Charges (1.85%)',
                'Payment Gateway GST (18%)',
                'Organiser Amount',
                'Final Registration Amount'
            ]
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Set horizontal alignment for all cells
                $sheet->getStyle('A1:L1')->getAlignment()->setHorizontal('left');
                $sheet->getStyle('M4:S4')->getAlignment()->setHorizontal('right');
                $sheet->getStyle('U4:AE4')->getAlignment()->setHorizontal('right');


                // Merge cells in the header
                $headerMergeRanges = ['A1:AE1', 'A2:AE2', 'A3:AE3'];
                foreach ($headerMergeRanges as $range) {
                    $sheet->mergeCells($range);
                }

                // Set row heights
                for ($row = 1; $row <= 4; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(25);
                }
                $sheet->getStyle('AE5:AE2000')->getAlignment()->setHorizontal('right');
                $sheet->getStyle('AD5:AD2000')->getAlignment()->setHorizontal('right');
                $sheet->getStyle('AC5:AC2000')->getAlignment()->setHorizontal('right');
                $sheet->getStyle('AB5:AB2000')->getAlignment()->setHorizontal('right');
                $sheet->getStyle('AA5:AA2000')->getAlignment()->setHorizontal('right');
                $sheet->getStyle('Y5:Y2000')->getAlignment()->setHorizontal('right');
                $sheet->getStyle('X5:X2000')->getAlignment()->setHorizontal('right');
                $sheet->getStyle('W5:W2000')->getAlignment()->setHorizontal('right');
                $sheet->getStyle('V5:V2000')->getAlignment()->setHorizontal('right');
                $sheet->getStyle('U5:U2000')->getAlignment()->setHorizontal('right');
                $sheet->getStyle('T5:T2000')->getAlignment()->setHorizontal('right');
                $sheet->getStyle('S5:S2000')->getAlignment()->setHorizontal('right');
                $sheet->getStyle('R5:R2000')->getAlignment()->setHorizontal('right');
                $sheet->getStyle('Q5:Q2000')->getAlignment()->setHorizontal('right');
                $sheet->getStyle('P5:P2000')->getAlignment()->setHorizontal('right');
               
                $sheet->getStyle('A1:N4')->getAlignment()->setHorizontal
                ('left');
                $sheet->getStyle('V1:W4')->getAlignment()->setHorizontal
                ('left');
                $sheet->getStyle('O1:U4')->getAlignment()->setHorizontal
                ('right');
                $sheet->getStyle('X1:AE4')->getAlignment()->setHorizontal
                ('right');

               
                // Apply font styling to header
                $sheet->getStyle('A1:AE4')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ]
                ]);
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Apply bold styling to the second array (assumed to be the last row)
        $lastRowIndex = count($this->array()) + 4;
        $sheet->getStyle('A' . $lastRowIndex . ':AE' . $lastRowIndex)->getFont()->setBold(true);

        return [
            // Style the header row as bold text
            1    => ['font' => ['bold' => true]],
        ];
    }


    

   

   
}
