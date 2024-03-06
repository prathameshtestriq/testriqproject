<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Libraries\Authenticate;
use Illuminate\Support\Facades\DB;

class TestimonialController extends Controller
{
    public function GetTestimonial(Request $request){
        $ResponseData = [];
        $response['message'] = "";
        $ResposneCode = 400;

        $sSQL = 'SELECT * FROM testimonial ';
        $testimonial = DB::select($sSQL, array());
        // dd( $testimonial);
        foreach ($testimonial as $value) {
            $value->testimonial_img = (!empty($value->testimonial_img)) ? url('/').'/uploads/testimonial_image/' . $value->testimonial_img . '' : '';
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
}
