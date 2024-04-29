<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Libraries\Authenticate;
use Illuminate\Support\Facades\DB;

class TestimonialController extends Controller
{
    public function GetTestimonial(Request $request)
    {
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;

        $sSQL = 'SELECT * FROM testimonial ';
        $testimonial = DB::select($sSQL, array());
        // dd( $testimonial);
        foreach ($testimonial as $value) {
            $value->testimonial_img = (!empty($value->testimonial_img)) ? url('/') . '/uploads/testimonial_image/' . $value->testimonial_img . '' : '';
        }

        if (!empty($testimonial)) {
            $ResponseData['testimonials'] = $testimonial;
        }
        // dd($ResponseData);
        $ResposneCode = 200;
        $message = 'Request processed successfully';


        $response = [
            'data' => $ResponseData,
            'message' => $message
        ];

        return response()->json($response, $ResposneCode);
    }

    public function AddSubscriber(Request $request)
    {
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;
        $empty = false;

        $aPost = $request->all();
        $Auth = new Authenticate();
        $Auth->apiLog($request);
        // $aPost['event_id'] = 48;
        if (empty($aPost['email'])) {
            $empty = true;
            $field = 'Email Id';
        }
        if (!$empty) {
            $Email = $aPost['email'];

            #CHECK EXIST SUBSCRIBER
            $checkSub = "SELECT id FROM `newsletter` WHERE `email`=:email";
            $resCheckSub = DB::select($checkSub,array("email"=>$Email));
            if (count($resCheckSub) > 0) {
                $message = 'This email is already subscribed. Please try another one!';
                $ResposneCode = 400;
            } else {
                $Sql = "INSERT INTO `newsletter` (email,created_at) VALUES (:email,:created_at);";
                DB::insert($Sql,array(
                    "email"=>$Email,
                    "created_at"=>strtotime("now")
                ));
                $ResposneCode = 200;
                $message = 'Thank you for your subscription!';
            }
        } else {
            $ResposneCode = 400;
            $message = $field . ' is empty';
        }

        $response = [
            'data' => $ResponseData,
            'status'=>$ResposneCode,
            'message' => $message
        ];

        return response()->json($response, $ResposneCode);
    }
}
