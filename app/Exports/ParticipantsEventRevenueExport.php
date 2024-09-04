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
        $participant_name = Session::has('participant_name') ? Session::get('participant_name') : '';
        $transaction_status = Session::has('transaction_status') ? Session::get('transaction_status') : '';
        $registration_id = Session::has('registration_id') ? Session::get('registration_id') : '';
        $mobile_no = Session::has('mobile_no') ? Session::get('mobile_no') : '';
        $email_id = Session::has('email_id') ? Session::get('email_id') : '';
        $category = Session::has('category') ? Session::get('category') : '';
        $start_booking_date = Session::has('start_booking_date') ? Session::get('start_booking_date') : '';
        $end_booking_date = Session::has('end_booking_date') ? Session::get('end_booking_date') : '';

        // Build the SQL query with search criteria
        $sSQL = 'SELECT a.*,e.booking_date, e.cart_details,e.booking_pay_id,e.total_amount, e.transaction_status, b.ticket_amount, b.event_id,a.id AS aId, CONCAT(a.firstname, " ", a.lastname) AS user_name,
        (SELECT et.ticket_name FROM event_tickets et WHERE et.id = a.ticket_id) AS category_name,
        (SELECT bpd.txnid FROM booking_payment_details bpd WHERE bpd.id = e.booking_pay_id) AS Transaction_order_id,
        (SELECT bpt.mihpayid FROM booking_payment_log bpt WHERE bpt.txnid = Transaction_order_id) AS payu_id
        FROM attendee_booking_details a
        LEFT JOIN booking_details AS b ON a.booking_details_id = b.id
        Inner JOIN event_booking AS e ON b.booking_id = e.id
        WHERE 1=1';

        if(!empty($eventId)){
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
        // dd( $event_participants);
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

                $aTemp->Organiser_amount = isset($card_details_array[0]->to_organiser) && !empty($card_details_array[0]->to_organiser) ? $card_details_array[0]->to_organiser : 0;

                $WhoPayYtcrFee = isset($card_details_array[0]->player_of_fee) && !empty($card_details_array[0]->player_of_fee) ? $card_details_array[0]->player_of_fee : 0;
                $WhoPayPaymentGatewayFee = isset($card_details_array[0]->player_of_gateway_fee) && !empty($card_details_array[0]->player_of_gateway_fee) ? $card_details_array[0]->player_of_gateway_fee : 0;

                if($WhoPayYtcrFee == 1 && $WhoPayPaymentGatewayFee == 1){
                    $aTemp->Pass_Bare = 'Passed on';
                }else if($WhoPayYtcrFee == 2 && $WhoPayPaymentGatewayFee == 2){
                    $aTemp->Pass_Bare = 'Bare by';
                }else if($WhoPayYtcrFee == 1 && $WhoPayPaymentGatewayFee == 2){
                    $aTemp->Pass_Bare = 'Bare by';
                }else if($WhoPayYtcrFee == 2 && $WhoPayPaymentGatewayFee == 1){
                    $aTemp->Pass_Bare = 'Passed on';
                }else{
                   $aTemp->Pass_Bare = ''; 
                }
                  
               
                    $aTemp->Ticket_count = isset($card_details_array[0]->count) && !empty($card_details_array[0]->count) ? $card_details_array[0]->count : 0;
                    $aTemp->Single_ticket_price = isset($card_details_array[0]->Main_Price) && !empty($card_details_array[0]->Main_Price) ? 
                    ($card_details_array[0]->Main_Price * $card_details_array[0]->count) : '0.00';
                    
                    $aTemp->Ticket_price = isset($card_details_array[0]->ticket_price) && !empty($card_details_array[0]->ticket_price) ? $card_details_array[0]->ticket_price : '0.00';
                   
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
                    
                    $aTemp->Extra_amount = isset($card_details_array[0]->discount_ticket_price) && !empty($card_details_array[0]->discount_ticket_price) ? ($card_details_array[0]->discount_ticket_price)  : '0.00'; 

                    $aTemp->Extra_amount_pg_charges = isset($card_details_array[0]->Extra_Amount_Payment_Gateway) && !empty($card_details_array[0]->Extra_Amount_Payment_Gateway) ? ($card_details_array[0]->Extra_Amount_Payment_Gateway * $card_details_array[0]->count)  : '0.00';  
                   
                    $aTemp->Extra_amount_pg_GST = isset($card_details_array[0]->Extra_Amount_Payment_Gateway_Gst) && !empty($card_details_array[0]->Extra_Amount_Payment_Gateway_Gst) ? ($card_details_array[0]->Extra_Amount_Payment_Gateway_Gst * $card_details_array[0]->count)  : '0.00';  

                    // Applied Coupon Amount
                    $aTemp->Applied_Coupon_Amount = isset($card_details_array[0]->appliedCouponAmount) && !empty($card_details_array[0]->appliedCouponAmount) ? ($card_details_array[0]->appliedCouponAmount * $card_details_array[0]->count)  : '0.00';  

                    
                $AttendeeDataArray[] = $aTemp;
            }
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
                'Firstname' => $val->firstname,
                'Lastname' => $val->lastname,
                'Email' =>$val->email,
                'Mobile' => $val->mobile,
                'Event Category' => $val->category_name,
                'Transaction/Order ID'=> $val->transaction_id,
                'Registration ID'=> $val->registration_id,
                'Payu ID'=> $val->payu_id,
                'Booking Date' => date('d-m-Y H:i:s',$val->booking_date),
                'Payment Status' => $val->payment_status,
                'Inclusive/Exclusive' => $val->taxes_status,  
                // 'Registration Price' => $val->Single_ticket_price,
                'Count' => $val->Ticket_count,
                'Registration Amount' => $val->Ticket_price,
                'Registration Fee GST' => $val->Registration_Fee_GST,
                'Applied Coupon Amount' => $val->Applied_Coupon_Amount,
                'Additional Amount' => $val->Extra_amount,
                'Additional Amount Payment Gateway Charges' => $val->Extra_amount_pg_charges,
                'Additional Amount Payment Gateway GST (18%)' => $val->Extra_amount_pg_GST,
                'Passed on/Bare by' => $val->Pass_Bare,
                'Convenience Fee' => $val->Convenience_fee,
                'Convenience Fee GST (18%)' => $val->Convenience_Fee_GST,
                'Platform Fee' => $val->Platform_fee,
                'Platform Fee GST (18%)' => $val->Platform_Fee_GST,
                'Payment Gateway Charges (1.85%)' => $val->Payment_gateway_charges,
                'Payment Gateway GST (18%)' => $val->Payment_Gateway_GST,
                'Organiser Amount' => $val->Organiser_amount,
                'Final Amount' => $val->Final_total_amount
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
            'Inclusive/Exclusive' =>'Total' ,
            // 'Registration Price' => !empty( $totalSingleTicketPrice)? $totalSingleTicketPrice :'0.00',
            'Count' => !empty($totalTicketCount)? $totalTicketCount :'0.00',
            'Registration Amount' => !empty($totalTicketPrice)? $totalTicketPrice :'0.00',
            'Registration Fee GST' =>!empty($totalRegistrationFeeGST)? $totalRegistrationFeeGST :'0.00',
            'Applied Coupon Amount' =>!empty($totalAppliedCouponAmount)? $totalAppliedCouponAmount :'0.00',
            'Additional Amount' => !empty($totalExtraAmount)? $totalExtraAmount :'0.00',
            'Additional Amount Payment Gateway Charges' => !empty($totalExtraAmountPGCharges)? $totalExtraAmountPGCharges :'0.00',
            'Additional Amount Payment Gateway GST (18%)' =>!empty($totalExtraAmountPGGST)? $totalExtraAmountPGGST :'0.00',
            'Passed on/Bare by' =>'',
            'Convenience Fee' =>!empty($totalConvenienceFee)? $totalConvenienceFee :'0.00',
            'Convenience Fee GST (18%)' =>!empty($totalConvenienceFeeGST)? $totalConvenienceFeeGST :'0.00',
            'Platform Fee' =>!empty($totalPlatformFee)? $totalPlatformFee :'0.00',
            'Payment Gateway Charges' =>!empty($totalPlatformFeeGST)? $totalPlatformFeeGST :'0.00',
            'Payment Gateway Charges (1.85%)' =>!empty($totalPaymentGatewayCharges)? $totalPaymentGatewayCharges :'0.00',
            'Payment Gateway GST (18%)' =>!empty($totalPaymentGatewayGST)? $totalPaymentGatewayGST :'0.00',
            'Organiser Amount'=> !empty($totalOrganiserAmount)? $totalOrganiserAmount :'0.00',
            'Final Amount'=> !empty($totalFinalAmount)? $totalFinalAmount :'0.00'
        );        
      
        return $excelData;
      
    }

    public function headings(): array
    {

        $eventId = $this->eventId;
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

            if(!empty($eventId)){
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
                'Event Category',
                'Transaction/Order ID',
                'Registration ID',
                'Payu ID',
                'Registration Date/Time',
                'Payment Status',
                'Inclusive/Exclusive',
                // 'Registration Price',
                'Count',
                'Registration Amount Paid',
                'Registration Fee GST',
                'Applied Coupon Amount',
                'Additional Amount',
                'Additional Amount Payment Gateway Charges',
                'Additional Amount Payment Gateway GST (18%)',
                'Passed on/Bare by',
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
                $sheet->getStyle('U4:AB4')->getAlignment()->setHorizontal('right');


                // Merge cells in the header
                $headerMergeRanges = ['A1:AB1', 'A2:AB2', 'A3:AB3'];
                foreach ($headerMergeRanges as $range) {
                    $sheet->mergeCells($range);
                }

                // Set row heights
                for ($row = 1; $row <= 4; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(25);
                }

                // Apply font styling to header
                $sheet->getStyle('A1:AB4')->applyFromArray([
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
        $sheet->getStyle('A' . $lastRowIndex . ':AB' . $lastRowIndex)->getFont()->setBold(true);

        return [
            // Style the header row as bold text
            1    => ['font' => ['bold' => true]],
        ];
    }


    

   

   
}
