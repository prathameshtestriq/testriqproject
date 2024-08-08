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
        return redirect('/banner');
    }

    public function index_banner(Request $request)
    {
        $aReturn = array();
        $aReturn['search_banner'] = '';
        $aReturn['search_start_booking_date'] = '';
        $aReturn['search_end_booking_date'] = '';
        $aReturn['banner_status'] = '';

        if(isset($request->form_type) && $request->form_type ==  'search_banner') {
            //  dd($request->name);
            session(['banner_name' => $request->name]);
            session(['start_booking_date' => $request->start_booking_date]);
            session(['end_booking_date' => $request->end_booking_date]);
            session(['banner_status' => $request->banner_status]);
            //  session(['mobile' => $request->mobile]);

            return redirect('/banner');
        }
        $aReturn['search_banner'] =  (!empty(session('banner_name'))) ? session('banner_name') : '';
        $aReturn['search_start_booking_date'] = (!empty(session('start_booking_date'))) ?  session('start_booking_date') : '';
        $aReturn['search_end_booking_date'] = (!empty(session('end_booking_date'))) ? session('end_booking_date'): '';
        $banner_status = session('banner_status');
        $aReturn['search_banner_status'] = (isset($banner_status) && $banner_status != '') ? $banner_status : '';

        // $aReturn['mobile'] =  (!empty(session('mobile'))) ? session('mobile') : '';

        // dd($aReturn);
        $CountRows = Banner::get_count($aReturn);
        $PageNo = request()->input('page', 1);
        $Limit = config('custom.per_page');
        // $Limit = 3;
        $aReturn['Offset'] = ($PageNo - 1) * $Limit;

        $aReturn["banner_array"] = Banner::get_all($Limit, $aReturn);
        //dd( $aReturn["banner_array"]);

        // foreach ($aReturn["banner_array"] as $banner) {
        //     // Assuming you have a field named 'banner_image' in your database table
        //     // Construct the image URL
        //     $banner->banner_image = asset('uploads/banner_image/' . $banner->banner_image);
        // }


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
        $a_return['start_time'] = '';
        $a_return['end_time'] = '';
        $a_return['city'] = '';
        $a_return['state'] = 'select id,name from states where flag ';
        $a_return['country'] = '';
        $a_return['active'] = '';
        $image_name = '';

        // $a_return['country_array'] = DB::table('countries')->pluck('name', 'id')->toArray();
        $cSql = 'select id,name FROM countries where flag = 1 and id = 101';
        $a_return['countries_array'] = DB::select($cSql);
        $sSql = 'select id,name FROM states where flag = 1 and country_id = 101';
        $a_return['states_array'] = DB::select($sSql);
        $ccSql = 'select id,name FROM cities where show_flag = 1 and country_id = 101';
        $a_return['cities_array'] = DB::select($ccSql);
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
         // dd($edit_array);

        if ($request->has('form_type') && $request->form_type == 'add_edit_banner') {

            $rules = [
                'banner_name' => 'required|unique:banner,banner_name,' . $iId . 'id',
                'banner_url' => 'required',
                'banner_image' => !empty($edit_array) && $edit_array['banner_image'] !== '' ? '' : 'required',
                'start_time' => 'required',
                'end_time' => 'required',
                'city' => 'required',
                'state' => 'required',
                'country' => 'required'
            ];

            if ($request->has('banner_image')) {
                $rules['banner_image'] = 'required|mimes:jpeg,jpg,png,gif|max:2000';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            //  dd($image_name);
            if ($iId > 0) {
                $result = Banner::update_banner($iId, $request);
                $successMessage = 'banner updated successfully';
            } else {

                $result = Banner::add_banner($request);
                $successMessage = 'banner added successfully';
            }

            return redirect('/banner')->with('success', $successMessage);
        } else {
            $a_return['edit_data'] = !empty($edit_array) ? $edit_array : [] ;
        }

        // $cSql = 'select id,name FROM countries where flag = 1';
        // $a_return['countries_array'] = DB::select($cSql);

        // $a_return['states_array'] = DB::select($sSql);
        // $ccSql = 'select id,name FROM cities where show_flag = 1';

        // $ccSql = 'select id,name FROM cities where show_flag = 1';
        // $a_return['cities_array'] = DB::select($ccSql);
        // dd($a_return['edit_data']);
        return view('Banner.create', $a_return);
    }


    // public function get_country_info(Request $request)
    // {
    //     $country_data = array();
    //     if($request->country_id == 1) {
    //         $country_id = !empty($request->country_id) ? $request->country_id : 0;
    //         $post = array('country_id' => $country_id);
    //         $country_data = Master_farmer::get_country_info($country_id, $post);
    //     }

    //     if($country_data) {
    //         return $country_data;
    //     } else {
    //         return [];
    //     }
    // }

    public function change_status(Request $request)
    {
        $aReturn = Banner::change_status_banner($request);
        // dd($aReturn);
        return $aReturn;
    }

    public function delete_banner($iId)
    {
        Banner::remove_banner($iId);
        return redirect(url('/banner'))->with('success', 'banner deleted successfully');
    }

    public function getStates(Request $request)
    {
        $states = DB::table('states')
            ->where('country_id', $request->country_id)
            ->pluck('name', 'id');
        return $states;
    }

    public function getCities(Request $request)
    {
        $cities = DB::table('cities')
            ->where('state_id', $request->state_id)
            ->pluck('name', 'id');
        return $cities;
    }
}
