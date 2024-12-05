<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\EventCertificateModel;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

class EventCertificateController extends Controller
{
    public function clear_search()
    {
        session::forget('event_certificate_status');
        return redirect('/event_certificate');
    }

    public function index(Request $request)
    {

        $a_return = array();
        $a_return['search_event_certificate_status'] = '';

        if (isset($request->form_type) && $request->form_type == 'search_event_certificate') {
            session(['event_certificate_status' => $request->event_certificate_status]);
            return redirect('/event_certificate');
        }

        $event_certificate_status = session('event_certificate_status');
        $a_return['search_event_certificate_status'] = (isset($event_certificate_status) && $event_certificate_status != '') ? $event_certificate_status : '';
        
        $CountRows = EventCertificateModel::get_count($a_return);
        // dd($CountRows);
        $PageNo = request()->input('page', 1);
        $Limit = config('custom.per_page');
        // $Limit = 3;
        $a_return['Offset'] = ($PageNo - 1) * $Limit;

        $a_return["Event_certificate"] = EventCertificateModel::get_all($Limit, $a_return);
        // dd($a_return["Event_certificate"]);
        $a_return['Paginator'] = new LengthAwarePaginator($a_return['Event_certificate'], $CountRows, $Limit, $PageNo);
        $a_return['Paginator']->setPath(request()->url());

    
        return view('event_certificate.list', $a_return);
    }

