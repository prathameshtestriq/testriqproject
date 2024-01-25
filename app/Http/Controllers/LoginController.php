<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use App\Http\Controllers\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Libraries\Authenticate;
use App\Libraries\SmsApis;
use App\Libraries\Emails;

class LoginController extends Controller
{

    public function validate_request($request)
    {
        // dd($request);
        if (empty($request->header('Authorization'))) {
            $resp['code'] = 401;
            $resp['message'] = 'Authorization missing';
            return $resp;
        }

        $Auth = new Authenticate();
        return $Auth->authenticate_token($request->header('Authorization'));
    }

    public function signup(Request $request)
    {
        // dd('signup');
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
        if (empty($aPost['email'])) {
            $empty = true;
            $field = 'Email Id';
        }
        // $aPost['dob'] = "22/04/1991";
        if (empty($aPost['dob'])) {
            $empty = true;
            $field = 'Birth Date';
        }
        if (empty($aPost['gender'])) {
            $empty = true;
            $field = 'Gender';
        }
        if (empty($aPost['password'])) {
            $empty = true;
            $field = 'Password';
        }
        if (empty($aPost['confirm_password'])) {
            $empty = true;
            $field = 'Confirm Password';
        }
        if (!$empty) {
            $Auth = new Authenticate();
            $Auth->apiLog($request);
            // dd($aPost);

            if (preg_match("/^(?=.*?[a-z])(?=.*?[0-9]).{8,20}$/", $aPost['password'])) {
                if ($aPost['password'] == $aPost['confirm_password']) {
                    if (preg_match("/^[a-zA-Z-' ]*$/", $aPost['firstname'])) {
                        if (preg_match("/^[a-zA-Z-' ]*$/", $aPost['lastname'])) {
                            if (filter_var($aPost['email'], FILTER_VALIDATE_EMAIL)) {
                                if (preg_match('/^[0-9]{10}+$/', $aPost['mobile'])) {

                                    #EXIST CHECK
                                    $SQL1 = 'SELECT id FROM Users WHERE (email=:email OR mobile=:mobile) AND is_deleted = 0';
                                    $Exist = DB::select($SQL1, array('email' => $aPost['email'], 'mobile' => $aPost['mobile']));

                                    if (sizeof($Exist) == 0) {

                                        $Binding = array(
                                            'firstname' => $aPost['firstname'],
                                            'lastname' => $aPost['lastname'],
                                            'mobile' => $aPost['mobile'],
                                            'email' => $aPost['email'],
                                            'dob' => date('Y-m-d', strtotime($aPost['dob'])),
                                            'gender' => $aPost['gender'],
                                            'password' => md5($aPost['password']),
                                            'created_at' => strtotime('now')
                                        );

                                        $SQL2 = 'INSERT INTO Users (firstname,lastname,mobile,email,dob,gender,password,created_at) VALUES(:firstname,:lastname,:mobile,:email,:dob,:gender,:password,:created_at)';
                                        DB::select($SQL2, $Binding);

                                        $lastInsertedId = DB::getPdo()->lastInsertId();

                                        $otp = rand(1000, 9999);
                                        if (!empty($otp)) {
                                            $data = DB::table('Users')->where('id', $lastInsertedId)->update(['otp' => $otp]);

                                            #SEND OTP MESSAGE
                                            $SmsObj = new SmsApis();
                                            $SmsObj->post_sms($aPost['mobile'], $otp);

                                            #SEND OTP MAIL
                                            $Email = new Emails();
                                            $Email->post_email($aPost['email'], $otp);
                                            // Error: Class &quot;SendGrid\Mail\Mail&quot; not found in file /var/www/html/RacesWeb/app/Libraries/Emails.php on line 11
                                        }

                                        $SQL3 = 'SELECT * FROM Users WHERE id =:id';
                                        $aResult = DB::select($SQL3, array('id' => $lastInsertedId));

                                        $aToken['ID'] = $aResult[0]->id;
                                        // $aToken['mobile'] = $aResult[0]->mobile;
                                        $aToken['email'] = $aResult[0]->email;
                                        $aToken['dob'] = $aResult[0]->dob;

                                        $Auth = new Authenticate();
                                        $ResponseData['token'] = $Auth->create_token($aToken);

                                        foreach ($aResult as $value) {

                                            // $value->barcode_image = (!empty($value->barcode_image)) ? env('ATHLETE_BARCODE_PATH') . $value->barcode_image . '' : "";

                                            $value->profile_pic = (!empty($value->profile_pic)) ? env('ATHLETE_PROFILE_PATH') . $value->profile_pic . '' : '';

                                            $value->id_proof_doc_upload = (!empty($value->id_proof_doc_upload)) ? env('ATHLETE_PROFILE_PATH') . $value->id_proof_doc_upload . '' : '';

                                            $value->address_proof_doc_upload = (!empty($value->address_proof_doc_upload)) ? env('ATHLETE_PROFILE_PATH') . $value->address_proof_doc_upload . '' : '';


                                            // $value->dob = isset($value->dob) ? date("d-m-Y", strtotime($value->dob)) : "";
                                        }

                                        $ResponseData['userData'] = $aResult[0];
                                        // dd($ResponseData['details']);
                                        $SQL = 'UPDATE Users SET auth_token=:auth_token,login_time=:login_time,is_login = 1 WHERE id=:id';
                                        DB::update($SQL, array('id' => $aResult[0]->id, 'auth_token' => "Bearer " . $ResponseData['token'], 'login_time' => strtotime('now')));
                                        #MODULES OF USER ON BASIS OF TYPE
                                        // $Modules = Modules::whereIn('type', explode(',', $

                                        $ResposneCode = 200;
                                        $message = 'Registered Successfully';
                                    } else {
                                        $ResposneCode = 400;
                                        $message = 'User already exist, please sign in';
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
                    $message = 'Confirm Password is not same as Password';
                }
            } else {
                $ResposneCode = 400;
                $message = 'Password should be 8-20 characters in length';
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

    public function login(Request $request)
    {
        // dd('login');
        $aPost = $request->all();
        //dd($aPost);
        $Auth = new Authenticate();
        $Auth->apiLog($request);

        $ResponseData = [];
        $ResposneCode = 200;
        $empty = false;
        $message = 'Success';
        $field = '';

        if (empty($aPost['Email'])) {
            $empty = true;
            $field = 'Email';
        }
        if (empty($aPost['Password'])) {
            $empty = true;
            $field = 'Password';
        }

        if (!$empty) {
            // Check Multple Athlete Exists or not with same email or mobile?
            $sql1 = 'SELECT id FROM Users WHERE email = :email and is_deleted = 0';
            $oUser = DB::select($sql1, array('email' => $aPost['Email']));
            // dd(count($oUser));
            if (count($oUser) == 1) {
                $sql2 = 'SELECT * FROM Users WHERE id=:id AND password =:password';
                $aResult = DB::select($sql2, array('id' => $oUser[0]->id, 'password' => md5($aPost['Password'])));
                // dd($aResult);
                if ($aResult) {
                    if ($aResult[0]->is_deleted == 0) {
                        if ($aResult[0]->is_active == 1) {
                            $aToken['ID'] = $aResult[0]->id;
                            // $aToken['mobile'] = $aResult[0]->mobile;
                            $aToken['email'] = $aResult[0]->email;
                            $aToken['dob'] = $aResult[0]->dob;

                            $Auth = new Authenticate();
                            $ResponseData['token'] = $Auth->create_token($aToken);

                            foreach ($aResult as $value) {

                                // $value->barcode_image = (!empty($value->barcode_image)) ? env('ATHLETE_BARCODE_PATH') . $value->barcode_image . '' : "";

                                $value->profile_pic = (!empty($value->profile_pic)) ? env('ATHLETE_PROFILE_PATH') . $value->profile_pic . '' : '';

                                $value->id_proof_doc_upload = (!empty($value->id_proof_doc_upload)) ? env('ATHLETE_PROFILE_PATH') . $value->id_proof_doc_upload . '' : '';

                                $value->address_proof_doc_upload = (!empty($value->address_proof_doc_upload)) ? env('ATHLETE_PROFILE_PATH') . $value->address_proof_doc_upload . '' : '';


                                // $value->dob = isset($value->dob) ? date("d-m-Y", strtotime($value->dob)) : "";
                            }

                            $ResponseData['userData'] = $aResult[0];
                            // dd($ResponseData['details']);
                            $SQL = 'UPDATE Users SET auth_token=:auth_token,login_time=:login_time,is_login = 1 WHERE id=:id';
                            DB::update($SQL, array('id' => $aResult[0]->id, 'auth_token' => "Bearer " . $ResponseData['token'], 'login_time' => strtotime('now')));

                            $message = 'Login Successfully';
                            $ResposneCode = 200;
                        } else {
                            $ResposneCode = 400;
                            $message = 'Account deactivated';
                        }
                    } else {
                        $ResposneCode = 400;
                        $message = 'Account is deleted';
                    }
                } else {
                    $ResposneCode = 400;
                    $message = 'Password is wrong';
                }
            } else if (count($oUser) > 1) {
                $ResposneCode = 401;
                $message = 'Multiple users registered with the same email ID and mobile number kindly sign up again';
                /*foreach ($oUser as $user) {
                    $SQL = 'UPDATE athletes SET is_deleted = 1 WHERE id=:id';
                    DB::update($SQL, array('id' => $user->id));
                } */
            } else {
                $ResposneCode = 400;
                $message = 'Account is deleted';
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


    public function GoogleSignUp(Request $request)
    {
        // dd('signup');
        $aPost = $request->all();

        $ResponseData = [];
        $ResposneCode = 200;
        $empty = false;
        $message = 'Success';
        $field = '';

        if (empty($aPost['email'])) {
            $empty = true;
            $field = 'Email Id';
        }
        if (empty($aPost['password'])) {
            $empty = true;
            $field = 'Password';
        }
        if (!$empty) {
            $Auth = new Authenticate();
            $Auth->apiLog($request);
            // dd($aPost);
            if (filter_var($aPost['email'], FILTER_VALIDATE_EMAIL)) {
                if (strlen($aPost['password']) >= 5) {
                    #EXIST CHECK
                    $SQL1 = 'SELECT id FROM Users WHERE email=:email AND is_deleted = 0';
                    $Exist = DB::select($SQL1, array('email' => $aPost['email']));

                    if (sizeof($Exist) == 0) {
                        $Binding = array(
                            'password' => $aPost['password'],
                            'email' => $aPost['email'],
                            'created_at' => strtotime('now')
                        );

                        $SQL2 = 'INSERT INTO Users (password,email,created_at) VALUES(:password,:email,:created_at)';
                        DB::select($SQL2, $Binding);

                        $UserId = DB::getPdo()->lastInsertId();

                        $SQL3 = 'SELECT * FROM Users WHERE id=:id';
                        $aResult = DB::select($SQL3, array('id' => $UserId));
                        //-----------------------------------------------------------------------------------------------

                        if ($aResult) {
                            if ($aResult[0]->is_deleted == 0) {
                                if ($aResult[0]->is_active == 1) {
                                    $aToken['ID'] = $aResult[0]->id;
                                    $aToken['mobile'] = $aResult[0]->mobile;
                                    $aToken['email'] = $aResult[0]->email;
                                    $aToken['dob'] = $aResult[0]->dob;

                                    $Auth = new Authenticate();
                                    $ResponseData['token'] = $Auth->create_token($aToken);
                                    foreach ($aResult as $value) {
                                        // $value->barcode_image = (!empty($value->barcode_image)) ? env('ATHLETE_BARCODE_PATH') . $value->barcode_image . '' : "";

                                        $value->profile_pic = (!empty($value->profile_pic)) ? env('ATHLETE_PROFILE_PATH') . $value->profile_pic . '' : '';

                                        // $value->dob = isset($value->dob) ? date("d-m-Y", strtotime($value->dob)) : "";
                                    }
                                    $ResponseData['details'] = $aResult[0];
                                    // dd($ResponseData['details']);
                                    $SQL = 'UPDATE Users SET auth_token=:auth_token,login_time=:login_time,is_login = 1 WHERE id=:id';
                                    DB::update($SQL, array('id' => $aResult[0]->id, 'auth_token' => "Bearer " . $ResponseData['token'], 'login_time' => strtotime('now')));

                                    $message = 'Login Successfully';
                                    $ResposneCode = 200;
                                } else {
                                    $ResposneCode = 400;
                                    $message = 'Account deactivated';
                                }
                            } else {
                                $ResposneCode = 400;
                                $message = 'User is deleted';
                            }
                        } else {
                            $ResposneCode = 400;
                            $message = 'Invalid login details';
                        }
                        // ----------------------------------------------------------------------------------------------
                    } else if (sizeof($Exist) == 1) {
                        $UserId = $Exist[0]->id;
                        $SQL3 = 'SELECT * FROM Users WHERE id=:id';
                        $aResult = DB::select($SQL3, array('id' => $UserId));

                        if ($aResult) {
                            if ($aResult[0]->is_deleted == 0) {
                                if ($aResult[0]->is_active == 1) {
                                    $aToken['ID'] = $aResult[0]->id;
                                    $aToken['mobile'] = $aResult[0]->mobile;
                                    $aToken['email'] = $aResult[0]->email;
                                    $aToken['dob'] = $aResult[0]->dob;

                                    $Auth = new Authenticate();
                                    $ResponseData['token'] = $Auth->create_token($aToken);

                                    foreach ($aResult as $value) {
                                        //$value->barcode_image = (!empty($value->barcode_image)) ? env('ATHLETE_BARCODE_PATH') . $value->barcode_image . '' : "";

                                        $value->profile_pic = (!empty($value->profile_pic)) ? env('ATHLETE_PROFILE_PATH') . $value->profile_pic . '' : '';

                                        $value->id_proof_doc_upload = (!empty($value->id_proof_doc_upload)) ? env('ATHLETE_PROFILE_PATH') . $value->id_proof_doc_upload . '' : '';

                                        $value->address_proof_doc_upload = (!empty($value->address_proof_doc_upload)) ? env('ATHLETE_PROFILE_PATH') . $value->address_proof_doc_upload . '' : '';

                                        // $value->dob = isset($value->dob) ? date("d-m-Y", strtotime($value->dob)) : "";
                                    }

                                    $ResponseData['details'] = $aResult[0];
                                    // dd($ResponseData['details']);
                                    $SQL = 'UPDATE Users SET auth_token=:auth_token,login_time=:login_time,is_login = 1 WHERE id=:id';
                                    DB::update($SQL, array('id' => $aResult[0]->id, 'auth_token' => "Bearer " . $ResponseData['token'], 'login_time' => strtotime('now')));

                                    $message = 'Login Successfully';
                                    $ResposneCode = 200;
                                } else {
                                    $ResposneCode = 400;
                                    $message = 'Account deactivated';
                                }
                            } else {
                                $ResposneCode = 400;
                                $message = 'User is deleted';
                            }
                        } else {
                            $ResposneCode = 400;
                            $message = 'Invalid login details';
                        }
                    } else if (sizeof($Exist) > 1) {
                        $ResposneCode = 401;
                        $message = 'Multiple users are registered with this email address, kindly sign up again';
                    } else {
                        $ResposneCode = 400;
                        $message = 'User is already exist';
                    }
                } else {
                    $ResposneCode = 400;
                    $message = 'Password should be at least 5 characters in length';
                }
            } else {
                $ResposneCode = 400;
                $message = 'Invalid email format';
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

    public function logout(Request $request)
    {
        // dd($request);
        $aPost = $request->all();

        $Auth = new Authenticate();
        $Auth->apiLog($request);

        $ResponseData = [];
        $ResposneCode = 200;
        $empty = false;
        $message = 'Success';
        $field = '';

        $aToken = $this->validate_request($request);

        if ($aToken['code'] == 200) {
            $aPost = $request->all();

            $Auth = new Authenticate();
            // if (empty($aPost['event'])) {
            //     $empty = true;
            //     $field = 'Event Id';
            // }

            if (!$empty) {
                $UserId = $aToken['data']->ID;
                // $Event = $aPost['event'];

                $sql = 'UPDATE Users SET auth_token=:auth_token WHERE id=:id';
                DB::select($sql, ['id' => $UserId, 'auth_token' => '']);
                $message = 'Logout Successfully';
            } else {
                $ResposneCode = 400;
                $message = $field . ' is empty';
            }

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

    public function forgot_password(Request $request)
    {
        // dd('signup');
        $aPost = $request->all();

        $ResponseData = [];
        $ResposneCode = 200;
        $empty = false;
        $message = 'Success';
        $field = '';

        if (empty($aPost['email'])) {
            $empty = true;
            $field = 'Email Id';
        }
        if (!$empty) {
            $Auth = new Authenticate();
            $Auth->apiLog($request);
            // dd($aPost);
            if (filter_var($aPost['email'], FILTER_VALIDATE_EMAIL)) {

                #EXIST CHECK
                $SQL1 = 'SELECT id,password FROM Users WHERE email=:email AND is_deleted = 0';
                $Exist = DB::select($SQL1, array('email' => $aPost['email']));

                if (sizeof($Exist) > 0) {

                    #GENERATE NEW PWD AND STORE INTO DB
                    // $randomPassword = $this->randomPassword();
                    // DB::table('Users')->where('id', $Exist[0]->id)->update(['password' => md5($randomPassword)]);
                    // dd($randomPassword);
                    #############################################

                    #SEND PASSWORD MAIL
                    $Email = new Emails();
                    $Email->post_email_pwd($aPost['email'], $Exist[0]->password);
                    // Error: Class &quot;SendGrid\Mail\Mail&quot; not found in file /var/www/html/RacesWeb/app/Libraries/Emails.php on line 11

                    $SQL3 = 'SELECT * FROM Users WHERE id =:id';
                    $ResponseData['userData'] = DB::select($SQL3, array('id' => $Exist[0]->id));

                    #MODULES OF USER ON BASIS OF TYPE
                    // $Modules = Modules::whereIn('type', explode(',', $

                    $ResposneCode = 200;
                    $message = 'Registered Successfully';
                } else {
                    $ResposneCode = 400;
                    $message = 'User is not exist, please sign in';
                }

            } else {
                $ResposneCode = 400;
                $message = 'Invalid email format';
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


    function randomPassword()
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 5; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }


    public function db_backup()
    {
        // Database type (mysql or pgsql)
        $databaseType = 'mysql';
        // Backup file name
        $backupFileName = 'Races2.0' . date('Y_m_d_H_i_s') . '.sql';
        // Path to store the backup file
        $backupPath = storage_path("app/Laravel/{$backupFileName}");

        $DB_USERNAME = 'root'; //env('DB_USERNAME');
        $DB_PASSWORD = '123456@Swt'; //env('DB_PASSWORD');
        $DB_DATABASE = 'Races2.0_Web'; //env('DB_DATABASE');
        //dd($DB_USERNAME,$DB_PASSWORD,$DB_DATABASE);
        // Generate the database backup using mysqldump or pg_dump
        if ($databaseType === 'mysql') {
            // exec("mysqldump -u root -p'123456@Swt' ytcr_admin > {$backupPath}");
            exec("mysqldump -u {$DB_USERNAME} -p{$DB_PASSWORD} {$DB_DATABASE} > {$backupPath}");
        } elseif ($databaseType === 'pgsql') {
            exec("pg_dump -U your_db_user your_database > {$backupPath}");
        }

        // Provide the download link for the generated backup file
        return response()->download($backupPath, $backupFileName)->deleteFileAfterSend(true);
    }


    public function send_reset_password_link(Request $request)
    {
        $aPost = $request->all();
        $ResponseData = [];
        $ResposneCode = 200;
        $success = 1;
        $empty = false;
        $message = 'Success';
        $field = '';

        if (empty($aPost['email'])) {
            $empty = true;
            $field = 'Email';
            $field = 'email';
        }

        if (!$empty) {
            $Auth = new Authenticate();
            $Auth->apiLog($request);

            $s_sql = 'SELECT id, email FROM Users WHERE is_deleted = 0 AND email = :email';
            $a_customer = DB::select($s_sql, array($aPost['email']));
            // dd($a_customer);

            if (!empty($a_customer[0])) {
                $ResponseData['token'] = bin2hex(random_bytes(16));
                $s_sql = 'UPDATE Users SET ResetPasswordToken = :ResetPasswordToken
                WHERE is_deleted = 0 AND email =:email';

                $Bindings = array(
                    'ResetPasswordToken' => $ResponseData['token'],
                    'email' => $aPost['email']
                );

                DB::update($s_sql, $Bindings);
                $BASE_URL = env('BASE_URL');
                $reset_link = config('custom.reset_password_link');
                $link = $BASE_URL . $reset_link . $ResponseData['token'];

                #SEND OTP MAIL
                $Email = new Emails();
                $Email->send_reset_password_link($aPost['email'], $link);

                $ResposneCode = 200;
                $message = 'Reset password link has been sent to provided email address. Please click on link to reset your password.';
                $success = 1;
            } else {
                $success = 0;
                $ResposneCode = 400;
                $field = 'email';
                $message = 'Customer not found having provided eamil. Please try with valid email.';
            }
        } else {
            $success = 0;
            $ResposneCode = 400;
            $message = $field . ' is empty';
        }

        // $response = [
        //     'success' => $success,
        //     'data' => $ResponseData,
        //     'message' => $message,
        //     'field' => $field
        // ];

        // return response()->json($response, $ResposneCode);

        $response = [
            'status' => $ResposneCode,
            'data' => $ResponseData,
            'message' => $message
        ];

        return response()->json($response, $ResposneCode);
    }

    public function reset_password(Request $request, $token)
    {
        $aPost = $request->all();
        $success = 1;
        $ResponseData = [];
        $ResposneCode = 200;
        $empty = false;
        $message = 'Password reset successfully.';
        $field = '';

        if (empty($aPost['new_password'])) {
            $empty = true;
            $field = 'Password';
            $field = 'new_password';
        }

        if (empty($aPost['confirm_new_password'])) {
            $empty = true;
            $field = 'Confirm Password';
            $field = 'confirm_new_password';
        }

        if (!$empty) {

            $Auth = new Authenticate();
            $Auth->apiLog($request);

            if (preg_match("/^(?=.*?[a-z])(?=.*?[0-9]).{8,20}$/", $aPost['new_password'])) {
                if ($aPost['confirm_new_password'] == $aPost['new_password']) {

                    $s_sql = 'SELECT id FROM Users WHERE ResetPasswordToken =:ResetPasswordToken AND is_deleted = 0';
                    $Result = DB::select($s_sql, array('ResetPasswordToken' => $token));

                    if (!empty($Result[0])) {
                        $s_sql = 'UPDATE Users SET password =:Password, ResetPasswordToken="" WHERE id = :id';

                        $Bindings = array(
                            'Password' => md5($aPost['new_password']),
                            'id' => $Result[0]->id
                        );
                        // dd($Bindings);
                        $Result = DB::update($s_sql, $Bindings);
                        $ResposneCode = 200;
                        $success = 1;
                    } else {
                        $success = 0;
                        $ResposneCode = 400;
                        $message = 'Token not found';
                        $field = "";
                    }
                    // }
                } else {
                    $ResposneCode = 400;
                    $message = 'Confirm Password is not same as Password';
                }
            } else {
                $ResposneCode = 400;
                $message = 'Password should be 8-20 characters in length ,atleast one letter and one number';
            }
        } else {
            $success = 0;
            $ResposneCode = 400;
            $message = $field . ' is empty';
        }

        $response = [
            'success' => $success,
            'data' => $ResponseData,
            'message' => $message,
            'field' => $field
        ];

        return response()->json($response, $ResposneCode);
    }



}
