<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

class AdvertiseController extends Controller
{
    public function clear_search()
    {
        session::forget('name');
        session::forget('start_date');
        session::forget('end_date');
        session::forget('advertisement_status');
        return redirect('/advertisement');
    }

    public function index_advertisement(Request $request)
    {

        $aReturn = array();
        $aReturn['search_name'] = ''; 
        $aReturn['search_start_booking_date'] = '';
        $aReturn['search_end_booking_date'] = '';
        $aReturn['search_advertisement_status'] = '';
        

        if(isset($request->form_type) && $request->form_type ==  'search_ad') {
            //  dd($request->name);
            session(['name' => $request->name]);
            session(['start_date' => $request->start_date]);
            session(['end_date' => $request->end_date]);
            session(['advertisement_status' => $request->advertisement_status]);
 
            return redirect('/advertisement');
        }

        $aReturn['search_name'] =  (!empty(session('name'))) ? session('name') : '';
        $aReturn['search_start_booking_date'] = (!empty(session('start_date'))) ?  session('start_date') : '';
        $aReturn['search_end_booking_date'] = (!empty(session('end_date'))) ? session('end_date'): '';
        $advertisement_status = session('advertisement_status');
        $aReturn['search_advertisement_status'] = (isset($advertisement_status) && $advertisement_status != '') ? $advertisement_status : '';

        //dd($aReturn);
        // dd($aReturn);
        $CountRows = Advertisement::get_count_ad($aReturn);
        $PageNo = request()->input('page', 1);
        $Limit = config('custom.per_page');
        // $Limit = 3;
        $aReturn['Offset'] = ($PageNo - 1) * $Limit;

        $aReturn["ad_array"] = Advertisement::get_all_ad($Limit, $aReturn);

        $aReturn['Paginator'] = new LengthAwarePaginator($aReturn['ad_array'], $CountRows, $Limit, $PageNo);
        $aReturn['Paginator']->setPath(request()->url());
        //  dd($aReturn);
        return view(' advertisement.list', $aReturn);
    }



    public function add_edit_advertisement(Request $request, $iId = 0)
    {

        $a_return['name'] = '';
        $a_return['status'] = '';
        $a_return['position'] = '';
        $a_return['start_date'] = '';
        $a_return['end_date'] = '';
        $a_return['url'] = '';
        $a_return['img'] = '';
        $aResult = [];
        
        if ($iId > 0) {
            $sql = 'SELECT id,name,position, start_time, end_time, img, url, status
            FROM advertisement 
            WHERE id = ?';

            $addetails = DB::select($sql, array($iId));
            $aResult = (array) $addetails[0];
           
        }

        if ($request->has('form_type') && $request->form_type == 'add_edit_ad') {


            $rules = [
                'name' => 'required|unique:advertisement,name,' . $iId . 'id',
                'url' => [
                    'required',
                    'regex:/^(www\.|http:\/\/|https:\/\/).*/i', 
                ],
                'start_date' => 'required',
                'end_date' => 'required',
                'position' => 'required', 
                'img' => empty($aResult) || $aResult['img'] === '' ? 'required|mimes:jpeg,jpg,png,gif' : '',
            ];

            $message = [ 
                'url.required' => 'The URL field is required.',
                'url.regex' => 'The URL must start with "www.", "http://", or "https://".',
                'img.required' => 'The image field is required .',
                'img.mimes' => 'The image must be a file of type: jpeg, jpg, png, gif.',
                // 'img.size' => 'The image must be 2MB or below.', 
            ];
            $validator = Validator::make($request->all(), $rules,$message);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }


            if ($iId > 0) {
                $result =  Advertisement::update_advertisement($iId, $request);
                $successMessage = 'Advertisement updated successfully';
            } else {

                $result =  Advertisement::add_advertisement($request);
                $successMessage = 'advertisement added successfully';
            }

            return redirect('/advertisement')->with('success', $successMessage);
        } else {
             $a_return['edit_data'] = $aResult;
            
        }
     

        return view('advertisement.create', $a_return);
        // Pass $a_return array to the view
        //return view('advertisement.create', compact('a_return'));

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
        $aReturn = Advertisement::change_status_advertisement($request);
        //  dd($aReturn);
        return $aReturn;
    }

    public function delete_advertisement($iId)
    {
        Advertisement::remove_add($iId);
        return redirect(url('/advertisement'))->with('success', 'advertisement deleted successfully');
    }
}
