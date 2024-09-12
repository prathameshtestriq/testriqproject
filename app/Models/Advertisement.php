<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Advertisement extends Model
{
    use HasFactory;
    protected $table = 'advertisement';

   /**
    * The attributes that should be hidden for serialization.
    *
    * @var array<int, string>
    */
   protected $hidden = [
       'password',
       'remember_token',
   ];

   /**
    * The attributes that should be cast.
    *
    * @var array<string, string>
    */
   protected $casts = [
       'email_verified_at' => 'datetime',
       'password' => 'hashed',
   ];

   /**
    * Interact with the user's first name.
    *
    * @param  string  $value
    * @return \Illuminate\Database\Eloquent\Casts\Attribute
    */

   // protected function type(): Attribute
   // {
   //     return new Attribute(
   //         get: fn($value) => ["superadmin", "admin", "user"][$value],
   //     );
   // }

   protected function type(): Attribute
   {
       return new Attribute(
           get: fn($value) => ["superadmin", "admin", "user"][$value],
       );
   }
   

   public $timestamps = false;


   public static function get_all_ad($limit, $a_search = array())
   {
       $a_return = [];
   
       $s_sql = 'SELECT a.id, a.name, a.position, a.start_time, a.end_time, a.img, a.url, a.status
                 FROM advertisement a where 1=1';
   
       if (!empty($a_search['search_name'])) {
           $s_sql .= ' AND LOWER(a.name) LIKE \'%' . strtolower($a_search['search_name']) . '%\'';
       }

       if(!empty($a_search['search_start_booking_date'])){
            $startdate = strtotime($a_search['search_start_booking_date']);  
            $s_sql .= " AND a.start_time >= "." $startdate";
            // dd($s_sql);
        }

        if(!empty($a_search['search_end_booking_date'])){
            $endDate = strtotime($a_search['search_end_booking_date']);
            $s_sql .= " AND  a.end_time <="." $endDate";
            // dd($sSQL);
        } 

        if(isset( $a_search['search_advertisement_status']) &&  $a_search['search_advertisement_status'] != ''){
            $s_sql .= ' AND a.status = '.$a_search['search_advertisement_status'].'';
        } 
       
       if ($limit > 0) {
           $s_sql .= ' LIMIT ' . $a_search['Offset'] . ',' . $limit;
       }
   
       $a_return = DB::select($s_sql);
    //    dd($a_return);
       return $a_return;
   }
   
   
   public static function get_count_ad($a_search = array())
   {
       $count = 0;
       $s_sql = 'SELECT count(id) as count FROM advertisement a WHERE 1=1';
   
       if (!empty($a_search['search_name'])) {
           $s_sql .= ' AND (LOWER(a.name) LIKE \'%' . strtolower($a_search['search_name']) . '%\')';
       }

       if(!empty($a_search['search_start_booking_date'])){
            $startdate = strtotime($a_search['search_start_booking_date']);    
            $s_sql .= " AND a.start_time >= "." $startdate";
            // dd($s_sql);
        }

        if(!empty($a_search['search_end_booking_date'])){
            $endDate = strtotime($a_search['search_end_booking_date']);
            $s_sql .= " AND  a.end_time <="." $endDate";
            // dd($sSQL);
        } 
       
        if(isset( $a_search['search_advertisement_status']) &&  $a_search['search_advertisement_status'] != ''){
            $s_sql .= ' AND a.status = '.$a_search['search_advertisement_status'];
        } 
   
       $CountsResult = DB::select($s_sql);
    //   dd($CountsResult);
   
       if (!empty($CountsResult)) {
           $count = $CountsResult[0]->count;
       }
   
       return $count;
   }
   

   public static function add_advertisement($request)
   {
       $img_name = '';

       if ($request->file('img')) {
           $path = public_path('uploads/images/');
           $img_file = $request->file('img');
           $img_extension = $img_file->getClientOriginalExtension();
           $img_name = strtotime('now') . '_ad.' . $img_extension;
           
           $img_file->move($path, $img_name);
       }
   
       
       $sql = 'INSERT INTO advertisement (
                  name, position, start_time, end_time, url, img
              ) VALUES (
                  :name, :position, :start_time, :end_time, :url, :img
              )';
   
       $bindings = [
           'name' => $request->name,
           'position' => $request->position,
           'start_time' => strtotime($request->start_date),
           'end_time' => strtotime($request->end_date),
           'url' => $request->url,
           'img' => $img_name
       ];
      
       $result = DB::insert($sql, $bindings);
   
       return $result;
   }
   

   
   public static function update_advertisement($id, $request)
   {
       $img_name = '';
   
       if ($request->hasFile('img')) { 
           $path = public_path('uploads/images/');
           $img_file = $request->file('img');
           $img_extension = $img_file->getClientOriginalExtension();
           $img_name = strtotime('now') . '_ad.' . $img_extension;
           
           $img_file->move($path, $img_name);
       }
   
       
       $sql = 'UPDATE advertisement SET
                  name = :name,
                  position= :position,
                  start_time = :start_time,
                  end_time = :end_time,
                  url = :url';
       
       $bindings = [
           'name' => $request->name,
           'position' =>$request->position,
           'start_time' => strtotime($request->start_date),
           'end_time' => strtotime($request->end_date),
           'url' => $request->url,
           'id' => $id
       ];
   
       
       if (!empty($img_name)) {
           $sql .= ', img = :img';
           $bindings['img'] = $img_name;
       }
   
       $sql .= ' WHERE id = :id';
   
       // Execute the update query
       $result = DB::update($sql, $bindings);
   
       return $result;
   }
   
   

 public static function change_status_advertisement($request)
 {
    $sSQL = 'UPDATE advertisement SET status=:status WHERE id=:id';
    $aReturn = DB::update(
        $sSQL,
        array(
            'status' => $request->status,
            'id' => $request->id
        )
    );
    return $aReturn;
  }


   public static function remove_add($iId)
   {
       $Result = null; 
       if (!empty($iId)) {
           $sSQL = 'DELETE FROM `advertisement` WHERE id=:id';
           $Result = DB::update(
               $sSQL,
               array(
                   'id' => $iId
               )
           );
           // dd($Result);
       }
       return $Result;
}

}
