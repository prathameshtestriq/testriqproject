<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    use HasFactory;
    protected $table = 'villages';

    protected $fillable = [
        'country_code',
        'postal_code',
        'place_name',
        'state_id',
        'city_id',
        'latitude',
        'longitude'
    ];
    public $timestamps = false;
}


