<?php
// Replace these with your actual PhonePe API credentials
$merchantId 	= 'M22NRF5GDVOVX'; // sandbox or test merchantId
$apiKey			= "5523ea88-9455-4c97-a068-ca283d679b1d"; // sandbox or test APIKEY
$redirectUrl 	= 'payment-success.php';

// Set transaction details
$order_id 	 = uniqid(); 
$name		 = "Races Website";
$email		 = "support@youtoocanrun.com";
$mobile		 = 9999999999;
$amount 	 = 10; // amount in INR
$description = 'Payment for event registration';

// Create Payload Data
$paymentData = array(
    'merchantId' => $merchantId,
    'merchantTransactionId' => "YTCR7000590068188104", // Should be unique every time
    "merchantUserId"=>"YTCR00002",
    'amount' => $amount*100,	// in paise
    'redirectUrl'=>$redirectUrl,
    'redirectMode'=>"POST",
    'callbackUrl'=>$redirectUrl,
    "merchantOrderId"=>$order_id,
    "mobileNumber"=>$mobile,
    "message"=>$description,
    "email"=>$email,
    "shortName"=>$name,
    "paymentInstrument"=> array(    
      "type"=> "PAY_PAGE",
    )
);

 $jsonencode = json_encode($paymentData);
 $payloadMain = base64_encode($jsonencode);
 $salt_index = 1; //key index 1
 $payload = $payloadMain . "/pg/v1/pay" . $apiKey;	// end point added
 $sha256 = hash("sha256", $payload);
 $final_x_header = $sha256 . '###' . $salt_index;
 $request = json_encode(array('request'=>$payloadMain));
                
$curl = curl_init();
curl_setopt_array($curl, [
  CURLOPT_URL => "https://api.phonepe.com/apis/hermes/pg/v1/pay",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => $request,
  CURLOPT_HTTPHEADER => [
    "Content-Type: application/json",
     "X-VERIFY: " . $final_x_header,
     "accept: application/json"
  ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);
//echo '<pre>'; print_r($response);   die;    
curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
	$res = json_decode($response);
 
	if(isset($res->success) && $res->success=='1'){
		$paymentCode=$res->code;
		$paymentMsg=$res->message;
		$payUrl=$res->data->instrumentResponse->redirectInfo->url;

		header('Location:'.$payUrl) ;	// If request is successfully created, it will redirect to payment page 
	}
}        
?>
