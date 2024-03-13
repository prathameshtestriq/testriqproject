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
        return redirect('/advertisement');
    }

    public function index_advertisement(Request $request)
    {

        $aReturn = array();
        $aReturn['search_ad'] = ''; 

        if(isset($request->form_type) && $request->form_type ==  'search_ad') {
            //  dd($request->name);
            session(['name' => $request->name]);

            return redirect('/advertisement');
        }

        $aReturn['search_ad'] =  (!empty(session('name'))) ? session('name') : '';
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
        $a_return['url'] = '';
        $a_return['img'] = '';


        if ($request->has('form_type') && $request->form_type == 'add_edit_ad') {


            $rules = [
                 'name' => 'required',
                 'url' => 'required',
                'staus' => 'required',
                'img' => 'required',
            ];

            if ($request->has('img')) {
                $rules['img'] = 'required|image|mimes:jpeg,png,jpg';
            }

            $validator = Validator::make($request->all(), $rules);

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
            if ($iId > 0) {
                $sql = 'SELECT id,name, img, url, status
                FROM advertisement 
                WHERE id = ?';


                $addetails = DB::select($sql, array($iId));
                $a_return = (array) $addetails[0];
                // dd($a_return);
            }
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
