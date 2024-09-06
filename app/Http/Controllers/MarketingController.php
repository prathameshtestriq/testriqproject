<?php

namespace App\Http\Controllers;

use App\Models\MarketingModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Pagination\LengthAwarePaginator;

class MarketingController extends Controller
{
    public function clear_search()
    {
        session::forget('campaign_name');
        session::forget('start_marketing_date');
        session::forget('end_marketing_date');
        session::forget('marketing_status');
        session::forget('search_event');
        session::forget('search_campaign_type');
        return redirect('/marketing');
    }

    public function index(Request $request)
    {
        $a_return = array();
        $a_return['search_campaign_name'] = '';
        $a_return['search_event'] = '';
        $a_return['search_campaign_type'] = '';
        $a_return['search_start_marketing_date'] = '';
        $a_return['search_end_marketing_date'] = '';
        $a_return['search_marketing_status'] = '';

        if (isset($request->form_type) && $request->form_type == 'search_marketing') {
           
            session(['campaign_name' => $request->campaign_name]);
            session(['search_event' => $request->search_event]);
            session(['search_campaign_type' => $request->search_campaign_type]);
            session(['start_marketing_date' => $request->start_marketing_date]);
            session(['end_marketing_date' => $request->end_marketing_date]);
            session(['marketing_status' => $request->marketing_status]);

            return redirect('/marketing');
        }
        $a_return['search_campaign_name'] = (!empty(session('campaign_name'))) ? session('campaign_name') : '';
        $a_return['search_event'] = (!empty(session('search_event'))) ? session('search_event') : '';
        $a_return['search_campaign_type'] = (!empty(session('search_campaign_type'))) ? session('search_campaign_type') : '';
        $a_return['search_start_marketing_date'] = (!empty(session('start_marketing_date'))) ?  session('start_marketing_date') : '';
        $a_return['search_end_marketing_date'] = (!empty(session('end_marketing_date'))) ? session('end_marketing_date'): '';
        $marketing_status = session('marketing_status');
        $a_return['search_marketing_status'] = (isset($marketing_status) && $marketing_status != '') ? $marketing_status : '';
   

        $CountRows = MarketingModel::get_count($a_return);
        // dd($CountRows);
        $PageNo = request()->input('page', 1);
        $Limit = config('custom.per_page');
        // $Limit = 3;
        $a_return['Offset'] = ($PageNo - 1) * $Limit;
        
       
        $a_return["Marketing"] = MarketingModel::get_all($Limit,$a_return);

        $SQL = "SELECT id,name FROM events WHERE active=1 AND deleted = 0";
        $a_return['EventsData'] = DB::select($SQL, array());
    //  dd($a_return["Remittance"][0]);
        $a_return['Paginator'] = new LengthAwarePaginator( $a_return["Marketing"], $CountRows, $Limit, $PageNo);
        $a_return['Paginator']->setPath(request()->url());


        return view('marketing.list',$a_return);
    }

    public function add_edit(Request $request, $iId = 0){
        $a_return['campaign_name'] = '';
        $a_return['count'] = '';
        $a_return['start_date'] = '';
        $a_return['end_date'] = '';
        $a_return['event_id'] = '';
        $a_return['campaign_type'] = '';
      

        if (isset($request->form_type) && $request->form_type == 'add_edit_marketing') {
            $rules = [
                'event' => 'required',
                'campaign_name' => 'required|unique:marketing,campaign_name,' . $iId . 'id',
                'campaign_type' => 'required',
                'count' => 'required',
                'start_date' => 'required',
                'end_date' => 'required'
            ];

            $request->validate($rules);

            if ($iId > 0) {
                MarketingModel::update_marketing($iId, $request);
                $successMessage = ' Marketing details updated successfully';
            }else{
              
                MarketingModel::add_marketing($request);
                $successMessage = 'Marketing details added successfully';
            }
            return redirect('/marketing')->with('success', $successMessage);

        }else{
            if($iId > 0){
            //   #SHOW EXISTING DETAILS ON EDIT
              $sSQL = 'SELECT id,event_id,campaign_name,campaign_type,count,start_date,end_date FROM marketing WHERE id=:id';
              $marketingdetails = DB::select($sSQL, array( 'id' => $iId));
              $a_return = (array)$marketingdetails[0];
             //dd($a_return );
            }
          }      
        
        $SQL = "SELECT id,name FROM events WHERE active=1 AND deleted = 0";
        $a_return['EventsData'] = DB::select($SQL, array());
          
        return view('marketing.create',$a_return);
    }

    public function delete_marketing($iId){
        MarketingModel::delete_marketing($iId);
        return redirect(url('/marketing'))->with('success', 'Marketing details deleted successfully');
    }

    public function change_active_status(Request $request)
    {
        $aReturn = MarketingModel::change_status_marketing($request);
        // dd($aReturn);
        return $aReturn;
    }
    
}
