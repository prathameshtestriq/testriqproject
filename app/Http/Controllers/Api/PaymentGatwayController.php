<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Libraries\Authenticate;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use \stdClass;

class PaymentGatwayController extends Controller
{

    // get user details
    public function payment_by_user_details(Request $request)
    {
        // dd($request);
        $response['data'] = [];
        $response['data']['user_details'] = [];
        $response['message'] = '';
        $ResposneCode = 400;
        $empty = false;

        // $Auth = new Authenticate();
        // $aToken = $Auth->decode_token($request->header('Authorization'));

        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        //dd($aToken);
        if ($aToken['code'] == 200) {

            $UserId = 0;
            if (!empty($aToken)) {
                $UserId = $aToken['data']->ID;
            }

            $EventId = !empty($request->event_id) ? $request->event_id : 0;
            $Sql = 'SELECT name FROM events WHERE active = 1 and id = ' . $EventId . ' ';
            $event_Result = DB::select($Sql);

            $Sql = 'SELECT id,firstname,lastname,email,mobile FROM users WHERE is_active = 1 and id = ' . $UserId . ' ';
            $aResult = DB::select($Sql);

            // dd($aResult);
            if (!empty($aResult)) {

                foreach ($aResult as $res) {

                    $res->product_info = !empty($event_Result) ? $event_Result[0]->name : '';
                    // $res->transaction_id = 'Ytcr-1';
                    $response['data']['user_details'] = $res;
                }


                $response['message'] = 'Request processed successfully';
                $ResposneCode = 200;
            } else {
                $response['message'] = 'No Data Found';
                $ResposneCode = 200;
            }

        } else {
            $ResposneCode = $aToken['code'];
            $response['message'] = $aToken['message'];
        }

        return response()->json($response, $ResposneCode);
    }

