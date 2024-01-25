<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Libraries\Authenticate;
use Illuminate\Support\Facades\DB;
use App\Models\Event;


class EventController extends Controller
{
    public function getEvents(Request $request)
    {
        // return($request->header());

        $ResponseData = [];
        $ResposneCode = 200;
        $empty = false;
        $message = 'Success';
        $field = '';

        $aToken = app('App\Http\Controllers\LoginController')->validate_request($request);
        // dd($aToken);
        if ($aToken['code'] == 200) {
        $aData = array();
        $aPost = $request->all();

        $Auth = new Authenticate();
        $Auth->apiLog($request);

        $ResponseData['eventData'] = Event::get($aData)->orderBy('id', 'DESC')->paginate(config('custom.page_limit'));
        // dd($ResponseData);
        } else {
            $ResposneCode = $aToken['code'];
            $message = $aToken['message'];
        }

        $response = [
            'status' => 'success',
            'data' => $ResponseData,
            'message' => $message
        ];

        return response()->json($response, $ResposneCode);

    }


}
