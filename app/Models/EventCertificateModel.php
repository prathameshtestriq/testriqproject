<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Casts\Attribute;

class EventCertificateModel extends Model
{
    use HasFactory;
    protected $table = 'event_certificates_details';

    public $timestamps = false;
   public static function get_all($limit, $a_search = array())
   {
       $a_return = [];
   
    //    $s_sql = 'SELECT ecd.id,ecd.event_id,ecd.image,ecd.status,ecd.field,(SELECT name FROM events as e where e.id =ecd.event_id ) As Event_Name FROM event_certificates_details ecd where 1=1';
   
        $s_sql  = "SELECT ecd.id,
            ecd.event_id,ecd.certificate_name,
            COALESCE(
                MAX(CASE WHEN ecd.image != '' THEN ecd.image END),
                '' -- Default to an empty string if no image is present
            ) AS image,
            MAX(ecd.status) AS status,
            (SELECT name FROM events e WHERE e.id = ecd.event_id) AS Event_Name
            FROM 
                event_certificates_details ecd
            WHERE 
                1=1";

        if(isset( $a_search['event_id_certificate']) &&  $a_search['event_id_certificate'] != ''){
            $s_sql .= ' AND ecd.event_id = '.$a_search['event_id_certificate'].'';
        } 

        if(isset( $a_search['search_event_certificate_status']) &&  $a_search['search_event_certificate_status'] != ''){
            $s_sql .= ' AND ecd.status = '.$a_search['search_event_certificate_status'].'';
        } 
       
        $s_sql .= ' GROUP BY ecd.event_id';
        

       if ($limit > 0) {
           $s_sql .= ' LIMIT ' . $a_search['Offset'] . ',' . $limit;
       }
         
       $a_return = DB::select($s_sql);
    //    dd($a_return);
       return $a_return;
   }
   
   
   public static function get_count($a_search = array())
   {
       $count = 0;
    //    $s_sql = 'SELECT count(ecd.id) as count FROM event_certificates_details ecd where 1=1';
        $s_sql  = "SELECT 
            COUNT(DISTINCT ecd.event_id) AS count
        FROM 
            event_certificates_details ecd
        WHERE 
            1=1";
   
       if(isset( $a_search['event_id_certificate']) &&  $a_search['event_id_certificate'] != ''){
            $s_sql .= ' AND ecd.event_id = '.$a_search['event_id_certificate'].'';
        } 

        if(isset( $a_search['search_event_certificate_status']) &&  $a_search['search_event_certificate_status'] != ''){
            $s_sql .= ' AND ecd.status = '.$a_search['search_event_certificate_status'].'';
        } 
   
       $CountsResult = DB::select($s_sql);
    //    dd($CountsResult);
   
       if (!empty($CountsResult)) {
           $count = $CountsResult[0]->count;
       }
  
       return $count;
   }
   

//    public static function add_advertisement($request)
//    {
//        $img_name = '';

//        if ($request->file('img')) {
//            $path = public_path('uploads/images/');
//            $img_file = $request->file('img');
//            $img_extension = $img_file->getClientOriginalExtension();
//            $img_name = strtotime('now') . '_ad.' . $img_extension;
           
//            $img_file->move($path, $img_name);
//        }
   
       
//        $sql = 'INSERT INTO advertisement (
//                   name, position, start_time, end_time, url, img
//               ) VALUES (
//                   :name, :position, :start_time, :end_time, :url, :img
//               )';
   
//        $bindings = [
//            'name' => $request->name,
//            'position' => $request->position,
//            'start_time' => strtotime($request->start_date),
//            'end_time' => strtotime($request->end_date),
//            'url' => $request->URL,
//            'img' => $img_name
//        ];
      
//        $result = DB::insert($sql, $bindings);
   
//        return $result;
//    }
   

   
//    public static function update_advertisement($id, $request)
//    {
//        $img_name = '';
   
//        if ($request->hasFile('img')) { 
//            $path = public_path('uploads/images/');
//            $img_file = $request->file('img');
//            $img_extension = $img_file->getClientOriginalExtension();
//            $img_name = strtotime('now') . '_ad.' . $img_extension;
           
//            $img_file->move($path, $img_name);
//        }
   
       
//        $sql = 'UPDATE advertisement SET
//                   name = :name,
//                   position= :position,
//                   start_time = :start_time,
//                   end_time = :end_time,
//                   url = :url';
       
//        $bindings = [
//            'name' => $request->name,
//            'position' =>$request->position,
//            'start_time' => strtotime($request->start_date),
//            'end_time' => strtotime($request->end_date),
//            'url' => $request->URL,
//            'id' => $id
//        ];
   
       
//        if (!empty($img_name)) {
//            $sql .= ', img = :img';
//            $bindings['img'] = $img_name;
//        }
   
//        $sql .= ' WHERE id = :id';
   
//        // Execute the update query
//        $result = DB::update($sql, $bindings);
   
//        return $result;
//    }
   
   

 public static function change_status_certificate($request)
 {
  
    $sSQL = 'UPDATE event_certificates_details SET status=:status WHERE event_id=:id';
    $aReturn = DB::update(
        $sSQL,
        array(
            'status' => $request->status,
            'id' => $request->event_id
        )
    );
    return $aReturn;
  }


   public static function delete_certificate($event_id)
   {
       
        if (!empty($event_id)) {
            $sSQL = 'DELETE FROM `event_certificates_details` WHERE event_id=:id';
            $Result = DB::delete(
                $sSQL,
                array(
                    'id' => $event_id
                )
            );
        }
        return $Result;
    }

}
