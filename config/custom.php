<?php

// use Illuminate\Support\Facades\Facade;

$flag = 3;  // 1 - Local / 2 - Prime / 3 - Live 

if($flag == 1){
    $url1 = 'http://localhost:3000/in/Nashik/1/';
    $new_url = 'http://localhost:3000/in/Nashik/';
}else if($flag == 2){
    $url1 = 'https://swtprime.com/Races2.0_Frontend/in/Mumbai/1/';
    $new_url = 'https://swtprime.com/Races2.0_Frontend/in/Mumbai/';
}else if($flag == 3){
    $url1 = 'https://racesregistrations.com/in/Mumbai/1/';
    $new_url = 'https://racesregistrations.com/in/Mumbai/';
}else{
    $url1 = '';
    $new_url = '';
}

//----------------- admin panel used (state/city onchange api)
$url_flag = 3;  // 1 - Local / 2 - Prime / 3 - Live 

if($url_flag == 1){
    $base_url1 = 'http://127.0.0.1:8000';
}else if($url_flag == 2){
    $base_url1 = 'https://swtprime.com/races/public';
}else if($url_flag == 3){
    $base_url1 = 'https://racesregistrations.com/races/public';
}else{
    $base_url1 = '';
}

return [
    'ytcr_fee_percent' =>18,
    'platform_fee_percent' => 18, // previos 10 per set
    'payment_gateway_fee_percent' => 1,  // previos 10 per set
    'payment_gateway_gst_percent' => 18,  // new added

    'max_size' => 2097152,
    'page_title' => 'Races2.0 - ',
    'base_url' => env('APP_URL'),
    'show_banner_limit' => 10, // races front showing banner slider set limit

    'a_countries'=>array(
        '1'=>env('URL_INDIA'),
        '2'=>env('URL_BANGLADESH'),
        // '2'=>env('URL_PAK').'/api/uploaddata_new_farmers'
    ),

    // 'season' =>  env('CURRENT_SEASON', 'default_season_value'),
    'season' => env('CURRENT_SEASON'),

    'filter' => [
        (object)[
            "id" => 1,
            "filter_name" => "country",
            "active" => 1
        ],
        (object)[
            "id" => 2,
            "filter_name" => "state",
            "active" => 1
        ],
        (object)[
            "id" => 3,
            "filter_name" => "district",
            "active" => 1
        ],
        (object)[
            "id" => 4,
            "filter_name" => "block",
            "active" => 1
        ],
        (object)[
            "id" => 5,
            "filter_name" => "village",
            "active" => 1
        ]
        ],

        'option' => [
            (object)[
                "id" => 'project',
                "name" => "Project",
                "active" => 1
            ],
            (object)[
                "id" => 'control',
                "name" => "Control",
                "active" => 1
            ],
            ],


    'per_page' => (!empty(env('PER_PAGE'))) ? env('PER_PAGE') : 30,
    'master_types' => [
        (object)[
            "id" => 1,
            "type_name" => "auto_next",
            "active" => 1
        ],
        (object)[
            "id" => 2,
            "type_name" => "input",
            "active" => 1
        ],
        (object)[
            "id" => 3,
            "type_name" => "radio",
            "active" => 1
        ],
        (object)[
            "id" => 4,
            "type_name" => "selection",
            "active" => 1
        ],
        (object)[
            "id" => 5,
            "type_name" => "checkbox",
            "active" => 1
        ]
        ],

    /*language array*/
    'language'=>array(
        'en'=>'english',
        'hi'=>'hindi'
    ),
     # 2MB

    'merchant_key' => 'ozLEHc',  // payment details key
    'salt' => 'vvHOCdxxbkTXYASLCevSJ7iDkE8DRBT4',  // payment details salt

    'url_link' => $url1, // set url
    'send_email_url' => $new_url, // set url

    'last_login_days' => '+10 days', // last login days (use for logout purpose only)

    'app_url' => $base_url1, // used for admin panel onchange api call

];
