<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Libraries\Authenticate;
use Illuminate\Support\Facades\DB;

class UserEventDetailsController extends Controller
{
    public function getallUsers(Request $request){
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $aToken = app('App\Http\Controllers\LoginController')->validate_request($request);
        // dd($aToken);

        if ($aToken['code'] == 200) {
            $aPost = $request->all(); 
            $Auth = new Authenticate();
            $Auth->apiLog($request);
                   
            $sSQL = 'SELECT * FROM users Where is_deleted = 0';
            $ResponseData = DB::select($sSQL, array());

            $ResposneCode = 200;
            $message = 'Request processed successfully';


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
    public function getallEvents(Request $request){
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $aToken = app('App\Http\Controllers\LoginController')->validate_request($request);
        // dd($aToken);

        if ($aToken['code'] == 200) {
            $aPost = $request->all(); 
            $Auth = new Authenticate();
            $Auth->apiLog($request);
                   
            $sSQL = 'SELECT * FROM events Where deleted = 0';
            $ResponseData = DB::select($sSQL, array());

            $ResposneCode = 200;
            $message = 'Request processed successfully';

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
