<?php
namespace App\Libraries;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
class SmsApis{

	public function post_sms($mobile,$otp)
	{
		if(!empty($otp)&& !empty($mobile)){
			$url ="http://site.ping4sms.com/api/smsapi?key=aebaa88ce79128bcb31282dcdb6507d5&route=2&sender=YTCRUN&number=$mobile&sms=Dear%20Customer,%20Your%20OTP%20is%20$otp.%20Thank%20You%C2%A0-%C2%A0YTCRUN&templateid=1707165967704553648";
		if( function_exists("curl_init")){
		$ch = curl_init();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_TIMEOUT, 30 );
		curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 0 );
		curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		$response = curl_exec ( $ch );
		//dd($response);
		curl_close($ch);
		}else{
		$return_val = file($url);
		$response = $return_val[0];
		}
		}

	}
}
?>


