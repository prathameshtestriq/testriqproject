<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Category extends Model
{
   // use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'id';
    protected $table = 'category';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'logo',
        'active',
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


    public static function get_all_category($limit, $a_search = array())
    {
        $a_return = [];

        $s_sql = 'SELECT id, name, logo, active FROM category where 1=1';

        if (!empty($a_search['search_category'])) {
            $s_sql .= ' AND LOWER(name) LIKE \'%' . strtolower($a_search['search_category']) . '%\'';
        }
     
        if(isset( $a_search['search_category_status'])){
            $s_sql .= ' AND (LOWER(active) LIKE \'%' . strtolower($a_search['search_category_status']) . '%\')';
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
        $s_sql = 'SELECT count(id) as count FROM category c WHERE 1=1';

        if (!empty($a_search['search_name'])) {
            $s_sql .= ' AND (LOWER(c.name) LIKE \'%' . strtolower($a_search['search_name']) . '%\'';
         //   $s_sql .= ' OR LOWER(u.lastname) LIKE \'%' . strtolower($a_search['search_name']) . '%\'';
           // $s_sql .= ' OR LOWER(u.email) LIKE \'%' . strtolower($a_search['search_name']) . '%\')';
        }

        if(isset( $a_search['search_category_status'])){
            $s_sql .= ' AND (LOWER(active) LIKE \'%' . strtolower($a_search['search_category_status']) . '%\')';
        } 

        $CountsResult = DB::select($s_sql);
        if (!empty($CountsResult)) {
            $count = $CountsResult[0]->count;
        }
        // dd($count);
        return $count;
    }

    public static function add_category($request)
    {
        // dd($request);
        // $name = $request->category_name; // Corrected field name
        // $logo = $request->category_logo; // Corrected field name
        // $active = $request->status == 'active' ? 1 : 0; // Corrected field name

        $name = $request['category_name'];
        $logo = $request['category_logo'];
        //dd($logo);
        $active = $request['status'] == 'active' ? 1 : 0;
    
        $sSQL = 'INSERT INTO category(name, logo, active)
                 VALUES(:name, :logo, :active)';
    
        $bindings = array(
            'name' => $name,
            'logo' => $logo,
            'active' => $active,
        );
    
        $result = DB::insert($sSQL, $bindings);
    
        return $result;
    }
    
    
    public static function update_category($iId, $request)
    {
        $name = $request['category_name'];
        $logo = $request['category_logo'];
        $active = $request['status'] == 'active' ? 1 : 0;
    
        if ($iId > 0) {
            $sSQL = 'UPDATE category SET
                name = :name,
                logo = :logo,
                active = :active
                WHERE id = :id';
    
            $bindings = array(
                'name' => $name,
                'logo' => $logo,
                'active' => $active, 
                'id' => $iId
            );
    
            $result = DB::update($sSQL, $bindings);
        }
    }
    
    

    public static function change_active_status_category($request)
    {
        $sSQL = 'UPDATE category SET active=:active WHERE id=:id';
        $aReturn = DB::update(
            $sSQL,
            array(
                'active' => $request->status,
                'id' => $request->id
            )
        );
        return $aReturn;
    }

    public static function delete_category($iId)
    {
        $Result = null; 
        if (!empty($iId)) {
            $sSQL = 'DELETE FROM `category` WHERE id=:id';
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
