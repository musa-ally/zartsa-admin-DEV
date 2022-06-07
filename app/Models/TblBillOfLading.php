<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblBillOfLading extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'consignee',
        'notify',
        'port_of_lading',
        'tbl_voyages_id'
    ];
}