    public function add_edit(Request $request, $id = 0)
    {
        $a_return[] = '';
        $a_return['id'] = $id;
        if (isset($request->form_type) && $request->form_type == 'add_edit_event_certificate') {
            if ($id > 0) {
                //EVENT CERTIFICATE PAGE
                $sSQL = 'SELECT id,image FROM email_certificates ecd WHERE id=:id';
                $a_return = DB::select($sSQL, array(
                    'id' => $id,
                ));
                $aReturn = (array) $a_return[0];
            }
         
            $rules = [
                'certificate_name' => 'required',
                'image' => empty($id) || $aReturn[ "image"] === ''  ? 'required|mimes:jpeg,jpg,png|max:10240' : 'mimes:jpeg,jpg,png|max:10240',
                'firstname_x_coordinate'=> 'required',
                'firstname_y_coordinate'=> 'required',
                'firstname_size'=> 'required',
                'firstname_color'=> 'required',
            ];

            $message = [ 
                'image.mimes' => 'The image must be a file of type: jpeg, jpg, png.',
                'image.max' => 'The image must be less than 10MB.',
            ];
            $validation = $request->validate($rules,$message );

            $UserId = Session::get('logged_in')['id'];
            $certificate_name =  (!empty($request->certificate_name)) ? $request->certificate_name : '';
           
          

            $field[0] = (!empty($request->firstname_field)) ? $request->firstname_field : '';
            $x_coordinate[0] = (!empty($request->firstname_x_coordinate)) ? $request->firstname_x_coordinate : '';
            $y_coordinate[0] = (!empty($request->firstname_y_coordinate)) ? $request->firstname_y_coordinate : '';
            $text_size[0] = (!empty($request->firstname_size)) ? $request->firstname_size : '';
            $text_color[0] = (!empty($request->firstname_color)) ? $request->firstname_color : '';

            $field[1] = (!empty($request->lastname_field)) ? $request->lastname_field : '';
            $x_coordinate[1] = (!empty($request->lastname_x_coordinate)) ? $request->lastname_x_coordinate : '';
            $y_coordinate[1] = (!empty($request->lastname_y_coordinate)) ? $request->lastname_y_coordinate : '';
            $text_size[1] = (!empty($request->lastname_size)) ? $request->lastname_size : '';
            $text_color[1] = (!empty($request->lastname_color)) ? $request->lastname_color : '';

            $field[2] = (!empty($request->option1_field)) ? $request->option1_field : '';
            $x_coordinate[2] = (!empty($request->option1_x_coordinate)) ? $request->option1_x_coordinate : '';
            $y_coordinate[2] = (!empty($request->option1_y_coordinate)) ? $request->option1_y_coordinate : '';
            $text_size[2] = (!empty($request->option1_size)) ? $request->option1_size : '';
            $text_color[2] = (!empty($request->option1_color)) ? $request->option1_color : '';

            $field[3] = (!empty($request->option2_field)) ? $request->option2_field : '';
            $x_coordinate[3] = (!empty($request->option2_x_coordinate)) ? $request->option2_x_coordinate : '';
            $y_coordinate[3] = (!empty($request->option2_y_coordinate)) ? $request->option2_y_coordinate : '';
            $text_size[3] = (!empty($request->option2_size)) ? $request->option2_size : '';
            $text_color[3] = (!empty($request->option2_color)) ? $request->option2_color : '';

            $field[4] = (!empty($request->option3_field)) ? $request->option3_field : '';
            $x_coordinate[4] = (!empty($request->option3_x_coordinate)) ? $request->option3_x_coordinate : '';
            $y_coordinate[4] = (!empty($request->option3_y_coordinate)) ? $request->option3_y_coordinate : '';
            $text_size[4] = (!empty($request->option3_size)) ? $request->option3_size : '';
            $text_color[4] = (!empty($request->option3_color)) ? $request->option3_color : '';
         
            $field[5] = (!empty($request->option4_field)) ? $request->option4_field : '';
            $x_coordinate[5] = (!empty($request->option4_x_coordinate)) ? $request->option4_x_coordinate : '';
            $y_coordinate[5] = (!empty($request->option4_y_coordinate)) ? $request->option4_y_coordinate : '';
            $text_size[5] = (!empty($request->option4_size)) ? $request->option4_size : '';
            $text_color[5] = (!empty($request->option4_color)) ? $request->option4_color : '';

            $field[6] = (!empty($request->option5_field)) ? $request->option5_field : '';
            $x_coordinate[6] = (!empty($request->option5_x_coordinate)) ? $request->option5_x_coordinate : '';
            $y_coordinate[6] = (!empty($request->option5_y_coordinate)) ? $request->option5_y_coordinate : '';
            $text_size[6] = (!empty($request->option5_size)) ? $request->option5_size : '';
            $text_color[6] = (!empty($request->option5_color)) ? $request->option5_color : '';

            if (!empty($request->file('image'))) {
                $image = $this->uploadFile($request->file('image'), public_path('uploads/Event_certificate/'));
                $a_return['image'] = $image;
            }

            if ($id > 0) {
                $count = count($field);
                
               $image = !empty($image) ? $image : $aReturn[ "image"];
             
                $sSQL = 'UPDATE email_certificates SET 
                    certificate_name=:certificate_name,
                    image=:image,
                    create_at=:create_at,
                    status_count=:status_count,
                    csv_count=:csv_count,
                    fail_email=:fail_email
                    WHERE id=:id';
                    // dd($sSQL);
                    $Bindings = array(
                        'certificate_name' =>$certificate_name,
                        'image' => $image,
                        'create_at' => strtotime('now'),
                        'status_count' => 2,
                        'csv_count' => 2,
                        'fail_email' => 2,
                        'id' => $id
                    );
         
                    DB::update($sSQL, $Bindings);
                for ($i = 0; $i < $count; $i++) {
                        $sql = 'SELECT id, email_certificate_id FROM event_certificates_details where email_certificate_id = :email_certificate_id';
                        $res = DB::select($sql,array(
                            'email_certificate_id' => $id,
                        ));
                        $edit_id = !empty($res[$i]->id) ?  $res[$i]->id : '';
                        $sSQL = 'UPDATE event_certificates_details SET 
                        email_certificate_id = :email_certificate_id,
                        field=:field,
                        x_coordinate=:x_coordinate,
                        y_coordinate=:y_coordinate,
                        text_size=:text_size,
                        text_color=:text_color,
                        added_by=:added_by,
                        created_at=:created_at
                        WHERE id=:id ';
                        // dd($sSQL);
                        $Bindings = array(
                            'email_certificate_id' => $id,
                            'field' => $field[$i],
                            'x_coordinate' => $x_coordinate[$i],
                            'y_coordinate' => $y_coordinate[$i],
                            'text_size' => $text_size[$i],
                            'text_color' => $text_color[$i],
                            'added_by' => $UserId,
                            'created_at' => strtotime('now'),
                            'id' => $edit_id
                        );
         
                       DB::update($sSQL, $Bindings);
                }
                $SuccessMessage = 'Event Certificate Details Updated Successfully';
              

            } else {
                $count = count($field);
                $sSQL = 'INSERT INTO email_certificates(
                    certificate_name,image,create_at,status_count,csv_count,fail_email
                ) VALUES (
                :certificate_name,:image,:create_at,:status_count,:csv_count,:fail_email
                )';
                $Bindings = array(
                    'certificate_name' => $certificate_name,
                    'image' => $image,
                    'create_at' => strtotime('now'),
                    'status_count' => 1,
                    'csv_count' => 1,
                    'fail_email' => 1,
                
                );
                DB::insert($sSQL, $Bindings);
                $emailcertificateId = DB::getPdo()->lastInsertId();
              
                for ($i = 0; $i < $count; $i++) {

                    $sSQL = 'INSERT INTO event_certificates_details(
                        email_certificate_id,field,x_coordinate,y_coordinate,text_size,text_color,added_by,created_at,status
                    ) VALUES (
                       :email_certificate_id,:field,:x_coordinate,:y_coordinate,:text_size,:text_color,:added_by,:created_at,:status
                    )';
                    // // dd($sSQL);
                    $Bindings = array(
                        'email_certificate_id' => $emailcertificateId,
                        'field' => $field[$i],
                        'x_coordinate' => $x_coordinate[$i],
                        'y_coordinate' => $y_coordinate[$i],
                        'text_size' => $text_size[$i],
                        'text_color' => $text_color[$i],
                        'added_by' => $UserId,
                        'created_at' => strtotime('now'),
                        'status' => 1
                    );
                    // // dd($Bindings);
                
                    DB::insert($sSQL, $Bindings);
                }
                    
                $SuccessMessage = 'Event Certificate Details Added Successfully';

  
            }
            return redirect('/event_certificate')->with('success',$SuccessMessage);
        } else {
            if ($id > 0) {
                //   #SHOW EXISTING DETAILS ON EDIT
                //EVENT CERTIFICATE PAGE
                $sSQL = 'SELECT ec.id, ec.certificate_name,ec.image,ecd.* FROM email_certificates ec LEFT JOIN event_certificates_details ecd ON ec.id = ecd.email_certificate_id where ecd.email_certificate_id = '.$id;
                $a_return['event_certificate_details'] = DB::select($sSQL, array());
           
            }
        }

    
        return view('event_certificate.create',$a_return);
    }
   
    
    public function change_active_status(Request $request)
    {
        $aReturn = EventCertificateModel::change_status_certificate($request);
        // dd($aReturn);

        $successMessage = 'Status changed successfully';
        $sucess = 'true';
        $aReturn = [];
        $aReturn['message'] = $successMessage;
        $aReturn['sucess'] = $sucess;
        return $aReturn;
    }

    public function delete_event_certificate($iId)
    {
        EventCertificateModel::delete_certificate($iId);
        return redirect(url('/event_certificate'))->with('success', 'Event Certificate Deleted Successfully');
    }

    public function preview_data_certificate(Request $request){
       
        $a_return[] = '';
            $Id =  (!empty( $request->Id)) ?  $request->Id : '';
                if ($Id > 0) {
                    //EVENT CERTIFICATE PAGE
                    $sSQL = 'SELECT id,image FROM email_certificates ecd WHERE id=:id';
                    $a_return = DB::select($sSQL, array(
                        'id' => $Id,
                    ));
                    $aReturn = (array) $a_return[0];
                    
                }
            
            $UserId = Session::get('logged_in')['id'];
            $certificate_name =  (!empty($request->certificate_name)) ? $request->certificate_name : '';
           

            $field[0] = (!empty($request->firstname_field)) ? $request->firstname_field : '';
            $x_coordinate[0] = (!empty($request->firstname_x_coordinate)) ? $request->firstname_x_coordinate : '0';
            $y_coordinate[0] = (!empty($request->firstname_y_coordinate)) ? $request->firstname_y_coordinate : '0';
            $text_size[0] = (!empty($request->firstname_size)) ? $request->firstname_size : '0';
            $text_color[0] = (!empty($request->firstname_color)) ? $request->firstname_color : '0';

            $field[1] = (!empty($request->lastname_field)) ? $request->lastname_field : '';
            $x_coordinate[1] = (!empty($request->lastname_x_coordinate)) ? $request->lastname_x_coordinate : '0';
            $y_coordinate[1] = (!empty($request->lastname_y_coordinate)) ? $request->lastname_y_coordinate : '0';
            $text_size[1] = (!empty($request->lastname_size)) ? $request->lastname_size : '0';
            $text_color[1] = (!empty($request->lastname_color)) ? $request->lastname_color : '0';

            $field[2] = (!empty($request->option1_field)) ? $request->option1_field : '';
            $x_coordinate[2] = (!empty($request->option1_x_coordinate)) ? $request->option1_x_coordinate : '0';
            $y_coordinate[2] = (!empty($request->option1_y_coordinate)) ? $request->option1_y_coordinate : '0';
            $text_size[2] = (!empty($request->option1_size)) ? $request->option1_size : '0';
            $text_color[2] = (!empty($request->option1_color)) ? $request->option1_color : '0';

            $field[3] = (!empty($request->option2_field)) ? $request->option2_field : '';
            $x_coordinate[3] = (!empty($request->option2_x_coordinate)) ? $request->option2_x_coordinate : '0';
            $y_coordinate[3] = (!empty($request->option2_y_coordinate)) ? $request->option2_y_coordinate : '0';
            $text_size[3] = (!empty($request->option2_size)) ? $request->option2_size : '0';
            $text_color[3] = (!empty($request->option2_color)) ? $request->option2_color : '0';

            $field[4] = (!empty($request->option3_field)) ? $request->option3_field : '';
            $x_coordinate[4] = (!empty($request->option3_x_coordinate)) ? $request->option3_x_coordinate : '0';
            $y_coordinate[4] = (!empty($request->option3_y_coordinate)) ? $request->option3_y_coordinate : '0';
            $text_size[4] = (!empty($request->option3_size)) ? $request->option3_size : '0';
            $text_color[4] = (!empty($request->option3_color)) ? $request->option3_color : '0';
         
            $field[5] = (!empty($request->option4_field)) ? $request->option4_field : '';
            $x_coordinate[5] = (!empty($request->option4_x_coordinate)) ? $request->option4_x_coordinate : '0';
            $y_coordinate[5] = (!empty($request->option4_y_coordinate)) ? $request->option4_y_coordinate : '0';
            $text_size[5] = (!empty($request->option4_size)) ? $request->option4_size : '0';
            $text_color[5] = (!empty($request->option4_color)) ? $request->option4_color : '0';

            $field[6] = (!empty($request->option5_field)) ? $request->option5_field : '';
            $x_coordinate[6] = (!empty($request->option5_x_coordinate)) ? $request->option5_x_coordinate : '0';
            $y_coordinate[6] = (!empty($request->option5_y_coordinate)) ? $request->option5_y_coordinate : '0';
            $text_size[6] = (!empty($request->option5_size)) ? $request->option5_size : '0';
            $text_color[6] = (!empty($request->option5_color)) ? $request->option5_color : '0';
           
            if (!empty($request->file('certificate_image'))) {
                $image = $this->uploadFile($request->file('certificate_image'), public_path('uploads/Event_certificate/'));
                $a_return['image'] = $image;
            }
          if($Id > 0){
              
                $count = count($field);
                $image = !empty($image) ? $image : $aReturn[ "image"];
                // dd( $image);
                $sSQL = 'UPDATE email_certificates SET 
                certificate_name=:certificate_name,
                image=:image,
                create_at=:create_at,
                status_count=:status_count,
                csv_count=:csv_count,
                fail_email=:fail_email
                WHERE id=:id';
                // dd($sSQL);
                $Bindings = array(
                    'certificate_name' =>$certificate_name,
                    'image' => $image,
                    'create_at' => strtotime('now'),
                    'status_count' => 2,
                    'csv_count' => 2,
                    'fail_email' => 2,
                    'id' => $Id
                );
         
                DB::update($sSQL, $Bindings);
                for ($i = 0; $i < $count; $i++) {
                    $sql = 'SELECT id, email_certificate_id FROM event_certificates_details where email_certificate_id = :email_certificate_id';
                    $res = DB::select($sql,array(
                        'email_certificate_id' => $Id,
                    ));
                    $edit_id = !empty($res[$i]->id) ?  $res[$i]->id : '';
                    $sSQL = 'UPDATE event_certificates_details SET 
                    email_certificate_id = :email_certificate_id,
                    field=:field,
                    x_coordinate=:x_coordinate,
                    y_coordinate=:y_coordinate,
                    text_size=:text_size,
                    text_color=:text_color,
                    added_by=:added_by,
                    created_at=:created_at
                    WHERE id=:id ';
                    // dd($sSQL);
                    $Bindings = array(
                        'email_certificate_id' => $Id,
                        'field' => $field[$i],
                        'x_coordinate' => $x_coordinate[$i],
                        'y_coordinate' => $y_coordinate[$i],
                        'text_size' => $text_size[$i],
                        'text_color' => $text_color[$i],
                        'added_by' => $UserId,
                        'created_at' => strtotime('now'),
                        'id' => $edit_id
                    );
     
                    $aReturn =  DB::update($sSQL, $Bindings);
            }
           

          }else{
           
            $count = count($field);
                $sSQL = 'INSERT INTO email_certificates(
                    certificate_name,image,create_at,status_count,csv_count,fail_email
                ) VALUES (
                :certificate_name,:image,:create_at,:status_count,:csv_count,:fail_email
                )';
                $Bindings = array(
                    'certificate_name' => $certificate_name,
                    'image' => $image,
                    'create_at' => strtotime('now'),
                    'status_count' => 1,
                    'csv_count' => 1,
                    'fail_email' => 1,
                
                );
                DB::insert($sSQL, $Bindings);
                $emailcertificateId = DB::getPdo()->lastInsertId();
            
                for ($i = 0; $i < $count; $i++) {
                  
                    $sSQL = 'INSERT INTO event_certificates_details(
                        email_certificate_id,field,x_coordinate,y_coordinate,text_size,text_color,added_by,created_at,status
                    ) VALUES (
                       :email_certificate_id,:field,:x_coordinate,:y_coordinate,:text_size,:text_color,:added_by,:created_at,:status
                    )';
                    // // dd($sSQL);
                    $Bindings = array(
                        'email_certificate_id' => $emailcertificateId,
                        'field' => $field[$i],
                        'x_coordinate' => $x_coordinate[$i],
                        'y_coordinate' => $y_coordinate[$i],
                        'text_size' => $text_size[$i],
                        'text_color' => $text_color[$i],
                        'added_by' => $UserId,
                        'created_at' => strtotime('now'),
                        'status' => 1
                    );
                    // // dd($Bindings);
                
                    $aReturn = DB::insert($sSQL, $Bindings);
                 
                   
                }
            }    

                $input_data = array(
                    "FirstNameX" => (isset($x_coordinate[0]) && !empty($x_coordinate[0])) ? $x_coordinate[0] : 0,
                    "FirstNameY" => (isset($y_coordinate[0]) && !empty($y_coordinate[0])) ? $y_coordinate[0] : 0,
                    "FirstNameSize" => (isset( $text_size[0]) && !empty( $text_size[0])) ?  $text_size[0] : 0,
                    "FirstNameColour" => (isset($text_color[0]) && !empty($text_color[0])) ? $text_color[0] : 0,
    
                    "LastNameX" => (isset($x_coordinate[1]) && !empty($x_coordinate[1])) ? $x_coordinate[1] : 0,
                    "LastNameY" => (isset($y_coordinate[1]) && !empty($y_coordinate[1]))? $y_coordinate[1] : 0,
                    "LastNameSize" => (isset($text_size[1]) && !empty($text_size[1])) ? $text_size[1] : 0,
                    "LastNameColour" => (isset($text_color[0]) && !empty($text_color[0])) ? $text_color[0] : 0,
    
                    "Option1X" => (isset($x_coordinate[2]) && !empty($x_coordinate[2])) ? $x_coordinate[2] : 0,
                    "Option1Y" => (isset($y_coordinate[2]) && !empty($y_coordinate[2])) ? $y_coordinate[2] : 0,
                    "Option1Size" =>( isset($text_size[2]) && !empty($text_size[2])) ? $text_size[2] : 0,
                    "Option1Colour" => (isset($text_color[2]) && !empty($text_color[2])) ? $text_color[2] : 0,
    
                    "Option2X" => (isset($x_coordinate[3]) && !empty($x_coordinate[3])) ? $x_coordinate[3] : 0,
                    "Option2Y" => (isset($y_coordinate[3]) && !empty($y_coordinate[3])) ? $y_coordinate[3] : 0,
                    "Option2Size" => (isset($text_size[3]) && !empty($text_size[3])) ? $text_size[3] : 0,
                    "Option2Colour" => (isset($text_color[3]) && !empty($text_color[3])) ? $text_color[3] : 0,
    
                    "Option3X" => (isset($x_coordinate[4]) && !empty($x_coordinate[4])) ? $x_coordinate[4] : 0,
                    "Option3Y" => (isset($y_coordinate[4]) && !empty($y_coordinate[4])) ? $y_coordinate[4] : 0,
                    "Option3Size" => (isset($text_size[4]) && !empty($text_size[4])) ? $text_size[4] : 0,
                    "Option3Colour" => (isset($text_color[4]) && !empty($text_color[4])) ? $text_color[4] : 0,
    
                    "Option4X" => (isset($x_coordinate[5]) && !empty($x_coordinate[5])) ? $x_coordinate[5] : 0,
                    "Option4Y" => (isset($y_coordinate[5]) && !empty($y_coordinate[5])) ? $y_coordinate[5] : 0,
                    "Option4Size" => (isset($text_size[5]) && !empty($text_size[5])) ? $text_size[5] : 0,
                    "Option4Colour" => (isset($text_color[5]) && !empty($text_color[5])) ? $text_color[5] : 0,
    
                    "Option5X" => (isset($x_coordinate[6]) && !empty($x_coordinate[6])) ? $x_coordinate[6] : 0,
                    "Option5Y" =>( isset($y_coordinate[6]) && !empty($y_coordinate[6])) ? $y_coordinate[6] : 0,
                    "Option5Size" => (isset($text_size[6]) && !empty($text_size[6])) ? $text_size[6] : 0,
                    "Option5Colour" => (isset($text_color[6]) && !empty($text_color[6])) ? $text_color[6] : 0,
                   
                     "certificate_image" =>  $image = !empty($image) ? $image : $aReturn[ "image"],
                   
                );
                $input_json_data = json_encode($input_data);
                // dd($input_json_data);
                // $headers[] = 'Content-Type: application/json';
                // $ch = curl_init();
                return $aReturn;


    }
    public function uploadFile($File, $Path)
    {
        // dd($File, $Path);
        $ImageExtention = $File->getClientOriginalExtension(); #get proper by code;
        $image = strtotime('now') . '.' . $ImageExtention;
        $File->move($Path, $image);
        return $image;
    }





}
