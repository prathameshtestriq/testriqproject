<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use GuzzleHttp\Client;

class GoogleLoginController extends Controller
{
    public function redirectToGoogle()
    {
        // dd(route('auth.google.callback'));
        $query = http_build_query([
            'client_id' => "300373314303-h4p19miaa7u1nsr8li4qqkdva8vobf3b.apps.googleusercontent.com",
            'redirect_uri' => route('auth.google.callback'),
            'response_type' => 'code',
            'scope' => 'email',
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

        // dd($accessToken);//ya29.a0Ad52N3_Dhe8XH4tCcwebJmmh4yi8C5DTDEbTLhjlmLUBN5CTV2teCuPXv7EzKOblnsGtps_sNTexv1Gif-keh3JTQEvyuZ7KDz_2rdJuA40J0m3-AsXgon0YePOVaSNeJ77DFbKEuG7R6st51oxa6RD23Uacr_HIAAaCgYKAYASARASFQHGX2MiX8S9LXv90w2ZnavnB5GsPg0169

        // Use $accessToken to fetch user data from Google APIs

        // Example:
        $user = $this->fetchGoogleUserData($accessToken);
        dd($user);
        // Once you have user data, you can handle user authentication or registration
    }


    function fetchGoogleUserData($accessToken)
    {
        // Construct the HTTP client
        $client = new Client();

        // try {
        // Make a GET request to the UserInfo endpoint
        $response = $client->get('https://openidconnect.googleapis.com/v1/userinfo', [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept' => 'application/json',
            ],
        ]);

        // Parse the JSON response
        $userData = json_decode($response->getBody(), true);
        dd($accessToken,$userData);
        // Return the user data
        return $userData;
        // } catch (Exception $e) {
        //     // Handle any errors or exceptions
        //     return null;
        // }
    }



}
