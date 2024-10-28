<?php

namespace App\Http\Controllers;

use App\Exports\participantworkExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;
use App\Imports\ParticipantBulkDetailsImport;

class ParticipantBulkController extends Controller
{
    
    public function clear_search(){
        session::forget('search_event');
        return redirect('/participan_work_upload');
    }

    public function index(){
        $a_return = [];
        $SQL = "SELECT id,name FROM events WHERE active=1 AND deleted = 0";
        $a_return['EventsData'] = DB::select($SQL, array());
        return view('particpant_bulk_upload.list',$a_return);
    }

    public function export_event_participants_work(Request $request)
    {         
        // dd($request->all());
        if (isset($request->form_type) && $request->form_type == 'Participant_work_upload') {
            session(['search_event' => $request->search_event]);
        }
        $a_return['search_event'] = (!empty(session('search_event'))) ? session('search_event') : '';
        if(!empty($a_return['search_event']) ){
            $filename = "participant_Excel_" .$a_return['search_event'].".xlsx";
            return Excel::download(new participantworkExport(), $filename);
        }
        
        $a_return = [];
        $SQL = "SELECT id,name FROM events WHERE active=1 AND deleted = 0";
        $a_return['EventsData'] = DB::select($SQL, array());

        return view('particpant_bulk_upload.list',$a_return);
    }

    public function event_participan_bulk_upload(Request $request)
    { 
       //dd($request); 
        $userId = 3;
        $participant_file = !empty($request->participant_file) ? $request->file('participant_file') : '';
        // dsd($participant_file);
        $event_id = (!empty(session('search_event'))) ? session('search_event') : '';
        dd($event_id);

        if(!empty($participant_file)){
            $data['userId'] = $userId;
            $import = new ParticipantBulkDetailsImport($data);
            Excel::import($import, request()->file('participant_file'));

        }


    }


}
