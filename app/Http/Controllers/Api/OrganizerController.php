<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Libraries\Authenticate;
use App\Models\Master;

class OrganizerController extends Controller
{
    public function getOrganizerDetails(Request $request)
    {
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
                $value->organizer_banner_img = (!empty ($value->organizer_banner_img)) ? env('ORGANIZER_BANNER_PATH') . $value->organizer_banner_img . '' : '';
                $value->organizer_logo_img = (!empty ($value->organizer_logo_img)) ? env('ORGANIZER_LOGO_PATH') . $value->organizer_logo_img . '' : '';
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

        } else {
            $ResposneCode = $aToken['code'];
            $message = $aToken['message'];
        }

        $response = [
            'data' => $ResponseData,
            'message' => $message
        ];

        return response()->json($response, $ResposneCode);

    }

    // public function getRoles(Request $request)
    // {
    //     $ResponseData = [];
    //     $message = "";
    //     $ResposneCode = 400;
    //     $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);

    //     if ($aToken['code'] == 200) {
    //         $aPost = $request->all();
    //         $Auth = new Authenticate();
    //         $Auth->apiLog($request);

    //         $Roles = DB::table('master_roles')
    //             ->select('id', 'role_name', 'access')
    //             ->where('active', '=', 1)
    //             ->get();
    //         $ResponseData['roles'] = $Roles;

    //         $ResposneCode = 200;
    //         $message = 'Roles getting successfully';

    //     } else {
    //         $ResposneCode = $aToken['code'];
    //         $message = $aToken['message'];
    //     }

    //     $response = [
    //         'data' => $ResponseData,
    //         'message' => $message
    //     ];

    //     return response()->json($response, $ResposneCode);
    // }

    function getOrganizingTeam(Request $request)
    {
        $ResponseData = [];
        $message = "";
        $ResposneCode = 400;
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);

        if ($aToken['code'] == 200) {
            $aPost = $request->all();
            $Auth = new Authenticate();
            $Auth->apiLog($request);

            $UserId = $aToken['data']->ID;

            #GETTING ORGANIZERS OF USER
            $SQL = "SELECT * FROM users WHERE parent_id IN (" . $UserId . ")";
            $Organizers = DB::select($SQL);
            // dd($Users);
            $ResponseData['organizers'] = $Organizers;


            #GETTING EVENTS OF USER
            $sql = "SELECT e.id,e.name,e.city,e.start_time FROM events AS e LEFT JOIN event_users AS u ON u.event_id=e.id WHERE u.user_id=:user_id AND  e.active=1 AND e.deleted=0";
            $Events = DB::select($sql, array('user_id' => $UserId));
            // dd($Events);
            // $ResponseData['events'] = app('App\Http\Controllers\Api\EventController')->ManipulateEvents($Events,$UserId);;
            $master = new Master();
            foreach ($Events as $event) {
                $event->checked = 0;
                $event->start_date = (!empty ($event->start_time)) ? gmdate("d-m-Y", $event->start_time) : 0;
                $event->city_name = !empty ($event->city) ? $master->getCityName($event->city) : "";
            }
            $ResponseData['events'] = $Events;
            #ROLES
            $Roles = DB::table('master_roles')
                ->select('id', 'role_name', 'access')
                ->where('active', '=', 1)
                ->get();
            foreach ($Roles as $role) {
                $role->checked = 0;
            }
            $ResponseData['roles'] = $Roles;

            $ResposneCode = 200;
            $message = 'Data getting successfully';

        } else {
            $ResposneCode = $aToken['code'];
            $message = $aToken['message'];
        }

        $response = [
            'data' => $ResponseData,
            'message' => $message
        ];

        return response()->json($response, $ResposneCode);
    }

    public function addEditOrganizer(Request $request)
    {
        $ResponseData = [];
        $message = "";
        $ResposneCode = 400;
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);

        if ($aToken['code'] == 200) {
            $aPost = $request->all();
            $Auth = new Authenticate();
            $Auth->apiLog($request);

            $UserId = $aToken['data']->ID;

            if (empty ($aPost['name'])) {
                $empty = true;
                $field = 'Name';
            }
            if (empty ($aPost['email'])) {
                $empty = true;
                $field = 'Email Id';
            }
            if (empty ($aPost['roles'])) {
                $empty = true;
                $field = 'Roles';
            }
            if (empty ($aPost['events'])) {
                $empty = true;
                $field = 'Events';
            }
            if (!$empty) {
                $Auth = new Authenticate();
                $Auth->apiLog($request);

                #CHECK USER IS EXIST OR NOT
                $SQL = "SELECT email FROM users WHERE email=:email";
                $IsExist = DB::select($SQL, array('email' => $aPost['email']));

                if (count($IsExist) === 0) {

                    // $ResponseData['organizers'] = $Organizers;
                    $ResposneCode = 200;
                    $message = 'Data getting successfully';
                } else {
                    $ResposneCode = 400;
                    $message = 'Organizer is exist';
                }
            } else {
                $ResposneCode = 400;
                $message = $field . ' is empty';
            }
        } else {
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
