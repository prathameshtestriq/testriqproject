<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Libraries\Authenticate;
use Illuminate\Support\Facades\DB;
use App\Models\Event;
use Carbon\Carbon;

class EventController extends Controller
{
    public function getEvents(Request $request)
    {
        // return($request->header());

        $ResponseData = [];
        $ResposneCode = 200;
        $empty = false;
        $message = 'Success';
        $field = '';

        $aToken = app('App\Http\Controllers\LoginController')->validate_request($request);
        // dd($aToken);
        if ($aToken['code'] == 200) {
            $aData = array();
            $aPost = $request->all();

            $Auth = new Authenticate();
            $Auth->apiLog($request);

            $ResponseData['eventData'] = Event::get($aData)->orderBy('id', 'DESC')->paginate(config('custom.page_limit'));
            // dd($ResponseData);
        } else {
            $ResposneCode = $aToken['code'];
            $message = $aToken['message'];
        }

        $response = [
            'status' => 'success',
            'data' => $ResponseData,
            'message' => $message
        ];

        return response()->json($response, $ResposneCode);

    }

    public function get_data_location_wise(Request $request)
    {
        // dd($request);
        $ResponseData = [];
        $ResposneCode = 200;
        $empty = false;
        $message = 'Success';
        $field = '';
        $Events = [];
        $ResponseData['CityName'] = '';

        #GET EVENTS COUNTRY WISE
        $sql = "SELECT e.* FROM events AS e ";

        $CountryCode = $request->country_code;
        $City = isset($request->city) ? $request->city : '';

        $sSQL = 'SELECT id FROM countries WHERE LOWER(country_code) =:country_code';
        $CountryId = DB::select($sSQL, array('country_code' => strtolower($CountryCode)));
        // dd($CountryId);
        if (sizeof($CountryId) > 0) {
            $SQL = $sql . 'WHERE e.active=1 AND e.country=:country';
            $Events = DB::select($SQL, array('country' => $CountryId[0]->id));
            // dd($SQL,$Events);
        }

        #SEARCH EVENTS WITH DROPDOWN CITIES
        if (!empty($City)) {
            $SQL = $sql . 'WHERE e.active=1 AND e.city=:city';
            $Events = DB::select($SQL, array('city' => $City));

            $SQL2 = 'SELECT name FROM cities WHERE id=:city';
            $FieldValue = DB::select($SQL2, array('city' => $City));
            // dd($FieldValue);
            $CityName = $FieldValue[0]->name;
            $ResponseData['CityName'] = strtolower($CityName);
        }

        #GET CITY WITH DROPDOWN COUNTRY WISE
        $Cities = array();
        if (sizeof($CountryId) > 0) {
            $SQL = 'SELECT * FROM cities WHERE country_id=:country_id AND show_flag=1'; //show_flag is show city flag
            $Cities = DB::select($SQL, array('country_id' => $CountryId[0]->id));
        }
        $ResponseData['cityData'] = $Cities;


        #GET EVENTS WITH INPUT CITY
        $DropdownArr = array();
        if (!empty($request->input_city)) {
            $SearchText = strtolower($request->input_city);
            // dd($SearchText);
            $SQL3 = $sql . "LEFT JOIN cities AS c ON e.city = c.id
                          LEFT JOIN states AS s ON e.state = s.id
                          WHERE e.active=1 AND
                             LOWER(e.name) LIKE '%" . $SearchText . "%'
                          OR LOWER(c.name) LIKE '%" . $SearchText . "%'
                          OR LOWER(s.name) LIKE '%" . $SearchText . "%'
                         ";
            $Events = DB::select($SQL3);
            // dd($SQL3,$Events);

            #GET DATA FRO DROPDOWN ON TYPE
            $SQL4 = "SELECT e.id AS event_id,e.name AS event_name,
                            c.id AS city_id,c.name AS city_name,
                            s.id AS state_id,s.name AS state_name
                        FROM events AS e
                        LEFT JOIN cities AS c ON e.city = c.id
                        LEFT JOIN states AS s ON e.state = s.id
                        WHERE e.active=1 AND
                        LOWER(e.name) LIKE '%" . $SearchText . "%'
                        OR LOWER(c.name) LIKE '%" . $SearchText . "%'
                        OR LOWER(s.name) LIKE '%" . $SearchText . "%'
                    ";
            $DropdownArr = DB::select($SQL4);
            // dd($SQL4,$DropdownArr);
            $ResponseData['DropdownArr'] = $DropdownArr;
        }
        foreach ($Events as $value) {
            $value->banner_image = !empty($value->banner_image) ? url('/') . '/uploads/banner_image/' . $value->banner_image . '' : '';
            $value->logo_image = !empty($value->logo_image) ? url('/') . '/uploads/logo_image/' . $value->logo_image . '' : '';
        }

        if (!empty($request->filter)) {
            $Filter = $request->filter;

            switch ($Filter) {
                case 'today':
                    $StartDate = strtotime(date('Y-m-d 00:00:00'));
                    $EndDate = strtotime(date('Y-m-d 23:59:59'));
                    break;

                case 'tomorrow':
                    $StartDate = strtotime(date('Y-m-d 00:00:00', strtotime("+1 day")));
                    $EndDate = strtotime(date('Y-m-d 23:59:59', strtotime("+1 day")));
                    break;

                case 'month':
                    $Start = new Carbon('first day of this month');
                    $StartDate = strtotime($Start->startOfMonth());

                    $End = new Carbon('last day of this month');
                    $EndDate = strtotime($End->endOfMonth());
                    break;

                case 'week':
                    $StartDate = strtotime(date('Y-m-d 00:00:00', strtotime('monday this week')));
                    $EndDate = strtotime(date('Y-m-d 23:59:59', strtotime('saturday this week')));
                    break;

                case 'weekend':
                    $StartDate = strtotime(date('Y-m-d 00:00:00', strtotime('saturday this week')));
                    $EndDate = strtotime(date('Y-m-d 23:59:59', strtotime('sunday this week')));
                    break;

                default:
                    break;
            }
            // dd($StartDate, $EndDate);
            if (isset($StartDate) && isset($EndDate)) {
                $SQL5 = $sql . 'WHERE e.active=1 AND e.start_time BETWEEN ' . $StartDate . ' AND ' . $EndDate;
            }else if($Filter == 'free'){
                $SQL5 =  $sql ."WHERE e.active=1 AND e.is_paid=0";
            }
            // dd($SQL5,$Events);
            $Events = DB::select($SQL5);
        }
        $ResponseData['eventData'] = $Events;

        $response = [
            'status' => 'success',
            'data' => $ResponseData,
            'message' => $message
        ];

        return response()->json($response, $ResposneCode);
    }

}
