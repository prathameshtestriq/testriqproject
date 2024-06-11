<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Libraries\Authenticate;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;

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
            $Sql = 'SELECT name FROM events WHERE active = 1 and id = '.$EventId.' ';
            $event_Result = DB::select($Sql);
         
            $Sql = 'SELECT id,firstname,lastname,email,mobile FROM users WHERE is_active = 1 and id = '.$UserId.' ';
            $aResult = DB::select($Sql);

            // dd($aResult);
            if(!empty($aResult)){

            	foreach($aResult as $res){
                  
                   $res->product_info = !empty($event_Result) ? $event_Result[0]->name : '';
                   // $res->transaction_id = 'Ytcr-1';
                   $response['data']['user_details'] = $res;
            	}
               
                
                $response['message'] = 'Request processed successfully';
                $ResposneCode = 200;
            }else{
                $response['message'] = 'No Data Found';
                $ResposneCode = 200;
            }

        }else{
            $ResposneCode = $aToken['code'];
            $response['message'] = $aToken['message'];
        }

        return response()->json($response, $ResposneCode);
    }

    public function booking_payment_process(Request $request)
    {

        // dd($request);
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
            
            $EventId      = !empty($request->event_id) ? $request->event_id : 0;
            $Amount       = !empty($request->amount) ? $request->amount : '';
            $Datetime     = time();
            $request_datetime = date('Y-m-d H:i:s');
            //dd($request_datetime);
             
            $Sql = 'SELECT name FROM events WHERE active = 1 and id = '.$EventId.' ';
            $event_Result = DB::select($Sql);
         
            $Sql = 'SELECT id,firstname,lastname,email,mobile FROM users WHERE is_active = 1 and id = '.$UserId.' ';
            $aResult = DB::select($Sql);

            $FirstName = !empty($aResult[0]->firstname) ? $aResult[0]->firstname : '';
            $LastName  = !empty($aResult[0]->lastname) ? $aResult[0]->lastname : '';
            $Email     = !empty($aResult[0]->email) ? $aResult[0]->email : '';
            $PhoneNo   = !empty($aResult[0]->mobile) ? $aResult[0]->mobile : '';
            $ProductInfo  = !empty($event_Result) ? $event_Result[0]->name : '';

            // $Merchant_key = !empty($request->merchant_key) ? $request->merchant_key : ''; 
            $Merchant_key = config('custom.merchant_key'); // set on custom file
            $SALT = config('custom.salt'); // set on custom file
            
            $Sql = 'SELECT counter FROM booking_payment_details WHERE 1=1 order by id desc limit 1';
            $aResult = DB::select($Sql);
            
            $last_count = !empty($aResult) && !empty($aResult[0]->counter) ? $aResult[0]->counter+1 : 1;
            $txnid  = !empty($last_count) ? 'Ytcr-'.$last_count : 'Ytcr-1';
           
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
                            "payment_status" => 'initiate'
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
                            "payment_status" => 'initiate'
                        );
            $insert_SQL1 = "INSERT INTO booking_payment_log (event_id,booking_det_id,txnid,amount,request_data,created_by,request_datetime,payment_status) VALUES(:event_id,:booking_det_id,:txnid,:amount,:request_data,:created_by,:request_datetime,:payment_status)";
            DB::insert($insert_SQL1, $Binding);

            //----------- temp table add entry for booking tickets
            $BookTicketArray = !empty($request->booking_tickets_array) ? $request->booking_tickets_array : [];
            
            if(!empty($BookTicketArray)){

                $total_attendees = !empty($BookTicketArray['total_attendees']) ? $BookTicketArray['total_attendees'] : "";
                $FormQuestions   = !empty($BookTicketArray['FormQuestions']) ? $BookTicketArray['FormQuestions'] : "";
                $TotalPrice      = !empty($BookTicketArray['TotalPrice']) ? $BookTicketArray['TotalPrice'] : "";
                $TotalDiscount   = !empty($BookTicketArray['TotalDiscount']) ? $BookTicketArray['TotalDiscount'] : "";
                $AllTickets      = !empty($BookTicketArray['AllTickets']) ? $BookTicketArray['AllTickets'] : "";
                $ExtraPricing    = !empty($BookTicketArray['ExtraPricing']) ? $BookTicketArray['ExtraPricing'] : "";
                $EventUrl        = !empty($BookTicketArray['EventUrl']) ? $BookTicketArray['EventUrl'] : "";
                $UtmCampaign     = !empty($BookTicketArray['UtmCampaign']) ? $BookTicketArray['UtmCampaign'] : "";  
                $GstArray        = !empty($BookTicketArray['GstArray']) ? $BookTicketArray['GstArray'] : [];  

                $Binding =  array(
                                "event_id" => $EventId,
                                "booking_pay_id" => $last_inserted_id,
                                "total_attendees" => $total_attendees,
                                "FormQuestions" => !empty($FormQuestions) ? json_encode($FormQuestions) : '', 
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
         
            $payment_details_array = array_merge($Bindings,array("booking_pay_id" => $last_inserted_id));
            $ResposneCode = 200;
            $response['data'] = !empty($payment_details_array) ? $payment_details_array : [];
            $response['message'] = 'Request processed successfully';
       
        }else{
            $ResposneCode = $aToken['code'];
            $response['message'] = $aToken['message'];
        }

        return response()->json($response, $ResposneCode);
    }


}
