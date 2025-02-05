<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Libraries\Authenticate;
use App\Models\Master;
use App\Models\Event;
use App\Libraries\Emails;

class OrganizerController extends Controller
{
    public function getOrganizerDetails(Request $request)
    {
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);

        if ($aToken['code'] == 200) {
            $aPost = $request->all();
            $Auth = new Authenticate();
            $Auth->apiLog($request);
            $UserId = $aToken['data']->ID;

            $sSQL = 'SELECT * FROM organizer WHERE user_id=:user_id';
            $organizerData = DB::select($sSQL, array('user_id' => $UserId));

            foreach ($organizerData as $value) {
                $value->banner_image = (!empty($value->banner_image)) ? url('/') . "/organiser/banner_image/" . $value->banner_image . '' : '';
                $value->logo_image = (!empty($value->logo_image)) ? url('/') . "/organiser/logo_image/" . $value->logo_image . '' : '';
            }
            $ResponseData['organizerData'] = $organizerData;

            // $sSQL = 'SELECT * FROM organizer_users';
            // if ($request->has('id')) {
            //     $organizerId = $aPost['id'];
            //     $sSQL .= ' WHERE organizer_id = ' . $organizerId;

            // }
            // $ResponseData['organizer_users'] = DB::select($sSQL);

            // $sSQL = 'SELECT * FROM organizer_events';
            // if ($request->has('id')) {
            //     $organizerId = $aPost['id'];
            //     $sSQL .= ' WHERE organizer_id = ' . $organizerId;

            // }
            // $ResponseData['organizer_events'] = DB::select($sSQL);

            // $sSQL = 'SELECT * FROM organizer_roles';
            // if ($request->has('id')) {
            //     $organizerId = $aPost['id'];
            //     $sSQL .= ' WHERE organizer_id = ' . $organizerId;

            // }
            // $ResponseData['organizer_roles'] = DB::select($sSQL);

            // $sSQL = 'SELECT * FROM organizers_follow';
            // if ($request->has('id')) {
            //     $organizerId = $aPost['id'];
            //     $sSQL .= ' WHERE organizer_id = ' . $organizerId;
            // }
            // $ResponseData['organizers_follow'] = DB::select($sSQL);


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

    public function addEditOrganizer(Request $request)
    {
        $empty = false;
        $ResponseData = [];
        $message = "";
        $ResposneCode = 400;
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);

        if ($aToken['code'] == 200) {
            $aPost = $request->all();
            $Auth = new Authenticate();
            $Auth->apiLog($request);

            $UserId = $aToken['data']->ID;

            if (empty($aPost['name'])) {
                $empty = true;
                $field = 'Name';
            }
            if (empty($aPost['email'])) {
                $empty = true;
                $field = 'Email Id';
            }
            if (empty($aPost['about'])) {
                $empty = true;
                $field = 'About';
            }

            if (!$empty) {
                $Auth = new Authenticate();
                $Auth->apiLog($request);

                $organiserId = $aPost['oragniser_id'];
                $name = $aPost['name'];
                $email = $aPost['email'];
                $mobile = $aPost['mobile'];
                $about = $aPost['about'];
                $gst = $aPost['gst'];
                $gstNo = isset($aPost['gst_number']) ? $aPost['gst_number'] : "";
                $gstPercent = isset($aPost['gst_percentage']) ? $aPost['gst_percentage'] : "";
                $contactPerson = isset($aPost['contact_person']) ? $aPost['contact_person'] : "";
                $contactNumber = isset($aPost['contact_no']) ? $aPost['contact_no'] : "";

                if (empty($organiserId)) {
                    #ADD ORGANISER
                    #CHECK SAME ORGANISER NAME EXIST OR NOT
                    $SQL = "SELECT name FROM organizer WHERE name=:name";
                    $IsExist = DB::select($SQL, array('name' => strtolower($aPost['name'])));

                    if (count($IsExist) > 0) {
                        $ResposneCode = 400;
                        $message = "Organiser with same name is already exists, please use another name.";
                    } else {
                        $banner_image = isset($aPost['banner_image']) ? $aPost['banner_image'] : '';
                        $logo_image = isset($aPost['logo_image']) ? $aPost['logo_image'] : '';
                        #INSERT CODE OF ORGANISER
                        DB::table('organizer')->insert([
                            'user_id' => $UserId,
                            'name' => $name,
                            'email' => $email,
                            'mobile'=> $mobile,
                            'about' => $about,
                            'gst' => $gst,
                            'gst_number' => $gstNo,
                            'gst_percentage' => $gstPercent,
                            'contact_person' => $contactPerson,
                            'contact_no' => $contactNumber,
                            'created_at'=>strtotime("now")
                        ]);
                        $organiserId = DB::getPdo()->lastInsertId();

                        #INSERT BANNER IMAGE INTO FOLDER
                        if (!empty($request->file('banner_image'))) {
                            $Path = public_path('organiser/banner_image/');
                            $new_banner_image = $request->file('banner_image');
                            $originalName = $new_banner_image->getClientOriginalName() . "_" . $organiserId;
                            $banner_image = $originalName;
                            $new_banner_image->move($Path, $banner_image);

                            $sSQLImg = 'UPDATE organizer SET banner_image = :banner_image WHERE id=:id';
                            DB::update($sSQLImg, ['banner_image' => $banner_image, 'id' => $organiserId]);
                        }

                        #INSERT LOGO IMAGE INTO FOLDER
                        if (!empty($request->file('logo_image'))) {
                            $Path = public_path('organiser/logo_image/');
                            $new_logo_image = $request->file('logo_image');
                            $originalName = $new_logo_image->getClientOriginalName() . "_" . $organiserId;
                            $logo_image = $originalName;
                            $new_logo_image->move($Path, $logo_image);

                            $sSQLImg = 'UPDATE organizer SET logo_image = :logo_image WHERE id=:id';
                            DB::update($sSQLImg, ['logo_image' => $logo_image, 'id' => $organiserId]);
                        }

                        #INSERT COMPANY PANCARD INTO FOLDER
                        if (!empty($request->file('company_pancard'))) {
                            $Path = public_path('organiser/company_pancard/');
                            $new_company_pancard = $request->file('company_pancard');
                            $originalName = $new_company_pancard->getClientOriginalName() . "_" . $organiserId;
                            $company_pancard = $originalName;
                            $new_company_pancard->move($Path, $company_pancard);

                            $sSQLImg = 'UPDATE organizer SET company_pan = :company_pancard WHERE id=:id';
                            DB::update($sSQLImg, ['company_pancard' => $company_pancard, 'id' => $organiserId]);
                        }

                        #INSERT GST CERTIFICATE INTO FOLDER
                        if (!empty($request->file('gst_certificate'))) {
                            $Path = public_path('organiser/gst_certificate/');
                            $new_gst_certificate = $request->file('gst_certificate');
                            $originalName = $new_gst_certificate->getClientOriginalName() . "_" . $organiserId;
                            $gst_certificate = $originalName;
                            $new_gst_certificate->move($Path, $gst_certificate);

                            $sSQLImg = 'UPDATE organizer SET gst_certificate = :gst_certificate WHERE id=:id';
                            DB::update($sSQLImg, ['gst_certificate' => $gst_certificate, 'id' => $organiserId]);
                        }

                        $ResposneCode = 200;
                        $message = 'Organizer inserted successfully';
                    }

                } else {
                    #UPDATE ORGANISER
                    #CHECK SAME ORGANISER NAME EXIST OR NOT
                    $SQL = "SELECT name FROM organizer WHERE name=:name AND id !=:id";
                    $IsExist = DB::select($SQL, array('name' => strtolower($aPost['name']), 'id' => $organiserId));

                    if (count($IsExist) > 0) {
                        $ResposneCode = 400;
                        $message = "Organiser with same name is already exists, please use another name.";
                    } else {
                        #UPDATE BANNER IMAGE INTO FOLDER
                        if (!empty($request->file('banner_image'))) {
                            $Path = public_path('organiser/banner_image/');
                            $new_banner_image = $request->file('banner_image');
                            $originalName = $new_banner_image->getClientOriginalName() . "_" . $organiserId;
                            $banner_image = $originalName;
                            $new_banner_image->move($Path, $banner_image);

                            $sSQLImg = 'UPDATE organizer SET banner_image = :banner_image WHERE id=:id';
                            DB::update($sSQLImg, ['banner_image' => $banner_image, 'id' => $organiserId]);
                        }

                        #UPDATE LOGO IMAGE INTO FOLDER
                        if (!empty($request->file('logo_image'))) {
                            $Path = public_path('organiser/logo_image/');
                            $new_logo_image = $request->file('logo_image');
                            $originalName = $new_logo_image->getClientOriginalName() . "_" . $organiserId;
                            $logo_image = $originalName;
                            $new_logo_image->move($Path, $logo_image);

                            $sSQLImg = 'UPDATE organizer SET logo_image = :logo_image WHERE id=:id';
                            DB::update($sSQLImg, ['logo_image' => $logo_image, 'id' => $organiserId]);
                        }

                        #UPDATE COMPANY PANCARD INTO FOLDER
                        if (!empty($request->file('company_pancard'))) {
                            $Path = public_path('organiser/company_pancard/');
                            $new_company_pancard = $request->file('company_pancard');
                            $originalName = $new_company_pancard->getClientOriginalName() . "_" . $organiserId;
                            $company_pancard = $originalName;
                            $new_company_pancard->move($Path, $company_pancard);

                            $sSQLImg = 'UPDATE organizer SET company_pan = :company_pancard WHERE id=:id';
                            DB::update($sSQLImg, ['company_pancard' => $company_pancard, 'id' => $organiserId]);
                        }

                        #UPDATE GST CERTIFICATE INTO FOLDER
                        if (!empty($request->file('gst_certificate'))) {
                            $Path = public_path('organiser/gst_certificate/');
                            $new_gst_certificate = $request->file('gst_certificate');
                            $originalName = $new_gst_certificate->getClientOriginalName() . "_" . $organiserId;
                            $gst_certificate = $originalName;
                            $new_gst_certificate->move($Path, $gst_certificate);

                            $sSQLImg = 'UPDATE organizer SET gst_certificate = :gst_certificate WHERE id=:id';
                            DB::update($sSQLImg, ['gst_certificate' => $gst_certificate, 'id' => $organiserId]);
                        }

                        #UPDATE CODE OF ORGANISER
                        DB::table('organizer')
                            ->where('id', $organiserId)
                            ->update([
                                'user_id' => $UserId,
                                'name' => $name,
                                'email' => $email,
                                'mobile' => $mobile,
                                'about' => $about,
                                'gst' => $gst,
                                'gst_number' => $gstNo,
                                'gst_percentage' => $gstPercent,
                                'contact_person' => $contactPerson,
                                'contact_no' => $contactNumber
                            ]);
                        $ResposneCode = 200;
                        $message = 'Organizer updated successfully';
                    }
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
            'data' => $ResponseData,
            'message' => $message
        ];

        return response()->json($response, $ResposneCode);
    }

    // public function getRoles(Request $request)
    // {
    //     $ResponseData = [];
    //     $message = "";
    //     $ResposneCode = 400;
    //     $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);

    //     if ($aToken['code'] == 200) {
    //         $aPost = $request->all();
    //         $Auth = new Authenticate();
    //         $Auth->apiLog($request);

    //         $Roles = DB::table('master_roles')
    //             ->select('id', 'role_name', 'access')
    //             ->where('active', '=', 1)
    //             ->get();
    //         $ResponseData['roles'] = $Roles;

    //         $ResposneCode = 200;
    //         $message = 'Roles getting successfully';

    //     } else {
    //         $ResposneCode = $aToken['code'];
    //         $message = $aToken['message'];
    //     }

    //     $response = [
    //         'data' => $ResponseData,
    //         'message' => $message
    //     ];

    //     return response()->json($response, $ResposneCode);
    // }

    function getOrganizingTeam(Request $request)
    {
        $ResponseData = [];
        $message = "";
        $ResposneCode = 400;
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);

        if ($aToken['code'] == 200) {
            $aPost = $request->all();
            $Auth = new Authenticate();
            $Auth->apiLog($request);

            $UserId = $aToken['data']->ID;

            #GETTING ORGANIZERS OF USER
            $SQL = "SELECT * FROM users WHERE parent_id IN (" . $UserId . ")";
            $Organizers = DB::select($SQL);
            // dd($Users);
            $ResponseData['organizers'] = $Organizers;


            #GETTING EVENTS OF USER
            $sql = "SELECT e.id,e.name,e.city,e.start_time FROM events AS e LEFT JOIN event_users AS u ON u.event_id=e.id WHERE u.user_id=:user_id AND  e.active=1 AND e.deleted=0";
            $Events = DB::select($sql, array('user_id' => $UserId));
            // dd($Events);
            // $ResponseData['events'] = app('App\Http\Controllers\Api\EventController')->ManipulateEvents($Events,$UserId);;
            $master = new Master();
            foreach ($Events as $event) {
                $event->checked = 0;
                $event->start_date = (!empty($event->start_time)) ? date("d-m-Y", $event->start_time) : 0;
                $event->city_name = !empty($event->city) ? $master->getCityName($event->city) : "";
            }
            $ResponseData['events'] = $Events;
            #ROLES
            $Roles = DB::table('master_roles')
                ->select('id', 'role_name', 'access')
                ->where('active', '=', 1)
                ->get();
            foreach ($Roles as $role) {
                $role->checked = 0;
            }
            $ResponseData['roles'] = $Roles;

            $ResposneCode = 200;
            $message = 'Data getting successfully';

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

    public function allOrganizerData(Request $request)
    {
        $ResponseData = [];
        $message = "";
        $ResposneCode = 400;
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        //dd($aToken);
        if ($aToken['code'] == 200) {

            $UserId = !empty($request->user_id) ? $request->user_id : 0;
            $OrganiserName = isset($request->organiser_name) ? str_replace("_", " ", $request->organiser_name) : '';

            $LoggedUserId = $aToken['data']->ID;
            // dd($LoggedUserId);
            $NowTime = strtotime('now');
            $e = new Event();

            $sSQL2 = 'SELECT * FROM organizer AS o WHERE o.user_id=:user_id AND name=:organiser_name';
            $Organizer = DB::select($sSQL2, array('user_id' => $UserId, 'organiser_name' => $OrganiserName));
            foreach ($Organizer as $value) {
                // dd($value->id);
                $value->is_follow = !empty($LoggedUserId) ? $e->isOrgFollowed($value->id, $LoggedUserId) : 0;
                $value->join_on = !empty($value->created_at) ? date("F d, Y", $value->created_at) : 0;
                $value->banner_image = (!empty($value->banner_image)) ? url('/') . "/organiser/banner_image/" . $value->banner_image . '' : '';
                $value->logo_image = (!empty($value->logo_image)) ? url('/') . "/organiser/logo_image/" . $value->logo_image . '' : '';
            }
            $ResponseData['Organizer'] = $Organizer;

            #UPCOMING EVETNS
            $UpcomingSql = "SELECT * from events AS u WHERE u.active=1 AND u.deleted=0 AND event_info_status = 1 AND u.created_by=:user_id AND u.start_time >=:start_time";
            $UpcomingEvents = DB::select($UpcomingSql, array('user_id' => $UserId, 'start_time' => $NowTime));
            $ResponseData['UpcomingEvents'] = app('App\Http\Controllers\Api\EventController')->ManipulateEvents($UpcomingEvents, $UserId);

            #PAST EVENTS
            $PastSql = "SELECT * from events AS u WHERE u.active=1 AND u.deleted=0  AND event_info_status = 1 AND u.created_by=:user_id AND u.start_time < :start_time";
            $PastEvents = DB::select($PastSql, array('user_id' => $UserId, 'start_time' => $NowTime));
            $ResponseData['PastEvents'] = app('App\Http\Controllers\Api\EventController')->ManipulateEvents($PastEvents, $UserId);

            $message = 'Request processed successfully';
            $ResposneCode = 200;

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

    function sendOrgMail(Request $request)
    {
        $ResponseData = [];
        $message = "";
        $ResposneCode = 400;
        $empty = false;
        $field = '';
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        //dd($aToken);
        $aPost = $request->all();
        if (empty($aPost['fullname'])) {
            $empty = true;
            $field = 'Fullname';
        }
        if (empty($aPost['email'])) {
            $empty = true;
            $field = 'Email';
        }
        if (empty($aPost['contact_no'])) {
            $empty = true;
            $field = 'Contact No';
        }
        if (empty($aPost['message'])) {
            $empty = true;
            $field = 'Message';
        }
        if (!$empty) {
            if ($aToken['code'] == 200) {
                $UserId = $aToken['data']->ID;

                $Email = new Emails();
                $Email->send_org_notification($aPost['fullname'], $aPost['email'], $aPost['contact_no'], $aPost['message']);
                $message = 'Mail sent successfully';
                $ResposneCode = 200;

            } else {
                $ResposneCode = $aToken['code'];
                $message = $aToken['message'];
            }
        } else {
            $ResposneCode = 400;
            $message = $field . ' is empty';
        }
        $response = [
            'status' => $ResposneCode,
            'data' => $ResponseData,
            'message' => $message
        ];

        return response()->json($response, $ResposneCode);
    }


}
