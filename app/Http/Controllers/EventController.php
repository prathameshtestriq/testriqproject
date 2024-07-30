<?php

namespace App\Http\Controllers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

//use DateTimeZone;

class EventController extends Controller
{
    //
    public function clear_search()
    {
        session()->forget('name');
        session()->forget('city');
        session()->forget('state');
        session()->forget('country');
        session()->forget('event_start_date');
        session()->forget('event_end_date');
        session()->forget('event_status');
        return redirect('/event');
    }


    public function index(Request $request)
    {
        // dd($request->all());
        // dd('here');

        $aReturn = array();
        $aReturn['search_name'] = '';
        $aReturn['search_city'] = '';
        $aReturn['search_event_start_date'] = '';
        $aReturn['search_event_end_date'] = '';
        $aReturn['search_event_status'] = '';

        // $aReturn['search_state'] = DB::table('vehicle_master')->where('isdeleted', 0)->pluck('vehicle_name', 'id');
        // $aReturn['search_country'] = DB::table('vehicle_management')->where('isdeleted', 0)->pluck('vehicle_number', 'id');
        // dd($aReturn['vehicle_numbers']);
        //  dd('vehicle_types');
        // $request->city = '';
        // $aReturn['citys'] = DB::table('cities')->where('show_flag', 1)->pluck('name', 'id');
        // dd($aReturn['city']);
        // $aReturn['vehicle_numbers'] = DB::table('vehicle_management')->where('isdeleted', 0)->pluck('vehicle_number', 'id');

        if (isset($request->form_type) && $request->form_type == 'search_event') {
            session(['name' => $request->name]);
            session(['city' => $request->city]);
            session(['state' => $request->state]);
            session(['country' => $request->country]);
            session(['event_start_date' => $request->event_start_date]);
            session(['event_end_date' => $request->event_end_date]);
            session(['event_status' => $request->event_status]);
            return redirect('/event');
        }

        $aReturn['search_name'] = (!empty(session('name'))) ? session('name') : '';
        $aReturn['search_city'] = (!empty(session('city'))) ? session('city') : '';
        $aReturn['search_state'] = (!empty(session('state'))) ? session('state') : '';
        $aReturn['search_country'] = (!empty(session('country'))) ? session('country') : '';
        $aReturn['search_event_start_date'] = (!empty(session('event_start_date'))) ?  session('event_start_date') : '';
        $aReturn['search_event_end_date'] = (!empty(session('event_end_date'))) ? session('event_end_date'): '';
        $event_status = session('event_status');
        $aReturn['search_event_status'] = (isset($event_status) && $event_status != '') ? $event_status : '';

 
        //dd($aReturn['search_vehicle_number']);
        $FiltersSql = '';
        //  dd($aReturn['search_district_name']);

        if (!empty($aReturn['search_name'])) {
            $FiltersSql .= ' AND (LOWER(vm.name) LIKE \'%' . strtolower($aReturn['search_name']) . '%\')';
        } else {
            $aReturn['search_name'] = '';
        }

        if(!empty($aReturn['search_event_start_date'])){
            $startdate = strtotime($aReturn['search_event_start_date']);
            $FiltersSql .= " AND vm.start_time >= "." $startdate";
            // dd($sSQL);
        }

        if(!empty($aReturn['search_event_end_date'])){
            $endDate = strtotime($aReturn['search_event_end_date']);
            $FiltersSql .= " AND vm.end_time <="." $endDate";
            // dd($sSQL);
        }

        if(isset(  $aReturn['search_event_status'] )){
            $FiltersSql .= ' AND (LOWER(vm.active) LIKE \'%' . strtolower( $aReturn['search_event_status'] ) . '%\')';
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
        // $Limit = 3;
        $aReturn['Offset'] = ($PageNo - 1) * $Limit;


        // $sSQL = 'SELECT vm.id,vm.name,vm.start_time,vm.end_time,vm.city,vm.state,vm.country,vm.active FROM events as vm WHERE vm.deleted = 0' . $FiltersSql;

        // $sSQL .= ' ORDER BY vm.name ASC ';
        $sSQL = 'SELECT vm.id, vm.name, vm.start_time, vm.end_time, (SELECT name FROM cities WHERE Id = vm.city) AS city, (SELECT name FROM states WHERE Id = vm.state) As state,(SELECT name FROM countries WHERE Id = vm.country) As country, vm.active FROM events AS vm WHERE vm.deleted = 0' . $FiltersSql. ' order by vm.id desc';


        if ($Limit > 0) {
            $sSQL .= ' LIMIT ' . $aReturn['Offset'] . ',' . $Limit;
        }
        //dd($sSQL );
        $aReturn['event_array'] = DB::select($sSQL, array());
        //  dd($aReturn['event_array']);
        $aReturn['Paginator'] = new LengthAwarePaginator($aReturn['event_array'], $CountRows, $Limit, $PageNo);
        $aReturn['Paginator']->setPath(request()->url());
        //dd($aReturn);

        //return view('master_data.event.list', $aReturn);
        return view('master_data.event.list', $aReturn);
    }

    public function add_edit(Request $request, $id = 0)
    {
        $aReturn = [
            'name' => '',
            'start_time' => '',
            'end_time' => '',
            'countries' => DB::table('countries')->where('flag', 1)->get(),
            //  'countries' => DB::select('select id,name FROM countries where flag = 1'),
            'time_zone' => 'select id,area FROM master_timezones where active = 1',
            'categories' => DB::table('event_category')->get(),
            'country' => '',
            'state' => 'select id,name FROM states where flag = 1',
            'city' => '',
            'event_url' => '',
            'active' => 1,
            'event_description' => '',
            'event_keywords' => '',
            'address' => '',
            'Category' => '',
            'SuccessMessage' => ''
        ];
        $Category = DB::select('SELECT *, (SELECT name FROM category WHERE category_id = id) AS category_name  FROM event_category WHERE  1=1');
      //  $allTypes = DB::select('SELECT *, (SELECT name FROM eTypes WHERE type_id = id) AS type_name FROM event_type WHERE 1=1');
        $selectedTypes = [];
                foreach ($Category as $category) {
                    $result = DB::select("SELECT * FROM event_category WHERE category_id = ? AND event_id = ? ", [$category->category_id, $id]);
                    $isSelected = sizeof($result) > 0 ? 'selected' : '';
                    // $aReturn['allTypes'] = $allTypes;
                    // If the type is selected, add it to the selectedTypes array
                    $selectedTypes[$category->category_id] = $category;
                    $category->selected = $isSelected;
                }
               
             //   dd($selectedTypes);
        
                $Category = array_diff_key($selectedTypes);
              //  dd($Category);
                $aReturn['Category'] = $selectedTypes; 
             //   $aReturn['Category'] = $selectedTypes; // Pass $allTypes to the view

     //dd( $aReturn['Category']);

        // Validation Rules
        $rules = [
            'name' => 'required|unique:events,name,' . $id . 'id',
            'start_time' => 'required|date',
            'end_time' => 'required|date',
            'city' => 'required|string',
            'state' => 'required|string',
            'country' => 'required|string',
            'address' => 'required|string',
            'event_url' => 'required|string',
            'event_description' => 'required|string',
            'event_keywords' => 'required|string',
            'time_zone' => 'required|string',

        ];

        // Form handling
        if ($request->form_type == 'add_edit_event') {
            $validatedData = $request->validate($rules);
           // $Category = isset($request->category_id) ? $request->category_id : [];
            
            // Extracting values from request
            $name = $validatedData['name'];
            $start_time = strtotime($validatedData['start_time']);
            $end_time = strtotime($validatedData['end_time']);
            $city = $validatedData['city'];
            $state = $validatedData['state'];
            $country = $validatedData['country'];
           // $active = $validatedData['active'];
            $address = $validatedData['address'];
            $event_url = $request->event_url;
            $description = $request->event_description;
            $event_keywords = $request->event_keywords;
            $timezones = $request->time_zone;
           // $active = $request->input('active', 1);
            $Category = $request->input('category_id', []);
            // Update or insert based on $id
            if ($id > 0) {
                // if ($request->active == 'active') {
                //     $active = 1;
                // }
                // if ($request->active == 'inactive') {
                //     $active = 0;
                // }
                // Existing event update logic
                $usql = 'UPDATE events SET name = :name, start_time = :start_time, end_time = :end_time, city = :city, state = :state, country = :country, event_url = :event_url, event_description = :description, event_keywords = :event_keywords, address = :address, time_zone = :timezones  WHERE id = :id';
                $bindings = [
                    'name' => $name,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'city' => $city,
                    'state' => $state,
                    'country' => $country,
                    'event_url' => $event_url,
                    'description' => $description,
                    'event_keywords' => $event_keywords,
                    'address' => $address,
                    'timezones' => $timezones,
                    'id' => $id
                ];

                // Execute the update query
                $result = DB::update($usql, $bindings);

                if ($result) {
                    $successMessage = 'Event updated successfully';
                    DB::delete('DELETE FROM event_category WHERE event_id = ?', [$id]); // Clear existing type entries

                    // Insert new type entries
                    foreach ($Category as $category) {
                   
                        DB::insert('INSERT INTO event_category (event_id, category_id) VALUES (?, ?)', [$id, $category]);
                    }
              
                    // foreach ($typeIds as $typeId) {
                    //     DB::insert('INSERT INTO event_type (event_id, type_id) VALUES (?, ?)', [$iId, $typeId]);
                    // }
                  //  dd($Category);
                    return redirect('/event')->with('success', $successMessage);
                } else {
                    return "failed";
                }
            } else {
                // New event insert logic
                
                $sql = 'INSERT INTO events (name, start_time, end_time, city, state, country, active, event_description, event_url, event_keywords, address, time_zone) VALUES (:name, :start_time, :end_time, :city, :state, :country, :active, :description, :event_url, :event_keywords, :address, :timezones)';
                $bindings = [
                    'name' => $name,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'city' => $city,
                    'state' => $state,
                    'country' => $country,
                    'active' => 1,
                    'description' => $description,
                    'event_url' => $event_url,
                    'event_keywords' => $event_keywords,
                    'address' => $address,
                    'timezones' => $timezones
                ];
                $successMessage = 'Event added successfully';

                // Execute query
                DB::insert($sql, $bindings);
                //   dd('here');


                $EventId = DB::getPdo()->lastInsertId();
               if (!empty($Category) && !empty($EventId)) {
                    foreach ($Category as $category) {
                        //  dd('here');
                        // Insert into pivot table for each category
                        // $categories = DB::table('event_category')->get();
                        // $aReturn['categories'] = $categories;
                        $sql = "INSERT INTO event_category (event_id, category_id,created_by) VALUES(:event_id,:category_id,:created_by)";
                        //    dd($sql);
                        $Bind = array(
                            "event_id" => $EventId,
                            "category_id" => $category,
                            "created_by" => $id // Assuming you're using Laravel's built-in authentication
                        );
                        DB::insert($sql, $Bind);
                    }

                }
                return redirect('/event')->with('success', $successMessage);
                // }
            }
        } else {
            // Edit event

            if ($id > 0) {

                $sSQL = 'SELECT * FROM events WHERE id=:id';
                //    $sSQL .= 'SELECT * FROM  event_category WHERE id =:id';
                $Materials = DB::select($sSQL, array('id' => $id));
                //     dd($Materials);
                $aReturn = (array) $Materials[0];
            }
        }
   
        $aReturn['timezones_array'] = DB::table('master_timezones')->where('active', 1)->get();
        $cSql = 'select id,name FROM countries where flag = 1 and id = 101';
        $aReturn['countries_array'] = DB::select($cSql);
        $sSql = 'select id,name FROM states where flag = 1 and country_id = 101';
        $aReturn['states_array'] = DB::select($sSql);
        $ccSql = 'select id,name FROM cities where show_flag = 1 and country_id = 101';
        $aReturn['cities_array'] = DB::select($ccSql);
        $aReturn['Category'] = $Category;
     //      
   //     dd($aReturn['Category']);
        return view('master_data.event.create', $aReturn);

    }
    public function change_active_status(Request $request)
    {
        if ($request->active == 'active') {
            $active = 1;
        }
        if ($request->active == 'inactive') {
            $active = 0;
        }
        //  dd('here');
        $sSQL = 'UPDATE events SET active=:active WHERE id=:id';
        $Bindings = array(
            'active' => $request->active,
            'id' => $request->id
        );
        $result = DB::update($sSQL, $Bindings);
        return $result;
    }

    public function remove_event($iId)
    {
        if (!empty($iId)) {
            // dd($iId);
            $sSQL = 'UPDATE events SET deleted = 1 WHERE id=:id';
            $Result = DB::update($sSQL, array('id' => $iId));
            //    dd($Result);       
        }
        return redirect('/event')->with('success', 'event deleted successfully');
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