<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class MasterOrganiser extends Model
{
    use HasFactory;
    protected $table = 'organizer';
 
    protected $fillable = [
        'name'
    ];
    public $timestamps = false;
 
    public static function get_count($a_search = array()){
        $count = 0;
        // dd($a_search);
        $s_sql = 'SELECT count(o.id) as count FROM organizer o';
        $s_sql .= ' LEFT JOIN users u ON u.id = o.user_id WHERE o.is_deleted = 0';

        if (!empty($a_search['search_organiser_name'])) {
            $s_sql .= ' AND LOWER(o.name) LIKE \'%' . strtolower($a_search['search_organiser_name']) . '%\'';
        }
        if (isset($a_search['search_gst_number'])) {
            $s_sql .= ' AND LOWER(o.gst_number) LIKE \'%' . strtolower($a_search['search_gst_number']) . '%\'';
        }
        if (!empty($a_search['search_organiser_user_name'])) {
            $s_sql .= ' AND LOWER(u.id) LIKE \'%' . strtolower($a_search['search_organiser_user_name']) . '%\'';
        }

        // if(isset( $a_search['search_role_status'])){
        //     $s_sql .= ' AND (LOWER(rm.status) LIKE \'%' . strtolower($a_search['search_role_status']) . '%\')';
        // } 

        $CountsResult = DB::select($s_sql);
        // dd($CountsResult);
        if (!empty($CountsResult)) {
            $count = $CountsResult[0]->count;
        }
        // dd($count);
        return $count;
    }

    public static function get_all($limit, $a_search = array()){
        $a_return = [];

        $s_sql = 'SELECT id,name,email,mobile,gst_number,logo_image,(select CONCAT(`firstname`, " ", `lastname`) AS user_name from users where id = organizer.user_id) as user_name,(select id from users u where u.id = organizer.user_id) as user_id,(select email from users where id = organizer.user_id) as user_email,(select password from users where id = organizer.user_id) as user_password FROM organizer where is_deleted = 0';
        
        // ,(select CONCAT(`firstname`, ' ', `lastname`) AS user_name from users where id = organizer.user_id ) as user_name
        if (!empty($a_search['search_organiser_name'])) {
            $s_sql .= ' AND LOWER(name) LIKE \'%' . strtolower($a_search['search_organiser_name']) . '%\'';
        }
        
        if(isset( $a_search['search_gst_number'])){
            $s_sql .= ' AND (LOWER(gst_number) LIKE \'%' . strtolower($a_search['search_gst_number']) . '%\')';
        } 
    //  dd($a_search['search_organiser_user_name']);
        if(!empty($a_search['search_organiser_user_name'])){
            $s_sql .= ' AND user_id = ' . strtolower($a_search['search_organiser_user_name']);
        } 

        if ($limit > 0) {
            $s_sql .= ' LIMIT ' . $a_search['Offset'] . ',' . $limit;
        }
         // dd($s_sql);
        $a_return = DB::select($s_sql);
        // dd($a_return );
        return $a_return;
    }

    
    public static function add_organiser($request)
	{
        $user_id = Session::get('logged_in');
        $ssql = 'INSERT INTO organizer(name,user_id,email,mobile,about,contact_person,contact_no,gst,gst_number,gst_percentage,created_at)
        VALUES ( :name,:user_id,:email,:mobile,:about,:contact_person,:contact_no,:gst,:gst_number,:gst_percentage,:created_at)';

        $bindings = array(
            'name' => !empty($request->organiser_name) ? $request->organiser_name : '',
            'user_id'=> $user_id['id'],
            'email' => !empty($request->email) ? $request->email : '',
            'mobile' => !empty($request->contact_number) ? $request->contact_number : '',
            'about' => !empty($request->about) ? $request->about : '',
            'contact_person' => !empty($request->contact_person_name) ? $request->contact_person_name : '',
            'contact_no' => !empty($request->contact_person_contact) ? $request->contact_person_contact : '',
            'gst' => !empty($request->gst) ? $request->gst : '',
            'gst_number' => !empty($request->gst_number) ? $request->gst_number : '',
            'gst_percentage' =>!empty(($request->contact_gst_percentage)) ? ($request->contact_gst_percentage): '',
            'created_at'  => strtotime('now'),
        );
        
        $Result = DB::insert($ssql,$bindings);
        
	}

    public static function update_organiser($iId, $request)
	{
        $user_id = Session::get('logged_in');
        $ssql = 'UPDATE organizer SET 
        name = :name,
        user_id = :user_id,
        email = :email,
        mobile = :mobile,
        about = :about,
        contact_person = :contact_person,
        contact_no = :contact_no,
        gst = :gst,
        gst_number = :gst_number,
        gst_percentage = :gst_percentage,
        created_at = :created_at
        WHERE id=:id';

        $bindings = array(
            'name' => !empty($request->organiser_name) ? $request->organiser_name : '',
            'user_id' => $user_id['id'],
            'email' => !empty($request->email) ? $request->email : '',
            'mobile' => !empty($request->contact_number) ? $request->contact_number : '',
            'about' => !empty($request->about) ? $request->about : '',
            'contact_person' =>  !empty($request->contact_person_name) ? $request->contact_person_name : '',
            'contact_no' =>  !empty($request->contact_person_contact) ? $request->contact_person_contact : '',
            'gst' => !empty($request->gst) ? $request->gst : '',
            'gst_number' =>!empty($request->gst_number) ? $request->gst_number : '',
            'gst_percentage' => !empty(($request->contact_gst_percentage)) ? ($request->contact_gst_percentage): '',
            'created_at'  => strtotime('now'),  
            'id' => $iId
        );
        // dd($bindings);
        $Result = DB::update($ssql, $bindings);
	}


    public static function delete_organiser($iId)
    {
        if(!empty($iId)){
            $sSQL = 'UPDATE organizer SET is_deleted = 1 WHERE id=:id';
            $Result = DB::update( $sSQL, array(
                    'id' => $iId
                )
            );
            return $Result;
          }
    }

 
    

   
}
