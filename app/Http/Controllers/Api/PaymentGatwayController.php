<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Libraries\Authenticate;
use Illuminate\Support\Facades\DB;

class PaymentGatwayController extends Controller
{
   
    // Event Form Question
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
                   $res->transaction_id = 'Ytcr-1';
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
}
