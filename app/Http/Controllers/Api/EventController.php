<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Libraries\Authenticate;
use Illuminate\Support\Facades\DB;
use App\Models\Master;
use App\Models\Event;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{

    function ManipulateEvents($Events, $UserId = 0)
    {
        // dd($Events,$UserId);
        $master = new Master();
        $e = new Event();
        foreach ($Events as $event) {
            $event->name = !empty($event->name) ? ucwords($event->name) : "";
            $event->display_name = !empty($event->name) ? (strlen($event->name) > 40 ? ucwords(substr($event->name, 0, 40)) . "..." : ucwords($event->name)) : "";
            // $event->event_description = !empty($event->event_description) ? html_entity_decode(strip_tags($event->event_description)) : "";

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
            $SQL = "SELECT COUNT(event_id) AS no_of_tickets,min(ticket_price) AS min_price,max(ticket_price) AS max_price,max(early_bird) AS early_bird FROM event_tickets WHERE event_id=:event_id AND active = 1 AND is_deleted = 0 ORDER BY ticket_price";
            $Tickets = DB::select($SQL, array('event_id' => $event->id));

            $event->min_price = (sizeof($Tickets) > 0) ? (!empty($Tickets[0]->min_price) ? $Tickets[0]->min_price : 0) : 0;
            $event->max_price = (sizeof($Tickets) > 0) ? (!empty($Tickets[0]->max_price) ? $Tickets[0]->max_price : 0) : 0;
            $event->no_of_tickets = (sizeof($Tickets) > 0) ? (!empty($Tickets[0]->no_of_tickets) ? $Tickets[0]->no_of_tickets : 0) : 0;
            $event->early_bird = (sizeof($Tickets) > 0) ? (!empty($Tickets[0]->early_bird) ? $Tickets[0]->early_bird : 0) : 0;
            //event start month
            $event->start_event_month = (!empty($event->start_time)) ? gmdate("M", $event->start_time) : 0;
            //event start d
            $event->start_event_date = (!empty($event->start_time)) ? gmdate("d", $event->start_time) : 0;

            //registration starting date
            $event->registration_start_date = (!empty($event->registration_start_time)) ? gmdate("d F Y", $event->registration_start_time) : 0;
            $event->registration_start_date_time = (!empty($event->registration_start_time)) ? date("h:i A", $event->registration_start_time) : "";

            //registration closing date
            $event->registration_end_date = (!empty($event->registration_end_time)) ? gmdate("d F Y", $event->registration_end_time) : 0;
            $event->registration_end_date_time = (!empty($event->registration_end_time)) ? date("h:i A", $event->registration_end_time) : "";

            #GETTING EVENT IMAGES
            $ImageQry = "SELECT * FROM event_images WHERE event_id=:event_id";
            $EventImg = DB::select($ImageQry, array('event_id' => $event->id));
            $EventImgArr = [];
            if (sizeof($EventImg) > 0) {
                foreach ($EventImg as $value) {
                    $EventImgArr[] = !empty($value->image) ? url('/') . '/uploads/event_images/' . $value->image : "";
                }
            }
            $event->event_images = $EventImgArr;

            #EVENT CATEGORIES
            $event->category = $e->getCategoryDetails($event->id);
            $event->types = $e->getTypeDetails($event->id);
        }
        // dd($Events);
        return $Events;
    }

    #COUNTRY WISE SEARCH BANNER AND EVENTS(NO RELATION BETWEEN EVENTS AND BANNERS)
    function get_banner_events(Request $request)
    {
        // dd($request);
        $ResponseData = [];
        $ResposneCode = 200;
        $empty = false;
        $message = 'Success';
        $field = '';
        $Events = $Banners = $UpcomingEvents = [];
        $ResponseData['CityName'] = '';
        $ResponseData['CountryId'] = $ResponseData['StateId'] = $ResponseData['CityId'] = 0;
        $FewSuggestionFlag = 0;
        $NowTime = strtotime('now');
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

        $HomeFlag = isset($request->home_flag) ? $request->home_flag : 0;

        #GET EVENTS COUNTRY WISE
        $EventSql = "SELECT e.* FROM events AS e WHERE e.active=1 AND e.deleted=0 AND e.event_info_status=1";
        $UpcomingSql = "SELECT * from events AS u WHERE u.active=1 AND u.deleted=0 AND u.start_time >=:start_time";
        // $UpcomingEvents = DB::select($UpcomingSql, array('start_time' => $NowTime));
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
                $UpcomingSql .= ' AND u.country=' . $CountryId[0]->id;
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
                $UpcomingSql .= ' AND u.city=' . $CityId[0]->id;
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
                $UpcomingSql .= ' AND u.state=' . $StateId[0]->id;
                $ResponseData['StateId'] = $StateId[0]->id;
            }
        }

        // dd($EventSql,$BannerSql);
        if(!empty($HomeFlag)){
            $EventSql .= ' Limit 8';
        }
        $Events = DB::select($EventSql);
        $Banners = DB::select($BannerSql);
        if(!empty($HomeFlag)){
            $UpcomingSql .= ' Limit 8';
        }
        $UpcomingEvents = DB::select($UpcomingSql, array('start_time' => $NowTime));

        #NEW SECTION STARTS IF EVENTS AND BANNERS GETTINGS EMPTY
        ##IF NO EVENTS OR BANNERS ARE FOUND FOR THE GIVEN TIME FRAME, THEN WE SHOW ALL AVAILABLE ON
        $NewState_id = (!empty($state_id)) ? $state_id : $ResponseData['StateId'];
        if (Sizeof($Events) == 0) {
            // $message = 'Few Suggestions';
            $FewSuggestionFlag = 1;

            $EventSql = "SELECT e.* FROM events AS e WHERE e.active=1 AND e.deleted=0 AND e.event_info_status=1";
            if (!empty($NewState_id)) {
                $EventSql .= ' AND e.state=' . $NewState_id;
                if(!empty($HomeFlag)){
                    $EventSql .= ' Limit 8';
                }
                $Events = DB::select($EventSql);
            }
        }

        if (Sizeof($Banners) == 0) {
            $BannerSql = "SELECT b.* FROM banner AS b WHERE b.active=1";
            if (!empty($NewState_id)) {
                $BannerSql .= ' AND b.state=' . $NewState_id;
                $Banners = DB::select($BannerSql);
            }
        }
        if (Sizeof($UpcomingEvents) == 0) {
            $UpcomingEventsql = "SELECT * from events AS u WHERE u.active=1 AND u.deleted=0 AND u.start_time >=:start_time AND u.event_info_status=1";
            if (!empty($NewState_id)) {
                $UpcomingEventsql .= ' AND u.state=' . $NewState_id;
                if(!empty($HomeFlag)){
                    $UpcomingEventsql .= ' Limit 8';
                }
                $UpcomingEvents = DB::select($UpcomingEventsql, array('start_time' => $NowTime));
            }
        }
        // dd($EventSql,$BannerSql,$Banners);
        $NewCountry_id = (!empty($country_id)) ? $country_id : $ResponseData['CountryId'];
        if (Sizeof($Events) == 0) {
            $EventSql = "SELECT e.* FROM events AS e WHERE e.active=1 AND e.deleted=0 AND e.event_info_status=1";
            if (!empty($NewCountry_id) && $NewCountry_id > 0) {
                $EventSql .= ' AND e.country=' . $NewCountry_id;
                if(!empty($HomeFlag)){
                    $EventSql .= ' Limit 8';
                }
                $Events = DB::select($EventSql);
            }
        }
        if (Sizeof($Banners) == 0) {
            $BannerSql = "SELECT b.* FROM banner AS b WHERE b.active=1";
            if (!empty($NewCountry_id)) {
                $BannerSql .= ' AND b.country=' . $NewCountry_id;
                $Banners = DB::select($BannerSql);
            }
        }
        if (Sizeof($UpcomingEvents) == 0) {
            $UpcomingEventsql = "SELECT * from events AS u WHERE u.active=1 AND u.deleted=0 AND u.start_time >=:start_time AND u.event_info_status=1";
            if (!empty($NewState_id)) {
                $UpcomingEventsql .= ' AND u.country=' . $NewCountry_id;
                if(!empty($HomeFlag)){
                    $UpcomingEventsql .= ' Limit 8';
                }
                $UpcomingEvents = DB::select($UpcomingEventsql, array('start_time' => $NowTime));
            }
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

        $ResponseData['eventData'] = $this->ManipulateEvents($Events, $UserId);
        $ResponseData['BannerImages'] = $BannerImages;

        #UPCOMING EVENTS
        $ResponseData['UpcomingEventData'] = $this->ManipulateEvents($UpcomingEvents, $UserId);
        $ResponseData['MAX_UPLOAD_FILE_SIZE'] = config('custom.max_size');//Config::get('custom.MAX_UPLOAD_FILE_SIZE');

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

        $EventId = isset($request->event_id) ? $request->event_id : 0;//for view event (Event Details page)
        $EventName = isset($request->event_name) ? $request->event_name : '';
        $Filter = isset($request->filter) ? $request->filter : '';
        $Category = isset($request->category_id) ? $request->category_id : 0;
        $StartDateTime = isset($request->start_date) ? strtotime($request->start_date) : 0;
        $CityId = isset($request->city) ? $request->city : 0;
        $StateId = isset($request->state) ? $request->state : 0;
        $CountryId = isset($request->country) ? $request->country : 0;
        $EndDateTime = (isset($request->end_date) && !empty($request->end_date)) ? strtotime(date("Y-m-d 23:59:59", strtotime($request->end_date))) : 0;
        $Distance = isset($request->distance) ? $request->distance : 0;
        // dd($StartDateTime,$EndDateTime);
        $EventSql = "SELECT * FROM events AS e";
        if (!empty($EventId)) {
            $EventSql .= " WHERE e.id=" . $EventId;
        }
        if (!empty($Category)) {
            $EventSql .= " LEFT JOIN event_category AS ec ON e.id = ec.event_id WHERE ec.category_id=" . $Category;
        }
        if (empty($Category) && empty($EventId)) {
            $EventSql .= " WHERE e.active=1 AND e.deleted=0 AND e.event_info_status=1";
        } else {
            $EventSql .= " AND e.active=1 AND e.deleted=0 AND e.event_info_status=1";
        }
        if ($EventName != "") {
            $EventSql .= " AND e.name LIKE '%" . $EventName . "%' ";
        }
        if ((!empty($StartDateTime)) && (empty($EndDateTime))) {
            $EventSql .= " AND e.start_time >=" . $StartDateTime;
        }
        if ((!empty($EndDateTime)) && (empty($StartDateTime))) {
            $EventSql .= " AND e.end_time <=" . $EndDateTime;
        }
        if ((!empty($StartDateTime)) && (!empty($EndDateTime))) {
            // $EventSql .= ' AND e.start_time BETWEEN ' . $StartDateTime . ' AND ' . $EndDateTime;
            $EventSql .= ' AND e.start_time >=' . $StartDateTime . ' AND e.end_time <= ' . $EndDateTime;

        }
        if (!empty($Distance)) {
            $EventSql .= " AND e.distance =" . $Distance;
        }
        if (!empty($CountryId)) {
            $EventSql .= " AND e.country=" . $CountryId;
        }
        if (!empty($StateId)) {
            $EventSql .= " AND e.state=" . $StateId;
        }
        if (!empty($CityId)) {
            $EventSql .= " AND e.city=" . $CityId;
        }

        // dd($EventSql);
        if (!empty($Filter)) {
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
                    $StartDate = strtotime(date('Y-m-01'));
                    $EndDate = strtotime(date('Y-m-t'));
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
                // $EventSql .= ' AND e.start_time >=' . $StartDate . ' AND e.end_time <= ' . $EndDate;

            } else if ($Filter == 'free') {
                $EventSql .= " AND e.is_paid=0";
            }
        }
        // dd($EventSql, $StartDate, $EndDate);

        $Events = DB::select($EventSql);
        // dd($Events);
        $ResponseData['EventData'] = $this->ManipulateEvents($Events, $UserId);
        $response = [
            'status' => 200,
            'data' => $ResponseData,
            'message' => $message,
            'FewSuggestionFlag' => $FewSuggestionFlag
        ];

        return response()->json($response, $ResposneCode);

    }

    #API FOR EVENT DETAILS PAGE
    public function EventDetailsPage(Request $request)
    {
        $ResponseData = [];
        $Events = [];
        $ResposneCode = 200;
        $message = 'Success';
        $FewSuggestionFlag = 0;
        $UserId = 0;
        $Auth = new Authenticate();
        $Auth->apiLog($request);

        #IF USER IS LOGIN GET USER ID
        // $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        // $UserId = $aToken['data']->ID;

        #ELSE USER NOT LOGIN
        $aToken = $Auth->decode_token($request->header('Authorization'));

        if (!empty($aToken)) {
            $UserId = $aToken->ID;
        }
        // dd($aToken,$UserId);

        $EventId = isset($request->event_id) ? $request->event_id : 0;//for view event (Event Details page)
        $EventName = isset($request->event_name) ? str_replace("_"," ",$request->event_name) : '';//for share event link
        // dd($EventName);
        if (!empty($EventId)) {
            $EventSql = "SELECT * FROM events AS e WHERE e.id=:event_id";
            $Events = DB::select($EventSql, array('event_id' => $EventId));
        }
        if ($EventName !== "") {
            $EventSql = "SELECT * FROM events AS e WHERE e.name=:event_name";
            $Events = DB::select($EventSql, array('event_name' => $EventName));
        }

        // dd($Events);
        $ResponseData['EventData'] = $this->ManipulateEvents($Events, $UserId);
        $ResponseData['EventDetailId'] = sizeof($Events)>0 ? $Events[0]->id : 0;

        #ORGANISER
        $ResponseData['OrganiserName'] = $ResponseData['OrganiserId'] = $ResponseData['UserId'] = "";
        if (!empty($Events[0]->created_by)) {
            $sql = "SELECT id,name FROM organizer WHERE user_id=:user_id";
            $Organiser = DB::select($sql, array('user_id' => $Events[0]->created_by));
            $ResponseData['OrganiserName'] = (sizeof($Organiser) > 0) ? ucwords($Organiser[0]->name) : "";
            $ResponseData['OrganiserId'] = (sizeof($Organiser) > 0) ? $Organiser[0]->id : 0;
            $ResponseData['UserId'] = $Events[0]->created_by;
        }
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
        $Events = $Banners = $UpcomingEvents = [];
        $ResponseData['CityName'] = '';
        $ResponseData['CountryId'] = $ResponseData['StateId'] = $ResponseData['CityId'] = 0;
        $FewSuggestionFlag = 0;
        $NowTime = strtotime('now');

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
        $HomeFlag = isset($request->home_flag) ? $request->home_flag : 0;

        #GET EVENTS COUNTRY WISE
        $EventSql = "SELECT e.* FROM events AS e WHERE e.active=1 AND e.deleted=0 AND e.event_info_status=1";
        $BannerSql = "SELECT b.* FROM banner AS b WHERE b.active=1";
        $UpcomingSql = "SELECT * from events AS u WHERE u.active=1 AND u.deleted=0 AND u.start_time >=:start_time AND u.event_info_status=1";

        // $CountryCode = $request->country_code;
        $City = isset($request->city) ? $request->city : '';
        // $State = isset($request->state) ? $request->state : '';
        $city_id = isset($request->scity) ? $request->scity : 0;
        $state_id = isset($request->state) ? $request->state : 0;
        $country_id = isset($request->country) ? $request->country : 0;
        // dd($country_id,$City);
        if (!empty($City)) {
            $sSQL = 'SELECT id,name,state_id,country_id FROM cities WHERE id =:id';
            $CityId = DB::select($sSQL, array('id' => $City));

            $EventSql .= ' AND e.city=' . $City;
            $BannerSql .= ' AND b.city=' . $City;
            $UpcomingSql .= ' AND u.city=' . $City;

            if (sizeof($CityId) > 0) {
                $ResponseData['CityName'] = $CityId[0]->name;
                $ResponseData['CountryId'] = $CityId[0]->country_id;
                $ResponseData['StateId'] = $CityId[0]->state_id;
                $ResponseData['CityId'] = $CityId[0]->id;
            }
            if(!empty($HomeFlag)){
                $EventSql .= ' Limit 8';
            }
            $Events = DB::select($EventSql);
            // dd($EventSql,$Events);
            $Banners = DB::select($BannerSql);
            if(!empty($HomeFlag)){
                $UpcomingSql .= ' Limit 8';
            }
            $UpcomingEvents = DB::select($UpcomingSql, array('start_time' => $NowTime));
        }

        #NEW SECTION STARTS IF EVENTS AND BANNERS GETTINGS EMPTY
        ##IF NO EVENTS OR BANNERS ARE FOUND FOR THE GIVEN TIME FRAME, THEN WE SHOW ALL AVAILABLE ON
        $NewState_id = (!empty($state_id)) ? $state_id : $ResponseData['StateId'];
        // dd($NewState_id, $Events);

        if (Sizeof($Events) == 0) {
            $FewSuggestionFlag = 1;
            $EventSql = "SELECT e.* FROM events AS e WHERE e.active=1 AND e.deleted=0 AND e.event_info_status=1";
            if (!empty($NewState_id)) {
                $EventSql .= ' AND e.state=' . $NewState_id;
                if (empty($ResponseData['StateId'])) {
                    // dd($ResponseData,$NewState_id);
                    $ResponseData['StateId'] = $NewState_id;
                }
                if(!empty($HomeFlag)){
                    $EventSql .= ' Limit 8';
                }
                $Events = DB::select($EventSql);
            }
        }
        // dd($EventSql,$BannerSql);
        if (Sizeof($Banners) == 0) {
            $BannerSql = "SELECT b.* FROM banner AS b WHERE b.active=1";
            if (!empty($NewState_id)) {
                $BannerSql .= ' AND b.state=' . $NewState_id;
                $Banners = DB::select($BannerSql);
            }
        }
        if (Sizeof($UpcomingEvents) == 0) {
            $UpcomingEventsql = "SELECT * from events AS u WHERE u.active=1 AND u.deleted=0 AND u.start_time >=:start_time AND u.event_info_status=1";
            if (!empty($NewState_id)) {
                $UpcomingEventsql .= ' AND u.state=' . $NewState_id;
                if(!empty($HomeFlag)){
                    $UpcomingEventsql .= ' Limit 8';
                }
                $UpcomingEvents = DB::select($UpcomingEventsql, array('start_time' => $NowTime));
            }
        }

        $NewCountry_id = (!empty($country_id)) ? $country_id : $ResponseData['CountryId'];
        // dd($ResponseData['CountryId'],$NewCountry_id,$Events);
        if (Sizeof($Events) == 0) {
            $EventSql = "SELECT e.* FROM events AS e WHERE e.active=1 AND e.deleted=0 AND e.event_info_status=1";
            if (!empty($NewCountry_id) && $NewCountry_id > 0) {
                $EventSql .= ' AND e.country=' . $NewCountry_id;
                if (empty($ResponseData['CountryId'])) {
                    $ResponseData['CountryId'] = $NewCountry_id;
                }
                if(!empty($HomeFlag)){
                    $EventSql .= ' Limit 8';
                }
                $Events = DB::select($EventSql);
            }
        }
        if (Sizeof($Banners) == 0) {
            $BannerSql = "SELECT b.* FROM banner AS b WHERE b.active=1";
            if (!empty($NewCountry_id)) {
                $BannerSql .= ' AND b.country=' . $NewCountry_id;
                $Banners = DB::select($BannerSql);
            }
        }
        if (Sizeof($UpcomingEvents) == 0) {
            $UpcomingEventsql = "SELECT * from events AS u WHERE u.active=1 AND u.deleted=0 AND u.start_time >=:start_time AND u.event_info_status=1";
            if (!empty($NewState_id)) {
                $UpcomingEventsql .= ' AND u.country=' . $NewCountry_id;
                if(!empty($HomeFlag)){
                    $UpcomingEventsql .= ' Limit 8';
                }
                $UpcomingEvents = DB::select($UpcomingEventsql, array('start_time' => $NowTime));
            }
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
        $ResponseData['eventData'] = $this->ManipulateEvents($Events, $UserId);
        $ResponseData['BannerImages'] = $BannerImages;

        #UPCOMING EVENTS
        $ResponseData['UpcomingEventData'] = $this->ManipulateEvents($UpcomingEvents, $UserId);
        $ResponseData['MAX_UPLOAD_FILE_SIZE'] = config('custom.max_size');
        // dd($ResponseData);

        $response = [
            'status' => 'success',
            'data' => $ResponseData,
            'message' => $message,
            'FewSuggestionFlag' => $FewSuggestionFlag
        ];

        return response()->json($response, $ResposneCode);
    }

    public function createEventBasicInfo(Request $request)
    {
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

            // if (empty($aPost['event_info_status'])) {
            //     $empty = true;
            //     $field = 'Event Status';
            // }
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
                $EventTypes = isset($request->event_types) ? $request->event_types : [];

                $EventUrl = isset($request->event_url) ? $request->event_url : "";
                $EventType = isset($request->event_type) ? $request->event_type : 0;

                $EventId = (isset($request->event_id) && !empty($request->event_id)) ? $request->event_id : 0;

                if (empty($EventId)) {

                    #CHECK SAME EVENT NAME EXIST OR NOT
                    $SQL = "SELECT name FROM events WHERE name=:name";
                    $IsExist = DB::select($SQL, array('name' => strtolower($EventName)));

                    if (count($IsExist) > 0) {
                        $ResposneCode = 400;
                        $message = "Event with same name is already exists, please use another name.";
                    } else {
                        $Bindings = array(
                            "event_info_status" => $EventStatus,
                            "event_name" => $EventName,
                            "display_name_status" => $EventDisplayStatus,
                            "display_name" => $EventDisplayName,
                            "event_url" => $EventUrl,
                            "event_type" => $EventType,
                            "created_by" => $UserId,
                            "ytcr_base_price" => 40
                        );
                        $SQL = "INSERT INTO events (event_info_status,name,event_visibilty,event_display_name,event_url,event_type,created_by,ytcr_base_price) VALUES(:event_info_status,:event_name,:display_name_status,:display_name,:event_url,:event_type,:created_by,:ytcr_base_price)";
                        DB::insert($SQL, $Bindings);
                        $EventId = DB::getPdo()->lastInsertId();

                        if (!empty($Category) && !empty($EventId)) {
                            foreach ($Category as $value) {
                                // dd($value);
                                if ($value['checked'] == "true") {
                                    $sql = "INSERT INTO event_category (event_id, category_id,created_by) VALUES(:event_id,:category_id,:created_by)";
                                    $Bind = array(
                                        "event_id" => $EventId,
                                        "category_id" => $value['id'],
                                        "created_by" => $UserId
                                    );
                                    DB::insert($sql, $Bind);
                                }
                            }
                        }

                        if (!empty($EventTypes) && !empty($EventId)) {
                            foreach ($EventTypes as $value) {
                                if ($value['checked'] == "true") {
                                    $sql = "INSERT INTO event_type (event_id, type_id,created_by) VALUES(:event_id,:type_id,:created_by)";
                                    $Bind = array(
                                        "event_id" => $EventId,
                                        "type_id" => $value['id'],
                                        "created_by" => $UserId
                                    );
                                    DB::insert($sql, $Bind);
                                }
                            }
                        }

                        $message = "Basic inforamtion of event added successfully";
                    }
                } else {
                    // dd($Category);
                    #CHECK SAME EVENT NAME EXIST OR NOT
                    $SQL = "SELECT name FROM events WHERE name=:name AND id !=:id";
                    $IsExist = DB::select($SQL, array('name' => strtolower($EventName), 'id' => $EventId));

                    if (count($IsExist) > 0) {
                        $ResposneCode = 400;
                        $message = "Event with same name is already exists, please use another name.";
                    } else {
                        $Bindings = array(
                            "event_info_status" => $EventStatus,
                            "event_name" => $EventName,
                            "display_name_status" => $EventDisplayStatus,
                            "display_name" => $EventDisplayName,
                            "event_url" => $EventUrl,
                            "event_type" => $EventType,
                            "id" => $EventId
                        );

                        $SQL = "UPDATE events SET event_info_status=:event_info_status,name=:event_name,event_visibilty=:display_name_status,event_display_name=:display_name,event_url=:event_url,event_type=:event_type WHERE id=:id";
                        DB::update($SQL, $Bindings);

                        #DELETE ALL CATEGORY AND TYPES OF EVENT
                        $delete_cat = 'DELETE FROM event_category WHERE event_id=:event_id';
                        DB::delete($delete_cat, array('event_id' => $EventId));
                        $delete_typ = 'DELETE FROM event_type WHERE event_id=:event_id';
                        DB::delete($delete_typ, array('event_id' => $EventId));


                        #NEWLY INSERT ALL CATEGORY AND TYPES OF EVENT
                        if (!empty($Category) && !empty($EventId)) {
                            foreach ($Category as $value) {
                                // dd($value);
                                if ($value['checked'] == "true") {
                                    $sql = "INSERT INTO event_category (event_id, category_id,created_by) VALUES(:event_id,:category_id,:created_by)";
                                    $Bind = array(
                                        "event_id" => $EventId,
                                        "category_id" => $value['id'],
                                        "created_by" => $UserId
                                    );
                                    DB::insert($sql, $Bind);
                                }
                            }
                        }

                        if (!empty($EventTypes) && !empty($EventId)) {
                            foreach ($EventTypes as $value) {
                                if ($value['checked'] == "true") {
                                    $sql = "INSERT INTO event_type (event_id, type_id,created_by) VALUES(:event_id,:type_id,:created_by)";
                                    $Bind = array(
                                        "event_id" => $EventId,
                                        "type_id" => $value['id'],
                                        "created_by" => $UserId
                                    );
                                    DB::insert($sql, $Bind);
                                }
                            }
                        }
                        $message = "Basic inforamtion of event updated successfully";
                    }
                }
                $ResponseData['event_id'] = $EventId;
                $ResposneCode = 200;
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

    #API FOR CREATE EVENT PAGE
    public function getEventDetails(Request $request)
    {
        $ResponseData = [];
        $ResposneCode = 200;
        $empty = false;
        $message = 'Success';
        $field = '';

        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);

        if ($aToken['code'] == 200) {
            $Auth = new Authenticate();
            $Auth->apiLog($request);
            $UserId = $aToken['data']->ID;
            $EventId = isset($request->event_id) ? $request->event_id : 0;
            $Events = array();
            if (!empty($EventId)) {
                $sql = "SELECT * from events WHERE active=1 AND deleted=0";
                $sql .= " AND id=" . $EventId;
                $Events = DB::select($sql);
            }

            // dd($Events);
            $master = new Master();
            $e = new Event();
            foreach ($Events as $event) {
                $event->start_date = (!empty($event->start_time)) ? date("Y-m-d", $event->start_time) : 0;
                $event->start_time_event = (!empty($event->start_time)) ? date("h:i", $event->start_time) : 0;

                $event->end_date = (!empty($event->end_time)) ? date("Y-m-d", $event->end_time) : 0;
                $event->end_time_event = (!empty($event->end_time)) ? date("h:i", $event->end_time) : 0;

                $event->repeat_start_time = (!empty($event->repeat_start_time)) ? date("h:i", $event->repeat_start_time) : 0;
                $event->repeat_end_time = (!empty($event->repeat_end_time)) ? date("h:i", $event->repeat_end_time) : 0;

                $event->banner_image = !empty($event->banner_image) ? url('/') . '/uploads/banner_image/' . $event->banner_image . '' : '';
                $event->logo_image = !empty($event->logo_image) ? url('/') . '/uploads/logo_image/' . $event->logo_image . '' : '';
                $event->background_image = url('/') . '/uploads/images/banner-bg-2.jpg';
                $event->city_name = !empty($event->city) ? $master->getCityName($event->city) : "";
                $event->state_name = !empty($event->state) ? $master->getStateName($event->state) : "";
                $event->country_name = !empty($event->country) ? $master->getCountryName($event->country) : "";
                $event->time_zone_name = !empty($event->country) ? $master->getTimeZoneName($event->time_zone) : "";

                #FOLLOW(WISHLIST)
                $event->is_follow = !empty($UserId) ? $e->isFollowed($event->id, $UserId) : 0;

                #GET ALL TICKETS
                $SQL = "SELECT COUNT(event_id) AS no_of_tickets,min(ticket_price) AS min_price,max(ticket_price) AS max_price,max(early_bird) AS early_bird FROM event_tickets WHERE event_id=:event_id AND ticket_status=1 AND active = 1 AND is_deleted = 0 ORDER BY ticket_price";
                $Tickets = DB::select($SQL, array('event_id' => $event->id));

                $event->min_price = (sizeof($Tickets) > 0) ? $Tickets[0]->min_price : 0;
                $event->max_price = (sizeof($Tickets) > 0) ? $Tickets[0]->max_price : 0;
                $event->no_of_tickets = (sizeof($Tickets) > 0) ? $Tickets[0]->no_of_tickets : 0;
                $event->early_bird = (sizeof($Tickets) > 0) ? $Tickets[0]->early_bird : 0;
                //event start month
                $event->start_event_month = (!empty($event->start_time)) ? gmdate("M", $event->start_time) : "";
                //event start d
                $event->start_event_date = (!empty($event->start_time)) ? gmdate("d", $event->start_time) : "";

                //registration closing date
                $event->registration_end_date = (!empty($event->registration_end_time)) ? gmdate("d M Y", $event->registration_end_time) : "";


            }
            // dd($Events);
            $ResponseData['EventData'] = $Events;

            $ResponseData['AllCategory'] = $e->getCategory($EventId);
            $ResponseData['AllEventTypes'] = $e->getTypes($EventId);

            #Preview data array
            $ResponseData['PreviewEventDetails'] = array();
            // if (!empty($Events)) {

            #GET ALL TICKETS
            $SQL = "SELECT COUNT(event_id) AS no_of_tickets,min(ticket_price) AS min_price,max(ticket_price) AS max_price FROM event_tickets WHERE event_id=:event_id AND ticket_status=1 AND active = 1 AND is_deleted = 0 ORDER BY ticket_price";
            $Tickets = DB::select($SQL, array('event_id' => $EventId));
            $ResponseData['PreviewEventDetails'] = array(
                "banner_img" => (isset($Events[0]->banner_image) && !empty($Events[0]->banner_image)) ? $Events[0]->banner_image . '' : "",
                "event_id" => isset($Events[0]->id) && !empty($Events[0]->id) ? $Events[0]->id : "",
                "start_date" => (isset($Events[0]->start_time) && (!empty($Events[0]->start_time))) ? date("F d, Y", $Events[0]->start_time) : 0,
                "city" => (isset($Events[0]->city) && !empty($Events[0]->city)) ? $master->getCityName($Events[0]->city) : "",
                "event_name" => (isset($Events[0]->name) && !empty($Events[0]->name)) ? (strlen($Events[0]->name) > 40 ? ucwords(substr($Events[0]->name, 0, 40)) . "..." : ucwords($Events[0]->name)) : "",
                "start_event_month" => (isset($Events[0]->start_time) && (!empty($Events[0]->start_time))) ? gmdate("M", $Events[0]->start_time) : gmdate("M", strtotime('now')),
                "start_event_date" => (isset($Events[0]->start_time) && (!empty($Events[0]->start_time))) ? gmdate("d", $Events[0]->start_time) : gmdate("d", strtotime('now')),
                "registration_end_date" => (isset($Events[0]->registration_end_time) && !empty($Events[0]->registration_end_time)) ? gmdate("d F Y", $event->registration_end_time) : gmdate("d F Y", strtotime('now')),

                "min_price" => (sizeof($Tickets) > 0) ? $Tickets[0]->min_price : 0,
                "max_price" => (sizeof($Tickets) > 0) ? $Tickets[0]->max_price : 0,
                "no_of_tickets" => (sizeof($Tickets) > 0) ? $Tickets[0]->no_of_tickets : 0
            );
            // }

            #GETTING EVENT IMAGES
            $ImageQry = "SELECT * FROM event_images WHERE event_id=:event_id";
            $EventImg = DB::select($ImageQry, array('event_id' => $EventId));

            if (sizeOf($EventImg) > 0) {
                foreach ($EventImg as $value) {
                    $value->image = !empty($value->image) ? url('/') . '/uploads/event_images/' . $value->image : "";
                }
            }
            $ResponseData['EventImages'] = $EventImg;

            #EVENT TICKETS
            $sql = "SELECT * FROM event_tickets WHERE event_id=:event_id AND is_deleted = 0 AND active = 1";
            $EventTickets = DB::select($sql, array('event_id' => $EventId));
            foreach ($EventTickets as $ticket) {
                $ticket->ticket_sale_start_date = (!empty($ticket->ticket_sale_start_date)) ? date("d F Y", $ticket->ticket_sale_start_date) : 0;
                $ticket->ticket_sale_end_date = (!empty($ticket->ticket_sale_end_date)) ? date("d F Y", $ticket->ticket_sale_end_date) : 0;

            }
            $ResponseData['EventTickets'] = $EventTickets;

            //------ setting tab get info
            $sql = "SELECT es.*,(select ytcr_base_price from events where id = " . $EventId . ") as ytcr_base_price FROM event_settings as es WHERE event_id =:event_id";
            $SettingInfoDetails = DB::select($sql, array('event_id' => $EventId));
            //dd($SettingInfoDetails);
            if (!empty($SettingInfoDetails)) {
                $ResponseData['event_setting_details'] = $SettingInfoDetails;
            } else {
                $ResponseData['event_setting_details'] = [];
            }

            $sql = "SELECT gst_percentage,gst FROM organizer WHERE user_id = :user_id";
            $OrganizerInfoDetails = DB::select($sql, array('user_id' => $UserId));

            $ResponseData['GST_PERCENTAGE'] = !empty($OrganizerInfoDetails) && !empty($OrganizerInfoDetails[0]->gst_percentage) && 
            $OrganizerInfoDetails[0]->gst == 1 ? $OrganizerInfoDetails[0]->gst_percentage : 0;

            $ResponseData['YTCR_FEE_PERCENTAGE'] = !empty($SettingInfoDetails) && !empty($SettingInfoDetails[0]->ticket_ytcr_base_price) ?
                $SettingInfoDetails[0]->ticket_ytcr_base_price : config('custom.ytcr_fee_percent');

            $ResponseData['PAYMENT_GATEWAY_FEE_PERCENTAGE'] = config('custom.payment_gateway_fee_percent');
            
            //----------
            $sSQL = 'SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = "Races2.0_Web" AND TABLE_NAME = "users" ';
            $ResponseData['field_mapping_details'] = DB::select($sSQL, array());
            //dd($FieldMppedDetails);

            $ResposneCode = 200;
            $message = "Events Data getting successfully";
        } else {
            $ResposneCode = $aToken['code'];
            $message = $aToken['message'];
        }
        $response = [
            'status' => $ResposneCode,
            'data' => $ResponseData,
            'message' => $message,
        ];

        return response()->json($response, $ResposneCode);

    }

    public function addEventDuration(Request $request)
    {
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

            if (empty($aPost['event_id'])) {
                $empty = true;
                $field = 'Event Id';
            }

            if (!$empty) {
                $EventId = $aPost['event_id'];
                //ADD EVENT CODE
                $Timezone = isset($request->timezone_id) ? $request->timezone_id : 0;

                $EventStartTime = $EventEndTime = 0;
                $StartDate = isset($request->event_start_date) ? $request->event_start_date : 0;
                $StartTime = isset($request->event_start_time) ? $request->event_start_time : 0;
                if (!empty($StartDate) && !empty($StartTime)) {
                    $start_date_time_string = $StartDate . ' ' . $StartTime;
                    $EventStartTime = strtotime($start_date_time_string);
                } else if (!empty($StartDate) && empty($StartTime)) {
                    $EventStartTime = strtotime($StartDate);
                }

                $EndDate = isset($request->event_end_date) ? $request->event_end_date : 0;
                $EndTime = isset($request->event_end_time) ? $request->event_end_time : 0;
                if (!empty($EndDate) && !empty($EndTime)) {
                    $end_date_time_string = $EndDate . ' ' . $EndTime;
                    $EventEndTime = strtotime($end_date_time_string);
                } else if (!empty($EndDate) && empty($EndTime)) {
                    $EventEndTime = strtotime($EndDate);
                }

                $IsRepeatEvent = isset($request->repeating_event) ? $request->repeating_event : 0;
                $RepeatType = isset($request->repeat_type) ? $request->repeat_type : 0;
                $RepeatStartTime = (isset($request->repeat_start_time) && !empty($request->repeat_start_time)) ? strtotime($request->repeat_start_time) : 0;
                $RepeatEndTime = (isset($request->repeat_end_time) && !empty($request->repeat_end_time)) ? strtotime($request->repeat_end_time) : 0;

                $CountryId = isset($request->country_id) ? $request->country_id : 0;
                $StateId = isset($request->state_id) ? $request->state_id : 0;
                $CityId = isset($request->city_id) ? $request->city_id : 0;
                $Address = isset($request->address) ? $request->address : "";

                $Bindings = array(
                    "timezone_id" => $Timezone,
                    "start_time" => $EventStartTime,
                    "end_time" => $EventEndTime,
                    "is_repeat" => $IsRepeatEvent,
                    "repeat_type" => $RepeatType,
                    "repeat_start_time" => $RepeatStartTime,
                    "repeat_end_time" => $RepeatEndTime,
                    "country" => $CountryId,
                    "state" => $StateId,
                    "city" => $CityId,
                    "address" => $Address,
                    "id" => $EventId
                );
                $sql = 'UPDATE events SET
                time_zone=:timezone_id,
                start_time=:start_time,
                end_time=:end_time,
                is_repeat=:is_repeat,
                repeat_type=:repeat_type,
                repeat_start_time=:repeat_start_time,
                repeat_end_time=:repeat_end_time,
                country=:country,
                state=:state,
                city=:city,
                address=:address
                WHERE id=:id';
                DB::update($sql, $Bindings);

                $ResponseData['event_id'] = $EventId;
                $ResposneCode = 200;
                $message = "Event Duration updated successfully";
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

    public function addEventDescription(Request $request)
    {
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $empty = false;
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        // dd($aToken);

        if ($aToken['code'] == 200) {
            $aPost = $request->all();
            $auth = new Authenticate();
            $auth->apiLog($request);
            $userId = $aToken['data']->ID;

            if (empty($aPost['event_id'])) {
                $empty = true;
                $field = 'Event Id';
            }

            if (!$empty) {
                $EventId = $aPost['event_id'];
                $Description = !empty($request->event_description) ? $request->event_description : '';
                $event_keywords = !empty($request->event_keywords) ? $request->event_keywords : '';

                // if (preg_match("/^[a-zA-Z-']*$/", $Description)) {
                $banner_image = '';
                $event_image = '';

                $sql = 'UPDATE events SET event_description=:description,event_keywords=:event_keywords WHERE id=:id';
                $bindings = [
                    "description" => $Description,
                    "event_keywords" => $event_keywords,
                    "id" => $EventId
                ];
                // $ResponseData['sql'] = $sql;

                $Result = DB::update($sql, $bindings);
                // dd($res);
                // if ($Result) {
                if (!empty($request->file('event_banner'))) {
                    $Path = public_path('uploads/banner_image/');
                    $logo_image = $request->file('event_banner');
                    $originalName = $logo_image->getClientOriginalName();
                    $banner_image = $originalName;
                    $logo_image->move($Path, $banner_image);

                    $sSQLImg = 'UPDATE events SET banner_image = :banner_image WHERE id=:id';
                    DB::update($sSQLImg, ['banner_image' => $banner_image, 'id' => $EventId]);
                }

                // $ResponseData['uploadedFile'] = $request->file('event_photos');

                if ($request->file('event_photos')) {
                    $delete_event = 'DELETE FROM event_images WHERE event_id=:event_id';
                    DB::delete($delete_event, array('event_id' => $EventId));

                    $Path = public_path('uploads/event_images/');
                    foreach ($request->file('event_photos') as $key => $uploadedFile) {
                        $validator = Validator::make(['event_photos' => $uploadedFile], [
                            'event_photos' => 'image|mimes:jpeg,png,jpg,gif',
                        ]);
                        if ($validator->fails()) {
                            continue;
                        }
                        if ($uploadedFile->isValid()) {
                            $originalName = strtotime('now') . '_' . $uploadedFile->getClientOriginalName();
                            $event_image = $originalName;
                            $uploadedFile->move($Path, $event_image);

                            // $originalName = $uploadedFile->getClientOriginalName(); // Get the original file name
                            // $extension = $uploadedFile->getClientOriginalExtension(); // Get the file extension
                            // $uniqueFileName = time() . '_' . uniqid() . '.' . $extension;
                            // $uploadedFile->move($Path, $uniqueFileName);

                            $sSQL = 'INSERT INTO event_images (event_id, image, created_by) VALUES(:event_id, :image, :created_by)';
                            $bindings = array(
                                'event_id' => $EventId,
                                'image' => $event_image,
                                'created_by' => $userId
                            );
                            DB::insert($sSQL, $bindings);
                        }
                    }
                }
                $message = 'Event Details updated successfully';
                $ResposneCode = 200;
            } else {
                $ResposneCode = 400;
                $message = $field . ' is empty';
            }
        } else {
            $ResposneCode = $aToken['code'];
            $message = $aToken['message'];
        }

        $response = [
            'status' => $ResposneCode,
            'data' => $ResponseData,
            'message' => $message
        ];

        return response()->json($response, $ResposneCode);

    }

    public function UserFollowEvent(Request $request)
    {
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);

        if ($aToken['code'] == 200) {
            $Auth = new Authenticate();
            $Auth->apiLog($request);
            $UserId = $aToken['data']->ID;
            $sSQL = 'SELECT events.* FROM event_user_follow eu
                    JOIN events ON eu.event_id = events.id
                    WHERE eu.user_id = :User_Id';
            $userfollowevent = DB::select($sSQL, [
                'User_Id' => $aToken['data']->ID
            ]);

            if (!empty($userfollowevent)) {
                $ResponseData['userfollowevent'] = $this->ManipulateEvents($userfollowevent, $UserId);
            }

            $ResposneCode = 200;
            $message = 'Request processed successfully';

        } else {
            $ResposneCode = $aToken['code'];
            $message = $aToken['message'];
        }

        $response = [
            'data' => $ResponseData,
            'message' => $message
        ];
        return response()->json($response, $ResposneCode);
    }

    function PopularCity(Request $request)
    {
        // dd($request);
        $CityArr = new \stdClass;
        $e = new Event();
        $CityArr = [
            0 => [
                'id' => 133024,
                'city' => 'Mumbai',
                'type' => 'city',
                'image' => url('/') . '/uploads/city_images/mumbai-1.png',
                'event_count' => $e->getEventCount(133024)
            ],
            1 => [
                'id' => 57933,
                'city' => 'Bengaluru',
                'type' => 'city',
                'image' => url('/') . '/uploads/city_images/Bengaluru-1.png',
                'event_count' => $e->getEventCount(57933)
            ],
            2 => [
                'id' => 131517,
                'city' => 'Chennai',
                'type' => 'city',
                'image' => url('/') . '/uploads/city_images/bangalore-1.png',
                'event_count' => $e->getEventCount(131517)
            ],
            3 => [
                'id' => 131679,
                'city' => 'Delhi',
                'type' => 'state',
                'image' => url('/') . '/uploads/city_images/delhi-1.png',
                'event_count' => $e->getEventCount(131679)
            ],
            4 => [
                'id' => 57686,
                'city' => 'Amritsar',
                'type' => 'state',
                'image' => url('/') . '/uploads/city_images/panjab-1.png',
                'event_count' => $e->getEventCount(57686)

            ],
            5 => [
                'id' => 133504,
                'city' => 'Pune',
                'type' => 'city',
                'image' => url('/') . '/uploads/city_images/pune-1.png',
                'event_count' => $e->getEventCount(133504)
            ]
        ];
        $ResponseData['CityArr'] = $CityArr;
        $message = "City Data getting successfully";
        $ResposneCode = 200;
        $response = [
            'data' => $ResponseData,
            'message' => $message
        ];
        return response()->json($response, $ResposneCode);
        // dd($CityArr);
    }


    //---------- Added by prathmesh on 08-04-24
    public function addEventSetting(Request $request)
    {
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $empty = false;
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        //dd($aToken['data']->ID);

        if ($aToken['code'] == 200) {
            $aPost = $request->all();
            $Auth = new Authenticate();
            $Auth->apiLog($request);
            $UserId = $aToken['data']->ID;

            if (empty($aPost['event_id'])) {
                $empty = true;
                $field = 'Event Id';
            }

            if (!$empty) {
                $EventId = $aPost['event_id'];
                $UserId = $aPost['user_id'];

                $NoOfPreviousConducts = !empty($request->no_of_previous_conducts) ? $request->no_of_previous_conducts : 0;
                $NoOfRunnersEstimate = !empty($request->no_of_runners_estimate) ? $request->no_of_runners_estimate : 0;
                $NoOfEventYear = !empty($request->no_of_event_year) ? $request->no_of_event_year : 0;

                $ContractOfFiveYear = !empty($request->contract_of_five_year) ? 1 : 0;
                $BackendSupport = !empty($request->backend_support) ? 1 : 0;
                $BulkRegistration = !empty($request->bulk_registration) ? 1 : 0;
                $CheckValidEntries = !empty($request->check_valid_entries) ? 1 : 0;

                $YtcrBasePrice = !empty($request->ytcr_base_price) ? $request->ytcr_base_price : "0.00";
                $EventSettingId = !empty($request->event_setting_id) ? $request->event_setting_id : 0;
                $EditYtcrBasePrice = !empty($request->edit_ytcr_base_price) ? $request->edit_ytcr_base_price : "";

                $SQL = "SELECT id FROM event_settings WHERE event_id =:event_id";
                $IsExist = DB::select($SQL, array('event_id' => $EventId));

                    if (empty($IsExist) && empty($EventSettingId)) {

                        $Bindings = array(
                            "event_id" => $EventId,
                            "no_of_previous_conducts" => $NoOfPreviousConducts,
                            "no_of_runners_estimate" => $NoOfRunnersEstimate,
                            "no_of_event_year" => $NoOfEventYear,
                            "contract_of_five_year" => $ContractOfFiveYear,
                            "backend_support" => $BackendSupport,
                            "bulk_registration" => $BulkRegistration,
                            "check_valid_entries" => $CheckValidEntries,
                            "ticket_ytcr_base_price" => $YtcrBasePrice,
                            "created_by" => $UserId,
                        );

                        $insert_SQL = "INSERT INTO event_settings (event_id,no_of_previous_conducts,no_of_runners_estimate,no_of_event_year,contract_of_five_year,backend_support,bulk_registration,check_valid_entries,ticket_ytcr_base_price,created_by) VALUES(:event_id,:no_of_previous_conducts,:no_of_runners_estimate,:no_of_event_year,:contract_of_five_year,:backend_support,:bulk_registration,:check_valid_entries,:ticket_ytcr_base_price,:created_by)";
                        DB::insert($insert_SQL, $Bindings);

                        $up_sql = 'UPDATE events SET ytcr_base_price = :ytcr_base_price WHERE id = :id';
                        $bindings = ["ytcr_base_price" => $EditYtcrBasePrice, "id" => $EventId];
                        DB::update($up_sql, $bindings);

                        $ResposneCode = 200;
                        $message = "Event Setting added successfully";

                    } else {

                        $Bindings = array(
                            "no_of_previous_conducts" => $NoOfPreviousConducts,
                            "no_of_runners_estimate" => $NoOfRunnersEstimate,
                            "no_of_event_year" => $NoOfEventYear,
                            "contract_of_five_year" => $ContractOfFiveYear,
                            "backend_support" => $BackendSupport,
                            "bulk_registration" => $BulkRegistration,
                            "check_valid_entries" => $CheckValidEntries,
                            "ticket_ytcr_base_price" => $YtcrBasePrice,
                            "event_setting_id" => $EventSettingId
                        );

                        $sql = 'UPDATE event_settings SET no_of_previous_conducts = :no_of_previous_conducts, no_of_runners_estimate  = :no_of_runners_estimate, no_of_event_year = :no_of_event_year, contract_of_five_year = :contract_of_five_year, backend_support = :backend_support, bulk_registration = :bulk_registration, check_valid_entries = :check_valid_entries, ticket_ytcr_base_price  = :ticket_ytcr_base_price WHERE id = :event_setting_id';
                        // dd($sql);
                        DB::update($sql, $Bindings);

                        $up_sql = 'UPDATE events SET ytcr_base_price = :ytcr_base_price WHERE id = :id';
                        $bindings = ["ytcr_base_price" => $EditYtcrBasePrice, "id" => $EventId];
                        DB::update($up_sql, $bindings);

                        $ResposneCode = 200;
                        $message = "Event Setting updated successfully";
                    }

            }else {
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



}

