<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Libraries\Authenticate;
use Illuminate\Support\Facades\DB;

class MaintanceController extends Controller
{
    public function Getmaintancemode(Request $request)
    {
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        // $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
       

        // if ($aToken['code'] == 200) {
            $aPost = $request->all();
            $Auth = new Authenticate();
            $Auth->apiLog($request);

            $sSQL = 'SELECT * FROM races_settings';
            $maintance_mode = DB::select($sSQL, array());
            // dd( $maintance_mode);
            
            if (!empty($maintance_mode)) {
                $ResponseData['maintance_mode'] = $maintance_mode;
            }
            // dd( $ResponseData);
            $ResposneCode = 200;
            $message = 'Request processed successfully';

        // }else {
        //     $ResposneCode = $aToken['code'];
        //     $message = $aToken['message'];
        // }

        $response = [
            'data' => $ResponseData,
            'message' => $message
        ];

        return response()->json($response, $ResposneCode);
    }
}
