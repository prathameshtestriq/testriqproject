<?php

namespace App\Http\Controllers;

use App\Models\MasterOrganiser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Pagination\LengthAwarePaginator;

class OrganiserController extends Controller
{
    // public function clear_search()
    // {
    //     session::forget('role_name');
    //     session::forget('role_status');
    //     return redirect('/role_master');
    // }

    public function clear_search()
    {
        session::forget('organiser_name');
        session::forget('gst_number');
        session::forget('organiser_user_name');
        return redirect('/organiser_master');
    }

    public function index(Request $request)
    {
        $a_return = array();
        $a_return['search_organiser_name'] = '';
        $a_return['search_gst_number'] = '';
        $a_return['search_organiser_user_name'] = '';

        if (isset($request->form_type) && $request->form_type == 'search_user') {
           
            session(['organiser_name' => $request->organiser_name]);
            session(['gst_number' => $request->gst_number]);
            session(['organiser_user_name' => $request->organiser_user_name]);
            
            return redirect('/organiser_master');
        }
        $a_return['search_organiser_name'] = (!empty(session('organiser_name'))) ? session('organiser_name') : '';
        $a_return['search_gst_number'] = (!empty(session('gst_number'))) ? session('gst_number') : '';
        $a_return['search_organiser_user_name'] = (!empty(session('organiser_user_name'))) ? session('organiser_user_name') : '';
 
       
        $CountRows = MasterOrganiser::get_count($a_return);
        // dd($CountRows);
        $PageNo = request()->input('page', 1);
        $Limit = config('custom.per_page');
        // $Limit = 3;
        $a_return['Offset'] = ($PageNo - 1) * $Limit;
         
       
        $a_return["OrganiserDetails"] = MasterOrganiser::get_all($Limit,$a_return);
        $s_sql = 'SELECT u.id, CONCAT(u.firstname, " ", u.lastname) as name, u.is_active FROM users u WHERE u.is_active = 1 GROUP BY u.firstname, u.lastname';
        $a_return["UserDetails"] = DB::select($s_sql,array());
        // dd($a_return["UserDetails"] );
    //  dd($a_return["Remittance"][0]);
        $a_return['Paginator'] = new LengthAwarePaginator( $a_return["OrganiserDetails"], $CountRows, $Limit, $PageNo);
        $a_return['Paginator']->setPath(request()->url());

        return view('organiser_master.list',$a_return);
    }

