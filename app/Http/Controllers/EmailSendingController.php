<?php

namespace App\Http\Controllers;


use App\Models\EmailSendingModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use League\Csv\Reader;

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

            // dd($email);
           
            EmailSendingModel::add_email($request, $email);
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

    

}
