<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Libraries\Authenticate;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Config;

class FormQuestionsController extends Controller
{

    // Event Form Question
    public function event_form_questions(Request $request)
    {
        // dd($request);
        $response['data'] = [];
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
            // $Sql = "SELECT question_label,question_form_type,question_form_name,is_manadatory FROM event_form_question WHERE question_status = 1";
            // $aResult = DB::select($Sql);

            $Sql = 'SELECT event_id,general_form_id,question_label,question_form_type,question_form_name,is_manadatory,question_form_option,is_compulsory FROM event_form_question WHERE question_status = 1 and event_id = '.$EventId.' ';
            $aResult = DB::select($Sql);

            //dd($aResult);
            if(!empty($aResult)){
                $new_array = [];
               
                foreach($aResult as $res){
                    if($res->question_form_type == 'radio' || $res->question_form_type == 'select' || $res->question_form_type == 'checkbox'){ 
                        $res->question_form_option = json_decode($res->question_form_option);
                    }

                    $new_array[] = $res;
                }
                
                $response['data']['form_question'] = $new_array;
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

    // General Form Question
    public function general_form_questions(Request $request)
    {
        // dd($request);
        $response['data'] = [];
        $response['message'] = '';
        $ResposneCode = 400;
        $empty = false;
       
        // $Auth = new Authenticate();
        // $aToken = $Auth->decode_token($request->header('Authorization'));

        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        //dd($aToken);
        if ($aToken['code'] == 200) {
            
            $EventId = !empty($request->event_id) ? $request->event_id : 0;
            $UserId = !empty($request->user_id) ? $request->user_id : 0;
           
            //dd($EventId);

            $Sql = 'SELECT id,question_label,question_form_type,question_form_name,is_manadatory FROM general_form_question WHERE question_status = 1 and is_compulsory != 1 and created_by in (0,'.$UserId.') ';
            $aResult = DB::select($Sql);
            //dd($aResult);
            $general_form_array = [];
            foreach($aResult as $res){

                $Sql = 'SELECT id FROM event_form_question WHERE question_status = 1 and event_id = '.$EventId.' and general_form_id = '.$res->id.'  ';
                $aResult1 = DB::select($Sql);

                if(!empty($aResult1)){
                    $res->event_questions_flag = 1;
                }else{
                    $res->event_questions_flag = 0;
                }
                $general_form_array[] = $res;
            }

            //dd($aResult);
            if(!empty($aResult)){
               
                $response['data']['form_question'] = $general_form_array;
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

    // General Form Question
    public function add_general_form_questions(Request $request)
    {
        //dd($request);
        $response['data'] = [];
        $response['message'] = '';
        $ResposneCode = 400;
        $empty = false;

        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        //dd($aToken);
        if ($aToken['code'] == 200) {

            $EventId = !empty($request->event_id) ? $request->event_id : 0;
            $GeneralFormId = !empty($request->general_form_id) ? $request->general_form_id : 0;
            $QuestionMandatory = !empty($request->question_mandatory_status) ? $request->question_mandatory_status : 0;
            $DisplayLabel = !empty($request->display_label_name) ? $request->display_label_name : 0;
            $LimitLengthCheck = !empty($request->limit_length_check) && ($request->limit_length_check) == true ? 1 : 0;
            $MinLength = !empty($request->min_length) ? $request->min_length : '';
            $MaxLength = !empty($request->max_length) ? $request->max_length : '';
            $FieldMapping = !empty($request->field_mapping) ? $request->field_mapping : '';

            //dd($LimitLengthCheck);
            $limit_length = '';
            if(!empty($MinLength) && !empty($MaxLength)){
                $limit_length = '{ "max_length" : '.$MaxLength.', "min_length" : '.$MinLength.'}';
            }else if(!empty($MinLength) && empty($MaxLength)){
                $limit_length = '{"min_length" : '.$MinLength.'}';
            }else if(empty($MinLength) && !empty($MaxLength)){
                $limit_length = '{"max_length" : '.$MaxLength.'}';
            }
            //dd($limit_length);
           
            $sSQL = 'INSERT INTO event_form_question (event_id, general_form_id, question_label, form_id, question_form_type, question_form_name, question_form_option, is_manadatory, question_status, sort_order, limit_check, user_field_mapping, limit_length)';
                
            $sSQL .= 'SELECT :eventId, id, :questionLabel, form_id, question_form_type, question_form_name, question_form_option, :isManadatory, question_status, sort_order, :limitCheck, :userFieldMapping, :limitLength
                FROM general_form_question
                WHERE `id`=:generalFormId AND question_status = 1 ';
            //dd($sSQL);
            DB::insert($sSQL,array(
                'eventId' => $EventId,
                'questionLabel' => $DisplayLabel,
                'isManadatory'  => $QuestionMandatory,
                'generalFormId' => $GeneralFormId,
                'limitCheck'    => $LimitLengthCheck,
                'userFieldMapping' => $FieldMapping,
                'limitLength' => $limit_length
            ));

            $response['data'] = [];
            $response['message'] = 'Question added successfully';
            $ResposneCode = 200;

        }else{
            $ResposneCode = $aToken['code'];
            $response['message'] = $aToken['message'];
        }
     
        return response()->json($response, $ResposneCode);
    }
    
    // Delete Event Form Questions
    public function delete_event_form_questions(Request $request)
    {
        $response['data'] = [];
        $response['message'] = '';
        $ResposneCode = 400;
        $empty = false;

        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        //dd($aToken);
        if ($aToken['code'] == 200) {
            
            $EventId = !empty($request->event_id) ? $request->event_id : 0;
            $GeneralFormId = !empty($request->general_form_id) ? $request->general_form_id : 0;
            
            $del_sSQL = 'DELETE FROM event_form_question WHERE `event_id`=:eventId AND `general_form_id`=:generalFormId ';
           
            DB::delete($del_sSQL,array(
                'eventId' => $EventId,
                'generalFormId' => $GeneralFormId
            ));

            $response['data'] = [];
            $response['message'] = 'Question removed successfully';
            $ResposneCode = 200;

        }else{
            $ResposneCode = $aToken['code'];
            $response['message'] = $aToken['message'];
        }
     
        return response()->json($response, $ResposneCode);
    }

    // Add Manual Event Form Questions
    public function add_manual_event_form_questions(Request $request)
    {
        $response['data'] = [];
        $response['message'] = '';
        $ResposneCode = 400;
        $empty = false;

        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        //dd($aToken);
        if ($aToken['code'] == 200) {
            
            $EventId = !empty($request->event_id) ? $request->event_id : 0;
             
            $sel_sSQL = 'SELECT id,event_id FROM event_form_question WHERE `event_id` =:eventId limit 1';
            $aResult =  DB::select($sel_sSQL,array('eventId' => $EventId));
            //dd($aResult);

            if(empty($aResult)){
                $sSQL = 'INSERT INTO event_form_question (event_id, general_form_id, question_label, form_id, question_form_type, question_form_name, question_form_option, is_manadatory, question_status, sort_order, is_compulsory)';
                
                $sSQL .= 'SELECT :eventId, id, question_label, form_id, question_form_type, question_form_name, question_form_option, is_manadatory, question_status, sort_order, is_compulsory
                    FROM general_form_question
                    WHERE question_status = 1 AND is_compulsory = 1';
               
                DB::insert($sSQL,array(
                    'eventId' => $EventId,
                ));
            }
          
            $response['data'] = [];
            $response['message'] = 'Question added successfully';
            $ResposneCode = 200;

        }else{
            $ResposneCode = $aToken['code'];
            $response['message'] = $aToken['message'];
        }
     
        return response()->json($response, $ResposneCode);
    }

    // Add Custom Form Questions
    public function add_custom_form_questions(Request $request)
    {
        $response['data'] = [];
        $response['message'] = '';
        $ResposneCode = 400;
        $empty = false;

        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        //dd($aToken);
        if ($aToken['code'] == 200) {
            
            $userId = !empty($request->user_id) ? $request->user_id : 0;
            $questionLabel = !empty($request->question_label) ? $request->question_label : '';
            $questionFormType = !empty($request->question_form_type) ? $request->question_form_type : '';
            $isManadatory = !empty($request->is_manadatory) ? $request->is_manadatory : 0;
            $questionFormOption = !empty($request->question_form_option) ? array_filter($request->question_form_option) : 0;

                //dd(json_encode($questionFormOption)); 
            $new_array = [];
            $i = 1;
            if(!empty($questionFormOption)){
                foreach($questionFormOption as $key=>$res){
                    //dd($res);
                    $new_array[] = array("id" => $i, "label" => $res);
                    $i++;
                }
            }

            $questionFormOptionArray = !empty($new_array) ? json_encode($new_array) : '';
            // dd($questionFormOptionArray);
             
            $sSQL = 'INSERT INTO general_form_question (question_label, form_id, question_form_type, question_form_name, question_form_option, is_manadatory, question_status, created_by, is_custom_form) VALUES (:questionLabel,:formId,:questionFormType,:questionFormName,:questionFormOption,:isManadatory,:questionStatus,:createdBy,:isCustomForm)';

            DB::insert($sSQL,array(
                'questionLabel'     => $questionLabel,
                'formId'            => 1,
                'questionFormType'  => $questionFormType,
                'questionFormName'  => '',
                'questionFormOption' => $questionFormOptionArray,
                'isManadatory'      => $isManadatory,
                'questionStatus'    => 1,
                'createdBy'         => $userId,
                'isCustomForm'      => 1
            ));
      
            $response['data'] = [];
            $response['message'] = 'Question added successfully';
            $ResposneCode = 200;

        }else{
            $ResposneCode = $aToken['code'];
            $response['message'] = $aToken['message'];
        }
     
        return response()->json($response, $ResposneCode);
    }

    // Add Event Setting
    public function add_event_setting(Request $request)
    {
        $response['data'] = [];
        $response['message'] = '';
        $ResposneCode = 400;
        $empty = false;

        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        //dd($aToken);
        if ($aToken['code'] == 200) {
            
            $EventId   = !empty($request->event_id) ? $request->event_id : 0;
            $EventType = !empty($request->event_type) ? $request->event_type : 0;
            $gstCharge = !empty($request->gst_charges) ? $request->gst_charges : 0;
            $gstValue  = !empty($request->gst_value) ? $request->gst_value : '';

            $sSQL = 'INSERT INTO event_setting (event_id, event_type , gst_charge, gst_value) VALUES (:eventId,:eventType,:gstCharge,:gstValue)';

            DB::insert($sSQL,array(
                'eventId'     => $EventId,
                'eventType'   => $EventType,
                'gstCharge'   => $gstCharge,
                'gstValue'    => $gstValue
            ));

            $response['data'] = [];
            $response['message'] = 'Event created successfully';
            $ResposneCode = 200;

        }else{
            $ResposneCode = $aToken['code'];
            $response['message'] = $aToken['message'];
        }
     
        return response()->json($response, $ResposneCode);
    }

    // All Event Details
    public function all_event_details(Request $request)
    {
        $response['data'] = [];
        $response['message'] = '';
        $ResposneCode = 400;
        $empty = false;

        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        //dd($aToken);
        if ($aToken['code'] == 200) {
            
            $EventInfoStatus = !empty($request->event_info_status) ? $request->event_info_status : 0;

            
            $sSQL = 'SELECT vm.id, vm.name, vm.start_time, vm.end_time, vm.registration_end_time, vm.banner_image, vm.display_name, vm.active, vm.event_type, (SELECT name FROM cities WHERE Id = vm.city) AS city, (SELECT name FROM states WHERE Id = vm.state) As state,(SELECT name FROM countries WHERE Id = vm.country) As country, vm.active FROM events AS vm WHERE vm.deleted = 0 ' ;

            if(!empty($EventInfoStatus)){
                $sSQL .= ' and vm.event_info_status = '.$EventInfoStatus;
            }

            $aResult = DB::select($sSQL);
            
            $new_array = [];
            foreach ($aResult as $res) {
                
                $res->event_status   = !empty($res->active) ? true : false;
                $res->event_name   = !empty($res->name) && $res->name != null ? ucfirst($res->name) : '';
                $res->display_name = !empty($res->display_name) && $res->display_name != null ? ucfirst($res->display_name) : '';
                $res->registration_end_date = !empty($res->registration_end_time) ? gmdate("d F Y", $res->registration_end_time) : '';
                $res->start_event_month = (!empty($res->start_time)) ? gmdate("M", $res->start_time) : '';
                $res->start_event_date = (!empty($res->start_time)) ? gmdate("d", $res->start_time) : '';

                #GET ALL TICKETS
                $SQL = "SELECT COUNT(event_id) AS no_of_tickets,min(ticket_price) AS min_price,max(ticket_price) AS max_price FROM event_tickets WHERE event_id=:event_id AND active = 1 AND is_deleted = 0 ORDER BY ticket_price";
                $Tickets = DB::select($SQL, array('event_id' => $res->id));

                $res->min_price = !empty($Tickets) && !empty($Tickets[0]->min_price) ? $Tickets[0]->min_price : 0;
                $res->max_price = !empty($Tickets) && !empty($Tickets[0]->max_price) ? $Tickets[0]->max_price : 0;
                $res->banner_image = !empty($res->banner_image) ? url('/') . '/uploads/banner_image/' . $res->banner_image . '' : '';

                $new_array[] = $res;
            }
            //dd($new_array);

            // $EventSql = "SELECT e.* FROM events AS e WHERE e.active=1 AND e.deleted=0";
            // $UpcomingSql = "SELECT * from events AS u WHERE u.active=1 AND u.deleted=0 AND u.start_time >=:start_time";
            // // $UpcomingEvents = DB::select($UpcomingSql, array('start_time' => $NowTime));
            // $BannerSql = "SELECT b.* FROM banner AS b WHERE b.active=1";

            // $CountryCode = $request->country_code;
            // $City = isset($request->city) ? $request->city : '';
            // $State = isset($request->state) ? $request->state : '';

            // $city_id = isset($request->scity) ? $request->scity : 0;
            // $state_id = isset($request->sstate) ? $request->sstate : 0;
            // $country_id = isset($request->scountry) ? $request->scountry : 0;
            // // dd($state_id);
            // if (!empty($CountryCode)) {
            //     $sSQL = 'SELECT id FROM countries WHERE LOWER(country_code) =:country_code';
            //     $CountryId = DB::select($sSQL, array('country_code' => strtolower($CountryCode)));
            //     // dd($CountryId);
            //     if (sizeof($CountryId) > 0) {
            //         $EventSql .= ' AND e.country=' . $CountryId[0]->id;
            //         $BannerSql .= ' AND b.country=' . $CountryId[0]->id;
            //         $UpcomingSql .= ' AND u.country=' . $CountryId[0]->id;
            //         $ResponseData['CountryId'] = $CountryId[0]->id;
            //     }
            // }
            // // dd($EventSql,$BannerSql);
            // if (!empty($City)) {
            //     $sSQL = 'SELECT id,name FROM cities WHERE LOWER(name) =:name';
            //     $CityId = DB::select($sSQL, array('name' => strtolower($City)));
            //     if (sizeof($CityId) > 0) {
            //         $EventSql .= ' AND e.city=' . $CityId[0]->id;
            //         $BannerSql .= ' AND b.city=' . $CityId[0]->id;
            //         $UpcomingSql .= ' AND u.city=' . $CityId[0]->id;
            //         $ResponseData['CityId'] = $CityId[0]->id;
            //         $ResponseData['CityName'] = $CityId[0]->name;
            //     }
            // }
            // if (!empty($State)) {
            //     $sSQL = 'SELECT id FROM states WHERE LOWER(name) =:name';
            //     $StateId = DB::select($sSQL, array('name' => strtolower($State)));
            //     if (sizeof($StateId) > 0) {
            //         $EventSql .= ' AND e.state=' . $StateId[0]->id;
            //         $BannerSql .= ' AND b.state=' . $StateId[0]->id;
            //         $UpcomingSql .= ' AND u.state=' . $StateId[0]->id;
            //         $ResponseData['StateId'] = $StateId[0]->id;
            //     }
            // }

            
            // $Events = DB::select($EventSql);
            // $Banners = DB::select($BannerSql);
            // $UpcomingEvents = DB::select($UpcomingSql, array('start_time' => $NowTime));
            
            // $BannerImages = [];
            // foreach ($Banners as $key => $banner) {
                
            // }

            

            $response['data'] = $new_array;
            $response['message'] = 'Request processed successfully';
            $ResposneCode = 200;

        }else{
            $ResposneCode = $aToken['code'];
            $response['message'] = $aToken['message'];
        }
     
        return response()->json($response, $ResposneCode);
    }

    public function event_delete_change_status(Request $request)
    {
        $response['data'] = [];
        $response['message'] = '';
        $ResposneCode = 400;
        $empty = false;

        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        //dd($aToken);
        if ($aToken['code'] == 200) {
            
            $EventId     = !empty($request->event_id) ? $request->event_id : 0;
            $EventStatus = 0;
            if(!empty($request->event_status)){
                if($request->event_status == 'true'){
                    $EventStatus = 1;
                }else if($request->event_status == 'false'){
                    $EventStatus = 0;
                }
            }
          
            //dd($EventStatus);
            $ActionFlag  = !empty($request->action_flag) ? $request->action_flag : '';
            $msg = '';

            if($ActionFlag == 'change_status'){
                $status_sSQL = 'UPDATE events SET `active` =:eventStatus WHERE `id`=:eventId ';
                DB::update($status_sSQL,array(
                    'eventId' => $EventId,
                    'eventStatus' => $EventStatus
                ));
                $msg = 'Event status change successfully';
            }else if($ActionFlag == 'delete'){
                $del_sSQL = 'UPDATE events SET `deleted` =:eventDelete WHERE `id`=:eventId ';
                DB::update($del_sSQL,array(
                    'eventId' => $EventId,
                    'eventDelete' => 1
                ));
                $msg = 'Event delete successfully';
            }
           
            $response['data'] = [];
            $response['message'] = $msg;
            $ResposneCode = 200;

        }else{
            $ResposneCode = $aToken['code'];
            $response['message'] = $aToken['message'];
        }
     
        return response()->json($response, $ResposneCode);
    }


}
