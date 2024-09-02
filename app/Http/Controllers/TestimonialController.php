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
        session()->forget('subtitle');
        session()->forget('testimonial_status');
        return redirect('/testimonial');
    }

    public function index(Request $request)
    {
        //    dd($request->all());
        // dd('here');

        $aReturn = array();
        $aReturn['search_name'] = '';
        $aReturn['search_subtitle'] = '';
        $aReturn['search_testimonial_status'] = '';
        if (isset($request->form_type) && $request->form_type == 'search_testimonial') {
            // dd('here1');
            session(['user_id' => $request->user_id]);
            session(['subtitle' => $request->subtitle]);
            session(['testimonial_status' => $request->testimonial_status]);
            return redirect('/testimonial');
        }
        $aReturn['search_name'] = (!empty(session('user_id'))) ? session('user_id') : '';
        $aReturn['search_subtitle'] = (!empty(session('subtitle'))) ? session('subtitle') : '';
        $testimonial_status = session('testimonial_status');
        $aReturn['search_testimonial_status'] = (isset($testimonial_status) && $testimonial_status != '') ? $testimonial_status : '';
        $FiltersSql = '';
        //  dd($aReturn['search_subtitle']);

        // if (!empty($aReturn['search_name'])) {
        //     $FiltersSql .= '(SELECT firstname FROM users WHERE Id = name)As name'' AND (LOWER(name) LIKE \'%' . strtolower($aReturn['search_name']) . '%\')';
        // } else {
        //     $aReturn['search_name'] = '';
        // }
        if (!empty($aReturn['search_name'])) {
            $FiltersSql .= " AND name LIKE '%" . strtolower($aReturn['search_name']) . "%'";
        } else {
            $aReturn['search_name'] = '';
        }

        if (!empty($aReturn['search_subtitle'])) {
            $FiltersSql .= " AND subtitle LIKE '%" . strtolower($aReturn['search_subtitle']) . "%'";
        } else {
            $aReturn['search_subtitle'] = '';
        }

        if(isset( $aReturn['search_testimonial_status'])){
            $FiltersSql .= ' AND (LOWER(active) LIKE \'%' . strtolower($aReturn['search_testimonial_status']) . '%\')';
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
        $sSQL = 'SELECT * FROM testimonial WHERE 1=1' . $FiltersSql;
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
        $aReturn['testimonial_name'] = '';
        $aReturn['subtitle']         = '';
        $aReturn['description']      = '';
        $aReturn['testimonial_img']  = '';
        //  $SuccessMessage = ''; 
        //dd($request);
        if (isset($request->form_type) && $request->form_type == 'add_edit_testimonial') {
            // dd('ss');
            #VALIDATION RULES
            $Rules = [
                'testimonial_name' => 'required|unique:testimonial,name,' . $iId . 'id',
                'subtitle'         => 'required|string',
                'description'      => 'required|string',
                'testimonial_img'  => 'required|mimes:jpeg,jpg,png,gif|max:2000'
            ]; 
            $testimonial_name = (!empty($request->testimonial_name)) ? $request->testimonial_name : '';
            $subtitle = (!empty($request->subtitle)) ? $request->subtitle : '';
            $description = (!empty($request->description)) ? strip_tags($request->description) : '';
            $testimonial_img = (!empty($request->testimonial_img)) ? $request->testimonial_img : '';
            $hidden_testimonial_img = (!empty($request->testimonial_img)) ? $request->hidden_testimonial_img : '';
            $image_name = '';
            //  $SuccessMessage = 'testimonial  updated successfully'; 
            if ($iId > 0) {
               
                #UPDATE
                $request->validate($Rules);

                if ($request->file('testimonial_img')) {
                   $path = public_path('uploads/testimonial_images/');
                   $img_file = $request->file('testimonial_img');
                   $img_extension = $img_file->getClientOriginalExtension();
                   $img_name = strtotime('now').'.'.$img_extension;
                   
                   $img_file->move($path, $img_name);
                }

                $sSQL = 'UPDATE testimonial SET name = :name, subtitle = :subtitle ,description = :description ';
                // dd($sSQL);

                $Bindings = array(
                    'name'     => $testimonial_name,
                    'subtitle' => $subtitle,
                    'description' => $description,
                    'id' => $iId
                );

                if (!empty($testimonial_img)) {
                   $sSQL .= ', testimonial_img = :testimonial_img';
                   $Bindings['testimonial_img'] = $img_name;
                }

                $sSQL .= ' WHERE id = :id';
                $Result = DB::update($sSQL, $Bindings);
                $SuccessMessage = 'testimonial updated successfully';
                
            } else {
               
                $request->validate($Rules);

                if ($request->file('testimonial_img')) {
                   $path = public_path('uploads/testimonial_images/');
                   $img_file = $request->file('testimonial_img');
                   $img_extension = $img_file->getClientOriginalExtension();
                   $img_name = strtotime('now').'.'.$img_extension;
                   
                   $img_file->move($path, $img_name);
                }
               
                $sSQL = 'INSERT INTO testimonial(name,subtitle,description,testimonial_img) VALUES(:name,:subtitle,:description,:testimonial_img)';
                // dd($sSQL);

                $Bindings = array(
                    'name'     => $testimonial_name,
                    'subtitle' => $subtitle,
                    'description' => $description,
                    'testimonial_img' => $img_name
                );

                $Result = DB::insert($sSQL, $Bindings);
                $SuccessMessage = 'testimonial added successfully';
               
            }
            return redirect('/testimonial')->with('success', $SuccessMessage);
        } else {


            //EDIT
            if ($iId > 0) {

                $sSQL = 'SELECT id,name as testimonial_name,testimonial_img,subtitle,description,active FROM testimonial WHERE id=:id';
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