    public function booking_payment_process(Request $request)
    {

        // return $request->booking_tickets_array;

        $response['data'] = [];
        $response['message'] = '';
        $ResposneCode = 400;
        $empty = false;

        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        //dd($aToken);
        if ($aToken['code'] == 200) {

            $UserId = 0;
            if (!empty($aToken)) {
                $UserId = $aToken['data']->ID;
            }

            $EventId = !empty($request->event_id) ? $request->event_id : 0;
            $Amount = !empty($request->amount) ? $request->amount : '';
            $Datetime = time();
            $request_datetime = date('Y-m-d H:i:s');
            //dd($request_datetime);

            $Sql = 'SELECT name FROM events WHERE active = 1 and id = ' . $EventId . ' ';
            $event_Result = DB::select($Sql);

            $Sql = 'SELECT id,firstname,lastname,email,mobile FROM users WHERE is_active = 1 and id = ' . $UserId . ' ';
            $aResult = DB::select($Sql);

            $FirstName = !empty($aResult[0]->firstname) ? $aResult[0]->firstname : '';
            $LastName = !empty($aResult[0]->lastname) ? $aResult[0]->lastname : '';
            $Email = !empty($aResult[0]->email) ? $aResult[0]->email : '';
            $PhoneNo = !empty($aResult[0]->mobile) ? $aResult[0]->mobile : '';
            $ProductInfo = !empty($event_Result) ? $event_Result[0]->name : '';

            // $Merchant_key = !empty($request->merchant_key) ? $request->merchant_key : ''; 
            $Merchant_key = config('custom.merchant_key'); // set on custom file
            $SALT = config('custom.salt'); // set on custom file

            $Sql = 'SELECT counter FROM booking_payment_details WHERE 1=1 order by id desc limit 1';
            $aResult = DB::select($Sql);

            $last_count = !empty($aResult) && !empty($aResult[0]->counter) ? $aResult[0]->counter + 1 : 1;
            $txnid = !empty($last_count) ? 'YTCR-' . date('dmy') . '-' . $last_count : 'YTCR-' . date('dmy') . '-1';

            // $hash = hash('sha512', $Merchant_key . '|' . $txnid . '|' . $Amount . '|' . $ProductInfo . '|' . $FirstName . '|' . $Email . '|' . '||||||vvHOCdxxbkTXYASLCevSJ7iDkE8DRBT4');

            // $hashstring = $Merchant_key . '|' . $txnid . '|' . $Amount . '|' . $ProductInfo . '|' . $FirstName . '|' . $Email . '| udf1 | udf2 | udf3 | udf4 | udf5 |' . '||||||' . $SALT;
            // $hash = strtolower(hash('sha512', $hashstring)); |||||||||||

            $hashString = $Merchant_key . '|' . $txnid . '|' . $Amount . '|' . $ProductInfo . '|' . $FirstName . '|' . $Email . '|||||||||||' . $SALT;
            $hash = hash('sha512', $hashString);

            $Bindings = array(
                "event_id" => $EventId,
                "txnid" => $txnid,
                "firstname" => $FirstName,
                "lastname" => $LastName,
                "email" => $Email,
                "phone_no" => $PhoneNo,
                "productinfo" => $ProductInfo,
                "amount" => $Amount,
                "merchant_key" => $Merchant_key,
                "hash" => $hash,
                "created_by" => $UserId,
                "created_datetime" => $Datetime,
                "counter" => $last_count,
                "payment_status" => !empty($Amount) && $Amount != '0.00' ? 'initiate' : 'free'
            );
            //dd($Bindings);
            //-----------------
            $insert_SQL = "INSERT INTO booking_payment_details (event_id,txnid,firstname,lastname,email,phone_no,productinfo,amount,merchant_key,hash,created_by,created_datetime,counter,payment_status) VALUES(:event_id,:txnid,:firstname,:lastname,:email,:phone_no,:productinfo,:amount,:merchant_key,:hash,:created_by,:created_datetime,:counter,:payment_status)";
            DB::insert($insert_SQL, $Bindings);
            $last_inserted_id = DB::getPdo()->lastInsertId();

            //----------------- log entry
            $post_data = json_encode($Bindings);
            // dd($post_data);
            $Binding = array(
                "event_id" => $EventId,
                "booking_det_id" => $last_inserted_id,
                "txnid" => $txnid,
                "amount" => $Amount,
                "request_data" => $post_data,
                "created_by" => $UserId,
                "request_datetime" => $request_datetime,
                "payment_status" => !empty($Amount) && $Amount != '0.00' ? 'initiate' : 'free'
            );
            $insert_SQL1 = "INSERT INTO booking_payment_log (event_id,booking_det_id,txnid,amount,request_data,created_by,request_datetime,payment_status) VALUES(:event_id,:booking_det_id,:txnid,:amount,:request_data,:created_by,:request_datetime,:payment_status)";
            DB::insert($insert_SQL1, $Binding);

            //----------- temp table add entry for booking tickets
            $BookTicketArray = !empty($request->booking_tickets_array) ? json_decode($request->booking_tickets_array) : [];
            // return $BookTicketArray;

            $ParitcipantFiles = '';
            // $date = strtotime(date("Y-m-d H:i:s"));
            $date = time();
            if(!empty($request->file('fils_array'))){
                $i = 1;
                foreach ($request->file('fils_array') as $key => $uploadedFile) {
                
                    $Path = public_path('uploads/attendee_documents/');
                  
                    if ($uploadedFile->isValid() && $uploadedFile->getSize() > 2000) {

                        if($uploadedFile->getMimeType() == 'application/pdf' || $uploadedFile->getMimeType() == 'image/jpeg' || $uploadedFile->getMimeType() == 'image/png' || $uploadedFile->getMimeType() == 'image/jpg'){

                            $originalName = $date.'_'.$i.'_'. $uploadedFile->getClientOriginalName();
                            $participant_image = str_replace(" ","_",$originalName);
                            $uploadedFile->move($Path, $participant_image);
                            $i++;
                        }
                    }
                }
            }

            if (!empty($BookTicketArray)) {
                // return $BookTicketArray->total_attendees;
                $total_attendees = !empty($BookTicketArray->total_attendees) ? $BookTicketArray->total_attendees : "";
                $FormQuestions = !empty($BookTicketArray->FormQuestions) ? $BookTicketArray->FormQuestions : "";
                $TotalPrice = !empty($BookTicketArray->TotalPrice) ? $BookTicketArray->TotalPrice : "0.00";
                $TotalDiscount = !empty($BookTicketArray->TotalDiscount) ? $BookTicketArray->TotalDiscount : "";
                $AllTickets = !empty($BookTicketArray->AllTickets) ? $BookTicketArray->AllTickets : "";
                $ExtraPricing = !empty($BookTicketArray->ExtraPricing) ? $BookTicketArray->ExtraPricing : "";
                $EventUrl = !empty($BookTicketArray->EventUrl) ? $BookTicketArray->EventUrl : "";
                $UtmCampaign = !empty($BookTicketArray->UtmCampaign) ? $BookTicketArray->UtmCampaign : "";
                $GstArray = !empty($BookTicketArray->GstArray) ? $BookTicketArray->GstArray : [];

                //------------- new create form question array  
                //dd($FormQuestions);
                $emptyOptionTypes = array("countries", "states", "cities", "age_category");
                $MainFormQuestions = [];
                $newResult = [];
                $j = 1;
                if (!empty($FormQuestions)) {
                   // $i = 1;
                    foreach ($FormQuestions as $formId => $questions) {
                        // dd($formId);
                        foreach ($questions as $que => $question) {
                            // dd($que);
                            foreach ($question as $key => $value) {
                                $result = [];
                                if($value->question_form_type == 'file' && $value->ActualValue != ""){
                                    // C:\fakepath\
                                    $TemVar = isset($value->ActualValue) ? str_replace("C:\\fakepath\\", "", $value->ActualValue) : '';
                                    $common_file_name = isset($TemVar) ? $date.'_'.$j.'_'.str_replace(" ","_",$TemVar) : '';
                                    $j++;
                                }else{
                                    $common_file_name = isset($value->ActualValue) ? $value->ActualValue : '';
                                }
                                $result = [
                                    'id' => isset($value->id) ? $value->id : 0,
                                    'ActualValue' => $common_file_name,//$common_form_array,
                                    'question_label' => isset($value->question_label) ? $value->question_label : "",
                                    'general_form_id' => isset($value->general_form_id) ? $value->general_form_id : "",
                                    'question_form_type' => isset($value->question_form_type) ? $value->question_form_type : "",
                                    'question_form_name' => isset($value->question_form_name) ? $value->question_form_name : "",
                                    'question_form_option' => in_array($value->question_form_type, $emptyOptionTypes) ? ""
                                        : $value->question_form_option,
                                    'ticket_details' => isset($value->ticket_details) ? $value->ticket_details : "",
                                    'TicketId' => isset($value->TicketId) ? $value->TicketId : "",
                                    'child_question_ids' => isset($value->child_question_ids) ? $value->child_question_ids : "",
                                    'apply_ticket' => isset($value->apply_ticket) ? $value->apply_ticket : "",
                                    'data' => isset($value->data) ? $value->data : ""
                                ];
                                $newResult[$formId][$que][] = $result;
                                // $i++;
                            }

                        }

                    }
                }
                //dd($newResult);

                $Binding = array(
                    "event_id" => $EventId,
                    "booking_pay_id" => $last_inserted_id,
                    "total_attendees" => $total_attendees,
                    "FormQuestions" => !empty($FormQuestions) && !empty($newResult) ? json_encode($newResult) : '',
                    "TotalPrice" => $TotalPrice,
                    "TotalDiscount" => $TotalDiscount,
                    "AllTickets" => !empty($AllTickets) ? json_encode($AllTickets) : '',
                    "ExtraPricing" => !empty($ExtraPricing) ? json_encode($ExtraPricing) : '',
                    "EventUrl" => $EventUrl,
                    "UtmCampaign" => $UtmCampaign,
                    "GstArray" => !empty($GstArray) ? json_encode($GstArray) : '',
                );
                $insert_SQL1 = "INSERT INTO temp_booking_ticket_details (event_id,booking_pay_id,total_attendees,FormQuestions,TotalPrice,TotalDiscount,AllTickets,ExtraPricing,EventUrl,UtmCampaign,GstArray) VALUES(:event_id,:booking_pay_id,:total_attendees,:FormQuestions,:TotalPrice,:TotalDiscount,:AllTickets,:ExtraPricing,:EventUrl,:UtmCampaign,:GstArray)";
                DB::insert($insert_SQL1, $Binding);
            }

            $payment_details_array = array_merge($Bindings, array("booking_pay_id" => $last_inserted_id));
            $ResposneCode = 200;
            $response['data'] = !empty($payment_details_array) ? $payment_details_array : [];
            $response['message'] = 'Request processed successfully';

        } else {
            $ResposneCode = $aToken['code'];
            $response['message'] = $aToken['message'];
        }

        return response()->json($response, $ResposneCode);
    }

