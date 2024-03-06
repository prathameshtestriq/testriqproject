<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Libraries\Authenticate;


class OrganizerController extends Controller
{
    public function GetOrganizer(Request $request){
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);

        if ($aToken['code'] == 200) {
            $aPost = $request->all();
            $Auth = new Authenticate();
            $Auth->apiLog($request);
          
            $sSQL = 'SELECT * FROM organizers ';
            if ($request->has('id')) {
                $organizerId = $aPost['id'];
                $sSQL .= 'WHERE id = ' . $organizerId;
            }
            $organizerData = DB::select($sSQL);
           
            foreach ($organizerData as $value) {
                $value->organizer_banner_img = (!empty($value->organizer_banner_img)) ? env('ORGANIZER_BANNER_PATH') . $value->organizer_banner_img . '' : '';
                $value->organizer_logo_img = (!empty($value->organizer_logo_img)) ? env('ORGANIZER_LOGO_PATH') . $value->organizer_logo_img . '' : '';
            }
            $ResponseData['organizerData'] = $organizerData;

            $sSQL = 'SELECT * FROM organizer_users';
            if ($request->has('id')) {
                $organizerId = $aPost['id'];
                $sSQL .= ' WHERE organizer_id = ' . $organizerId;
               
            }
            $ResponseData['organizer_users'] = DB::select($sSQL);

            $sSQL = 'SELECT * FROM organizer_events';
            if ($request->has('id')) {
                $organizerId = $aPost['id'];
                $sSQL .= ' WHERE organizer_id = ' . $organizerId;
               
            }
            $ResponseData['organizer_events'] = DB::select($sSQL);

            $sSQL = 'SELECT * FROM organizer_roles';
            if ($request->has('id')) {
                $organizerId = $aPost['id'];
                $sSQL .= ' WHERE organizer_id = ' . $organizerId;
               
            }
            $ResponseData['organizer_roles'] = DB::select($sSQL);

            $sSQL = 'SELECT * FROM organizers_follow';
            if ($request->has('id')) {
                $organizerId = $aPost['id'];
                $sSQL .= ' WHERE organizer_id = ' . $organizerId;   
            }
            $ResponseData['organizers_follow'] = DB::select($sSQL);

        
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
