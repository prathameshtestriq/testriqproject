<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class EmailPlaceholderManagement extends Model
{
    use HasFactory;
    protected $table = 'email_placeholders';
 
    protected $fillable = [
        'event_id',
        'question_id',
        'placeholder_name',
        'status',
        'created_date  ',
       
    ];
    public $timestamps = false;

    public static function get_count($a_search = array()){
        $count = 0;
        // dd($a_search);
        $s_sql = 'SELECT count(ep.id) as count FROM email_placeholders ep WHERE 1=1';

        if (!empty($a_search['search_placeholder_name'])) {
            $s_sql .= ' AND LOWER(ep.placeholder_name) LIKE \'%' . strtolower($a_search['search_placeholder_name']) . '%\'';
        }

        if(isset( $a_search['search_event_id']) &&  $a_search['search_event_id'] != ''){
            $s_sql .= ' AND ep.event_id = '.$a_search['search_event_id'];
        } 

        if(isset( $a_search['search_placeholder_status']) &&  $a_search['search_placeholder_status'] != ''){
            $s_sql .= ' AND ep.status = '.$a_search['search_placeholder_status'];
        } 

        $CountsResult = DB::select($s_sql);
        if (!empty($CountsResult)) {
            $count = $CountsResult[0]->count;
        }
        // dd($count);
        return $count;
    }

    public static function get_all_email_placeholders($limit, $a_search = array()){
        $a_return = []; 

        $s_sql = 'SELECT *,(SELECT name FROM events as e where e.id =ep.event_id ) AS event_name,(SELECT question_form_name FROM event_form_question as eq where eq.id =ep.question_id ) AS question_form FROM email_placeholders ep where 1=1';

        if (!empty($a_search['search_placeholder_name'])) {
            $s_sql .= ' AND LOWER(ep.placeholder_name) LIKE \'%' . strtolower($a_search['search_placeholder_name']) . '%\'';
        }
        
        if(isset( $a_search['search_event_id']) &&  $a_search['search_event_id'] != ''){
            $s_sql .= ' AND ep.event_id = '.$a_search['search_event_id'];
        } 

        if(isset( $a_search['search_placeholder_status']) &&  $a_search['search_placeholder_status'] != ''){
            $s_sql .= ' AND ep.status = '.$a_search['search_placeholder_status'];
        } 
       
        if ($limit > 0) {
            $s_sql .= ' LIMIT ' . $a_search['Offset'] . ',' . $limit;
        }

        $a_return = DB::select($s_sql);
        return $a_return;
    }

    public static function update_email_placeholder($iId, $request)
	{
        $sql = 'SELECT id,question_form_name,question_label FROM event_form_question Where id ='.$request->question;
        $res = DB::select($sql,array());
        if($res[0]->question_form_name == 'sub_question'){
            $sub_question = 1;
        }else{
            $sub_question = 0;
        }

        $ssql = 'UPDATE email_placeholders SET 
        event_id = :event_id,
        question_id = :question_id, 
        question_form_name = :question_form_name,
        sub_question = :sub_question,
        placeholder_name = :name,
        created_date = :created_date
        WHERE id=:id';

        $bindings = array(
            'event_id' => $request->event,
            'question_id' => $request->question,
            'question_form_name' => $request->question_form_name,
            'sub_question' => $sub_question,
            'name' => $request->placeholder_name,
            'created_date' => strtotime('now'),
            'id' => $iId
        );
        // dd($bindings);
        $Result = DB::update($ssql, $bindings);
	}

    public static function add_email_placeholder($request)
	{  
        $sql = 'SELECT id,question_form_name,question_label FROM event_form_question Where id ='.$request->question;
        $res = DB::select($sql,array());
        if($res[0]->question_form_name == 'sub_question'){
            $sub_question = 1;
        }else{
            $sub_question = 0;
        }
        // dd($res[0]->question_form_name == 'sub_question');
        // dd($request->question_form_name);
        $ssql = 'INSERT INTO email_placeholders(
            event_id,question_id,question_form_name,sub_question,placeholder_name,created_date)
                VALUES (
            :event_id,:question_id,:question_form_name,:sub_question,:placeholder_name,:created_date)';
        
        $bindings = array(
            'event_id' => $request->event,
            'question_id' => $request->question,
            'question_form_name' => $request->question_form_name ,
            'sub_question' => $sub_question ,
            'placeholder_name' => $request->placeholder_name,
            'created_date' => strtotime('now')
        );
        //dd($bindings);
        $Result = DB::insert($ssql,$bindings);
        
	}

    public static function delete_email_placeholder($iId)
    {
       
        if (!empty($iId)) {
            $sSQL = 'DELETE FROM `email_placeholders` WHERE id=:id';
            $Result = DB::delete(
                $sSQL,
                array(
                    'id' => $iId
                )
            );
            // dd($Result);
        }
        return $Result;
        
    }

    public static function change_status_email_placeholder($request)
    {
        $sSQL = 'UPDATE email_placeholders SET status=:status WHERE id=:id';
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
