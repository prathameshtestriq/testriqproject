<?php

namespace App\Http\Controllers;

use App\Exports\UserExport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class MaintenanceModeController extends Controller
{
    
    public function index_mode(Request $request){

        $sSQL = 'SELECT id,maintenance_mode FROM races_settings ';
        $aReturn["maintenance_mode_details"] = DB::select($sSQL, array());
        $aReturn["races_setting"] =  $aReturn["maintenance_mode_details"][0]->maintenance_mode;

      
        return view('races_setting.list',$aReturn);
    }

    public function change_active_status_mode(Request $request){
        $aReturn = User::change_status_mode($request);
        $successMessage  = 'Races Setting changed successfully';
        $sucess = 'true';
        $aReturn = [];
        $aReturn['message'] =  $successMessage ;
        $aReturn['sucess'] = $sucess;
        //dd($aReturn);
        return $aReturn;
    }
    
}
