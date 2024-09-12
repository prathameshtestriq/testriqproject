<?php

namespace App\Http\Controllers;


use App\Models\EmailSendingModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use League\Csv\Reader;
use Storage;
use App\Libraries\Emails;
use Illuminate\Pagination\LengthAwarePaginator;

class EmailSendingController extends Controller
{
    public function clear_search()
    {
        session::forget('search_email_type');
        session::forget('search_receiver');
        session::forget('search_event');
        session::forget('send_email_start_date');
        session::forget('send_email_end_date');
      
        return redirect('/email_sending');
    }
    public function index(Request $request)
    {
        $a_return = array();
        $a_return['search_email_type'] = '';
        $a_return['search_receiver'] = '';
        $a_return['search_event'] = '';
        $a_return['search_send_email_start_date'] = '';
        $a_return['search_send_email_end_date'] = '';
        $a_return['search_send_email_end_date'] = '';

        if (isset($request->form_type) && $request->form_type == 'search_email_send') {
           
            session(['search_email_type' => $request->search_email_type]);
            session(['search_receiver' => $request->search_receiver]);
            session(['search_event' => $request->search_event]);
            session(['send_email_start_date' => $request->send_email_start_date]);
            session(['send_email_end_date' => $request->send_email_end_date]);
        
            return redirect('/email_sending');
        }
       
        $a_return['search_email_type'] = (!empty(session('search_email_type'))) ? session('search_email_type') : '';
        $a_return['search_receiver'] = (!empty(session('search_receiver'))) ? session('search_receiver') : '';
        $search_event = session('search_event');
        $a_return['search_event'] =  (isset($search_event) && $search_event != "") ? $search_event : '';
        $a_return['search_send_email_start_date'] = (!empty(session('send_email_start_date'))) ?  session('send_email_start_date') : '';
        $a_return['search_send_email_end_date'] = (!empty(session('send_email_end_date'))) ? session('send_email_end_date'): '';
        $search_email_type = session('search_email_type');
        $a_return['search_email_type'] = (isset($search_email_type) && $search_email_type != '') ? $search_email_type : '';
   


        $CountRows = EmailSendingModel::get_count($a_return);
        // dd($CountRows);
        $PageNo = request()->input('page', 1);
        $Limit = config('custom.per_page');
        // $Limit = 3;
        $a_return['Offset'] = ($PageNo - 1) * $Limit;
        
       
        $a_return["Email_details"] = EmailSendingModel::get_all($Limit,$a_return);

        $SQL = "SELECT id,name FROM events WHERE active=1 AND deleted = 0";
        $a_return['EventsData'] = DB::select($SQL, array());

        //  dd($a_return["Remittance"][0]);
        $a_return['Paginator'] = new LengthAwarePaginator( $a_return["Email_details"], $CountRows, $Limit, $PageNo);
        $a_return['Paginator']->setPath(request()->url());

        return view('email.list', $a_return);
    }
    public function add_edit(Request $request, $iId = 0)
    {
        // dd("here");
        // echo '<pre>';print_r($request->all());die;

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
                // $rules['email'] = 'required|email:rfc,dns';
                $emails = explode(',', $request->email);
                // Custom validation rule for each email
                $rules['email'] = ['required', function($attribute, $value, $fail) use ($emails) {
                    foreach($emails as $email) {
                        $email = trim($email); // remove any whitespace around the email
                        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $fail($email . ' is not a valid email address.');
                        }
                    }
                }];
            } elseif ($request->email_type == 3) {
                $rules['email_file'] = 'required|file|mimes:csv,txt';
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
                            
                    $email = implode(',', $emails);
                    fclose($handle); 
                }

                $rules = [
                    'email_file' => function($attribute, $value, $fail) use ($emails) {
                        foreach($emails as $index => $email) {
                            $email = trim($email); // remove any whitespace around the email
                            if (empty($email)) {
                                $fail("Email is blank.");
                            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                $fail("$email is not a valid email address.");
                            }
                        }
                    }
                ];
            
                $validator = Validator::make(['email_file' => $emails], $rules);
            
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }

            }

            EmailSendingModel::add_email($request, $email);

            //------------- send email
            EmailSendingController::send_email_for_all($request);
            $successMessage = 'Email sending successfully';

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

    public function upload(Request $request)
    {
        if ($request->hasFile('upload')) {
            $originName = $request->file('upload')->getClientOriginalName();
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $file = $request->file('upload');
            $extension = $file->getClientOriginalExtension();
            $fileName = $fileName . '_' . time() . '.' . $extension;
    
            $request->file('upload')->move(public_path('uploads/ckeditimage/'), $fileName);     
            $url = asset('uploads/ckeditimage/' . $fileName);
            $filePath = 'uploads/ckeditimage/' ;
     
            return response()->json([
                // 'uploaded' => 1,
                // 'fileName' => $filename,
                // 'url' => $url
                'fileName' => $fileName, 
                'uploaded' => 1, 
                'url' => $url, 
                'filePath' => $filePath
            ]);
        }
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
          $manual_email_address = !empty($request->email) ? $request->email : [];
          
            // dd($email_type);
            $email_ids = [];
            //------------------- send email - (Manual Emails)
            if(!empty($email_type) && $email_type == 2){

                $manual_email_array = explode(",",$manual_email_address);
                if(!empty($manual_email_array)){
                    foreach($manual_email_array as $res){
                        //---------- log entry
                        $Binding1 = array(
                            "type" => 'Manual Email',
                            "send_mail_to" => $res,
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

            //------------ send email (Upload CSV)
             $emails = [];
             if (!empty($email_type) && $request->email_type == 3 && $request->hasFile('email_file')) {
                $file = $request->file('email_file');
              
                if (($handle = fopen($file->getRealPath(), 'r')) !== FALSE) {
                    $header = fgetcsv($handle, 1000, ',');
                    $emailIndex = array_search('Email', $header);

                    while (($row = fgetcsv($handle, 1000, ',')) !== FALSE) {
                        $emails[] = $row[$emailIndex];
                    }
                            
                    fclose($handle); 
                   
                    if(!empty($emails)){
                        foreach($emails as $res){
                            //---------- log entry
                            $Binding1 = array(
                                "type" => 'Upload CSV',
                                "send_mail_to" => $res,
                                "subject"  => $subject,
                                "message"  => $message,
                                "datetime" => strtotime("now"),
                            );
                            $Sql1 = "INSERT INTO admin_send_email_log (type,send_mail_to,subject,message,datetime) VALUES (:type,:send_mail_to,:subject,:message,:datetime)";
                            DB::insert($Sql1, $Binding1);
                         
                            $Email = new Emails();
                            $Email->send_admin_side_mail($res, $message, $subject, 'Upload CSV'); 
                        }
                    }

                }
            }

            //------------------------------------------------------
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
