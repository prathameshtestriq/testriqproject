<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class MasterRole extends Model
{
    use HasFactory;
    protected $table = 'role_master';
 
    protected $fillable = [
        'name'
    ];
    public $timestamps = false;

    public static function get_count($a_search = array()){
        $count = 0;
        // dd($a_search);
        $s_sql = 'SELECT count(rm.id) as count FROM role_master rm WHERE is_deleted = 0';

        if (!empty($a_search['search_role_name'])) {
            $s_sql .= ' AND LOWER(rm.name) LIKE \'%' . strtolower($a_search['search_role_name']) . '%\'';
        }

        if(isset( $a_search['search_role_status'])){
            $s_sql .= ' AND (LOWER(rm.status) LIKE \'%' . strtolower($a_search['search_role_status']) . '%\')';
        } 

        $CountsResult = DB::select($s_sql);
        if (!empty($CountsResult)) {
            $count = $CountsResult[0]->count;
        }
        // dd($count);
        return $count;
    }

    public static function get_all($limit, $a_search = array()){
        $a_return = [];

        $s_sql = 'SELECT * FROM role_master rm where is_deleted = 0';

        if (!empty($a_search['search_role_name'])) {
            $s_sql .= ' AND LOWER(rm.name) LIKE \'%' . strtolower($a_search['search_role_name']) . '%\'';
        }

        if(isset( $a_search['search_role_status'])){
            $s_sql .= ' AND (LOWER(rm.status) LIKE \'%' . strtolower($a_search['search_role_status']) . '%\')';
        } 
     
        if ($limit > 0) {
            $s_sql .= ' LIMIT ' . $a_search['Offset'] . ',' . $limit;
        }

        $a_return = DB::select($s_sql);
        return $a_return;
    }

    public static function update_role_master($iId, $request)
	{
       
        $ssql = 'UPDATE role_master SET 
        name = :name
        WHERE id = :id';

        $bindings = array(
            'name' => $request->role_name,
            'id' => $iId
        );
        // dd($bindings);
        $Result = DB::update($ssql, $bindings);
	}

    public static function add_role_master($request)
	{
       
        $ssql = 'INSERT INTO role_master(name)
                VALUES ( :name )';
        
        $bindings = array(
            'name' => $request->role_name,
        );
     
        $Result = DB::insert($ssql,$bindings);
        
	}

    public static function delete_role_master($iId)
    {
       
        if (!empty($iId)) {
            $sSQL = 'UPDATE role_master SET is_deleted = 1 WHERE id=:id';
            $Result = DB::delete(
                $sSQL,
                array(
                    'id' => $iId
                )
            );
            // dd($Result);
        }
        return $Result;
        
    }

    public static function change_status_Role($request)
    {
        $sSQL = 'UPDATE role_master SET status=:active WHERE id=:id';
        $aReturn = DB::update(
            $sSQL,
            array(
                'active' => $request->status,
                'id' => $request->id
            )
        );
        return $aReturn;
    }

   
}
