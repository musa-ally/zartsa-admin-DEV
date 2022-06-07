<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblVoyage extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'estimated_arrival_date',
        'arrival_date',
        'departure_date',
        'vessels_name',
        'vessel_code',
        'shipping_line',
    ];
}
