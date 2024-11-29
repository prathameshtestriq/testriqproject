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
        session()->forget('event_city');
        session()->forget('event_state');
        session()->forget('event_country');
        session()->forget('event_start_date');
        session()->forget('event_end_date');
        session()->forget('event_status');
        session()->forget('organizer');
        session()->forget('event_info_status');
        return redirect('/event');
    }


    public function index(Request $request)
    {
        // dd($request->all());
        // dd('here');

        $aReturn = array();
        $aReturn['search_name'] = '';
        $aReturn['search_event_city'] = '';
        $aReturn['search_event_state'] = '';
        $aReturn['search_event_country'] = '';
        $aReturn['search_event_start_date'] = '';
        $aReturn['search_event_end_date'] = '';
        $aReturn['search_event_status'] = '';
        $aReturn['search_organizer'] = '';
        $aReturn['search_event_info_status'] = '';
        $aReturn['search_event_name'] = '';


        if (isset($request->form_type) && $request->form_type == 'search_event') {
            session(['name' => $request->name]);
            session(['event_city' => $request->event_city]);
            session(['event_state' => $request->event_state]);
            session(['event_country' => $request->event_country]);
            session(['event_start_date' => $request->event_start_date]);
            session(['event_end_date' => $request->event_end_date]);
            session(['event_status' => $request->event_status]);
            session(['organizer' => $request->organizer]);
            session(['event_info_status' => $request->event_info_status]);
            return redirect('/event');
        }

        $aReturn['search_event_name'] = (!empty(session('name'))) ? session('name') : '';
        $aReturn['search_event_city'] = (!empty(session('event_city'))) ? session('event_city') : '';
        $aReturn['search_event_state'] = (!empty(session('event_state'))) ? session('event_state') : '';
        $aReturn['search_event_country'] = (!empty(session('event_country'))) ? session('event_country') : '';
        $aReturn['search_event_start_date'] = (!empty(session('event_start_date'))) ?  session('event_start_date') : '';
        $aReturn['search_event_end_date'] = (!empty(session('event_end_date'))) ? session('event_end_date'): '';
        $event_status = session('event_status');
        $aReturn['search_event_status'] = (isset($event_status) && $event_status != '') ? $event_status : '';
        $aReturn['search_organizer'] = (!empty(session('organizer'))) ? session('organizer') : '';
        $aReturn['search_event_info_status'] = (!empty(session('event_info_status'))) ? session('event_info_status') : '';
        // dd($aReturn['search_organizer']);

 
        // dd($aReturn['search_organizer']);
        $FiltersSql = '';
        //  dd($aReturn['search_district_name']);

        if (!empty($aReturn['search_event_name'])) {
            $FiltersSql .= ' AND vm.id = '. $aReturn['search_event_name']. ' ';
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

        if(!empty( $aReturn['search_event_country'])){
            $FiltersSql .= ' AND vm.country = '. $aReturn['search_event_country']. ' ';
        } 

        if(!empty( $aReturn['search_event_state'])){
            $FiltersSql .= ' AND vm.state = '. $aReturn['search_event_state']. ' ';
        } 

        if(!empty( $aReturn['search_event_city'])){
            $FiltersSql .= ' AND vm.city = '. $aReturn['search_event_city']. ' ';
        } 

        if(isset(  $aReturn['search_event_status'] )){
            $FiltersSql .= ' AND (LOWER(vm.active) LIKE \'%' . strtolower( $aReturn['search_event_status'] ) . '%\')';
        } 

        // if (isset($aReturn['search_organizer'])) {
        //     $FiltersSql .= ' AND LOWER(o.name) LIKE \'%' . strtolower($aReturn['search_organizer']) . '%\'';
        // } 

        // dd($aReturn['search_organizer']);
        if (!empty($aReturn['search_organizer'])) {
            $FiltersSql .= ' AND vm.created_by = (select id from organizer where id = vm.created_by AND id = '.$aReturn['search_organizer'].')';
        } 
        if(!empty( $aReturn['search_event_info_status'])){
            $FiltersSql .= ' AND vm.event_info_status = '. $aReturn['search_event_info_status']. ' ';
        } 
       
       
        #PAGINATION
        // $sSQL = 'SELECT count(vm.id) as count FROM events as vm  LEFT JOIN organizer AS o ON o.user_id = vm.created_by WHERE 1=1 AND vm.deleted=0 ' . $FiltersSql;
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

        
        // $sSQL = 'SELECT vm.id, vm.name, vm.start_time, vm.end_time,vm.created_by (SELECT name FROM cities WHERE Id = vm.city) AS city, (SELECT name FROM states WHERE Id = vm.state) As state,(SELECT name FROM countries WHERE Id = vm.country) As country, vm.active FROM events AS vm WHERE vm.deleted = 0' . $FiltersSql. ' order by vm.id desc';

        // $sSQL = 'SELECT vm.id, vm.name, vm.start_time, vm.end_time,vm.created_by,
        //         o.user_id,(o.name) As organizer_name,(SELECT name FROM cities WHERE 
        //         Id = vm.city) AS city,(SELECT name FROM states WHERE Id = vm.state) AS state, 
        //         (SELECT name FROM countries WHERE Id = vm.country) AS country, 
        //         vm.active, 
        //         o.user_id
        //  FROM events AS vm
        //  LEFT JOIN organizer AS o ON o.user_id = vm.created_by
        //  WHERE vm.deleted = 0 ' . $FiltersSql . ' 
        //  ORDER BY vm.id DESC';

        $sSQL = 'SELECT vm.id, vm.name, vm.start_time, vm.end_time,vm.created_by,vm.banner_image,vm.event_info_status,
                (SELECT name FROM cities WHERE 
                Id = vm.city) AS city,(SELECT name FROM states WHERE Id = vm.state) AS state, 
                (SELECT name FROM countries WHERE Id = vm.country) AS country, 
                vm.active,vm.event_info_status
         FROM events AS vm
         WHERE vm.deleted = 0 ' . $FiltersSql . ' 
         ORDER BY vm.id ASC';

        if ($Limit > 0) {
            $sSQL .= ' LIMIT ' . $aReturn['Offset'] . ',' . $Limit;
        }
        //dd($sSQL );
        $aReturn['event_array'] = DB::select($sSQL, array());
        // dd($aReturn['event_array']);
        $sSQL = 'SELECT * FROM organizer';
         $aReturn['organizer'] = DB::select($sSQL, array());
         
        $aReturn['Paginator'] = new LengthAwarePaginator($aReturn['event_array'], $CountRows, $Limit, $PageNo);
        $aReturn['Paginator']->setPath(request()->url());
        //dd($aReturn);

        $sSQL = 'SELECT id, name FROM countries WHERE 1=1';
        $aReturn["countries"] = DB::select($sSQL, array());
        //return view('master_data.event.list', $aReturn);

        $SQL = "SELECT id,name FROM events WHERE active=1 AND deleted = 0";
        $aReturn['EventsData'] = DB::select($SQL, array());

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
            'SuccessMessage' => '',
            'banner_image' => '',
            'imagePath'=> '',
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
        if($id>0){
            $sql_r = 'SELECT Banner_image FROM events WHERE id = '.$id;
            $result = DB::select($sql_r,array());
            $aReturn = (array) $result[0];
           
            
        }
        // Validation Rules
        $rules = [
            'name' => 'required|unique:events,name,' . $id . 'id',
            'start_time' => 'required|date',
            'end_time' => 'required|date',
            'city' => 'required|string',
            'state' => 'required|string',
            'country' => 'required|string',
            'address' => 'required|string',
            // 'event_url' => 'required|string',
            'event_url' => [
                'required',
                'regex:/^(www\.|http:\/\/|https:\/\/).*/i', 
            ],
            'event_description' => 'required|string',
            'event_keywords' => 'required|string',
            'time_zone' => 'required|string',
            'event_banner_image' => empty($id) || $aReturn[ "Banner_image"] === ''  ? 'required|mimes:jpeg,jpg,png|max:5120' : 'mimes:jpeg,jpg,png||max:5120',

        ];

        $message = [ 
            'event_url.required' => 'The Event URL field is required.',
            'event_url.regex' => 'The Event URL must start with "www.", "http://", or "https://".',
            'event_banner_image.max' => 'The event banner image must be less than 5MB.',
        ]; 

        // if(!empty($request->event_communication_creatives)){
        //     $event_communication_Rules = [
        //         'event_communication_creatives' => 'mimes:jpeg,jpg,png',       
        //     ];
        //      // Merge base rules with GST rules
        //      $rules = array_merge($rules, $event_communication_Rules);
        // }

        // Form handling
        if (isset($request->form_type) && $request->form_type  == 'add_edit_event') {
            $validatedData = $request->validate($rules,$message);
          
           // $Category = isset($request->category_id) ? $request->category_id : [];
           // dd($request->event_communication_creatives);
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
            $description = !empty($request->event_description)? $request->event_description:'';
            $event_keywords = $request->event_keywords;
            $timezones = $request->time_zone;
           // $active = $request->input('active', 1);
            $Category = $request->input('category_id', []);
            // Update or insert based on $id
          
            if(!empty($request->file('event_banner_image'))){
                $banner_image = $this->uploadFile($request->file('event_banner_image'), public_path('uploads/banner_image'));
                $sSQL = 'UPDATE events SET banner_image = :banner_image WHERE id=:id';
                $Result = DB::update( $sSQL, array(
                        'banner_image' => $banner_image,
                        'id' => $id
                    )
                );
            } 

            if (!empty($request->file('event_communication_creatives'))) {
                // Get event_id (assuming you are updating images for a specific event)
                $event_id = $id;
                // Loop through each uploaded file
                foreach ($request->file('event_communication_creatives') as $file) {
                    // Upload the file and get the filename/path
                    $imagePath = $this->eventimageuploadFile($file, public_path('uploads/event_images'));    
                    
                    if (!empty($imagePath)) {
                        $user_id = Session::get('logged_in');
                        // Insert each uploaded image into the database
                        $sSQL = 'INSERT INTO event_images (event_id, image, created_by) VALUES (:event_id, :image, :created_by)';
                        $Result = DB::insert($sSQL, [
                            'event_id' => $event_id,
                            'image' => $imagePath,
                            'created_by' => $user_id['id']
                        ]); 
                    } 
                }  
            }
            
          
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

                $sSQL = 'SELECT id,image FROM event_images WHERE event_id=:event_id';
                //    $sSQL .= 'SELECT * FROM  event_category WHERE id =:id';
                $aReturn['event_images'] = DB::select($sSQL, array('event_id' => $id));
                //     dd($Materials);
               
                // $event_images = !empty($event_image[0]) ? $event_image[0] : '';
                // $aReturn['event_images'] = (array) $event_images;
                

            }
        }
   
        $aReturn['timezones_array'] = DB::table('master_timezones')->where('active', 1)->get();
        $sSQL = 'SELECT id, name FROM countries WHERE 1=1';
        $aReturn["countries"] = DB::select($sSQL, array());
        $aReturn['Category'] = $Category;
         
        // dd($aReturn['Category']);
        return view('master_data.event.create', $aReturn);

    }


    public function uploadFile($File, $Path){
        $ImageExtention =  $File->getClientOriginalExtension(); #get proper by code;
        $a_return['event_banner_image'] = strtotime('now').'.'.$ImageExtention;
        $File->move($Path, $a_return['event_banner_image']);
        return $a_return['event_banner_image'];
    }

    public function eventimageuploadFile($file, $destinationPath)
    {
        // Create a unique file name for the image
        $filename = time() . '-' . $file->getClientOriginalName();
        // Move the file to the specified path
        $file->move($destinationPath, $filename);
        // Return the relative path of the uploaded image
        return $filename;
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
        $successMessage  = 'Status changed successfully';
        $sucess = 'true';
        $result = [];
        $result['message'] =  $successMessage ;
        $result['sucess'] = $sucess;
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
        return redirect('/event')->with('success', 'Event deleted successfully');
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

    public function delete_event_image($event_id ,$iId){
        if (!empty($iId)) {
            $sSQL1 = 'SELECT id,image FROM event_images Where id = '.$iId;
            $Result = DB::select($sSQL1,array());

            if (!empty($Result)) {
                // Construct the full path to the image file
                $imagePath = public_path('uploads/event_images/' . $Result[0]->image); 
                // Delete the image file from the directory if it exists
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
    
                // Now delete the image record from the database
                $sSQL2 = 'DELETE FROM `event_images` WHERE id=:id';
                $Result = DB::delete($sSQL2,array( 'id' => $iId));
            }
        }        
        return redirect(url('/event/edit/'.$event_id));
    }
    public function upload(Request $request)
    {
        if ($request->hasFile('upload')) {
            $originName = $request->file('upload')->getClientOriginalName();
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $file = $request->file('upload');
            $extension = $file->getClientOriginalExtension();
            $fileName = $fileName . '_' . time() . '.' . $extension;
    
            $request->file('upload')->move(public_path('uploads/ckeditor_event_image/'), $fileName);     
            $url = asset('uploads/ckeditor_event_image/' . $fileName);
            $filePath = 'uploads/ckeditor_event_image/' ;
     
            return response()->json([
                'fileName' => $fileName, 
                'uploaded' => 1, 
                'url' => $url, 
                'filePath' => $filePath
            ]);
        }
    }
} 