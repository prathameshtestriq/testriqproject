<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Libraries\Authenticate;
use Illuminate\Support\Facades\DB;

class TestCronController extends Controller
{
    public function sheduleEmailCron(Request $request)
    {
         // dd('sfsdf');
        $current_datetime = time();
        $start_time = strtotime('-1 hour', $current_datetime);
         // dd($start_time); 

        $SQL1 = "SELECT id,event_id,email_type,email,subject,message,recipient_type,send_date_type,sent_date_time,email_sent FROM send_email_log WHERE email_sent = 0 AND send_date_type = 2 AND status = 1";

        if(!empty($current_datetime) && !empty($start_time)){
           $SQL1 .= " AND sent_date_time BETWEEN ".$start_time." AND ".$current_datetime." ";
        }

        $aEmailResult = DB::select($SQL1, array());
        // dd($aEmailResult);

        if(!empty($aEmailResult)){
            foreach($aEmailResult as $res){

                $subject = !empty($res->subject) ? $res->subject : '';
                $message = !empty($res->message) ? $res->message : '';
                $receiver = !empty($res->recipient_type) ? $res->recipient_type : '';
                $event_ids = !empty($res->event_id) ? $res->event_id : '';
                $manual_email_address = !empty($res->email) ? $res->email : [];

                //------------------- send email for - (Manual Emails)
                if($res->email_type == 2){

                    $manual_email_array = explode(",",$manual_email_address);
                    if(!empty($manual_email_array)){
                        foreach($manual_email_array as $res1){
                            //---------- log entry
                            $Binding1 = array(
                                "type" => 'Manual Email',
                                "send_mail_to" => $res1,
                                "subject"  => $subject,
                                "message"  => $message,
                                "datetime" => strtotime("now"),
                            );
                            $Sql1 = "INSERT INTO admin_send_email_log (type,send_mail_to,subject,message,datetime) VALUES (:type,:send_mail_to,:subject,:message,:datetime)";
                            DB::insert($Sql1, $Binding1);
                         
                            $Email = new Emails();
                            $Email->send_admin_side_mail($res, $message, $subject, 'Manual Email'); 
                        }
                    }
                }


                //---------------- send email for All Participant
                if($res->email_type == 1 && $receiver == 'All Participant'){

                    $sSQL = "SELECT distinct(ad.email) as email_ids FROM event_booking as eb left join booking_details as bd on bd.booking_id = eb.id left join attendee_booking_details as ad on ad.booking_details_id = bd.id  WHERE eb.event_id IN (".$event_ids.")  AND eb.transaction_status IN(1,3)";
                    $aParticipantEmailResult = DB::select($sSQL, array());
                    // dd($aParticipantEmailResult);

                    $EmailIdsArray = [];
               
                    if(!empty($aParticipantEmailResult)){
                        $emailArray     = array_column($aParticipantEmailResult,'email_ids');
                        $FilteredEmails = array_filter($emailArray);
                        $EmailIdsArray  = array_values($FilteredEmails);
                    }
                   
                    if(!empty($EmailIdsArray)){
                        foreach($EmailIdsArray as $res2){
                            $particepant_email_ids = $res2;
                            
                            //---------- log entry
                            $Binding1 = array(
                                "type" => $receiver,
                                "send_mail_to" => $particepant_email_ids,
                                "subject"  => $subject,
                                "message"  => $message,
                                "datetime" => strtotime("now"),
                            );
                            $Sql1 = "INSERT INTO admin_send_email_log(type,send_mail_to,subject,message,datetime) VALUES (:type,:send_mail_to,:subject,:message,:datetime)";
                            DB::insert($Sql1, $Binding1);
                         
                            $Email = new Emails();
                            $Email->send_admin_side_mail($particepant_email_ids, $message, $subject, $receiver); 
                        }
                    }
                }

                //---------------- send email for All Organizer
                if($res->email_type == 1 && $receiver == 'All Organizer'){

                    $SQL1 = "SELECT email FROM organizer WHERE 1=1 ";
                    $aEventResult = DB::select($SQL1, array());
                    // dd($aEventResult);
                    if(!empty($aEventResult)){
                        $email_array = array_column($aEventResult,'email');
                        $email_ids = array_unique($email_array);
                    }

                    if(!empty($email_ids)){
                        foreach($email_ids as $res3){
                            $organizer_email_ids = $res3;

                            //---------- log entry
                            $Binding1 = array(
                                "type" => $receiver,
                                "send_mail_to" => $organizer_email_ids,
                                "subject"  => $subject,
                                "message"  => $message,
                                "datetime" => strtotime("now"),
                            );
                            $Sql1 = "INSERT INTO admin_send_email_log (type,send_mail_to,subject,message,datetime) VALUES (:type,:send_mail_to,:subject,:message,:datetime)";
                            DB::insert($Sql1, $Binding1);

                            $Email = new Emails();
                            $Email->send_admin_side_mail($organizer_email_ids, $message, $subject, $receiver); 
                        }
                    }
                }

                //---------------- send email for All Registration
                if($res->email_type == 1 && $receiver == 'All Registration'){
                    $SQL1 = "SELECT email FROM booking_payment_details WHERE event_id IN (".$event_ids.") ";
                    $aEventResult = DB::select($SQL1, array());
                    
                    if(!empty($aEventResult)){
                        $email_array = array_column($aEventResult,'email');
                        $email_ids = array_unique($email_array);
                    }
                    
                    if(!empty($email_ids)){
                        foreach($email_ids as $res4){
                            $registration_email_ids = $res4;

                            //---------- log entry
                            $Binding1 = array(
                                "type" => $receiver,
                                "send_mail_to" => $registration_email_ids,
                                "subject"  => $subject,
                                "message"  => $message,
                                "datetime" => strtotime("now"),
                            );
                            $Sql1 = "INSERT INTO admin_send_email_log (type,send_mail_to,subject,message,datetime) VALUES (:type,:send_mail_to,:subject,:message,:datetime)";
                            DB::insert($Sql1, $Binding1);

                            $Email = new Emails();
                            $Email->send_admin_side_mail($registration_email_ids, $message, $subject, $receiver); 
                        }
                    }
                }

                //-------------- update email_sent flag
                $up_sSQL = 'UPDATE send_email_log SET email_sent =:email_sent WHERE id =:SendId';
                DB::update($up_sSQL, array(
                        'email_sent' => 1,
                        'SendId' => (int)$res->id
                    )
                );

            }
        }
    }


}
