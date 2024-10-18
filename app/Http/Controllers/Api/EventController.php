<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Libraries\Authenticate;
use Illuminate\Support\Facades\DB;
use App\Models\Master;
use App\Models\Event;
use Illuminate\Support\Facades\Validator;
use League\Csv\Reader;

class EventController extends Controller
{

    function ManipulateEvents($Events, $UserId = 0)
    {
        // dd($Events,$UserId);
        $master = new Master();
        $e = new Event();
        $now = strtotime("now");
        foreach ($Events as $event) {
            $event->name = !empty($event->name) ? ucwords($event->name) : "";
            $event->display_name = !empty($event->name) ? (strlen($event->name) > 40 ? ucwords(substr($event->name, 0, 40)) . "..." : ucwords($event->name)) : "";
            // $event->event_description = !empty($event->event_description) ? html_entity_decode(strip_tags($event->event_description)) : "";

            $event->start_date = (!empty($event->start_time)) ? date("d M Y", $event->start_time) : 0;
            $event->end_date = (!empty($event->end_time)) ? date("d M Y", $event->end_time) : 0;
            $event->start_time_event = (!empty($event->start_time)) ? date("h:i A", $event->start_time) : "";
            $event->end_date_event = (!empty($event->end_time)) ? date("h:i A", $event->end_time) : 0;

            $event->banner_image = !empty($event->banner_image) ? url('/') . '/uploads/banner_image/' . $event->banner_image . '' : '';
            $event->logo_image = !empty($event->logo_image) ? url('/') . '/uploads/logo_image/' . $event->logo_image . '' : '';
            $event->background_image = url('/') . '/uploads/images/banner-bg-2.jpg';
            $event->city_name = !empty($event->city) ? $master->getCityName($event->city) : "";
            $event->state_name = !empty($event->state) ? $master->getStateName($event->state) : "";
            $event->country_name = !empty($event->country) ? $master->getCountryName($event->country) : "";

            // $event->latitude = !empty($event->city) ? $master->getCityLatitude($event->city) : "";
            // $event->longitude = !empty($event->city) ? $master->getCityLongitude($event->city) : "";

            #FOLLOW(WISHLIST)
            $event->is_follow = !empty($UserId) ? $e->isFollowed($event->id, $UserId) : 0;

            #GET ALL TICKETS
            $event->TicketDetails = $e->getEventTicketDetails($event->id);

            //event start month
            $event->start_event_month = (!empty($event->start_time)) ? date("M", $event->start_time) : 0;
            //event start d
            $event->start_event_date = (!empty($event->start_time)) ? date("d", $event->start_time) : 0;

            //registration starting date
            $event->registration_start_date = (!empty($event->registration_start_time)) ? date("d F Y", $event->registration_start_time) : 0;
            $event->registration_start_date_time = (!empty($event->registration_start_time)) ? date("h:i A", $event->registration_start_time) : "";

            //registration closing date
            $event->registration_end_date = (!empty($event->registration_end_time)) ? date("d F Y", $event->registration_end_time) : 0;
            $event->registration_end_date_time = (!empty($event->registration_end_time)) ? date("h:i A", $event->registration_end_time) : "";

            // registration button show flag
            $event->show_registration_button = ($now >= $event->registration_start_time && $now <= $event->registration_end_time) ? 1 : 0;
            $event->show_registration_button_msg = "";
            if ($now < $event->registration_start_time) {
                $event->show_registration_button_msg = "Registration Starting Soon";
            } else if ($now > $event->registration_end_time) {
                $event->show_registration_button_msg = "Registration Closed";
            }

            if($now > $event->registration_end_time) {
                $event->registration_close_flag = 1;
            }else{ $event->registration_close_flag = 0; }

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

            #EVENT TICKETS DISTANCE
            $event->distances = $e->getDistances($event->id);

            #EVENT CATEGORIES
            $event->category = $e->getCategoryDetails($event->id);
            $event->types = $e->getTypeDetails($event->id);
            
            //----------- event overall limit flag
            //$event_overall_limit = $event->overall_limit;
            // dd($event_overall_limit);
            if(!empty($event->overall_limit)){
                // $sSQL = 'SELECT count(id) as total_bookings FROM event_booking WHERE event_id =:event_id AND transaction_status IN(1,3)';
           
                // $sSQL = "SELECT SUM(b.quantity) AS total_bookings
                // FROM booking_details AS b
                // LEFT JOIN event_booking AS e ON b.booking_id = e.id
                // WHERE b.event_id =:event_id AND e.transaction_status IN (1,3)";

                $sSQL = "SELECT IFNULL(COUNT(e.id), 0) AS total_bookings  
                FROM attendee_booking_details AS a 
                LEFT JOIN booking_details AS b ON b.id=a.booking_details_id
                LEFT JOIN event_booking AS e ON b.booking_id = e.id
                WHERE b.event_id =:event_id AND e.transaction_status IN (1,3)";

                $aResult = DB::select($sSQL, array('event_id' => $event->id));
                // dd((int)$aResult[0]->total_bookings , (int)$event->overall_limit);
                if(!empty($aResult) && (int)$aResult[0]->total_bookings >= (int)$event->overall_limit){
                    $event->event_overall_limit_flag = 1;
                }else{
                    $event->event_overall_limit_flag = 0;
                }
            }else{
                $event->event_overall_limit_flag = 0;
            }
            
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
        $Events = $Banners = $UpcomingEvents = $RegistrationEvents = [];
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
        $UpcomingSql = "SELECT * from events AS u WHERE u.active=1 AND u.deleted=0 AND u.event_info_status=1 AND u.start_time >=:start_time";
        $RegistrationSql = "SELECT * from events AS r WHERE r.active=1 AND r.deleted=0 AND r.event_info_status=1 AND r.registration_start_time >=:registration_start_time";
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
                $RegistrationSql .= ' AND r.country=' . $CountryId[0]->id;
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
                $RegistrationSql .= ' AND r.city=' . $CityId[0]->id;
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
                $RegistrationSql .= ' AND r.state=' . $StateId[0]->id;
                $ResponseData['StateId'] = $StateId[0]->id;
            }
        }
        $EventSql .= ' ORDER BY e.id DESC';
        $RegistrationSql .= ' ORDER BY r.id DESC';
        $UpcomingSql .= ' ORDER BY u.id DESC';

        // dd($EventSql,$BannerSql);
        if (!empty($HomeFlag)) {
            $EventSql .= ' Limit 8';
            $RegistrationSql .= ' Limit 8';
        }
        $Events = DB::select($EventSql);
        $Banners = DB::select($BannerSql);
        $RegistrationEvents = DB::select($RegistrationSql, array('registration_start_time' => $NowTime));
        if (!empty($HomeFlag)) {
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
                $EventSql .= ' ORDER BY e.id DESC';

                if (!empty($HomeFlag)) {
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

                $UpcomingSql .= ' ORDER BY u.id DESC';
                if (!empty($HomeFlag)) {
                    $UpcomingEventsql .= ' Limit 8';
                }
                $UpcomingEvents = DB::select($UpcomingEventsql, array('start_time' => $NowTime));
            }
        }
        if (Sizeof($RegistrationEvents) == 0) {
            $RegistrationSql = "SELECT * from events AS r WHERE r.active=1 AND r.deleted=0 AND r.registration_start_time >=:registration_start_time AND r.event_info_status=1";
            if (!empty($NewState_id)) {
                $RegistrationSql .= ' AND r.state=' . $NewState_id;

                $RegistrationSql .= ' ORDER BY r.id DESC';
                if (!empty($HomeFlag)) {
                    $RegistrationSql .= ' Limit 8';
                }
                $RegistrationEvents = DB::select($RegistrationSql, array('registration_start_time' => $NowTime));
            }
        }


        // dd($EventSql,$BannerSql,$Banners);
        $NewCountry_id = (!empty($country_id)) ? $country_id : $ResponseData['CountryId'];
        if (Sizeof($Events) == 0) {
            $EventSql = "SELECT e.* FROM events AS e WHERE e.active=1 AND e.deleted=0 AND e.event_info_status=1";
            if (!empty($NewCountry_id) && $NewCountry_id > 0) {
                $EventSql .= ' AND e.country=' . $NewCountry_id;
                $EventSql .= ' ORDER BY e.id DESC';

                if (!empty($HomeFlag)) {
                    $EventSql .= ' Limit 8';
                }
                $Events = DB::select($EventSql);
            }
        }
        if (Sizeof($Banners) == 0) {
            $BannerSql = "SELECT b.* FROM banner AS b WHERE b.active = 1";
            if (!empty($NewCountry_id)) {
                $BannerSql .= ' AND b.country=' . $NewCountry_id;
                $Banners = DB::select($BannerSql);
            }
        }
        if (Sizeof($UpcomingEvents) == 0) {
            $UpcomingEventsql = "SELECT * from events AS u WHERE u.active=1 AND u.deleted=0 AND u.start_time >=:start_time AND u.event_info_status=1";
            if (!empty($NewState_id)) {
                $UpcomingEventsql .= ' AND u.country=' . $NewCountry_id;
                $UpcomingEventsql .= ' ORDER BY u.id DESC';
                if (!empty($HomeFlag)) {
                    $UpcomingEventsql .= ' Limit 8';
                }
                $UpcomingEvents = DB::select($UpcomingEventsql, array('start_time' => $NowTime));
            }
        }

        if (Sizeof($RegistrationEvents) == 0) {
            $RegistrationSql = "SELECT * from events AS r WHERE r.active=1 AND r.deleted=0 AND r.registration_start_time >=:registration_start_time AND r.event_info_status=1";
            if (!empty($NewState_id)) {
                $RegistrationSql .= ' AND r.country=' . $NewCountry_id;
                $RegistrationSql .= ' ORDER BY r.id DESC';
                if (!empty($HomeFlag)) {
                    $RegistrationSql .= ' Limit 8';
                }
                $RegistrationEvents = DB::select($RegistrationSql, array('registration_start_time' => $NowTime));
            }
        }

        $BannerImages = [];
        foreach ($Banners as $key => $banner) {
            if (!empty((!empty($banner->banner_image)) && ($key <= config('custom.show_banner_limit')))) {
                $banner->banner_url = !empty($banner->banner_url) ? $banner->banner_url : "";
                $banner->banner_image = !empty($banner->banner_image) ? url('/') . '/uploads/banner_image/' . $banner->banner_image . '' : '';
                $BannerImages[$key]['url'] = $banner->banner_url;
                $BannerImages[$key]['img'] = $banner->banner_image;
            }
        }
        // dd($Events,$Banners);

        #BANNERS
        $ResponseData['BannerImages'] = $BannerImages;

        #POPULAR EVENTS
        $ResponseData['eventData'] = $this->ManipulateEvents($Events, $UserId);

        #UPCOMING EVENTS
        $ResponseData['UpcomingEventData'] = $this->ManipulateEvents($UpcomingEvents, $UserId);

        #REGISTRATION COMING SOON EVENTS
        $ResponseData['RegistrationEventData'] = $this->ManipulateEvents($RegistrationEvents, $UserId);

