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

    public static function get($aSearch)
    {
        $query = Event::from('events as e');
        $query->where('e.deleted', 0);

        return $query;
    }

    public function isFollowed($EventId, $UserId)
    {
        // dd($EventId,$UserId);
        $Follow = 0;
        if (!empty($EventId) && !empty($UserId)) {
            // dd($EventId,$UserId);
            $sql = "SELECT id FROM event_user_follow WHERE event_id=:event_id AND user_id=:user_id";
            $Result = DB::select($sql, array('event_id' => $EventId, 'user_id' => $UserId));
            // dd($Result);
            if (count($Result) > 0)
                $Follow = 1;
        }
        return $Follow;
    }

    // public function getCategory($EventId)
    // {
    //     // dd($EventId,$UserId);
    //     $Return = [];
    //     if (!empty($EventId)) {
    //         $sql = "SELECT category_id FROM event_category WHERE event_id=:event_id";
    //         $Result = DB::select($sql, array('event_id' => $EventId));
    //         foreach ($Result as $key => $value) {
    //             $Return[$key] = $value->category_id;
    //         }
    //     }
    //     return $Return;
    // }
    function getCategory($EventId)
    {
        $sql = "SELECT c.* FROM category AS c WHERE active=1";
        $Allcategory = DB::select($sql);

        foreach ($Allcategory as $key => $value) {
            $sql = "SELECT id FROM event_category WHERE category_id=:category_id AND event_id=:event_id";
            $IsExist = DB::select($sql, array('category_id' => $value->id, 'event_id' => $EventId));
            $value->checked = (sizeof($IsExist) > 0) ? true : false;
        }
        return $Allcategory;
    }

    // public function getTypes($EventId)
    // {
    //     // dd($EventId,$UserId);
    //     $Return = [];
    //     if (!empty($EventId)) {
    //         $sql = "SELECT type_id FROM event_type WHERE event_id=:event_id";
    //         $Result = DB::select($sql, array('event_id' => $EventId));
    //         foreach ($Result as $key => $value) {
    //             $Return[$key] = $value->type_id;
    //         }
    //     }
    //     return $Return;
    // }

    function getTypes($EventId)
    {
        $ResponseData = [];
        $ResposneCode = 200;
        $empty = false;
        $message = 'Success';
        $field = '';

        $sql = "SELECT e.* FROM eTypes AS e WHERE e.active=1";
        $AllEventTypes = DB::select($sql);

        foreach ($AllEventTypes as $key => $value) {
            $sql = "SELECT id FROM event_type WHERE type_id=:type_id AND event_id=:event_id";
            $IsExist = DB::select($sql, array('type_id' => $value->id, 'event_id' => $EventId));
            $value->checked = (sizeof($IsExist) > 0) ? true : false;

            $value->logo = (isset($value->logo) && !empty($value->logo)) ? url('/') . '/assets/img/banner/' . $value->logo : "";
        }
        return $AllEventTypes;
    }


    public function getCategoryDetails($EventId)
    {
        $Return = [];
        if (!empty($EventId)) {
            $sql = "SELECT c.id,c.name,c.logo FROM category AS c
            LEFT JOIN event_category AS ec ON c.id = ec.category_id
            WHERE ec.event_id=:event_id";
            $Return = DB::select($sql, array('event_id' => $EventId));
        }
        return $Return;
    }

    public function getTypeDetails($EventId)
    {
        $Return = [];
        if (!empty($EventId)) {
            $sql = "SELECT c.id,c.name,c.logo FROM eTypes AS c
            LEFT JOIN event_type AS ec ON c.id = ec.type_id
            WHERE ec.event_id=:event_id";
            $Return = DB::select($sql, array('event_id' => $EventId));
        }
        return $Return;
    }

    public function getEventImages($EventId)
    {
        $sql = "SELECT id,image FROM event_images WHERE event_id=:event_id";
        $Result = DB::select($sql, array('event_id' => $EventId));
        // dd($Result);
        foreach ($Result as $item) {
            $item->image = !empty($item->image) ? url('/').'/uploads/event_images/'.$item->image : "";
        }
        return $Result;
    }

    function getEventCount($CityId){
        $EventCount = 0;
        $sql = 'SELECT COUNT(id) AS count FROM events WHERE city=:city';
        $Count = DB::select($sql,array('city'=> $CityId));
        if(sizeof($Count) > 0){
            $EventCount = isset($Count[0]->count) ? $Count[0]->count : 0;
        }
        // dd($Count);
        return $EventCount;
    }

}
