<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Libraries\Authenticate;

// use function Laravel\Prompts\select;

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
                $value->profile_pic = (!empty($value->profile_pic)) ? url('') . '/uploads/profile_photo/' . $value->profile_pic . '' : '';
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

        }
        return response()->json($response, $ResposneCode);
    }

    public function editProfile(Request $request)
    {
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
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


                $AthleteId = $aToken['data']->ID;

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

                                if (!$is_valid) {
                                    $image = '';
                                    if (!empty($request->file('profile_pic'))) {

                                        //$Path = public_path('uploads/profile_images/');
                                        //$logo_image = $request->file('profile_pic');

                                        //$ImageExtention = $logo_image->getClientOriginalExtension(); #get proper by code;
                                        ///$image = strtotime('now') . '_profile.' . $ImageExtention;
                                        //$logo_image->move($Path, $image);

                                        //NEW UPDATED CODE 11 JAN 24 BY SHARDA FOR RESIZE IMAGE
                                        $Path = public_path('uploads/profile_images/');
                                        $logo_image = $request->file('profile_pic');

                                        $ImageExtension = $logo_image->getClientOriginalExtension();
                                        $image = strtotime('now') . '_profile.' . $ImageExtension;

                                        // Save the original image without orientation metadata
                                        $originalImagePath = $Path . $image;
                                        $logo_image = Image::make($logo_image->getRealPath());
                                        $logo_image->orientate()->save($originalImagePath);

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
                                    // dd($image);

                                    // $SQL = 'SELECT mobile,email,dob,UDB FROM athletes WHERE id=:id';
                                    // $userData = DB::select(DB::raw($SQL), array('id' => $AthleteId));

                                    // $UDB = $userData[0]->UDB;

                                    #NON-EDITABLE FIELDS
                                    $email = $userData[0]->email;
                                    $dob = $userData[0]->dob;
                                    $mobile = $userData[0]->mobile;

                                    $firstname = $aPost['firstname'];
                                    $lastname = $aPost['lastname'];
                                    $Exist = array();


                                    #UDB ATHLETE EDIT FIELDS
                                    if (!empty($UDB)) {
                                        $mobile = $aPost['mobile'];
                                        $email = $aPost['email'];
                                        $dob = date('Y-m-d', strtotime($aPost['dob']));

                                        #CHECK IF SAME EMAIL OR MOBILE USER EXIST OR NOT
                                        $SQL2 = 'SELECT id FROM athletes WHERE (mobile=:mobile OR
                                        email=:email) AND NOT id =:id';
                                        $Exist = DB::select(DB::raw($SQL2), array('mobile' => $mobile, 'email' => $email, 'id' => $AthleteId));

                                        //$SQL = 'UPDATE athletes SET UDB=:UDB WHERE id=:id';
                                        //DB::update(DB::raw($SQL), array('UDB'=> 0, 'id' => $AthleteId));
                                    }

                                    //dd($Exist);

                                    if (sizeof($Exist) == 0) {

                                        $SQL = 'UPDATE athletes SET UDB=:UDB WHERE id=:id';
                                        DB::update(DB::raw($SQL), array('UDB' => 0, 'id' => $AthleteId));

                                        $gender = isset($aPost['gender']) ? $aPost['gender'] : '';
                                        $emergency_contact_person = isset($aPost['emergency_contact_person']) ? $aPost['emergency_contact_person'] : '';
                                        $emergency_contact_no1 = isset($aPost['emergency_contact_no1']) ? $aPost['emergency_contact_no1'] : '';
                                        $t_shirt_size = isset($aPost['t_shirt_size']) ? $aPost['t_shirt_size'] : '';
                                        $blood_group = isset($aPost['blood_group']) ? $aPost['blood_group'] : '';
                                        $weight = isset($aPost['weight']) ? $aPost['weight'] : '';
                                        $height = isset($aPost['height']) ? $aPost['height'] : '';
                                        $profile_pic = $image;
                                        $organization = isset($aPost['organization']) ? $aPost['organization'] : '';
                                        $designation = isset($aPost['designation']) ? $aPost['designation'] : "";
                                        $allergies = isset($aPost['allergies']) ? $aPost['allergies'] : "";
                                        $medical_conditions = isset($aPost['medical_conditions']) ? $aPost['medical_conditions'] : "";

                                        $Gender = '';
                                        if ($gender == 1)
                                            $Gender = 'Male';
                                        if ($gender == 2)
                                            $Gender = 'Female';
                                        if ($gender == 3)
                                            $Gender = 'Other';

                                        $SQL = 'UPDATE athletes SET
                                            firstname=:firstname,
                                            lastname=:lastname,
                                            mobile=:mobile,
                                            email=:email,
                                            dob=:dob,
                                            gender=:gender,
                                            emergency_contact_person=:emergency_contact_person,
                                            emergency_contact_no1=:emergency_contact_no1,
                                            t_shirt_size=:t_shirt_size,
                                            blood_group=:blood_group,
                                            weight=:weight,
                                            height=:height,
                                            organization=:organization,
                                            designation=:designation,
                                            allergies=:allergies,
                                            medical_conditions=:medical_conditions
                                            WHERE
                                            id=:id';
                                        $Bindings = array(
                                            'firstname' => $firstname,
                                            'lastname' => $lastname,
                                            'mobile' => $mobile,
                                            'email' => $email,
                                            'dob' => $dob,
                                            'gender' => $Gender,
                                            'emergency_contact_person' => $emergency_contact_person,
                                            'emergency_contact_no1' => $emergency_contact_no1,
                                            't_shirt_size' => $t_shirt_size,
                                            'blood_group' => $blood_group,
                                            'weight' => $weight,
                                            'height' => $height,
                                            'organization' => $organization,
                                            'designation' => $designation,
                                            'allergies' => $allergies,
                                            'medical_conditions' => $medical_conditions,
                                            'id' => $AthleteId
                                        );

                                        DB::update($SQL, $Bindings);

                                        if (!empty($request->file('profile_pic'))) {
                                            $sSQL_img = 'UPDATE athletes SET profile_pic = :profile_pic WHERE id=:id';
                                            $Result = DB::update(
                                                $sSQL_img,
                                                array(
                                                    'profile_pic' => $profile_pic,
                                                    'id' => $AthleteId
                                                )
                                            );
                                        }

                                        $sql2 = 'SELECT * FROM athletes WHERE id=:id';
                                        $ResponseData = DB::select($sql2, array('id' => $AthleteId));

                                        foreach ($ResponseData as $value) {
                                            $value->barcode_image = (!empty($value->barcode_image)) ? env('ATHLETE_BARCODE_PATH') . $value->barcode_image . '' : "";

                                            $value->profile_pic = (!empty($value->profile_pic)) ? env('ATHLETE_PROFILE_PATH') . $value->profile_pic . '' : '';

                                            $value->id_proof_doc_upload = (!empty($value->id_proof_doc_upload)) ? env('ATHLETE_PROFILE_PATH') . $value->id_proof_doc_upload . '' : '';

                                            $value->address_proof_doc_upload = (!empty($value->address_proof_doc_upload)) ? env('ATHLETE_PROFILE_PATH') . $value->address_proof_doc_upload . '' : '';

                                            if ($value->gender == 'Male')
                                                $value->gender = 1;
                                            elseif ($value->gender == 'Female')
                                                $value->gender = 2;
                                            else
                                                $value->gender = 0;
                                        }
                                        //echo '<pre>'; print_r($ResponseData); die;

                                        $message = 'Personal Details updated successfully';
                                        $ResposneCode = 200;
                                    } else {
                                        #ELSE
                                        $ResposneCode = 400;
                                        $message = 'Athlete is exist with same credentials';
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
                $message = $field . ' is empty';
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

}
