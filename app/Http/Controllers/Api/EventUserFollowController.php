<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Libraries\Authenticate;

class EventUserFollowController extends Controller
{
    public function Eventuserfollow(Request $request)
    {
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $empty = false;
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
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
                $UserId = $aToken['data']->ID;
                $EventId = $aPost['event_id'];

                if (empty($aPost['is_follow'])) { //is_follow == 0 then need to follow means add entry in table
                    $sSQL = 'INSERT INTO event_user_follow(
                    event_id,user_id,created_at) VALUES (:eventId,:userId,:created_at)';

                    $Bindings = array(
                        'eventId' => $EventId,
                        'userId' => $UserId,
                        'created_at' => strtotime('now')
                    );
                    $ResponseData = DB::insert($sSQL, $Bindings);
                    $message = 'Event wishlisted successfully';

                } else { //is_follow == 1 then need to unfollow means delete the entry form table
                    $sSQL = 'DELETE FROM event_user_follow WHERE event_id=:event_id AND user_id=:user_id';
                    $ResponseData = DB::delete(
                        $sSQL,
                        array(
                            'event_id' => $EventId,
                            'user_id' => $UserId
                        )
                    );
                    $message = 'Event remove from wishlisted successfully';
                }
                $ResposneCode = 200;
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

    public function OrgEventuserfollow(Request $request)
    {
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $empty = false;
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        // dd($aToken['data']->ID);

        if ($aToken['code'] == 200) {
            $aPost = $request->all();
            $Auth = new Authenticate();
            $Auth->apiLog($request);

            $UserId = $aToken['data']->ID;
            $OrgId = $aPost['org_id'];

            if (empty($aPost['is_follow'])) { //is_follow == 0 then need to follow means add entry in table
                $sSQL = 'INSERT INTO organizers_follow(
                    organizer_id,user_id,created_at) VALUES (:orgId,:userId,:created_at)';

                $Bindings = array(
                    'orgId' => $OrgId,
                    'userId' => $UserId,
                    'created_at' => strtotime('now')
                );
                $ResponseData = DB::insert($sSQL, $Bindings);
                $message = 'Organizer wishlisted successfully';

            } else { //is_follow == 1 then need to unfollow means delete the entry form table
                $sSQL = 'DELETE FROM organizers_follow WHERE organizer_id=:orgId AND user_id=:user_id';
                $ResponseData = DB::delete(
                    $sSQL,
                    array(
                        'orgId' => $OrgId,
                        'user_id' => $UserId
                    )
                );
                $message = 'Organizer remove from wishlisted successfully';
            }
            $ResposneCode = 200;
            //  }
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
