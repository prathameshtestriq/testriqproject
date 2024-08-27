<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Libraries\Authenticate;
use App\Models\UserRight;
use App\Models\Master;
use App\Libraries\Emails;

class UserController extends Controller
{
    public function getProfile(Request $request)
    {
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        // dd($aToken['data']->ID);

        if ($aToken['code'] == 200) {
            $Auth = new Authenticate();
            $Auth->apiLog($request);

            $UserId = $aToken['data']->ID;

            $sSQL = 'SELECT users.*, user_details.* FROM users LEFT JOIN user_details ON users.id = user_details.user_id WHERE users.id=:Id';
            $userData = DB::select(
                $sSQL,
                array(
                    'Id' => $UserId
                )
            );


            $UserIds = [33, 36,40,51,1,2,3];
            $master = new Master();
            foreach ($userData as $value) {
                $value->profile_pic = (!empty($value->profile_pic)) ? url('/') . '/uploads/profile_images/' . $value->profile_pic . '' : url('/') . '/uploads/profile_images/user-icon.png';
                $value->cover_picture = (!empty($value->cover_picture)) ? url('') . '/uploads/cover_photo/' . $value->cover_picture . '' : '';

                $value->id_proof_doc_upload = (!empty($value->id_proof_doc_upload)) ? url('') . '/uploads/user_documents/' . $value->id_proof_doc_upload . '' : '';
                $value->address_proof_doc_upload = (!empty($value->address_proof_doc_upload)) ? url('') . '/uploads/user_documents/' . $value->address_proof_doc_upload . '' : '';

                $value->city_name = !empty($value->city) ? $master->getCityName($value->city) : "";
                $value->state_name = !empty($value->state) ? $master->getStateName($value->state) : "";
                $value->country_name = !empty($value->country) ? $master->getCountryName($value->country) : "";
                $value->country_code = !empty($value->country) ? $master->getCountryCode($value->country) : "";

                $value->ca_city_name = !empty($value->ca_city) ? $master->getCityName($value->ca_city) : "";
                $value->ca_state_name = !empty($value->ca_state) ? $master->getStateName($value->ca_state) : "";
                $value->ca_country_name = !empty($value->ca_country) ? $master->getCountryName($value->ca_country) : "";
                // $value->ca_country_code = !empty ($value->ca_country) ? $master->getCountryCode($value->ca_country) : "";
                $value->updated_at = (!empty($value->updated_at)) ? date("Y-m-d h:i A", $value->updated_at) : 0;

                if (in_array($UserId, $UserIds, true)) {
                    $value->show_create_event_tab = true;
                } else {
                    $value->show_create_event_tab = false;
                }

            }
            if (!empty($userData)) {
                $ResponseData['userData'] = $userData;
            }

            $sSQL = 'SELECT * FROM master_timezones Where active = 1';
            $ResponseData['timezones'] = DB::select($sSQL, array());

            $ResponseData['ProfileCompletionPercentage'] = $this->profileCompletionPercentage($aToken['data']->ID);

            $ResposneCode = 200;
            $message = 'Request processed successfully';

            $response = [
                'data' => $ResponseData,
                'message' => $message
            ];

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

    function profileCompletionPercentage($UserId)
    {
        $TotalPercentage = 0.00;
        $SQL = 'SELECT users.*, user_details.* FROM users LEFT JOIN user_details ON users.id = user_details.user_id WHERE users.id=:user_id';
        $UserDetails = DB::select($SQL, ['user_id' => $UserId]);
        $oUserDetails = $UserDetails[0];

        #PERSONAL DETAILS
        $personal_details = [
            'firstname',
            'lastname',
            'email',
            'mobile',
            'dob',
            'gender',
            'about_you',
            'profile_pic'
        ];
        $total_personal_details = count($personal_details);
        $completed_personal_details = 0;

        foreach ($personal_details as $field) {
            // dd($field);
            if ((!empty($field))) {
                if (!empty($oUserDetails->$field))
                    $completed_personal_details++;
            }
        }
        // dd($completed_personal_details,($completed_personal_details / $total_personal_details) * 20);
        $personal_details_percentage = ($completed_personal_details / $total_personal_details) * 25;
        $TotalPercentage += $personal_details_percentage;


        #GENERAL DETAILS
        $general_details = [
            'organization',
            'designation',
            'emergency_contact_person',
            'emergency_contact_no1',
            'id_proof_type',
            'id_proof_no',
            'id_proof_doc_upload'
        ];
        $total_general_details = count($general_details);
        $completed_general_details = 0;

        foreach ($general_details as $field) {
            // dd($field);
            if ((!empty($field))) {
                if (!empty($oUserDetails->$field))
                    $completed_general_details++;
            }
        }
        // dd($completed_general_details,($completed_general_details / $total_general_details) * 20);
        $general_details_percentage = ($completed_general_details / $total_general_details) * 25;
        $TotalPercentage += $general_details_percentage;

        #ADDRESS DETAILS
        $address = [
            'address1',
            'address2',
            'country',
            'state',
            'city',
            'pincode',
            'ca_address1',
            'ca_address2',
            'ca_country',
            'ca_state',
            'ca_city',
            'ca_pincode',
            'nationality'
            // 'address_proof_type',
            // 'address_proof_no',
            // 'address_proof_doc_upload'
        ];
        $total_address = count($address);
        $completed_address = 0;

        foreach ($address as $field) {
            // dd($field);
            if ((!empty($field))) {
                if (!empty($oUserDetails->$field))
                    $completed_address++;
            }
        }
        // dd($completed_address,($completed_address / $total_address) * 20);
        $address_percentage = ($completed_address / $total_address) * 25;
        $TotalPercentage += $address_percentage;


        #SOCIAL MEDIA ACCOUNTS
        $social_media_accounts = [
            'facebook_link',
            'twitter_profile_link',
            'linkedin_profile_link'
        ];
        $total_social_media_accounts = count($social_media_accounts);
        $completed_social_media_accounts = 0;

        foreach ($social_media_accounts as $field) {
            // dd($field);
            if ((!empty($field))) {
                if (!empty($oUserDetails->$field))
                    $completed_social_media_accounts++;
            }
        }
        // dd($completed_social_media_accounts,($completed_social_media_accounts / $total_social_media_accounts) * 20);
        $social_media_accounts_percentage = ($completed_social_media_accounts / $total_social_media_accounts) * 25;
        $TotalPercentage += $social_media_accounts_percentage;

        #COMMUNICATION SETTINGS
        // $communication_settings = [
        //     'email_notification_frequency',
        //     'support_email_id',
        //     'support_mobile'
        // ];
        // $total_communication_settings = count($communication_settings);
        // $completed_communication_settings = 0;

        // foreach ($communication_settings as $field) {
        //     // dd($field);
        //     if ((!empty ($field))) {
        //         if (!empty ($oUserDetails->$field))
        //             $completed_communication_settings++;
        //     }
        // }
        // // dd($completed_communication_settings,($completed_communication_settings / $total_communication_settings) * 16);
        // $communication_settings_percentage = ($completed_communication_settings / $total_communication_settings) * 16;
        // $TotalPercentage += $communication_settings_percentage;

        #HEALTH DETAILS
        $health_details = [
            'blood_group',
            'weight',
            'height',
            'medical_conditions',
            'diabetes',
            'chestpain',
            'angina',
            'abnormalheartrhythm',
            'pacemaker',
            'dehydrationseverity',
            'musclecramps',
            'highbloodpressure',
            'lowbloodsugar',
            'epilepsy',
            'bleedingdisorders',
            'asthma',
            'anemia',
            'hospitalized',
            'hospitalization_details',
            'infections',
            'pregnant',
            'stage_pregnancy',
            'covidstatus',
            'undermedication',
            'currentmedications',
            'meditaion_details',
            'allergies',
            'drugallergy',
            'drug_allergy_details',
            'familydoctorname',
            'familydoctorcontactno'
        ];
        $total_health_details = count($health_details);
        $completed_health_details = 0;

        foreach ($health_details as $field) {
            // dd($field);
            if ((!empty($field))) {
                if (!empty($oUserDetails->$field))
                    $completed_health_details++;
            }
        }
        // dd($completed_health_details,($completed_health_details / $total_health_details) * 16);
        $health_details_percentage = ($completed_health_details / $total_health_details) * 25;
        $TotalPercentage += $health_details_percentage;

        $SQL = 'UPDATE users SET profile_completion_percentage =:profile_completion_percentage WHERE id=:id';
        $Bindings = array('profile_completion_percentage' => $TotalPercentage,'id' => $UserId);
        // dd( $Bindings);
        DB::update($SQL, $Bindings);

        return number_format($TotalPercentage, 2);
    }

    function PersonalDetails(Request $request)
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

            $sSQL = 'SELECT * FROM users Where id =:Id';
            $userData = DB::select($sSQL, array('Id' => $aToken['data']->ID));

            if (empty($aPost['firstname'])) {
                $empty = true;
                $field = 'Firstname';
            }
            if (empty($aPost['lastname'])) {
                $empty = true;
                $field = 'Lastname';
            }
            if (empty($aPost['mobile'])) {
                $empty = true;
                $field = 'Mobile No';
            }
            if (empty($aPost['email'])) {
                $empty = true;
                $field = 'Email Id';
            }
            if (empty($aPost['dob'])) {
                $empty = true;
                $field = 'Birth Date';
            }
            if (empty($aPost['gender'])) {
                $empty = true;
                $field = 'Gender';
            }


            if (!$empty) {
                $UserId = $aToken['data']->ID;

                // if (preg_match("/^(?=.*?[a-z])(?=.*?[0-9]).{8,20}$/", $aPost['password'])) {
                if (preg_match("/^[a-zA-Z-' ]*$/", $aPost['firstname'])) {
                    if (preg_match("/^[a-zA-Z-' ]*$/", $aPost['lastname'])) {
                        if (filter_var($aPost['email'], FILTER_VALIDATE_EMAIL)) {
                            if (preg_match('/^[0-9]{10}+$/', $aPost['mobile'])) {

                                $Exist = array();
                                $mobile = $aPost['mobile'];
                                $email = $aPost['email'];
                                $dob = date('Y-m-d', strtotime($aPost['dob']));

                                #CHECK IF SAME EMAIL OR MOBILE USER EXIST OR NOT
                                $SQL2 = 'SELECT id FROM users WHERE (mobile=:mobile OR
                                        email=:email) AND NOT id =:id';
                                $Exist = DB::select($SQL2, array('mobile' => $mobile, 'email' => $email, 'id' => $UserId));

                                if (sizeof($Exist) == 0) {
                                    $about_you = isset($request->about_you) ? $request->about_you : "";

                                    $SQL = 'UPDATE users SET
                                            firstname= :firstname,
                                            lastname= :lastname,
                                            email= :email,
                                            mobile=:mobile,
                                            dob=:dob,
                                            gender= :gender,
                                            about_you=:about_you
                                            WHERE id=:id';
                                    $Bindings = array(
                                        'firstname' => $aPost['firstname'],
                                        'lastname' => $aPost['lastname'],
                                        'email' => $aPost['email'],
                                        'mobile' => $aPost['mobile'],
                                        'dob' => $dob,
                                        'gender' => $aPost['gender'],
                                        'about_you' => $about_you,
                                        'id' => $UserId
                                    );
                                    // dd( $Bindings);
                                    DB::update($SQL, $Bindings);

                                    $message = 'Personal Details updated successfully';
                                    $ResposneCode = 200;
                                } else {
                                    #ELSE
                                    $ResposneCode = 400;
                                    $message = 'User is exist with same credentials';
                                }

                            } else {
                                $ResposneCode = 400;
                                $message = 'Invalid mobile number format';
                            }
                        } else {
                            $ResposneCode = 400;
                            $message = 'Invalid email format';
                        }
                    } else {
                        $ResposneCode = 400;
                        $message = 'Invalid lastname format';
                    }
                } else {
                    $ResposneCode = 400;
                    $message = 'Invalid firstname format';
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

    function AddressDetails(Request $request)
    {
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $empty = false;
        $field = '';
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        // dd($aToken['data']->ID);
        if ($aToken['code'] == 200) {
            $aPost = $request->all();
            $Auth = new Authenticate();
            $Auth->apiLog($request);

            $sSQL = 'SELECT * FROM users Where id =:Id';
            $userData = DB::select($sSQL, array('Id' => $aToken['data']->ID));
            $address_proof_doc_upload = $userData[0]->address_proof_doc_upload;

            if (!$empty) {
                $UserId = $aToken['data']->ID;
                $allowedExts = array('jpeg', 'jpg', "png", "gif", "bmp", "pdf");
                $is_valid = false;
                $filename = '';
                #PDF VALIDATION
                if (!empty($_FILES["address_proof_doc_upload"]["name"])) {
                    $address_proof_doc_upload_temp = explode(".", $_FILES["address_proof_doc_upload"]["name"]);
                    $address_proof_type = strtolower(end($address_proof_doc_upload_temp));
                    if (!in_array($address_proof_type, $allowedExts)) {
                        $filename = 'Address proof document';
                        $is_valid = true;
                    }
                }
                if (!$is_valid) {
                    #PDF UPLOAD
                    if (!empty($request->file('address_proof_doc_upload'))) {
                        $Path = public_path('uploads/user_documents/');
                        $logo_image = $request->file('address_proof_doc_upload');

                        $ImageExtention = $logo_image->getClientOriginalExtension(); #get proper by code;
                        $address_proof_doc_upload = strtotime('now') . '.' . $ImageExtention;
                        $logo_image->move($Path, $address_proof_doc_upload);
                    }

                    $address1 = (!empty($request->address1)) ? $request->address1 : '';
                    $address2 = (!empty($request->address2)) ? $request->address2 : '';
                    $city = (!empty($request->city)) ? $request->city : '';
                    $state = (!empty($request->state)) ? $request->state : '';
                    $country = (!empty($request->country)) ? $request->country : '';
                    $pincode = (!empty($request->pincode)) ? $request->pincode : '';
                    $sameAsPermanent = (!empty($request->sameAsPermanent)) ? $request->sameAsPermanent : 0;

                    $SQL = 'UPDATE users SET
                    address1= :address1,
                    address2= :address2,
                    city= :city,
                    state= :state,
                    country= :country,
                    pincode=:pincode,
                    sameAsPermanent=:sameAsPermanent,
                    ca_address1= :ca_address1,
                    ca_address2= :ca_address2,
                    ca_city= :ca_city,
                    ca_state= :ca_state,
                    ca_pincode= :ca_pincode,
                    ca_country= :ca_country,

                    nationality= :nationality,
                    address_proof_type= :address_proof_type,
                    address_proof_no= :address_proof_no,
                    address_proof_doc_upload= :address_proof_doc_upload
                    WHERE id=:id';
                    $Bindings = array(

                        'address1' => $address1,
                        'address2' => $address2,
                        'city' => $city,
                        'state' => $state,
                        'country' => $country,
                        'pincode' => $pincode,
                        'sameAsPermanent' => $sameAsPermanent,
                        'ca_address1' => !empty($request->ca_address1) ? $request->ca_address1 : "",
                        'ca_address2' => $request->ca_address2,
                        'ca_city' => $request->ca_city,
                        'ca_state' => $request->ca_state,
                        'ca_pincode' => $request->ca_pincode,
                        'ca_country' => $request->ca_country,

                        'nationality' => $request->nationality,
                        'address_proof_type' => $request->address_proof_type,
                        'address_proof_no' => $request->address_proof_no,
                        'address_proof_doc_upload' => $address_proof_doc_upload,

                        'id' => $UserId
                    );
                    DB::update($SQL, $Bindings);

                    $message = 'Address updated successfully';
                    $ResposneCode = 200;
                } else {
                    $ResposneCode = 400;
                    $message = $filename . ' file is in invalid format';
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

    function getAddress(Request $request)
    {
        $ResponseData = $FullAddress = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $empty = false;
        $field = '';
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        // dd($aToken['data']->ID);
        if ($aToken['code'] == 200) {
            $aPost = $request->all();
            if (empty($aPost['country_id'])) {
                $empty = true;
                $field = 'Country';
            }
            if (empty($aPost['pincode'])) {
                $empty = true;
                $field = 'Pincode';
            }
            if (!$empty) {

                $Auth = new Authenticate();
                $Auth->apiLog($request);
                $master = new Master();

                $CountryId = $aPost['country_id'];
                $Pincode = $aPost['pincode'];
                $CountryName = $CountryCode = "";
                $Address = [];
                // get country code from country id
                $SQL1 = "SELECT country_code,name FROM `countries` WHERE id=:country_id";
                $CountryData = DB::select($SQL1, array("country_id" => $CountryId));
                if (count($CountryData) > 0) {
                    $CountryName = $CountryData[0]->name;
                    $CountryCode = $CountryData[0]->country_code;
                }

                if (!empty($CountryCode) && !empty($Pincode)) {
                    $SQL2 = "SELECT * FROM `villages` WHERE postal_code=:pincode AND country_code=:country_code";
                    $Address = DB::select($SQL2, array("pincode" => $Pincode, "country_code" => $CountryCode));

                    if (count($Address) > 0) {

                        foreach ($Address as $key => $value) {
                            $value->city_name = !empty($value->city_id) ? $master->getCityName($value->city_id) : "";
                            $value->state_name = !empty($value->state_id) ? $master->getStateName($value->state_id) : "";
                        }
                        $FullAddress = $Address[0];
                    }
                }

                $ResponseData['FullAddress'] = $FullAddress;

                $ResposneCode = 200;
                $message = 'Request processed successfully';
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

    function GeneralDetails(Request $request)
    {
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $empty = false;
        $field = '';
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        // dd($aToken['data']->ID);

        if ($aToken['code'] == 200) {
            $UserId = $aToken['data']->ID;

            $aPost = $request->all();
            $Auth = new Authenticate();
            $Auth->apiLog($request);

            if (!$empty) {
                $UserId = $aToken['data']->ID;
                $sSQL = 'SELECT * FROM users Where id =:Id';
                $userData = DB::select($sSQL, array('Id' => $aToken['data']->ID));

                $is_valid = false;
                $filename = '';
                $id_proof_doc_upload = $userData[0]->id_proof_doc_upload;
                $allowedExts = array('jpeg', 'jpg', "png", "gif", "bmp", "pdf");
                #IMAGE VALIDATION
                if (!empty($_FILES["id_proof_doc_upload"]["name"])) {
                    $id_proof_doc_upload_temp = explode(".", $_FILES["id_proof_doc_upload"]["name"]);
                    $id_proof_doc_upload_extension = strtolower(end($id_proof_doc_upload_temp));

                    if (!in_array($id_proof_doc_upload_extension, $allowedExts)) {
                        $filename = 'Id proof document';
                        $is_valid = true;
                    }
                }
                if (!$is_valid) {
                    #IMAGE UPLOAD
                    if (!empty($request->file('id_proof_doc_upload'))) {
                        $Path = public_path('uploads/user_documents/');
                        $logo_image = $request->file('id_proof_doc_upload');

                        $ImageExtention = $logo_image->getClientOriginalExtension(); #get proper by code;
                        $id_proof_doc_upload = strtotime('now') . '.' . $ImageExtention;
                        $logo_image->move($Path, $id_proof_doc_upload);
                    }

                    $SQL = 'UPDATE users SET
                        emergency_contact_person= :emergency_contact_person,
                        emergency_contact_no1=:emergency_contact_no,
                        organization= :organization,
                        designation= :designation,
                        id_proof_type= :id_proof_type,
                        id_proof_no= :id_proof_no,
                        id_proof_doc_upload= :id_proof_doc_upload
                WHERE id=:id';
                    $Bindings = array(
                        'emergency_contact_person' => $request->emergency_contact_person,
                        'emergency_contact_no' => $request->emergency_contact_no,
                        'organization' => $request->organization,
                        'designation' => $request->designation,
                        'id_proof_type' => $request->id_proof_type,
                        'id_proof_no' => $request->id_proof_no,
                        'id_proof_doc_upload' => $id_proof_doc_upload,
                        'id' => $UserId
                    );
                    // dd( $Bindings);
                    DB::update($SQL, $Bindings);

                    $message = 'General details updated successfully';
                    $ResposneCode = 200;


                } else {
                    $ResposneCode = 400;
                    $message = $filename . ' file is in invalid format';
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

    function SocialMedia(Request $request)
    {
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $empty = false;
        $field = '';
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        // dd($aToken['data']->ID);

        if ($aToken['code'] == 200) {
            $UserId = $aToken['data']->ID;

            $aPost = $request->all();
            $Auth = new Authenticate();
            $Auth->apiLog($request);

            if (!$empty) {
                $UserId = $aToken['data']->ID;
                $sSQL = 'SELECT * FROM users Where id =:Id';
                $userData = DB::select($sSQL, array('Id' => $aToken['data']->ID));

                $SQL = 'UPDATE users SET
                        facebook_link= :facebook_link,
                        twitter_profile_link= :twitter_link,
                        linkedin_profile_link= :linkedin_profile_link
                WHERE id=:id';
                $Bindings = array(
                    'facebook_link' => $request->facebook_link,
                    'twitter_link' => $request->twitter_link,
                    'linkedin_profile_link' => $request->linkedin_profile_link,
                    'id' => $UserId
                );
                // dd( $Bindings);
                DB::update($SQL, $Bindings);

                $message = 'Social Media details updated successfully';
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
            'data' => $ResponseData,
            'message' => $message
        ];

        return response()->json($response, $ResposneCode);
    }

    function CommunicationSettings(Request $request)
    {
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $empty = false;
        $field = '';
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        // dd($aToken['data']->ID);

        if ($aToken['code'] == 200) {
            $UserId = $aToken['data']->ID;

            $aPost = $request->all();
            $Auth = new Authenticate();
            $Auth->apiLog($request);

            if (!$empty) {
                $UserId = $aToken['data']->ID;
                $sSQL = 'SELECT * FROM users Where id =:Id';
                $userData = DB::select($sSQL, array('Id' => $aToken['data']->ID));

                $SQL = 'UPDATE users SET
                        support_email_id= :support_email_id,
                        support_mobile= :support_mobile,
                        email_notification_frequency= :email_notification_frequency
                WHERE id=:id';
                $Bindings = array(
                    'support_mobile' => $request->support_mobile,
                    'support_email_id' => $request->support_email_id,
                    'email_notification_frequency' => $request->email_notification_frequency,
                    'id' => $UserId
                );
                // dd( $Bindings);
                DB::update($SQL, $Bindings);

                $message = 'Communication Settings updated successfully';
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
            'data' => $ResponseData,
            'message' => $message
        ];

        return response()->json($response, $ResposneCode);
    }
    public function editProfile(Request $request)
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

            $sSQL = 'SELECT * FROM users Where id =:Id';
            $userData = DB::select($sSQL, array('Id' => $aToken['data']->ID));

            if (empty($aPost['firstname'])) {
                $empty = true;
                $field = 'Firstname';
            }
            if (empty($aPost['lastname'])) {
                $empty = true;
                $field = 'Lastname';
            }
            if (empty($aPost['mobile'])) {
                $empty = true;
                $field = 'Mobile No';
            }
            if (empty($aPost['email'])) {
                $empty = true;
                $field = 'Email Id';
            }
            if (empty($aPost['dob'])) {
                $empty = true;
                $field = 'Birth Date';
            }
            if (empty($aPost['gender'])) {
                $empty = true;
                $field = 'Gender';
            }

            $is_valid = false;
            $filename = '';
            $id_proof_doc_upload = $userData[0]->id_proof_doc_upload;
            $address_proof_doc_upload = $userData[0]->address_proof_doc_upload;
            if (!$empty) {
                $UserId = $aToken['data']->ID;

                // if (preg_match("/^(?=.*?[a-z])(?=.*?[0-9]).{8,20}$/", $aPost['password'])) {
                if (preg_match("/^[a-zA-Z-' ]*$/", $aPost['firstname'])) {
                    if (preg_match("/^[a-zA-Z-' ]*$/", $aPost['lastname'])) {
                        if (filter_var($aPost['email'], FILTER_VALIDATE_EMAIL)) {
                            if (preg_match('/^[0-9]{10}+$/', $aPost['mobile'])) {
                                #IMAGE VALIDATION
                                if (!empty($_FILES["profile_pic"]["name"])) {
                                    $profile_pic_allowedExts = array('jpeg', 'jpg', "png", "gif", "bmp");
                                    $profile_pic_temp = explode(".", $_FILES["profile_pic"]["name"]);
                                    $profile_pic_extension = strtolower(end($profile_pic_temp));

                                    if (!in_array($profile_pic_extension, $profile_pic_allowedExts)) {
                                        $filename = 'Profile pic document';
                                        $is_valid = true;
                                    }
                                }


                                $allowedExts = array('jpeg', 'jpg', "png", "gif", "bmp", "pdf");
                                #IMAGE VALIDATION
                                if (!empty($_FILES["id_proof_doc_upload"]["name"])) {
                                    $id_proof_doc_upload_temp = explode(".", $_FILES["id_proof_doc_upload"]["name"]);
                                    $id_proof_doc_upload_extension = strtolower(end($id_proof_doc_upload_temp));

                                    if (!in_array($id_proof_doc_upload_extension, $allowedExts)) {
                                        $filename = 'Id proof document';
                                        $is_valid = true;
                                    }
                                }

                                #PDF VALIDATION
                                if (!empty($_FILES["address_proof_doc_upload"]["name"])) {
                                    $address_proof_doc_upload_temp = explode(".", $_FILES["address_proof_doc_upload"]["name"]);
                                    $address_proof_type = strtolower(end($address_proof_doc_upload_temp));
                                    if (!in_array($address_proof_type, $allowedExts)) {
                                        $filename = 'Address proof document';
                                        $is_valid = true;
                                    }
                                }

                                if (!empty($_FILES["cover_picture"]["name"])) {
                                    $cover_picture_allowedExts = array('jpeg', 'jpg', "png", "gif", "bmp");
                                    $cover_picture_temp = explode(".", $_FILES["cover_picture"]["name"]);
                                    $cover_picture_extension = strtolower(end($cover_picture_temp));

                                    if (!in_array($cover_picture_extension, $cover_picture_allowedExts)) {
                                        $filename = 'Cover pic document';
                                        $is_valid = true;
                                    }
                                }

                                if (!$is_valid) {
                                    $profile_image = '';
                                    if (!empty($request->file('profile_pic'))) {

                                        $Path = public_path('uploads/profile_images/');
                                        $logo_image = $request->file('profile_pic');

                                        $ImageExtention = $logo_image->getClientOriginalExtension(); #get proper by code;
                                        $profile_image = strtotime('now') . '_profile.' . $ImageExtention;

                                        $logo_image->move($Path, $profile_image);

                                    }
                                    $cover_image = '';
                                    if (!empty($request->file('cover_picture'))) {
                                        // dd("here");
                                        $Path = public_path('uploads/cover_images/');
                                        $logo_image = $request->file('cover_picture');

                                        $ImageExtension = $logo_image->getClientOriginalExtension();
                                        $cover_image = strtotime('now') . '_cover.' . $ImageExtension;
                                        $logo_image->move($Path, $cover_image);

                                    }

                                    #IMAGE UPLOAD
                                    if (!empty($request->file('id_proof_doc_upload'))) {
                                        $Path = public_path('uploads/user_documents/');
                                        $logo_image = $request->file('id_proof_doc_upload');

                                        $ImageExtention = $logo_image->getClientOriginalExtension(); #get proper by code;
                                        $id_proof_doc_upload = strtotime('now') . '.' . $ImageExtention;
                                        $logo_image->move($Path, $id_proof_doc_upload);
                                    }
                                    #PDF UPLOAD
                                    if (!empty($request->file('address_proof_doc_upload'))) {
                                        $Path = public_path('uploads/user_documents/');
                                        $logo_image = $request->file('address_proof_doc_upload');

                                        $ImageExtention = $logo_image->getClientOriginalExtension(); #get proper by code;
                                        $address_proof_doc_upload = strtotime('now') . '.' . $ImageExtention;
                                        $logo_image->move($Path, $address_proof_doc_upload);
                                    }
                                    // dd($image);

                                    $Exist = array();
                                    $mobile = $aPost['mobile'];
                                    $email = $aPost['email'];
                                    $dob = date('Y-m-d', strtotime($aPost['dob']));

                                    #CHECK IF SAME EMAIL OR MOBILE USER EXIST OR NOT
                                    $SQL2 = 'SELECT id FROM users WHERE (mobile=:mobile OR
                                        email=:email) AND NOT id =:id';
                                    $Exist = DB::select($SQL2, array('mobile' => $mobile, 'email' => $email, 'id' => $UserId));
                                    // dd(sizeof($Exist));

                                    if (sizeof($Exist) == 0) {

                                        $barcode_number = (!empty($request->barcode_number)) ? $request->barcode_number : 0;
                                        $type = (!empty($request->type)) ? $request->type : 0;
                                        $athleteid_request = (!empty($request->athleteid_request)) ? $request->athleteid_request : 0;
                                        $athleteid_request_datetime = (!empty($request->athleteid_request_datetime)) ? $request->athleteid_request_datetime : 0;
                                        $dob = (!empty($request->dob)) ? $request->dob : '';
                                        $gender = (!empty($request->gender)) ? $request->gender : '';
                                        $emergency_contact_person = (!empty($request->emergency_contact_person)) ? $request->emergency_contact_person : '';
                                        $emergency_contact_no = (!empty($request->emergency_contact_no)) ? $request->emergency_contact_no : '';
                                        $t_shirt_size = (!empty($request->t_shirt_size)) ? $request->t_shirt_size : '';
                                        $blood_group = (!empty($request->blood_group)) ? $request->blood_group : '';
                                        $weight = (!empty($request->weight)) ? $request->weight : 0;
                                        $height = (!empty($request->height)) ? $request->height : 0;
                                        $profile_pic = $profile_image;
                                        $organization = (!empty($request->organization)) ? $request->organization : '';
                                        $designation = (!empty($request->designation)) ? $request->designation : '';
                                        $address1 = (!empty($request->address1)) ? $request->address1 : '';
                                        $address2 = (!empty($request->address2)) ? $request->address2 : '';
                                        $city = (!empty($request->city)) ? $request->city : '';
                                        $state = (!empty($request->state)) ? $request->state : '';
                                        $country = (!empty($request->country)) ? $request->country : '';
                                        $cover_picture = $cover_image;
                                        $about_you = isset($request->about_you) ? $request->about_you : "";
                                        $support_email_id = isset($request->support_email_id) ? $request->support_email_id : "";
                                        $support_mobile = isset($request->support_mobile) ? $request->support_mobile : "";
                                        $email_notification_frequency = isset($request->email_notification_frequency) ? $request->email_notification_frequency : 0;

                                        $SQL = 'UPDATE users SET
                                            barcode_number= :barcode_number,
                                            type= :type,
                                            athleteid_request= :athleteid_request,
                                            athleteid_request_datetime= :athleteid_request_datetime,
                                            count_of_eligibility= :count_of_eligibility,
                                            otp= :otp,
                                            firstname= :firstname,
                                            lastname= :lastname,
                                            email= :email,
                                            -- password= :password,
                                            mobile=:mobile,
                                            dob=:dob,
                                            gender= :gender,
                                            emergency_contact_person= :emergency_contact_person,
                                            emergency_contact_no1=:emergency_contact_no,
                                            t_shirt_size= :t_shirt_size,
                                            blood_group= :blood_group,
                                            weight= :weight,
                                            height= :height,
                                            organization= :organization,
                                            designation= :designation,
                                            address1= :address1,
                                            address2= :address2,
                                            city= :city,
                                            state= :state,
                                            country= :country,
                                            pincode=:pincode,
                                            address_check= :address_check,
                                            ca_address1= :ca_address1,
                                            ca_address2= :ca_address2,
                                            ca_city= :ca_city,
                                            ca_state= :ca_state,
                                            ca_pincode= :ca_pincode,
                                            ca_country= :ca_country,
                                            id_proof_type= :id_proof_type,
                                            id_proof_no= :id_proof_no,
                                            id_proof_doc_upload= :id_proof_doc_upload,
                                            nationality= :nationality,
                                            address_proof_type= :address_proof_type,
                                            address_proof_no= :address_proof_no,
                                            address_proof_doc_upload= :address_proof_doc_upload,
                                            device_token= :device_token,
                                            facebook_link= :facebook_link,
                                            twitter_profile_link= :twitter_link,
                                            created_at= :created_at,
                                            linkedin_profile_link= :linkedin_profile_link,
                                            support_email_id= :support_email_id,
                                            support_mobile= :support_mobile,
                                            email_notification_frequency= :email_notification_frequency,
                                            -- UDB= :UDB,
                                            allergies= :allergies,
                                            medical_conditions= :medical_conditions,
                                            -- cover_picture= :cover_picture,
                                            Timezone= :Timezone,
                                            about_you=:about_you
                                            WHERE id=:id';
                                        $Bindings = array(
                                            'barcode_number' => $barcode_number,
                                            'type' => $type,
                                            'athleteid_request' => $athleteid_request,
                                            'athleteid_request_datetime' => $athleteid_request_datetime,
                                            'count_of_eligibility' => ($request->count_of_eligibility) ? $request->count_of_eligibility : 0,
                                            'otp' => $request->otp,
                                            'firstname' => $request->firstname,
                                            'lastname' => $request->lastname,
                                            'email' => $request->email,
                                            // 'password' => md5($request->password),
                                            'mobile' => $request->mobile,
                                            'dob' => $dob,
                                            'gender' => $gender,
                                            'emergency_contact_person' => $emergency_contact_person,
                                            'emergency_contact_no' => $emergency_contact_no,
                                            't_shirt_size' => $t_shirt_size,
                                            'blood_group' => $blood_group,
                                            'weight' => $weight,
                                            'height' => $height,
                                            'organization' => $organization,
                                            'designation' => $designation,
                                            'address1' => $address1,
                                            'address2' => $address2,
                                            'city' => $city,
                                            'state' => $state,
                                            'country' => $country,
                                            'pincode' => $request->pincode,
                                            'address_check' => ($request->address_check) ? $request->address_check : 0,
                                            'ca_address1' => $request->ca_address1,
                                            'ca_address2' => $request->ca_address2,
                                            'ca_city' => $request->ca_city,
                                            'ca_state' => $request->ca_state,
                                            'ca_pincode' => $request->ca_pincode,
                                            'ca_country' => $request->ca_country,
                                            'id_proof_type' => $request->id_proof_type,
                                            'id_proof_no' => $request->id_proof_no,
                                            'id_proof_doc_upload' => $id_proof_doc_upload,
                                            'nationality' => $request->nationality,
                                            'address_proof_type' => $request->address_proof_type,
                                            'address_proof_no' => $request->address_proof_no,
                                            'address_proof_doc_upload' => $address_proof_doc_upload,
                                            'device_token' => $request->device_token,
                                            'facebook_link' => $request->facebook_link,
                                            'twitter_link' => $request->twitter_link,
                                            'linkedin_profile_link' => $request->linkedin_profile_link,
                                            'created_at' => strtotime('now'),
                                            'support_mobile' => $support_mobile,
                                            'support_email_id' => $support_email_id,
                                            'email_notification_frequency' => $email_notification_frequency,
                                            'allergies' => $request->allergies,
                                            'medical_conditions' => $request->medical_conditions,
                                            'Timezone' => $request->Timezone,
                                            'about_you' => $about_you,
                                            'id' => $UserId
                                        );
                                        // dd( $Bindings);
                                        DB::update($SQL, $Bindings);

                                        if (!empty($request->file('profile_pic'))) {
                                            $sSQL_img = 'UPDATE users SET profile_pic = :profile_pic WHERE id=:id';
                                            $Result = DB::update(
                                                $sSQL_img,
                                                array(
                                                    'profile_pic' => $profile_pic,
                                                    'id' => $UserId
                                                )
                                            );
                                        }
                                        if (!empty($request->file('cover_picture'))) {
                                            $sSQL_img = 'UPDATE users SET cover_picture = :cover_picture WHERE id=:id';
                                            $Result = DB::update(
                                                $sSQL_img,
                                                array(
                                                    'cover_picture' => $cover_picture,
                                                    'id' => $UserId
                                                )
                                            );
                                        }

                                        $sql2 = 'SELECT * FROM users WHERE id=:id';
                                        $ResponseData = DB::select($sql2, array('id' => $UserId));
                                        // // dd($ResponseData );
                                        foreach ($ResponseData as $value) {
                                            // $value->barcode_image = (!empty($value->barcode_image)) ? env('ATHLETE_BARCODE_PATH') . $value->barcode_image . '' : "";
                                            $value->profile_pic = (!empty($value->profile_pic)) ? env('ATHLETE_PROFILE_PATH') . $value->profile_pic . '' : '';

                                            $value->cover_picture = (!empty($value->cover_picture)) ? env('ATHLETE_COVER_PATH') . $value->cover_picture . '' : '';

                                        }
                                        //echo '<pre>'; print_r($ResponseData); die;

                                        $message = 'Personal Details updated successfully';
                                        $ResposneCode = 200;
                                    } else {
                                        #ELSE
                                        $ResposneCode = 400;
                                        $message = 'User is exist with same credentials';
                                    }
                                } else {
                                    $ResposneCode = 400;
                                    $message = $filename . ' file is in invalid format';
                                }
                            } else {
                                $ResposneCode = 400;
                                $message = 'Invalid mobile number format';
                            }
                        } else {
                            $ResposneCode = 400;
                            $message = 'Invalid email format';
                        }
                    } else {
                        $ResposneCode = 400;
                        $message = 'Invalid lastname format';
                    }
                } else {
                    $ResposneCode = 400;
                    $message = 'Invalid firstname format';
                }
                // } else {
                //     $ResposneCode = 400;
                //     $message = 'Password should be 8-20 characters in length';
                // }
            } else {

                $ResposneCode = 400;
                $message = $field . ' is empty';
                // dd($message);
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

    public function module_access(Request $request)
    {
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);

        if ($aToken['code'] == 200) {

            $Auth = new Authenticate();
            $Auth->apiLog($request);

            $user_id = $aToken['data']->ID;
            $access = $request->access;

            foreach ($access as $key => $val) {
                $role_id = $key;
                $acs = $val;

                $data = UserRight::where('user_id', $user_id)->where('role_id', $role_id)->get();

                if (!$data->isEmpty()) {
                    UserRight::where('user_id', $user_id)->where('role_id', $role_id)->update(['access' => $acs, 'updated_date' => strtotime('now')]);
                } else {
                    UserRight::insert(['user_id' => $user_id, 'role_id' => $role_id, 'access' => $acs, 'updated_date' => strtotime('now')]);
                }
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

    public function addnewuser(Request $request)
    {
        $aPost = $request->all();

        $ResponseData = [];
        $ResposneCode = 200;
        $empty = false;
        $message = 'Success';
        $field = '';

        if (empty($aPost['firstname'])) {
            $empty = true;
            $field = 'Firstname';
        }
        if (empty($aPost['lastname'])) {
            $empty = true;
            $field = 'Lastname';
        }
        if (empty($aPost['mobile'])) {
            $empty = true;
            $field = 'Mobile No';
        }
        if (empty($aPost['events'])) {
            $empty = true;
            $field = 'Events';
        }

        if (empty($aPost['email'])) {
            $empty = true;
            $field = 'Email Id';
        }
        if (empty($aPost['role'])) {
            $empty = true;
            $field = 'Role';
        }

        if (!$empty) {
            $Auth = new Authenticate();
            $Auth->apiLog($request);
            // dd($aPost);
            if (preg_match("/^[a-zA-Z-' ]*$/", $aPost['firstname'])) {
                if (preg_match("/^[a-zA-Z-' ]*$/", $aPost['lastname'])) {
                    if (filter_var($aPost['email'], FILTER_VALIDATE_EMAIL)) {
                        if (preg_match('/^[0-9]{10}+$/', $aPost['mobile'])) {
                            #EXIST CHECK
                            $SQL1 = 'SELECT id FROM users WHERE (email=:email OR mobile=:mobile) AND is_deleted = 0';
                            $Exist = DB::select($SQL1, array('email' => $aPost['email'], 'mobile' => $aPost['mobile']));

                            // dd($Exist);
                            if (sizeof($Exist) == 0) {
                                $Binding = array(
                                    'firstname' => $aPost['firstname'],
                                    'lastname' => $aPost['lastname'],
                                    'email' => $aPost['email'],
                                    'mobile' => $aPost['mobile'],
                                    'created_at' => strtotime('now')
                                );

                                $SQL2 = 'INSERT INTO users (firstname,lastname,email,mobile,created_at) VALUES(:firstname,:lastname,:email,:mobile,:created_at)';
                                DB::select($SQL2, $Binding);

                                $UserId = DB::getPdo()->lastInsertId();

                                #ADD EVENTS OF USER
                                $Events = array($aPost['events']);
                                // dd($Events);
                                $eventIds = is_array($Events) ? $Events : [$Events];
                                foreach ($eventIds as $key => $value) {

                                    #event_users insert
                                    $Binding = array(
                                        'event_id' => $value[$key],
                                        'user_id' => $UserId
                                    );
                                    // dd($Binding);
                                    $SQL2 = 'INSERT INTO event_users (event_id,user_id) VALUES(:event_id,:user_id)';
                                    DB::select($SQL2, $Binding);
                                }

                                #ADD USER ROLE
                                $Role = $aPost['role'];
                                // insert in user_rights
                                $SQL3 = 'INSERT INTO user_rights (role_id,user_id,updated_date) VALUES(:role_id,:user_id,:updated_date)';
                                DB::select($SQL3, array('role_id' => $Role, 'user_id' => $UserId, 'updated_date' => strtotime('now')));

                                $ResposneCode = 200;
                                $message = 'New user insert Successfully';
                            } else {
                                $ResposneCode = 400;
                                $message = 'User already exist, please add new user';
                            }
                        } else {
                            $ResposneCode = 400;
                            $message = 'Invalid mobile number format';
                        }
                    } else {
                        $ResposneCode = 400;
                        $message = 'Invalid email format';
                    }
                } else {
                    $ResposneCode = 400;
                    $message = 'Invalid lastname format';
                }
            } else {
                $ResposneCode = 400;
                $message = 'Invalid firstname format';
            }


        } else {
            $ResposneCode = 400;
            $message = $field . ' is empty';
        }
        $response = [
            'data' => $ResponseData,
            'message' => $message
        ];

        return response()->json($response, $ResposneCode);
    }

    public function delete_profile(Request $request)
    {

        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        // dd($aToken['data']->ID);

        if ($aToken['code'] == 200) {
            $aPost = $request->all();
            $Auth = new Authenticate();
            $Auth->apiLog($request);

            $sSQL = "UPDATE users SET profile_pic='' Where id=:Id";
            $userData = DB::update($sSQL, array('Id' => $aToken['data']->ID));
            // dd($sSQL,$userData);

            $ResposneCode = 200;
            $message = "Profile deleted successfully";
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

    public function update_profile_pic(Request $request)
    {

        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        // dd($aToken['data']->ID);

        if ($aToken['code'] == 200) {
            $aPost = $request->all();
            $Auth = new Authenticate();
            $Auth->apiLog($request);

            // dd($request->file('profile_pic'));

            if (!empty($request->file('profile_pic'))) {

                $Path = public_path('uploads/profile_images/');
                $logo_image = $request->file('profile_pic');

                $ImageExtention = $logo_image->getClientOriginalExtension();
                $profile_image = strtotime('now') . '_profile.' . $ImageExtention;

                $logo_image->move($Path, $profile_image);
                // dd($profile_image);

                $sSQL_img = 'UPDATE users SET profile_pic=:profile_pic WHERE id=:id';
                $Result = DB::update(
                    $sSQL_img,
                    array(
                        'profile_pic' => $profile_image,
                        'id' => $aToken['data']->ID
                    )
                );
            }

            $ResposneCode = 200;
            $message = "Profile pic uploaded successfully";
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

    public function EditUserMedical(Request $request)
    {
        $responseData = [];
        $response['message'] = "";
        $responseCode = 400;
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);

        if ($aToken['code'] == 200) {
            $Auth = new Authenticate();
            $Auth->apiLog($request);
            $userId = $aToken['data']->ID;

            $diabetes = (!empty($request->diabetes)) ? $request->diabetes : 0;
            $chestpain = (!empty($request->chestpain)) ? $request->chestpain : 0;
            $dehydrationseverity = (!empty($request->dehydrationseverity)) ? $request->dehydrationseverity : 0;
            $musclecramps = (!empty($request->musclecramps)) ? $request->musclecramps : 0;
            $lowbloodsugar = (!empty($request->lowbloodsugar)) ? $request->lowbloodsugar : 0;
            $undermedication = (!empty($request->undermedication)) ? $request->undermedication : 0;
            $drugallergy = (!empty($request->drugallergy)) ? $request->drugallergy : 0;
            $angina = (!empty($request->angina)) ? $request->angina : 0;
            $abnormalheartrhythm = (!empty($request->abnormalheartrhythm)) ? $request->abnormalheartrhythm : 0;
            $pacemaker = (!empty($request->pacemaker)) ? $request->pacemaker : 0;
            $highbloodpressure = (!empty($request->highbloodpressure)) ? $request->highbloodpressure : 0;

            $epilepsy = (!empty($request->epilepsy)) ? $request->epilepsy : 0;
            $bleedingdisorders = (!empty($request->bleedingdisorders)) ? $request->bleedingdisorders : 0;
            $asthma = (!empty($request->asthma)) ? $request->asthma : 0;
            $anemia = (!empty($request->anemia)) ? $request->anemia : 0;
            $hospitalized = (!empty($request->hospitalized)) ? $request->hospitalized : 0;
            $infections = (!empty($request->infections)) ? $request->infections : 0;
            $pregnant = (!empty($request->pregnant)) ? $request->pregnant : 0;
            $covidstatus = isset($request->covidstatus) ? $request->covidstatus : 0;
            $currentmedications = isset($request->currentmedications) ? $request->currentmedications : 0;
            $familydoctorname = isset($request->familydoctorname) ? $request->familydoctorname : "";
            $familydoctorcontactno = isset($request->familydoctorcontactno) ? $request->familydoctorcontactno : "";
            $meditaion_details = isset($request->meditaion_details) ? $request->meditaion_details : "";
            $drug_allergy_details = isset($request->drug_allergy_details) ? $request->drug_allergy_details : "";
            $hospitalization_details = isset($request->hospitalization_details) ? $request->hospitalization_details : "";
            $stage_pregnancy = isset($request->stage_pregnancy) ? $request->stage_pregnancy : "";
            $current_medication_names = isset($request->current_medication_names) ? $request->current_medication_names : "";

            // Fetching user details
            $sql = 'SELECT * from user_details where user_id =' . $userId;
            $Exist = DB::select($sql, array());
            // dd($Exist);

            if (!empty($Exist)) {
                $sql = 'UPDATE user_details
                SET diabetes= :diabetes,
                chestpain= :chestpain,
                dehydrationseverity=:dehydrationseverity,
                musclecramps=:musclecramps,
                lowbloodsugar=:lowbloodsugar,
                undermedication=:undermedication,
                drugallergy=:drugallergy,
                angina=:angina,
                abnormalheartrhythm=:abnormalheartrhythm,
                pacemaker=:pacemaker,
                highbloodpressure=:highbloodpressure,
                epilepsy=:epilepsy,
                bleedingdisorders=:bleedingdisorders,
                asthma=:asthma,
                anemia=:anemia,
                hospitalized=:hospitalized,
                infections=:infections,
                pregnant=:pregnant,
                covidstatus=:covidstatus,
                currentmedications=:currentmedications,
                familydoctorname=:familydoctorname,
                familydoctorcontactno=:familydoctorcontactno,
                meditaion_details=:meditaion_details,
                drug_allergy_details=:drug_allergy_details,
                hospitalization_details=:hospitalization_details,
                stage_pregnancy=:stage_pregnancy,
                current_medication_names=:current_medication_names,
                updated_at=:updated_at
                WHERE  user_id=:user_id';

                $Binding = [
                    'diabetes' => $diabetes,
                    'chestpain' => $chestpain,
                    'dehydrationseverity' => $dehydrationseverity,
                    'musclecramps' => $musclecramps,
                    'lowbloodsugar' => $lowbloodsugar,
                    'undermedication' => $undermedication,
                    'drugallergy' => $drugallergy,
                    'angina' => $angina,
                    'abnormalheartrhythm' => $abnormalheartrhythm,
                    'pacemaker' => $pacemaker,
                    'highbloodpressure' => $highbloodpressure,
                    'epilepsy' => $epilepsy,
                    'bleedingdisorders' => $bleedingdisorders,
                    'asthma' => $asthma,
                    'anemia' => $anemia,
                    'hospitalized' => $hospitalized,
                    'infections' => $infections,
                    'pregnant' => $pregnant,
                    'covidstatus' => $covidstatus,
                    'currentmedications' => $currentmedications,
                    'familydoctorname' => $familydoctorname,
                    'familydoctorcontactno' => $familydoctorcontactno,
                    'meditaion_details' => $meditaion_details,
                    'drug_allergy_details' => $drug_allergy_details,
                    'hospitalization_details' => $hospitalization_details,
                    'stage_pregnancy' => $stage_pregnancy,
                    'current_medication_names' => $current_medication_names,
                    'updated_at' => strtotime("now"),
                    'user_id' => $userId
                    // 'id' => $id

                ];
                //  dd($sql,$Binding);
                $responseData['userData'] = DB::update($sql, $Binding);
                // dd($responseData['userData']);
                $sql = 'UPDATE users
                SET blood_group= :blood_group,
                weight= :weight,
                height= :height,
                allergies= :allergies,
                medical_conditions= :medical_conditions
                WHERE  id=:user_id';

                $Binding = [
                    'blood_group' => $request->blood_group,
                    'weight' => $request->weight,
                    'height' => $request->height,
                    'allergies' => $request->allergies,
                    'medical_conditions' => $request->medical_conditions,
                    'user_id' => $userId
                ];
                $responseData['user'] = DB::update($sql, $Binding);



                $responseCode = 200;
                $message = 'Medical Details updated successfully';

            } else {

                $sql = 'INSERT INTO user_details( diabetes , chestpain, dehydrationseverity, musclecramps, lowbloodsugar,undermedication, drugallergy,angina,abnormalheartrhythm,pacemaker,highbloodpressure,epilepsy,bleedingdisorders,asthma,anemia,hospitalized,infections,pregnant,covidstatus,currentmedications,familydoctorname,familydoctorcontactno,user_id,meditaion_details,drug_allergy_details,hospitalization_details,stage_pregnancy,current_medication_names) VALUES
                (:diabetes,:chestpain,:dehydrationseverity,:musclecramps,:lowbloodsugar,:undermedication,:drugallergy,:angina,:abnormalheartrhythm,:pacemaker,:highbloodpressure,:epilepsy,:bleedingdisorders,:asthma,:anemia,:hospitalized,:infections,:pregnant,:covidstatus,:currentmedications,:familydoctorname,:familydoctorcontactno,:user_id,:meditaion_details,:drug_allergy_details,:hospitalization_details,:stage_pregnancy,:current_medication_names)';

                $Binding = [
                    'diabetes' => $diabetes,
                    'chestpain' => $chestpain,
                    'dehydrationseverity' => $dehydrationseverity,
                    'musclecramps' => $musclecramps,
                    'lowbloodsugar' => $lowbloodsugar,
                    'undermedication' => $undermedication,
                    'drugallergy' => $drugallergy,
                    'angina' => $angina,
                    'abnormalheartrhythm' => $abnormalheartrhythm,
                    'pacemaker' => $pacemaker,
                    'highbloodpressure' => $highbloodpressure,
                    'epilepsy' => $epilepsy,
                    'bleedingdisorders' => $bleedingdisorders,
                    'asthma' => $asthma,
                    'anemia' => $anemia,
                    'hospitalized' => $hospitalized,
                    'infections' => $infections,
                    'pregnant' => $pregnant,
                    'covidstatus' => $covidstatus,
                    'currentmedications' => $currentmedications,
                    'familydoctorname' => $familydoctorname,
                    'familydoctorcontactno' => $familydoctorcontactno,
                    'user_id' => $userId,
                    'meditaion_details' => $meditaion_details,
                    'drug_allergy_details' => $drug_allergy_details,
                    'hospitalization_details' => $hospitalization_details,
                    'stage_pregnancy' => $stage_pregnancy,
                    'current_medication_names' => $current_medication_names
                ];


                // dd($sql, $Binding);
                $responseData['userData'] = DB::insert($sql, $Binding);

                $sql = 'UPDATE users
                SET blood_group= :blood_group,
                weight= :weight,
                height= :height,
                allergies= :allergies,
                medical_conditions= :medical_conditions
                WHERE  id=:user_id';

                $Binding = [
                    'blood_group' => $request->blood_group,
                    'weight' => $request->weight,
                    'height' => $request->height,
                    'allergies' => $request->allergies,
                    'medical_conditions' => $request->medical_conditions,
                    'user_id' => $userId
                ];
                $responseData['user'] = DB::update($sql, $Binding);

                $responseCode = 200;
                $message = 'Record inserted successfully';

            }

            // $sql = 'SELECT * from users where id =' . $userId;
            // $users = DB::select($sql, array());
            // if(!empty($users )){
            //     $responseData['users'] = $users ;
            // }
        } else {
            $responseCode = $aToken['code'];
            $message = $aToken['message'];
        }

        $response = [
            'data' => $responseData,
            'message' => $message
        ];

        return response()->json($response, $responseCode);
    }

    public function getRoles(Request $request)
    {
        $ResponseData = $FullAddress = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $empty = false;
        $field = '';
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        // dd($aToken['data']->ID);
        if ($aToken['code'] == 200) {
            $aPost = $request->all();
          
                $Auth = new Authenticate();
               
                $SQL1 = "SELECT id,name FROM `role_master` WHERE status = 1 AND is_deleted = 0";
                $aRoleData = DB::select($SQL1, array());
                $ResponseData['AllRoles'] = $aRoleData;

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

    public function getEvents(Request $request)
    {
        $ResponseData = $FullAddress = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $empty = false;
        $field = '';
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        $user_id = $aToken['data']->ID;
        // dd($aToken['data']->ID);
        if ($aToken['code'] == 200) {
            $aPost = $request->all();
          
                $Auth = new Authenticate();
               
                $SQL1 = "SELECT id,name FROM `events` WHERE active = 1 AND deleted = 0 AND created_by = ".$user_id." ";
                $aEventData = DB::select($SQL1, array());

                if(!empty($aEventData)){
                    foreach($aEventData as $res){
                        $res->checked = false;
                    }
                }
                $ResponseData['AllEvents'] = $aEventData;

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

    public function addEditOrgUser(Request $request)
    {

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

            if (empty($aPost['user_role'])) {
                $empty = true;
                $field = 'User Role';
            }

             if (empty($aPost['email'])) {
                $empty = true;
                $field = 'Email';
            }

            if (!$empty) {

                $UserRole = !empty($request->user_role) ? $request->user_role : 0;
                $firstname = !empty($request->firstname) ? $request->firstname : '';
                $lastname = !empty($request->lastname) ? $request->lastname : '';
                $email = !empty($request->email) ? $request->email : '';
                $mobile = !empty($request->mobile) ? $request->mobile : '';
                $dob = !empty($request->dob) ? $request->dob : '';
                $gender = !empty($request->gender) ? $request->gender : '';
                $EventSelectedType = !empty($request->event_selected_type) ? $request->event_selected_type : 1;
                $EventData = !empty($request->event_data) ? json_decode($request->event_data) : [];

                $EditOrgUserId = !empty($request->edit_org_user_id) ? $request->edit_org_user_id : 0;

                //---------- all ticket apply -----------
                $event_ids = '';
                if ($EventSelectedType == 1) {
                    $Sql = 'SELECT id FROM events WHERE `created_by`=:userId AND active = 1 AND deleted = 0';
                    $event_aResult = DB::select(
                        $Sql,array(
                            'userId' => $UserId
                        )
                    );
                
                    $event_id_array = !empty($event_aResult) ? array_column($event_aResult, 'id') : '';
                    $event_ids = !empty($event_id_array) ? implode(",", $event_id_array) : '';
                } else {
                    $event_id_array = [];
                    if (!empty($EventData)) {
                        foreach ($EventData as $res) {
                            if (isset($res->checked) && $res->checked == true)
                                $event_id_array[] = $res->id;
                        }
                    }
                    $event_ids = !empty($event_id_array) ? implode(",", $event_id_array) : '';
                }

                $SQL1 = "SELECT CONCAT(firstname,' ', lastname) AS username FROM users WHERE id = :user_id";
                $aUserResult = DB::select($SQL1, array("user_id" => $UserId));
                $username = !empty($aUserResult) ? $aUserResult[0]->username : '';

                if (empty($EditOrgUserId)) {     // data insert

                    $SQL = "SELECT email FROM organiser_users WHERE LOWER(email) = :email";
                    $IsExist = DB::select($SQL, array('email' => strtolower($email)));
                    // dd($IsExist);

                    $SQL1 = "SELECT email FROM users WHERE LOWER(email) = :email AND id = :user_id";
                    $IsExist1 = DB::select($SQL1, array('email' => strtolower($email), "user_id" => $UserId));
                    //dd($IsCouponExist);

                    if (!empty($IsExist)) {
                        $ResposneCode = 200;
                        $message = "Email id is already exists, please use another email.";
                        $flag = 1;
                        $ResponseData = $flag;
                    } else if (!empty($IsExist1)) {
                        $ResposneCode = 200;
                        $message = "Email id is already exists, please use another email.";
                        $flag = 2;
                        $ResponseData = $flag;
                    } else {

                        $Bindings = array(
                            "user_role" => $UserRole,
                            "firstname" => $firstname,
                            "lastname" => $lastname,
                            "email" => $email,
                            "mobile" => $mobile,
                            "dob" => $dob,
                            "gender" => $gender,
                            "event_selected_type" => $EventSelectedType,
                            "event_ids" => $event_ids,
                            "created_by" => $UserId,
                            "created_date" => time()
                        );

                        $insert_SQL = "INSERT INTO organiser_users (user_role,firstname,lastname,email,mobile,dob,gender,event_selected_type,event_ids,created_by,created_date) VALUES(:user_role,:firstname,:lastname,:email,:mobile,:dob,:gender,:event_selected_type,:event_ids,:created_by,:created_date)";
                        DB::insert($insert_SQL, $Bindings);
                        $last_inserted_id = DB::getPdo()->lastInsertId();
                        //dd($last_inserted_id);
                       
                        // $Email = new Emails();
                        // $Email->send_OrganiserUser_mail($email, $firstname, $lastname, $username, $last_inserted_id);

                        $message = 'Organising user added successfully';
                        $ResposneCode = 200;
                    }

                } else {                       // data update
                   
                    $SQL = "SELECT email FROM organiser_users WHERE LOWER(email) = :email AND id != :edit_id ";
                    $IsExist = DB::select($SQL, array('email' => strtolower($email), "edit_id" => $EditOrgUserId));
                    // dd($IsExist);

                    $SQL1 = "SELECT email FROM users WHERE LOWER(email) = :email AND id = :user_id";
                    $IsExist1 = DB::select($SQL1, array('email' => strtolower($email), "user_id" => $UserId));

                    if (!empty($IsExist)) {
                        $ResposneCode = 200;
                        $message = "Email id is already exists, please use another email.";
                        $flag = 1;
                        $ResponseData = $flag;
                    } else if (!empty($IsExist1)) {
                        $ResposneCode = 200;
                        $message = "Email id is already exists, please use another email.";
                        $flag = 2;
                        $ResponseData = $flag;
                    } else {

                        $Bindings = array(
                            "user_role" => $UserRole,
                            "firstname" => $firstname,
                            "lastname" => $lastname,
                            "email" => $email,
                            "mobile" => $mobile,
                            "dob" => $dob,
                            "gender" => $gender,
                            "event_selected_type" => $EventSelectedType,
                            "event_ids" => $event_ids,
                            'edit_id' => $EditOrgUserId
                        );

                        $edit_sql = 'UPDATE organiser_users SET user_role =:user_role, firstname =:firstname, lastname =:lastname, email =:email, mobile =:mobile, dob =:dob, gender =:gender, event_selected_type =:event_selected_type, event_ids =:event_ids WHERE id = :edit_id';
                        DB::update($edit_sql, $Bindings);
                        
                        // $Email = new Emails();
                        // $Email->send_OrganiserUser_mail($email, $firstname, $lastname, $username, $EditOrgUserId);

                        $message = 'Organising user updated successfully';
                        $ResposneCode = 200;
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
            'success' => $ResposneCode,
            'data' => $ResponseData,
            'message' => $message
        ];
        return response()->json($response, $ResposneCode);
    }

    public function OrgUserDetails(Request $request)
    {
        $ResponseData = $FullAddress = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $empty = false;
        $field = '';
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        $user_id = $aToken['data']->ID;
        // dd($aToken['data']->ID);
        if ($aToken['code'] == 200) {
            $aPost = $request->all();
          
                $Auth = new Authenticate();
               
                $SQL1 = "SELECT id,user_role,firstname,lastname,mobile,email,gender,status,dob,(select name from role_master where id=organiser_users.user_role) as user_role FROM `organiser_users` WHERE status = 1 AND created_by = ".$user_id." ";
                $aData = DB::select($SQL1, array());

                if(!empty($aData)){
                    foreach($aData as $res){
                        $res->username = ucfirst($res->firstname.' '.$res->lastname);
                        if($res->gender == 1){
                           $res->gender = 'Male';
                        }else if($res->gender == 2){
                           $res->gender = 'Female';
                        }else{
                           $res->gender = 'Other';
                        }

                        $res->dob = date('d-m-Y',strtotime($res->dob));
                    }
                }
                $ResponseData['AllOrgUsers'] = $aData;

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

    // Delete Org User
    public function DeleteOrgUser(Request $request)
    {
        $response['data'] = [];
        $response['message'] = '';
        $ResposneCode = 400;
        $empty = false;

        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        //dd($aToken);
        if ($aToken['code'] == 200) {

            $OrgUserId = !empty($request->org_user_id) ? $request->org_user_id : 0;
           
            $del_sSQL = 'DELETE FROM organiser_users WHERE `id`=:id ';
            DB::delete(
                $del_sSQL,array(
                    'id' => $OrgUserId
                )
            );
            
            $response['data'] = [];
            $response['message'] = 'Record removed successfully';
            $ResposneCode = 200;

        } else {
            $ResposneCode = $aToken['code'];
            $response['message'] = $aToken['message'];
        }

        return response()->json($response, $ResposneCode);
    }

    public function EditOrgUser(Request $request)
    {
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $empty = false;
        $field = '';
        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        $user_id = $aToken['data']->ID;
        // dd($aToken['data']->ID);
        if ($aToken['code'] == 200) {
            $aPost = $request->all();
          
                $Auth = new Authenticate();
                $OrgUserId = !empty($request->org_user_id) ? $request->org_user_id : 0;
               
                $SQL1 = "SELECT * FROM `organiser_users` WHERE status = 1 AND id = ".$OrgUserId." ";
                $aData = DB::select($SQL1, array());

                if(!empty($aData)){
                    foreach($aData as $res){
                        
                        if(!empty($res->event_ids)){
                            $new_array = explode(",", $res->event_ids);

                            $SQL1 = "SELECT id,name FROM `events` WHERE active = 1 AND deleted = 0 AND created_by = ".$user_id." ";
                            $aEventData = DB::select($SQL1, array());

                            if (!empty($aEventData)) {
                                foreach ($aEventData as $res1) {
                                    if (in_array($res1->id, $new_array)) {
                                        $res1->checked = true;
                                    } else {
                                        $res1->checked = false;
                                    }
                                }
                            }
                        }

                        $res->eventData = isset($aEventData) ? $aEventData : [];
                       
                    }
                }
                $ResponseData['AllOrgUsers'] = $aData;

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

}
