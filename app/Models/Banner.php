<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\DB;

class Banner extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $table = 'banner';


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'barcode_number',
        'firstname',
        'lastname',
        'mobile',
        'email',
        'password',
        'mobile',
        'gender',
        'created_at'
    ];

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


    public static function get_all($limit, $a_search = array())
    {
        $a_return = [];

        $s_sql = 'SELECT b.id, b.banner_name, b.banner_image, b.banner_url, b.start_time, b.end_time,
                        (SELECT name FROM cities WHERE Id = b.city) AS city,
                        (SELECT name FROM states WHERE Id = b.state) AS state,
                        (SELECT name FROM countries WHERE Id = b.country) AS country,
                        b.active
                  FROM banner b
                  WHERE 1=1';

        if (!empty($a_search['search_banner'])) {
            $s_sql .= ' AND (LOWER(b.banner_name) LIKE \'%' . strtolower($a_search['search_banner']) . '%\')';
        }

        if(!empty($a_search['search_start_booking_date'])){
            $startdate = strtotime($a_search['search_start_booking_date']);    
            $s_sql .= " AND b.start_time >= "." $startdate";
            // dd($sSQL);
        }

        if(!empty($a_search['search_end_booking_date'])){
            $endDate = strtotime($a_search['search_end_booking_date']);
            $s_sql .= " AND  b.end_time <="." $endDate";
            // dd($sSQL);
        } 

        if(isset( $a_search['search_banner_status']) &&  $a_search['search_banner_status'] != ''){
            $s_sql .= ' AND b.active = '.$a_search['search_banner_status'];
        } 

        if(!empty( $a_search['search_country'])){
            $s_sql .= ' AND b.country = '. $a_search['search_country']. ' ';
        } 

        if(!empty( $a_search['search_state'])){
            $s_sql .= ' AND b.state = '. $a_search['search_state']. ' ';
        } 

        if(!empty( $a_search['search_city'])){
            $s_sql .= ' AND b.city = '. $a_search['search_city']. ' ';
        } 

        if ($limit > 0) {
            $s_sql .= ' LIMIT ' . $a_search['Offset'] . ',' . $limit;
        }

        $a_return = DB::select($s_sql);
        return $a_return;
    }






    public static function get_count($a_search = array())
    {
        $count = 0;
        // dd($a_search);
        $s_sql = 'SELECT count(id) as count FROM banner b WHERE 1=1';

        if (!empty($a_search['search_banner'])) {
            $s_sql .= ' AND (LOWER(b.banner_name) LIKE \'%' . strtolower($a_search['search_banner']) . '%\')';
        }

        if(!empty($a_search['search_start_booking_date'])){
            $startdate = strtotime($a_search['search_start_booking_date']);
            $s_sql .= " AND b.start_time >= "." $startdate";
            // dd($sSQL);
        }

        if(!empty($a_search['search_end_booking_date'])){
            $endDate = strtotime($a_search['search_end_booking_date']);
            $s_sql .= " AND  b.end_time <="." $endDate";
            // dd($sSQL);
        } 

      
        if(isset( $a_search['search_banner_status']) &&  $a_search['search_banner_status'] != ''){
            $s_sql .= ' AND b.active = '.$a_search['search_banner_status'];
        } 
   
        
        if(!empty( $a_search['search_country'])){
            $s_sql .= ' AND b.country = '. $a_search['search_country']. ' ';
        } 

        if(!empty( $a_search['search_state'])){
            $s_sql .= ' AND b.state = '. $a_search['search_state']. ' ';
        } 

        if(!empty( $a_search['search_city'])){
            $s_sql .= ' AND b.city = '. $a_search['search_city']. ' ';
        } 


        $CountsResult = DB::select($s_sql);
        if (!empty($CountsResult)) {
            $count = $CountsResult[0]->count;
        }
        // dd($count);
        return $count;
    }

    public static function add_banner($request)
{
        $banner_image_name = '';

        if ($request->file('banner_image')) {
            $path = public_path('uploads/banner_image/');
            $banner_image = $request->file('banner_image');
            $imageExtension = $banner_image->getClientOriginalExtension();
            $banner_image_name = strtotime('now') . '_banner.' . $imageExtension;
            //dd($banner_image_name);
            $banner_image->move($path, $banner_image_name);
        }

        $ssql = 'INSERT INTO banner (
            banner_name, banner_image, banner_url, start_time, end_time, city, state, country, created_datetime
        ) VALUES (
            :banner_name, :banner_image, :banner_url, :start_time, :end_time, :city, :state, :country, :created_datetime
        )';

        $bindings = array(
            'banner_name' => $request->banner_name,
            'banner_image' => $banner_image_name, // Use $banner_image_name here
            'banner_url' => $request->banner_url,
            'start_time' => strtotime($request->start_date),
            'end_time' => strtotime($request->end_date),
            'city' => !empty($request->city) ? $request->city : ' ',
            'state' => !empty($request->state) ? $request->state : ' ',
            'country' => !empty($request->country) ? $request->country : ' ',
            'created_datetime' => now() // Assuming `created_datetime` is a timestamp field
        );

        $Result = DB::insert($ssql, $bindings);
// dd( $Result);
        return $banner_image_name;
    }



    public static function update_banner($iId, $request)
    {

        $banner_image_name = '';

        if (!empty($request->file('banner_image'))) { // Check if banner_image is not empty
            $path = public_path('uploads/banner_image/');
            $banner_image = $request->file('banner_image');
            $imageExtension = $banner_image->getClientOriginalExtension();

            $banner_image_name = strtotime('now') . '_banner.' . $imageExtension;

            $banner_image->move($path, $banner_image_name);
        } else {
            $banner_image_name = $request->banner_image_name;
        }

        $ssql = 'UPDATE banner SET
            banner_name = :banner_name,
            banner_url = :banner_url,
            start_time = :start_time,
            end_time = :end_time,
            city = :city,
            state = :state,
            country = :country';

        $bindings = array(
            'banner_name' => $request->banner_name,
            'banner_url' => $request->banner_url,
            'start_time' => strtotime($request->start_date),
            'end_time' => strtotime($request->end_date),
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country
        );

        if (!empty($banner_image_name)) {
            $ssql .= ', banner_image = :banner_image';
            $bindings['banner_image'] = $banner_image_name;
        }

        $ssql .= ' WHERE id = :id';

        $bindings['id'] = $iId;

        $Result = DB::update($ssql, $bindings);
        // You might want to add error handling or return statements here
    }



    public static function change_status_banner($request)
    {
        $sSQL = 'UPDATE banner SET active=:active WHERE id=:id';
        $aReturn = DB::update(
            $sSQL,
            array(
                'active' => $request->status,
                'id' => $request->id
            )
        );
        return $aReturn;
    }

    public static function remove_banner($iId)
    {
        $Result = null;
        if (!empty($iId)) {
            $sSQL = 'DELETE FROM `banner` WHERE id=:id';
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
