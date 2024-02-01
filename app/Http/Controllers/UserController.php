<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Libraries\Authenticate;
use App\Models\UserRight;


class UserController extends Controller
{
    public function getProfile(Request $request)
    {
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $aToken = app('App\Http\Controllers\LoginController')->validate_request($request);
        // dd($aToken['data']->ID);

        if ($aToken['code'] == 200) {

            $Auth = new Authenticate();
            $Auth->apiLog($request);

            $sSQL = 'SELECT * FROM Users Where id =:Id';
            $userData = DB::select(
                $sSQL,
                array(
                    'Id' => $aToken['data']->ID
                )
            );

            foreach ($userData as $value) {
                $value->profile_pic = (!empty($value->profile_pic)) ? env('ATHLETE_PROFILE_PATH') . $value->profile_pic . '' : '';
                $value->cover_picture = (!empty($value->cover_picture)) ? url('') . '/uploads/cover_photo/' . $value->cover_picture . '' : '';
            }
            if (!empty($userData)) {
                $ResponseData['userData'] = $userData;
            }

            $sSQL = 'SELECT * FROM master_timezones Where active = 1';
            $ResponseData['timezones'] = DB::select($sSQL, array());

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

    // public function editProfile(Request $request)
    // {
    //     $ResponseData = [];
    //     $response['message'] = "";
    //     $ResposneCode = 400;
    //     $aToken = app('App\Http\Controllers\LoginController')->validate_request($request);
    //     // dd($aToken['data']->ID);

    //     if ($aToken['code'] == 200) {
    //         $aPost = $request->all();
    //         $Auth = new Authenticate();
    //         $Auth->apiLog($request);

    //         $sSQL = 'SELECT * FROM Users Where id =:Id';
    //         $userData = DB::select(
    //             $sSQL,
    //             array(
    //                 'Id' => $aToken['data']->ID
    //             )
    //         );

    //         if (empty($aPost['firstname'])) {
    //             $empty = true;
    //             $field = 'Firstname';
    //         }
    //         if (empty($aPost['lastname'])) {
    //             $empty = true;
    //             $field = 'Lastname';
    //         }
    //         if (empty($aPost['mobile'])) {
    //             $empty = true;
    //             $field = 'Mobile No';
    //         }
    //         if (empty($aPost['email'])) {
    //             $empty = true;
    //             $field = 'Email Id';
    //         }
    //         if (empty($aPost['dob'])) {
    //             $empty = true;
    //             $field = 'Birth Date';
    //         }
    //         if (empty($aPost['gender'])) {
    //             $empty = true;
    //             $field = 'Gender';
    //         }

    //         $is_valid = false;
    //         $filename = '';
    //         if (!$empty) {


    //             $AthleteId = $aToken['data']->ID;

    //             if (preg_match("/^[a-zA-Z-' ]*$/", $aPost['firstname'])) {
    //                 if (preg_match("/^[a-zA-Z-' ]*$/", $aPost['lastname'])) {
    //                     if (filter_var($aPost['email'], FILTER_VALIDATE_EMAIL)) {
    //                         if (preg_match('/^[0-9]{10}+$/', $aPost['mobile'])) {
    //                             #IMAGE VALIDATION
    //                             if (!empty($_FILES["profile_pic"]["name"])) {
    //                                 $profile_pic_allowedExts = array('jpeg', 'jpg', "png", "gif", "bmp");
    //                                 $profile_pic_temp = explode(".", $_FILES["profile_pic"]["name"]);
    //                                 $profile_pic_extension = strtolower(end($profile_pic_temp));

    //                                 if (!in_array($profile_pic_extension, $profile_pic_allowedExts)) {
    //                                     $filename = 'Profile pic document';
    //                                     $is_valid = true;
    //                                 }
    //                             }

    //                             if (!$is_valid) {
    //                                 $image = '';
    //                                 if (!empty($request->file('profile_pic'))) {

    //                                     //$Path = public_path('uploads/profile_images/');
    //                                     //$logo_image = $request->file('profile_pic');

    //                                     //$ImageExtention = $logo_image->getClientOriginalExtension(); #get proper by code;
    //                                     ///$image = strtotime('now') . '_profile.' . $ImageExtention;
    //                                     //$logo_image->move($Path, $image);

    //                                     //NEW UPDATED CODE 11 JAN 24 BY SHARDA FOR RESIZE IMAGE
    //                                     $Path = public_path('uploads/profile_images/');
    //                                     $logo_image = $request->file('profile_pic');

    //                                     $ImageExtension = $logo_image->getClientOriginalExtension();
    //                                     $image = strtotime('now') . '_profile.' . $ImageExtension;

    //                                     // Save the original image without orientation metadata
    //                                     $originalImagePath = $Path . $image;
    //                                     $logo_image = Image::make($logo_image->getRealPath());
    //                                     $logo_image->orientate()->save($originalImagePath);

    //                                     // Create and save the thumbnail
    //                                     //$thumbnailPath = public_path('thumbnail') . '/' . $image;
    //                                     // Image::make($originalImagePath)
    //                                     //     ->resize(260, 260, function ($constraint) {
    //                                     //         $constraint->aspectRatio();
    //                                     //     });
    //                                     //->save($thumbnailPath);

    //                                     // Move the original image to its destination
    //                                     //$destinationPath = public_path('uploads/profile_images/');
    //                                     // $logo_image->move($destinationPath, $image);
    //                                 }
    //                                 // dd($image);

    //                                 // $SQL = 'SELECT mobile,email,dob,UDB FROM athletes WHERE id=:id';
    //                                 // $userData = DB::select(DB::raw($SQL), array('id' => $AthleteId));

    //                                 // $UDB = $userData[0]->UDB;

    //                                 #NON-EDITABLE FIELDS
    //                                 $email = $userData[0]->email;
    //                                 $dob = $userData[0]->dob;
    //                                 $mobile = $userData[0]->mobile;

    //                                 $firstname = $aPost['firstname'];
    //                                 $lastname = $aPost['lastname'];
    //                                 $Exist = array();


    //                                 #UDB ATHLETE EDIT FIELDS
    //                                 if (!empty($UDB)) {
    //                                     $mobile = $aPost['mobile'];
    //                                     $email = $aPost['email'];
    //                                     $dob = date('Y-m-d', strtotime($aPost['dob']));

    //                                     #CHECK IF SAME EMAIL OR MOBILE USER EXIST OR NOT
    //                                     $SQL2 = 'SELECT id FROM athletes WHERE (mobile=:mobile OR
    //                                     email=:email) AND NOT id =:id';
    //                                     $Exist = DB::select(DB::raw($SQL2), array('mobile' => $mobile, 'email' => $email, 'id' => $AthleteId));

    //                                     //$SQL = 'UPDATE athletes SET UDB=:UDB WHERE id=:id';
    //                                     //DB::update(DB::raw($SQL), array('UDB'=> 0, 'id' => $AthleteId));
    //                                 }

    //                                 //dd($Exist);

    //                                 if (sizeof($Exist) == 0) {

    //                                     $SQL = 'UPDATE athletes SET UDB=:UDB WHERE id=:id';
    //                                     DB::update(DB::raw($SQL), array('UDB' => 0, 'id' => $AthleteId));

    //                                     $gender = isset($aPost['gender']) ? $aPost['gender'] : '';
    //                                     $emergency_contact_person = isset($aPost['emergency_contact_person']) ? $aPost['emergency_contact_person'] : '';
    //                                     $emergency_contact_no1 = isset($aPost['emergency_contact_no1']) ? $aPost['emergency_contact_no1'] : '';
    //                                     $t_shirt_size = isset($aPost['t_shirt_size']) ? $aPost['t_shirt_size'] : '';
    //                                     $blood_group = isset($aPost['blood_group']) ? $aPost['blood_group'] : '';
    //                                     $weight = isset($aPost['weight']) ? $aPost['weight'] : '';
    //                                     $height = isset($aPost['height']) ? $aPost['height'] : '';
    //                                     $profile_pic = $image;
    //                                     $organization = isset($aPost['organization']) ? $aPost['organization'] : '';
    //                                     $designation = isset($aPost['designation']) ? $aPost['designation'] : "";
    //                                     $allergies = isset($aPost['allergies']) ? $aPost['allergies'] : "";
    //                                     $medical_conditions = isset($aPost['medical_conditions']) ? $aPost['medical_conditions'] : "";

    //                                     $Gender = '';
    //                                     if ($gender == 1)
    //                                         $Gender = 'Male';
    //                                     if ($gender == 2)
    //                                         $Gender = 'Female';
    //                                     if ($gender == 3)
    //                                         $Gender = 'Other';

    //                                     $SQL = 'UPDATE athletes SET
    //                                         firstname=:firstname,
    //                                         lastname=:lastname,
    //                                         mobile=:mobile,
    //                                         email=:email,
    //                                         dob=:dob,
    //                                         gender=:gender,
    //                                         emergency_contact_person=:emergency_contact_person,
    //                                         emergency_contact_no1=:emergency_contact_no1,
    //                                         t_shirt_size=:t_shirt_size,
    //                                         blood_group=:blood_group,
    //                                         weight=:weight,
    //                                         height=:height,
    //                                         organization=:organization,
    //                                         designation=:designation,
    //                                         allergies=:allergies,
    //                                         medical_conditions=:medical_conditions
    //                                         WHERE
    //                                         id=:id';
    //                                     $Bindings = array(
    //                                         'firstname' => $firstname,
    //                                         'lastname' => $lastname,
    //                                         'mobile' => $mobile,
    //                                         'email' => $email,
    //                                         'dob' => $dob,
    //                                         'gender' => $Gender,
    //                                         'emergency_contact_person' => $emergency_contact_person,
    //                                         'emergency_contact_no1' => $emergency_contact_no1,
    //                                         't_shirt_size' => $t_shirt_size,
    //                                         'blood_group' => $blood_group,
    //                                         'weight' => $weight,
    //                                         'height' => $height,
    //                                         'organization' => $organization,
    //                                         'designation' => $designation,
    //                                         'allergies' => $allergies,
    //                                         'medical_conditions' => $medical_conditions,
    //                                         'id' => $AthleteId
    //                                     );

    //                                     DB::update($SQL, $Bindings);

    //                                     if (!empty($request->file('profile_pic'))) {
    //                                         $sSQL_img = 'UPDATE athletes SET profile_pic = :profile_pic WHERE id=:id';
    //                                         $Result = DB::update(
    //                                             $sSQL_img,
    //                                             array(
    //                                                 'profile_pic' => $profile_pic,
    //                                                 'id' => $AthleteId
    //                                             )
    //                                         );
    //                                     }

    //                                     $sql2 = 'SELECT * FROM athletes WHERE id=:id';
    //                                     $ResponseData = DB::select($sql2, array('id' => $AthleteId));

    //                                     foreach ($ResponseData as $value) {
    //                                         $value->barcode_image = (!empty($value->barcode_image)) ? env('ATHLETE_BARCODE_PATH') . $value->barcode_image . '' : "";

    //                                         $value->profile_pic = (!empty($value->profile_pic)) ? env('ATHLETE_PROFILE_PATH') . $value->profile_pic . '' : '';

    //                                         $value->id_proof_doc_upload = (!empty($value->id_proof_doc_upload)) ? env('ATHLETE_PROFILE_PATH') . $value->id_proof_doc_upload . '' : '';

    //                                         $value->address_proof_doc_upload = (!empty($value->address_proof_doc_upload)) ? env('ATHLETE_PROFILE_PATH') . $value->address_proof_doc_upload . '' : '';

    //                                         if ($value->gender == 'Male')
    //                                             $value->gender = 1;
    //                                         elseif ($value->gender == 'Female')
    //                                             $value->gender = 2;
    //                                         else
    //                                             $value->gender = 0;
    //                                     }
    //                                     //echo '<pre>'; print_r($ResponseData); die;

    //                                     $message = 'Personal Details updated successfully';
    //                                     $ResposneCode = 200;
    //                                 } else {
    //                                     #ELSE
    //                                     $ResposneCode = 400;
    //                                     $message = 'Athlete is exist with same credentials';
    //                                 }
    //                             } else {
    //                                 $ResposneCode = 400;
    //                                 $message = $filename . ' file is in invalid format';
    //                             }
    //                         } else {
    //                             $ResposneCode = 400;
    //                             $message = 'Invalid mobile number format';
    //                         }
    //                     } else {
    //                         $ResposneCode = 400;
    //                         $message = 'Invalid email format';
    //                     }
    //                 } else {
    //                     $ResposneCode = 400;
    //                     $message = 'Invalid lastname format';
    //                 }
    //             } else {
    //                 $ResposneCode = 400;
    //                 $message = 'Invalid firstname format';
    //             }
    //         } else {
    //             $ResposneCode = 400;
    //             $message = $field . ' is empty';
    //         }



    //         $ResposneCode = 200;
    //         $message = 'Request processed successfully';

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

    public function editProfile(Request $request)
    {
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $empty = false;
        $aToken = app('App\Http\Controllers\LoginController')->validate_request($request);
        // dd($aToken['data']->ID);

        if ($aToken['code'] == 200) {
            $aPost = $request->all();
            $Auth = new Authenticate();
            $Auth->apiLog($request);

            $sSQL = 'SELECT * FROM Users Where id =:Id';
            $userData = DB::select(
                $sSQL,
                array(
                    'Id' => $aToken['data']->ID
                )
            );

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

            if (!$empty) {
                $UserId = $aToken['data']->ID;

                if (preg_match("/^(?=.*?[a-z])(?=.*?[0-9]).{8,20}$/", $aPost['password'])) {
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

                                            // //NEW UPDATED CODE 11 JAN 24 BY SHARDA FOR RESIZE IMAGE
                                            // $Path = public_path('uploads/profile_images/');
                                            // $logo_image = $request->file('profile_pic');

                                            // $ImageExtension = $logo_image->getClientOriginalExtension();
                                            // $image = strtotime('now') . '_profile.' . $ImageExtension;

                                            // // Save the original image without orientation metadata
                                            // $originalImagePath = $Path . $image;
                                            // $logo_image = Image::make($logo_image->getRealPath());
                                            // $logo_image->orientate()->save($originalImagePath);

                                            // Create and save the thumbnail
                                            //$thumbnailPath = public_path('thumbnail') . '/' . $image;
                                            // Image::make($originalImagePath)
                                            //     ->resize(260, 260, function ($constraint) {
                                            //         $constraint->aspectRatio();
                                            //     });
                                            //->save($thumbnailPath);

                                            // Move the original image to its destination
                                            //$destinationPath = public_path('uploads/profile_images/');
                                            // $logo_image->move($destinationPath, $image);
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
                                        // dd($image);

                                        $Exist = array();
                                        $mobile = $aPost['mobile'];
                                        $email = $aPost['email'];
                                        $dob = date('Y-m-d', strtotime($aPost['dob']));

                                        #CHECK IF SAME EMAIL OR MOBILE USER EXIST OR NOT
                                        $SQL2 = 'SELECT id FROM Users WHERE (mobile=:mobile OR
                                        email=:email) AND NOT id =:id';
                                        $Exist = DB::select($SQL2, array('mobile' => $mobile, 'email' => $email, 'id' => $UserId));

                                        // dd(sizeof($Exist));

                                        if (sizeof($Exist) == 0) {

                                            $barcode_number = (!empty($request->barcode_number)) ? $request->barcode_number : 0;
                                            $type = (!empty($request->type)) ? $request->type : '';
                                            $athleteid_request = (!empty($request->athleteid_request)) ? $request->athleteid_request : 0;
                                            $athleteid_request_datetime = (!empty($request->athleteid_request_datetime)) ? $request->athleteid_request_datetime : 0;
                                            $dob = (!empty($request->dob)) ? $request->dob : '';
                                            $gender = (!empty($request->gender)) ? $request->gender : '';
                                            $emergency_contact_person = (!empty($request->emergency_contact_person)) ? $request->emergency_contact_person : '';
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


                                            $SQL = 'UPDATE Users SET
                                            barcode_number= :barcode_number,
                                            type= :type,
                                            athleteid_request= :athleteid_request,
                                            athleteid_request_datetime= :athleteid_request_datetime,
                                            count_of_eligibility= :count_of_eligibility,
                                            otp= :otp,
                                            firstname= :firstname,
                                            lastname= :lastname,
                                            email= :email,
                                            password= :password,
                                            mobile=:mobile,
                                            dob=:dob,
                                            gender= :gender,
                                            emergency_contact_person= :emergency_contact_person,
                                            t_shirt_size= :t_shirt_size,
                                            blood_group= :blood_group,
                                            weight= :weight,
                                            height= :height,
                                            -- profile_pic= :profile_pic,
                                            organization= :organization,
                                            designation= :designation,
                                            address1= :address1,
                                            address2= :address2,
                                            city= :city,
                                            state= :state,
                                            country= :country,
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
                                            -- is_active= :is_active,
                                            -- is_deleted= :is_deleted,
                                            created_at= :created_at,
                                            -- is_login= :is_login,
                                            -- auth_token= :auth_token,
                                            -- login_time= :login_time,
                                            -- UDB= :UDB,
                                            allergies= :allergies,
                                            medical_conditions= :medical_conditions,
                                            -- cover_picture= :cover_picture,
                                            Timezone= :Timezone
                                            WHERE id=:id';
                                            $Bindings = array(
                                                'barcode_number' => $barcode_number,
                                                'type' => $type,
                                                'athleteid_request' => $athleteid_request,
                                                'athleteid_request_datetime' => $athleteid_request_datetime,
                                                'count_of_eligibility' => $request->count_of_eligibility,
                                                'otp' => $request->otp,
                                                'firstname' => $request->firstname,
                                                'lastname' => $request->lastname,
                                                'email' => $request->email,
                                                'password' => md5($request->password),
                                                'mobile' => $request->mobile,
                                                'dob' => $dob,
                                                'gender' => $gender,
                                                'emergency_contact_person' => $emergency_contact_person,
                                                't_shirt_size' => $t_shirt_size,
                                                'blood_group' => $blood_group,
                                                'weight' => $weight,
                                                'height' => $height,
                                                // 'profile_pic'=> $request->profile_pic,
                                                'organization' => $organization,
                                                'designation' => $designation,
                                                'address1' => $address1,
                                                'address2' => $address2,
                                                'city' => $city,
                                                'state' => $state,
                                                'country' => $country,
                                                'address_check' => $request->address_check,
                                                'ca_address1' => $request->ca_address1,
                                                'ca_address2' => $request->ca_address2,
                                                'ca_city' => $request->ca_city,
                                                'ca_state' => $request->ca_state,
                                                'ca_pincode' => $request->ca_pincode,
                                                'ca_country' => $request->ca_country,
                                                'id_proof_type' => $request->id_proof_type,
                                                'id_proof_no' => $request->id_proof_no,
                                                'id_proof_doc_upload' => $request->id_proof_doc_upload,
                                                'nationality' => $request->nationality,
                                                'address_proof_type' => $request->address_proof_type,
                                                'address_proof_no' => $request->address_proof_no,
                                                'address_proof_doc_upload' => $request->address_proof_doc_upload,
                                                'device_token' => $request->device_token,
                                                // 'is_active'=> $request->is_active,
                                                // 'is_deleted'=> 0,
                                                'created_at' => strtotime('now'),
                                                // 'is_login'=> $request->is_login,
                                                // 'auth_token' => $request->auth_token,
                                                // 'login_time'=> $request->login_time,
                                                // 'UDB'=> $request->UDB,
                                                'allergies' => $request->allergies,
                                                'medical_conditions' => $request->medical_conditions,
                                                // 'cover_picture'=> $request->cover_picture,
                                                'Timezone' => $request->Timezone,
                                                'id' => $UserId
                                            );
                                            // dd( $Bindings);
                                            DB::update($SQL, $Bindings);

                                            if (!empty($request->file('profile_pic'))) {
                                                $sSQL_img = 'UPDATE Users SET profile_pic = :profile_pic WHERE id=:id';
                                                $Result = DB::update(
                                                    $sSQL_img,
                                                    array(
                                                        'profile_pic' => $profile_pic,
                                                        'id' => $UserId
                                                    )
                                                );
                                            }
                                            if (!empty($request->file('cover_picture'))) {
                                                $sSQL_img = 'UPDATE Users SET cover_picture = :cover_picture WHERE id=:id';
                                                $Result = DB::update(
                                                    $sSQL_img,
                                                    array(
                                                        'cover_picture' => $cover_picture,
                                                        'id' => $UserId
                                                    )
                                                );
                                            }

                                            $sql2 = 'SELECT * FROM Users WHERE id=:id';
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
                } else {
                    $ResposneCode = 400;
                    $message = 'Password should be 8-20 characters in length';
                }
            } else {

                $ResposneCode = 400;
                $message = $field . ' is empty';
                // dd($message);
            }



            // $ResposneCode = 200;
            // $message = 'Request processed successfully';

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
        $aToken = app('App\Http\Controllers\LoginController')->validate_request($request);

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
                            $SQL1 = 'SELECT id FROM Users WHERE (email=:email OR mobile=:mobile) AND is_deleted = 0';
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

                                $SQL2 = 'INSERT INTO Users (firstname,lastname,email,mobile,created_at) VALUES(:firstname,:lastname,:email,:mobile,:created_at)';
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

}
