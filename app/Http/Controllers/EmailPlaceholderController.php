<?php

namespace App\Http\Controllers;


use App\Models\EmailPlaceholderManagement;
use App\Models\RemittanceManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Pagination\LengthAwarePaginator;



class EmailPlaceholderController extends Controller
{
    public function clear_search()
    {
        session::forget('search_Name');
        session::forget('event_placeholder');
        session::forget('placeholder_status');
        return redirect('/email_placeholder_management');
    }

    public function index(Request $request)
    {
        $a_return = array();
        $a_return['search_placeholder_name'] = '';
       
        

        if (isset($request->form_type) && $request->form_type == 'search_email_placeholder') {
            session(['search_Name' => $request->search_Name]);
            session(['event_placeholder' => $request->event]);
            session(['placeholder_status' => $request->placeholder_status]);
            return redirect('/email_placeholder_management');
        }
        $a_return['search_placeholder_name'] = (!empty(session('search_Name'))) ? session('search_Name') : '';
        $placeholder_status = session('placeholder_status');
        $a_return['search_placeholder_status'] = (isset($placeholder_status) && $placeholder_status != '') ? $placeholder_status : '';
        $a_return['search_event_id'] = (!empty(session('event_placeholder'))) ? session('event_placeholder'): '';

        $CountRows = EmailPlaceholderManagement::get_count($a_return);
        // dd($CountRows);
        $PageNo = request()->input('page', 1);
        $Limit = config('custom.per_page');
        // $Limit = 3;
        $a_return['Offset'] = ($PageNo - 1) * $Limit;
        
        
        $a_return["Email_placeholder"] = EmailPlaceholderManagement::get_all_email_placeholders($Limit, $a_return);
        //  dd($a_return["Email_placeholder"]);
        $a_return['Paginator'] = new LengthAwarePaginator($a_return['Email_placeholder'], $CountRows, $Limit, $PageNo);
        $a_return['Paginator']->setPath(request()->url());

        $SQL = "SELECT id,name FROM events WHERE active=1 AND deleted = 0";
        $a_return['EventsData'] = DB::select($SQL, array());

        return view('email_placeholder.list',$a_return);
    }
 
    public function add_edit(Request $request, $iId = 0){
        $a_return['event_id'] = '';
        $a_return['question_id'] = '';
        $a_return['placeholder_name'] = '';
        $a_return['question_form_name'] = '';
       
     
        //  dd($request->all());
        if (isset($request->form_type) && $request->form_type == 'add_edit_email_placeholder') {
            $question_id = isset($request->question) ? $request->question : 0;
            $s_sql = 'SELECT question_form_name FROM event_form_question as eq WHERE eq.id = '.$question_id;
            $event_form_question = DB::select($s_sql);
            $event_form_question_name = !empty($event_form_question[0]->question_form_name) ? $event_form_question[0]->question_form_name : '';

            // Query existing placeholders to check uniqueness manually if needed
            $s_sql = 'SELECT *,(SELECT question_form_name FROM event_form_question as eq WHERE eq.id = ep.question_id) AS question_form_name FROM email_placeholders ep WHERE 1=1';
            $email_placeholders = DB::select($s_sql);
            $is_name_unique = true;
            $is_unique = true;
           

            foreach ($email_placeholders as $val) {   
                if ($event_form_question_name === $val->question_form_name && $val->id != $iId) {
                    if ($val->placeholder_name == $request->placeholder_name ) {
                        $is_name_unique = false;
                        break;
                    }
                }
               
            }

            // Validation rules and messages
            $rules = [
                'event' => 'required',
                'question' => 'required',
                'placeholder_name' => 'required|unique:email_placeholders,placeholder_name,' . $iId . ',id',
                // 'question_form_name' => 'unique:email_placeholders,question_form_name,' . $iId . ',id'
            ];
            if(!empty($request->question)){
                $rules = [
                    'question_form_name' => 'unique:email_placeholders,question_form_name,' . $iId . ',id'
                ];
            }
            $messages = [
                'placeholder_name.unique' => 'The placeholder name must be unique for the selected question.',
                // 'question_form_name' => 'The question name must be unique for the selected event .',
            ];

            // dd($messages );
            // Validate the request with custom validation logic
            $vari = $request->validate($rules,$messages);
           

            // Perform update or add based on ID
            if ($iId > 0) {
                EmailPlaceholderManagement::update_email_placeholder($iId, $request);
                $successMessage = 'Email Placeholders Updated Successfully';
            } else {
                EmailPlaceholderManagement::add_email_placeholder($request);
                $successMessage = 'Email Placeholders  Details Added Successfully';
            }

            // Redirect with success message
            return redirect('/email_placeholder_management')->with('success', $successMessage);

        }else{
            if($iId > 0){
        //     //   #SHOW EXISTING DETAILS ON EDIT
              $sSQL = 'SELECT id,event_id,question_id,question_form_name,placeholder_name FROM email_placeholders WHERE id=:id';
              $email_placeholder_details = DB::select($sSQL, array( 'id' => $iId));
              $a_return = (array)$email_placeholder_details[0];
            //   dd(  $remittance_management_details);
            }
        }      
        
        $SQL = "SELECT id,name FROM events WHERE active=1 AND deleted = 0";
        $a_return['EventsData'] = DB::select($SQL, array());
       
        return view('email_placeholder.create',$a_return);
    }
    public function get_questionsby_event(Request $request){

        $eventId = $request->get('EventId');     
        $SQL = "SELECT id,question_form_name,question_label FROM event_form_question Where event_id = ".$eventId;
        $EventFormQuestion = DB::select($SQL, array());
        foreach ($EventFormQuestion as $question) {
            if ($question->question_form_name == 'sub_question') {
                // Modify the question_label, for example, replacing underscores with spaces
                $question->question_form_name = strtolower(str_replace(' ', '_', $question->question_label));
                // dd($question->question_label);
            }
        }
        // dd($EventFormQuestion);
        return response()->json($EventFormQuestion);
        
    }

    public function delete_email_placeholder_management($iId){
        EmailPlaceholderManagement::delete_email_placeholder($iId);
        return redirect(url('/email_placeholder_management'))->with('success', 'Email Placeholders  Deleted Successfully');
    }

    public function change_active_status(Request $request)
    {
        $aReturn = EmailPlaceholderManagement::change_status_email_placeholder($request);
    
        $successMessage  = 'Status changed successfully';
        $sucess = 'true';
        $aReturn = [];
        $aReturn['message'] =  $successMessage ;
        $aReturn['sucess'] = $sucess;
        return $aReturn;
    }
    
   
    
}