    public function add_edit(Request $request, $iId = 0){
        $a_return['name'] = '';
        $a_return['email'] = '';
        $a_return['mobile'] = '';
        $a_return['about'] = '';
        $a_return['contact_person'] = '';
        $a_return['contact_no'] = '';
        $a_return['gst'] = '';
        $a_return['gst_number'] = '';
        $a_return['gst_percentage'] = '';
        $a_return['logo_image'] = '';
        $a_return['banner_image'] = '';
        $a_return['company_pan'] = '';
        $a_return['gst_certificate'] = '';
        $a_return['banner_image'] = '';
        $a_return['company_pan'] = '';
        $a_return['gst_certificate'] = '';

        // dd($request->all()); 
        // dd($request->gst);
        // gst_number_format = 22ABCDE1234F1Z5 ;
        // dd(  $a_return['logo_image']);
        

        if (isset($request->form_type) && $request->form_type == 'add_edit_organiser') {
          
            $rules = [
                'organiser_name' => 'required|unique:organizer,name,'.$iId.',id',
                'email' => 'required|email:rfc,dns',
                'contact_number' => 'required|digits:10',
                'about' => 'required',
                'organiser_logo_image' => !empty($iId) ? '' : 'required|mimes:jpeg,jpg,png',
                //  |max:5120|dimensions:width=1920,height=744 
                // ,gif|dimensions:width=1920,height=744
            ];


         
            if(($request->gst) == 1){
                $gstRules = [
                    'gst_number' => 'required|regex:/^([0-9]{2})([A-Z]{5})([0-9]{4})([A-Z]{1})([A-Z0-9]{3})$/',
                    'contact_gst_percentage' => 'required|numeric',
                ];
                 // Merge base rules with GST rules
                 $rules = array_merge($rules, $gstRules);
            }
           
            $request->validate($rules);

            if($iId > 0){
                // dd("jdskfkdsj");
                MasterOrganiser::update_organiser($iId,$request);
                $OrganiserId = $iId;
                $successMessage = ' Master Organiser Details Updated Successfully';
               
            }else{
                MasterOrganiser::add_organiser($request);
                $OrganiserId = DB::getPdo()->lastInsertId(); #GET LAST INSERTED ID 
                $successMessage = 'Master Organiser Details Added Successfully';  
            }
            
            // $logo_image = !empty($request->organiser_logo_image) ? $request->organiser_logo_image : '';
            if(!empty($request->file('organiser_logo_image'))){
                $logo_image = $this->uploadFile($request->file('organiser_logo_image'), public_path('uploads/organiser/logo_image'));
                $sSQL = 'UPDATE organizer SET logo_image = :logo_image WHERE id=:id';
                $Result = DB::update( $sSQL, array(
                        'logo_image' => $logo_image,
                        'id' => $OrganiserId
                    )
                );
            
            } 

            // $banner_image = !empty(/$request->banner_image) ? $request->banner_image : '';
            if(!empty($request->file('banner_image'))){
                $banner_image = $this->uploadFile($request->file('banner_image'), public_path('uploads/organiser/banner_image'));
                $sSQL = 'UPDATE organizer SET banner_image = :banner_image WHERE id=:id';
                $Result = DB::update( $sSQL, array(
                        'banner_image' => $banner_image,
                        'id' => $OrganiserId
                    )
                );
            } 


            // $company_pan = !empty($request->registered_pancard) ? $request->registered_pancard : '';
            if(!empty($request->file('registered_pancard'))){
                $company_pan = $this->uploadFile($request->file('registered_pancard'), public_path('uploads/organiser/company_pancard'));
                $sSQL = 'UPDATE organizer SET company_pan = :company_pan WHERE id=:id';
                $Result = DB::update( $sSQL, array(
                        'company_pan' => $company_pan,
                        'id' => $OrganiserId
                    )
                );
            
            } 


            // $gst_certificate = !empty($request->organiser_logo_image) ? $request->organiser_logo_image : '';
            if(!empty($request->file('registered_gst_certificate'))){
                $gst_certificate = $this->uploadFile($request->file('registered_gst_certificate'), public_path('uploads/organiser/gst_certificate'));
                $sSQL = 'UPDATE organizer SET gst_certificate = :gst_certificate WHERE id=:id';
                $Result = DB::update( $sSQL, array(
                        'gst_certificate' => $gst_certificate,
                        'id' => $OrganiserId
                    )
                );
            } 
           
            return redirect('/organiser_master')->with('success', $successMessage);
        }else{
            if($iId > 0){
                // dd("here");
               //   #SHOW EXISTING DETAILS ON EDIT
              $sSQL = 'SELECT * FROM organizer WHERE id=:id';
              $organiser_details = DB::select($sSQL, array( 'id' => $iId));
              $a_return = (array)$organiser_details[0];
            
            }
          
        }  
        return view('organiser_master.create', $a_return);
    }

    public function uploadFile($File, $Path){
        $ImageExtention =  $File->getClientOriginalExtension(); #get proper by code;
        $a_return['logo_image'] = strtotime('now').'.'.$ImageExtention;
        $File->move($Path, $a_return['logo_image']);
        return $a_return['logo_image'];

        
        $a_return['banner_image'] = strtotime('now').'.'.$ImageExtention;
        $File->move($Path, $a_return['banner_image']);
        return $a_return['banner_image'];

        $a_return['company_pan'] = strtotime('now').'.'.$ImageExtention;
        $File->move($Path, $a_return['company_pan']);
        return $a_return['company_pan'];

        $a_return['gst_certificate'] = strtotime('now').'.'.$ImageExtention;
        $File->move($Path, $a_return['gst_certificate']);
        return $a_return['gst_certificate'];
    }

    public function delete_organiser($iId)
    {
        MasterOrganiser::delete_organiser($iId);
        return redirect(url('/organiser_master'))->with('success', 'Organiser Deleted Successfully');
    }

   
    
}
