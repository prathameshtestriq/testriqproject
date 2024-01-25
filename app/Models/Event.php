<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events';
	public $timestamps = false;

    public static function get($aSearch){
        $query = Event::from('events as e');
        $query->where('e.deleted',0);

       return $query;
   }
}
