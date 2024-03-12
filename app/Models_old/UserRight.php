<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserRight extends Model
{
    use HasFactory;
    protected $table = 'user_rights';

    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'role_id',
        'access'
    ];

    public static function get_user_modules($user_id,$iIsSuperadmin)
    {
        if($iIsSuperadmin){
            $rAccess = DB::table('master_roles')
                        ->select('id as role_id')
                        ->where('active','=',1)
                        // ->orderBy('sort_order', 'ASC')
                        ->get();
        }else{
            $rAccess = DB::table('master_roles')
                        ->leftJoin('user_rights', 'master_roles.id', '=', 'user_rights.role_id')
                        ->select('master_roles.id as role_id')
                        ->where('user_rights.user_id','=',$user_id)
                        ->where('master_roles.access','>',0)
                        ->where('master_roles.active','=',1)
                        // ->orderBy('master_roles.sort_order', 'ASC')
                        ->get();
        }
		$aReturn = array();
        // dd($rAccess);
		foreach($rAccess as $aAccess){
            $rModule = DB::table('master_roles')->select('*')->where('id','=',$aAccess->role_id)->first();
            $rModule = json_decode(json_encode($rModule,true),true);
			// $aReturn[$rModule['role_name']] = $rModule;
		}
		return $aReturn;
    }
}
