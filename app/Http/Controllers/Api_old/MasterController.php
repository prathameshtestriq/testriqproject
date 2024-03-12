<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class MasterController extends Controller
{

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
            $sql .=" AND LOWER(c.country_code) LIKE '%" . $SearchText . "%' ";
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
            $sql .=" AND s.country_id=".$CountryId;
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
        $StateId = isset($request->state_id) ? $request->state_id : "";

        $sql = "SELECT c.* FROM cities AS c WHERE show_flag=1";
        if (!empty($CountryCode)) {
            $SearchText = strtolower($CountryCode);
            $sql .=" AND LOWER(c.country_code) LIKE '%" . $SearchText . "%' ";
        }
        if (!empty($request->search_city)) {
            $SearchText = strtolower($request->search_city);
            $sql .=" AND LOWER(c.name) LIKE '%" . $SearchText . "%' ";
        }
        if (!empty($StateId)) {
            $sql .=" AND c.state_id=".$StateId;
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
}
