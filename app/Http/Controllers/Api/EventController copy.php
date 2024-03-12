<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Libraries\Authenticate;
use Illuminate\Support\Facades\DB;
use App\Models\Event;
use Carbon\Carbon;

class EventController extends Controller
{

    #COUNTRY WISE SEARCH BANNER AND EVENTS(NO RELATION BETWEEN EVENTS AND BANNERS)
    function get_banner_events(Request $request)
    {
        // dd($request);
        $ResponseData = [];
        $ResposneCode = 200;
        $empty = false;
        $message = 'Success';
        $field = '';
        $Events = [];
        $ResponseData['CityName'] = '';
        $ResponseData['CountryId'] = $ResponseData['StateId'] = $ResponseData['CityId'] = 0;

        #GET EVENTS COUNTRY WISE
        $EventSql = "SELECT e.* FROM events AS e WHERE e.active=1 AND e.deleted=0";
        $BannerSql = "SELECT b.* FROM banner AS b WHERE b.active=1";

        $CountryCode = $request->country_code;
        $City = isset($request->city) ? $request->city : '';
        $State = isset($request->state) ? $request->state : '';

        $CityId = isset($request->scity) ? $request->scity : 0;
        $StateId = isset($request->sstate) ? $request->sstate : 0;
        $CountryId = isset($request->scountry) ? $request->scountry : 0;

        if (!empty($CountryCode)) {
            $sSQL = 'SELECT id FROM countries WHERE LOWER(country_code) =:country_code';
            $CountryId = DB::select($sSQL, array('country_code' => strtolower($CountryCode)));
            // dd($CountryId);
            if (sizeof($CountryId) > 0) {
                $EventSql .= ' AND e.country=' . $CountryId[0]->id;
                $BannerSql .= ' AND b.country=' . $CountryId[0]->id;
                $ResponseData['CountryId'] = $CountryId[0]->id;
            }
        }
        if (!empty($City)) {
            $sSQL = 'SELECT id,name FROM cities WHERE LOWER(name) =:name';
            $CityId = DB::select($sSQL, array('name' => strtolower($City)));
            if (sizeof($CityId) > 0) {
                $EventSql .= ' AND e.city=' . $CityId[0]->id;
                $BannerSql .= ' AND b.city=' . $CityId[0]->id;
                $ResponseData['CityId'] = $CityId[0]->id;
                $ResponseData['CityName'] = $CityId[0]->name;
            }
        }
        if (!empty($State)) {
            $sSQL = 'SELECT id FROM states WHERE LOWER(name) =:name';
            $StateId = DB::select($sSQL, array('name' => strtolower($State)));
            if (sizeof($StateId) > 0) {
                $EventSql .= ' AND e.state=' . $StateId[0]->id;
                $BannerSql .= ' AND b.state=' . $StateId[0]->id;
                $ResponseData['StateId'] = $StateId[0]->id;
            }
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
                $EventSql .= ' AND e.start_time BETWEEN ' . $StartDate . ' AND ' . $EndDate;
                $BannerSql .= ' AND b.start_time BETWEEN ' . $StartDate . ' AND ' . $EndDate;
            } else if ($Filter == 'free') {
                $EventSql .= " AND e.is_paid=0";
                $BannerSql .= " AND b.is_paid=0";
            }
        }

        // dd($EventSql,$BannerSql);
        $Events = DB::select($EventSql);
        $Banners = DB::select($BannerSql);

        if (Sizeof($Events) == 0) {
            if (!empty($StateId) && $StateId > 0) {
                $EventSql .= ' AND e.state=' . $StateId;
                $BannerSql .= ' AND b.state=' . $StateId;
            }
        }
        dd($EventSql,$BannerSql);
        $Events = DB::select($EventSql);

        if (Sizeof($Events) == 0) {
            if (!empty($CountryId) && $CountryId > 0) {
                $EventSql .= ' AND e.country=' . $CountryId;
                $BannerSql .= ' AND b.country=' . $CountryId;
            }
        }
        $Events = DB::select($EventSql);

        foreach ($Events as $event) {
            $event->start_date = (!empty($event->start_time)) ? gmdate("d M Y", $event->start_time) : 0;
            $event->banner_image = !empty($event->banner_image) ? url('/') . '/uploads/banner_image/' . $event->banner_image . '' : '';
            $event->logo_image = !empty($event->logo_image) ? url('/') . '/uploads/logo_image/' . $event->logo_image . '' : '';
        }
        foreach ($Banners as $key => $banner) {
            if (!empty((!empty($banner->banner_image)) && ($key <= 4))) {
                $banner->banner_image = !empty($banner->banner_image) ? url('/') . '/uploads/banner_image/' . $banner->banner_image . '' : '';
            }
        }
        // dd($Events,$Banners);

        $ResponseData['eventData'] = $Events;
        $ResponseData['BannerImages'] = $Banners;

        $response = [
            'status' => 'success',
            'data' => $ResponseData,
            'message' => $message
        ];

