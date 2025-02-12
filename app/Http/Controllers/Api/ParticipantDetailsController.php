<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Libraries\Authenticate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use \stdClass;
use DateTime;

class ParticipantDetailsController extends Controller
{
    public function participantdetails(Request $request){
        $empty = false;
        $ResponseData = [];
        $message = "";
        $ResposneCode = 200;
        $aPost = $request->all();
        if (empty($aPost['event_id'])) {
            $empty = true;
            $field = 'Event Id';
        }
        if (!$empty) {
            $sSQL = 'SELECT a.id,e.event_id,a.registration_id,a.id,a.firstname,a.lastname,a.mobile,a.email,(SELECT ticket_name FROM event_tickets WHERE id=a.ticket_id) AS TicketName,a.attendee_details FROM event_booking e LEFT JOIN booking_details As bd ON e.id = bd.booking_id LEFT JOIN attendee_booking_details As a ON bd.id = a.booking_details_id WHERE e.event_id=:event_id AND e.transaction_status IN (1,3) AND a.id IS NOT NULL ';  // LIMIT 5

            $participantData = DB::select($sSQL, array('event_id' => $aPost['event_id']));
           
            $participant_details = [];
            if(!empty($participantData)){
                foreach($participantData as $res){
                
                    $aTemp = new stdClass;
                    $loc_gender = $loc_tshirt_size = $loc_emergency_contact_name = $loc_emergency_contact_number = $loc_blood_group = $loc_date_of_birth = $loc_age = $loc_photo_id = '';

                    $question_answer_result = json_decode(json_decode($res->attendee_details));
                    // dd($question_answer_result);
                    foreach ($question_answer_result as $detail) {
                        
                        $question_form_option = json_decode($detail->question_form_option, true);

                        if($detail->question_form_type == 'radio' && $detail->question_form_name == 'gender' && !empty($detail->ActualValue)){
                            // dd($question_form_option);
                            $label = '';
                            foreach ($question_form_option as $option) {
                                if ($option['id'] === (int)$detail->ActualValue) {
                                    $label = $option['label'];
                                    break;
                                }
                            }
                            $loc_gender = $label;
                        }

                        if($detail->question_form_type == 'select' && ($detail->question_form_name == 'select_t-shirt_size' || $detail->question_form_name == 'tshirt_size' || $detail->question_form_name == 't-shirt_size' || $detail->question_form_name == 'select_tshirt_sizes' || ($detail->question_form_name == 'sub_question' && $detail->question_label == 'Select T-shirt size')) && !empty($detail->ActualValue)){
                            $label1 = '';
                            foreach ($question_form_option as $option) {
                                if ($option['id'] === (int)$detail->ActualValue) {
                                    $label1 = $option['label'];
                                    break;
                                }
                            }
                            $loc_tshirt_size = $label1;
                        }

                        if($detail->question_form_type == 'text' && $detail->question_form_name == 'emergency_contact_name' && !empty($detail->ActualValue)){
                            $loc_emergency_contact_name = $detail->ActualValue;
                        }

                        if($detail->question_form_type == 'mobile' && $detail->question_form_name == 'emergency_contact_number' && !empty($detail->ActualValue)){
                            $loc_emergency_contact_number = $detail->ActualValue;
                        }

                        if($detail->question_form_type == 'select' && $detail->question_form_name == 'blood_group' && !empty($detail->ActualValue)){
                            // dd($question_form_option);
                            $label2 = '';
                            foreach ($question_form_option as $option) {
                                if ($option['id'] === (int)$detail->ActualValue) {
                                    $label2 = $option['label'];
                                    break;
                                }
                            }
                            $loc_blood_group = $label2;
                        }

                        if($detail->question_form_type == 'date' && $detail->question_form_name == 'date_of_birth' && !empty($detail->ActualValue)){
                            $loc_date_of_birth = date('d-m-Y',(strtotime($detail->ActualValue)));

                            //------- To calculate Age
                            $loc_age = ParticipantDetailsController::calculateAge($loc_date_of_birth);
                            // dd($loc_age);
                        }

                        if($detail->question_form_type == 'file' && $detail->question_form_name == 'upload_id_proof' && !empty($detail->ActualValue)){
                            $loc_photo_id = $detail->ActualValue;
                        }

                    }

                    $aTemp->event_id         = $res->event_id;
                    $aTemp->registration_id  = $res->registration_id;
                    $aTemp->TicketName       = $res->TicketName;
                    $aTemp->firstname        = $res->firstname;
                    $aTemp->lastname         = $res->lastname;
                    $aTemp->email            = $res->email;
                    $aTemp->mobile           = $res->mobile;
                    $aTemp->gender           = $loc_gender;
                    $aTemp->tshirt_size      = $loc_tshirt_size;
                    $aTemp->emergency_contact_name   = $loc_emergency_contact_name;
                    $aTemp->emergency_contact_number = $loc_emergency_contact_number;
                    $aTemp->blood_group      = $loc_blood_group;
                    $aTemp->date_of_birth    = $loc_date_of_birth;
                    $aTemp->age              = $loc_age;
                    $aTemp->photo_id         = $loc_photo_id;

                    $participant_details[] = $aTemp;

                }
            }

            // dd($participant_details);
            $ResponseData['participantData'] = $participant_details;
            $message = "Request process successfully";

        } else {
            $ResposneCode = 400;
            $message = $field . ' is empty';
        }

        $response = [
            'data' => $ResponseData,
            'message' => $message
        ];

        return response()->json($response, $ResposneCode);
    }

    function calculateAge($date)
    {
        // dd($date);
        // Set timezone
        date_default_timezone_set('Asia/Kolkata');

        $startDate = DateTime::createFromFormat('d-m-Y', $date);
        $endDate = new DateTime();

        if (!$startDate) {
            return "Invalid date format. Please use dd-mm-yyyy.";
        }

        $interval = $startDate->diff($endDate);
        $years  = $interval->y;
        $months = $interval->m;
        $days   = $interval->d;
        
        $final_date = "{$years} Years, {$months} Months, {$days} Days";
        return $final_date;
    }

}

