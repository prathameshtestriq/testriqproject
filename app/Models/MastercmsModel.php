<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class MastercmsModel extends Model
{
    use HasFactory;
    protected $table = 'CMS_master';

    public $timestamps = false;

    public static function get_count($a_search = array()){
        $count = 0;
        // dd($a_search);
        $s_sql = 'SELECT count(cm.id) as count FROM CMS_master cm WHERE 1=1';

        // if (!empty($a_search['search_remittance_name'])) {
        //     $s_sql .= ' AND LOWER(rm.remittance_name) LIKE \'%' . strtolower($a_search['search_remittance_name']) . '%\'';
        // }

       

        $CountsResult = DB::select($s_sql);
        if (!empty($CountsResult)) {
            $count = $CountsResult[0]->count;
        }
        // dd($count);
        return $count;
    }

    public static function get_all_master_cms($limit, $a_search = array()){
        $a_return = []; 

        $s_sql = 'SELECT * FROM CMS_master cm where 1=1';

        // if (!empty($a_search['search_remittance_name'])) {
        //     $s_sql .= ' AND LOWER(rm.remittance_name) LIKE \'%' . strtolower($a_search['search_remittance_name']) . '%\'';
        // }

      
     
        if ($limit > 0) {
            $s_sql .= ' LIMIT ' . $a_search['Offset'] . ',' . $limit;
        }

        $a_return = DB::select($s_sql);
        return $a_return;
    }

    public static function update_master_cms($iId, $request)
	{
       
        $ssql = 'UPDATE CMS_master SET 
        title = :title,
        description = :cms_description
        WHERE id=:id';

        $bindings = array(
            'title' => $request->title,
            'cms_description' => $request->cms_description,
            'id' => $iId
        );
        // dd($bindings);
        $Result = DB::update($ssql, $bindings);
	}

    public static function add_master_cms($request)
	{
       
        $ssql = 'INSERT INTO CMS_master(
            title,description)
                VALUES (
            :title,:description )';
        
        $bindings = array(
            'title' => $request->title,
            'description' => $request->cms_description
        );
    //  dd( $bindings);
        $Result = DB::insert($ssql,$bindings);
        
	}

    public static function delete_master_cms($iId)
    {
       
        if (!empty($iId)) {
            $sSQL = 'DELETE FROM `CMS_master` WHERE id=:id';
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

    public static function change_status_master_cms($request)
    {
        $sSQL = 'UPDATE CMS_master SET is_active=:active WHERE id=:id';
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
