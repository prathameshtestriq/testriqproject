<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EventBookTicket extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:event-book-ticket';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Event Book Ticket Update';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // \Log::info("is log working");

        $Sql = 'SELECT id,created_by,payment_status,event_id FROM booking_payment_details WHERE id NOT IN(select booking_pay_id from event_booking where booking_pay_id = booking_payment_details.id)';
        $aResult = DB::select($Sql);
       
        // $ss = array_column($aResult,'id');
        //  dd(implode(",",$ss));
        if (!empty($aResult)) {

            foreach($aResult as $res){

                $booking_pay_id = !empty($res->id) ? $res->id : 0;
                $UserId         = !empty($res->created_by) ? $res->created_by : 0;
                $payment_status = !empty($res->payment_status) ? $res->payment_status : '';
                $BookEventId         = !empty($res->event_id) ? $res->event_id : 0;

                $Sql1 = 'SELECT id FROM event_booking WHERE booking_pay_id = ' . $booking_pay_id . ' ';
                $eventBookingResult = DB::select($Sql1);
                // dd($eventBookingResult);
                $new_registration_id_array = []; 
                if (empty($eventBookingResult)) {
                    // $BookingProcess = PaymentGatwayController::book_tickets_third_party($booking_pay_id, $UserId);
                    $BookingPaymentId = !empty($booking_pay_id) ? $booking_pay_id : 0;
                    $sql = "SELECT * FROM temp_booking_ticket_details WHERE booking_pay_id =:booking_pay_id";
                    $BookingPayment = DB::select($sql, array('booking_pay_id' => $BookingPaymentId));
                    
                    if (!empty($BookingPayment) && count($BookingPayment) > 0) {
                        $EventId = $BookingPayment[0]->event_id;
                        $TotalAttendee = $BookingPayment[0]->total_attendees;
                        $FormQuestions = !empty($BookingPayment[0]->FormQuestions) ? json_decode($BookingPayment[0]->FormQuestions) : [];
                        $AllTickets = !empty($BookingPayment[0]->AllTickets) ? json_decode($BookingPayment[0]->AllTickets) : [];
                        $TotalPrice = $BookingPayment[0]->TotalPrice;
                        $TotalDiscount = $BookingPayment[0]->TotalDiscount;
                        $ExtraPricing = !empty($BookingPayment[0]->ExtraPricing) ? json_decode($BookingPayment[0]->ExtraPricing) : [];
                        $UtmCampaign = $BookingPayment[0]->UtmCampaign;
                        $GstArray = !empty($BookingPayment[0]->GstArray) ? json_decode($BookingPayment[0]->GstArray) : [];
                        // $TransactionStatus = 0; //Initiated Transaction

                        // if (empty($TotalPrice) || $TotalPrice == 0 || $TotalPrice == '0.00' || $TotalPrice == '0') {
                        //     $TransactionStatus = 3; // Free Transaction
                        // }

                        if($payment_status == 'Initiate'){
                            $TransactionStatus = 0; 
                        }else if($payment_status == 'Success'){
                            $TransactionStatus = 1; 
                        }else if($payment_status == 'Fail'){
                            $TransactionStatus = 2; 
                        }else if($payment_status == 'Free'){
                            $TransactionStatus = 3; 
                        }else{
                           $TransactionStatus = 0;   
                        }

                        // dd($UserEmail);
                        $TotalTickets = 0;

                        // if (!empty($TotalPrice)) {
                        #event_booking
                        $Binding1 = array(
                            "event_id" => $EventId,
                            "user_id" => $UserId,
                            "booking_date" => strtotime("now"),
                            "total_amount" => $TotalPrice,
                            "total_discount" => $TotalDiscount,
                            "utm_campaign" => $UtmCampaign,
                            "cart_details" => json_encode($GstArray),
                            "transaction_status" => $TransactionStatus,
                            "booking_pay_id" => $BookingPaymentId
                        );
                        $Sql1 = "INSERT INTO event_booking (event_id,user_id,booking_date,total_amount,total_discount,utm_campaign,cart_details,transaction_status,booking_pay_id) VALUES (:event_id,:user_id,:booking_date,:total_amount,:total_discount,:utm_campaign,:cart_details,:transaction_status,:booking_pay_id)";
                        DB::insert($Sql1, $Binding1);
                        $BookingId = DB::getPdo()->lastInsertId();

                        #booking_details
                        $BookingDetailsIds = [];

                        foreach ($AllTickets as $ticket) {
                            if (!empty($ticket->count)) {
                                $Binding2 = [];
                                $Sql2 = "";
                                $Binding2 = array(
                                    "booking_id" => $BookingId,
                                    "event_id" => $EventId,
                                    "user_id" => $UserId,
                                    "ticket_id" => $ticket->id,
                                    "quantity" => $ticket->count,
                                    "ticket_amount" => $ticket->ticket_price,
                                    "ticket_discount" => isset($ticket->ticket_discount) ? ($ticket->ticket_discount) : 0,
                                    "booking_date" => strtotime("now"),
                                );
                                $Sql2 = "INSERT INTO booking_details (booking_id,event_id,user_id,ticket_id,quantity,ticket_amount,ticket_discount,booking_date) VALUES (:booking_id,:event_id,:user_id,:ticket_id,:quantity,:ticket_amount,:ticket_discount,:booking_date)";
                                DB::insert($Sql2, $Binding2);
                                #Get the last inserted id of booking_details
                                $BookingDetailsId = DB::getPdo()->lastInsertId();

                                $BookingDetailsIds[$ticket->id] = $BookingDetailsId;
                                $new_ticket_id = !empty($aResult[0]->ticket_id) ? $aResult[0]->ticket_id : $ticket->id;

                                // ADD IF COUPONS APPLY ON TICKET
                                $appliedCouponId = $appliedCouponAmount = 0;
                                $appliedCouponCode = "";

                                $appliedCouponId = (isset($ticket->appliedCouponId) && !empty($ticket->appliedCouponId)) ? $ticket->appliedCouponId : 0;
                                $appliedCouponAmount = (isset($ticket->appliedCouponAmount) && !empty($ticket->appliedCouponAmount)) ? $ticket->appliedCouponAmount : 0;
                                // $appliedCouponCode = (isset($ticket["appliedCouponCode"]) && !empty($ticket["appliedCouponCode"])) ? $ticket["appliedCouponCode"] : "";

                                if (!empty($appliedCouponId) && $appliedCouponAmount) {
                                    $Binding6 = [];
                                    $Sql6 = "";
                                    $Binding6 = array(
                                        "event_id" => $EventId,
                                        "coupon_id" => $appliedCouponId,
                                        "ticket_ids" => $ticket->id,
                                        "amount" => $appliedCouponAmount,
                                        "created_by" => $UserId,
                                        "created_at" => strtotime("now"),
                                        "booking_id" => $BookingId,
                                        "booking_detail_id" => $BookingDetailsId
                                    );
                                    $Sql6 = "INSERT INTO applied_coupons (event_id,coupon_id,ticket_ids,amount,created_by,created_at,booking_id,booking_detail_id) VALUES (:event_id,:coupon_id,:ticket_ids,:amount,:created_by,:created_at,:booking_id,:booking_detail_id)";
                                    DB::insert($Sql6, $Binding6);

                                    $Sql7 = "";
                                    $Binding7 = [];

                                    $Sql7 = "SELECT discount_type FROM event_coupon_details WHERE event_coupon_id=:event_coupon_id";
                                    $Binding7 = array("event_coupon_id" => $appliedCouponId);
                                    $Result = DB::select($Sql7, $Binding7);
                                    $IsDiscountOneTime = 0;

                                    if (count($Result) > 0) {
                                        $IsDiscountOneTime = $Result[0]->discount_type;
                                        $Sql8 = "";
                                        $Binding8 = [];
                                        if ($IsDiscountOneTime == 1) {
                                            $Binding8 = array("event_coupon_id" => $appliedCouponId);
                                            $Sql8 = "UPDATE event_coupon_details SET end_coupon=1 WHERE event_coupon_id=:event_coupon_id";
                                            DB::update($Sql8, $Binding8);
                                        }
                                    }
                                }
                            }
                        }
           
                        #ADD EXTRA AMOUNT FOR PAYABLE FOR USER in booking_details
                        if (!empty($ExtraPricing)) {
                            foreach ($ExtraPricing as $value) {
                                $Binding4 = [];
                                $Sql4 = "";
                                $Binding4 = array(
                                    "booking_id" => $BookingId,
                                    "event_id" => $EventId,
                                    "user_id" => $UserId,
                                    "ticket_id" => $value->ticket_id,
                                    "quantity" => 0,
                                    "ticket_amount" => $value->value,
                                    "ticket_discount" => 0,
                                    "booking_date" => strtotime("now"),
                                    "question_id" => $value->question_id,
                                    "attendee_number" => $value->aNumber
                                );
                                $Sql4 = "INSERT INTO booking_details (booking_id,event_id,user_id,ticket_id,quantity,ticket_amount,ticket_discount,booking_date,question_id,attendee_number) VALUES (:booking_id,:event_id,:user_id,:ticket_id,:quantity,:ticket_amount,:ticket_discount,:booking_date,:question_id,:attendee_number)";
                                DB::insert($Sql4, $Binding4);

                                #ADD COUNT IN extra_pricing_booking TABLE
                                $Binding5 = [];
                                $Sql5 = "";
                                $CurrentSoldCount = 0;

                                #Check If Question Id & Option Id Exists In extra_pricing_booking Table Or Not. If Yes Then Get The Current Count
                                if (!empty($value->count)) {
                                    $SqlExist = "SELECT id,current_count,option_id FROM extra_pricing_booking WHERE question_id =:question_id AND option_id=:option_id";
                                    $Exist = DB::select($SqlExist, array("question_id" => $value->question_id, "option_id" => $value->option_id));

                                    if (sizeof($Exist) > 0) {
                                        #UPDATE THE RECORD GET CURRENT COUNT FROM SAME TABLE
                                        $ExistId = $Exist[0]->id;
                                        $SoldCount = $Exist[0]->current_count;
                                        $CurrentSoldCount = $SoldCount + 1;

                                        if ($value->count >= $CurrentSoldCount) {
                                            $Binding5 = array(
                                                "event_id" => $EventId,
                                                "booking_id" => $BookingId,
                                                "user_id" => $UserId,
                                                "ticket_id" => $value->ticket_id,
                                                "total_count" => $value->count,
                                                "current_count" => $CurrentSoldCount,
                                                "last_booked_date" => strtotime('now'),
                                                "id" => $ExistId
                                            );
                                            $Sql5 = "UPDATE extra_pricing_booking SET
                                                          event_id = :event_id,
                                                          booking_id = :booking_id,
                                                          user_id = :user_id,
                                                          ticket_id = :ticket_id,
                                                          total_count = :total_count,
                                                          current_count = :current_count,
                                                          last_booked_date = :last_booked_date
                                                          WHERE id = :id";
                                            DB::update($Sql5, $Binding5);
                                        } else {
                                            // $ResposneCode = 400;
                                            // $message = 'The ' . $value["question_label"] . ' you want to add is out of stock.';
                                        }
                                    } else {
                                        #ADD A NEW RECORD TO THE TABLE
                                        $CurrentSoldCount = 1;
                                        $Binding5 = array(
                                            "event_id" => $EventId,
                                            "booking_id" => $BookingId,
                                            "user_id" => $UserId,
                                            "ticket_id" => $value->ticket_id,
                                            "question_id" => $value->question_id,
                                            "option_id" => $value->option_id,
                                            "total_count" => $value->count,
                                            "current_count" => $CurrentSoldCount,
                                            "first_booked_date" => strtotime('now')
                                        );
                                        $Sql5 = "INSERT INTO extra_pricing_booking (event_id,booking_id,user_id,ticket_id,question_id,option_id,total_count,current_count,first_booked_date) VALUES (:event_id,:booking_id,:user_id,:ticket_id,:question_id,:option_id,:total_count,:current_count,:first_booked_date)";
                                        DB::insert($Sql5, $Binding5);
                                    }
                                }

                            }
                        }
                        #ATTENDEE DETAILS
                        $separatedArrays = [];
                        $first_name = null;
                        $last_name = null;
                        $email = null;
                        $IdBookingDetails = 0;

                        // dd($FormQuestions);
                        foreach ($FormQuestions as $key => $arrays) {
                            foreach ($arrays as $subArray) {
                                $separatedArrays[] = json_encode($subArray);
                            }
                        }


                        foreach ($separatedArrays as $key => $value) {
                            $subArray = [];
                            $subArray = json_decode($value);
                            $TicketId = 0;
                            // dd($subArray);
                            foreach ($subArray as $key => $sArray) {
                                if (isset($sArray->question_form_name)) {
                                    if ($sArray->question_form_name == 'first_name') {
                                        $first_name = $sArray->ActualValue;
                                    } elseif ($sArray->question_form_name == 'last_name') {
                                        $last_name = $sArray->ActualValue;
                                    } elseif ($sArray->question_form_type == 'email') {
                                        $email = $sArray->ActualValue;
                                    }
                                }
                                if (empty($TicketId)) {
                                    $TicketId = !empty($sArray->TicketId) ? $sArray->TicketId : 0;
                                }

                            }
                            // die;
                            $IdBookingDetails = isset($BookingDetailsIds[$TicketId]) ? $BookingDetailsIds[$TicketId] : 0;
                            $sql = "INSERT INTO attendee_booking_details (booking_details_id,ticket_id,attendee_details,email,firstname,lastname,created_at) VALUES (:booking_details_id,:ticket_id,:attendee_details,:email,:firstname,:lastname,:created_at)";
                            $Bind1 = array(
                                "booking_details_id" => !empty($IdBookingDetails) ? $IdBookingDetails : $BookingDetailsId,
                                "ticket_id" => !empty($TicketId) ? $TicketId : $new_ticket_id,
                                "attendee_details" => json_encode($value),
                                "email" => $email,
                                "firstname" => $first_name,
                                "lastname" => $last_name,
                                "created_at" => strtotime("now")
                            );
                            DB::insert($sql, $Bind1);
                            $attendeeId = DB::getPdo()->lastInsertId();
                            // dd($attendeeId);

                            $booking_date = 0;
                            $bd_sql = "SELECT booking_date FROM booking_details WHERE id = :booking_details_id";
                            $bd_bind = DB::select($bd_sql, array("booking_details_id" => $BookingDetailsId));
                            if (count($bd_bind) > 0) {
                                $booking_date = $bd_bind[0]->booking_date;
                            }
                            $uniqueId = 0;
                            $uniqueId = $EventId . "-" . $attendeeId . "-" . $booking_date;
                            // dd($uniqueId,$IdBookingDetails,$booking_date);
                            $u_sql = "UPDATE attendee_booking_details SET registration_id=:registration_id WHERE id=:id";
                            $u_bind = DB::update($u_sql, array("registration_id" => $uniqueId, 'id' => $attendeeId));

                            //------------ new added
                            $sql1 = "SELECT ticket_name FROM event_tickets WHERE id = :ticket_id";
                            $aResult1 = DB::select($sql1, array("ticket_id" => !empty($TicketId) ? $TicketId : $new_ticket_id));
                            $new_ticket_name_array = !empty($aResult1) ? array_column($aResult1,"ticket_name") : [];
                            $new_registration_id_array[] = $uniqueId;

                        }

                        //------- new added for update ticket_names, registration_ids on 25-06-24 (because send email to reg no, tick name issue)
                        $loc_ticket_names = !empty($new_ticket_name_array) ? implode(", ", array_unique($new_ticket_name_array)) : '';
                        $loc_registration_id = !empty($new_registration_id_array) ? implode(", ", $new_registration_id_array) : '';
                         // dd($loc_ticket_names,$loc_registration_id);
                        $up_sql = "UPDATE booking_payment_details SET ticket_names =:ticket_names, registration_ids =:registration_ids  WHERE id=:id";
                        DB::update($up_sql, array("ticket_names" => $loc_ticket_names, "registration_ids" => $loc_registration_id, 'id' => $BookingPaymentId));


                        // -------------------------------------------END ATTENDEE DETAIL
                        foreach ($FormQuestions as $Form) {
                            $TotTickets = count($Form);
                            $TotalTickets += $TotTickets;
                            foreach ($Form as $Question) {
                                // echo "<pre>";print_r($Question);
                                foreach ($Question as $value) {
                                    // dd($BookingDetailsIds,$value['ticket_id']);
                                    $Binding3 = [];
                                    $Sql3 = "";
                                    $IdBookingDetails = 0;
                                    if ((isset($value->ActualValue)) && ($value->ActualValue !== "")) {

                                        if ($value->question_form_type == "file") {
                                            // $_FILES = $value['ActualValue'];
                                            $allowedExts = array('jpeg', 'jpg', "png", "gif", "bmp", "pdf");
                                            $is_valid = false;
                                            $filename = $address_proof_doc_upload = '';
                                        }
                                    }
                                }
                            }
                        }
                        // return 'Request processed successfully';
                        

                        //-------------------------- send email
                        if($TransactionStatus == 1){
                            $SendEmail = app('App\Http\Controllers\Api\PaymentGatwayController')->send_email_payment_success($booking_pay_id,$BookEventId,$UserId);
                        }
                       
                    }
                }

                
            }
        }



    }

  
}
