<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Libraries\Authenticate;

class EventUserFollowController extends Controller
{
   public function Eventuserfollow(Request $request){
    $ResponseData = [];
    $response['message'] = "";
    $ResposneCode = 400;
    $empty = false;
    $aToken = app('App\Http\Controllers\LoginController')->validate_request($request);
    // dd($aToken['data']->ID);

    if ($aToken['code'] == 200) {
        $aPost = $request->all();
        $Auth = new Authenticate();
        $Auth->apiLog($request);

        if (empty($aPost['event_id'])) {
            $empty = true;
            $field = 'Event Id';
        }

        if (!$empty) {
            $sSQL = 'INSERT INTO event_user_follow(
                event_id,user_id,created_at) VALUES (:eventId,:userId,:created_at)';
            
            $Bindings = array(
                    'eventId' => $request->event_id,
                    'userId' => $aToken['data']->ID,
                    'created_at' => strtotime('now')
                );
            // dd($Bindings);

            $ResponseData=DB::insert($sSQL,$Bindings);
            $ResposneCode = 200;
            $message = 'Event user insert successfully';
        } else {     
            $ResposneCode = 400;
            $message = $field . ' is empty';
            // dd($message);
        }
    }else {
        $ResposneCode = $aToken['code'];
        $message = $aToken['message'];
    }

    $response = [
        'data' => $ResponseData,
        'message' => $message
    ];

    return response()->json($response, $ResposneCode);

   }

   public function Eventuserunfollow(Request $request){
    $ResponseData = [];
    $response['message'] = "";
    $ResposneCode = 400;
    $empty = false;
    $aToken = app('App\Http\Controllers\LoginController')->validate_request($request);
    // dd($aToken['data']->ID);

    if ($aToken['code'] == 200) {
        $aPost = $request->all();
        $Auth = new Authenticate();
        $Auth->apiLog($request);

        
        if (empty($aPost['event_id'])) {
            $empty = true;
            $field = 'Event Id';
        }
       
        if (!$empty) {
            $sSQL = 'DELETE FROM event_user_follow WHERE event_id=:event_id ';
            $ResponseData= DB::delete($sSQL,
                array(
                    'event_id' => $request->event_id
                )
            );
            $ResposneCode = 200;
            $message = 'Event user deleted successfully';
        } else {     
            $ResposneCode = 400;
            $message = $field . ' is empty';
            // dd($message);
        }
       
    }else {
        $ResposneCode = $aToken['code'];
        $message = $aToken['message'];
    }

    $response = [
        'data' => $ResponseData,
        'message' => $message
    ];

    return response()->json($response, $ResposneCode);
   }
   

}
