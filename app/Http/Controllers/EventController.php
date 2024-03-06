<?php

namespace App\Http\Controllers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;


class EventController extends Controller
{
    //
    public function clear_search()
    {
        session()->forget('name');
        session()->forget('city');
        session()->forget('state');
        session()->forget('country');
        return redirect('/event');
    }


    public function index(Request $request)
    {
        // dd($request->all());
        // dd('here');

        $aReturn = array();
        $aReturn['search_name'] = '';
        $aReturn['search_city'] = '';
        // $aReturn['search_state'] = DB::table('vehicle_master')->where('isdeleted', 0)->pluck('vehicle_name', 'id');
        // $aReturn['search_country'] = DB::table('vehicle_management')->where('isdeleted', 0)->pluck('vehicle_number', 'id');
        // dd($aReturn['vehicle_numbers']);
        //  dd('vehicle_types');

        if (isset($request->form_type) && $request->form_type == 'search_event') {
            session(['name' => $request->name]);
            session(['city' => $request->city]);
            session(['state' => $request->state]);
            session(['country' => $request->country]);
            return redirect('/event');
        }

        $aReturn['search_name'] = (!empty(session('name'))) ? session('name') : '';
        $aReturn['search_city'] = (!empty(session('city'))) ? session('city') : null;
        $aReturn['search_state'] = (!empty(session('state'))) ? session('state') : '';
        $aReturn['search_country'] = (!empty(session('country'))) ? session('country') : '';
        //dd($aReturn['search_vehicle_number']);
        $FiltersSql = '';
        //  dd($aReturn['search_district_name']);

        if (!empty($aReturn['search_name'])) {
            $FiltersSql .= ' AND (LOWER(vm.name) LIKE \'%' . strtolower($aReturn['search_name']) . '%\')';
        } else {
            $aReturn['search_name'] = '';
        }
        // if (!empty($aReturn['search_vehicle_number'])) {
        //     $FiltersSql .= ' AND vm.id = ' . $aReturn['search_vehicle_number'];

        //     //  dd($FiltersSql);

        // } else {
        //     $aReturn['search_vehicle_number'] = '';
        //     // dd($aReturn['search_vehicle_number']);
        // }
        // // dd($FiltersSql);
        // if (!empty($aReturn['search_vehicle_type'])) {
        //     $FiltersSql .= ' AND vm.vehicle_type = ' . $aReturn['search_vehicle_type'] . '';
        // } else {
        //     $aReturn['search_vehicle_type'] = '';
        // }
        //  dd($FiltersSql);

        #PAGINATION
        $sSQL = 'SELECT count(vm.id) as count FROM events as vm WHERE 1=1 AND vm.deleted=0 ' . $FiltersSql;
        $CountsResult = DB::select($sSQL);
        // dd($CountsResult);
        $CountRows = 0;
        if (!empty($CountsResult)) {
            $CountRows = $CountsResult[0]->count;
        }
        // dd($count);
        $PageNo = request()->input('page', 1);
        $Limit = config('custom.per_page');
        $aReturn['Offset'] = ($PageNo - 1) * $Limit;

        $sSQL = 'SELECT vm.id,vm.name,vm.start_time,vm.end_time,vm.city,vm.state,vm.country,vm.active FROM events as vm WHERE vm.deleted = 0' . $FiltersSql;

        $sSQL .= ' ORDER BY vm.name ASC ';

        if ($Limit > 0) {
            $sSQL .= ' LIMIT ' . $aReturn['Offset'] . ',' . $Limit;
        }
        //dd($sSQL );
        $aReturn['event_array'] = DB::select($sSQL, array());
       //  dd($aReturn['event_array']);
        $aReturn['Paginator'] = new LengthAwarePaginator($aReturn['event_array'], $CountRows, $Limit, $PageNo);
        $aReturn['Paginator']->setPath(request()->url());
        //dd($aReturn);

        return view('master_data.event.list', $aReturn);
    }