        // $upcomingEventIds = array_map(function ($event) {
        //     return $event->id;
        // }, $ResponseData['UpcomingEventData']);

        // Filter out events from RegistrationEventData if their IDs are present in UpcomingEventData
        // $filteredRegistrationEventData = array_filter($ResponseData['RegistrationEventData'], function ($event) use ($upcomingEventIds) {
        //     return !in_array($event->id, $upcomingEventIds);
        // });

        // // Set the filtered RegistrationEventData
        // $ResponseData['RegistrationEventData'] = $filteredRegistrationEventData;

        $ResponseData['MAX_UPLOAD_FILE_SIZE'] = config('custom.max_size');//Config::get('custom.MAX_UPLOAD_FILE_SIZE');
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
        $Events = $Banners = $UpcomingEvents = $RegistrationEvents = [];
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
        $RegistrationSql = "SELECT * from events AS r WHERE r.active=1 AND r.deleted=0 AND r.event_info_status=1 AND r.registration_start_time >=:registration_start_time";
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
            $RegistrationSql .= ' AND r.city=' . $City;

            if (sizeof($CityId) > 0) {
                $ResponseData['CityName'] = $CityId[0]->name;
                $ResponseData['CountryId'] = $CityId[0]->country_id;
                $ResponseData['StateId'] = $CityId[0]->state_id;
                $ResponseData['CityId'] = $CityId[0]->id;
            }
            $EventSql .= ' ORDER BY e.id DESC';
            $RegistrationSql .= ' ORDER BY r.id DESC';
            $UpcomingSql .= ' ORDER BY u.id DESC';
            if (!empty($HomeFlag)) {
                $EventSql .= ' Limit 8';
                $RegistrationSql .= ' Limit 8';
            }
            $Events = DB::select($EventSql);
            $RegistrationEvents = DB::select($RegistrationSql, array('registration_start_time' => $NowTime));

            // dd($EventSql,$Events);
            $Banners = DB::select($BannerSql);
            if (!empty($HomeFlag)) {
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
                $EventSql .= ' ORDER BY e.id DESC';

                if (empty($ResponseData['StateId'])) {
                    // dd($ResponseData,$NewState_id);
                    $ResponseData['StateId'] = $NewState_id;
                }
                if (!empty($HomeFlag)) {
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
                $UpcomingEventsql .= ' ORDER BY u.id DESC';
                if (!empty($HomeFlag)) {
                    $UpcomingEventsql .= ' Limit 8';
                }
                $UpcomingEvents = DB::select($UpcomingEventsql, array('start_time' => $NowTime));
            }
        }
        if (Sizeof($RegistrationEvents) == 0) {
            $RegistrationSql = "SELECT * from events AS r WHERE r.active=1 AND r.deleted=0 AND r.registration_start_time >=:registration_start_time AND r.event_info_status=1";
            if (!empty($NewState_id)) {
                $RegistrationSql .= ' AND r.state=' . $NewState_id;
                $RegistrationSql .= ' ORDER BY r.id DESC';
                if (!empty($HomeFlag)) {
                    $RegistrationSql .= ' Limit 8';
                }
                $RegistrationEvents = DB::select($RegistrationSql, array('registration_start_time' => $NowTime));
            }
        }

