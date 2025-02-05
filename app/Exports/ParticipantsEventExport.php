<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;
use App\Libraries\Numberformate;

class ParticipantsEventExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $eventId;

    public function __construct($eventId)
    {
        $this->eventId = $eventId;
    }

    public function array(): array
    {
        ini_set('max_execution_time', '-1');
        $eventId = $this->eventId;
        $numberFormate = new Numberformate();

        // Collect session data for filtering
        $participant_name = Session::has('participant_name') ? Session::get('participant_name') : '';
        $transaction_status = Session::has('transaction_status') ? Session::get('transaction_status') : '';
        $registration_id = Session::has('registration_id') ? Session::get('registration_id') : '';
        $mobile_no = Session::has('mobile_no') ? Session::get('mobile_no') : '';
        $email_id = Session::has('email_id') ? Session::get('email_id') : '';
        $category = Session::has('category') ? Session::get('category') : '';
        $start_booking_date = Session::has('start_booking_date') ? Session::get('start_booking_date') : '';
        $end_booking_date = Session::has('end_booking_date') ? Session::get('end_booking_date') : '';
        $transaction_order_id = Session::has('transaction_order_id') ? Session::get('transaction_order_id') : '';

        $sSQL = "SELECT *,a.id AS aId,e.total_amount,(SELECT ticket_name FROM event_tickets WHERE id=a.ticket_id) AS TicketName,(SELECT et.ticket_name FROM event_tickets et WHERE et.id = a.ticket_id) AS category_name, (SELECT ticket_status FROM event_tickets WHERE id=a.ticket_id) AS ticket_status FROM attendee_booking_details AS a 
                LEFT JOIN booking_details AS b ON a.booking_details_id = b.id
                LEFT JOIN event_booking AS e ON b.booking_id = e.id WHERE 1=1 ";

        if(!empty($eventId)) {
            $sSQL .= ' AND b.event_id = '.$eventId;
        }
         
        // Add conditions based on session data
        if (!empty($participant_name)) {
            $sSQL .= ' AND (LOWER((CONCAT(a.firstname, " ", a.lastname))) LIKE \'%' . strtolower($participant_name) . '%\')';
        }
       
        if (isset($transaction_status) && $transaction_status !== '') {
            if ($transaction_status == 1) { // Special case for Success/Free
                $sSQL .= ' AND (e.transaction_status IN (1, 3))'; // Success = 1, Free = 3
            } else {
                $sSQL .= ' AND e.transaction_status = ' . intval($transaction_status);
            }
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
            // $sSQL .= ' AND a.ticket_id = (SELECT id FROM event_tickets WHERE ticket_name LIKE \'%' . strtolower($category) . '%\')';
            $sSQL .= ' AND a.ticket_id = '.$Return['search_category'];
        }

        if (!empty($start_booking_date)) {
            $sSQL .= ' AND e.booking_date >= '. strtotime($start_booking_date);
        }

        if (!empty($end_booking_date)) {
            $sSQL .= ' AND e.booking_date <= '. strtotime($end_booking_date);
        }

        if(!empty($transaction_order_id)){
            $sSQL .= ' AND (LOWER((SELECT bpd.txnid FROM booking_payment_details bpd WHERE bpd.id = e.booking_pay_id)) LIKE \'%' . strtolower($transaction_order_id) . '%\')';
        } 

        $sSQL .= " ORDER BY a.id DESC";

        $event_participants = DB::select($sSQL, array());
       // dd($event_participants);
      
        $excelData = [];  $final_ticket_amount = $TotalBookedTickets1 = $early_bird_total_discount = 0;
        $label = ''; $show_age_category = $show_coupon_code = $show_utm = 0;

        foreach ($event_participants as $value) {
            
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
          
            if($value->bulk_upload_flag == 0 && !empty($value->cart_details)){
                        // dd(json_decode($value->cart_details));
                        foreach(json_decode($value->cart_details) as $res){
                            if(!empty($res->ticket_price) && $value->ticket_id == $res->id){
                                $Extra_Amount = isset($res->Extra_Amount) && !empty($res->Extra_Amount) ? floatval($res->Extra_Amount) : 0;
                                $Extra_Amount_Payment_Gateway = isset($res->Extra_Amount_Payment_Gateway) && !empty($res->Extra_Amount_Payment_Gateway) ? floatval($res->Extra_Amount_Payment_Gateway) : 0;
                                $Extra_Amount_Payment_Gateway_Gst = isset($res->Extra_Amount_Payment_Gateway_Gst) && !empty($res->Extra_Amount_Payment_Gateway_Gst) ? floatval($res->Extra_Amount_Payment_Gateway_Gst) : 0;

                                $final_ticket_amount = (floatval($res->BuyerPayment) + $Extra_Amount + $Extra_Amount_Payment_Gateway + $Extra_Amount_Payment_Gateway_Gst);
                            }
                        }
                $value->total_amount = $final_ticket_amount;
            }else{
                $value->total_amount = $value->final_ticket_price;
            }

            //-------------------
            // $now = strtotime("now");

            // $sql10 = "SELECT COUNT(a.id) AS TotalBookedTickets
            //         FROM attendee_booking_details AS a 
            //         LEFT JOIN booking_details AS b ON b.id=a.booking_details_id
            //         LEFT JOIN event_booking AS e ON b.booking_id = e.id
            //         WHERE b.event_id =:event_id AND b.ticket_id=:ticket_id AND e.transaction_status IN (1,3)";
            // $TotalTickets1 = DB::select($sql10, array("event_id" => $eventId, "ticket_id" => $value->ticket_id));
            // $TotalBookedTickets1 = !empty($TotalTickets1) ? $TotalTickets1[0]->TotalBookedTickets : 0;
            // // dd($TotalBookedTickets1);
            // if ($value->early_bird == 1 && $TotalBookedTickets1 <= $value->no_of_tickets && $value->start_time <= $now && $value->end_time >= $now) {
            //     if ($value->discount == 1) { //percentage
            //         $value->total_discount = ($value->ticket_price * ($value->discount_value / 100));
            //     } else if ($value->discount == 2) { //amount
            //         $value->total_discount = $value->discount_value;
            //     }
            // }else{
            //    $value->total_discount = 0; 
            // }
            
            //--------- question array
            $ExcellDataArray = [];
            $sql = "SELECT id,question_label,question_form_type,question_form_name,(select name from events where id = event_form_question.event_id) as event_name FROM event_form_question WHERE question_status = 1";
           
            if(!empty($eventId)){
                $sql .= " AND event_id = ".$eventId." ";
            }
            // if(!empty($serach_event_id)) {
            //     $sql .= ' AND event_id = '.$serach_event_id;
            // }
            $sql .= ' order by sort_order asc';
            $EventQuestionData = DB::select($sql, array());
            // dd($EventQuestionData);

            //----------- get coupon code
            $sql = "SELECT id,(select ed.discount_code from event_coupon as ec left join event_coupon_details as ed on ed.event_coupon_id = ec.id where ec.id = applied_coupons.coupon_id) as coupon_name FROM applied_coupons WHERE event_id = :event_id AND booking_detail_id=:booking_detail_id ";
            $aCouponResult = DB::select($sql, array('event_id' => $value->event_id, 'booking_detail_id' => $value->booking_details_id));
            // dd($aCouponResult); 

             //-----------------------------
            $sql = "SELECT txnid,payment_mode,payment_status,created_datetime,(select mihpayid from booking_payment_log where booking_payment_details.id = booking_det_id) as mihpayid,bulk_upload_group_name FROM booking_payment_details WHERE id =:booking_pay_id ";
            $paymentDetails = DB::select($sql, array('booking_pay_id' => $res1->booking_pay_id));
            //dd($paymentDetails);
            $tran_id = !empty($paymentDetails) ? $paymentDetails[0]->txnid : '';
            $payment_mode = !empty($paymentDetails) ? $paymentDetails[0]->payment_mode : '';
            $payment_status = !empty($paymentDetails) ? $paymentDetails[0]->payment_status : '';
            $mihpayid = !empty($paymentDetails) ? $paymentDetails[0]->mihpayid : '';
            $booking_datetime = !empty($paymentDetails) ? date('d-m-Y h:i:s A', $paymentDetails[0]->created_datetime) : '';
            $bulk_upload_group_name = !empty($paymentDetails) && !empty($paymentDetails[0]->bulk_upload_group_name) ? $paymentDetails[0]->bulk_upload_group_name : '';
         


            // $sql = "SELECT id,(select ed.discount_code from event_coupon as ec left join event_coupon_details as ed on ed.event_coupon_id = ec.id where ec.id = applied_coupons.coupon_id) as coupon_name FROM applied_coupons WHERE event_id = :event_id AND booking_detail_id=:booking_detail_id ";
            // $aCouponResult = DB::select($sql, array('event_id' => $val->event_id, 'booking_detail_id' => $val->booking_details_id));
            // $coupancode = !empty($aCouponResult) ? $aCouponResult[0]->coupon_name : '-'; 
            // $free_status = !empty($val->ticket_amount) && $val->ticket_amount > 0 ? 'PAID' : 'FREE';
            // $total_amount = !empty($val->total_amount) ? number_format($val->total_amount,2) : '0.00'; 
            // $excelData[] = array(
            //     'Participant Name' => $val->firstname . ' ' . $val->lastname,
            //     'Booking Date' => date('d-m-Y H:i:s', $val->booking_date),
            //     'Transaction/order Id' => $val->Transaction_order_id,
            //     'Registration Id' => $val->registration_id,
            //     'Payu Id' => $val->payu_id,
            //     'Free/paid status'=> $free_status,
            //     'Coupan Code'=> $coupancode,
            //     'Total amount'=> $total_amount,
            //     'Transaction Status' => 
            //     ($val->transaction_status == 0 ? "Initiate" :
            //      ($val->transaction_status == 1 ? "Success" :
            //       ($val->transaction_status == 2 ? "Fail" :
            //        ($val->transaction_status == 3 ? "Free" : "Unknown")))),
            //     'Email/Mobile No' => $val->email . '  '.$val->mobile,
            //     'Category Name' => $val->category_name,
                
               
            // );
        }
         dd($event_participants);
      

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
            WHERE 1=1 ';

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
            ['Participant Count : ' .$event_participants[0]->count],
            [],
            [
                'Participant Name',
                'Booking Date',
                'Transaction/order Id',
                'Registration Id',
                'Payu Id',
                'Free/paid status',
                'Coupan Code',
                'Total amount',
                'Payment/Transaction status',
                'Email/Mobile No',
                'Category Name'
            ]
        ];
    }


    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Set horizontal alignment for all cells
                $sheet->getStyle('A1:K1')->getAlignment()->setHorizontal('left');

                // Merge cells in the header
                $headerMergeRanges = ['A1:K1', 'A2:K2', 'A3:K3'];
                foreach ($headerMergeRanges as $range) {
                    $sheet->mergeCells($range);
                }

                // Set row heights
                for ($row = 1; $row <= 4; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(25);
                }

                // Apply font styling to header
                $sheet->getStyle('A1:K4')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ]
                ]);
            },
        ];
    }
}