    public function add_edit(Request $request, $iId = 0)
    {
        $aReturn['name'] = '';
        $aReturn['start_time'] = '';
        $aReturn['end_time'] = '';
        $aReturn['city'] = '';
        $aReturn['state'] = '';
        $aReturn['country'] = '';
        $SuccessMessage = '';
        //dd($request);
        if (isset($request->form_type) && $request->form_type == 'add_edit_event') {
            // dd('aa');
            $Rules = [
                'name' => 'required|string',
                'start_time' => 'required|string',
                'end_time' => 'required|string',
                'city' => 'required|string',
                'state' => 'required|string',
                'country' => 'required|string',
                //   'driver_contact_no' => 'required|max:13',
              //  'driver_contact_no' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|max:10|min:10',

            ];
            $name = (!empty($request->name)) ? $request->name : '';
            $start_time = (!empty($request->start_time)) ? $request->start_time : 0;
            $end_time = (!empty($request->end_time)) ? $request->end_time : '';
            $city = (!empty($request->city)) ? $request->city : '';
            $state = (!empty($request->state)) ? $request->state : '';
            $country = (!empty($request->country)) ? $request->country : '';


            if ($iId > 0) {
                //dd('update');
                #UPDATE

                $sSQL = 'UPDATE events SET name = :name,start_time = :start_time,end_time = :end_time,city = :city,state = :state, country = :country WHERE id = :id';



                $Bindings = array(
                    'name' => $name,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'city' => $city,
                    'state' => $state,
                    'country' => $country,
                    'id' => $iId
                );
                //  dd($Bindings);
                //duplication
                // $sSQL1 = "SELECT count(id) AS rec_count FROM vehicle_management WHERE LOWER(vehicle_brand) = '" . strtolower($vehicle_brand) . "' and id != " . $iId . " and isdeleted = 0 ";

                $sSQL1 = "SELECT COUNT(id) AS rec_count FROM events WHERE LOWER(name) = :name AND id != :id AND deleted = 0";
                $rResult = DB::select($sSQL1, ['name' => strtolower($name), 'id' => $iId]);
                //  dd($rResult);
                if (($rResult[0]->rec_count) == 1) {
                    $SuccessMessage = 'Event already exist';
                    return redirect('event/add')->with('error', $SuccessMessage);
                } else {
                    $Result = DB::update($sSQL, $Bindings);
                    $SuccessMessage = 'Event updated successfully';
                }
                // dd($Result);
            } else {
                // dd('insert');


                #ADD
                $request->validate($Rules);
                $sSQL = 'INSERT INTO events (name, start_time, end_time,city, state, country, created_date) VALUES (:name, :start_time, :end_time, :city, :state, :country,:created_date)';
                //   dd($sSQL);
                $Bindings = array(
                    'name' => $name,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'city' => $city,
                    'state' => $state,
                    'country' => $country,
                    'created_date' => time()
                );
                //dd($Bindings);

                //duplication
                //  $sSQL1 = "SELECT count(id) AS rec_count FROM vehicle_management WHERE vehicle_brand = '" . $vehicle_brand . "' AND vehicle_type = '" . $vehicle_type . "' AND vehicle_number = '" . $vehicle_number . "'";
                $sSQL1 = "SELECT COUNT(id) AS rec_count FROM events WHERE LOWER(name) = :name AND id != :id AND deleted = 0";
$rResult = DB::select($sSQL1, ['name' => strtolower($name), 'id' => $iId]);

                //dd($sSQL1);
                // $rResult = DB::select(DB::raw($sSQL1));
                //  dd($rResult);

                if (($rResult[0]->rec_count) == 0) {
                    $Result = DB::insert($sSQL, $Bindings);
                    $SuccessMessage = 'Event added successfully';
                } else {
                    $SuccessMessage = ' Event already exist';
                    return redirect('evnet/add')->with('error', $SuccessMessage);

                }
            }
            return redirect('/event')->with('success', $SuccessMessage);
        } else {


            //EDIT
            // if ($iId > 0) {

            //     $sSQL = 'SELECT * FROM events WHERE id=:id';
            //     $Materials = DB::select($sSQL, array('id' => $iId));
            //   //  dd($Materials);
            //     $aReturn = (array) $Materials[0];
            // }
        }

        $aSql = 'select id,name FROM events where deleted = 0';
        $aReturn['event_array'] = DB::select($aSql);
       // dd($aReturn['event_array']);
        return view('master_data.event.create', $aReturn);
    }

    public function remove_vehicle_management($iId)
    {
        if (!empty($iId)) {
            // dd($iId);
            $sSQL = 'UPDATE events SET deleted = 1 WHERE id=:id';
            $Result = DB::update($sSQL, array('id' => $iId));
            //   dd($Result);
        }
        return redirect('/event')->with('success', 'event deleted successfully');
    }
}
