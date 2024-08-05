<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class EmailSendingModel extends Model
{
    use HasFactory;
    protected $table = 'sent_email_log';
 
    protected $fillable = [];
    public $timestamps = false;
 
    public static function add_email($request)
	{
        // dd($request->all());
        $s_sql = 'SELECT count(u.id) As countId FROM users u WHERE u.is_active = 1 AND u.is_deleted = 0';
        $user_count = DB::select($s_sql,array());
        $user_count1 = (count($user_count) > 0) ? $user_count[0]->countId : 0;
   
      
        $s_sql = 'SELECT count(a.id) as countId
        FROM attendee_booking_details a
        LEFT JOIN booking_details AS b ON a.booking_details_id = b.id
        Inner JOIN event_booking AS e ON b.booking_id = e.id
        WHERE 1=1';
        $event_participant = DB::select($s_sql,array());
        $event_participant1 = (count($event_participant) > 0) ? $event_participant[0]->countId : 0;
        // dd($event_participant'] );

        $s_sql = "SELECT COUNT(e.id) AS countId FROM event_booking AS e 
        LEFT JOIN booking_details AS b ON b.booking_id = e.id
        WHERE 1=1";
        $all_registration = DB::select($s_sql,array());
        $all_registration1 = (count($all_registration) > 0) ? $all_registration[0]->countId : 0;
        // dd( $all_registration'] );

        $s_sql = 'SELECT count(id) AS countId FROM organizer WHERE 1=1';
        $Organizer = DB::select($s_sql, array());
        $Organizer1 = (count($Organizer) > 0) ? $Organizer[0]->countId : 0;
        // dd(  $Organizer']);
       
        $eventIds = implode(',', $request->event); 
      
            $ssql = 'INSERT INTO sent_email_log(
                event_id,subject,message,
                recipient_type,recipient_count,send_email_type,sent_date_time)
                    VALUES (
                :event_id,:subject,:message,
                :recipient_type,:recipient_count,:send_email_type,:sent_date_time)';

            if($request->receiver == 'All User'){
                if($request->date == 'now_date'){
                    $bindings = array(
                        'event_id' => $eventIds,
                        'subject' => $request->subject,
                        'message' => strip_tags($request->message),
                        'recipient_type' => $request->receiver,
                        'recipient_count' => $user_count1,
                        'send_email_type' => 1,
                        'sent_date_time' =>  strtotime('now')       
                    );
                }else{
                    $bindings = array(
                        'event_id' => $eventIds,
                        'subject' => $request->subject,
                        'message' => strip_tags($request->message),
                        'recipient_type' => $request->receiver,
                        'recipient_count' => $user_count1,
                        'send_email_type' => 2,
                        'sent_date_time' =>  strtotime($request->shedulingdate)  
                    );
                }
            }elseif($request->receiver == 'All Organizer'){
                if($request->date == 'now_date'){
                    $bindings = array(
                        'event_id' => $eventIds,
                        'subject' => $request->subject,
                        'message' => strip_tags($request->message),
                        'recipient_type' => $request->receiver,
                        'recipient_count' => $Organizer1,
                        'send_email_type' => 1,
                        'sent_date_time' =>  strtotime('now')     
                    );
                }else{
                    $bindings = array(
                        'event_id' => $eventIds,
                        'subject' => $request->subject,
                        'message' => strip_tags($request->message),
                        'recipient_type' => $request->receiver,
                        'recipient_count' => $Organizer1,
                        'send_email_type' => 2,
                        'sent_date_time' => strtotime($request->shedulingdate)   
                    );
                }
            }elseif($request->receiver == 'All Registration'){
                if($request->date == 'now_date'){
                    $bindings = array(
                        'event_id' => $eventIds,
                        'subject' => $request->subject,
                        'message' => strip_tags($request->message),
                        'recipient_type' => $request->receiver,
                        'recipient_count' => $all_registration1,
                        'send_email_type' => 1,
                        'sent_date_time' =>  strtotime('now')      
                    );
                }else{
                    $bindings = array(
                        'event_id' => $eventIds,
                        'subject' => $request->subject,
                        'message' => strip_tags($request->message),
                        'recipient_type' => $request->receiver,
                        'recipient_count' => $all_registration1,
                        'send_email_type' => 2,
                        'sent_date_time' => strtotime($request->shedulingdate)     
                    );
                }
            }elseif($request->receiver == 'All Participant'){
                if($request->date == 'now_date'){
                    $bindings = array(
                        'event_id' => $eventIds,
                        'subject' => $request->subject,
                        'message' => strip_tags($request->message),
                        'recipient_type' => $request->receiver,
                        'recipient_count' => $event_participant1,
                        'send_email_type' => 1,
                        'sent_date_time' =>  strtotime('now')       
                    );
                }else{
                    $bindings = array(
                        'event_id' => $eventIds,
                        'subject' => $request->subject,
                        'message' => strip_tags($request->message),
                        'recipient_type' => $request->receiver,
                        'recipient_count' => $event_participant1,
                        'send_email_type' => 2 ,
                        'sent_date_time' => strtotime($request->shedulingdate)       
                    );
                }
            }
            // dd( $bindings);
            $Result = DB::insert($ssql,$bindings);
	}
    
    

    public static function change_status_email($request)
    {
        // dd($request->all());
        $sSQL = 'UPDATE sent_email_log SET status=:status WHERE id=:id';
        $aReturn = DB::update(
            $sSQL,
            array(
                'status' => $request->status,
                'id' => $request->id
            )
        );
    
        return $aReturn;
    }

   
}
