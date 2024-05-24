<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Libraries\Authenticate;
use Illuminate\Support\Facades\DB;

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
            
            $EventId   = !empty($request->event_id) ? $request->event_id : 0;
            $FirstName = !empty($request->firstname) ? $request->firstname : '';
            $LastName  = !empty($request->lastname) ? $request->lastname : '';
            $Email     = !empty($request->email) ? $request->email : '';
            $PhoneNo   = !empty($request->phone) ? $request->phone : '';
            $ProductInfo  = !empty($request->product_info) ? $request->product_info : '';
            $Amount       = !empty($request->amount) ? $request->amount : '';
            $Datetime     = time();

            $Merchant_key = !empty($request->merchant_key) ? $request->merchant_key : ''; // set on custom file
            
            $Sql = 'SELECT counter FROM booking_payment_details WHERE 1=1 order by id desc limit 1';
            $aResult = DB::select($Sql);
            
            $last_count = !empty($aResult) && !empty($aResult[0]->counter) ? $aResult[0]->counter+1 : 1;
            $txnid  = !empty($last_count) ? 'Ytcr-'.$last_count : 'Ytcr-1';
            //dd($txnid);
            // hash('sha512', $this->key . '|' . $params['txnid'] . '|' . $params['amount'] . '|' . $params['productinfo'] . '|' . $params['firstname'] . '|' . $params['email'] . '|' . $params['udf1'] . '|' . $params['udf2'] . '|' . $params['udf3'] . '|' . $params['udf4'] . '|' . $params['udf5'] . '||||||' . $this->salt)
            
            $hash = hash('sha512', $Merchant_key . '|' . $txnid . '|' . $Amount . '|' . $ProductInfo . '|' . $FirstName . '|' . $LastName . '|' . $PhoneNo . '||||||');
            // dd($hash);

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
                            "counter" => $last_count
                        );
            //dd($Bindings);
            $insert_SQL = "INSERT INTO booking_payment_details (event_id,txnid,firstname,lastname,email,phone_no,productinfo,amount,merchant_key,hash,created_by,created_datetime,counter) VALUES(:event_id,:txnid,:firstname,:lastname,:email,:phone_no,:productinfo,:amount,:merchant_key,:hash,:created_by,:created_datetime,:counter)";
            DB::insert($insert_SQL, $Bindings);

            //----------------- log entry

            $post_data = json_encode($Bindings);
            // dd($post_data);
            $Binding = array(
                            "event_id" => $EventId,
                            "txnid" => $txnid,
                            "amount" => $Amount,
                            "post_data" => $post_data,
                            "created_by" => $UserId
                        );
            $insert_SQL1 = "INSERT INTO booking_payment_log (event_id,txnid,amount,post_data,created_by) VALUES(:event_id,:txnid,:amount,:post_data,:created_by)";
            DB::insert($insert_SQL1, $Binding);

            $ResposneCode = 200;
            $response['message'] = 'Request processed successfully';
       
        }else{
            $ResposneCode = $aToken['code'];
            $response['message'] = $aToken['message'];
        }

        return response()->json($response, $ResposneCode);
    }


}
