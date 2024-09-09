<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Libraries\Authenticate;
use Illuminate\Support\Facades\DB;
use App\Models\Master;
use App\Models\Event;

class EventDetailsController extends Controller
{
   
    public function page_view_details(Request $request) 
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

            $EventId    = !empty($request->event_id) ? $request->event_id : 0;
            $IpAddress  = !empty($request->ip_address) ? $request->ip_address : '';
           
            $Sql = 'SELECT id,event_id,ip_address FROM page_views WHERE event_id = '.$EventId.' and ip_address = "'.$IpAddress.'" ';
            $aResult = DB::select($Sql);
            
          
            if(empty($aResult)) {

            	$last_updated_datetime = date('Y-m-d h:i:s A');

	            $Binding = array(
	                "event_id" 	            => $EventId,
	                "ip_address"            => $IpAddress,
	                "last_updated_datetime" => $last_updated_datetime,
	                "created_by"            => $UserId
	            );
	            // dd($Binding);
	            $insert_SQL1 = "INSERT INTO page_views (event_id,ip_address,last_updated_datetime,created_by) VALUES(:event_id,:ip_address,:last_updated_datetime,:created_by)";
	            DB::insert($insert_SQL1, $Binding);
               
            }

            $response['message'] = 'Request processed successfully';
            $ResposneCode = 200;

        } else {
            $ResposneCode = $aToken['code'];
            $response['message'] = $aToken['message'];
        }

        return response()->json($response, $ResposneCode);
    }

    public function check_organizer_user_details(Request $request)
    {
        // dd($request);
        $response['data'] = [];
        $response['message'] = '';
        $ResposneCode = 400;
        $empty = false;

        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        //dd($aToken);
        // if ($aToken['code'] == 200) {

        //     $UserId = 0;
        //     if (!empty($aToken)) {
        //         $UserId = $aToken['data']->ID;
        //     }

            $OrganizerUserId  = !empty($request->organizer_user) ? $request->organizer_user : 0;
            $UserEmail        = !empty($request->user_email) ? $request->user_email : '';
           
            $Sql = 'SELECT id FROM users WHERE email = "'.$UserEmail.'" ';
            $aResult = DB::select($Sql);
            
            $response['data'] = !empty($aResult) ? $aResult : [];
            $response['message'] = 'Request processed successfully';
            $ResposneCode = 200;

        // } else {
        //     $ResposneCode = $aToken['code'];
        //     $response['message'] = $aToken['message'];
        // }

        return response()->json($response, $ResposneCode);
    }

    public function communication_master_details(Request $request) 
    {
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

            $Sql = 'SELECT id,subject_name FROM communication_master WHERE status = 1';
            $aResult = DB::select($Sql);
            $response['data'] = !empty($aResult) ? $aResult : [];
          
            $response['message'] = 'Request processed successfully';
            $ResposneCode = 200;

        } else {
            $ResposneCode = $aToken['code'];
            $response['message'] = $aToken['message'];
        }

        return response()->json($response, $ResposneCode);
    }

    public function check_user_last_login_details(Request $request) 
    {
        $response['data'] = [];
        $response['message'] = '';
        $ResposneCode = 400;
        $empty = false;

        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        // dd($aToken);
        if ($aToken['code'] == 200) {

            $UserId = 0;
            if (!empty($aToken)) {
                $UserId = $aToken['data']->ID;
            }

            $UserLoginId     = !empty($request->user_id) ? $request->user_id : $UserId;
            $currentDateTime = time();
            $LastLoginFlag = 0;
           // dd($TenDaysAgo);
            if(!empty($UserLoginId)){
                $Sql = 'SELECT id,login_time FROM users WHERE is_active = 1 AND id = '.$UserLoginId.' ';
                $aResult = DB::select($Sql);
               
                $lastLoginDays = config('custom.last_login_days');
                if(!empty($aResult)){
                    $futureDate = strtotime($lastLoginDays, $aResult[0]->login_time);
                    $formattedFutureDate = date('Y-m-d H:i:s', $futureDate);
                    $lastTenDayDate = strtotime($formattedFutureDate);
                    //dd($aResult[0]->login_time, $lastTenDayDate);
                    if($currentDateTime > $lastTenDayDate){
                        $LastLoginFlag = 1;
                    }else{
                        $LastLoginFlag = 0;
                    }
                }
                $response['data'] = !empty($aResult) ? $LastLoginFlag : 0;
            }
                     
            $response['message'] = 'Request processed successfully';
            $ResposneCode = 200;

        } else {
            $ResposneCode = $aToken['code'];
            $response['message'] = $aToken['message'];
        }

        return response()->json($response, $ResposneCode);
    }


}
