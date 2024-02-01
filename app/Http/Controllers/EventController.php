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
    public function DuplicateEvents(Request $request){
        $ResponseData = [];
        $ResposneCode = 200;
        $empty = false;
        $message = 'Success';
        // $field = '';
        $aToken = app('App\Http\Controllers\LoginController')->validate_request($request);
        if ($aToken['code'] == 200) {
            $aPost = $request->all();
            $Auth = new Authenticate();
            $Auth->apiLog($request);

            // dd($request->event_id);

            $sSQL = 'SELECT * FROM events Where id =:eventid AND deleted = 0';
            $eventData = DB::select($sSQL,array(
                'eventid' => $request->event_id
            ));
            // dd($eventData);
            if($eventData){
                $state = (!empty($eventData[0]->state)) ? $eventData[0]->state : 0;
                $country = (!empty($eventData[0]->country)) ? $eventData[0]->country : 0;
                $criteria = (!empty($eventData[0]->criteria)) ? $eventData[0]->criteria : 0;
                $Binding= array(
                    'name'=> $eventData[0]->name,
                    'event_type'=> $eventData[0]->event_type,
                    'ytcr_event_id'=> $eventData[0]->ytcr_event_id,
                    'start_time'=> $eventData[0]->start_time,
                    'end_time'=> $eventData[0]->end_time,
                    'created_date'=> date('Y-m-d', strtotime('now')),
                    'state'=> $state,
                    'country'=> $country,
                    'criteria'=> $criteria
                );
                //  dd($Binding);
                $SQL2 = 'INSERT INTO events (name,event_type,ytcr_event_id,start_time,end_time,state,country,criteria,created_date) VALUES(:name,:event_type,:ytcr_event_id,:start_time,:end_time,:state,:country,:criteria,:created_date)';
                DB::select($SQL2, $Binding);

                $ResposneCode = 200;
                $message = 'Duplicate Event Insert Successfully';

            }else {
                $ResposneCode = 404;
                $message = 'Event not found or already deleted';
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

    public function EventDelete(Request $request){
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $empty = false;
        $aToken = app('App\Http\Controllers\LoginController')->validate_request($request);
        // dd($aToken);
    
        if ($aToken['code'] == 200) {
            $aPost = $request->all();
            $Auth = new Authenticate();
            $Auth->apiLog($request);
        
            $sSQL = 'UPDATE events SET deleted = 1 WHERE id=:id ';
            $ResponseData= DB::delete($sSQL,
                array(
                    'id' => $request->id
                )
            );
            $ResposneCode = 200;
            $message = 'Event Deleted Successfully';
           
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
    
    public function EventStatus(Request $request){
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $aToken = app('App\Http\Controllers\LoginController')->validate_request($request);
        // dd($aToken);
    
        if ($aToken['code'] == 200) {
            $Auth = new Authenticate();
            $Auth->apiLog($request);

            $sSQL = 'SELECT id,is_live FROM events Where id =:id AND deleted = 0';
            $eventData = DB::select($sSQL,array(
                'id' => $request->id
            ));
            // dd($eventData);
            if($eventData){
                $SQL2 = 'UPDATE events SET is_live= :is_live  WHERE id=:id';
                    DB::select($SQL2, array(
                        'id'=>$request->id,
                        'is_live'=>$request->is_live
                    ));

                $ResposneCode = 200;
                $message = 'Event status updated successfully';
            }else {
                $ResposneCode = 404;
                $message = 'Event not found or already deleted';
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
