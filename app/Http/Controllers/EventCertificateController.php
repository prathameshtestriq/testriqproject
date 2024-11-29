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
        session::forget('event_id_certificate');
        session::forget('event_certificate_status');
        return redirect('/event_certificate');
    }

    public function index(Request $request)
    {

        $a_return = array();
        $a_return['event_id_certificate'] = '';
        $a_return['search_event_certificate_status'] = '';

        if (isset($request->form_type) && $request->form_type == 'search_event_certificate') {
            session(['event_id_certificate' => $request->event_id_certificate]);
            session(['event_certificate_status' => $request->event_certificate_status]);
            return redirect('/event_certificate');
        }

        $event_certificate_status = session('event_certificate_status');
        $a_return['search_event_certificate_status'] = (isset($event_certificate_status) && $event_certificate_status != '') ? $event_certificate_status : '';
        $a_return['event_id_certificate'] = (!empty(session('event_id_certificate'))) ? session('event_id_certificate') : '';

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

        $SQL = "SELECT id,name FROM events WHERE active=1 AND deleted = 0";
        $a_return['EventsData'] = DB::select($SQL, array());
        return view('event_certificate.list', $a_return);
    }

    public function add_edit(Request $request, $event_id = 0)
    {
        $a_return['event_id'] = '';


        if (isset($request->form_type) && $request->form_type == 'add_edit_event_certificate') {
            if ($event_id > 0) {
                //   #SHOW EXISTING DETAILS ON EDIT
                //EVENT CERTIFICATE PAGE
                $sSQL = 'SELECT *,(SELECT name FROM events as e where e.id =ecd.event_id ) AS event_name FROM event_certificates_details ecd WHERE event_id=:event_id';
                $a_return = DB::select($sSQL, array('event_id' => $event_id));
                $aReturn = (array) $a_return[0];
            }
         
            $rules = [
                'event_id' => 'required',
                'certificate_name' => 'required',
                'image' => empty($event_id) || $aReturn[ "image"] === ''  ? 'required|mimes:jpeg,jpg,png|max:10240' : 'mimes:jpeg,jpg,png||max:10240',
                'name_x_coordinate'=> 'required',
                'name_y_coordinate'=> 'required',
                'name_size'=> 'required',
                'name_color'=> 'required',
            ];

            $message = [ 
                'image.mimes' => 'The image must be a file of type: jpeg, jpg, png.',
                'image.max' => 'The image must be less than 10MB.',
            ];
            $validation = $request->validate($rules,$message );
            // dd($validation);

            $eventId = (!empty($request->event_id)) ? $request->event_id : '';
            $certificate_name[0] =  (!empty($request->certificate_name)) ? $request->certificate_name : '';
            $field[0] = (!empty($request->image_field)) ? $request->image_field : '';
            $image[0] = '';
            $x_coordinate[0] = '';
            $y_coordinate[0] = '';
            $text_size[0] = '';
            $text_color[0] = '';

            $certificate_name[1] = '';
            $field[1] = (!empty($request->name_field)) ? $request->name_field : '';
            $image[1] = '';
            $x_coordinate[1] = (!empty($request->name_x_coordinate)) ? $request->name_x_coordinate : '';
            $y_coordinate[1] = (!empty($request->name_y_coordinate)) ? $request->name_y_coordinate : '';
            $text_size[1] = (!empty($request->name_size)) ? $request->name_size : '';
            $text_color[1] = (!empty($request->name_color)) ? $request->name_color : '';

            $certificate_name[2] = '';
            $field[2] = (!empty($request->timing_field)) ? $request->timing_field : '';
            $image[2] = '';
            $x_coordinate[2] = (!empty($request->timing_x_coordinate)) ? $request->timing_x_coordinate : '';
            $y_coordinate[2] = (!empty($request->timing_y_coordinate)) ? $request->timing_y_coordinate : '';
            $text_size[2] = (!empty($request->timing_size)) ? $request->timing_size : '';
            $text_color[2] = (!empty($request->timing_color)) ? $request->timing_color : '';

            $certificate_name[3] = '';
            $field[3] = (!empty($request->days_field)) ? $request->days_field : '';
            $image[3] = '';
            $x_coordinate[3] = (!empty($request->days_x_coordinate)) ? $request->days_x_coordinate : '';
            $y_coordinate[3] = (!empty($request->days_y_coordinate)) ? $request->days_y_coordinate : '';
            $text_size[3] = (!empty($request->days_size)) ? $request->days_size : '';
            $text_color[3] = (!empty($request->days_color)) ? $request->days_color : '';

            $certificate_name[4] = '';
            $field[4] = (!empty($request->distance_field)) ? $request->distance_field : '';
            $image[4] = '';
            $x_coordinate[4] = (!empty($request->distance_x_coordinate)) ? $request->distance_x_coordinate : '';
            $y_coordinate[4] = (!empty($request->distance_y_coordinate)) ? $request->distance_y_coordinate : '';
            $text_size[4] = (!empty($request->distance_size)) ? $request->distance_size : '';
            $text_color[4] = (!empty($request->distance_color)) ? $request->distance_color : '';

            $certificate_name[5] = '';
            $field[5] = (!empty($request->image_field1)) ? $request->image_field1 : '';
            $image[5] = '';
            $x_coordinate[5] = (!empty($request->image_x_coordinate)) ? $request->image_x_coordinate : '';
            $y_coordinate[5] = (!empty($request->image_y_coordinate)) ? $request->image_y_coordinate : '';
            $text_size[5] = (!empty($request->image_size)) ? $request->image_size : '';
            $text_color[5] = (!empty($request->image_color)) ? $request->image_color : '';

            if (!empty($request->file('image'))) {
                $image[0] = $this->uploadFile($request->file('image'), public_path('uploads/Event_certificate/'));
                $a_return['image'] = $image[0];
            }
            $sSQL = 'SELECT id,image FROM event_certificates_details WHERE event_id=:event_id';
            $result = DB::select($sSQL, array('event_id' => $event_id));
            $res_id = (!empty($result[0]->id)) ? $result[0]->id : '';

            if ($res_id > 0) {
                $count = count($field);
                for ($i = 0; $i < $count; $i++) {
                    
                    $sSQL = 'SELECT id,image FROM event_certificates_details WHERE event_id=:event_id';
                    $result = DB::select($sSQL, array('event_id' => $event_id));
                    $edit_id = (!empty($result[0]->id)) ? $result[0]->id : '';

                    $image[0] = !empty($image[0]) ? $image[0] : $result[0]->image;
                    // dd($image[0]);
                    // $edit_id = (!empty($i + 1)) ? $i + 1 : 0;
         
                    $sSQL = 'UPDATE event_certificates_details SET 
                    event_id=:event_id,
                    certificate_name=:certificate_name,
                    field=:field,
                    image=:image,
                    x_coordinate=:x_coordinate,
                    y_coordinate=:y_coordinate,
                    text_size=:text_size,
                    text_color=:text_color
                    WHERE id=:id';
                    // dd($sSQL);
                    $Bindings = array(
                        'event_id' => $eventId,
                        'certificate_name' =>$certificate_name[0],
                        'field' => $field[$i],
                        'image' => $image[0],
                        'x_coordinate' => $x_coordinate[$i],
                        'y_coordinate' => $y_coordinate[$i],
                        'text_size' => $text_size[$i],
                        'text_color' => $text_color[$i],
                        'id' => $edit_id
                    );
         
                    DB::update($sSQL, $Bindings);
                }
                $SuccessMessage = 'Event Certificate Details Updated Successfully';

            } else {

                // insert  
                $count = count($field);
                for ($i = 0; $i < $count; $i++) {

                    $sSQL = 'INSERT INTO event_certificates_details(
                        event_id,certificate_name,field,image,x_coordinate,y_coordinate,text_size,text_color,created_at,status
                    ) VALUES (
                        :event_id,:certificate_name,:field,:image,:x_coordinate,:y_coordinate,:text_size,:text_color,:created_at,:status
                    )';
                    // dd($sSQL);
                    $Bindings = array(
                        'event_id' => $eventId,
                        'certificate_name' =>$certificate_name[$i],
                        'field' => $field[$i],
                        'image' => $image[$i],
                        'x_coordinate' => $x_coordinate[$i],
                        'y_coordinate' => $y_coordinate[$i],
                        'text_size' => $text_size[$i],
                        'text_color' => $text_color[$i],
                        'created_at' => date('Y-m-d', strtotime('now')),
                        'status' => 1
                    );
                    // dd($Bindings);

                    DB::insert($sSQL, $Bindings);
                }
                $SuccessMessage = 'Event Certificate Details Added Successfully';

            }
            return redirect('/event_certificate')->with('success', $SuccessMessage);
        } else {
            if ($event_id > 0) {
                //   #SHOW EXISTING DETAILS ON EDIT
                //EVENT CERTIFICATE PAGE
                $sSQL = 'SELECT *,(SELECT name FROM events as e where e.id =ecd.event_id ) AS event_name FROM event_certificates_details ecd WHERE event_id=:event_id';
                $a_return['event_certificate_details'] = DB::select($sSQL, array('event_id' => $event_id));

            }
        }

        $EventId = !empty($event_id) ? $event_id : '';
        $SQL = "SELECT e.id,e.name FROM events e WHERE active=1 AND deleted = 0  AND (e.id NOT IN (SELECT event_id FROM event_certificates_details) OR e.id = :event_id)";
        $a_return['EventsData'] = DB::select($SQL, array($event_id));



        return view('event_certificate.create', $a_return);
    }
    public function uploadFile($File, $Path)
    {
        // dd($File, $Path);
        $ImageExtention = $File->getClientOriginalExtension(); #get proper by code;
        $image = strtotime('now') . '.' . $ImageExtention;
        $File->move($Path, $image);
        return $image;
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





}
