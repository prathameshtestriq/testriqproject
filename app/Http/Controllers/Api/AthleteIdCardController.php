<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Libraries\Authenticate;
// use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Libraries\Emails;

class AthleteIdCardController extends Controller
{
    public function athleteCardPreview(Request $request)
    {
        $ResponseData = [];
        $ResposneCode = 200;
        $empty = false;
        $message = "";
        $field = '';

        $aToken = app('App\Http\Controllers\Api\LoginController')->validate_request($request);
        // dd($aToken);

        if ($aToken['code'] == 200) {
            $aPost = $request->all();
            $Auth = new Authenticate();
            // dd($aPost['user_id']);
       
            $sql = "SELECT id,event_id FROM booking_payment_details Where created_by = ".$aPost['user_id'] . " GROUP BY event_id";
            $res = DB::select($sql,array());
          
            if(count($res) >= 3){
                    $SQL = 'SELECT * FROM users WHERE id=:id';
                    $oAthlete = DB::select($SQL, array('id' => $aPost['user_id']));
                    // dd($oAthlete);
                    if (sizeof($oAthlete) > 0) {
                            if ($oAthlete[0]->barcode_number == '') {
                                $barcode_number = rand(1000, 999999);
                                // Ensure the number is 12 digits long
                                $number = str_pad($barcode_number, 12, '0', STR_PAD_LEFT);
                                // $barcode = self::generateBarcode($number, $AthleteId);
                              
                                
                                $gender = isset($oAthlete[0]->gender) ? $oAthlete[0]->gender : 'male';
                                if (strtolower($gender) == 'male'){
                                    $gender_digit = 1;
                                }else{
                                    $gender_digit = 2;
                                }    

                                $dob = isset($oAthlete[0]->dob) ? date('d-m-Y',strtotime($oAthlete[0]->dob)) : '';
                                $dob_year = date("Y", strtotime($dob));
                               
                                $initial_six_digit = date('y') . '' . $gender_digit . '' . $dob_year;
                                
                                $six_digit_random_number = substr(number_format(time() * rand(), 0, '', ''), 0, 7);
                                $final_barcode_number = $initial_six_digit . '' . $six_digit_random_number;

                                // //$barcode = self::generateBarcode($final_barcode_number, $AthleteId);

                                $sql = 'UPDATE users SET barcode_number=:barcode_number WHERE id=:user_id';
                                DB::update($sql, array('barcode_number' => $final_barcode_number, 'user_id' => $aPost['user_id']));

                                $SQL = 'SELECT * FROM users WHERE id=:id';
                                $oAthlete = DB::select($SQL, array('id' => $aPost['user_id']));
                            } else {
                                $final_barcode_number = $oAthlete[0]->barcode_number;
                            }

                            foreach ($oAthlete as $value) { 
                                $value->blood_group = isset($value->blood_group) ? $value->blood_group : "";
                                $value->name = (!empty($value->firstname)) ? $value->firstname . " " . $value->lastname : "";
                                // $value->barcode_image = base64_encode(QrCode::format('svg')->size(300)->errorCorrection('H')->generate($final_barcode_number));
                                $value->barcode_image = base64_encode(QrCode::format('png')->size(300)->generate($final_barcode_number));
                                // dd( $value->barcode_image ); 
                                $value->profile_pic = (!empty($value->profile_pic)) ? url('/').'/uploads/profile_images/' . $value->profile_pic . '' : url('/').'uploads/images/customer.png';
                                $value->b_number = isset($value->barcode_number) ? $value->barcode_number : '';
                            }
                        // dd($oAthlete);
                    }else{
                        $message = 'No Data Found';
                        $ResposneCode = 200;
                    }

                    $pdf = PDF::loadView('pdf_athlete_card', compact('oAthlete'));
                    $pdf->setPaper('L', 'landscape');
                    $pdf->save(public_path('uploads/Athlete_pdfs/AthleteCard' . $aPost['user_id'] . '.pdf'));
                    $content = $pdf->download('AthleteCard' . $aPost['user_id'] . '.pdf')->getOriginalContent();
                    // $path = public_path('uploads/Athlete_pdfs/AthleteCard' . $aPost['user_id'] . '.pdf');
                    $path = url('/')."/uploads/Athlete_pdfs/AthleteCard".$aPost['user_id'].'.pdf';

                    $ResponseData['pdf_path'] = $path;
                    $message = 'Athlete Card downloaded successfully';

            }else{
                $message = 'Less than 3 events are available.';
                $ResposneCode = 200;
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

   
}
