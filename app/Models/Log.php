<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'log';

    protected $fillable = [
        'url',
        'method',
        'event_id',
        'user_id',
        'action',
        'post_data',
        'created_timestamp',
        'header',
        'server_ip',
        'created_by'
    ];

    public $timestamps = false;
}
