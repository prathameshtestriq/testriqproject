<?php

namespace App\Http\Controllers;

use App\Models\EmailSendingModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use App\Libraries\Emails;

class EmailSendingController extends Controller
{
    public function index(Request $request)
    {
        $s_sql = 'SELECT el.*, GROUP_CONCAT(e.name) AS event_names
          FROM send_email_log el
          LEFT JOIN events e ON FIND_IN_SET(e.id, el.event_id)
          GROUP BY el.id';

        $a_return['Email_details'] = DB::select($s_sql, array());

        return view('email.list', $a_return);
    }

    public function add_edit(Request $request,$iId=0){
        
        // $a_return['event'] = '';
        // $a_return['event_ids']=[];
        // $a_return['receiver'] = '';
        // $a_return['date'] = '';
        // $a_return['message'] = '';
        // $a_return['shedulingdate'] = '';
        // $a_return['email_type'] = '';

        $a_return['event'] = '';
        $a_return['event_ids'] = [];
        $a_return['receiver'] = '';
        $a_return['date'] = '';
        $a_return['message'] = '';
        $a_return['shedulingdate'] = '';
        
        if (isset($request->form_type) && $request->form_type == 'add_edit_email_sending') {


            $rules = [
                'subject' => 'required',
                'message' => 'required',
                'email_type' => 'required',
            ];

            // Add rule for scheduling date if applicable
            if ($request->date == 'shedule_date') {
                $rules['shedulingdate'] = 'required';
            }

           
            // install(composer require league/csv)
            // Add rules based on email type
            if ($request->email_type == 1) {
                // rules for email_type 1
                $rules['receiver'] = 'required';
                // Conditional rules based on receiver value
                if ($request->receiver == 'All Registration' || $request->receiver == 'All Participant') {
                    $rules['event'] = 'required';
                }
            } elseif ($request->email_type == 2) {
                $rules['email'] = 'required|email:rfc,dns';
            } elseif ($request->email_type == 3) {
                //$rules['email_file'] = 'required|file|mimes:csv,txt';
            }
            //dd($request->all());

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            
            $email = '';
            if ($request->email_type == 3 && $request->hasFile('email_file')) {
                $file = $request->file('email_file');
                // Open and read the CSV file
                if (($handle = fopen($file->getRealPath(), 'r')) !== FALSE) {
                    // Get the header row (assuming the first row contains column headers)
                    $header = fgetcsv($handle, 1000, ',');
                    // Find the index of the 'email' column
                    $emailIndex = array_search('Email', $header);

                    if ($emailIndex === false) {
                        dd("file not get");
                    }

                    $emails = [];

                    while (($row = fgetcsv($handle, 1000, ',')) !== FALSE) {
                        $emails[] = $row[$emailIndex];
                    }

                    // Process valid emails
                    $email = implode(',', $emails);
                  
                    fclose($handle); 
                   
                }

            }

            EmailSendingModel::add_email($request, $email);

            //------------- send email
            EmailSendingController::send_email_for_all($request);

            $successMessage = 'Email Sending Successfully';

            return redirect('/email_sending')->with('success', $successMessage);

        }

        $SQL = "SELECT id,name FROM events WHERE active=1 AND deleted = 0";
        $a_return['EventsData'] = DB::select($SQL, array());

        return view('email.create', $a_return);

    }

    public function change_active_status(Request $request)
    {
       
        $aReturn = EmailSendingModel::change_status_email($request);
        return $aReturn;
    }

    function send_email_for_all($request)
    {
        // dd($request);
        
          $email_type = !empty($request->email_type) ? $request->email_type : ""; // => All
          $event_ids = !empty($request->event) ? implode(',', $request->event) : [];  
          $receiver = !empty($request->receiver) ? $request->receiver : ""; // All Registration
          $subject = !empty($request->subject) ? $request->subject : "";
          $date = !empty($request->date) ? $request->date : "";
          $message = !empty($request->message) ? $request->message : "";
          
            $email_ids = [];
            if(!empty($email_type) && $email_type == 1){
                
                //---------------- send email for All Registration
                if($receiver == 'All Registration'){
                    $SQL1 = "SELECT email FROM booking_payment_details WHERE event_id IN (".$event_ids.") ";
                    $aEventResult = DB::select($SQL1, array());
                    
                    if(!empty($aEventResult)){
                        $email_array = array_column($aEventResult,'email');
                        $email_ids = array_unique($email_array);
                    }
                    
                    if(!empty($email_ids)){
                        foreach($email_ids as $res){
                            $registration_email_ids = $res;

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
                
                //---------------- send email for All Participant
                if($receiver == 'All Participant'){
        
                    $sSQL = "SELECT distinct(ad.email) as email_ids FROM event_booking as eb left join booking_details as bd on bd.booking_id = eb.id left join attendee_booking_details as ad on ad.booking_details_id = bd.id  WHERE eb.event_id IN (".$event_ids.")  AND eb.transaction_status IN(1,3)";
                    $aParticipantEmailResult = DB::select($sSQL, array());

                    $EmailIdsArray = [];
               
                    if(!empty($aParticipantEmailResult)){
                        $emailArray     = array_column($aParticipantEmailResult,'email_ids');
                        $FilteredEmails = array_filter($emailArray);
                        $EmailIdsArray  = array_values($FilteredEmails);
                    }

                    if(!empty($EmailIdsArray)){
                        foreach($EmailIdsArray as $res){
                            $particepant_email_ids = $res;
                            
                            //---------- log entry
                            $Binding1 = array(
                                "type" => $receiver,
                                "send_mail_to" => $particepant_email_ids,
                                "subject"  => $subject,
                                "message"  => $message,
                                "datetime" => strtotime("now"),
                            );
                            $Sql1 = "INSERT INTO admin_send_email_log (type,send_mail_to,subject,message,datetime) VALUES (:type,:send_mail_to,:subject,:message,:datetime)";
                            DB::insert($Sql1, $Binding1);
                         
                            $Email = new Emails();
                            $Email->send_admin_side_mail($particepant_email_ids, $message, $subject, $receiver); 
                        }
                    }
                }

                //---------------- send email for All Organizer
                if($receiver == 'All Organizer'){

                    $SQL1 = "SELECT email FROM organizer WHERE 1=1 ";
                    $aEventResult = DB::select($SQL1, array());
                    // dd($aEventResult);
                    if(!empty($aEventResult)){
                        $email_array = array_column($aEventResult,'email');
                        $email_ids = array_unique($email_array);
                    }

                    if(!empty($email_ids)){
                        foreach($email_ids as $res){
                            $organizer_email_ids = $res;

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
              
             
            }
        return true;
    }

    
}
