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
           
           // $hash = hash('sha512', $Merchant_key . '|' . $txnid . '|' . $Amount . '|' . $ProductInfo . '|' . $FirstName . '|' . $Email . '|' . '||||||vvHOCdxxbkTXYASLCevSJ7iDkE8DRBT4');
            $SALT = 'vvHOCdxxbkTXYASLCevSJ7iDkE8DRBT4';
            // $hashstring = $Merchant_key . '|' . $txnid . '|' . $Amount . '|' . $ProductInfo . '|' . $FirstName . '|' . $Email . '| udf1 | udf2 | udf3 | udf4 | udf5 |' . '||||||' . $SALT;
            // $hash = strtolower(hash('sha512', $hashstring));

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
                            "counter" => $last_count
                        );
            dd($Bindings);
            //-----------------
            
                // $hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|"
                //     ."udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";
                // $hashVarsSeq  = explode('|', $hashSequence);
                // $hashString   = '';  
                // foreach ($hashVarsSeq as $hashVar) {
                //     $hashString .= isset($payObject['params'][$hashVar]) ? $payObject['params'][$hashVar] : '';
                //     $hashString .= '|';
                // }
                // $hashString .= $SALT;
                // //generate hash
                // $hash1 = strtolower(hash('sha512', $hashString));
                // dd($hash1);

                // $retHashSeq = $SALT.'|'.'||||||||'.$Email.'|||'.$Amount.'|'.$txnid.'|'.$Merchant_key;
                // $hash1 = hash("sha512", $retHashSeq);
                // dd($hash1);

            // $hashString = $Merchant_key . '|' . $txnid . '|' . $Amount . '|' . $ProductInfo . '|' . $FirstName . '|' . $Email . '|||||||||||' . $SALT;
            // $hash1 = hash('sha512', $hashString);
            //dd($hash1);

            //-----------------
            
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


    public function payment_process(Request $request)
    {
        $response['data'] = [];
        $response['message'] = '';
        $ResposneCode = 400;
        $empty = false;

        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        //dd($aToken);
        if ($aToken['code'] == 200) {
            // dd($request);
               //$client = new Client();
               
              // dd($amount);
               //  $data = [
                    
               //      "key" => "ozLEHc",
               //      "txnid" => "Ytcr-7",
               //      "product_info" => "Event Ticket",
               //      "amount" => "1.00",
               //      "email" => "swt.avap@gmail.com",
               //      "firstname" => "Test",
               //      //"phone" => "8208763029",
               //      "surl" => "https://apiplayground-response.herokuapp.com/",
               //      "furl" => "http://localhost/test/demo.php",
               //      "hash"=> "13ec5624f9b08411f7814bf60ac4f27e6cb026b5f636b7b5d2231aa384b7e2a61ac85fc772eb46380c66476767b02a9be07dc9d40515b3db8dc0ab403013d599"

               //  ];
               //  // dd(json_encode($data));
                
               //  $ch = curl_init('https://secure.payu.in/_payment');
               //  curl_setopt($ch, CURLOPT_POST, 1);
               //  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
               //  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
               //  curl_setopt($ch, CURLOPT_HTTPHEADER, [
               //      'Content-Type: application/json',
               //      'Authorization: Basic ' . base64_encode("ozLEHc" . ':' . "5b15dbc054c7f0ecb471707da52538415535383855d929498c5eee2d76aa078b"),
               //  ]);
               //  $response1 = curl_exec($ch);
               //  curl_close($ch);
               
               // dd($response1);
                
                // $posted = array();

                // $posted['key'] = "ozLEHc";
                // $posted['hash'] = "13ec5624f9b08411f7814bf60ac4f27e6cb026b5f636b7b5d2231aa384b7e2a61ac85fc772eb46380c66476767b02a9be07dc9d40515b3db8dc0ab403013d599";
                // $posted['txnid'] = "Ytcr-7";
                // $posted['firstname'] = "Test";
                // $posted['email'] = "swt.avap@gmail.com";
                // //$posted['phone'] = "8208763029";
                // $posted['amount'] = $amount;
                // $posted['productinfo'] = "Event Ticket";
                // $posted['surl'] = "https://apiplayground-response.herokuapp.com/";
                // $posted['furl'] = "http://localhost/test/demo.php";
                
                // //dd($posted);

      //            $url = "https://secure.payu.in/_payment";
             
      //               $key = 'ozLEHc';
      //               $txnid  = 'Ytcr-8';
      //               $productinfo  = 'Event Ticket';
      //               $amount  = '1.00';
      //               $email  = 'swt.avap@gmail.com';
      //               $firstname  = 'Test';
      //               //'phone'  = '8208763029',
      //               $surl  = 'https://apiplayground-response.herokuapp.com/';
      //               $furl  = 'http://localhost/test/demo.php';
      //               $hash = '8971e7ea7a8499cd48546a51faf919aff64de8db8c1343b22d0bce3cfc3d5d321b1b29031a288e598abfa2f202e5f498206262189b9823fdf56ac98dda0a97a6';

      //                $data = [
      //                   'key' => $key,
      //                   'txnid' => $txnid,
      //                   'productinfo' => $productinfo,
      //                   'amount' => $amount,
      //                   'email' => $email,
      //                   'firstname' => $firstname,
      //                   'surl' => $surl,
      //                   'furl' => $furl,
      //                   'hash' => $hash,
      //               ];

      //   $ch = curl_init($url);
      //   curl_setopt($ch, CURLOPT_POST, 1);
      //   curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
      //   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      //   $result = curl_exec($ch);
      //   curl_close($ch);
      
      // dd($result);
    //  $curl = curl_init();
        
        //  $payuUrl = 'https://secure.payu.in/_payment';
        // $formData = [
        //     'key' => 'ozLEHc',
        //     'txnid' => 'Ytcr-8',
        //     'productinfo' => 'Event Ticket',
        //     'amount' => '1.00',
        //     'email' => 'swt.avap@gmail.com',
        //     'firstname' => 'Test',
        //     // Uncomment the line below if you want to include phone number
        //     // 'phone' => '8208763029',
        //     'surl' => 'https://apiplayground-response.herokuapp.com/',
        //     'furl' => 'http://localhost/test/demo.php',
        //     'hash' => '8971e7ea7a8499cd48546a51faf919aff64de8db8c1343b22d0bce3cfc3d5d321b1b29031a288e598abfa2f202e5f498206262189b9823fdf56ac98dda0a97a6'
        // ];

        // $client = new Client();
        // $response1 = $client->request('POST', $payuUrl, [
        //     'form_params' => $formData
        // ]);
       // dd($response->getBody()->getContents());

            // $html = '<html><body><h3>gggg</h3></body></html>';


            $response['data'] = $html;
            $ResposneCode = 200;
            $response['message'] = 'Request processed successfully';

        }else{
            $ResposneCode = $aToken['code'];
            $response['message'] = $aToken['message'];
        }

        return response()->json($response, $ResposneCode);
    }
    
    public function featch_pay_data(Request $request)
    {
        // $new_data = $_REQUEST;
        $mihpayid = $_POST['mihpayid'];
        $mode = $_POST['mode'];
        $status=$_POST['status'];
        $unmappedstatus=$_POST['unmappedstatus'];
        $key =$_POST['key'];
        $txnid=$_POST['txnid'];
        $amount=$_POST['amount'];
        $discount=$_POST['discount'];
        $net_amount_debit=$_POST['net_amount_debit'];
        $addedon=$_POST['addedon'];
        $productinfo=$_POST['productinfo'];
        $firstname=$_POST['firstname'];
        $lastname=$_POST['lastname'];
        $address1=$_POST['address1'];
        $address2=$_POST['address2'];
        $city =$_POST['city'];
        $state=$_POST['state'];
        $country=$_POST['country'];
        $zipcode=$_POST['zipcode'];
        $email=$_POST['email'];
        $phone=$_POST['phone'];
        $udf1=$_POST['udf1'];
        $udf2=$_POST['udf2'];
        $udf3=$_POST['udf3'];
        $udf4=$_POST['udf4'];
        $udf5=$_POST['udf5'];
        $udf6=$_POST['udf6'];
        $udf7=$_POST['udf7'];
        $udf8=$_POST['udf8'];
        $udf9=$_POST['udf9'];
        $udf10=$_POST['udf10'];
        $hash=$_POST['hash'];
        $field1=$_POST['field1'];
        $field2=$_POST['field2'];
        $field3=$_POST['field3'];
        $field4=$_POST['field4'];
        $field5=$_POST['field5'];
        $field6=$_POST['field6'];
        $field7=$_POST['field7'];
        $field8=$_POST['field8'];
        $field9=$_POST['field9'];
        $payment_source=$_POST['payment_source'];
        $PG_TYPE=$_POST['PG_TYPE'];
        $bank_ref_num=$_POST['bank_ref_num'];
        $bankcode =$_POST['bankcode'];
        $error =$_POST['error'];
        $error_Message =$_POST['error_Message'];

        $transcation_array = array(
            "mihpayid" => $mihpayid,
            "mode"  => $mode,
            "status" => $status,
            "unmappedstatus" => $unmappedstatus,
            "key"   => $key,
            "txnid" => $txnid,
            "amount" => $amount,
            "discount" => $discount,
            "net_amount_debit" => $net_amount_debit,
            "addedon"  => $addedon,
            "productinfo" => $productinfo,
            "firstname" => $firstname,
            "lastname" => $lastname,
            "address1" => $address1,
            "address2" => $address2,
            "city"  => $city,
            "state" => $state,
            "country" => $country,
            "zipcode" => $zipcode,
            "email"  => $email,
            "phone" => $phone,
            "udf1" => $udf1,
            "udf2" => $udf2,
            "udf3" => $udf3,
            "udf4" => $udf4,
            "udf5" => $udf5,
            "udf6" => $udf6,
            "udf7" => $udf7,
            "udf8" => $udf8,
            "udf9" => $udf9,
            "udf10" => $udf10,
            "hash" => $hash,
            "field1" => $field1,
            "field2" => $field2,
            "field3" => $field3,
            "field4" => $field4,
            "field5" => $field5,
            "field6" => $field6,
            "field7" => $field7,
            "field8" => $field8,
            "field9" => $field9,
            "payment_source" => $payment_source,
            "PG_TYPE" => $PG_TYPE,
            "bank_ref_num" => $bank_ref_num,
            "bankcode" => $bankcode,
            "error" => $error,
            "error_Message" => $error_Message
        );
        // print_r($new_array); die;
        $jsonData = json_encode($transcation_array);

        $Binding = array(
                        "status" => $status,
                        "post_data" => $jsonData
                    );
        $insert_SQL = "INSERT INTO new_payment (status,post_data) VALUES(:status,:post_data)";
        DB::insert($insert_SQL, $Binding);
      return redirect()->back();
    }

}
