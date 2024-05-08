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

            $Sql = 'SELECT id,event_id,general_form_id,question_label,question_form_type,question_form_name,is_manadatory,question_form_option,is_compulsory,is_subquestion,sort_order,child_question_ids FROM event_form_question WHERE question_status = 1 and event_id = '.$EventId.' and is_subquestion = 0 order by sort_order asc';
            $aResult = DB::select($Sql);

            //dd($aResult);
            if(!empty($aResult)){
                $new_array = [];

                foreach($aResult as $res){
                    if($res->question_form_type == 'radio' || $res->question_form_type == 'select' || $res->question_form_type == 'checkbox'){
                        $res->question_form_option = json_decode($res->question_form_option);
                    }

                    if($res->child_question_ids !== ''){
                        $Sql1 = 'SELECT id,event_id,general_form_id,question_label,question_form_type,question_form_name,is_manadatory,question_form_option,is_compulsory,is_subquestion,sort_order,child_question_ids FROM event_form_question WHERE question_status = 1 and event_id = '.$EventId.' and is_subquestion = 1 and general_form_id IN('.$res->child_question_ids.')  order by general_form_id asc';
                        $sub_questions_aResult = DB::select($Sql1);
                        //dd($sub_questions_aResult);
                        $res->sub_questions_array = !empty($sub_questions_aResult) ? $sub_questions_aResult : [];
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
         //dd($request);
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

          //  $Sql = 'SELECT id,question_label,question_form_type,question_form_name,is_manadatory,question_form_option,is_subquestion,parent_question_id,form_id,(select form_name from form_master where form_master.id = general_form_question.form_id) as form_name FROM general_form_question WHERE question_status = 1 and is_compulsory != 1 and created_by in (0,'.$UserId.') and is_subquestion = 0  '; // 
            $Sql = 'SELECT id,question_label,question_form_type,question_form_name,is_manadatory,question_form_option,is_subquestion,parent_question_id,form_id FROM general_form_question WHERE question_status = 1 and is_compulsory != 1 and created_by in (0,'.$UserId.') and is_subquestion = 0  ';
            $aResult = DB::select($Sql);
            //dd($aResult);
            $general_form_array = [];
            foreach($aResult as $res){

                $Sql = 'SELECT id,child_question_ids FROM event_form_question WHERE question_status = 1 and event_id = '.$EventId.' and general_form_id = '.$res->id.'  ';
                $aResult1 = DB::select($Sql);
                //dd($aResult1);
                if(!empty($aResult1)){
                    $res->event_questions_flag = 1;
                    $res->sub_questions_added_flag = !empty($aResult1[0]->child_question_ids) ? 1 : 0;
                }else{
                    $res->event_questions_flag = 0;
                    $res->sub_questions_added_flag = 0;
                    $event_question_form_option = [];
                }

                //---------------------
                if($res->question_form_type == 'radio' || $res->question_form_type == 'select' || $res->question_form_type == 'checkbox' || $res->question_form_type == 'select_amount'){
                    $res->question_form_option = !empty($res->question_form_option) ? json_decode($res->question_form_option) : [];
                    $que_option_json_count = count($res->question_form_option);
                }else{ $res->question_form_option = []; $que_option_json_count = 0; }

                //-------- sub question
                $Sql1 = 'SELECT count(id) as tot_count FROM general_form_question WHERE question_status = 1 and created_by = '.$UserId.' and parent_question_id = '.$res->id.'  ';
                $aResult2 = DB::select($Sql1);

                if(!empty($aResult2) && !empty($aResult2[0]->tot_count)){

                    if($que_option_json_count == $aResult2[0]->tot_count){
                        $res->sub_question_json_tick_count = 1;
                    }else{
                        $res->sub_question_json_tick_count = 0;
                    }
                }else{ $res->sub_question_json_tick_count = 0;}

                //--------- sub question arrray-----------
                // if($res->question_form_option){
                //     foreach($res->question_form_option as $res1){
                //         $sub_question_id = isset($res1->child_question_id) ? $res1->child_question_id : 0;

                //         if(!empty($sub_question_id)){
                //             $Sql = 'SELECT question_form_option FROM general_form_question WHERE question_status = 1 and id = '.$sub_question_id.'  ';
                //             $aResult1 = DB::select($Sql);

                //             $res->sub_question_array[$res1->label] = !empty($aResult1[0]->question_form_option) ? json_decode($aResult1[0]->question_form_option) : [];
                //         }
                //     }
                // }else{ $res->sub_question_array = []; }
                $general_form_array[] = $res;
                //$general_form_array[$res->form_name][] = $res;
            }//die;

            //dd($general_form_array);
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
        // dd($request);
        $response['data'] = [];
        $response['message'] = '';
        $ResposneCode = 400;
        $empty = false;

        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        //dd($aToken);
        if ($aToken['code'] == 200) {

            $EventId = !empty($request->event_id) ? $request->event_id : 0;
            $userId = !empty($request->user_id) ? $request->user_id : 0;
            $GeneralFormId = !empty($request->general_form_id) ? $request->general_form_id : 0;
            $QuestionMandatory = !empty($request->question_mandatory_status) ? $request->question_mandatory_status : 0;
            $DisplayLabel = !empty($request->display_label_name) ? $request->display_label_name : 0;
            $LimitLengthCheck = !empty($request->limit_length_check) && ($request->limit_length_check) == true ? 1 : 0;
            $MinLength = !empty($request->min_length) ? $request->min_length : '';
            $MaxLength = !empty($request->max_length) ? $request->max_length : '';
            $FieldMapping = !empty($request->field_mapping) ? $request->field_mapping : '';
            $questionHint = !empty($request->question_hint) ? $request->question_hint : '';

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
            //--------- check duplication
            $Sql5 = 'SELECT count(id) as tot_count FROM event_form_question WHERE question_status = 1 and general_form_id = '.$GeneralFormId.'  and event_id = '.$EventId.' ';
            $aResult5 = DB::select($Sql5);
            //dd($aResult5);
          
            if(!empty($aResult5) && $aResult5[0]->tot_count == 0)
            {
                $sSQL = 'INSERT INTO event_form_question (event_id, general_form_id, question_label, form_id, question_form_type, question_form_name, question_form_option, is_manadatory, question_status, sort_order, is_subquestion, parent_question_id, is_compulsory, created_by, is_custom_form, limit_check, user_field_mapping, limit_length, child_question_ids, question_hint)';

                $sSQL .= 'SELECT :eventId, id, :questionLabel, form_id, question_form_type, question_form_name, question_form_option, :isManadatory, question_status, sort_order, is_subquestion, parent_question_id, is_compulsory, created_by, is_custom_form,:limitCheck, :userFieldMapping, :limitLength, child_question_ids, question_hint
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
            }

            //------------- Add new sub question ----------------

                $SubQuestionFlag = !empty($request->sub_question_flag) ? 1 : 0;
                $QuestionType = !empty($request->question_type) ? $request->question_type : 0;
                $SubQuestionTitle = !empty($request->sub_question_title) ? $request->sub_question_title : '';
                $QuestionFormOptionArray = !empty($request->sub_question_array) ? array_filter($request->sub_question_array) : [];
                $SubQuestionMandatory = !empty($request->sub_question_mandatory_status) ? $request->sub_question_mandatory_status : 0;
                $SubQuestionFormType = !empty($request->sub_question_form_type) ? $request->sub_question_form_type : 'text';
               
                $SubQuestionPriceFlag = !empty($request->sub_question_price_flag) ? 1 : 0;
                $SubQuestionCountFlag = !empty($request->sub_question_count_flag)  ? 1 : 0;
                $SubQuestionOtherAmountFlag = !empty($request->sub_question_other_amount) ? 1 : 0;
                $ParentGeneralFormId = !empty($request->parent_general_form_id) ? $request->parent_general_form_id : 0;

                if($SubQuestionFlag == 1){

                   //dd($QuestionFormOptionArray);
                    $new_array = [];
                    $i = 1;
                    if(!empty($QuestionFormOptionArray)){
                        foreach($QuestionFormOptionArray as $key=>$res){
                           //dd($res['id']);
                  
                            if(!empty($res['label']) && empty($res['price']) && empty($res['count'])){
                                $new_array[] = array("id" => $i, "label" => $res['label']);
                            }else if(!empty($res['label']) && empty($res['count']) && $SubQuestionPriceFlag == 1){
                                $new_array[] = array("id" => $i, "label" => $res['label'], "price" => !empty($res['price']) ? $res['price'] : 0 );
                            }else if(!empty($res['label']) && empty($res['price']) && $SubQuestionPriceFlag == 1){
                                $new_array[] = array("id" => $i, "label" => $res['label'], "price" => !empty($res['price']) ? $res['price'] : 0 );
                            }
                            else if(!empty($res['label']) && !empty($res['price']) && !empty($res['count']) && $SubQuestionPriceFlag == 1 && $SubQuestionCountFlag == 1){
                                $new_array[] = array("id" => $i, "label" => $res['label'], "price" => !empty($res['price']) ? $res['price'] : 0, "count" => !empty($res['count']) ? $res['count'] : '');
                            }
                            $i++;
                        }
                    }
                    
                    //dd($i);
                    if($SubQuestionOtherAmountFlag == 1 && !empty($QuestionFormOptionArray)){
                        $other_amt_array = array(array("id" => $i, "label" => "Other Amount", "price" => 0, "count" => ''));
                        $new_array = array_merge($new_array, $other_amt_array);
                    }
                    // dd($new_array);

                    $SubQuestionOptionArray = !empty($new_array) ? json_encode($new_array) : '';
                    //dd($SubQuestionOptionArray);

                    $Sql = 'SELECT id,question_form_option,child_question_ids FROM general_form_question WHERE question_status = 1 and id = '.$GeneralFormId.'  ';
                    $aResult1 = DB::select($Sql);

                    $question_form_option_array = !empty($aResult1[0]->question_form_option) ? json_decode($aResult1[0]->question_form_option) : [];

                    $question_title_name = '';
                   // if($SubQuestionFormType == 'text'){
                        if(!empty($question_form_option_array)){
                            foreach($question_form_option_array as $key=>$res2){
                                if(isset($res2->child_question_id) && $res2->id == (int)$QuestionType){
                                    $question_title_name = $res2->label;
                                }
                            }
                        }
                    //}

                    // dd($question_title_name);

                    if($SubQuestionTitle != '')
                    {
                        $sSQL = 'INSERT INTO general_form_question (question_label, form_id, question_form_type, question_form_name, question_form_option, is_manadatory, question_status, created_by, is_custom_form, parent_question_id, is_subquestion,sub_question_price_flag,sub_question_count_flag,sub_question_other_amount,question_hint) VALUES (:questionLabel,:formId,:questionFormType,:questionFormName,:questionFormOption,:isManadatory,:questionStatus,:createdBy,:isCustomForm,:parentQusId, :isSubquestion, :subQuePrice, :subQueCount, :subQueOtherAmount, :questionHint)';

                        DB::insert($sSQL,array(
                            //'questionLabel'     => !empty($question_title_name) ? $question_title_name.' '.$SubQuestionTitle : $SubQuestionTitle,
                            'questionLabel'     => $SubQuestionTitle,
                            'formId'            => 1,
                            'questionFormType'  => $SubQuestionFormType, // ex. t-shirt size
                            'questionFormName'  => 'sub_question',
                            'questionFormOption' => $SubQuestionOptionArray,
                            'isManadatory'      => $SubQuestionMandatory,
                            'questionStatus'    => 1,
                            'createdBy'         => $userId,
                            'isCustomForm'      => 1,
                            "parentQusId"       => $GeneralFormId,
                            "isSubquestion"     => $SubQuestionFlag,
                            "subQuePrice"       => $SubQuestionPriceFlag,
                            "subQueCount"       => $SubQuestionCountFlag,
                            "subQueOtherAmount" => $SubQuestionOtherAmountFlag,
                            "questionHint"      => $questionHint

                        ));

                        $last_inserted_id = DB::getPdo()->lastInsertId();
                        //dd($last_inserted_id);

                        $old_question_form_option_array = !empty($aResult1[0]->question_form_option) ? json_decode($aResult1[0]->question_form_option) : [];
                        //dd($old_question_form_option_array);
                        $subArray = [];
                        if(!empty($old_question_form_option_array)){
                            foreach($old_question_form_option_array as $res){
                                if($res->id == $QuestionType){
                                    $res->child_question_id = $last_inserted_id;
                                }
                                $subArray[] = $res;
                            }
                        }

                        $child_ques_ids = !empty($aResult1) && $aResult1[0]->child_question_ids ? $aResult1[0]->child_question_ids : $last_inserted_id;

                        //dd($subArray);  --- json updated for privous question  `child_question_ids` =:childQuestionIds
                        if(!empty($subArray)){
                            $up_sSQL = 'UPDATE general_form_question SET `question_form_option` =:questionFormOption WHERE `id`=:generalFormId ';
                            DB::update($up_sSQL,array(
                                'questionFormOption' => json_encode($subArray),
                                'generalFormId' => $GeneralFormId
                               // 'childQuestionIds' => $child_ques_ids
                            ));

                            $up_sSQL1 = 'UPDATE event_form_question SET `question_form_option` =:evtQuestionFormOption, `child_question_ids` =:childQuestionIds WHERE `event_id`=:eventId and `general_form_id`=:generalFormId ';
                            DB::update($up_sSQL1,array(
                                'evtQuestionFormOption' => json_encode($subArray),
                                'eventId' => $EventId,
                                'generalFormId' => $GeneralFormId,
                                'childQuestionIds' => $child_ques_ids
                            ));
                        }

                        //-------------
                        // $Sql1 = 'SELECT id,child_question_ids FROM general_form_question WHERE question_status = 1 and id = '.$ParentGeneralFormId.'  ';
                        // $aResult2 = DB::select($Sql1);
                        // $child_ques_ids1 = !empty($aResult2) && $aResult2[0]->child_question_ids ? $aResult2[0]->child_question_ids.','.$last_inserted_id : $last_inserted_id;

                        // $up_sSQL = 'UPDATE general_form_question SET `child_question_ids` =:childQuestionIds WHERE `id`=:generalFormId ';
                        // DB::update($up_sSQL,array(
                        //     'childQuestionIds' => $child_ques_ids1,
                        //     'generalFormId' => $ParentGeneralFormId
                        // ));

                        $Sql1 = 'SELECT id,child_question_ids FROM event_form_question WHERE question_status = 1 and general_form_id = '.$ParentGeneralFormId.' and event_id = '.$EventId.' ';
                        $aResult2 = DB::select($Sql1);
                        
                      
                        $child_ques_ids1 = !empty($aResult2) && $aResult2[0]->child_question_ids ? $aResult2[0]->child_question_ids.','.$last_inserted_id : $last_inserted_id;

                        $up_sSQL = 'UPDATE event_form_question SET `child_question_ids` =:childQuestionIds WHERE `general_form_id`=:generalFormId AND `event_id`=:eventId ';
                        DB::update($up_sSQL,array(
                            'childQuestionIds' => $child_ques_ids1,
                            'generalFormId' => $ParentGeneralFormId,
                            'eventId' => $EventId
                        )); 


                    }
            }else{
                // $Sql1 = 'SELECT id,child_question_ids FROM general_form_question WHERE question_status = 1 and id = '.$ParentGeneralFormId.'  ';
                // $aResult2 = DB::select($Sql1);

                // $up_sSQL1 = 'UPDATE event_form_question SET `child_question_ids` =:childQuestionIds WHERE `event_id`=:eventId and `general_form_id`=:generalFormId ';
                // DB::update($up_sSQL1,array(
                //     'childQuestionIds' => !empty($aResult2) && $aResult2[0]->child_question_ids ? $aResult2[0]->child_question_ids : '',
                //     'eventId' => $EventId,
                //     'generalFormId' => $ParentGeneralFormId
                // ));
            }

            //------------------ end ----------------------------

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
        // dd($aToken);
        if ($aToken['code'] == 200) {

            $EventId = !empty($request->event_id) ? $request->event_id : 0;
            $GeneralFormId = !empty($request->general_form_id) ? $request->general_form_id : 0;

            $sel_SQL = "SELECT child_question_ids FROM event_form_question WHERE `event_id`=:eventId AND `general_form_id`=:generalFormId ";
            $questionsObj = DB::select($sel_SQL,array(
                'eventId' => $EventId,
                'generalFormId' => $GeneralFormId
            ));
           //dd($questionsObj[0]->child_question_ids);

            $del_sSQL = 'DELETE FROM event_form_question WHERE `event_id`=:eventId AND `general_form_id`=:generalFormId ';

            DB::delete($del_sSQL,array(
                'eventId' => $EventId,
                'generalFormId' => $GeneralFormId
            ));

            if(!empty($questionsObj) && !empty($questionsObj[0]->child_question_ids)){

                $del_sSQL1 = 'DELETE FROM event_form_question WHERE `event_id`=:eventId AND general_form_id IN('.$questionsObj[0]->child_question_ids.') ';
                DB::delete($del_sSQL1,array(
                    'eventId' => $EventId
                ));
            }
            // dd($del_sSQL);

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
    // public function add_manual_event_form_questions(Request $request)
    // {
    //     $response['data'] = [];
    //     $response['message'] = '';
    //     $ResposneCode = 400;
    //     $empty = false;

    //     $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
    //     //dd($aToken);
    //     if ($aToken['code'] == 200) {

    //         $EventId = !empty($request->event_id) ? $request->event_id : 0;

    //         $sel_sSQL = 'SELECT id,event_id FROM event_form_question WHERE `event_id` =:eventId limit 1';
    //         $aResult =  DB::select($sel_sSQL,array('eventId' => $EventId));
    //         //dd($aResult);

    //         if(empty($aResult)){
    //             $sSQL = 'INSERT INTO event_form_question (event_id, general_form_id, question_label, form_id, question_form_type, question_form_name, question_form_option, is_manadatory, question_status, sort_order, is_compulsory)';

    //             $sSQL .= 'SELECT :eventId, id, question_label, form_id, question_form_type, question_form_name, question_form_option, is_manadatory, question_status, sort_order, is_compulsory
    //                 FROM general_form_question
    //                 WHERE question_status = 1 AND is_compulsory = 1';

    //             DB::insert($sSQL,array(
    //                 'eventId' => $EventId,
    //             ));
    //         }

    //         $response['data'] = [];
    //         $response['message'] = 'Question added successfully';
    //         $ResposneCode = 200;

    //     }else{
    //         $ResposneCode = $aToken['code'];
    //         $response['message'] = $aToken['message'];
    //     }

    //     return response()->json($response, $ResposneCode);
    // }

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
            $questionHint = !empty($request->question_hint) ? $request->question_hint : '';
            $isManadatory = !empty($request->is_manadatory) ? $request->is_manadatory : 0;
            $questionFormOption = !empty($request->question_form_option) ? array_filter($request->question_form_option) : 0;

            $question_name = strtolower($questionLabel);
            $question_name = str_replace(' ', '_', $question_name);
            //dd($question_name);
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

            $sSQL = 'INSERT INTO general_form_question (question_label, form_id, question_form_type, question_form_name, question_hint,question_form_option, is_manadatory, question_status, created_by, is_custom_form) VALUES (:questionLabel,:formId,:questionFormType,:questionFormName,:questionHint,:questionFormOption,:isManadatory,:questionStatus,:createdBy,:isCustomForm)';

            DB::insert($sSQL,array(
                'questionLabel'     => $questionLabel,
                'formId'            => 8,
                'questionFormType'  => $questionFormType,
                'questionFormName'  => $question_name,
                'questionHint'      => $questionHint,
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
            $UserId          = !empty($request->user_id) ? $request->user_id : 0;

            $sSQL = 'SELECT vm.id, vm.name, vm.start_time, vm.end_time, vm.registration_end_time, vm.banner_image, vm.display_name, vm.active, vm.event_type, (SELECT name FROM cities WHERE Id = vm.city) AS city, (SELECT name FROM states WHERE Id = vm.state) As state,(SELECT name FROM countries WHERE Id = vm.country) As country,(select CONCAT(`firstname`, " ", `lastname`) as user_name from users where id = '.$UserId.') as user_name,(select created_at from users where id = '.$UserId.') as user_created_date, (select about_you from users where id = '.$UserId.') as user_about, vm.active FROM events AS vm WHERE vm.deleted = 0 ' ;

            if(!empty($EventInfoStatus)){
                $sSQL .= ' and vm.event_info_status = '.$EventInfoStatus;
            }

            if(!empty($UserId)){
                $sSQL .= ' and vm.created_by = '.$UserId;
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
                $res->user_join_date = !empty($res->user_created_date) ? date('M d, Y',$res->user_created_date) : 0;

                $new_array[] = $res;
            }
            //dd($new_array);

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
                if($request->event_status == "true"){
                    $EventStatus = 0;
                }else if($request->event_status == "false"){
                    $EventStatus = 1;
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

    public function view_sub_question_tree(Request $request)
    {
        $response['data'] = [];
        $response['message'] = '';
        $ResposneCode = 400;
        $empty = false;

        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        //dd($aToken);
        if ($aToken['code'] == 200) {
            
            $EventId     = !empty($request->event_id) ? $request->event_id : 0;
            $questionId     = !empty($request->question_id) ? $request->question_id : 0;
            
            $SQL = "SELECT id,question_form_option,question_label,form_id,question_form_type,question_form_name FROM general_form_question WHERE id = :qus_id ";
            $questionsObj = DB::select($SQL, array('qus_id' => $questionId));
            $questionArray = array();
            $parent_question_array = array();
            
            foreach($questionsObj as $data){
                $question_form_option = isset($data->question_form_option) ? json_decode($data->question_form_option) : [];
                //dd($question_form_option);
                $data->question_form_option = !empty($question_form_option) ? $question_form_option : [];
                $parent_question_array[] = $data;

                //---------------- check event table added form question entry or not
                $Sql = 'SELECT id FROM event_form_question WHERE question_status = 1 and event_id = '.$EventId.' and general_form_id = '.$data->id.'  ';
                $aResult1 = DB::select($Sql);
                //dd($aResult1);
                if(!empty($aResult1)){
                    $data->event_questions_flag = 1;
                }else{
                    $data->event_questions_flag = 0;
                }

                //----------------------
                if(!empty($question_form_option) && isset($question_form_option)){
                    foreach($question_form_option as $res){
                        // dd($res->child_question_id);
                        $questionArray[] = isset($res->child_question_id) ? $this->get_child_questions($res->child_question_id, $questionId, $questionArray, $EventId) : []; // $res->label
                       // $data->ChildQuestionArray = !empty($questionArray) && ($questionArray[0] > 0) ? $questionArray[0] : [];
                       // dd($res->child_question_id);
                        if(isset($res->child_question_id)){
                            $data->ChildQuestionArray = array( array(
                                                            "id" => $data->id+11,
                                                            "question_form_option" => !empty($question_form_option) ? $question_form_option : [],
                                                            "question_label" => isset($res->child_question_id) ? $res->label : '',
                                                            "form_id" => $data->form_id,
                                                            "question_form_type" => $data->question_form_type,
                                                            "question_form_name" => $data->question_form_name,
                                                            "event_questions_flag" => 1,
                                                            "ChildQuestionArray" => !empty($questionArray) && ($questionArray[0] > 0) ? $questionArray[0] : []
                                                        ) );
                        }
                        // else{
                        //     $data->ChildQuestionArray = !empty($questionArray) && ($questionArray[0] > 0) ? $questionArray[0] : [];
                        // }
                        
                    }
                }
            }
           // dd($parent_question_array);

            $response['data'] = $parent_question_array;
            $response['message'] = 'Request processed successfully';
            $ResposneCode = 200;

        }else{
            $ResposneCode = $aToken['code'];
            $response['message'] = $aToken['message'];
        }

        return response()->json($response, $ResposneCode);
    }

    function get_child_questions($child_question_id, $questionId, $questionArray, $EventId) {
        //dd($questionId);

        $SQL = "SELECT * FROM general_form_question WHERE id = :qus_id AND is_subquestion = 1";
        $questionArray1 = DB::select($SQL, array('qus_id' => $child_question_id));
        //dd($questionCatArray);
        
       $questionCatArray = array();
        if (!empty($questionArray1)) {
           
            foreach($questionArray1 as $data){
                     // $i = 1;
                    $SubQuestionsArray[] = $data;
                    $questionCatArray = isset($data->question_form_option) ? json_decode($data->question_form_option) : [];
                    //dd($questionCatArray);

                    //---------------- check event table added form question entry or not
                    $Sql = 'SELECT id FROM event_form_question WHERE question_status = 1 and event_id = '.$EventId.' and general_form_id = '.$data->id.'  ';
                    $aResult1 = DB::select($Sql);
                    //dd($aResult1);
                    if(!empty($aResult1)){
                        $data->event_questions_flag = 1;
                    }else{
                        $data->event_questions_flag = 0;
                    }

                    $data->question_form_option = !empty($questionCatArray) ? $questionCatArray : [];
                    if(!empty($questionCatArray) && isset($questionCatArray)){
                        if($data->is_subquestion == 1){
                            foreach($questionCatArray as $res){
                               
                                if(isset($res->child_question_id)){
                                    $tempArr = $this->get_child_questions($res->child_question_id, $questionId, $questionArray, $EventId);
                                    $data->ChildQuestionArray[] = isset($tempArr) && !empty(count($tempArr) > 0) ? $tempArr[0] : []; //$res->label
                                }
                              
                            }
                        }
                    }
                    
            }
            
            return empty($SubQuestionsArray)? array() : $SubQuestionsArray;
        }

    }

    // Event Form Question Sort Order
    public function event_form_question_sorting(Request $request)
    {
         //dd($request);
        $response['data'] = [];
        $response['message'] = '';
        $ResposneCode = 400;
        $empty = false;

        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        //dd($aToken);
        if ($aToken['code'] == 200) {

            $UserId = 0;
            if (!empty($aToken)) {
                $UserId = $aToken['data']->ID;
            }

            $EventId = !empty($request->event_id) ? $request->event_id : 0;
            $EventFormQuestionArray = !empty($request->event_form_question_array) ? $request->event_form_question_array : [];

            //dd($aResult);
            if(!empty($EventFormQuestionArray)){
                $new_array = [];

                foreach($EventFormQuestionArray as $key=>$res){
                    //dd($res['id']);
                    $sort_order = $res['is_subquestion'] == 1 ? 0 : $key+1;
                    
                    $sSQL = 'UPDATE event_form_question SET `sort_order` =:sort_order WHERE `event_id`=:eventId AND `general_form_id`=:generalFormId AND `id`= :Id ';
                    DB::update($sSQL,array(
                        'sort_order' => $sort_order,
                        'eventId' => $EventId,
                        'generalFormId' => $res['general_form_id'],
                        'Id' => $res['id']
                    ));

                    if($res['child_question_ids'] != ''){
                        $sSQL = 'UPDATE event_form_question SET `sort_order` =:sort_order WHERE `event_id`=:eventId AND `general_form_id` IN('.$res['child_question_ids'].')';
                        DB::update($sSQL,array(
                            'sort_order' => $sort_order,
                            'eventId' => $EventId
                        ));
                    }

                }

                $response['data'] = $new_array;
                $response['message'] = 'Sort order updated successfully';
                $ResposneCode = 200;
            }else{
                $response['message'] = 'No Data Found';
                $ResposneCode = 200;
            }

            //dd('sss');

        }else{
            $ResposneCode = $aToken['code'];
            $response['message'] = $aToken['message'];
        }

        return response()->json($response, $ResposneCode);
    }


}
