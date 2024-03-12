<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events';
	public $timestamps = false;

    public static function get($aSearch){
        $query = Event::from('events as e');
        $query->where('e.deleted',0);

       return $query;
   }

   public function isFollowed($EventId,$UserId){
        // dd($EventId,$UserId);
        $Follow = 0;
        if(!empty($EventId) && !empty($UserId)){
            $sql = "SELECT id FROM event_user_follow WHERE event_id=:event_id AND user_id=:user_id";
            $Result = DB::select($sql,array('event_id' => $EventId,'user_id'=>$UserId));
            // dd($Result);
            if(count($Result)>0)    $Follow =1;
        }
        return $Follow;
   }
}