    public function payment_verify_status(Request $request)
    {
        $url = 'https://info.payu.in/merchant/postservice?form=2';
        // $merchantKey = 'ozLEHc';
        // $merchantSalt = 'vvHOCdxxbkTXYASLCevSJ7iDkE8DRBT4';

        $Merchant_key = config('custom.merchant_key'); // set on custom file
        $SALT = config('custom.salt'); // set on custom file
        $command = 'verify_payment';
        //$txnid = 'YTCR-180624-90';

        //----------------
        $Sql = 'SELECT txnid,amount FROM booking_payment_details WHERE 1=1';
        $aResult = DB::select($Sql);

        //dd($aResult);

        if (!empty($aResult)) {
            foreach ($aResult as $res) {
                $Transaction_id = $res->txnid;
                //dd($Transaction_id);
                $hashString = $Merchant_key . '|' . $command . '|' . $Transaction_id . '|' . $SALT;
                $hash = hash('sha512', $hashString);

                $postData = [
                    'key' => $Merchant_key,
                    'command' => 'verify_payment',
                    'hash' => $hash,
                    'var1' => $Transaction_id,
                ];

                // Make cURL request to PayUmoney API
                $ch = curl_init('https://info.payu.in/merchant/postservice?form=2');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

                $response = curl_exec($ch);
                curl_close($ch);

                $result = json_decode($response, true);

                $verify_payment_status = !empty($result['transaction_details'][$Transaction_id]['status']) ? $result['transaction_details'][$Transaction_id]['status'] : '';
                $payment_sub_status = !empty($result['transaction_details'][$Transaction_id]['unmappedstatus']) ? $result['transaction_details'][$Transaction_id]['unmappedstatus'] : '';

                //dd($verify_payment_status,$payment_sub_status);

                $up_sSQL = 'UPDATE booking_payment_details SET `verify_payment_status` =:verify_payment_status, `payment_sub_status` =:payment_sub_status WHERE `txnid`=:Txnid ';
                DB::update(
                    $up_sSQL,
                    array(
                        'verify_payment_status' => $verify_payment_status,
                        'payment_sub_status' => $payment_sub_status,
                        'Txnid' => $Transaction_id
                    )
                );

                //--------- generate log
                $post_data = json_encode($postData);
                // dd($post_data);
                $Binding = array(
                    "txnid" => $Transaction_id,
                    "amount" => !empty($res->amount) ? $res->amount : 0,
                    "post_data" => $post_data,
                    "verify_payment_status" => $verify_payment_status
                );
                $insert_SQL = "INSERT INTO cron_verify_payment_log(txnid,amount,post_data,verify_payment_status) VALUES(:txnid,:amount,:post_data,:verify_payment_status)";
                DB::insert($insert_SQL, $Binding);
                // die;
            }
        }

        //dd($result['transaction_details'][$txnid]);
    }