        $NewCountry_id = (!empty($country_id)) ? $country_id : $ResponseData['CountryId'];
        // dd($ResponseData['CountryId'],$NewCountry_id,$Events);
        if (Sizeof($Events) == 0) {
            $EventSql = "SELECT e.* FROM events AS e WHERE e.active=1 AND e.deleted=0 AND e.event_info_status=1";
            if (!empty($NewCountry_id) && $NewCountry_id > 0) {
                $EventSql .= ' AND e.country=' . $NewCountry_id;
                $EventSql .= ' ORDER BY e.id DESC';
                if (empty($ResponseData['CountryId'])) {
                    $ResponseData['CountryId'] = $NewCountry_id;
                }
                if (!empty($HomeFlag)) {
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
                $UpcomingEventsql .= ' ORDER BY u.id DESC';
                if (!empty($HomeFlag)) {
                    $UpcomingEventsql .= ' Limit 8';
                }
                $UpcomingEvents = DB::select($UpcomingEventsql, array('start_time' => $NowTime));
            }
        }
        if (Sizeof($RegistrationEvents) == 0) {
            $RegistrationSql = "SELECT * from events AS r WHERE r.active=1 AND r.deleted=0 AND r.registration_start_time >=:registration_start_time AND r.event_info_status=1";
            if (!empty($NewState_id)) {
                $RegistrationSql .= ' AND r.country=' . $NewCountry_id;
                $RegistrationSql .= ' ORDER BY r.id DESC';
                if (!empty($HomeFlag)) {
                    $RegistrationSql .= ' Limit 8';
                }
                $RegistrationEvents = DB::select($RegistrationSql, array('registration_start_time' => $NowTime));
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
        $ResponseData['RegistrationEventData'] = $this->ManipulateEvents($RegistrationEvents, $UserId);
        $ResponseData['BannerImages'] = $BannerImages;
        //dd($ResponseData['eventData']);
        // dd($ResponseData['RegistrationEventData']);
        #UPCOMING EVENTS
        // $NoOfUpcomingEvent = count($UpcomingEvents);
        // // dd($NoOfUpcomingEvent);
        // if ($NoOfUpcomingEvent == 1) {
        //     for ($i = 1; $i < 4; $i++) {
        //         $UpcomingEvents[] = clone $UpcomingEvents[0];
        //     }
        // } else if ($NoOfUpcomingEvent == 2) {
        //     $duplicateArray = $UpcomingEvents[0];
        //     $UpcomingEvents[] = $duplicateArray;
        //     $UpcomingEvents[] = clone $duplicateArray;
        // } else if ($NoOfUpcomingEvent == 3) {
        //     $duplicateArray = clone $UpcomingEvents[0];
        //     $UpcomingEvents[] = clone $duplicateArray;
        // }

        // foreach ($UpcomingEvents as $key => $value) {
        //     $value->visibility = ($key < $NoOfUpcomingEvent) ? "show" : "hidden";
        // }
        // dd(count($UpcomingEvents));
        // echo "<pre>";
        // print_r($UpcomingEvents);
        // die;
        $ResponseData['UpcomingEventData'] = $this->ManipulateEvents($UpcomingEvents, $UserId);


        // $upcomingEventIds = array_map(function ($event) {
        //     return $event->id;
        // }, $ResponseData['UpcomingEventData']);

        // Filter out events from RegistrationEventData if their IDs are present in UpcomingEventData
        // $filteredRegistrationEventData = array_filter($ResponseData['RegistrationEventData'], function ($event) use ($upcomingEventIds) {
        //     return !in_array($event->id, $upcomingEventIds);
        // });

        // // // Set the filtered RegistrationEventData
        // $ResponseData['RegistrationEventData'] = $filteredRegistrationEventData;

        $ResponseData['MAX_UPLOAD_FILE_SIZE'] = config('custom.max_size');
        //dd($ResponseData['RegistrationEventData']);

        $response = [
            'status' => 'success',
            'data' => $ResponseData,
            'message' => $message,
            'FewSuggestionFlag' => $FewSuggestionFlag
        ];

        return response()->json($response, $ResposneCode);
    }


    #API FOR SEARCH EVENTS PAGE
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
        $event_ids = '';
        if (!empty($Category)) {
            $aSql = "SELECT e.id FROM events as e LEFT JOIN event_tickets AS et ON e.id = et.event_id WHERE et.category = ".$Category." ";
            $aResult = DB::select($aSql);
            // dd($aResult);
            $event_array = !empty($aResult) ? array_column($aResult,'id') : [];
            $event_ids = !empty($event_array) ? implode(",",$event_array) : "";
        }
        // dd($event_ids);

        $EventSql = "SELECT * FROM events AS e";
        if (!empty($EventId)) {
            $EventSql .= " WHERE e.id=" . $EventId;
        }
        // dd($Category);
        if (!empty($Category)) {
            //$EventSql .= " LEFT JOIN event_category AS ec ON e.id = ec.event_id WHERE ec.category_id=" . $Category;
            // $EventSql .= " LEFT JOIN event_tickets AS et ON e.id = et.event_id WHERE et.category =" . $Category;
        }

        if (!empty($Category) && !empty($event_ids)) {
            $EventSql .= " WHERE e.id IN(".$event_ids.") ";
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
            $EventSql .= " AND e.registration_start_time >=" . $StartDateTime;
        }
        if ((!empty($EndDateTime)) && (empty($StartDateTime))) {
            $EventSql .= " AND e.registration_end_time <=" . $EndDateTime;
        }
        if ((!empty($StartDateTime)) && (!empty($EndDateTime))) {
            $EventSql .= ' AND e.registration_end_time BETWEEN ' . $StartDateTime . ' AND ' . $EndDateTime;
            // $EventSql .= ' AND e.registration_start_time >=' . $StartDateTime . ' AND e.registration_end_time <= ' . $EndDateTime;
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

        // return $EventSql;
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

                case 'quarter':
                    $currentMonth = date('m');
                    $currentYear = date('Y');
                    $StartDate = strtotime(date('Y-m-d', strtotime("$currentYear-$currentMonth-01 -1 month")));
                    $EndDate = strtotime(date('Y-m-t 23:59:59', strtotime("$currentYear-$currentMonth-01 +1 months")));
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
        // dd($EventSql);

        $Events = DB::select($EventSql);

        // $temp_array = array_unique(array_column($Events, 'event_id'));
        // $filteredData = array_intersect_key($Events, $temp_array);

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
        $EventName = isset($request->event_name) ? str_replace("_", " ", $request->event_name) : '';//for share event link
        // dd($EventName);
        if (!empty($EventId)) {
            $EventSql = "SELECT * FROM events AS e WHERE e.id=:event_id";
            $Events = DB::select($EventSql, array('event_id' => $EventId));
            // dd($Events);
        }
        if ($EventName !== "") {
            $EventSql = "SELECT * FROM events AS e WHERE e.name=:event_name";
            $Events = DB::select($EventSql, array('event_name' => $EventName));
            $EventId = sizeof($Events) > 0 ? $Events[0]->id : 0;
        }

        // Get Event Tickets 
        $sSQL = 'SELECT id,ticket_name FROM event_tickets WHERE event_id =:event_id AND is_deleted = 0 AND active = 1';
        $AllTickets = DB::select($sSQL, array('event_id' => $EventId));
        $ResponseData['AllTickets'] = $AllTickets ? $AllTickets : [];

        // dd($Events);
        $ResponseData['EventData'] = $this->ManipulateEvents($Events, $UserId);
        $ResponseData['EventDetailId'] = $EventId ? $EventId : 0;
        $ResponseData['FAQ'] = [];

        if (!empty($EventId)) {
            $sSQL = 'SELECT * FROM event_FAQ WHERE event_id =:event_id AND status=1';
            $FAQ = DB::select($sSQL, array('event_id' => $EventId));
            $ResponseData['FAQ'] = $FAQ;
        }
        // dd($Events);
        #ORGANISER
        $ResponseData['OrganiserName'] = $ResponseData['OrganiserId'] = $ResponseData['UserId'] = "";
        if (!empty($Events[0]->created_by)) {
            $sql = "SELECT id,name FROM organizer WHERE user_id=:user_id";
            $Organiser = DB::select($sql, array('user_id' => $Events[0]->created_by));
            $ResponseData['OrganiserName'] = (sizeof($Organiser) > 0) ? ucwords($Organiser[0]->name) : "";
            $ResponseData['OrganiserId'] = (sizeof($Organiser) > 0) ? $Organiser[0]->id : 0;
            $ResponseData['UserId'] = $Events[0]->created_by;
        }
        // dd($Events);
        $response = [
            'status' => 200,
            'data' => $ResponseData,
            'message' => $message,
            'FewSuggestionFlag' => $FewSuggestionFlag
        ];

        return response()->json($response, $ResposneCode);
    }

    #API FOR CREATE EVENT PAGE
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

                $ByAdmin = (isset($request->by_admin) && !empty($request->by_admin)) ? $request->by_admin : 0;

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
                            "created_date" => time(),
                            "ytcr_base_price" => 40,
                            "by_admin" => $ByAdmin
                        );
                        $SQL = "INSERT INTO events (event_info_status,name,event_visibilty,event_display_name,event_url,event_type,created_by,created_date,ytcr_base_price,by_admin) VALUES(:event_info_status,:event_name,:display_name_status,:display_name,:event_url,:event_type,:created_by,:created_date,:ytcr_base_price,:by_admin)";
                        DB::insert($SQL, $Bindings);
                        $EventId = DB::getPdo()->lastInsertId();

                        //------------ added manual FAQ questions -------------
                        if (!empty($EventId)) {

                            $insert_sSQL = 'INSERT INTO event_FAQ (event_id, user_id, question, answer, custom_faq)';
                            $insert_sSQL .= 'SELECT :eventId, :user_id, faq_name, faq_answer, :customFaq
                                FROM FAQ_master
                                WHERE faq_status = 1 ';
                            //dd($insert_sSQL);
                            DB::insert(
                                $insert_sSQL,
                                array(
                                    'eventId' => $EventId,
                                    'user_id' => $UserId,
                                    'customFaq' => 1
                                )
                            );
                        }

                        //------------ added manual Communication questions -------------
                        if (!empty($EventId)) {
                            $insert_sSQL1 = 'INSERT INTO event_communication (event_id, user_id, subject_name, message_content, email_type,comm_id)';
                            $insert_sSQL1 .= 'SELECT :eventId, :user_id, subject_name, message_content, email_type, id
                                FROM communication_master
                                WHERE status = 1 ';
                            //dd($insert_sSQL);
                            DB::insert(
                                $insert_sSQL1,
                                array(
                                    'eventId' => $EventId,
                                    'user_id' => $UserId
                                )
                            );
                        }

                        //------------ added manual terms & conditions -------------
                        if (!empty($EventId)) {
                            $insert_sSQL2 = 'INSERT INTO event_terms_conditions (event_id, user_id, title, terms_conditions)';
                            $insert_sSQL2 .= 'SELECT :eventId, :user_id, subject_name, message_content
                                FROM communication_master
                                WHERE status = 0 ';
                            //dd($insert_sSQL);
                            DB::insert(
                                $insert_sSQL2,
                                array(
                                    'eventId' => $EventId,
                                    'user_id' => $UserId
                                )
                            );
                        }

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

                        //------------ added manual race category charges on 12-07-2024 -------------
                        if (!empty($EventId)) {
                            $races_charges_array = array(
                                array("registration_amount" => 1000, "convenience_fee" => 2),
                                array("registration_amount" => 1400, "convenience_fee" => 30),
                                array("registration_amount" => 1401, "convenience_fee" => 40)
                            );

                            foreach ($races_charges_array as $res) {
                                    $sql1 = "INSERT INTO race_category_charges (event_id, registration_amount, convenience_fee, platform_fee,    payment_gateway_fee, created_by) VALUES(:event_id,:registration_amount,:convenience_fee,:platform_fee,:payment_gateway_fee,:created_by)";
                                    $Bind1 = array(
                                        "event_id" => $EventId,
                                        "registration_amount" => $res['registration_amount'],
                                        "convenience_fee" => $res['convenience_fee'],
                                        "platform_fee" => 5,
                                        "payment_gateway_fee" => 1.85,
                                        "created_by" => $UserId
                                    );
                                    DB::insert($sql1, $Bind1);
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
                $sql = "SELECT * from events WHERE deleted=0"; //AND active=1
                $sql .= " AND id=" . $EventId;
                $Events = DB::select($sql);
            }

            // dd($Events);
            $master = new Master();
            $e = new Event();
            foreach ($Events as $event) {
                $event->start_date = (!empty($event->start_time)) ? date("Y-m-d", $event->start_time) : 0;
                $event->start_time_event = (!empty($event->start_time)) ? date("H:i", $event->start_time) : 0;

                $event->end_date = (!empty($event->end_time)) ? date("Y-m-d", $event->end_time) : 0;
                $event->end_time_event = (!empty($event->end_time)) ? date("H:i", $event->end_time) : 0;

                $event->repeat_start_time = (!empty($event->repeat_start_time)) ? date("H:i", $event->repeat_start_time) : 0;
                $event->repeat_end_time = (!empty($event->repeat_end_time)) ? date("H:i", $event->repeat_end_time) : 0;

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
                $event->start_event_month = (!empty($event->start_time)) ? date("M", $event->start_time) : "";
                //event start d
                $event->start_event_date = (!empty($event->start_time)) ? date("d", $event->start_time) : "";

                //registration closing date
                $event->registration_end_date = (!empty($event->registration_end_time)) ? date("d M Y", $event->registration_end_time) : "";

                $event->diplay_registration_start_date = (!empty($event->registration_start_time)) ? date("Y-m-d", $event->registration_start_time) : 0;
                $event->diplay_registration_start_time = (!empty($event->registration_start_time)) ? date("H:i", $event->registration_start_time) : 0;

                $event->diplay_registration_end_date = (!empty($event->registration_end_time)) ? date("Y-m-d", $event->registration_end_time) : 0;
                $event->diplay_registration_end_time = (!empty($event->registration_end_time)) ? date("H:i", $event->registration_end_time) : 0;

                if(!empty($event->description_image)){
                    $img_url = url('/').'/uploads/description_images/'.$event->description_image;
                    $event->content_image = '<img src="'.$img_url.'" height="50%" width="50%" alt="Image">';
                }else{
                    $event->content_image   = '';
                }

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
                "start_event_month" => (isset($Events[0]->start_time) && (!empty($Events[0]->start_time))) ? date("M", $Events[0]->start_time) : date("M", strtotime('now')),
                "start_event_date" => (isset($Events[0]->start_time) && (!empty($Events[0]->start_time))) ? date("d", $Events[0]->start_time) : date("d", strtotime('now')),
                "pre_start_event_month" => (isset($Events[0]->start_time) && (!empty($Events[0]->start_time))) ? date("M", $Events[0]->start_time) : date("M", strtotime('now')),
                "pre_start_event_date" => (isset($Events[0]->start_time) && (!empty($Events[0]->start_time))) ? date("d", $Events[0]->start_time) : date("d", strtotime('now')),
                // "registration_end_date" => (isset($Events[0]->registration_end_time) && !empty($Events[0]->registration_end_time)) ? date("d F Y", $event->registration_end_time) : date("d F Y", strtotime('now')),
                "registration_end_date" => (isset($Events[0]->registration_end_time) && !empty($Events[0]->registration_end_time)) ? date("d F Y", $event->registration_end_time) : date("d F Y", strtotime('now')),

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
            $sql = "SELECT * FROM event_tickets WHERE event_id=:event_id AND is_deleted = 0 AND active = 1 order by sort_order asc";
            $EventTickets = DB::select($sql, array('event_id' => $EventId));
            foreach ($EventTickets as $ticket) {
                $ticket->ticket_sale_start_date = (!empty($ticket->ticket_sale_start_date)) ? date("d F Y", $ticket->ticket_sale_start_date) : 0;
                $ticket->ticket_sale_end_date = (!empty($ticket->ticket_sale_end_date)) ? date("d F Y", $ticket->ticket_sale_end_date) : 0;

            }
            $ResponseData['EventTickets'] = $EventTickets;

            //------ setting tab get info
            // $sql = "SELECT es.*,(select ytcr_base_price from events where id = " . $EventId . ") as ytcr_base_price FROM event_settings as es WHERE event_id =:event_id";
            // $SettingInfoDetails = DB::select($sql, array('event_id' => $EventId));
            // //dd($SettingInfoDetails);
            // if (!empty($SettingInfoDetails)) {
            //     $ResponseData['event_setting_details'] = $SettingInfoDetails;
            // } else {
            //     $ResponseData['event_setting_details'] = [];
            // }

            $sql = "SELECT overall_limit,allow_unique_registration,event_registration_status FROM events WHERE id =:id";
            $SettingInfoDetails = DB::select($sql, array('id' => $EventId));
            //dd($SettingInfoDetails);
            if (!empty($SettingInfoDetails)) {

                if(!empty($SettingInfoDetails[0]->overall_limit) || !empty($SettingInfoDetails[0]->allow_unique_registration) || $SettingInfoDetails[0]->event_registration_status == 1){
                    $ResponseData['event_setting_details'] = $SettingInfoDetails;
                }else{
                    $ResponseData['event_setting_details'] = [];
                }
                
            } else {
                $ResponseData['event_setting_details'] = [];
            }

            $sql = "SELECT gst_percentage,gst FROM organizer WHERE user_id = :user_id";
            $OrganizerInfoDetails = DB::select($sql, array('user_id' => $UserId));

            $ResponseData['GST_PERCENTAGE'] = !empty($OrganizerInfoDetails) && !empty($OrganizerInfoDetails[0]->gst_percentage) &&
                $OrganizerInfoDetails[0]->gst == 1 ? $OrganizerInfoDetails[0]->gst_percentage : 0;

            //$ResponseData['YTCR_FEE_PERCENTAGE'] = !empty($SettingInfoDetails) && !empty($SettingInfoDetails[0]->ticket_ytcr_base_price) ?
            //   $SettingInfoDetails[0]->ticket_ytcr_base_price : config('custom.ytcr_fee_percent');

            $ytcr_base_price = !empty($Events) && $Events[0]->ytcr_base_price ? $Events[0]->ytcr_base_price : 0;
            $ResponseData['YTCR_FEE_PERCENTAGE'] = !empty($SettingInfoDetails) && !empty($SettingInfoDetails[0]->ticket_ytcr_base_price) ?
                $SettingInfoDetails[0]->ticket_ytcr_base_price : (int) $ytcr_base_price;

            $ResponseData['PAYMENT_GATEWAY_FEE_PERCENTAGE'] = config('custom.payment_gateway_fee_percent');
            $ResponseData['PAYMENT_GATEWAY_GST_PERCENTAGE'] = config('custom.payment_gateway_gst_percent');

            //----------
            $sSQL = 'SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = "Races2.0_Web" AND TABLE_NAME = "users" ';
            $ResponseData['field_mapping_details'] = DB::select($sSQL, array());


            // ---------- get communication tab details
            $sql1 = "SELECT id,subject_name,message_content,status FROM event_communication WHERE event_id=:event_id AND user_id=:user_id ";
            $CommResult = DB::select($sql1, array('event_id' => $EventId, 'user_id' => $UserId));
            //dd($CommResult);
            $is_comm_selected = 0;
            if (!empty($CommResult)) {
                foreach ($CommResult as $res) {
                    if ($res->status == 1) {
                        $res->status = true;
                    } else {
                        $res->status = false;
                    }

                    if ($res->status == 1) {
                        $is_comm_selected = 1;
                    }

                }
            }

            $ResponseData['communication_details'] = !empty($CommResult) ? $CommResult : [];

            // ---------- get FAQ tab details
            $sql1 = "SELECT id,question,answer,status,custom_faq FROM event_FAQ WHERE event_id=:event_id AND user_id=:user_id ";
            $FAQResult = DB::select($sql1, array('event_id' => $EventId, 'user_id' => $UserId));
            //dd($CommResult);
            $is_one_selected = 0;
            if (!empty($FAQResult)) {
                foreach ($FAQResult as $res) {
                    if ($res->status == 1) {
                        $res->status = true;
                    } else {
                        $res->status = false;
                    }

                    if ($res->status == 1) {
                        $is_one_selected = 1;
                    }

                }
            }

            $ResponseData['faq_selected_flag'] = $is_one_selected;
            $ResponseData['comm_selected_flag'] = $is_comm_selected;
            $ResponseData['faq_details'] = !empty($FAQResult) ? $FAQResult : [];

            // ---------- get Tickets details ticket_status = 1
            $sql1 = "SELECT id,ticket_name FROM event_tickets WHERE event_id=:event_id AND active = 1 AND is_deleted = 0";
            $TicketResult = DB::select($sql1, array('event_id' => $EventId));
            //dd($CommResult);
            if (!empty($TicketResult)) {
                foreach ($TicketResult as $res) {
                    $res->checked = false;
                }
            }
            $ResponseData['tickets_details'] = !empty($TicketResult) ? $TicketResult : [];

            $sql2 = "SELECT id,ticket_name FROM event_tickets WHERE event_id=:event_id AND active = 1 AND ticket_status = 1 AND is_deleted = 0";
            $TicketResult1 = DB::select($sql2, array('event_id' => $EventId));
            //dd($CommResult);
            if (!empty($TicketResult1)) {
                foreach ($TicketResult1 as $res) {
                    $res->checked = false;
                }
            }
            $ResponseData['coupon_tickets_details'] = !empty($TicketResult1) ? $TicketResult1 : [];

            $sql2 = "SELECT id,ticket_name FROM event_tickets WHERE event_id=:event_id AND is_deleted = 0";
            $TicketResult1 = DB::select($sql2, array('event_id' => $EventId));
            //dd($CommResult);
            if (!empty($TicketResult1)) {
                foreach ($TicketResult1 as $res) {
                    $res->checked = false;
                }
            }
            $ResponseData['form_tickets_details'] = !empty($TicketResult1) ? $TicketResult1 : [];

            // ---------- get Coupons Details
            $sql1 = "SELECT id,discount_type,discount_name,coupon_status FROM event_coupon WHERE is_deleted = 0 AND event_id=:event_id";
            $CouponResult = DB::select($sql1, array('event_id' => $EventId));

            if (!empty($CouponResult)) {
                foreach ($CouponResult as $res) {
                    $sql2 = "SELECT * FROM event_coupon_details WHERE event_id=:event_id AND event_coupon_id=:event_coupon_id ";
                    $CouponDetailsResult = DB::select($sql2, array('event_id' => $EventId, 'event_coupon_id' => $res->id));

                    $res->no_of_discount = !empty($CouponDetailsResult[0]->no_of_discount) ? $CouponDetailsResult[0]->no_of_discount : '';
                    $res->discount_amt_per_type = !empty($CouponDetailsResult[0]->discount_amt_per_type) ? (string) $CouponDetailsResult[0]->discount_amt_per_type : '';
                    $res->discount_amount = !empty($CouponDetailsResult[0]->discount_amount) ? $CouponDetailsResult[0]->discount_amount : '';
                    $res->discount_percentage = !empty($CouponDetailsResult[0]->discount_percentage) ? $CouponDetailsResult[0]->discount_percentage : '';
                    $res->expired_date = !empty($CouponDetailsResult[0]->discount_to_datetime) ? date('d-m-Y', $CouponDetailsResult[0]->discount_to_datetime) : '';
                    $res->discount_code = !empty($CouponDetailsResult[0]->discount_code) ? $CouponDetailsResult[0]->discount_code : '';
                    $res->coupon_status = !empty($res->coupon_status) ? true : false;
                    $res->user_email_address = !empty($CouponDetailsResult[0]->user_email_address) ? $CouponDetailsResult[0]->user_email_address : '';
                    $res->discount_coupon_flag = !empty($CouponDetailsResult) && count($CouponDetailsResult) > 1 ? 1 : 0;

                    foreach ($CouponDetailsResult as $val) {
                        $res->multiple_coupon_details[] = $val->discount_code;
                    }
                }
            }
            //dd($CouponResult);
            $ResponseData['coupon_details'] = !empty($CouponResult) ? $CouponResult : [];

            //-------- age criteria dropdown -----
            $new_array = [];

            for ($i = 1; $i <= 110; $i++) {
                // code...
                $ResponseData['age_details'][] = array("id" => $i, "name" => $i);
            }
            //dd($new_array);

            // ---------- get Age Criteria
            $sql1 = "SELECT id,distance_category,age_category,status   FROM age_criteria WHERE event_id=:event_id";
            $ageCriteriaResult = DB::select($sql1, array('event_id' => $EventId));
            //dd($CommResult);
            if (!empty($ageCriteriaResult)) {
                foreach ($ageCriteriaResult as $res) {
                    if ($res->status == 1) {
                        $res->status = true;
                    } else {
                        $res->status = false;
                    }
                }
            }
            $ResponseData['age_criteria_details'] = !empty($ageCriteriaResult) ? $ageCriteriaResult : [];

            // ---------- get terms & conditions tab details
            $sql1 = "SELECT id,title,terms_conditions,status FROM event_terms_conditions WHERE event_id=:event_id AND user_id=:user_id ";
            $CommResult = DB::select($sql1, array('event_id' => $EventId, 'user_id' => $UserId));
            //dd($CommResult);
            if (!empty($CommResult)) {
                foreach ($CommResult as $res) {
                    if ($res->status == 1) {
                        $res->status = true;
                    } else {
                        $res->status = false;
                    }
                }
            }
            $ResponseData['terms_conditions_details'] = !empty($CommResult) ? $CommResult : [];

            // ---------- get event form question
            $sql1 = "SELECT id FROM event_form_question WHERE event_id=:event_id";
            $ResponseData['event_form_question_details'] = DB::select($sql1, array('event_id' => $EventId));
            
            // ---------- get races category charges
            $sql1 = "SELECT id,registration_amount,convenience_fee,platform_fee,payment_gateway_fee FROM race_category_charges WHERE event_id=:event_id";
            $chargesResult = DB::select($sql1, array('event_id' => $EventId));
            $ResponseData['race_category_charges_details'] = !empty($chargesResult) ? $chargesResult : [];

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

    public function eventIntegration(Request $request)
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
            $PopupFlag = isset($request->popup_flag) ? $request->popup_flag : 0;

            if (empty($aPost['event_id'])) {
                $empty = true;
                $field = 'Event Id';
            }

            if (!$empty) {
                
                $Bindings = array(
                    "tcs_popup" => $PopupFlag,
                    "id" => $aPost['event_id']
                );
                DB::update("UPDATE events SET tcs_popup=:tcs_popup WHERE id=:id", $Bindings);
                $ResposneCode = 200;
                $message = "Request processed successfully";
            }else {
                $ResposneCode = 400;
                $message = $field . ' is empty';
            }
        }else {
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

                $FinalRegistrationStartTime = $FinalRegistrationEndTime = 0;
                $RegistrationStartDate = isset($request->registration_start_date) ? $request->registration_start_date : 0;
                $RegistrationStartTime = isset($request->registration_start_time) ? $request->registration_start_time : 0;
                if (!empty($RegistrationStartDate) && !empty($RegistrationStartTime)) {
                    $start_date_time_string1 = $RegistrationStartDate . ' ' . $RegistrationStartTime;
                    $FinalRegistrationStartTime = strtotime($start_date_time_string1);
                } else if (!empty($RegistrationStartDate) && empty($RegistrationStartTime)) {
                    $FinalRegistrationStartTime = strtotime($RegistrationStartDate);
                }

                $RegistrationEndDate = isset($request->registration_end_date) ? $request->registration_end_date : 0;
                $RegistrationEndTime = isset($request->registration_end_time) ? $request->registration_end_time : 0;
                if (!empty($RegistrationEndDate) && !empty($RegistrationEndTime)) {
                    $end_date_time_string1 = $RegistrationEndDate . ' ' . $RegistrationEndTime;
                    $FinalRegistrationEndTime = strtotime($end_date_time_string1);
                } else if (!empty($RegistrationEndDate) && empty($RegistrationEndTime)) {
                    $FinalRegistrationEndTime = strtotime($RegistrationEndDate);
                }
                $PinCode = isset($request->pincode) ? $request->pincode : "";
                $Latitude = isset($request->latitude) ? $request->latitude : "";
                $Longitude = isset($request->longitude) ? $request->longitude : "";

                $GoogleMapLink = isset($request->google_map_link) ? $request->google_map_link : "";

                $Bindings = array(
                    "timezone_id" => $Timezone,
                    "pincode" => $PinCode,
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
                    "registration_start_time" => $FinalRegistrationStartTime,
                    "registration_end_time" => $FinalRegistrationEndTime,
                    "latitude" => $Latitude,
                    "longitude" => $Longitude,
                    "google_map_link" => $GoogleMapLink,
                    "id" => $EventId
                );
                $sql = 'UPDATE events SET
                time_zone=:timezone_id,
                pincode=:pincode,
                start_time=:start_time,
                end_time=:end_time,
                is_repeat=:is_repeat,
                repeat_type=:repeat_type,
                repeat_start_time=:repeat_start_time,
                repeat_end_time=:repeat_end_time,
                country=:country,
                state=:state,
                city=:city,
                address=:address,
                registration_start_time=:registration_start_time,
                registration_end_time=:registration_end_time,
                latitude=:latitude,
                longitude=:longitude,
                google_map_link=:google_map_link
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

                //------------ description image upload for (ckeditor)
                $content_image = '';
                $description_image = !empty($request->description_image) ? $request->description_image : '';
                if(!empty($description_image)){
                    $Path1 = public_path('uploads/description_images/');
                    $desc_image = $request->file('description_image');
                    $originalName1 = $desc_image->getClientOriginalName();
                    $content_image = $originalName1;
                    $desc_image->move($Path1, $content_image);
                }
               
                $pattern = '/<img[^>]*>/i';  // Case-insensitive match for <img> tags
                $updatedHtml = preg_replace($pattern, '', $Description);
                $updatedHtml = str_replace('<p></p>','', $updatedHtml);
                $final_description = str_replace('<p>&nbsp;</p>','', $updatedHtml);
                $final_description = str_replace('<figure class="image"></figure>','', $final_description);
                
                if(!empty($description_image)){
                    $bindings = [
                    "description" => $final_description,
                    "event_keywords" => $event_keywords,
                    "description_image" => $content_image,
                    "id" => $EventId
                    ];
                    $sql = 'UPDATE events SET event_description=:description, event_keywords=:event_keywords, description_image=:description_image WHERE id=:id';
                }else{
                    $bindings = [
                    "description" => $final_description,
                    "event_keywords" => $event_keywords,
                    "id" => $EventId
                    ];
                    $sql = 'UPDATE events SET event_description=:description, event_keywords=:event_keywords WHERE id=:id';
                }
               
        
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

               
                $OverallLimit = !empty($request->overall_limit) ? $request->overall_limit : '';
                $AllowUniqueRegistration = !empty($request->allow_unique_registration) ? 1 : 0;
                $EventRegistrationStatus = !empty($request->event_registration_status) ? $request->event_registration_status : 0;

                $up_sql = 'UPDATE events SET overall_limit =:overall_limit, allow_unique_registration =:allow_unique_registration, event_registration_status =:event_registration_status WHERE id = :id';
                $bindings = ["overall_limit" => $OverallLimit, "allow_unique_registration" => $AllowUniqueRegistration, "event_registration_status" => $EventRegistrationStatus, "id" => $EventId];
                DB::update($up_sql, $bindings);

                $ResposneCode = 200;
                $message = "Event Setting updated successfully";

                // $NoOfPreviousConducts = !empty($request->no_of_previous_conducts) ? $request->no_of_previous_conducts : 0;
                // $NoOfRunnersEstimate = !empty($request->no_of_runners_estimate) ? $request->no_of_runners_estimate : 0;
                // $NoOfEventYear = !empty($request->no_of_event_year) ? $request->no_of_event_year : 0;

                // $ContractOfFiveYear = !empty($request->contract_of_five_year) ? 1 : 0;
                // $BackendSupport = !empty($request->backend_support) ? 1 : 0;
                // $BulkRegistration = !empty($request->bulk_registration) ? 1 : 0;
                // $CheckValidEntries = !empty($request->check_valid_entries) ? 1 : 0;

                // $YtcrBasePrice = !empty($request->ytcr_base_price) ? $request->ytcr_base_price : "0.00";
                // $EventSettingId = !empty($request->event_setting_id) ? $request->event_setting_id : 0;
                // $EditYtcrBasePrice = !empty($request->edit_ytcr_base_price) ? $request->edit_ytcr_base_price : "";

                // $SQL = "SELECT id FROM event_settings WHERE event_id =:event_id";
                // $IsExist = DB::select($SQL, array('event_id' => $EventId));

                // if (empty($IsExist) && empty($EventSettingId)) {

                //     $Bindings = array(
                //         "event_id" => $EventId,
                //         "no_of_previous_conducts" => $NoOfPreviousConducts,
                //         "no_of_runners_estimate" => $NoOfRunnersEstimate,
                //         "no_of_event_year" => $NoOfEventYear,
                //         "contract_of_five_year" => $ContractOfFiveYear,
                //         "backend_support" => $BackendSupport,
                //         "bulk_registration" => $BulkRegistration,
                //         "check_valid_entries" => $CheckValidEntries,
                //         "ticket_ytcr_base_price" => $YtcrBasePrice,
                //         "created_by" => $UserId,
                //     );

                //     $insert_SQL = "INSERT INTO event_settings (event_id,no_of_previous_conducts,no_of_runners_estimate,no_of_event_year,contract_of_five_year,backend_support,bulk_registration,check_valid_entries,ticket_ytcr_base_price,created_by) VALUES(:event_id,:no_of_previous_conducts,:no_of_runners_estimate,:no_of_event_year,:contract_of_five_year,:backend_support,:bulk_registration,:check_valid_entries,:ticket_ytcr_base_price,:created_by)";
                //     DB::insert($insert_SQL, $Bindings);

                //     $up_sql = 'UPDATE events SET ytcr_base_price = :ytcr_base_price WHERE id = :id';
                //     $bindings = ["ytcr_base_price" => $EditYtcrBasePrice, "id" => $EventId];
                //     DB::update($up_sql, $bindings);

                //     $ResposneCode = 200;
                //     $message = "Event Setting added successfully";

                // } else {

                //     $Bindings = array(
                //         "no_of_previous_conducts" => $NoOfPreviousConducts,
                //         "no_of_runners_estimate" => $NoOfRunnersEstimate,
                //         "no_of_event_year" => $NoOfEventYear,
                //         "contract_of_five_year" => $ContractOfFiveYear,
                //         "backend_support" => $BackendSupport,
                //         "bulk_registration" => $BulkRegistration,
                //         "check_valid_entries" => $CheckValidEntries,
                //         "ticket_ytcr_base_price" => $YtcrBasePrice,
                //         "event_setting_id" => $EventSettingId
                //     );

                //     $sql = 'UPDATE event_settings SET no_of_previous_conducts = :no_of_previous_conducts, no_of_runners_estimate  = :no_of_runners_estimate, no_of_event_year = :no_of_event_year, contract_of_five_year = :contract_of_five_year, backend_support = :backend_support, bulk_registration = :bulk_registration, check_valid_entries = :check_valid_entries, ticket_ytcr_base_price  = :ticket_ytcr_base_price WHERE id = :event_setting_id';
                //     // dd($sql);
                //     DB::update($sql, $Bindings);

                //     $up_sql = 'UPDATE events SET ytcr_base_price = :ytcr_base_price WHERE id = :id';
                //     $bindings = ["ytcr_base_price" => $EditYtcrBasePrice, "id" => $EventId];
                //     DB::update($up_sql, $bindings);

                //     $ResposneCode = 200;
                //     $message = "Event Setting updated successfully";
                // }

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

    public function addCommunication(Request $request)
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

                $SubjectName = !empty($request->subject_name) ? $request->subject_name : 0;
                $MessageContent = !empty($request->message_content) ? $request->message_content : 0;
                $EventCommunicationId = !empty($request->event_comm_id) ? $request->event_comm_id : 0;

                // $SQL = "SELECT id FROM event_communication WHERE event_id =:event_id";
                // $IsExist = DB::select($SQL, array('event_id' => $EventId));
                $pattern = '/<img[^>]*>/i';  // Case-insensitive match for <img> tags
                $updatedHtml = preg_replace($pattern, '', $MessageContent);
                $updatedHtml = str_replace('<p></p>','', $updatedHtml);
                $updatedHtml = str_replace('<p>&nbsp;</p>','', $updatedHtml);

                if (empty($EventCommunicationId)) {

                    $Bindings = array(
                        "event_id" => $EventId,
                        "user_id" => $UserId,
                        "subject_name" => $SubjectName,
                        "message_content" => $MessageContent
                    );

                    $insert_SQL = "INSERT INTO event_communication (event_id,user_id,subject_name,message_content) VALUES(:event_id,:user_id,:subject_name,:message_content)";
                    DB::insert($insert_SQL, $Bindings);

                    $ResposneCode = 200;
                    $message = "Communication added successfully";

                } else {

                    $Bindings = array(
                        "subject_name" => $SubjectName,
                        "message_content" => $updatedHtml,
                        "event_comm_id" => $EventCommunicationId
                    );

                    $sql = 'UPDATE event_communication SET subject_name = :subject_name, message_content  = :message_content WHERE id = :event_comm_id';
                    // dd($sql);
                    DB::update($sql, $Bindings);

                    $ResposneCode = 200;
                    $message = "Communication updated successfully";
                }

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

    public function communicationMessageImage(Request $request)
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
                    $EventCommunicationId = !empty($request->event_comm_id) ? $request->event_comm_id : 0;
                    // $MessageContent = !empty($request->message_content) ? $request->message_content : '';

                    if (!empty($request->file('file'))) {
                        $Path = public_path('uploads/communication_email_images/');
                        $logo_image = $request->file('file');
                        $originalName = $logo_image->getClientOriginalName();
                        $content_image = $originalName;
                        $logo_image->move($Path, $content_image);

                        // $pattern = '/<img[^>]*>/i';  // Case-insensitive match for <img> tags
                        // $updatedHtml = preg_replace($pattern, '', $MessageContent);
                        // $updatedHtml = str_replace('<p></p>','', $updatedHtml);
                        // $updatedHtml = str_replace('<p>&nbsp;</p>','', $updatedHtml);

                        $Bindings = array(
                            "content_image" => $content_image,
                            // "message_content" => $updatedHtml,
                            "event_comm_id" => $EventCommunicationId,
                            "event_id" => $EventId
                        );

                        $sql = 'UPDATE event_communication SET content_image =:content_image WHERE id = :event_comm_id AND event_id =:event_id';
                        // dd($sql); , message_content =:message_content
                        DB::update($sql, $Bindings);
                    }
               
                    $ResposneCode = 200;
                    $message = "Communication updated successfully";
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

    public function addFAQ(Request $request)
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

                $QuestionName = !empty($request->quetion_name) ? $request->quetion_name : 0;
                $Answer = !empty($request->answer) ? $request->answer : 0;
                $EventCommunicationId = !empty($request->event_comm_id) ? $request->event_comm_id : 0;

                // $SQL = "SELECT id FROM event_communication WHERE event_id =:event_id";
                // $IsExist = DB::select($SQL, array('event_id' => $EventId));

                if (empty($EventCommunicationId)) {

                    $Bindings = array(
                        "event_id" => $EventId,
                        "user_id" => $UserId,
                        "question" => $QuestionName,
                        "answer" => $Answer
                    );

                    $insert_SQL = "INSERT INTO event_FAQ (event_id,user_id,question,answer) VALUES(:event_id,:user_id,:question,:answer)";
                    DB::insert($insert_SQL, $Bindings);

                    $ResposneCode = 200;
                    $message = "FAQ added successfully";

                } else {

                    $Bindings = array(
                        "question" => $QuestionName,
                        "answer" => $Answer,
                        "event_comm_id" => $EventCommunicationId
                    );

                    $sql = 'UPDATE event_FAQ SET question = :question, answer  = :answer WHERE id = :event_comm_id';
                    // dd($sql);
                    DB::update($sql, $Bindings);

                    $ResposneCode = 200;
                    $message = "FAQ updated successfully";
                }

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

    // Delete Communication and FAQ
    public function deleteEventCommFqa(Request $request)
    {
        $response['data'] = [];
        $response['message'] = '';
        $ResposneCode = 400;
        $empty = false;

        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        //dd($aToken);
        if ($aToken['code'] == 200) {

            $EventId = !empty($request->event_id) ? $request->event_id : 0;
            $EventCommId = !empty($request->event_comm_id) ? $request->event_comm_id : 0;
            $CommonFlag = !empty($request->common_flag) ? $request->common_flag : '';

            if (isset($request->common_flag) && $CommonFlag == 'faq_delete') {
                $del_sSQL = 'DELETE FROM event_FAQ WHERE `event_id`=:eventId AND `id`=:event_comm_id ';
                DB::delete(
                    $del_sSQL,
                    array(
                        'eventId' => $EventId,
                        'event_comm_id' => $EventCommId
                    )
                );
            } else if (isset($request->common_flag) && $CommonFlag == 'coupon_delete') {

                $Bindings = array(
                    "is_deleted" => 1,
                    'event_comm_id' => $EventCommId
                );
                $sql = 'UPDATE event_coupon SET is_deleted = :is_deleted WHERE id = :event_comm_id';
                DB::update($sql, $Bindings);

                $del_sSQL = 'DELETE FROM event_coupon_details WHERE `event_id`=:eventId AND `event_coupon_id`=:event_comm_id ';
                DB::delete(
                    $del_sSQL,
                    array(
                        'eventId' => $EventId,
                        'event_comm_id' => $EventCommId
                    )
                );
            } else if (isset($request->common_flag) && $CommonFlag == 'age_delete') {
                $del_sSQL = 'DELETE FROM age_criteria WHERE `event_id`=:eventId AND `id`=:event_comm_id ';
                DB::delete(
                    $del_sSQL,
                    array(
                        'eventId' => $EventId,
                        'event_comm_id' => $EventCommId
                    )
                );
            } else if (isset($request->common_flag) && $CommonFlag == 'delete_terms') {
                $del_sSQL = 'DELETE FROM event_terms_conditions WHERE `event_id`=:eventId AND `id`=:event_comm_id ';
                DB::delete(
                    $del_sSQL,
                    array(
                        'eventId' => $EventId,
                        'event_comm_id' => $EventCommId
                    )
                );
            } else {
                $del_sSQL1 = 'DELETE FROM event_communication WHERE `event_id`=:eventId AND `id`=:event_comm_id ';
                DB::delete(
                    $del_sSQL1,
                    array(
                        'eventId' => $EventId,
                        'event_comm_id' => $EventCommId
                    )
                );
            }

            $response['data'] = [];
            $response['message'] = 'Record removed successfully';
            $ResposneCode = 200;

        } else {
            $ResposneCode = $aToken['code'];
            $response['message'] = $aToken['message'];
        }

        return response()->json($response, $ResposneCode);
    }

    // Featch Edit Communication and FAQ
    public function editEventCommFqa(Request $request)
    {
        $response['data'] = [];
        $response['message'] = '';
        $ResposneCode = 400;
        $empty = false;

        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        //dd($aToken);
        if ($aToken['code'] == 200) {

            $aResult = [];

            $EventId = !empty($request->event_id) ? $request->event_id : 0;
            $EventCommId = !empty($request->event_comm_id) ? $request->event_comm_id : 0;
            $EventEditFlag = !empty($request->event_edit_flag) ? $request->event_edit_flag : '';

            if ($EventEditFlag == 'communication_edit') {

                $Sql = 'SELECT id,subject_name,message_content,content_image FROM event_communication WHERE `event_id`=:eventId AND `id`=:event_comm_id  ';
                $aCommResult = DB::select(
                    $Sql,
                    array(
                        'eventId' => $EventId,
                        'event_comm_id' => $EventCommId
                    )
                );

               if(!empty($aCommResult)){
                   foreach($aCommResult as $res){
                        if(!empty($res->content_image)){
                            $img_url = url('/').'/uploads/communication_email_images/'.$res->content_image;
                            $res->content_image = '<img src="'.$img_url.'" height="50%" width="50%" alt="Image">';
                        }else{
                            $res->message_content = $res->message_content;
                            $res->content_image   = '';
                        }
                   }
               } 

                $aResult['communication_edit_details'] = $aCommResult; 
            } else if ($EventEditFlag == 'faq_edit') {

                $Sql = 'SELECT id,question,answer FROM event_FAQ WHERE `event_id`=:eventId AND `id`=:event_comm_id  ';
                $aResult['faq_edit_details'] = DB::select(
                    $Sql,
                    array(
                        'eventId' => $EventId,
                        'event_comm_id' => $EventCommId
                    )
                );

            } else if ($EventEditFlag == 'age_criteria_edit') {

                $Sql = 'SELECT * FROM age_criteria WHERE `event_id`=:eventId AND `id`=:event_comm_id  ';
                $aResult['age_criteria_details'] = DB::select(
                    $Sql,
                    array(
                        'eventId' => $EventId,
                        'event_comm_id' => $EventCommId
                    )
                );

            } else if ($EventEditFlag == 'term_conditions_edit') {

                $Sql = 'SELECT * FROM event_terms_conditions WHERE `event_id`=:eventId AND `id`=:event_comm_id  ';
                $aResult['terms_conditions_details'] = DB::select(
                    $Sql,
                    array(
                        'eventId' => $EventId,
                        'event_comm_id' => $EventCommId
                    )
                );

            } else if ($EventEditFlag == 'coupon_edit') {

                $Sql = 'SELECT id,discount_type,discount_name,description,show_public FROM event_coupon WHERE `event_id`=:eventId AND `id`=:event_comm_id';
                $CouponResult = DB::select(
                    $Sql,
                    array(
                        'eventId' => $EventId,
                        'event_comm_id' => $EventCommId
                    )
                );
                //dd($CouponResult);

                if (!empty($CouponResult)) {
                    foreach ($CouponResult as $res) {
                        $sql2 = "SELECT * FROM event_coupon_details WHERE event_id=:event_id AND event_coupon_id=:event_coupon_id ";
                        $CouponDetailsResult = DB::select($sql2, array('event_id' => $EventId, 'event_coupon_id' => $res->id));

                        $res->discount_amt_per_type = !empty($CouponDetailsResult[0]->discount_amt_per_type) ? (string) $CouponDetailsResult[0]->discount_amt_per_type : '';
                        $res->discount_amount = !empty($CouponDetailsResult[0]->discount_amount) ? $CouponDetailsResult[0]->discount_amount : '';
                        $res->discount_percentage = !empty($CouponDetailsResult[0]->discount_percentage) ? $CouponDetailsResult[0]->discount_percentage : '';
                        $res->code_type = !empty($CouponDetailsResult[0]->code_type) ? (string) $CouponDetailsResult[0]->code_type : '';
                        $res->no_of_discount = !empty($CouponDetailsResult[0]->no_of_discount) ? $CouponDetailsResult[0]->no_of_discount : '';
                        $res->discount_code = !empty($CouponDetailsResult[0]->discount_code) ? $CouponDetailsResult[0]->discount_code : '';
                        $res->prefix_code = !empty($CouponDetailsResult[0]->prefix_code) ? $CouponDetailsResult[0]->prefix_code : '';
                        $res->discount_from_date = !empty($CouponDetailsResult[0]->discount_from_datetime) ? date('Y-m-d', $CouponDetailsResult[0]->discount_from_datetime) : '';
                        $res->discount_from_time = !empty($CouponDetailsResult[0]->discount_from_datetime) ? date('h:i', $CouponDetailsResult[0]->discount_from_datetime) : '';
                        $res->discount_to_date = !empty($CouponDetailsResult[0]->discount_to_datetime) ? date('Y-m-d', $CouponDetailsResult[0]->discount_to_datetime) : '';
                        $res->discount_to_time = !empty($CouponDetailsResult[0]->discount_to_datetime) ? date('h:i', $CouponDetailsResult[0]->discount_to_datetime) : '';
                        $res->apply_ticket = !empty($CouponDetailsResult[0]->apply_ticket) ? $CouponDetailsResult[0]->apply_ticket : 1;
                        $res->user_email_address = !empty($CouponDetailsResult[0]->user_email_address) ? $CouponDetailsResult[0]->user_email_address : '';
                        $ticket_details_id = !empty($CouponDetailsResult[0]->ticket_details) ? $CouponDetailsResult[0]->ticket_details : '';
                        $new_array = explode(",", $ticket_details_id);
                        $Sql = 'SELECT id,ticket_name FROM event_tickets WHERE `event_id`=:eventId';
                        $TicketArray = DB::select(
                            $Sql,
                            array(
                                'eventId' => $EventId
                            )
                        );

                        if (!empty($TicketArray)) {
                            foreach ($TicketArray as $res1) {
                                if (in_array($res1->id, $new_array)) {
                                    $res1->checked = true;
                                } else {
                                    $res1->checked = false;
                                }
                            }
                        }

                        $res->edit_ticket_details = !empty($TicketArray) ? $TicketArray : [];
                    }
                }
                $aResult['coupon_edit_details'] = !empty($CouponResult) ? $CouponResult : [];
                // dd($CouponResult);

            }

            $response['data'] = $aResult;
            $response['message'] = 'Request processed successfully';
            $ResposneCode = 200;

        } else {
            $ResposneCode = $aToken['code'];
            $response['message'] = $aToken['message'];
        }

        return response()->json($response, $ResposneCode);
    }


    public function addEditCoupon(Request $request)
    {
        // return $request->file('upload_csv');
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $empty = false;
        $flag = 0;
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

                $DiscountType = !empty($request->discount_type) ? $request->discount_type : 1;
                $DiscountName = !empty($request->discount_name) ? $request->discount_name : '';
                //$EventCommunicationId = !empty($request->event_comm_id) ? $request->event_comm_id : 0;
                $DiscountAmount = !empty($request->discount_amount) ? $request->discount_amount : 0;
                $DiscountPercentage = !empty($request->discount_percentage) ? $request->discount_percentage : 0;
                $CodeType = !empty($request->code_type) ? $request->code_type : 1;
                $NoOfDiscount = !empty($request->no_of_discount) ? $request->no_of_discount : 0;
                $DiscountCode = !empty($request->discount_code) ? $request->discount_code : '';
                $PrefixCode = !empty($request->prefix_code) ? $request->prefix_code : '';
                $DiscountAmtPerType = !empty($request->discount_amt_per_type) ? $request->discount_amt_per_type : '';

                $discount_from_date = !empty($request->discount_from_date) ? $request->discount_from_date : 0;
                $discount_from_time = !empty($request->discount_from_time) ? $request->discount_from_time : 0;
                $discount_to_date = !empty($request->discount_to_date) ? $request->discount_to_date : 0;
                $discount_to_time = !empty($request->discount_to_time) ? $request->discount_to_time : 0;

                $description = !empty($request->description) ? $request->description : '';
                $show_public = !empty($request->show_public) ? $request->show_public : 0;
                $UserEmailAddress = !empty($request->user_email_address) ? $request->user_email_address : '';

                $final_discount_from_datetime = !empty($discount_from_date) && !empty($discount_from_time) ? strtotime($discount_from_date . ' ' . $discount_from_time) : '';
                $final_discount_to_datetime = !empty($discount_to_date) && !empty($discount_to_time) ? strtotime($discount_to_date . ' ' . $discount_to_time) : '';
                //dd($final_discount_to_datetime);

                $HaveListCodes = !empty($request->have_list_codes) ? $request->have_list_codes : 0;
                $ApplyTicket = !empty($request->apply_ticket) ? $request->apply_ticket : 1;
                $TicketSelectedData = !empty($request->ticket_selected_data) ? json_decode($request->ticket_selected_data) : '';

                $UploadedCsv = !empty($request->upload_csv) ? $request->file('upload_csv') : '';

                $EditCouponId = !empty($request->edit_coupon_id) ? $request->edit_coupon_id : '';

                //---------- all ticket apply -----------
                $ticket_ids = '';
                if ($ApplyTicket == 1) {
                    $Sql = 'SELECT id FROM event_tickets WHERE `event_id`=:eventId ';
                    $ticket_aResult = DB::select(
                        $Sql,
                        array(
                            'eventId' => $EventId
                        )
                    );
                    //dd($ticket_aResult);
                    $ticket_id_array = !empty($ticket_aResult) ? array_column($ticket_aResult, 'id') : '';
                    $ticket_ids = !empty($ticket_id_array) ? implode(",", $ticket_id_array) : '';
                } else {
                    $ticket_id_array = [];
                    if (!empty($TicketSelectedData)) {
                        foreach ($TicketSelectedData as $res) {
                            if (isset($res->checked) && $res->checked == true)
                                $ticket_id_array[] = $res->id;
                        }
                    }
                    $ticket_ids = !empty($ticket_id_array) ? implode(",", $ticket_id_array) : '';
                }


                if (empty($EditCouponId)) {     // data insert

                    $SQL = "SELECT discount_name FROM event_coupon WHERE LOWER(discount_name) = :discount_name AND event_id = :event_id";
                    $IsExist = DB::select($SQL, array('discount_name' => strtolower($DiscountName), "event_id" => $EventId));
                    // dd($IsExist);

                    $SQL1 = "SELECT discount_code FROM event_coupon_details WHERE LOWER(discount_code) = :discount_code AND event_id = :event_id";
                    $IsCouponExist = DB::select($SQL1, array('discount_code' => strtolower($DiscountCode), "event_id" => $EventId));
                    //dd($IsCouponExist);

                    if (!empty($IsExist)) {
                        $ResposneCode = 200;
                        $message = "Discount name is already exists, please use another name.";
                        $flag = 1;
                        $ResponseData = $flag;
                    } else if (!empty($IsCouponExist)) {
                        $ResposneCode = 200;
                        $message = "Discount code is already exists, please use another code.";
                        $flag = 2;
                        $ResponseData = $flag;
                    } else {

                        $Bindings = array(
                            "event_id" => $EventId,
                            "discount_type" => $DiscountType,
                            "discount_name" => $DiscountName,
                            "description" => $description,
                            "show_public" => $show_public,
                            "created_by" => $UserId,
                            "created_date" => time()
                        );

                        $insert_SQL = "INSERT INTO event_coupon (event_id,discount_type,discount_name,description,show_public,created_by,created_date) VALUES(:event_id,:discount_type,:discount_name,:description,:show_public,:created_by,:created_date)";
                        DB::insert($insert_SQL, $Bindings);
                        $last_inserted_id = DB::getPdo()->lastInsertId();
                        //dd($last_inserted_id);


                        if ($CodeType == 1) {

                            if (!empty($last_inserted_id)) {

                                $Bindings1 = array(
                                    "event_coupon_id" => $last_inserted_id,
                                    "event_id" => $EventId,
                                    "discount_type" => $DiscountType,
                                    "discount_amt_per_type" => $DiscountAmtPerType,
                                    "discount_amount" => $DiscountAmount,
                                    "discount_percentage" => $DiscountPercentage,
                                    "code_type" => $CodeType,
                                    "no_of_discount" => $NoOfDiscount,
                                    "discount_code" => $DiscountCode,
                                    "prefix_code" => $PrefixCode,
                                    "discount_from_datetime" => $final_discount_from_datetime,
                                    "discount_to_datetime" => $final_discount_to_datetime,
                                    "have_list_codes" => $HaveListCodes,
                                    "apply_ticket" => $ApplyTicket,
                                    "ticket_details" => $ticket_ids,
                                    "user_email_address" => $UserEmailAddress

                                );
                                $insert_SQL1 = "INSERT INTO event_coupon_details (event_coupon_id,event_id,discount_type,discount_amt_per_type,discount_amount,discount_percentage,code_type,no_of_discount,discount_code,prefix_code,discount_from_datetime,discount_to_datetime,have_list_codes,apply_ticket,ticket_details,user_email_address) VALUES(:event_coupon_id,:event_id,:discount_type,:discount_amt_per_type,:discount_amount,:discount_percentage,:code_type,:no_of_discount,:discount_code,:prefix_code,:discount_from_datetime,:discount_to_datetime,:have_list_codes,:apply_ticket,:ticket_details,:user_email_address)";
                                //dd($insert_SQL1);
                                DB::insert($insert_SQL1, $Bindings1);
                            }

                        } else {
                            //$last_inserted_id = 33;
                            if (!empty($UploadedCsv)) {
                                // Read CSV file
                                $csv = Reader::createFromPath($UploadedCsv->getPathname(), 'r');
                                $records = $csv->getRecords();
                                //dd($records);
                                // $header = array_shift($records);
                                $NewCouponArray = [];
                                $NewCouponArray1 = [];
                                $column1 = [];
                                // Iterate over records and convert them to arrays

                                foreach ($records as $record) {
                                    $column1[] = isset($record[0]) ? $record[0] : []; // Assuming the first column
                                    $column2[] = isset($record[1]) ? $record[1] : []; // Assuming the second column
                                }

                                $column1 = array_unique($column1);
                                foreach ($column1 as $key => $res) {
                                    if ($res !== 'DISCOUNT_CODE')
                                        $NewCouponArray[] = array("id" => $key, "name" => $res);
                                }

                                if (!empty($column2)) {
                                    foreach ($column2 as $key => $res1) {
                                        if ($res1 !== 'EMAIL_ADDRESS')
                                            $NewCouponArray1[] = array("id" => $key, "name" => $res1);
                                    }
                                } else {
                                    $NewCouponArray1 = [];
                                }

                                $final_code_array = [];
                                if (!empty($NewCouponArray1))
                                    foreach ($NewCouponArray as $val) {
                                        foreach ($NewCouponArray1 as $val1) {
                                            // dd($val1);
                                            if (isset($val['id']) && isset($val1['id']) && $val['id'] == $val1['id']) {
                                                $final_code_array[] = array("id" => $val['id'], "name" => $val['name'], "emails" => isset($val1['name']) && !empty($val1['name']) ? $val1['name'] : "");
                                            }
                                        }
                                    }
                                // dd($new_array);
                                // if(!empty($NewCouponArray)){
                                //     $indexSpam = array_search('DISCOUNT_CODE', $NewCouponArray);
                                //     unset($NewCouponArray[$indexSpam]);
                                // }
                                //$NewCouponArray = array_unique($NewCouponArray);
                                //dd($NewCouponArray1);

                                // if(!empty($NewCouponArray1)){
                                //     $indexSpam1 = array_search('EMAIL_ADDRESS', $NewCouponArray1);
                                //     unset($NewCouponArray1[$indexSpam1]);
                                // }
                                // $NewCouponArray1 = array_unique($NewCouponArray1);


                                //if($HaveListCodes == 1){

                                if (!empty($final_code_array)) {
                                    foreach ($final_code_array as $res) {

                                        $Bindings1 = array(
                                            "event_coupon_id" => $last_inserted_id,
                                            "event_id" => $EventId,
                                            "discount_type" => $DiscountType,
                                            "discount_amt_per_type" => $DiscountAmtPerType,
                                            "discount_amount" => $DiscountAmount,
                                            "discount_percentage" => $DiscountPercentage,
                                            "code_type" => $CodeType,
                                            "no_of_discount" => $NoOfDiscount,
                                            "discount_code" => $res['name'],
                                            "prefix_code" => $PrefixCode,
                                            "discount_from_datetime" => $final_discount_from_datetime,
                                            "discount_to_datetime" => $final_discount_to_datetime,
                                            "have_list_codes" => $HaveListCodes,
                                            "apply_ticket" => $ApplyTicket,
                                            "ticket_details" => $ticket_ids,
                                            "user_email_address" => isset($res['emails']) && !empty($res['emails']) ? $res['emails'] : ''
                                        );
                                        $insert_SQL1 = "INSERT INTO event_coupon_details (event_coupon_id,event_id,discount_type,discount_amt_per_type,discount_amount,discount_percentage,code_type,no_of_discount,discount_code,prefix_code,discount_from_datetime,discount_to_datetime,have_list_codes,apply_ticket,ticket_details,user_email_address) VALUES(:event_coupon_id,:event_id,:discount_type,:discount_amt_per_type,:discount_amount,:discount_percentage,:code_type,:no_of_discount,:discount_code,:prefix_code,:discount_from_datetime,:discount_to_datetime,:have_list_codes,:apply_ticket,:ticket_details,:user_email_address)";
                                        //dd($insert_SQL1);
                                        DB::insert($insert_SQL1, $Bindings1);

                                    }
                                }
                                //}
                            }
                        }

                        $message = 'Coupon added successfully';
                        $ResposneCode = 200;
                    }

                } else {                       // data update
                    // dd($EditCouponId);

                    $SQL = "SELECT discount_name FROM event_coupon WHERE LOWER(discount_name) = :discount_name AND event_id = :event_id AND id != :edit_id";
                    $IsExist = DB::select($SQL, array('discount_name' => strtolower($DiscountName), "event_id" => $EventId, "edit_id" => $EditCouponId));
                    // dd($IsExist);
                    $SQL1 = "SELECT discount_code FROM event_coupon_details WHERE LOWER(discount_code) = :discount_code AND event_id = :event_id AND event_coupon_id != :edit_id";
                    $IsCouponExist = DB::select($SQL1, array('discount_code' => strtolower($DiscountCode), "event_id" => $EventId, "edit_id" => $EditCouponId));

                    if (!empty($IsExist)) {
                        $ResposneCode = 200;
                        $message = "Discount name is already exists, please use another name.";
                        $flag = 1;
                        $ResponseData = $flag;
                    } else if (!empty($IsCouponExist)) {
                        $ResposneCode = 200;
                        $message = "Discount code is already exists, please use another code.";
                        $flag = 2;
                        $ResponseData = $flag;
                    } else {


                        $Bindings = array(
                            "discount_type" => $DiscountType,
                            "discount_name" => $DiscountName,
                            "description" => $description,
                            "show_public" => $show_public,
                            "event_id" => $EventId,
                            'edit_id' => $EditCouponId
                        );
                        $edit_sql = 'UPDATE event_coupon SET discount_type = :discount_type, discount_name = :discount_name, description = :description, show_public =:show_public  WHERE event_id = :event_id AND id = :edit_id';
                        DB::update($edit_sql, $Bindings);

                        $cond = $arr_cond = '';
                        if ($CodeType == '1') {
                            $cond = ', discount_code = :discount_code';
                        }

                        //coupon details update
                        if ($CodeType == '1') {
                            $Bindings1 = array(
                                "discount_type" => $DiscountType,
                                "discount_amt_per_type" => $DiscountAmtPerType,
                                "discount_amount" => $DiscountAmount,
                                "discount_percentage" => $DiscountPercentage,
                                "code_type" => $CodeType,
                                "no_of_discount" => $NoOfDiscount,
                                "prefix_code" => $PrefixCode,
                                "discount_from_datetime" => $final_discount_from_datetime,
                                "discount_to_datetime" => $final_discount_to_datetime,
                                "apply_ticket" => $ApplyTicket,
                                "ticket_details" => $ticket_ids,
                                "user_email_address" => $UserEmailAddress,
                                "discount_code" => $DiscountCode,
                                "event_id" => $EventId,
                                'edit_id' => $EditCouponId
                            );
                        } else {
                            $Bindings1 = array(
                                "discount_type" => $DiscountType,
                                "discount_amt_per_type" => $DiscountAmtPerType,
                                "discount_amount" => $DiscountAmount,
                                "discount_percentage" => $DiscountPercentage,
                                "code_type" => $CodeType,
                                "no_of_discount" => $NoOfDiscount,
                                "prefix_code" => $PrefixCode,
                                "discount_from_datetime" => $final_discount_from_datetime,
                                "discount_to_datetime" => $final_discount_to_datetime,
                                "apply_ticket" => $ApplyTicket,
                                "ticket_details" => $ticket_ids,
                                "user_email_address" => $UserEmailAddress,
                                "event_id" => $EventId,
                                'edit_id' => $EditCouponId
                            );
                        }

                        $edit_sql1 = 'UPDATE event_coupon_details SET discount_type = :discount_type, discount_amt_per_type = :discount_amt_per_type, discount_amount = :discount_amount, discount_percentage = :discount_percentage, code_type = :code_type, no_of_discount = :no_of_discount, prefix_code = :prefix_code, discount_from_datetime = :discount_from_datetime, discount_to_datetime = :discount_to_datetime, apply_ticket = :apply_ticket, ticket_details = :ticket_details, user_email_address = :user_email_address ' . $cond . ' WHERE event_id = :event_id AND event_coupon_id = :edit_id';
                        DB::update($edit_sql1, $Bindings1);

                        $message = 'Coupon updated successfully';
                        $ResposneCode = 200;
                    }
                }
                ////


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

    public function StatusCoupon(Request $request)
    {
        $response['data'] = [];
        $response['message'] = '';
        $ResposneCode = 400;
        $empty = false;

        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        //dd($aToken);
        if ($aToken['code'] == 200) {
            //return $request;
            $CouponId = !empty($request->coupon_id) ? $request->coupon_id : 0;
            // $CouponStatus = 0;
            //if(isset($request->coupon_status)){
            if ($request->coupon_status == "true") {
                $CouponStatus = 0;
            } else if ($request->coupon_status == "false") {
                $CouponStatus = 1;
            }
            //}


            $ActionFlag = !empty($request->action_flag) ? $request->action_flag : '';
            $msg = '';

            if (isset($ActionFlag) && $ActionFlag == 'faq_changes_status') {
                $status_sSQL = 'UPDATE event_FAQ SET `status` =:coupon_status WHERE `id`=:coupon_id ';
                DB::update(
                    $status_sSQL,
                    array(
                        'coupon_id' => $CouponId,
                        'coupon_status' => $CouponStatus
                    )
                );
                $msg = 'FAQ status changed successfully';

            } else if ($ActionFlag == 'age_criteria_changes_status') {
                $status_sSQL = 'UPDATE age_criteria SET `status` =:coupon_status WHERE `id`=:age_id ';
                DB::update(
                    $status_sSQL,
                    array(
                        'age_id' => $CouponId,
                        'coupon_status' => $CouponStatus
                    )
                );
                $msg = 'Age criteria status changed successfully';

            } else if ($ActionFlag == 'communication_changes_status') {
                $status_sSQL = 'UPDATE event_communication SET `status` =:comm_status WHERE `id`=:comm_id ';
                DB::update(
                    $status_sSQL,
                    array(
                        'comm_status' => $CouponStatus,
                        'comm_id' => $CouponId
                    )
                );
                $msg = 'Communication status changed successfully';

            } else if ($ActionFlag == 'term_changes_status') {
                $status_sSQL = 'UPDATE event_terms_conditions SET `status` =:term_status WHERE `id`=:term_id ';
                DB::update(
                    $status_sSQL,
                    array(
                        'term_status' => $CouponStatus,
                        'term_id' => $CouponId
                    )
                );
                $msg = 'Terms & conditions status changed successfully';

            } else {
                $status_sSQL = 'UPDATE event_coupon SET `coupon_status` =:coupon_status WHERE `id`=:coupon_id ';
                DB::update(
                    $status_sSQL,
                    array(
                        'coupon_id' => $CouponId,
                        'coupon_status' => $CouponStatus
                    )
                );
                $msg = 'Coupon status changed successfully';
            }

            $response['data'] = [];
            $response['message'] = $msg;
            $ResposneCode = 200;

        } else {
            $ResposneCode = $aToken['code'];
            $response['message'] = $aToken['message'];
        }

        return response()->json($response, $ResposneCode);
    }

    // ----------- Age Criteria
    public function addEditAgeCriteria(Request $request)
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

                $DistanceCategory = !empty($request->distance_category) ? $request->distance_category : 0;
                $AgeCategory = !empty($request->age_category) ? $request->age_category : 0;
                $AgeStart = !empty($request->age_start) ? $request->age_start : 0;
                $AgeEnd = !empty($request->age_end) ? $request->age_end : 0;
                $Gender = !empty($request->gender) ? $request->gender : 0;
                $EventCommunicationId = !empty($request->event_comm_id) ? $request->event_comm_id : 0;

                // $SQL = "SELECT id FROM event_communication WHERE event_id =:event_id";
                // $IsExist = DB::select($SQL, array('event_id' => $EventId));

                if (empty($EventCommunicationId)) {

                    $Bindings = array(
                        "event_id" => $EventId,
                        "distance_category" => $DistanceCategory,
                        "age_category" => $AgeCategory,
                        "gender" => $Gender,
                        "age_start" => $AgeStart,
                        "age_end" => $AgeEnd,
                        "created_by" => $UserId
                    );

                    $insert_SQL = "INSERT INTO age_criteria (event_id,distance_category,age_category,gender,age_start,age_end,created_by) VALUES(:event_id,:distance_category,:age_category,:gender,:age_start,:age_end,:created_by)";
                    DB::insert($insert_SQL, $Bindings);

                    $ResposneCode = 200;
                    $message = "Age Criteria added successfully";

                } else {

                    $Bindings = array(
                        "distance_category" => $DistanceCategory,
                        "age_category" => $AgeCategory,
                        "gender" => $Gender,
                        "age_start" => $AgeStart,
                        "age_end" => $AgeEnd,
                        "event_comm_id" => $EventCommunicationId
                    );

                    $sql = 'UPDATE age_criteria SET distance_category= :distance_category, age_category= :age_category, gender= :gender, age_start= :age_start, age_end= :age_end WHERE id = :event_comm_id';
                    // dd($sql);
                    DB::update($sql, $Bindings);

                    $ResposneCode = 200;
                    $message = "Age Criteria updated successfully";
                }

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

    // ----------- Terms & Conditions
    public function addEditTermsConditions(Request $request)
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

                $Title = !empty($request->title) ? $request->title : '';
                $TermsConditions = !empty($request->terms_conditions) ? $request->terms_conditions : '';
                $EventTermId = !empty($request->event_comm_id) ? $request->event_comm_id : 0;

                if (empty($EventTermId)) {

                    $Bindings = array(
                        "event_id" => $EventId,
                        "user_id" => $UserId,
                        "title" => $Title,
                        "terms_conditions" => $TermsConditions
                    );

                    $insert_SQL = "INSERT INTO event_terms_conditions (event_id,user_id,title,terms_conditions) VALUES(:event_id,:user_id,:title,:terms_conditions)";
                    DB::insert($insert_SQL, $Bindings);

                    $ResposneCode = 200;
                    $message = "Terms & Conditions added successfully";

                } else {

                    $Bindings = array(
                        "title" => $Title,
                        "terms_conditions" => $TermsConditions,
                        "event_comm_id" => $EventTermId
                    );

                    $sql = 'UPDATE event_terms_conditions SET title= :title, terms_conditions= :terms_conditions WHERE id = :event_comm_id';
                    // dd($sql);
                    DB::update($sql, $Bindings);

                    $ResposneCode = 200;
                    $message = "Terms & Conditions updated successfully";
                }

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

    // Delete description for Banner Image and Photos
    public function deleteEventImages(Request $request)
    {
        $response['data'] = [];
        $response['message'] = '';
        $ResposneCode = 400;
        $empty = false;

        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        //dd($aToken);
        if ($aToken['code'] == 200) {

            $EventId = !empty($request->event_id) ? $request->event_id : 0;
            $EventPhotoId = !empty($request->event_img_id) ? $request->event_img_id : 0;
            $CommonFlag = !empty($request->common_flag) ? $request->common_flag : '';

            $CollectGst = !empty($request->collect_gst) ? $request->collect_gst : 0;
            $PriceTaxesStatus = !empty($request->prices_taxes_status) ? $request->prices_taxes_status : 1;

            $msg = '';

            if (isset($request->common_flag) && $CommonFlag == 'banner_delete') {

                $Bindings = array(
                    "event_id" => $EventId
                );

                $sql = 'UPDATE events SET banner_image = "" WHERE id = :event_id';
                DB::update($sql, $Bindings);

                $msg = 'Banner image deleted successfully';
            } else if (isset($request->common_flag) && $CommonFlag == 'photo_delete') {

                $del_sSQL = 'DELETE FROM event_images WHERE `event_id`=:eventId AND `id`=:event_img_id ';
                DB::delete(
                    $del_sSQL,
                    array(
                        'eventId' => $EventId,
                        'event_img_id' => $EventPhotoId
                    )
                );
                $msg = 'Event Photo deleted successfully';

            }

            //------------ event collect gst and price taxes saving

            if (isset($request->common_flag) && $CommonFlag == 'ticket_gst_taxes_price') {

                $Bindings = array(
                    "collect_gst" => $CollectGst,
                    "prices_taxes_status" => $PriceTaxesStatus,
                    "event_id" => $EventId
                );

                $sql = 'UPDATE events SET collect_gst =:collect_gst, prices_taxes_status =:prices_taxes_status  WHERE id = :event_id';
                DB::update($sql, $Bindings);

                $msg = 'Record updated successfully';
            }

            $response['data'] = [];
            $response['message'] = $msg;
            $ResposneCode = 200;

        } else {
            $ResposneCode = $aToken['code'];
            $response['message'] = $aToken['message'];
        }

        return response()->json($response, $ResposneCode);
    }


}

