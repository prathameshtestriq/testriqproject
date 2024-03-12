<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class TestimonialController extends Controller
{
    //
    public function clear_search()
    {
        session()->forget('user_id');
        return redirect('/testimonial');
    }

    public function index(Request $request)
    {
        //    dd($request->all());
        // dd('here');

        $aReturn = array();
        $aReturn['search_name'] = '';
        if (isset($request->form_type) && $request->form_type == 'search_testimonial') {
            // dd('here1');
            session(['user_id' => $request->user_id]);
            return redirect('/testimonial');
        }
        $aReturn['search_name'] = (!empty(session('user_id'))) ? session('user_id') : '';
        $FiltersSql = '';
        //  dd($aReturn['search_district_name']);

        // if (!empty($aReturn['search_name'])) {
        //     $FiltersSql .= '(SELECT firstname FROM users WHERE Id = name)As name'' AND (LOWER(name) LIKE \'%' . strtolower($aReturn['search_name']) . '%\')';
        // } else {
        //     $aReturn['search_name'] = '';
        // }
        if (!empty($aReturn['search_name'])) {
            $FiltersSql .= " AND (SELECT firstname FROM users WHERE Id = user_id) LIKE '%" . strtolower($aReturn['search_name']) . "%'";
        } else {
            $aReturn['search_name'] = '';
        }


        //  dd($FiltersSql);

        #PAGINATION
        $sSQL = 'SELECT count(id) as count FROM testimonial dm WHERE 1=1  ' . $FiltersSql;
        $CountsResult = DB::select($sSQL);
        //dd($CountsResult);
        $CountRows = 0;
        if (!empty($CountsResult)) {
            $CountRows = $CountsResult[0]->count;
        }
        $PageNo = request()->input('page', 1);
        $Limit = config('custom.per_page');
        // $Limit = 3;
        $aReturn['Offset'] = ($PageNo - 1) * $Limit;

        // $sSQL = 'SELECT *,(SELECT firstname FROM users WHERE Id = user_id) As name,(SELECT profile_pic FROM users WHERE Id = testimonial_img) As testimonial_img FROM testimonial WHERE 1=1' . $FiltersSql;
        $sSQL = 'SELECT *,(SELECT firstname FROM users WHERE Id = user_id) As name FROM testimonial WHERE 1=1' . $FiltersSql;



        $sSQL .= ' ORDER BY name ASC ';

        if ($Limit > 0) {
            $sSQL .= ' LIMIT ' . $aReturn['Offset'] . ',' . $Limit;
        }
        //dd($sSQL );
        $aReturn['testimonial_array'] = DB::select($sSQL, array());
        // dd($aReturn['testimonial_array']);
        $aReturn['Paginator'] = new LengthAwarePaginator($aReturn['testimonial_array'], $CountRows, $Limit, $PageNo);
        $aReturn['Paginator']->setPath(request()->url());
        //dd($aReturn);

        return view('master_data.testimonial.list', $aReturn);
    }

    public function add_edit(Request $request, $iId = 0)
    {
        // dd($request);

        $aReturn['user_id'] = '';
        $aReturn['subtitle'] = '';
        $aReturn['description'] = '';
        //  $aReturn['testimonial_img'] = '';
        $aReturn['active'] = '';
        $aReturn['rating'] = '';
        //  $SuccessMessage = ''; 
        //dd($request);
        if (isset($request->form_type) && $request->form_type == 'add_edit_testimonial') {
            // dd('ss');
            #VALIDATION RULES
            $Rules = [
                'user_id' => 'required|string',
                'subtitle' => 'required|string',
                'description' => 'required|string',
                'active' => 'required|string',
                'rating' => 'required|string',
            ];
            $user_id = (!empty($request->user_id)) ? $request->user_id : '';
            $subtitle = (!empty($request->subtitle)) ? $request->subtitle : '';
            $description = (!empty($request->description)) ? $request->description : '';
            // $testimonial_img = (!empty($request->testimonial_img)) ? $request->testimonial_img : '';
            // $hidden_testimonial_img = (!empty($request->testimonial_img)) ? $request->hidden_testimonial_img : '';
            $active = (!empty($request->active)) ? $request->active : '';
            $rating = (!empty($request->rating)) ? $request->rating : '';
            $image_name = '';
            //  $SuccessMessage = 'testimonial  updated successfully'; 
            if ($iId > 0) {
                //    dd($request->all());
                //  dd($image_name);
                if ($request->active == 'active') {
                    $active = 1;
                }
                if ($request->active == 'inactive') {
                    $active = 0;
                }
                #UPDATE
                $request->validate($Rules);

                // if ($request->hasFile('testimonial_img')) {
                //     // Upload the new image
                //     $image_name = $request->file('testimonial_img')->getClientOriginalName();
                //     $request->file('testimonial_img')->move(public_path('uploads/testimonial_images/'), $image_name);
                // } else {
                //     // If no new image is uploaded, retain the old image
                //     $image_name = $hidden_testimonial_img;
                // }

                //dd($image_name);
                // if (!empty($image_name)) {
                $sSQL = 'UPDATE testimonial SET user_id = :user_id,subtitle = :subtitle ,description = :description, active = :active , rating = :rating WHERE id = :id';
                // dd($sSQL);


                //dd($sSQL);
                $Bindings = array(
                    'user_id' => $user_id,
                    'subtitle' => $subtitle,
                    //  'testimonial_img' => $image_name,
                    'description' => $description,
                    'active' => $active,
                    'rating' => $rating,
                    'id' => $iId
                );
                //dd($Bindings);
                // $Result = DB::update($sSQL, $Bindings);


                //duplication
          //      $sSQL1 = "SELECT count(id) AS rec_count FROM testimonial WHERE LOWER(user_id) = '" . strtolower($user_id) . "' and id != " . $iId . " and 1 = 1 ";
                //   $sSQL1 = "SELECT count(id) AS rec_count FROM vehicle_master WHERE vehicle_name = '" . $vehicle_name . "' ";
                // dd( $sSQL1);
            //    $rResult = DB::select(($sSQL1));
                //  dd($rResult);
                // if (($rResult[0]->rec_count) == 1) {
                //     $SuccessMessage = 'name already exist';
                //     return redirect('/testimonial/add')->with('error', $SuccessMessage);
                // } else {
                    $Result = DB::update($sSQL, $Bindings);
                    $SuccessMessage = 'testimonial  updated successfully';
                // }
                //  }
                // dd($Result);
            } else {
                // dd('aaa');
                // dd($request->vehicle_image);

                // if ((!empty($request->testimonial_img))) {
                //     $image_name = $request->file('testimonial_img')->getClientOriginalName();
                //     //    dd($image_name);
                //     $file = $request->file('testimonial_img');
                //     $url = env('APP_URL');
                //     $final_url = $url . 'uploads/testimonial_images';
                //     $file->move(public_path('uploads/testimonial_images/'), $final_url . '/' . $image_name);
                // } else {
                //     $image_name = '';
                // }

                $request->validate($Rules);
                #ADD

                $sSQL = 'INSERT INTO testimonial(user_id,subtitle,description,active,rating) VALUES(:user_id,:subtitle,:description,:active,:rating)';
                // dd($sSQL);

                $Bindings = array(
                    'user_id' => $user_id,
                    'subtitle' => $subtitle,
                    //  'testimonial_img' => $image_name,
                    'description' => $description,
                    'active' => $active,
                    'rating' => $rating
                );
                //  dd($Bindings);

                //duplication
                // $sSQL1 = "SELECT count(id) AS rec_count FROM testimonial WHERE user_id = '" . $user_id . "' ";
                // $rResult = DB::select(($sSQL1));
                //    dd($rResult);

             //   if (($rResult[0]->rec_count) == 0) {
                    $Result = DB::insert($sSQL, $Bindings);
                    $SuccessMessage = 'testimonial added successfully';
                // } else {

                //     $SuccessMessage = 'testimonial already exist';
                //     return redirect('/testimonial/add')->with('error', $SuccessMessage);
                // }
            }
            return redirect('/testimonial')->with('success', $SuccessMessage);
        } else {


            //EDIT
            if ($iId > 0) {

                $sSQL = 'SELECT * FROM testimonial WHERE id=:id';
                $Materials = DB::select($sSQL, array('id' => $iId));
                // dd($Districts);
                $aReturn = (array) $Materials[0];
            }
        }

        $aSql = 'select id,firstname FROM users where is_deleted = 0';
        $aReturn['users_master_array'] = DB::select($aSql);
        //   dd($aReturn['users_master_array']);
        return view('master_data.testimonial.create', $aReturn);
    }

    public function remove_testimonial($iId)
    {
        if (!empty($iId)) {
            $sSQL = 'DELETE FROM testimonial WHERE id=:id';
            $Result = DB::update(
                $sSQL,
                array(
                    'id' => $iId
                )
            );
            // dd($Result);       
        }
        return redirect(url('/testimonial'))->with('success', 'testimonial deleted successfully');
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
        $sSQL = 'UPDATE testimonial SET active=:active WHERE id=:id';
        $Bindings = array(
            'active' => $request->active,
            'id' => $request->id
        );
        $result = DB::update($sSQL, $Bindings);
        return $result;
    }
}



