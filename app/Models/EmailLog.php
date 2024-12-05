<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $table = 'email_log';

    protected $fillable = [
        'event_id',
        'type',
        'send_mail_to',
        'subject',
        'message',
        'response',
        'datetime',
        'email_send_status'
    ];

    public $timestamps = false;
}
