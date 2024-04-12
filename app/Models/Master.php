<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Master extends Model
{
    use HasFactory;

    public function getCityName($CityId){
        $CityName = "";
        if($CityId){
            $sql = "SELECT name FROM cities WHERE id=:id";
            $City = DB::select($sql,array('id'=>$CityId));
            $CityName = isset($City[0]->name) ? $City[0]->name : "";
        }
        return $CityName;
    }

    public function getCityLatitude($CityId){
        $CityLatitude = "";
        if($CityId){
            $sql = "SELECT latitude FROM cities WHERE id=:id";
            $City = DB::select($sql,array('id'=>$CityId));
            $CityLatitude = isset($City[0]->latitude) ? $City[0]->latitude : "";
        }
        return $CityLatitude;
    }

    public function getCityLongitude($CityId){
        $CityLongitude = "";
        if($CityId){
            $sql = "SELECT longitude FROM cities WHERE id=:id";
            $City = DB::select($sql,array('id'=>$CityId));
            $CityLongitude = isset($City[0]->longitude) ? $City[0]->longitude : "";
        }
        return $CityLongitude;
    }

    public function getStateName($StateId){
        $StateName = "";
        if($StateId){
            $sql = "SELECT name FROM states WHERE id=:id";
            $State = DB::select($sql,array('id'=>$StateId));
            $StateName = isset($State[0]->name) ? $State[0]->name : "";
        }
        return $StateName;
    }

    public function getCountryName($CountryId){
        $CountryName = "";
        if($CountryId){
            $sql = "SELECT name FROM countries WHERE id=:id";
            $Country = DB::select($sql,array('id'=>$CountryId));
            $CountryName = isset($Country[0]->name) ? $Country[0]->name : "";
        }
        return $CountryName;
    }

    public function getTimeZoneName($TimeZoneId){
        $TimeZoneName = "";
        if($TimeZoneId){
            $sql = "SELECT area FROM master_timezones WHERE id=:id";
            $timezone = DB::select($sql,array('id' => $TimeZoneId));
            $TimeZoneName = isset($timezone[0]->area) ? $timezone[0]->area : "";
        }
        return $TimeZoneName;
    }
}
