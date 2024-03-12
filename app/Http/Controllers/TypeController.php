<?php

namespace App\Http\Controllers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

class TypeController extends Controller
{
    //
    public function clear_search()
    {
        session()->forget('name');
        return redirect('/type');
    }

    public function index(Request $request)
    {
        //   dd($request->all());
        // dd('here');

        $aReturn = array();
        $aReturn['search_name'] = '';
        if (isset($request->form_type) && $request->form_type == 'search_type') {
            // dd('here1');
            session(['name' => $request->name]);
            return redirect('/type');
        }
        $aReturn['search_name'] = (!empty(session('name'))) ? session('name') : '';
        $FiltersSql = '';
        //  dd($aReturn['search_district_name']);

        if (!empty($aReturn['search_name'])) {
            $FiltersSql .= ' AND (LOWER(name) LIKE \'%' . strtolower($aReturn['search_name']) . '%\')';
        } else {
            $aReturn['search_name'] = '';
        }
        //     if (!empty($aReturn['search_name'])) {
        //   //      $FiltersSql .= ' AND (LOWER(vm.name) LIKE \'%' . strtolower($aReturn['search_name']) . '%\')';
        //     } else {
        //         $aReturn['search_name'] = '';
        //     }


        //  dd($FiltersSql);
        #PAGINATION
        $sSQL = 'SELECT count(vm.id) as count FROM eTypes as vm WHERE 1=1  ' . $FiltersSql;
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


        // $sSQL = 'SELECT *,(SELECT firstname FROM users WHERE Id = user_id) As name,(SELECT profile_pic FROM users WHERE Id = testimonial_img) As testimonial_img FROM testimonial WHERE 1=1' . $FiltersSql;
        $sSQL = 'SELECT * FROM eTypes WHERE 1=1' . $FiltersSql;



        $sSQL .= ' ORDER BY name ASC ';

        if ($Limit > 0) {
            $sSQL .= ' LIMIT ' . $aReturn['Offset'] . ',' . $Limit;
        }
        //dd($sSQL );
        $aReturn['type_array'] = DB::select($sSQL, array());
        // dd($aReturn['testimonial_array']);
        $aReturn['Paginator'] = new LengthAwarePaginator($aReturn['type_array'], $CountRows, $Limit, $PageNo);
        $aReturn['Paginator']->setPath(request()->url());
        //dd($aReturn);

        return view('master_data.type.list', $aReturn);
    }
    public function add_edit(Request $request, $iId = 0)
    {
        // Initialize return data
        $aReturn['name'] = '';
        $aReturn['active'] = '';
        $aReturn['logo'] = '';
        $aReturn['type'] = '';
    
        // Fetch all types available
       // Fetch all types available
$allTypes = DB::select('SELECT *, (SELECT name FROM eTypes WHERE type_id = id) AS type_name FROM event_type WHERE 1=1');

// Initialize an associative array to keep track of selected types
$selectedTypes = [];

foreach ($allTypes as $type) {
    $result = DB::select("SELECT * FROM event_type WHERE type_id = ? AND event_id = ?", [$type->type_id, $iId]);
    $isSelected = sizeof($result) > 0 ? 'selected' :'';
   // $aReturn['allTypes'] = $allTypes;
    // If the type is selected, add it to the selectedTypes array
        $selectedTypes[$type->type_id] = $type;
   

    $type->selected = $isSelected;
}
//dd($selectedTypes);
// Remove duplicate selected types
$allTypes = array_diff_key( $selectedTypes);
//dd($allTypes);
$aReturn['allTypes'] = $selectedTypes; // Pass $allTypes to the view

//dd($aReturn['allTypes']);
    
        if (isset($request->form_type) && $request->form_type == 'add_edit_type') {
            // Validation rules
           // dd($request->all());
            $Rules = [
                'name' => 'required|string',
                'active' => 'required|string',
            ];
    
            // Retrieve data from request
            $name = $request->input('name', '');
            $active = $request->input('active', 1);
            $typeIds = $request->input('type_id', []);

         if($iId>0){
            if ($request->active == 'active') {
                $active = 1;
            }
            if ($request->active == 'inactive') {
                $active = 0;
            }
            // Determine if image update is required
            $updateImage = $request->hasFile('logo');
    
            // Handle image update if necessary
            $image_name = '';
            if ($updateImage) {
                $image_name = $request->file('logo')->getClientOriginalName();
                $request->file('logo')->move(public_path('uploads/type_images/'), $image_name);
            }
    
            // Update record
            $Bindings = [
                'name' => $name,
                'active' => $active,
                'id' => $iId
            ];
            $sSQL = 'UPDATE eTypes SET name = :name, active = :active';
            if ($updateImage) {
                $sSQL .= ', logo = :logo';
                $Bindings['logo'] = $image_name;
            }
            $sSQL .= ' WHERE id = :id';
    
            $Result = DB::update($sSQL, $Bindings);
    
            // Handle success or failure
            if ($Result !== false) {
                $successMessage = 'Type updated successfully';

                // Update event categories
                DB::delete('DELETE FROM event_type WHERE event_id = ?', [$iId]); // Clear existing type entries

                // Insert new type entries
                foreach ($typeIds as $typeId) {
                    DB::insert('INSERT INTO event_type (event_id, type_id) VALUES (?, ?)', [$iId, $typeId]);
                }

                return redirect('/type')->with('success', $successMessage);
            } else {
                $errorMessage = 'Failed to update type';
                return redirect('/type/add')->with('error', $errorMessage);
            }
    
    
             
                               
            } else {
                // dd('aaa');
                // dd($request->vehicle_image);
                //      dd($request->all());
                if ((!empty($request->logo))) {
                    $image_name = $request->file('logo')->getClientOriginalName();
                    //    dd($image_name);
                    $file = $request->file('logo');
                    $url = env('APP_URL');
                    $final_url = $url . 'uploads/type_images';
                    $file->move(public_path('uploads/type_images/'), $final_url . '/' . $image_name);
                } else {
                    $image_name = '';
                }

                $request->validate($Rules);
                // New event insert logic
                $sql = 'INSERT INTO eTypes (name, logo, active) VALUES (:name, :logo, :active)';
                $bindings = [
                    'name' => $name,
                    'logo' => $image_name,
                    'active' => $active,

                ];
                $successMessage = 'type added successfully';

                // Execute query
                DB::insert($sql, $bindings);
               //  dd($request->all());
                //   dd('here');
                // Insert type-event relationships
                // $typeEventInsertData = [];
                // foreach ($request->type_id as $typeId) {
                //     $typeEventInsertData[] = [
                //         'event_id' => $typeId,
                //         'type_id' => $typeId,
                //         'created_by' => $iId, // Assuming you're using authentication
                //     ];
                //     dd( $typeEventInsertData);
                // }
                $EventId = DB::getPdo()->lastInsertId();
                if (!empty($type) && !empty($EventId)) {
                    foreach ($type as $value) {
                        //    dd($value);

                        $sql = "INSERT INTO event_type (event_id, type_id,created_by) VALUES(:event_id,:type_id,:created_by)";
                        //  dd($sql);
                        $Bind = array(
                            "event_id" => $EventId,
                            "type_id" => $value,
                            "created_by" => $iId
                        );
                        //     dd($Bind);
                        DB::insert($sql, $Bind);
                    }

                }


                return redirect('/type')->with('success', 'Type added successfully');
            }
        } else {

            //EDIT
            if ($iId > 0) {
                $sSQL = 'SELECT * FROM eTypes WHERE id=:id';
                $Materials = DB::select($sSQL, array('id' => $iId));
                $aReturn = (array) $Materials[0];
            }
        }
        $aReturn['allTypes'] = $allTypes; // Pass $allTypes to the view
    //    dd(     $aReturn['allTypes']);
        return view('master_data.type.create', $aReturn);
    }
   

    public function remove_type($iId)
    {
        if (!empty($iId)) {
            $sSQL = 'DELETE FROM eTypes WHERE id=:id';
            $Result = DB::update(
                $sSQL,
                array(
                    'id' => $iId
                )
            );
            // dd($Result);       
        }
        return redirect(url('/type'))->with('success', 'type deleted successfully');
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
        $sSQL = 'UPDATE eTypes SET active=:active WHERE id=:id';
        $Bindings = array(
            'active' => $request->active,
            'id' => $request->id
        );
        $result = DB::update($sSQL, $Bindings);
        return $result;
    }
}
