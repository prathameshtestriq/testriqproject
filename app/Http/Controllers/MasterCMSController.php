<?php

namespace App\Http\Controllers;

use App\Models\MastercmsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Pagination\LengthAwarePaginator;

class MasterCMSController extends Controller
{
   
    public function index(Request $request){
        $a_return = array();

        $CountRows = MastercmsModel::get_count($a_return);
        // dd($CountRows);
        $PageNo = request()->input('page', 1);
        $Limit = config('custom.per_page');
        // $Limit = 3;
        $a_return['Offset'] = ($PageNo - 1) * $Limit;
        
        
        $a_return["master_cms"] = MastercmsModel::get_all_master_cms($Limit,$a_return);
      
        $a_return['Paginator'] = new LengthAwarePaginator($a_return['master_cms'], $CountRows, $Limit, $PageNo);
        $a_return['Paginator']->setPath(request()->url());

        return view('master_cms.list',$a_return);
    }

    public function add_edit(Request $request,$iId=0){
        $a_return['title'] = '';
        $a_return['description'] = '';

        if (isset($request->form_type) && $request->form_type == 'add_edit_master_cms') {
            $rules = [
                'title' => 'required',
                'cms_description' => 'required', 
            ];
            $request->validate($rules);

            if ($iId > 0) {
                MastercmsModel::update_master_cms($iId, $request);
                $successMessage = ' Master CMS Details Updated Successfully';
            }else{
              
                MastercmsModel::add_master_cms($request);
                $successMessage = 'Master CMS Details Added Successfully';
            }
            return redirect('/master_cms')->with('success', $successMessage);

        }else{
            if($iId > 0){
            //   #SHOW EXISTING DETAILS ON EDIT
              $sSQL = 'SELECT * FROM CMS_master WHERE id=:id';
              $master_cms_details = DB::select($sSQL, array( 'id' => $iId));
              $a_return = (array)$master_cms_details[0];
            }
        }        
        return view('master_cms.create', $a_return);
        
    }

    public function delete_master_cms($iId){
        MastercmsModel::delete_master_cms($iId);
        return redirect(url('/master_cms'))->with('success', 'Master CMS deleted successfully');
    }

    public function change_active_status(Request $request)
    {
        $aReturn = MastercmsModel::change_status_master_cms($request);
        // dd($aReturn);

        $successMessage  = 'Status changed successfully';
        $sucess = 'true';
        $aReturn = [];
        $aReturn['message'] =  $successMessage ;
        $aReturn['sucess'] = $sucess;
        return $aReturn;
    }

    public function upload(Request $request)
    {
        if ($request->hasFile('upload')) {
            $originName = $request->file('upload')->getClientOriginalName();
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $file = $request->file('upload');
            $extension = $file->getClientOriginalExtension();
            $fileName = $fileName . '_' . time() . '.' . $extension;
    
            $request->file('upload')->move(public_path('uploads/cms_master_ckeditor_image/'), $fileName);     
            $url = asset('uploads/cms_master_ckeditor_image/' . $fileName);
            $filePath = 'uploads/cms_master_ckeditor_image/' ;
     
            return response()->json([
                'fileName' => $fileName, 
                'uploaded' => 1, 
                'url' => $url, 
                'filePath' => $filePath
            ]);
        }
    }
}
