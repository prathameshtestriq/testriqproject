<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'id';
    protected $table = 'users';

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

    // protected function type(): Attribute
    // {
    //     return new Attribute(
    //         get: fn($value) => ["superadmin", "admin", "user"][$value],
    //     );
    // }
    
    protected function type(): Attribute
    {
        return new Attribute(
            get: fn($value) => ["superadmin", "admin", "user"][($value % 3 + 3) % 3],
        );
    }



    public $timestamps = false;

    public static function get_all($limit, $a_search = array())
    {
        $a_return = [];

        $s_sql = 'SELECT u.id, u.firstname,u.lastname,u.is_active,u.email,u.mobile,u.gender,u.dob,u.profile_completion_percentage,(SELECT name FROM  states s WHERE u.state = s.id) as state_name,(SELECT name FROM  cities s WHERE u.city = s.id) as city_name
                FROM users u WHERE 1=1';

    

        if(!empty( $a_search['search_name'])){
            $s_sql .= ' AND (LOWER((CONCAT(u.firstname, " ", u.lastname))) LIKE \'%' . strtolower($a_search['search_name']) . '%\')';
        } 

        if(!empty( $a_search['search_email_id'])){
            $s_sql .= ' AND (LOWER(u.email) LIKE \'%' . strtolower($a_search['search_email_id']) . '%\')';
        } 
 
        if(!empty( $a_search['search_mobile'])){
            $s_sql .= ' AND (LOWER(u.mobile) LIKE \'%' . strtolower($a_search['search_mobile']) . '%\')';
        } 
        if(!empty( $a_search['search_state'])){
            $s_sql .= ' AND (LOWER(u.state) LIKE \'%' . strtolower($a_search['search_state']) . '%\')';
        } 

        if(!empty( $a_search['search_city'])){
            $s_sql .= ' AND (LOWER(u.city) LIKE \'%' . strtolower($a_search['search_city']) . '%\')';
        } 
        
        if(isset( $a_search['search_gender'])){
            $s_sql .= ' AND (LOWER(u.gender) LIKE \'%' . strtolower($a_search['search_gender']) . '%\')';
        }


        if(isset( $a_search['search_status'])){
            $s_sql .= ' AND (LOWER(u.is_active) LIKE \'%' . strtolower($a_search['search_status']) . '%\')';
        } 

        // dd($s_sql);
        if ($limit > 0) {
            $s_sql .= ' LIMIT ' . $a_search['Offset'] . ',' . $limit;
        }
        // dd($s_sql);
        $a_return = DB::select($s_sql, array());
        // dd($a_return);
        return $a_return;
    }





    public static function get_count($a_search = array())
    {
        $count = 0;
        // dd($a_search);
        $s_sql = 'SELECT count(id) as count FROM users u WHERE 1=1';

        // if (!empty($a_search['search_name'])) {
        //     $s_sql .= ' AND (LOWER(u.firstname) LIKE \'%' . strtolower($a_search['search_name']) . '%\'';
        //     $s_sql .= ' OR LOWER(u.lastname) LIKE \'%' . strtolower($a_search['search_name']) . '%\'';
        //     $s_sql .= ' OR LOWER(u.email) LIKE \'%' . strtolower($a_search['search_name']) . '%\')';
        // }

        if(!empty( $a_search['search_name'])){
            $s_sql .= ' AND (LOWER((CONCAT(u.firstname, " ", u.lastname))) LIKE \'%' . strtolower($a_search['search_name']) . '%\')';
        } 

        if(!empty( $a_search['search_email_id'])){
            $s_sql .= ' AND (LOWER(u.email) LIKE \'%' . strtolower($a_search['search_email_id']) . '%\')';
        } 

        if(!empty( $a_search['search_mobile'])){
            $s_sql .= ' AND (LOWER(u.mobile) LIKE \'%' . strtolower($a_search['search_mobile']) . '%\')';
        } 

        if(!empty( $a_search['search_state'])){
            $s_sql .= ' AND (LOWER(u.state) LIKE \'%' . strtolower($a_search['search_state']) . '%\')';
        } 

        if(!empty( $a_search['search_city'])){
            $s_sql .= ' AND (LOWER(u.city) LIKE \'%' . strtolower($a_search['search_city']) . '%\')';
        } 
        if(isset( $a_search['search_gender'])){
            $s_sql .= ' AND (LOWER(u.gender) LIKE \'%' . strtolower($a_search['search_gender']) . '%\')';
        }

        if(isset( $a_search['search_status'])){
            $s_sql .= ' AND (LOWER(u.is_active) LIKE \'%' . strtolower($a_search['search_status']) . '%\')';
        } 

       
        
        $CountsResult = DB::select($s_sql);
        if (!empty($CountsResult)) {
            $count = $CountsResult[0]->count;
        }
        // dd($count);
        return $count;
    }

    public static function add_user($request)
    {
        dd($request->all());
        // $firstname = (!empty($request->firstname)) ? $request->firstname : '';
        // $lastname = (!empty($request->lastname)) ? $request->lastname : '';
        // $email = (!empty($request->email)) ? $request->email : '';
        // $password = (!empty($request->password)) ? $request->password : '';
        // $username = !empty($request->username) ? $request->username : '';
        // $user_role = !empty($request->user_role) ? $request->user_role : '0';
        // if ($request->status == 'active') {
        //     $status = 1;
        // }
        // if ($request->status == 'inactive') {
        //     $status = 0;
        // }

        $firstname = (!empty($request->firstname)) ? $request->firstname : '';
        $lastname = (!empty($request->lastname)) ? $request->lastname : '';
        $email = (!empty($request->email)) ? $request->email : '';
        $password = (!empty($request->password)) ? $request->password : '';
        $username = !empty($request->username) ? $request->username : '';
        $user_role = !empty($request->type) ? $request->type : '0';
        // $status = $request->status == 'active' ? 1 : 0;
        $state = !empty($request->state) ? $request->state : '0';
        $city = !empty($request->city) ? $request->city : '0';
        $gender = !empty($request->gender) ? $request->gender : '0';


        $sSQL = 'INSERT INTO users(firstname, lastname, email, password, mobile, type,state,city,gender)
                 VALUES(:firstname, :lastname, :email, :password, :mobile, :type,:state,:city,:gender)';

        $bindings = array(
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'password' => md5($password),
            'mobile' => $request->mobile,
            'type' => $user_role,
            'state'=> $state,
            'city'=>$city,
            'gender'=>$gender
        );
dd($bindings);
        $result = DB::insert($sSQL, $bindings);
        // dd($Result);

    }
    public static function update_user($iId, $request)
    {  
        // dd($request->all());
       
        $firstname = (!empty($request->firstname)) ? $request->firstname : '';
        $lastname = (!empty($request->lastname)) ? $request->lastname : '';
        $email = (!empty($request->email)) ? $request->email : '';
        $password = (!empty($request->password)) ? $request->password : '';
        $mobile_no = !empty($request->mobile) ? $request->mobile : '';
        $user_role = !empty($request->type) ? $request->type : '0';
        // $status = $request->status == 'active' ? 1 : 0;
        $state = !empty($request->state) ? $request->state : '0';
        $city = !empty($request->city) ? $request->city : '0';
        $gender = !empty($request->gender) ? $request->gender : '0';


        if ($iId > 0) {

            $Bindings = array(
                'firstname' => $firstname,
                'lastname' => $lastname,
                'email' => $email,
                'mobile' => $mobile_no,
                'type' => $user_role,
                'state'=> $state,
                'city'=>$city,
                'gender'=>$gender,
                'id' => $iId
            );

            $sSQL = 'UPDATE users SET firstname =:firstname, lastname =:lastname, email =:email, mobile =:mobile, type =:type,state=:state,city=:city,gender=:gender WHERE id=:id';

            // dd($Bindings);
            $result = DB::update($sSQL, $Bindings);

            if (!empty($password)) {

                $sSQL = 'UPDATE users SET Password = :Password WHERE Id=:Id';
                $Result = DB::update(
                    $sSQL,
                    array(
                        'Password' => md5($password),
                        'Id' => $iId
                    )
                );
            }


            // return $Result;
        }
    }

    public static function change_status($request)
    {
        $sSQL = 'UPDATE users SET is_active=:is_active WHERE id=:id';
        $aReturn = DB::update(
            $sSQL,
            array(
                'is_active' => $request->status,
                'id' => $request->id
            )
        );
        return $aReturn;
    }

    public static function remove_user($iId)
    {
        if (!empty($iId)) {
            $sSQL = 'DELETE FROM `users` WHERE id=:id';
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
