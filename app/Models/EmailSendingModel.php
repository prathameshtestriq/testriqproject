<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class EmailSendingModel extends Model
{
    use HasFactory;
    protected $table = 'send_email_log';
 
    protected $fillable = [];
    public $timestamps = false;
 
    public static function get_count($a_search = array()){
        $count = 0;
        // dd($a_search);
        $s_sql = 'SELECT count(el.id) as count FROM send_email_log el WHERE 1=1';

        if (!empty($a_search['search_email_type'])) {
            $s_sql .= ' AND el.email_type = ' .$a_search['search_email_type'];
        }

        if(!empty($a_search['search_send_email_start_date'])){
            $startdate = strtotime($a_search['search_send_email_start_date']);    
            $s_sql .= " AND el.sent_date_time >= "." $startdate";
            // dd($sSQL);
        }

        if(!empty($a_search['search_end_marketing_date'])){
            $endDate = strtotime($a_search['search_end_marketing_date']);
            $s_sql .= " AND  el.sent_date_time <="." $endDate";
            // dd($sSQL);
        } 

        if(!empty( $a_search['search_receiver'])){
            $s_sql .= ' AND LOWER(el.recipient_type) LIKE \'%' . strtolower($a_search['search_receiver']) . '%\'';
        } 

        if (!empty($a_search['search_event'])) {
            // Ensure the search_event is properly formatted
            $searchEventIds = explode(',', $a_search['search_event']);
            $searchEventIds = array_map('intval', $searchEventIds); // Convert to integers for safety
            $searchEventIds = implode(',', $searchEventIds);
            $s_sql .= ' AND FIND_IN_SET(' . $searchEventIds . ', el.event_id) > 0';
        }
        
        $CountsResult = DB::select($s_sql);
        if (!empty($CountsResult)) {
            $count = $CountsResult[0]->count;
        }
        // dd($count);
        return $count;
    }

    public static function get_all($limit, $a_search = array()){
        $a_return = [];

        $s_sql = 'SELECT el.*, GROUP_CONCAT(e.name) AS event_names
        FROM send_email_log el
        LEFT JOIN events e ON FIND_IN_SET(e.id, el.event_id)
        where 1=1';

        if (!empty($a_search['search_email_type'])) {
            $s_sql .= ' AND el.email_type = ' .$a_search['search_email_type'];
        }

        if(!empty($a_search['search_send_email_start_date'])){
            $startdate = strtotime($a_search['search_send_email_start_date']);    
            $s_sql .= " AND el.sent_date_time >= "." $startdate";
            // dd($sSQL);
        }

        if(!empty($a_search['search_send_email_end_date'])){
            $endDate = strtotime($a_search['search_send_email_end_date']);
            $s_sql .= " AND  el.sent_date_time <="." $endDate";
            // dd($sSQL);
        } 

        if(!empty( $a_search['search_receiver'])){
            $s_sql .= ' AND LOWER(el.recipient_type) LIKE \'%' . strtolower($a_search['search_receiver']) . '%\'';
        } 

        if (!empty($a_search['search_event'])) {
            // Ensure the search_event is properly formatted
            $searchEventIds = explode(',', $a_search['search_event']);
            $searchEventIds = array_map('intval', $searchEventIds); // Convert to integers for safety
            $searchEventIds = implode(',', $searchEventIds);
            $s_sql .= ' AND FIND_IN_SET(' . $searchEventIds . ', el.event_id) > 0';
        }
        $s_sql .= " GROUP BY el.id";
        
        if ($limit > 0) {
            $s_sql .= ' LIMIT ' . $a_search['Offset'] . ',' . $limit;
        }
    
      
       
        $a_return = DB::select($s_sql);
        return $a_return;
    }


    public static function add_email($request,$email)
    {
        // dd($request->all());
        // Get the counts for different recipient types
        // $s_sql = 'SELECT count(u.id) As countId FROM users u WHERE u.is_active = 1 AND u.is_deleted = 0';
        // $user_count = DB::select($s_sql,array());
        // $user_count1 = (count($user_count) > 0) ? $user_count[0]->countId : 0;

        $s_sql = 'SELECT count(id) AS countId FROM organizer WHERE 1=1';
        $Organizer = DB::select($s_sql, array());
        $Organizer1 = (count($Organizer) > 0) ? $Organizer[0]->countId : 0;

        $s_sql = "SELECT COUNT(e.id) AS countId FROM event_booking AS e 
        LEFT JOIN booking_details AS b ON b.booking_id = e.id
        WHERE 1=1";
        $all_registration = DB::select($s_sql,array());
        $all_registration1 = (count($all_registration) > 0) ? $all_registration[0]->countId : 0;

        $s_sql = 'SELECT count(a.id) as countId
        FROM attendee_booking_details a
        LEFT JOIN booking_details AS b ON a.booking_details_id = b.id
        Inner JOIN event_booking AS e ON b.booking_id = e.id
        WHERE 1=1';
        $event_participant = DB::select($s_sql,array());
        $event_participant1 = (count($event_participant) > 0) ? $event_participant[0]->countId : 0;
       
        $counts = [
            // 'All User' => $user_count1,
            'All Organizer' =>  $Organizer1,
            'All Registration' => $all_registration1,
            'All Participant' => $event_participant1,
        ];

       
        $recipientType = $request->receiver;
        $sendEmailDateType = $request->date == 'now_date' ? 1 : 2;
        $sentDateTime = strtotime($request->date == 'now_date' ? 'now' : $request->shedulingdate);
        if($request->email_type == '2'){
            $email = $request->email_type == '2' ? $request->email : '';
        }else if($request->email_type == '3'){
            $email = $request->email_type == '3' ? $email : '';  
        }
        // $email = $request->email_type == '2' ? $request->email : '';
        // $email = $request->email_type == '3' ? $email : '';
        // dd($EmailType);
        if(($recipientType == 'All Registration')||($recipientType =='All Participant')){
            $eventIds = !empty($request->event) ? implode(',', $request->event) : '0';
        }else{
            $eventIds = '0';
        } 

        $bindings = [
            'event_id' => $eventIds,
            'subject' => !empty($request->subject) ? $request->subject : '',
            'message' => !empty($request->message) ? $request->message  :'',
            'recipient_type' => !empty($recipientType) ? $recipientType : '0',
            'recipient_count' =>  !empty($counts[$recipientType])?$counts[$recipientType]:'0',
            'send_date_type' => !empty($sendEmailDateType)?$sendEmailDateType:'0',
            'sent_date_time' => !empty($sentDateTime)?$sentDateTime:'0',
            'email_type' => !empty($request->email_type) ? $request->email_type : 0,
            'email'=> !empty($email) ? $email : ''
        ];

        // dd($bindings);

        $ssql = 'INSERT INTO send_email_log (
                    event_id, subject, message,
                    recipient_type, recipient_count, send_date_type, sent_date_time,email_type,email
                ) VALUES (
                    :event_id, :subject, :message,
                    :recipient_type, :recipient_count, :send_date_type, :sent_date_time, :email_type, :email
                )';

        $Result = DB::insert($ssql, $bindings);
    }

    public static function change_status_email($request)
    {
        // dd($request->all());
        $sSQL = 'UPDATE send_email_log SET status=:status WHERE id=:id';
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
