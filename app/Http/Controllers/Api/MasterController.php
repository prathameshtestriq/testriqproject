<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class MasterController extends Controller
{


    function getTypes(Request $request)
    {
        $ResponseData = [];
        $ResposneCode = 200;
        $empty = false;
        $message = 'Success';
        $field = '';

        $sql = "SELECT e.* FROM eTypes AS e WHERE e.active=1";
        $AllEventTypes = DB::select($sql);

        foreach ($AllEventTypes as $key => $value) {
            $value->logo = (isset($value->logo) && !empty($value->logo)) ? url('/') . '/assets/img/banner/' . $value->logo : "";
        }

        $ResponseData['AllEventTypes'] = $AllEventTypes;

        $response = [
            'status' => 200,
            'data' => $ResponseData,
            'message' => $message
        ];
        return response()->json($response, $ResposneCode);
    }

    function getCategory(Request $request)
    {
        $ResponseData = [];
        $ResposneCode = 200;
        $empty = false;
        $message = 'Success';
        $field = '';

        $sql = "SELECT c.* FROM category AS c WHERE active=1";
        $Allcategory = DB::select($sql);

        $ResponseData['Allcategory'] = $Allcategory;

        $response = [
            'status' => 200,
            'data' => $ResponseData,
            'message' => $message
        ];
        return response()->json($response, $ResposneCode);
    }

    function getCountry(Request $request)
    {
        $ResponseData = [];
        $ResposneCode = 200;
        $empty = false;
        $message = 'Success';
        $field = '';

        #GET CITY WISE
        $CountryCode = isset($request->country_code) ? $request->country_code : "";

        $sql = "SELECT c.* FROM countries AS c WHERE c.flag=1";
        if (!empty($CountryCode)) {
            $SearchText = strtolower($CountryCode);
            $sql .= " AND LOWER(c.country_code) LIKE '%" . $SearchText . "%' ";
        }
        $AllCountries = DB::select($sql);

        $ResponseData['AllCountries'] = $AllCountries;

        $response = [
            'status' => 200,
            'data' => $ResponseData,
            'message' => $message
        ];

        return response()->json($response, $ResposneCode);
    }

    function getState(Request $request)
    {
        $ResponseData = [];
        $ResposneCode = 200;
        $empty = false;
        $message = 'Success';
        $field = '';

        #GET STATE WISE
        $CountryId = isset($request->country_id) ? $request->country_id : 0;
        $sql = "SELECT s.* FROM states AS s WHERE flag=1";
        if (!empty($CountryId)) {
            $sql .= " AND s.country_id=" . $CountryId;
        }
        $AllState = DB::select($sql);
        $ResponseData['AllState'] = $AllState;

        $response = [
            'status' => 200,
            'data' => $ResponseData,
            'message' => $message
        ];

        return response()->json($response, $ResposneCode);
    }

    function getCity(Request $request)
    {
        $ResponseData = [];
        $ResposneCode = 200;
        $empty = false;
        $message = 'Success';
        $field = '';
        $Events = [];

        #GET CITY WISE
        $CountryCode = isset($request->country_code) ? $request->country_code : "";
        $CountryId = isset($request->country_id) ? $request->country_id : "";
        $StateId = isset($request->state_id) ? $request->state_id : "";

        $sql = "SELECT c.* FROM cities AS c WHERE show_flag=1";
        if (!empty($CountryCode)) {
            $SearchText = strtolower($CountryCode);
            $sql .= " AND LOWER(c.country_code) LIKE '%" . $SearchText . "%' ";
        }
        if (!empty($request->search_city)) {
            $SearchText = strtolower($request->search_city);
            $sql .= " AND LOWER(c.name) LIKE '%" . $SearchText . "%' ";
        }
        if (!empty($StateId)) {
            $sql .= " AND c.state_id=" . $StateId;
        }
        if (!empty($CountryId)) {
            $sql .= " AND c.country_id=" . $CountryId;
        }
        $AllCities = DB::select($sql);

        $ResponseData['AllCities'] = $AllCities;

        $response = [
            'status' => 200,
            'data' => $ResponseData,
            'message' => $message
        ];

        return response()->json($response, $ResposneCode);
    }

    function getTimezone(Request $request)
    {
        $ResponseData = [];
        $ResposneCode = 200;
        $empty = false;
        $message = 'Success';
        $field = '';
        $Events = [];
        $TimezoneId = isset($request->timezone_id) ? $request->timezone_id : 0;

        $sql = "SELECT c.* FROM master_timezones AS c WHERE active=1";
        if (!empty($TimezoneId)) {
            $sql .= " AND id=" . $TimezoneId;
        }
        $AllTimezones = DB::select($sql);
        $ResponseData['AllTimezones'] = $AllTimezones;
        $response = [
            'status' => 200,
            'data' => $ResponseData,
            'message' => $message
        ];
        return response()->json($response, $ResposneCode);
    }

    function getDistanceOfEvents(Request $request)
    {
        $ResponseData = [];
        $ResposneCode = 200;
        $empty = false;
        $message = 'Success';
        $field = '';
        $Events = [];

        $sql = "SELECT DISTINCT(distance) FROM `events` WHERE distance IS NOT null";
        $AllDistances = DB::select($sql);
        $ResponseData['AllDistances'] = $AllDistances;
        $response = [
            'status' => 200,
            'data' => $ResponseData,
            'message' => $message
        ];
        return response()->json($response, $ResposneCode);
    }


}
