<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class RemittanceManagement extends Model
{
    use HasFactory;
    protected $table = 'remittance_management';
 
    protected $fillable = [
        'remittance_name',
        'remittance_date',
        'gross_amount',
        'service_charge',
        'Sgst  ',
        'Cgst',
        'Igst',
        'deductions',
        'Tds',
        'amount_remitted',
        'bank_reference',
       
    ];
    public $timestamps = false;

    public static function get_count($a_search = array()){
        $count = 0;
        // dd($a_search);
        $s_sql = 'SELECT count(rm.id) as count FROM remittance_management rm WHERE 1=1';

        if (!empty($a_search['search_remittance_name'])) {
            $s_sql .= ' AND LOWER(rm.remittance_name) LIKE \'%' . strtolower($a_search['search_remittance_name']) . '%\'';
        }

        if(!empty($a_search['search_start_remittance_date'])){
            $startdate = strtotime($a_search['search_start_remittance_date']);    
            $s_sql .= " AND rm.remittance_date >= "." $startdate";
            // dd($sSQL);
        }

        if(!empty($a_search['search_end_remittance_date'])){
            $endDate = strtotime($a_search['search_end_remittance_date']);
            $s_sql .= " AND  rm.remittance_date <="." $endDate";
            // dd($sSQL);
        } 

       
        if(isset( $a_search['search_remittance_status']) &&  $a_search['search_remittance_status'] != ''){
            $s_sql .= ' AND rm.active = '.$a_search['search_remittance_status'];
        } 

        if(isset( $a_search['search_event_id'])){
            $s_sql .= ' AND (LOWER(rm.event_id) LIKE \'%' . strtolower($a_search['search_event_id']) . '%\')';
        } 

        $CountsResult = DB::select($s_sql);
        if (!empty($CountsResult)) {
            $count = $CountsResult[0]->count;
        }
        // dd($count);
        return $count;
    }

    public static function get_all_remittance($limit, $a_search = array()){
        $a_return = []; 

        $s_sql = 'SELECT *,(SELECT name FROM events as e where e.id =rm.event_id ) AS event_name FROM remittance_management rm where 1=1';

        if (!empty($a_search['search_remittance_name'])) {
            $s_sql .= ' AND LOWER(rm.remittance_name) LIKE \'%' . strtolower($a_search['search_remittance_name']) . '%\'';
        }

        if(!empty($a_search['search_start_remittance_date'])){
            $startdate = strtotime($a_search['search_start_remittance_date']);    
            $s_sql .= " AND rm.remittance_date >= "." $startdate";
            // dd($sSQL);
        }

        if(!empty($a_search['search_end_remittance_date'])){
            $endDate = strtotime($a_search['search_end_remittance_date']);
            $s_sql .= " AND  rm.remittance_date <="." $endDate";
            // dd($sSQL);
        } 

        if(isset( $a_search['search_remittance_status']) &&  $a_search['search_remittance_status'] != ''){
            $s_sql .= ' AND rm.active = '.$a_search['search_remittance_status'];
        } 

        if(isset( $a_search['search_event_id'])){
            $s_sql .= ' AND (LOWER(rm.event_id) LIKE \'%' . strtolower($a_search['search_event_id']) . '%\')';
        } 
     
        if ($limit > 0) {
            $s_sql .= ' LIMIT ' . $a_search['Offset'] . ',' . $limit;
        }

        $a_return = DB::select($s_sql);
        return $a_return;
    }

    public static function update_remittance_management($iId, $request)
	{
       
        $ssql = 'UPDATE remittance_management SET 
        remittance_name = :remittance_name,
        remittance_date = :remittance_date, 
        event_id = :event_id,
        gross_amount = :gross_amount, 
        service_charge = :service_charge, 
        Sgst = :Sgst, 
        Cgst = :Cgst,
        Igst = :Igst,
        deductions = :deductions,
        Tds = :Tds,
        amount_remitted = :amount_remitted,
        bank_reference = :bank_reference
        WHERE id=:id';

        $bindings = array(
            'remittance_name' => $request->remittance_name,
            'remittance_date' => strtotime($request->remittance_date),
            'event_id' => $request->event,
            'gross_amount' => $request->gross_amount,
            'service_charge' => $request->service_charge,
            'Sgst' => $request->Sgst,
            'Cgst' => $request->Cgst,
            'Igst' => $request->Igst,
            'deductions' => $request->deductions,
            'Tds' => $request->Tds,
            'amount_remitted' => $request->amount_remitted,
            'bank_reference' => $request->bank_reference,
            'id' => $iId
        );
        // dd($bindings);
        $Result = DB::update($ssql, $bindings);
	}

    public static function add_remittance_management($request)
	{
       
        $ssql = 'INSERT INTO remittance_management(
            remittance_name,remittance_date,event_id,gross_amount,service_charge,
            Sgst,Cgst,Igst,deductions,Tds,amount_remitted,bank_reference)
                VALUES (
            :remittance_name,:remittance_date,:event_id,:gross_amount,:service_charge,
            :Sgst,:Cgst,:Igst,:deductions,:Tds,:amount_remitted,:bank_reference
            )';
        
        $bindings = array(
            'remittance_name' => $request->remittance_name,
            'remittance_date' => strtotime($request->remittance_date),
            'event_id' => $request->event,
            'gross_amount' => $request->gross_amount,
            'service_charge' => $request->service_charge,
            'Sgst' => $request->Sgst,
            'Cgst' => $request->Cgst,
            'Igst' => $request->Igst,
            'deductions' => $request->deductions,
            'Tds' => $request->Tds,
            'amount_remitted' => $request->amount_remitted,
            'bank_reference' => $request->bank_reference
           
        );
     
        $Result = DB::insert($ssql,$bindings);
        
	}

    public static function delete_remittance_management($iId)
    {
       
        if (!empty($iId)) {
            $sSQL = 'DELETE FROM `remittance_management` WHERE id=:id';
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

    public static function change_status_remittance_management($request)
    {
        $sSQL = 'UPDATE remittance_management SET active=:active WHERE id=:id';
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
