<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\MasterRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RoleController extends Controller
{

    public function index($id)
    {
        //   dd($id);
        $role = MasterRole::findOrFail($id);
        $s_sql = 'SELECT id,module_name,status FROM module_master where is_deleted =0 ';
        $a_modules = DB::select($s_sql);
        // dd($a_modules);
        $r_sql = 'SELECT `id`, `role_id`, `module_id`, `access`, `updated_date` FROM `role_access` WHERE  role_id = :role_id ';

        $role_access = DB::select($r_sql, array('role_id' => $id));
        // dd($role_access);
        $access = array();
        foreach ($role_access as $val) {
            $access[$val->module_id] = $val->access;
        }
        foreach ($a_modules as $mod) {

            if (isset($access[$mod->id])) {
                $mod->access = $access[$mod->id];
            } else {
                $mod->access = 0;
            }
        }
        //   dd($a_modules);


        return view('role_master.index', ['role' => $role, 'modules' => $a_modules]);

    }
    public function update(Request $request, $id)
    {
        $accessLevels = $request->input('access', []);
        // dd($accessLevels);

        foreach ($accessLevels as $moduleId => $accessLevel) {
            $existing = DB::select('SELECT id FROM role_access WHERE role_id = :role_id AND module_id = :module_id', [
                'role_id' => $id,
                'module_id' => $moduleId
            ]);
            $user_details = Session::get('logged_in');
       //     dd($user_details['id']);
            // dd($existing);
            if ($existing) {
                DB::update('UPDATE role_access SET access = :access, updated_date = NOW() WHERE role_id = :role_id AND module_id = :module_id', [
                    'access' => $accessLevel,
                    'role_id' => $id,
                    'module_id' => $moduleId
                ]);
            } else {
                DB::insert('INSERT INTO role_access (role_id, module_id, access, updated_date, created_by, created_date) VALUES (:role_id, :module_id, :access, NOW(), :created_by, NOW())', [
                    'role_id' => $id,
                    'module_id' => $moduleId,
                    'access' => $accessLevel,
                    'created_by' => $user_details['id']
                ]);
            }
        }

        return redirect()->route('role_master_index')->with('success', 'Access levels updated successfully.');
    }
}




