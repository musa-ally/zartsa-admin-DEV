<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblCargo extends Model
{
    use HasFactory;

    protected $table = 'tbl_cargo';

    protected $fillable = [
        'number',
        'weight_kg',
        'remarks',
        'cargo_type',
        'container_type',
        'container_size',
        'tbl_bill_of_ladings_id',
        'tbl_bill_of_ladings_voyages_id',
        'is_electric',
        'content',
    ];
}
