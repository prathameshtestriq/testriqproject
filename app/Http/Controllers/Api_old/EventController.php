<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Libraries\Authenticate;
use Illuminate\Support\Facades\DB;
use App\Models\Master;
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
        $FewSuggestionFlag = 0;

        #IF USER IS LOGIN GET USER ID
        // $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        // $UserId = $aToken['data']->ID;
        $Auth = new Authenticate();
        #ELSE USER NOT LOGIN
        $aToken = $Auth->decode_token($request->header('Authorization'));
        $UserId = 0;
        if (!empty($aToken)) {
            $UserId = $aToken->ID;
        }
        // dd($aToken,$UserId);

        #GET EVENTS COUNTRY WISE
        $EventSql = "SELECT e.* FROM events AS e WHERE e.active=1 AND e.deleted=0";
        $BannerSql = "SELECT b.* FROM banner AS b WHERE b.active=1";

        $CountryCode = $request->country_code;
        $City = isset($request->city) ? $request->city : '';
        $State = isset($request->state) ? $request->state : '';

        $city_id = isset($request->scity) ? $request->scity : 0;
        $state_id = isset($request->sstate) ? $request->sstate : 0;
        $country_id = isset($request->scountry) ? $request->scountry : 0;
        // dd($state_id);
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
        // dd($EventSql,$BannerSql);
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
        // dd($EventSql,$BannerSql,$ResponseData);

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

        #NEW SECTION STARTS IF EVENTS AND BANNERS GETTINGS EMPTY
        ##IF NO EVENTS OR BANNERS ARE FOUND FOR THE GIVEN TIME FRAME, THEN WE SHOW ALL AVAILABLE ON
        $NewState_id = (!empty($state_id)) ? $state_id : $ResponseData['StateId'];
        if (Sizeof($Events) == 0) {
            // $message = 'Few Suggestions';
            $FewSuggestionFlag = 1;

            $EventSql = "SELECT e.* FROM events AS e WHERE e.active=1 AND e.deleted=0";
            if (!empty($NewState_id)) {
                $EventSql .= ' AND e.state=' . $NewState_id;
            }
            $Events = DB::select($EventSql);
        }

        if (Sizeof($Banners) == 0) {
            $BannerSql = "SELECT b.* FROM banner AS b WHERE b.active=1";
            if (!empty($NewState_id)) {
                $BannerSql .= ' AND b.state=' . $NewState_id;
            }
            $Banners = DB::select($BannerSql);
        }
        // dd($EventSql,$BannerSql,$Banners);
        $NewCountry_id = (!empty($country_id)) ? $country_id : $ResponseData['CountryId'];
        if (Sizeof($Events) == 0) {
            $EventSql = "SELECT e.* FROM events AS e WHERE e.active=1 AND e.deleted=0";
            if (!empty($NewCountry_id) && $NewCountry_id > 0) {
                $EventSql .= ' AND e.country=' . $NewCountry_id;
            }
            $Events = DB::select($EventSql);
        }
        if (Sizeof($Banners) == 0) {
            $BannerSql = "SELECT b.* FROM banner AS b WHERE b.active=1";
            if (!empty($NewCountry_id)) {
                $BannerSql .= ' AND b.country=' . $NewCountry_id;
            }
            $Banners = DB::select($BannerSql);
        }
        // dd($EventSql,$BannerSql,$Banners);

        $master = new Master();
        $e = new Event();
        foreach ($Events as $event) {
            $event->start_date = (!empty($event->start_time)) ? gmdate("d M Y", $event->start_time) : 0;
            $event->start_time_event = (!empty($event->start_time)) ? date("h:i A", $event->start_time) : "";
            $event->end_date_event = (!empty($event->end_time)) ? date("h:i A", $event->end_time) : 0;

            $event->banner_image = !empty($event->banner_image) ? url('/') . '/uploads/banner_image/' . $event->banner_image . '' : '';
            $event->logo_image = !empty($event->logo_image) ? url('/') . '/uploads/logo_image/' . $event->logo_image . '' : '';
            $event->background_image = url('/') . '/uploads/images/banner-bg-2.jpg';
            $event->city_name = !empty($event->city) ? $master->getCityName($event->city) : "";
            $event->state_name = !empty($event->state) ? $master->getStateName($event->state) : "";
            $event->country_name = !empty($event->country) ? $master->getCountryName($event->country) : "";

            $event->latitude = !empty($event->city) ? $master->getCityLatitude($event->city) : "";
            $event->longitude = !empty($event->city) ? $master->getCityLongitude($event->city) : "";

            #FOLLOW(WISHLIST)
            $event->is_follow = !empty($UserId) ? $e->isFollowed($event->id, $UserId) : 0;

            #GET ALL TICKETS
            $SQL = "SELECT COUNT(event_id) AS no_of_tickets,min(ticket_price) AS min_price,max(ticket_price) AS max_price FROM event_tickets WHERE event_id=:event_id AND active = 1 ORDER BY ticket_price";
            $Tickets = DB::select($SQL, array('event_id' => $event->id));

            $event->min_price = (sizeof($Tickets) > 0) ? $Tickets[0]->min_price : 0;
            $event->max_price = (sizeof($Tickets) > 0) ? $Tickets[0]->max_price : 0;
            $event->no_of_tickets = (sizeof($Tickets) > 0) ? $Tickets[0]->no_of_tickets : 0;
        }//die;
        $BannerImages = [];
        foreach ($Banners as $key => $banner) {
            if (!empty((!empty($banner->banner_image)) && ($key <= 4))) {
                $banner->banner_url = !empty($banner->banner_url) ? $banner->banner_url : "";
                $banner->banner_image = !empty($banner->banner_image) ? url('/') . '/uploads/banner_image/' . $banner->banner_image . '' : '';
                $BannerImages[$key]['url'] = $banner->banner_url;
                $BannerImages[$key]['img'] = $banner->banner_image;
            }
        }
        // dd($Events,$Banners);

        $ResponseData['eventData'] = $Events;
        $ResponseData['BannerImages'] = $BannerImages;
        // dd($ResponseData['BannerImages']);
        $response = [
            'status' => 200,
            'data' => $ResponseData,
            'message' => $message,
            'FewSuggestionFlag' => $FewSuggestionFlag
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
        $FewSuggestionFlag = 0;
        $aData = array();
        $aPost = $request->all();

        $Auth = new Authenticate();
        $Auth->apiLog($request);

        #IF USER IS LOGIN GET USER ID
        // $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        // $UserId = $aToken['data']->ID;

        #ELSE USER NOT LOGIN
        $aToken = $Auth->decode_token($request->header('Authorization'));
        $UserId = 0;
        if (!empty($aToken)) {
            $UserId = $aToken->ID;
        }
        // dd($aToken,$UserId);

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
            $FewSuggestionFlag = 1;
            $sql .= " AND id=" . $EventId;
        }
        // if (!empty($CityId)) {
        //     $sql .= " AND city=" . $CityId;
        // }
        // $sql .= " LIMIT 5";
        $Events = DB::select($sql);

        #IF EVENTS ARE NOT FOUND IN SELECTED CITY
        // if (Sizeof($Events) == 0) {
        //     $FewSuggestionFlag = 1;

        //     $EventSql = "SELECT e.* FROM events AS e WHERE e.active=1 AND e.deleted=0";
        //     if ($EventName != "") {
        //         $EventSql .= " AND e.name LIKE '%" . $EventName . "%' ";
        //     }
        //     if (!empty($StateId)) {
        //         $EventSql .= ' AND e.state=' . $StateId;
        //     }
        //     $EventSql .= " LIMIT 5";

        //     $Events = DB::select($EventSql);
        // }

        // if (Sizeof($Events) == 0) {
        //     $EventSql = "SELECT e.* FROM events AS e WHERE e.active=1 AND e.deleted=0";
        //     if ($EventName != "") {
        //         $EventSql .= " AND e.name LIKE '%" . $EventName . "%' ";
        //     }
        //     if (!empty($CountryId) && $CountryId > 0) {
        //         $EventSql .= ' AND e.country=' . $CountryId;
        //     }
        //     $EventSql .= " LIMIT 5";

        //     $Events = DB::select($EventSql);
        // }
        $master = new Master();
        $e = new Event();
        foreach ($Events as $event) {
            $event->start_date = (!empty($event->start_time)) ? gmdate("d M Y", $event->start_time) : 0;
            $event->start_time_event = (!empty($event->start_time)) ? date("h:i A", $event->start_time) : "";
            $event->end_date_event = (!empty($event->end_time)) ? date("h:i A", $event->end_time) : 0;

            $event->banner_image = !empty($event->banner_image) ? url('/') . '/uploads/banner_image/' . $event->banner_image . '' : '';
            $event->logo_image = !empty($event->logo_image) ? url('/') . '/uploads/logo_image/' . $event->logo_image . '' : '';
            $event->background_image = url('/') . '/uploads/images/banner-bg-2.jpg';
            $event->city_name = !empty($event->city) ? $master->getCityName($event->city) : "";
            $event->state_name = !empty($event->state) ? $master->getStateName($event->state) : "";
            $event->country_name = !empty($event->country) ? $master->getCountryName($event->country) : "";

            $event->latitude = !empty($event->city) ? $master->getCityLatitude($event->city) : "";
            $event->longitude = !empty($event->city) ? $master->getCityLongitude($event->city) : "";

            #FOLLOW(WISHLIST)
            $event->is_follow = !empty($UserId) ? $e->isFollowed($event->id, $UserId) : 0;
        }

        $ResponseData['EventData'] = $Events;

        // $ResponseData['EventData'] = Event::get($aData)->orderBy('id', 'DESC')->paginate(config('custom.page_limit'));

        $response = [
            'status' => 200,
            'data' => $ResponseData,
            'message' => $message,
            'FewSuggestionFlag' => $FewSuggestionFlag
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
        $FewSuggestionFlag = 0;

        #IF USER IS LOGIN GET USER ID
        // $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        // $UserId = $aToken['data']->ID;
        $Auth = new Authenticate();
        #ELSE USER NOT LOGIN
        $aToken = $Auth->decode_token($request->header('Authorization'));
        $UserId = 0;
        if (!empty($aToken)) {
            $UserId = $aToken->ID;
        }
        // dd($aToken,$UserId);

        #GET EVENTS COUNTRY WISE
        $EventSql = "SELECT e.* FROM events AS e WHERE e.active=1 AND e.deleted=0";
        $BannerSql = "SELECT b.* FROM banner AS b WHERE b.active=1";

        // $CountryCode = $request->country_code;
        $City = isset($request->city) ? $request->city : '';
        // $State = isset($request->state) ? $request->state : '';
        $city_id = isset($request->scity) ? $request->scity : 0;
        $state_id = isset($request->state) ? $request->state : 0;
        $country_id = isset($request->country) ? $request->country : 0;

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

        #NEW SECTION STARTS IF EVENTS AND BANNERS GETTINGS EMPTY
        ##IF NO EVENTS OR BANNERS ARE FOUND FOR THE GIVEN TIME FRAME, THEN WE SHOW ALL AVAILABLE ON
        $NewState_id = (!empty($state_id)) ? $state_id : $ResponseData['StateId'];
        if (Sizeof($Events) == 0) {
            $FewSuggestionFlag = 1;
            $EventSql = "SELECT e.* FROM events AS e WHERE e.active=1 AND e.deleted=0";
            if (!empty($NewState_id)) {
                $EventSql .= ' AND e.state=' . $NewState_id;
            }
            $Events = DB::select($EventSql);
        }
        // dd($EventSql,$BannerSql);
        if (Sizeof($Banners) == 0) {
            $BannerSql = "SELECT b.* FROM banner AS b WHERE b.active=1";
            if (!empty($NewState_id)) {
                $BannerSql .= ' AND b.state=' . $NewState_id;
            }
            $Banners = DB::select($BannerSql);
        }

        $NewCountry_id = (!empty($country_id)) ? $country_id : $ResponseData['CountryId'];
        if (Sizeof($Events) == 0) {
            $EventSql = "SELECT e.* FROM events AS e WHERE e.active=1 AND e.deleted=0";
            if (!empty($NewCountry_id) && $NewCountry_id > 0) {
                $EventSql .= ' AND e.country=' . $NewCountry_id;
            }
            $Events = DB::select($EventSql);
        }
        if (Sizeof($Banners) == 0) {
            $BannerSql = "SELECT b.* FROM banner AS b WHERE b.active=1";
            if (!empty($NewCountry_id)) {
                $BannerSql .= ' AND b.country=' . $NewCountry_id;
            }
            $Banners = DB::select($BannerSql);
        }

        $master = new Master();
        $e = new Event();
        foreach ($Events as $event) {
            $event->start_date = (!empty($event->start_time)) ? gmdate("d M Y", $event->start_time) : 0;
            $event->start_time_event = (!empty($event->start_time)) ? date("h:i A", $event->start_time) : "";
            $event->end_date_event = (!empty($event->end_time)) ? date("h:i A", $event->end_time) : 0;

            $event->banner_image = !empty($event->banner_image) ? url('/') . '/uploads/banner_image/' . $event->banner_image . '' : '';
            $event->logo_image = !empty($event->logo_image) ? url('/') . '/uploads/logo_image/' . $event->logo_image . '' : '';
            $event->background_image = url('/') . '/uploads/images/banner-bg-2.jpg';
            $event->city_name = !empty($event->city) ? $master->getCityName($event->city) : "";
            $event->state_name = !empty($event->state) ? $master->getStateName($event->state) : "";
            $event->country_name = !empty($event->country) ? $master->getCountryName($event->country) : "";

            $event->latitude = !empty($event->city) ? $master->getCityLatitude($event->city) : "";
            $event->longitude = !empty($event->city) ? $master->getCityLongitude($event->city) : "";

            #FOLLOW(WISHLIST)
            $event->is_follow = !empty($UserId) ? $e->isFollowed($event->id, $UserId) : 0;

            #GET ALL TICKETS
            $SQL = "SELECT COUNT(event_id) AS no_of_tickets,min(ticket_price) AS min_price,max(ticket_price) AS max_price FROM event_tickets WHERE event_id=:event_id AND active = 1 ORDER BY ticket_price";
            $Tickets = DB::select($SQL, array('event_id' => $event->id));

            $event->min_price = (sizeof($Tickets) > 0) ? $Tickets[0]->min_price : 0;
            $event->max_price = (sizeof($Tickets) > 0) ? $Tickets[0]->max_price : 0;
            $event->no_of_tickets = (sizeof($Tickets) > 0) ? $Tickets[0]->no_of_tickets : 0;
        }
        $BannerImages = [];
        foreach ($Banners as $key => $banner) {
            if (!empty((!empty($banner->banner_image)) && ($key <= 4))) {
                $banner->banner_url = !empty($banner->banner_url) ? $banner->banner_url : "";
                $banner->banner_image = !empty($banner->banner_image) ? url('/') . '/uploads/banner_image/' . $banner->banner_image . '' : '';
                $BannerImages[$key]['url'] = $banner->banner_url;
                $BannerImages[$key]['img'] = $banner->banner_image;
            }
        }
        // dd($Events,$Banners);
        $ResponseData['eventData'] = $Events;
        $ResponseData['BannerImages'] = $BannerImages;

        $response = [
            'status' => 'success',
            'data' => $ResponseData,
            'message' => $message,
            'FewSuggestionFlag' => $FewSuggestionFlag
        ];

        return response()->json($response, $ResposneCode);
    }


    public function createEvent(Request $request)
    {

        // Event basic information Tab keys
        // ------------------------------------------------------
        /// event_info_status - (public - 1/ private - 2/ draft - 3)
        // event_name  - text - name
        /// display_name_status - 0/1 - event_visibilty
        /// display_name - text- event_display_name
        // category_id  - array(0:1,1:2,2:3)
        // event_url    - text - event_url
        // event_type   - int - event_type
        // created_by   - int - created_by
        // dd('hello');

        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $empty = false;
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        // dd($aToken['data']->ID);

        if ($aToken['code'] == 200) {
            $aPost = $request->all();
            $Auth = new Authenticate();
            $Auth->apiLog($request);
            $UserId = $aToken['data']->ID;

            if (empty($aPost['event_info_status'])) {
                $empty = true;
                $field = 'Event Status';
            }
            if (empty($aPost['event_name'])) {
                $empty = true;
                $field = 'Event Name';
            }
            if (!$empty) {
                //ADD EVENT CODE
                $EventStatus = isset($request->event_info_status) ? $request->event_info_status : 0;
                $EventName = isset($request->event_name) ? $request->event_name : "";
                $EventDisplayStatus = isset($request->display_name_status) ? $request->display_name_status : 0;
                $EventDisplayName = isset($request->display_name) ? $request->display_name : "";
                $Category = isset($request->category_id) ? $request->category_id : [];
                $EventUrl = isset($request->event_url) ? $request->event_url : "";
                $EventType = isset($request->event_type) ? $request->event_type : 0;

                $Bindings = array(
                    "event_info_status" => $EventStatus,
                    "event_name" => $EventName,
                    "display_name_status" => $EventDisplayStatus,
                    "display_name" => $EventDisplayName,
                    "event_url" => $EventUrl,
                    "event_type" => $EventType,
                    "created_by" => $UserId
                );
                $SQL = "INSERT INTO events (event_info_status,name,event_visibilty,event_display_name,event_url,event_type,created_by) VALUES(:event_info_status,:event_name,:display_name_status,:display_name,:event_url,:event_type,:created_by)";
                DB::insert($SQL, $Bindings);
                $EventId = DB::getPdo()->lastInsertId();

                if (!empty($Category) && !empty($EventId)) {
                    foreach ($Category as $value) {
                        $sql = "INSERT INTO event_category (event_id, category_id,created_by) VALUES(:event_id,:category_id,:created_by)";
                        $Bind = array(
                            "event_id" => $EventId,
                            "category_id" => $value,
                            "created_by" => $UserId
                        );
                        DB::insert($sql, $Bind);
                    }
                }
                $ResposneCode = 200;
                $message = "Basic inforamtion of event added successfully";
            } else {
                $ResposneCode = 400;
                $message = $field . ' is empty';
            }
        } else {
            $ResposneCode = $aToken['code'];
            $message = $aToken['message'];
        }
        $response = [
            'success' => $ResposneCode,
            'data' => $ResponseData,
            'message' => $message
        ];
        return response()->json($response, $ResposneCode);

    }
   
 
    public function updateEventDescription(Request $request, $id = null)
    {
      //  dd($request->all());
        $responseData = [];
        $response['message'] = "";
        $responseCode = 400;
        $empty = false;
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
    
        if ($aToken['code'] == 200) {
            $aPost = $request->all();
            $auth = new Authenticate();
            $auth->apiLog($request);
            $userId = $aToken['data']->ID;
    
            if (!$empty) {
                $description = isset($request->description) ? $request->description : '';
                $event_keywords = isset($request->event_keywords)? $request->event_keywords:'';
    
                if (preg_match("/^[a-zA-Z-' ]*$/", $description)) {
                    if (preg_match("/^[a-zA-Z-' ]*$/", $event_keywords)) {
                    $banner_image_name = '';
                    $logo_image_name = '';
    
                    if (!empty($request->file('banner_image'))) {
                        $Path = public_path('uploads/banner_images/');
                        $banner_image = $request->file('banner_image');
                        $ImageExtension = $banner_image->getClientOriginalExtension();
                        $url = env('APP_URL'); 
                        $final_url = $url.'/uploads/banner_images';
                        $banner_image_name = $final_url.'/'.strtotime('now') . '_banner.' . $ImageExtension;
                       // $banner_image->move(public_path('uploads/waste_collection_booking_images/'), $final_url.'/'.$banner_image_name);
                       $banner_image->move($Path, $banner_image_name);
                    }
    
                    if (!empty($request->file('logo_image'))) {
                        $Path = public_path('uploads/logo_images/');
                        $logo_image = $request->file('logo_image');
                        $ImageExtension = $logo_image->getClientOriginalExtension();
                        $url = env('APP_URL'); 
                        $final_url = $url.'/uploads/logo_images';
                        $logo_image_name = $final_url.'/'.strtotime('now') . '_log.' . $ImageExtension;
                        $logo_image->move($Path, $logo_image_name);
                    }
    
                    $bindings = [
                        "description" => $description,
                        "event_keywords" => $event_keywords,
                        "id" => $id
                    ];
    
                    $sql = 'UPDATE events SET description=:description,event_keywords = :event_keywords WHERE id=:id';
                  //  dd($sql);
                    DB::update($sql, $bindings);
    
                    if ($banner_image_name) {
                        $sSQLImg = 'UPDATE events SET banner_image = :banner_image WHERE id=:id';
                        DB::update($sSQLImg, ['banner_image' => $banner_image_name, 'id' => $id]);
                    }
    
                    if ($logo_image_name) {
                        $sSQLImg = 'UPDATE events SET logo_image = :logo_image WHERE id=:id';
                        DB::update($sSQLImg, ['logo_image' => $logo_image_name, 'id' => $id]);
                    }
    
                    $sql2 = 'SELECT * FROM events WHERE id=:id';
                    $responseData = DB::select($sql2, ['id' => $id]);

                   // dd($responseData);
    
                    foreach ($responseData as $value) {
                        $value->banner_image = (!empty($value->banner_image)) ? env('ATHLETE_BANNER_PATH') . $value->banner_image . '' : '';
                        $value->logo_image = (!empty($value->logo_image)) ? env('ATHLETE_LOGO_PATH') . $value->logo_image . '' : '';
                    }
    
                    $message = 'Event Details updated successfully';
                    $responseCode = 200;
                } else {
                    $message = 'Invalid description format';
                }
            }
        } else {
                $responseCode = $aToken['code'];
                $message = $aToken['message'];
            }
    
            $response = [
                'success' => $responseCode,
                'data' => $responseData,
                'message' => $message
            ];
    
            return response()->json($response, $responseCode);
        }
    }
}


