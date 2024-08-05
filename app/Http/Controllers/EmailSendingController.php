<?php

namespace App\Http\Controllers;

use App\Models\EmailSendingModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EmailSendingController extends Controller
{
    public function index(Request $request){
        $s_sql = 'SELECT el.*, GROUP_CONCAT(e.name) AS event_names
          FROM sent_email_log el
          LEFT JOIN events e ON FIND_IN_SET(e.id, el.event_id)
          GROUP BY el.id';

        $a_return['Email_details'] = DB::select($s_sql,array());
 
     return view('email.list', $a_return);
    }
    public function add_edit(Request $request,$iId=0){
        // dd("here");
        $a_return['event'] = '';
        $a_return['event_ids']=[];
        $a_return['receiver'] = '';
        $a_return['date'] = '';
        $a_return['message'] = '';
        $a_return['shedulingdate'] = '';

        
       
        if (isset($request->form_type) && $request->form_type == 'add_edit_email_sending') {
            
            $rules = [
                'event' => 'required',
                'receiver' => 'required',
                'subject' => 'required',
                'message' => 'required',
                'date' => 'required'
               
            ];
            if($request->date  == 'shedule_date'){
                $rules = [
                    'shedulingdate' => 'required',
                ];
            }
           
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            //    dd($request->all());
            EmailSendingModel::add_email($request);
            $successMessage = 'Email Sending Successfully';
        
            return redirect('/email_sending')->with('success', $successMessage);

        }

        $SQL = "SELECT id,name FROM events WHERE active=1 AND deleted = 0";
        $a_return['EventsData'] = DB::select($SQL, array());

        return view('email.create',$a_return);

    }
    public function change_active_status(Request $request)
    {
       
        $aReturn = EmailSendingModel::change_status_email($request);
        return $aReturn;
    }
    
}