    public function payment_verify_transaction_status($tranid)
    {
        // dd($tranid);

        $url = 'https://info.payu.in/merchant/postservice?form=2';
        $Merchant_key = config('custom.merchant_key'); // set on custom file
        $SALT = config('custom.salt'); // set on custom file
        $command = 'verify_payment';

        $Sql = 'SELECT id,txnid,amount,created_by FROM booking_payment_details WHERE txnid = "' . $tranid . '" ';
        $aResult = DB::select($Sql);
        //dd($aResult);
        if (!empty($aResult)) {

            //-------------
            $booking_pay_id = !empty($aResult[0]->id) ? $aResult[0]->id : 0;
            $UserId = !empty($aResult[0]->created_by) ? $aResult[0]->created_by : 0;

            $Sql1 = 'SELECT id FROM event_booking WHERE booking_pay_id = ' . $booking_pay_id . ' ';
            $eventBookingResult = DB::select($Sql1);
            // dd($eventBookingResult);
            if (empty($eventBookingResult)) {
                $BookingProcess = PaymentGatwayController::book_tickets_third_party($booking_pay_id, $UserId);
            }

            //---------------------------------
            $Transaction_id = $tranid;
            //dd($Transaction_id);
            $hashString = $Merchant_key . '|' . $command . '|' . $Transaction_id . '|' . $SALT;
            $hash = hash('sha512', $hashString);

            $postData = [
                'key' => $Merchant_key,
                'command' => 'verify_payment',
                'hash' => $hash,
                'var1' => $Transaction_id,
            ];

            // Make cURL request to PayUmoney API
            $ch = curl_init('https://info.payu.in/merchant/postservice?form=2');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

            $response = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($response, true);
            // dd($result);

            $verify_payment_status = !empty($result['transaction_details'][$Transaction_id]['status']) ? $result['transaction_details'][$Transaction_id]['status'] : '';
            $payment_sub_status = !empty($result['transaction_details'][$Transaction_id]['unmappedstatus']) ? $result['transaction_details'][$Transaction_id]['unmappedstatus'] : '';
            $mih_pay_id = !empty($result['transaction_details'][$Transaction_id]['mihpayid']) ? $result['transaction_details'][$Transaction_id]['mihpayid'] : '';
            $payment_mode = !empty($result['transaction_details'][$Transaction_id]['mode']) && $result['transaction_details'][$Transaction_id]['mode'] != '-' ? $result['transaction_details'][$Transaction_id]['mode'] : '';
            $amount = !empty($result['transaction_details'][$Transaction_id]['amt']) ? $result['transaction_details'][$Transaction_id]['amt'] : '';
            $response_error_message = !empty($result['transaction_details'][$Transaction_id]['error_Message']) ? $result['transaction_details'][$Transaction_id]['error_Message'] : '';

            //dd($verify_payment_status,$payment_sub_status);

            $up_sSQL = 'UPDATE booking_payment_details SET `verify_payment_status` =:verify_payment_status, `payment_sub_status` =:payment_sub_status, `payment_mode` =:payment_mode, `response_error_message` =:response_error_message WHERE `txnid`=:Txnid ';
            DB::update(
                $up_sSQL,
                array(
                    'verify_payment_status' => $verify_payment_status,
                    'payment_sub_status' => $payment_sub_status,
                    'payment_mode' => $payment_mode,
                    'response_error_message' => $response_error_message,
                    'Txnid' => $Transaction_id
                )
            );

            //--------- generate log
            $post_data = json_encode($postData);
            // dd($post_data);
            $Binding = array(
                "txnid" => $Transaction_id,
                "amount" => !empty($amount) ? $amount : 0,
                "post_data" => $post_data,
                "verify_payment_status" => $verify_payment_status
            );
            $insert_SQL = "INSERT INTO cron_verify_payment_log(txnid,amount,post_data,verify_payment_status) VALUES(:txnid,:amount,:post_data,:verify_payment_status)";
            DB::insert($insert_SQL, $Binding);

            //-------- update status for booking_payment_details
            if ($verify_payment_status == 'success') {
                $up_sSQL = 'UPDATE booking_payment_details SET `payment_status` =:verify_payment_status, `payment_mode` =:payment_mode WHERE `txnid`=:Txnid ';
                DB::update(
                    $up_sSQL,
                    array(
                        'verify_payment_status' => $verify_payment_status,
                        'payment_mode' => $payment_mode,
                        'Txnid' => $Transaction_id
                    )
                );
            }

            //-------- update status for booking_payment_log
            $response_datetime = date('Y-m-d H:i:s');
            $up_sSQL = 'UPDATE booking_payment_log SET `mihpayid` =:mihpayid, `payment_status` =:payment_status, `response_datetime` =:response_datetime WHERE `txnid`=:Txnid ';
            DB::update(
                $up_sSQL,
                array(
                    'mihpayid' => $mih_pay_id,
                    'payment_status' => $verify_payment_status,
                    'response_datetime' => $response_datetime,
                    'Txnid' => $Transaction_id
                )
            );

            //-------- event booking table update payment status
            $transaction_status = 0;
            if ($verify_payment_status == "success") {
                $transaction_status = 1;
            } else {
                $transaction_status = 2;
            }
            $up_sSQL = 'UPDATE event_booking SET `transaction_status` =:transaction_status WHERE `booking_pay_id`=:booking_pay_id ';
            DB::update(
                $up_sSQL,
                array(
                    'transaction_status' => $transaction_status,
                    'booking_pay_id' => $booking_pay_id
                )
            );

            echo 'Request processed successfully.';

        } else {
            echo 'Transaction Id Not Found.';
        }
    }

