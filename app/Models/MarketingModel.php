<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class MarketingModel extends Model
{
    use HasFactory;
    protected $table = 'Marketing';
 
    protected $fillable = [
        'campaign_name',
        'count',
        'start_date',
        'end_date',
       
    ];
    public $timestamps = false;

    public static function get_count($a_search = array()){
        $count = 0;
        // dd($a_search);
        $s_sql = 'SELECT count(m.id) as count FROM Marketing m WHERE 1=1';

        if (!empty($a_search['search_campaign_name'])) {
            $s_sql .= ' AND LOWER(m.campaign_name) LIKE \'%' . strtolower($a_search['search_campaign_name']) . '%\'';
        }

        if(!empty($a_search['search_start_marketing_date'])){
            $startdate = strtotime($a_search['search_start_marketing_date']);    
            $s_sql .= " AND m.start_date >= "." $startdate";
            // dd($sSQL);
        }

        if(!empty($a_search['search_end_marketing_date'])){
            $endDate = strtotime($a_search['search_end_marketing_date']);
            $s_sql .= " AND  m.end_date <="." $endDate";
            // dd($sSQL);
        } 

        if(isset( $a_search['search_marketing_status'])){
            $s_sql .= ' AND (LOWER(m.status) LIKE \'%' . strtolower($a_search['search_marketing_status']) . '%\')';
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

        $s_sql = 'SELECT * FROM Marketing m where 1=1';

        if (!empty($a_search['search_campaign_name'])) {
            $s_sql .= ' AND LOWER(m.campaign_name) LIKE \'%' . strtolower($a_search['search_campaign_name']) . '%\'';
        }

        if(!empty($a_search['search_start_marketing_date'])){
            $startdate = strtotime($a_search['search_start_marketing_date']);    
            $s_sql .= " AND m.start_date >= "." $startdate";
            // dd($sSQL);
        }

        if(!empty($a_search['search_end_marketing_date'])){
            $endDate = strtotime($a_search['search_end_marketing_date']);
            $s_sql .= " AND  m.end_date <="." $endDate";
            // dd($sSQL);
        } 

        if(isset( $a_search['search_marketing_status'])){
            $s_sql .= ' AND (LOWER(m.status) LIKE \'%' . strtolower($a_search['search_marketing_status']) . '%\')';
        } 
     
        if ($limit > 0) {
            $s_sql .= ' LIMIT ' . $a_search['Offset'] . ',' . $limit;
        }

        $a_return = DB::select($s_sql);
        return $a_return;
    }

    public static function update_marketing($iId, $request)
	{
       
        $ssql = 'UPDATE Marketing SET 
        campaign_name = :campaign_name,
        count =:count,
        start_date = :start_date, 
        end_date = :end_date
        WHERE id=:id';

        $bindings = array(
            'campaign_name' => $request->campaign_name,
            'count' => $request->count,
            'start_date' => strtotime($request->start_date),
            'end_date' => strtotime($request->end_date),
            'id' => $iId
        );
        // dd($bindings);
        $Result = DB::update($ssql, $bindings);
	}

    public static function add_marketing($request)
	{
       
        $ssql = 'INSERT INTO Marketing(
            campaign_name,count,start_date,end_date)
                VALUES (
            :campaign_name,:count,:start_date,:end_date
            )';
        
        $bindings = array(
            'campaign_name' => $request->campaign_name,
            'count' => $request->count,
            'start_date' => strtotime($request->start_date),
            'end_date' => strtotime($request->end_date) 
        );
     
        $Result = DB::insert($ssql,$bindings);
        
	}

    public static function delete_marketing($iId)
    {
       
        if (!empty($iId)) {
            $sSQL = 'DELETE FROM `Marketing` WHERE id=:id';
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

    public static function change_status_marketing($request)
    {
        $sSQL = 'UPDATE Marketing SET status=:active WHERE id=:id';
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
