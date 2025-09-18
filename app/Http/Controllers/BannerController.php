<?php

namespace App\Http\Controllers;

use App\Libraries\Curlcall;
use App\Libraries\Mysecurity; 
use App\Models\AdminModel;
use App\Models\AdminUserRight;
use App\Models\Banner;
use App\Models\Master_farmer;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class BannerController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */

    public function clear_search()
    {
        session::forget('banner_name');
        session::forget('start_booking_date');
        session::forget('end_booking_date');
        session::forget('banner_status');
        session::forget('banner_country'); 
        session::forget('banner_state'); 
        session::forget('banner_city'); 
        return redirect('/banner');
    }

    public function index_banner(Request $request)
    {
        $aReturn = array();
        $aReturn['search_banner'] = '';
        $aReturn['search_start_booking_date'] = '';
        $aReturn['search_end_booking_date'] = '';
        $aReturn['banner_status'] = '';
        $aReturn['search_country'] = '';
        $aReturn['search_state'] = '';
        $aReturn['search_city'] = '';

        if(isset($request->form_type) && $request->form_type ==  'search_banner') {
            //  dd($request->name);
            session(['banner_name' => $request->name]);
            session(['start_booking_date' => $request->start_booking_date]);
            session(['end_booking_date' => $request->end_booking_date]);
            session(['banner_status' => $request->banner_status]);
            //  session(['mobile' => $request->mobile]);
            session(['banner_country' => $request->country]);
            session(['banner_state' => $request->state]);
            session(['banner_city' => $request->city]);

            return redirect('/banner');
        }
        $aReturn['search_banner'] =  (!empty(session('banner_name'))) ? session('banner_name') : '';
        $aReturn['search_start_booking_date'] = (!empty(session('start_booking_date'))) ?  session('start_booking_date') : '';
        $aReturn['search_end_booking_date'] = (!empty(session('end_booking_date'))) ? session('end_booking_date'): '';
        $banner_status = session('banner_status');
        $aReturn['search_banner_status'] = (isset($banner_status) && $banner_status != '') ? $banner_status : '';
        $aReturn['search_country'] =  (!empty(session('banner_country'))) ? session('banner_country') :'';
        $aReturn['search_state'] =  (!empty(session('banner_state'))) ? session('banner_state') : '';
        $aReturn['search_city'] =  (!empty(session('banner_city'))) ? session('banner_city') : '';
        // $aReturn['mobile'] =  (!empty(session('mobile'))) ? session('mobile') : '';

        // dd($aReturn);
        $CountRows = Banner::get_count($aReturn);
        $PageNo = request()->input('page', 1);
        $Limit = config('custom.per_page');
        // $Limit = 3;
        $aReturn['Offset'] = ($PageNo - 1) * $Limit;
        $aReturn["banner_array"] = Banner::get_all($Limit, $aReturn);
        //dd( $aReturn["banner_array"]);

        $sSQL = 'SELECT id, name FROM countries WHERE 1=1';
        $aReturn["countries"] = DB::select($sSQL, array());


        $aReturn['Paginator'] = new LengthAwarePaginator($aReturn['banner_array'], $CountRows, $Limit, $PageNo);
        $aReturn['Paginator']->setPath(request()->url());
        //  dd($aReturn);
        return view('Banner.list', $aReturn);
    }



    public function add_edit_banner(Request $request, $iId = 0)
    {

        $a_return['banner_name'] = '';
        $a_return['banner_image'] = ''; 
        $a_return['banner_url'] = '';
        $a_return['start_date'] = '';
        $a_return['end_date'] = '';
        $a_return['city'] = '';
        $a_return['state'] = 'select id,name from states where flag ';
        $a_return['country'] = '';
        $a_return['active'] = '';
        $image_name = '';

        // $a_return['country_array'] = DB::table('countries')->pluck('name', 'id')->toArray();
        // $cSql = 'select id,name FROM countries where flag = 1 and id = 101';
        // $a_return['countries_array'] = DB::select($cSql);
        // $sSql = 'select id,name FROM states where flag = 1 and country_id = 101';
        // $a_return['states_array'] = DB::select($sSql);
        // $ccSql = 'select id,name FROM cities where show_flag = 1 and country_id = 101';
        // $a_return['cities_array'] = DB::select($ccSql);
        // dd($a_return['city_array']);

        $edit_array = [];
        if ($iId > 0) {
            // #SHOW EXISTING DETAILS ON EDIT
            $sql = 'SELECT id,banner_name, banner_image, banner_url, start_time, end_time, city, state, country, active, created_datetime
                    FROM banner
                    WHERE id = ?';

            $bannerdetails = DB::select($sql, array($iId));
            $edit_array = (array) $bannerdetails[0];
           
        }
        //  dd($edit_array);

        if ($request->has('form_type') && $request->form_type == 'add_edit_banner') {

            $rules = [
                'banner_name' => 'required|unique:banner,banner_name,' . $iId . 'id',
                'banner_url' => [
                    'required',
                    'regex:/^(www\.|http:\/\/|https:\/\/).*/i', 
                ],
                'banner_image' => empty($edit_array) || $edit_array['banner_image'] === '' ? 'required|mimes:jpeg,jpg,png,gif|max:10240' : 'mimes:jpeg,jpg,png,gif|max:10240',
                 'start_date' => 'required|date|after_or_equal:today',
                 'end_date'   => 'required|date|after_or_equal:start_date',
                 'country' => 'required|exists:countries,id',
                 'state'   => 'required|exists:states,id',
                 'city'    => 'required|exists:cities,id',
            ];
            
            
            $message = [ 
                'banner_url.required' => 'The banner URL field is required.',
                'banner_url.regex' => 'The banner URL must start with "www.", "http://", or "https://".',
                'banner_image.mimes' => 'The banner image must be a file of type: jpeg, jpg, png, gif.',
                'banner_image.max' => 'The banner image must be less than 10MB.', 
            ];  
         

            $validator = Validator::make($request->all(), $rules,$message);
            // dd($validator);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            //  dd($request->all());
            if ($iId > 0) {
                $result = Banner::update_banner($iId, $request);
                $successMessage = 'Banner updated successfully';
            } else {

                $result = Banner::add_banner($request);
                $successMessage = 'Banner added successfully';
            }

            return redirect('/banner')->with('success', $successMessage);
        } else {
            $a_return['edit_data'] = !empty($edit_array) ? $edit_array : [] ;
        }

        $sSQL = 'SELECT id, name FROM countries WHERE 1=1';
        $a_return["countries"] = DB::select($sSQL, array());

        return view('Banner.create', $a_return);
    }

    public function change_status(Request $request)
    {
        $aReturn = Banner::change_status_banner($request);
        $successMessage  = 'Status changed successfully';
        $sucess = 'true';
        $aReturn = [];
        $aReturn['message'] =  $successMessage ;
        $aReturn['sucess'] = $sucess;
        
        // dd($aReturn);
        return $aReturn;
    }

    public function delete_banner($iId)
    {
        Banner::remove_banner($iId);
        return redirect(url('/banner'))->with('success', 'Banner deleted successfully');
    }

    public function get_states(Request $request){
        $countryId = $request->get('country_id');
        // dd($countryId);
        $sSQL = 'SELECT id, name,country_id FROM states WHERE country_id ='. $countryId;
        $states = DB::select($sSQL, array());
        return response()->json($states);
        //dd($Return["states"]);
        // return $Return;
    }
    public function get_cities(Request $request){
        $stateId = $request->get('state_id');
        $sSQL = 'SELECT id,name,state_id FROM cities WHERE state_id =' .$stateId;
        $cities = DB::select($sSQL, array());
        return response()->json($cities);
        // return $Return;
    }
}
