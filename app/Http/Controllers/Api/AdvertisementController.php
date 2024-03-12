<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Libraries\Authenticate;
use Illuminate\Support\Facades\DB;

class AdvertisementController extends Controller
{
    public function GetAdvertisement(Request $request)
    {
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        // $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        // dd($aToken);

        // if ($aToken['code'] == 200) {
        $aPost = $request->all();
        $Auth = new Authenticate();
        $Auth->apiLog($request);

        $sSQL = 'SELECT * FROM advertisement WHERE status = 1';
        $advertisement = DB::select($sSQL, array());
        // dd( $ResponseData);
        foreach ($advertisement as $value) {
            $value->img = (!empty($value->img)) ? url('/').'/uploads/images/'. $value->img . '' : '';
        }

        if (!empty($advertisement)) {
            $ResponseData['advertisement'] = $advertisement;
        }

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