        return response()->json($response, $ResposneCode);


    }

    public function getEvents(Request $request)
    {
        // return($request->header());

        $ResponseData = [];
        $ResposneCode = 200;
        $empty = false;
        $message = 'Success';
        $field = '';

        $aData = array();
        $aPost = $request->all();

        $Auth = new Authenticate();
        $Auth->apiLog($request);

        $EventId = isset($request->event_id) ? $request->event_id : 0;
        $EventName = isset($request->event_name) ? $request->event_name : '';
        $CityId = isset($request->city) ? $request->city : 0;
        $StateId = isset($request->state) ? $request->state : 0;
        $CountryId = isset($request->country) ? $request->country : 0;

        $sql = "SELECT * from events WHERE active=1 AND deleted=0";
        if ($EventName != "") {
            $sql .= " and name LIKE '%" . $EventName . "%' ";
        }
        if (!empty($EventId)) {
            $sql .= " AND id=" . $EventId;
        }
        if (!empty($CityId)) {
            $sql .= " AND city=" . $CityId;
        } else {
            if (!empty($StateId)) {
                $sql .= " AND state=" . $StateId;
            } elseif (!empty($CountryId)) {
                $sql .= " AND country=" . $CountryId;
            }
        }
        // dd($sql);
        $Events = DB::select($sql);

        foreach ($Events as $event) {
            $event->start_date = (!empty($event->start_time)) ? gmdate("d M Y", $event->start_time) : 0;
            $event->banner_image = !empty($event->banner_image) ? url('/') . '/uploads/banner_image/' . $event->banner_image . '' : '';
            $event->logo_image = !empty($event->logo_image) ? url('/') . '/uploads/logo_image/' . $event->logo_image . '' : '';
        }
        $ResponseData['EventData'] = $Events;

        // $ResponseData['EventData'] = Event::get($aData)->orderBy('id', 'DESC')->paginate(config('custom.page_limit'));

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
        $ResponseData['CountryId'] = $ResponseData['StateId'] = $ResponseData['CityId'] = 0;

        #GET EVENTS COUNTRY WISE
        $EventSql = "SELECT e.* FROM events AS e WHERE e.active=1 AND e.deleted=0";
        $BannerSql = "SELECT b.* FROM banner AS b WHERE b.active=1";

        // $CountryCode = $request->country_code;
        $City = isset($request->city) ? $request->city : '';
        // $State = isset($request->state) ? $request->state : '';
        $CityId = isset($request->scity) ? $request->scity : 0;
        $StateId = isset($request->state) ? $request->state : 0;
        $CountryId = isset($request->country) ? $request->country : 0;

        if (!empty($City)) {
            $sSQL = 'SELECT id,name,state_id,country_id FROM cities WHERE id =:id';
            $CityId = DB::select($sSQL, array('id' => $City));

            $EventSql .= ' AND e.city=' . $City;
            $BannerSql .= ' AND b.city=' . $City;
            if (sizeof($CityId) > 0) {
                $ResponseData['CityName'] = $CityId[0]->name;
                $ResponseData['CountryId'] = $CityId[0]->country_id;
                $ResponseData['StateId'] = $CityId[0]->state_id;
                $ResponseData['CityId'] = $CityId[0]->id;
            }
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
                $EventSql .= ' AND e.start_time BETWEEN ' . $StartDate . ' AND ' . $EndDate;
                $BannerSql .= ' AND b.start_time BETWEEN ' . $StartDate . ' AND ' . $EndDate;
            } else if ($Filter == 'free') {
                $EventSql .= " AND e.is_paid=0";
                $BannerSql .= " AND b.is_paid=0";
            }
        }

        // dd($EventSql,$BannerSql);
        $Events = DB::select($EventSql);
        $Banners = DB::select($BannerSql);

        if (Sizeof($Events) == 0) {
            if (!empty($StateId) && $StateId > 0) {
                $EventSql .= ' AND e.state=' . $StateId;
                $BannerSql .= ' AND b.state=' . $StateId;
            }
        }
        $Events = DB::select($EventSql);

        if (Sizeof($Events) == 0) {
            if (!empty($CountryId) && $CountryId > 0) {
                $EventSql .= ' AND e.country=' . $CountryId;
                $BannerSql .= ' AND b.country=' . $CountryId;
            }
        }
        $Events = DB::select($EventSql);

        foreach ($Events as $event) {
            $event->start_date = (!empty($event->start_time)) ? gmdate("d M Y", $event->start_time) : 0;
            $event->banner_image = !empty($event->banner_image) ? url('/') . '/uploads/banner_image/' . $event->banner_image . '' : '';
            $event->logo_image = !empty($event->logo_image) ? url('/') . '/uploads/logo_image/' . $event->logo_image . '' : '';
        }
        foreach ($Banners as $key => $banner) {
            if (!empty((!empty($banner->banner_image)) && ($key <= 4))) {
                $banner->banner_image = !empty($banner->banner_image) ? url('/') . '/uploads/banner_image/' . $banner->banner_image . '' : '';
            }
        }
        // dd($Events,$Banners);

        $ResponseData['eventData'] = $Events;
        $ResponseData['BannerImages'] = $Banners;

        $response = [
            'status' => 'success',
            'data' => $ResponseData,
            'message' => $message
        ];

        return response()->json($response, $ResposneCode);


    }



}
