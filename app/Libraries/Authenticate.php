<?php
namespace App\Libraries;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Models\Log;


class Authenticate
{
	public function create_token($input)
	{
		$ExpireSeonds = 60 * 60 * 24 * 365;
		// $ExpireSeonds = 60*60;
		$input['start_time'] = strtotime('now');
		$input['end_time'] = strtotime('now +' . $ExpireSeonds . ' seconds');
		$iv = hex2bin('FAB0016AF0B0012AFBB0012AF0B0012A');
		$encripted_token = openssl_encrypt(json_encode($input), "AES-128-CBC", '5APA723471UE41254D42A47N2D67GMAT78RIC', 0, $iv);
		$encripted_token = str_replace('+', '^', $encripted_token);
		return $encripted_token;
	}

	public function authenticate_token($token)
	{

		// $token = $request->header('Authorization');

		// $result = array();
		$aToken = $this->decode_token($token);

		// $Event = isset($request->event) ? $request->event : 0;
		// $AthleteId = isset($aToken->ID) ? $aToken->ID : 0;

		// if (!empty($Event) && !empty($AthleteId)) {
		// 	$sql = "SELECT id,auth_token FROM event_atheletes WHERE event_id=:event_id AND athelete_id=:athelete_id";
		// 	$result = DB::select($sql, ['event_id' => $Event, 'athelete_id' => $AthleteId]);

		// 	if (!empty($result)) {
		// 		$AuthToken = isset($result[0]->auth_token) ? $result[0]->auth_token : 0;
		// 		// dd($AuthToken,$token);
		// 		if((empty($AuthToken)) && ($Event == 1)){
		// 			$aToken = $this->decode_token($token);
		// 		}
		// 		if(empty($AuthToken) && ($Event != 1)) $AuthToken = 0;
		// 		// if((!empty($AuthToken)) && ($AuthToken != $token))
		// 		if((!empty($AuthToken)) && ($AuthToken == $token))
		// 			$aToken = $this->decode_token($result[0]->auth_token);

		// 		// if ((!empty($result[0]->auth_token))) {
		// 		// 	$aToken = $this->decode_token($result[0]->auth_token);
		// 		// 	$auth_token = $token;
		// 		// 	$msgFlag = 'Not Empty Token';

		// 		// 	if($token == $result[0]->auth_token) $msgFlag = 'Token Equal';
		// 		// 	else $msgFlag = 'Token Not Equal';
		// 		// } else {
		// 		// 	$auth_token = '';
		// 		// }
		// 	}
		// 	// else {
		// 	// 	$auth_token = '';
		// 	// }
		// 	// $aToken = $this->decode_token($result[0]->auth_token);
		// 	// dd($result);
		// }
		// dd($result,$aToken);
		// if((!empty($AuthToken)) && ($AuthToken != $token)) {
		// 	$return['code'] = 401;
		// 	$return['message'] = 'You have logged in from another device';
		// } elseif ((empty($AuthToken)) && ($Event != 1)) {
		// 	$return['code'] = 201;
		// 	$return['message'] = 'Logout Successfully';
		// } else
		if (empty($aToken->end_time) || empty($aToken->ID)) {
			$return['code'] = 401;
			$return['message'] = 'Forbidden';
		} elseif (empty($aToken->end_time) || $aToken->end_time < strtotime('now')) {
			// $return['code'] = 401;
			$return['code'] = 200;
			$return['message'] = 'Login expired.';
			$return['data'] = $aToken;
		} else {
			$return['code'] = 200;
			$return['message'] = 'success';
			$return['data'] = $aToken;
		}
		// dd($return);
		return $return;
	}

	public function decode_token($token)
	{
		$sToekn = substr($token, 7);
		$atemp = explode(" ", $sToekn);
		$sToekn = implode('+', $atemp);
		$sToekn = str_replace('^', '+', $sToekn);
		$iv = hex2bin('FAB0016AF0B0012AFBB0012AF0B0012A');
		$sToekn = openssl_decrypt($sToekn, "AES-128-CBC", '5APA723471UE41254D42A47N2D67GMAT78RIC', 0, $iv);
		$aToken = json_decode($sToekn);
		return $aToken;
	}

	// public function apiLog($Request, $Event, $AthleteId, $action)
	// {
	// 	$RequestData = $Request->all();
	// 	// dd($RequestData);
	// 	$data = array(
	// 		'event_id' => isset($Event) ? $Event : 0,
	// 		'athlete_id' => isset($AthleteId) ? $AthleteId : 0,
	// 		'action' => isset($action) ? $action : "",
	// 		'url' => $Request->url(),
	// 		'method' => $Request->method(),
	// 		'header' => $Request->header('authorization'),
	// 		'post_data' => json_encode($RequestData),
	// 		'created' => date("Y-m-d H:i:s")
	// 	);
	// 	DB::table('log')->insert($data);
	// }

    public function apiLog(Request $Request, $CreatedBy = 0,$action=NULL){
		$RequestData = $Request->all();
        if(!empty($_FILES)){
			$RequestData['LogFiles'] = $_FILES;
		}

        $Log = new Log;
        $Log->url = $Request->url();
        $Log->method= $Request->method();
        $Log->event_id = isset($Request->event_id) ? $Request->event_id : 0;
        $Log->user_id = isset($Request->user_id) ? $Request->user_id : 0;
        $Log->action = isset($action) ? $action : "";
        $Log->post_data = json_encode($RequestData);
        $Log->created_timestamp = date("Y-m-d H:i:s");
        $Log->header = !empty($Request->header('Authorization')) ? $Request->header('Authorization') : "";
		// $Log->server_ip = $_SERVER['REMOTE_ADDR'];
		$Log->created_by = $CreatedBy;
        $Log->save();
		return $Log->id;
    }

}