    public function book_tickets_third_party($booking_pay_id, $UserId)
    {
        // dd($booking_pay_id,$UserId);
        $BookingPaymentId = !empty($booking_pay_id) ? $booking_pay_id : 0;
        $sql = "SELECT * FROM temp_booking_ticket_details WHERE booking_pay_id =:booking_pay_id";
        $BookingPayment = DB::select($sql, array('booking_pay_id' => $BookingPaymentId));

        if (count($BookingPayment) > 0) {
            $EventId = $BookingPayment[0]->event_id;
            $TotalAttendee = $BookingPayment[0]->total_attendees;
            $FormQuestions = !empty($BookingPayment[0]->FormQuestions) ? json_decode($BookingPayment[0]->FormQuestions) : [];
            $AllTickets = !empty($BookingPayment[0]->AllTickets) ? json_decode($BookingPayment[0]->AllTickets) : [];
            $TotalPrice = $BookingPayment[0]->TotalPrice;
            $TotalDiscount = $BookingPayment[0]->TotalDiscount;
            $ExtraPricing = !empty($BookingPayment[0]->ExtraPricing) ? json_decode($BookingPayment[0]->ExtraPricing) : [];
            $UtmCampaign = $BookingPayment[0]->UtmCampaign;
            $GstArray = !empty($BookingPayment[0]->GstArray) ? json_decode($BookingPayment[0]->GstArray) : [];
            $TransactionStatus = 0; //Initiated Transaction

            if (empty($TotalPrice) || $TotalPrice == 0 || $TotalPrice == '0.00' || $TotalPrice == '0') {
                $TransactionStatus = 3; // Free Transaction
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
                    "booking_details_id" => $IdBookingDetails,
                    "ticket_id" => $TicketId,
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
                $bd_bind = DB::select($bd_sql, array("booking_details_id" => $IdBookingDetails));
                if (count($bd_bind) > 0) {
                    $booking_date = $bd_bind[0]->booking_date;
                }
                $uniqueId = 0;
                $uniqueId = $EventId . "-" . $attendeeId . "-" . $booking_date;
                // dd($uniqueId,$IdBookingDetails,$booking_date);
                $u_sql = "UPDATE attendee_booking_details SET registration_id=:registration_id WHERE id=:id";
                $u_bind = DB::update($u_sql, array("registration_id" => $uniqueId, 'id' => $attendeeId));

            }
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
            return 'Request processed successfully';
        }
    }


}
