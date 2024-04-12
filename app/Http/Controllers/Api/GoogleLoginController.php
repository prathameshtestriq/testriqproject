<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Libraries\Authenticate;
use Illuminate\Support\Facades\DB;

class GoogleLoginController extends Controller
{
    public function redirectToGoogle()
    {
        // dd(route('auth.google.callback'));
        $query = http_build_query([
            'client_id' => "300373314303-h4p19miaa7u1nsr8li4qqkdva8vobf3b.apps.googleusercontent.com",
            'redirect_uri' => route('auth.google.callback'),
            'response_type' => 'code',
            'scope' => 'email profile openid',
        ]);
        // return $query;
        return redirect('https://accounts.google.com/o/oauth2/auth?' . $query);
    }

    public function handleGoogleCallback(Request $request)
    {
        // dd("here success");
        $client = new Client();

        $response = $client->post('https://oauth2.googleapis.com/token', [
            'form_params' => [
                'client_id' => "300373314303-h4p19miaa7u1nsr8li4qqkdva8vobf3b.apps.googleusercontent.com",
                'client_secret' => "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",
                'code' => $request->code,
                'grant_type' => 'authorization_code',
                'redirect_uri' => route('auth.google.callback'),
            ],
        ]);

        $accessToken = json_decode($response->getBody(), true)['access_token'];

        $user = $this->fetchGoogleUserData($accessToken);

        $ResponseData = [];
        $ResposneCode = 200;
        $empty = false;
        $message = 'Success';
        $field = '';

        if (!empty($user)) {
            $Auth = new Authenticate();
            $Auth->apiLog($request);

            #EXIST CHECK
            $SQL1 = 'SELECT id FROM users WHERE (email=:email) AND is_deleted = 0';
            $Exist = DB::select($SQL1, array('email' => $user['email']));

            if (sizeof($Exist) == 0) {
                // $user['name']
                // $user['sub'] => unique id

                $Binding = array(
                    'firstname' => $user['given_name'],
                    'lastname' => $user['family_name'],
                    'email' => $user['email'],
                    'is_google_login' => 1,
                    'google_login_id' => $user['sub'],
                    'created_at' => strtotime('now')
                );

                $SQL2 = 'INSERT INTO users (firstname,lastname,email,is_google_login,google_login_id,created_at) VALUES(:firstname,:lastname,:email,:is_google_login,:google_login_id,:created_at)';
                DB::select($SQL2, $Binding);

                $lastInsertedId = DB::getPdo()->lastInsertId();

                // echo "<pre>.lastInsertedId : "; print_r($lastInsertedId);

                $SQL3 = 'SELECT * FROM users WHERE id =:id';
                $aResult = DB::select($SQL3, array('id' => $lastInsertedId));

                $aToken['ID'] = $aResult[0]->id;
                $aToken['email'] = $aResult[0]->email;
                $aToken['dob'] = $aResult[0]->dob;

                $Auth = new Authenticate();
                $ResponseData['token'] = $Auth->create_token($aToken);

                foreach ($aResult as $value) {
                    $value->profile_pic = (!empty($value->profile_pic)) ? env('ATHLETE_PROFILE_PATH') . $value->profile_pic . '' : '';

                    $value->id_proof_doc_upload = (!empty($value->id_proof_doc_upload)) ? env('ATHLETE_PROFILE_PATH') . $value->id_proof_doc_upload . '' : '';

                    $value->address_proof_doc_upload = (!empty($value->address_proof_doc_upload)) ? env('ATHLETE_PROFILE_PATH') . $value->address_proof_doc_upload . '' : '';

                    $value->cover_picture = (!empty($value->cover_picture)) ? url('') . '/uploads/cover_photo/' . $value->cover_picture . '' : '';
                }

                $ResponseData['userData'] = $aResult[0];
                // dd($ResponseData['details']);
                $SQL = 'UPDATE users SET auth_token=:auth_token,login_time=:login_time,is_login = 1 WHERE id=:id';
                DB::update($SQL, array('id' => $aResult[0]->id, 'auth_token' => "Bearer " . $ResponseData['token'], 'login_time' => strtotime('now')));

                #MODULES OF USER ON BASIS OF ITS ROLE
                // $aModules = $this->admin_user_rights->get_user_modules($aResult[0]->id, $aResult[0]->type);
                // $ResponseData['modules'] = $aModules;

                $ResposneCode = 200;
                $message = 'Registered Successfully';
            } else {
                // $ResposneCode = 400;
                // $message = 'User already exist, please sign in';
                $sql1 = 'SELECT * FROM users WHERE id = :id';
                $aResult = DB::select($sql1, array('id' => $Exist[0]->id));

                if ($aResult[0]->is_active == 1) {
                    $aToken['ID'] = $aResult[0]->id;
                    $aToken['email'] = $aResult[0]->email;
                    $aToken['dob'] = $aResult[0]->dob;

                    $Auth = new Authenticate();
                    $ResponseData['token'] = $Auth->create_token($aToken);

                    foreach ($aResult as $value) {
                        $value->profile_pic = (!empty($value->profile_pic)) ? env('ATHLETE_PROFILE_PATH') . $value->profile_pic . '' : '';

                        $value->id_proof_doc_upload = (!empty($value->id_proof_doc_upload)) ? env('ATHLETE_PROFILE_PATH') . $value->id_proof_doc_upload . '' : '';

                        $value->address_proof_doc_upload = (!empty($value->address_proof_doc_upload)) ? env('ATHLETE_PROFILE_PATH') . $value->address_proof_doc_upload . '' : '';

                        $value->cover_picture = (!empty($value->cover_picture)) ? url('') . '/uploads/cover_photo/' . $value->cover_picture . '' : '';
                    }

                    $ResponseData['userData'] = $aResult[0];
                    // dd($ResponseData['details']);
                    $SQL = 'UPDATE users SET auth_token=:auth_token,login_time=:login_time,is_login = 1 WHERE id=:id';
                    DB::update($SQL, array('id' => $aResult[0]->id, 'auth_token' => "Bearer " . $ResponseData['token'], 'login_time' => strtotime('now')));

                    #MODULES OF USER ON BASIS OF ITS ROLE
                    // $aModules = $this->admin_user_rights->get_user_modules($aResult[0]->id, $aResult[0]->type);
                    // $ResponseData['modules'] = $aModules;

                    $message = 'Login Successfully';
                    $ResposneCode = 200;
                } else {
                    $ResposneCode = 400;
                    $message = 'Account deactivated';
                }
            }
        }

        $response = [
            'status' => $ResposneCode,
            'data' => $ResponseData,
            'message' => $message
        ];

        return response()->json($response, $ResposneCode);
    }


    function fetchGoogleUserData($accessToken)
    {
        $client = new Client();
        $response = $client->get('https://openidconnect.googleapis.com/v1/userinfo', [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept' => 'application/json',
            ],
        ]);
        $userData = json_decode($response->getBody(), true);
        return $userData;
    }






}
