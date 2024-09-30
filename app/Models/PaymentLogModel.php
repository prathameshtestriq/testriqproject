<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PaymentLogModel extends Model
{
    use HasFactory;
    protected $table = 'booking_payment_log';

    protected $fillable = [

    ];
    public $timestamps = false;


    public static function get_count($a_search = array()){
        $count = 0;
        // dd($a_search);
        $s_sql = "SELECT count(p.id) AS count,p.txnid,p.amount,p.post_data,p.payment_status,p.created_datetime,u.id AS userId,u.firstname,u.lastname,u.email,u.mobile 
        FROM booking_payment_details AS p 
        LEFT JOIN users AS u ON u.id=p.created_by
        WHERE 1=1";

        if(!empty( $a_search['search_user_name'])){
            $s_sql .= ' AND (LOWER((CONCAT(u.firstname, " ", u.lastname))) LIKE \'%' . strtolower($a_search['search_user_name']) . '%\')';
        } 

        if(!empty($a_search['search_start_payment_date'])){
            $startdate = strtotime($a_search['search_start_payment_date']);    
            $s_sql .= " AND p.created_datetime >= "." $startdate";
            // dd($sSQL);
        }

        if(!empty($a_search['search_end_payment_date'])){
            $endDate = strtotime($a_search['search_end_payment_date']);
            $s_sql .= " AND  p.created_datetime <="." $endDate";
            // dd($sSQL);
        } 

        if (!empty($a_search['search_transaction_id_payment'])) {
            $s_sql .= ' AND LOWER( p.txnid) LIKE \'%' . strtolower($a_search['search_transaction_id_payment']) . '%\'';
        }
     
        if(isset( $a_search['search_transaction_status_payment']) &&  $a_search['search_transaction_status_payment'] != ''){
            $s_sql .= ' AND p.payment_status = '.'"'.$a_search['search_transaction_status_payment'].'"';
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
        $s_sql = "SELECT p.id AS paymentId,p.txnid,p.amount,p.post_data,p.payment_status,p.created_datetime,u.id AS userId,u.firstname,u.lastname,u.email,u.mobile 
        FROM booking_payment_details AS p 
        LEFT JOIN users AS u ON u.id=p.created_by
        WHERE 1=1";

        if(!empty( $a_search['search_user_name'])){
            $s_sql .= ' AND (LOWER((CONCAT(u.firstname, " ", u.lastname))) LIKE \'%' . strtolower($a_search['search_user_name']) . '%\')';
        } 

        if(!empty($a_search['search_start_payment_date'])){
            $startdate = strtotime($a_search['search_start_payment_date']);    
            $s_sql .= " AND p.created_datetime >= "." $startdate";
            // dd($sSQL);
        }

        if(!empty($a_search['search_end_payment_date'])){
            $endDate = strtotime($a_search['search_end_payment_date']);
            $s_sql .= " AND  p.created_datetime <="." $endDate";
            // dd($sSQL);
        } 

        
        if (!empty($a_search['search_transaction_id_payment'])) {
            $s_sql .= ' AND LOWER( p.txnid) LIKE \'%' . strtolower($a_search['search_transaction_id_payment']) . '%\'';
        }
     

        if(isset( $a_search['search_transaction_status_payment']) &&  $a_search['search_transaction_status_payment'] != ''){
            $s_sql .= ' AND p.payment_status = '.'"'.$a_search['search_transaction_status_payment'].'"';
        } 

        if ($limit > 0) {
            $s_sql .= ' LIMIT ' . $a_search['Offset'] . ',' . $limit;
        }
         
        $a_return = DB::select($s_sql);
        // dd($a_return);
        return $a_return;
    }

    

}
