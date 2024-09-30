<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class RegistrationSuccessfulModel extends Model
{
    use HasFactory;
    protected $table = 'event_booking';
 
    protected $fillable = [
       
    ];
    public $timestamps = false;

    public static function get_count_event_registration($event_id,$a_search = array()){
        $count = 0;
    
        $s_sql = "SELECT count(eb.id) AS count FROM event_booking AS eb 
        LEFT JOIN booking_details AS bd ON bd.booking_id = eb.id
        LEFT JOIN users AS u ON u.id = eb.user_id
        WHERE eb.event_id= ".$event_id;

        if (!empty($a_search['search_registration_user_name'])) {
            $s_sql .= ' AND (LOWER((CONCAT(u.firstname, " ", u.lastname))) LIKE \'%' . strtolower($a_search['search_registration_user_name']) . '%\')';
        }


        if(isset( $a_search['search_registration_transaction_status'])){
            $s_sql .= ' AND (LOWER( eb.transaction_status) LIKE \'%' . strtolower($a_search['search_registration_transaction_status']) . '%\')';
        } 


        if(!empty($a_search['search_start_registration_booking_date'])){
            $startdate = strtotime($a_search['search_start_registration_booking_date']);  
            $s_sql .= " AND eb.booking_date >= "." $startdate";
            // dd($sSQL);
        }

        if(!empty($a_search['search_end_registration_booking_date'])){
            $endDate = strtotime($a_search['search_end_registration_booking_date']);
            $s_sql .= " AND  eb.booking_date <="." $endDate";
            // dd($sSQL);
        } 


        $CountsResult = DB::select($s_sql);
       
        if (!empty($CountsResult)) {
            $count = $CountsResult[0]->count;
        }
        // dd( $count);
        return $count;
    }

    public static function get_all_event_registration($limit,$event_id,$a_search = array()){
        $a_return = [];

        $s_sql = "SELECT eb.id AS EventBookingId,eb.user_id,eb.booking_date,eb.total_amount AS TotalAmount,eb.transaction_status,
        SUM(bd.quantity) AS TotalTickets,u.id,u.firstname,u.lastname,u.email,u.mobile
        FROM event_booking AS eb 
        LEFT JOIN booking_details AS bd ON bd.booking_id = eb.id
        LEFT JOIN users AS u ON u.id = eb.user_id
        WHERE eb.event_id= ".$event_id;

        if (!empty($a_search['search_registration_user_name'])) {
            $s_sql .= ' AND (LOWER((CONCAT(u.firstname, " ", u.lastname))) LIKE \'%' . strtolower($a_search['search_registration_user_name']) . '%\')';
        }
 
       
        // if(isset( $a_search['search_registration_transaction_status'])){
        //     $s_sql .= ' AND (LOWER( eb.transaction_status) LIKE \'%' . strtolower($a_search['search_registration_transaction_status']) . '%\')';
        // } 

        if(isset( $a_search['search_registration_transaction_status']) &&  $a_search['search_registration_transaction_status'] != ''){
            $s_sql .= ' AND eb.transaction_status = '.$a_search['search_registration_transaction_status'];
        } 

        if(!empty( $a_search['search_registration_email'])){
            $s_sql .= ' AND (LOWER(u.email) LIKE \'%' . strtolower($a_search['search_registration_email']) . '%\')';
        } 

        if(!empty( $a_search['search_registration_mobile'])){
            $s_sql .= ' AND (LOWER(u.mobile) LIKE \'%' . strtolower($a_search['search_registration_mobile']) . '%\')';
        } 

        if(!empty($a_search['search_start_registration_booking_date'])){
            $startdate = strtotime($a_search['search_start_registration_booking_date']);  
            $s_sql .= " AND eb.booking_date >= "." $startdate";
            // dd($sSQL);
        }

        if(!empty($a_search['search_end_registration_booking_date'])){
            $endDate = strtotime($a_search['search_end_registration_booking_date']);
            $s_sql .= " AND  eb.booking_date <="." $endDate";
            // dd($sSQL);
        } 

        $s_sql .= " GROUP BY bd.booking_id";
        $s_sql .= " ORDER BY eb.id DESC";
     
        if ($limit > 0) {
            $s_sql .= ' LIMIT ' . $a_search['Offset'] . ',' . $limit;
        }

        $a_return = DB::select($s_sql);
    //    dd( $a_return);
        return $a_return;
    }

    public static function get_all_count($event_id,$a_search = array()){
        $count = 0;

        // $s_sql = "SELECT COUNT(eb.id) AS count FROM event_booking AS eb 
        // LEFT JOIN booking_details AS bd ON bd.booking_id = eb.id
        // LEFT JOIN users AS u ON u.id = eb.user_id
        // WHERE  1=1";
      
         $s_sql = "SELECT COUNT(e.id) AS count FROM event_booking e WHERE 1=1";

        if (!empty($a_search['search_registration_user_name'])) {
            $s_sql .= ' AND (LOWER((CONCAT(u.firstname, " ", u.lastname))) LIKE \'%' . strtolower($a_search['search_registration_user_name']) . '%\')';
        }


        if(isset( $a_search['search_registration_transaction_status']) &&  $a_search['search_registration_transaction_status'] != ''){
            $s_sql .= ' AND eb.transaction_status = '.$a_search['search_registration_transaction_status'];
        } 


        if(!empty($a_search['search_start_registration_booking_date'])){
            $startdate = strtotime($a_search['search_start_registration_booking_date']);  
            $s_sql .= " AND eb.booking_date >= "." $startdate";
            // dd($sSQL);
        }

        if(!empty($a_search['search_end_registration_booking_date'])){
            $endDate = strtotime($a_search['search_end_registration_booking_date']);
            $s_sql .= " AND  eb.booking_date <="." $endDate";
            // dd($sSQL);
        } 

        // $s_sql .= " AND e.transaction_status IN (1,3)";
        // $s_sql .= " GROUP BY bd.booking_id";
        // $s_sql .= " ORDER BY eb.id DESC";

        $CountsResult = DB::select($s_sql);
       
        if (!empty($CountsResult)) {
            $count = $CountsResult[0]->count;
        }
        // dd( $count);
        return $count;
    }
    public static function get_all($limit,$event_id,$a_search = array()){
        $a_return = [];

        $s_sql = "SELECT count(eb.id) AS EventBookingId,eb.user_id,eb.booking_date,eb.total_amount AS TotalAmount,eb.transaction_status,
        SUM(bd.quantity) AS TotalTickets,(SELECT firstname FROM users u WHERE u.id =  eb.user_id) As firstname,(SELECT lastname FROM users u WHERE u.id =  eb.user_id) As lastname,(SELECT email FROM users u WHERE u.id =  eb.user_id) As email,(SELECT mobile FROM users u WHERE u.id =  eb.user_id) As mobile,(SELECT id FROM users u WHERE u.id =  eb.user_id) As id  FROM event_booking AS eb LEFT JOIN booking_details AS bd ON bd.booking_id = eb.id LEFT JOIN users AS u ON u.id = eb.user_id WHERE 1=1";


        if (!empty($a_search['search_registration_user_name'])) {
            $s_sql .= ' AND (LOWER((CONCAT(u.firstname, " ", u.lastname))) LIKE \'%' . strtolower($a_search['search_registration_user_name']) . '%\')';
        }
 
       
        if(isset( $a_search['search_registration_transaction_status'])){
            $s_sql .= ' AND (LOWER( eb.transaction_status) LIKE \'%' . strtolower($a_search['search_registration_transaction_status']) . '%\')';
        } 

        if(!empty( $a_search['search_registration_email'])){
            $s_sql .= ' AND (LOWER(u.email) LIKE \'%' . strtolower($a_search['search_registration_email']) . '%\')';
        } 

        if(!empty( $a_search['search_registration_mobile'])){
            $s_sql .= ' AND (LOWER(u.mobile) LIKE \'%' . strtolower($a_search['search_registration_mobile']) . '%\')';
        } 

        if(!empty($a_search['search_start_registration_booking_date'])){
            $startdate = strtotime($a_search['search_start_registration_booking_date']);  
            $s_sql .= " AND eb.booking_date >= "." $startdate";
            // dd($sSQL);
        }

        if(!empty($a_search['search_end_registration_booking_date'])){
            $endDate = strtotime($a_search['search_end_registration_booking_date']);
            $s_sql .= " AND  eb.booking_date <="." $endDate";
            // dd($sSQL);
        } 

        // $s_sql .= " AND eb.transaction_status IN (1,3)";
        $s_sql .= " GROUP BY bd.booking_id";
        $s_sql .= " ORDER BY eb.id DESC";
     
        if ($limit > 0) {
            $s_sql .= ' LIMIT ' . $a_search['Offset'] . ',' . $limit;
        }

        $a_return = DB::select($s_sql);
    //    dd( $a_return);
        return $a_return;
    }

  
}
